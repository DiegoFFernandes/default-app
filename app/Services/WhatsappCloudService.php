<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappCloudService
{
    private string $token;
    private string $phoneNumberId;
    private string $wabaId;
    private string $appId;

    public function __construct()
    {
        $this->token         = config('services.whatsapp_cloud.access_token');
        $this->phoneNumberId = config('services.whatsapp_cloud.phone_number_id');
        $this->wabaId        = config('services.whatsapp_cloud.waba_id');
        $this->appId         = config('services.whatsapp_cloud.app_id');
    }

    // Fora da janela de 24h só é permitido mandar mensagens de template aprovado
    public function enviarTemplate(string $para, string $template, string $idioma = 'en_US', array $componentes = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->formatarTelefone($para),
            'type'              => 'template',
            'template'          => [
                'name'     => $template,
                'language' => ['code' => $idioma],
            ],
        ];

        if ($componentes) {
            $payload['template']['components'] = $componentes;
        }

        return $this->enviar($payload);
    }

    // Só funciona dentro da janela de 24h após o cliente ter mandado mensagem
    public function enviarTexto(string $para, string $mensagem): array
    {
        return $this->enviar([
            'messaging_product' => 'whatsapp',
            'to'                => $this->formatarTelefone($para),
            'type'              => 'text',
            'text'              => ['body' => $mensagem],
        ]);
    }

    // Sobe um arquivo (PDF, etc.) pra API de mídia da Meta e devolve o media id,
    // usado no header de um template do tipo documento.
    public function enviarMidia(string $conteudo, string $nomeArquivo, string $mimeType = 'application/pdf'): ?string
    {
        $resposta = Http::withToken($this->token)
            ->attach('file', $conteudo, $nomeArquivo, ['Content-Type' => $mimeType])
            ->post("https://graph.facebook.com/v25.0/{$this->phoneNumberId}/media", [
                'messaging_product' => 'whatsapp',
                'type'              => $mimeType,
            ]);

        Log::info('WhatsApp Cloud upload de mídia', ['nome' => $nomeArquivo, 'resposta' => $resposta->json()]);

        return $resposta->json('id');
    }

    // Cria (submete pra analise) um template novo na conta (WABA) configurada.
    // $definicao: ['nome' => ..., 'categoria' => ..., 'idioma' => ..., 'componentes' => [...]]
    public function criarTemplate(array $definicao): array
    {
        $resposta = Http::withToken($this->token)
            ->post("https://graph.facebook.com/v26.0/{$this->wabaId}/message_templates", [
                'name'       => $definicao['nome'],
                'category'   => $definicao['categoria'],
                'language'   => $definicao['idioma'],
                'components' => $definicao['componentes'],
            ]);

        Log::info('WhatsApp Cloud criação de template', ['definicao' => $definicao, 'resposta' => $resposta->json()]);

        return $resposta->json() ?? [];
    }

    // Dados do numero conectado na WABA configurada - alimenta o card
    // informativo do "Canal Oficial" (so 1 numero por instalacao).
    public function numeroConectado(): ?array
    {
        $resposta = Http::withToken($this->token)
            ->get("https://graph.facebook.com/v26.0/{$this->wabaId}/phone_numbers", [
                'fields' => 'display_phone_number,verified_name,quality_rating,name_status,messaging_limit_tier',
            ]);

        return $resposta->json('data.0');
    }

    // Upload resumivel (API do App, nao do numero) - gera o "handle" exigido
    // como amostra de documento no header de um template. O handle expira,
    // entao isso deve ser chamado na hora de submeter, nao ao salvar o rascunho.
    public function obterHandleDocumento(string $conteudo, string $mimeType = 'application/pdf'): ?string
    {
        $sessao = Http::withToken($this->token)
            ->post("https://graph.facebook.com/v26.0/{$this->appId}/uploads", [
                'file_length' => strlen($conteudo),
                'file_type'   => $mimeType,
            ]);

        $sessaoId = $sessao->json('id');

        if (!$sessaoId) {
            Log::warning('WhatsApp Cloud: falha ao iniciar sessão de upload', ['resposta' => $sessao->json()]);
            return null;
        }

        $upload = Http::withHeaders([
                'Authorization' => 'OAuth ' . $this->token,
                'file_offset'   => '0',
            ])
            ->withBody($conteudo, 'application/octet-stream')
            ->post("https://graph.facebook.com/v26.0/{$sessaoId}");

        if (!$upload->json('h')) {
            Log::warning('WhatsApp Cloud: falha ao enviar arquivo de amostra', ['resposta' => $upload->json()]);
        }

        return $upload->json('h');
    }

    // Edita um template que ja existe na Meta (ex: corrigir e reenviar um
    // rejeitado) - a API nao deixa criar de novo com o mesmo nome, precisa
    // editar pelo ID do template (endpoint diferente do de criacao). Nome e
    // idioma nao mudam por aqui, so categoria/componentes.
    public function editarTemplateRemoto(string $metaTemplateId, array $definicao): array
    {
        $resposta = Http::withToken($this->token)
            ->post("https://graph.facebook.com/v26.0/{$metaTemplateId}", [
                'category'   => $definicao['categoria'],
                'components' => $definicao['componentes'],
            ]);

        Log::info('WhatsApp Cloud edição de template', ['definicao' => $definicao, 'resposta' => $resposta->json()]);

        return $resposta->json() ?? [];
    }

    // Lista os templates cadastrados de fato na conta (WABA) - usado pra
    // sincronizar o status real (aprovado/rejeitado/em analise) com o MySQL.
    public function listarTemplatesRemoto(): array
    {
        $resposta = Http::withToken($this->token)
            ->get("https://graph.facebook.com/v26.0/{$this->wabaId}/message_templates", ['limit' => 100]);

        return $resposta->json('data') ?? [];
    }

    public function apagarTemplateRemoto(string $nome): array
    {
        $resposta = Http::withToken($this->token)
            ->delete("https://graph.facebook.com/v26.0/{$this->wabaId}/message_templates", ['name' => $nome]);

        Log::info('WhatsApp Cloud exclusão de template', ['nome' => $nome, 'resposta' => $resposta->json()]);

        return $resposta->json() ?? [];
    }

    private function enviar(array $payload): array
    {
        $resposta = Http::withToken($this->token)
            ->post("https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages", $payload);

        Log::info('WhatsApp Cloud envio', ['payload' => $payload, 'resposta' => $resposta->json()]);

        return $resposta->json() ?? [];
    }

    // Mesma convencao do WppConnectService::formatPhone() - numero sem DDI
    // (<=11 digitos: DDD + numero) recebe o 55 do Brasil na frente.
    private function formatarTelefone(string $telefone): string
    {
        $digitos = preg_replace('/\D/', '', $telefone);

        return strlen($digitos) <= 11 ? '55' . $digitos : $digitos;
    }
}
