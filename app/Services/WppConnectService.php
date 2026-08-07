<?php

namespace App\Services;

use App\Models\User;
use App\Models\WppDisparo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WppConnectService
{
    private string $baseUrl;
    private string $secret;
    private string $session;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wppconnect.url'), '/');
        $this->secret  = config('services.wppconnect.secret');
        $this->session = config('services.wppconnect.session');
    }

    // -------------------------------------------------------
    // Autenticação
    // -------------------------------------------------------

    private function getToken(): string
    {
        $cacheKey = "wppconnect_token_{$this->session}";

        return Cache::remember($cacheKey, now()->addHours(23), function () {
            $response = Http::post("{$this->baseUrl}/api/{$this->session}/{$this->secret}/generate-token");

            if (! $response->successful()) {
                Log::error('WppConnect: falha ao gerar token', ['response' => $response->body()]);
                throw new \RuntimeException('WppConnect: não foi possível gerar o token.');
            }

            return $response->json('token');
        });
    }

    private function http()
    {
        return Http::withToken($this->getToken())
            ->timeout(15);
    }

    // -------------------------------------------------------
    // Sessão
    // -------------------------------------------------------

    public function closeSession(): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/close-session");
        Cache::forget("wppconnect_token_{$this->session}");
        return $response->json() ?? [];
    }

    public function logoutSession(): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/logout-session");
        Cache::forget("wppconnect_token_{$this->session}");
        return $response->json() ?? [];
    }

    public function startSession(): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/start-session");

        return $response->json();
    }

    public function startSessionWithPhone(string $phone): array
    {
        $response = $this->http()
            ->post("{$this->baseUrl}/api/{$this->session}/start-session", [
                'phone' => $this->formatPhone($phone),
            ]);

        return $response->json() ?? [];
    }

    public function statusSession(): array
    {
        $response = $this->http()->get("{$this->baseUrl}/api/{$this->session}/status-session");

        return $response->json();
    }

    public function isConnected(): bool
    {
        try {
            $status = $this->statusSession();
            return ($status['status'] ?? '') === 'CONNECTED';
        } catch (\Throwable) {
            return false;
        }
    }

    public function getQrCode(): array
    {
        $response = $this->http()->get("{$this->baseUrl}/api/{$this->session}/qrcode-session");

        $contentType = $response->header('Content-Type');

        // Endpoint retorna PNG bruto quando o QR está disponível
        if (str_contains($contentType, 'image/png')) {
            $base64 = 'data:image/png;base64,' . base64_encode($response->body());
            return ['qrcode' => $base64];
        }

        return $response->json() ?? ['qrcode' => null, 'message' => 'QR Code não disponível'];
    }

    // -------------------------------------------------------
    // Envio de mensagens
    // -------------------------------------------------------

    public function sendText(string $phone, string $message, string $referenciaTipo = '', int $referenciaId = null, ?int $userId = null): array
    {
        $fone     = $this->resolverFoneEnvio($phone, $userId);
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-message", [
            'phone'   => $fone,
            'message' => $message,
        ]);

        $this->logResponse('sendText', $phone, $response);
        $this->registrarDisparo($phone, $message, $response, $referenciaTipo, $referenciaId, $userId);

        return $response->json() ?? [];
    }

    public function sendImage(string $phone, string $imageUrl, string $caption = ''): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-image", [
            'phone'    => $this->formatPhone($phone),
            'base64'   => $imageUrl,
            'filename' => 'image.jpg',
            'caption'  => $caption,
        ]);

        $this->logResponse('sendImage', $phone, $response);

        return $response->json();
    }

    public function sendFile(string $phone, string $fileUrl, string $filename, string $caption = ''): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-file", [
            'phone'    => $this->formatPhone($phone),
            'base64'   => $fileUrl,
            'filename' => $filename,
            'caption'  => $caption,
        ]);

        $this->logResponse('sendFile', $phone, $response);

        return $response->json();
    }

    public function sendLinkPreview(string $phone, string $url, string $caption): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-link-preview", [
            'phone'   => [$this->formatPhone($phone)], // endpoint espera array
            'url'     => $url,
            'caption' => $caption,
        ]);

        $this->logResponse('sendLinkPreview', $phone, $response);

        return $response->json() ?? [];
    }

    // -------------------------------------------------------
    // Verificação de número
    // -------------------------------------------------------

    public function checkNumber(string $phone): array
    {
        $response = $this->http()->get("{$this->baseUrl}/api/{$this->session}/check-number-status/{$this->formatPhone($phone)}");

        return $response->json();
    }

    public function numberExists(string $phone): bool
    {
        try {
            $result = $this->checkNumber($phone);
            return ($result['numberExists'] ?? false) === true;
        } catch (\Throwable) {
            return false;
        }
    }

    // -------------------------------------------------------
    // Notificações de negócio
    // -------------------------------------------------------

    public function notificarAprovadores(
        int    $idSolicitacao,
        string $nmSolicitante,
        string $nmEmpresa,
        float  $vlTotal,
        array  $itens,
        array  $aprovadores,  // [['id_etapa' => X, 'cd_usuario' => Y, 'ds_cargo' => Z]]
        array  $cotacoes = []  // linhas de COMPRA_COTACAO (NM_FORNECEDOR, VL_TOTAL, ST_SELECIONADA, DS_MOTIVO_ESCOLHA)
    ): void {
        $appUrl  = rtrim(config('app.url'), '/');
        $linkSol = "{$appUrl}/compras/solicitacoes/{$idSolicitacao}";

        $itensTexto = collect($itens)
            ->map(fn($i) => "• {$i->QT_ITEM}x {$i->DS_ITEM}")
            ->join("\n");

        // Coloca o fornecedor ganhador no topo, com troféu e motivo da escolha
        $cotacoesOrdenadas = collect($cotacoes)
            ->sortByDesc(fn($c) => ($c->ST_SELECIONADA ?? 'N') === 'S' ? 1 : 0)
            ->values();

        $cotacoesTexto = $cotacoesOrdenadas
            ->map(function ($c) {
                $ganhador = ($c->ST_SELECIONADA ?? 'N') === 'S';
                $prefixo  = $ganhador ? '🏆' : '•';
                $valor    = 'R$ ' . number_format((float) ($c->VL_TOTAL ?? 0), 2, ',', '.');
                $linha    = "{$prefixo} {$c->NM_FORNECEDOR} — {$valor}";

                if ($ganhador && !empty(trim($c->DS_MOTIVO_ESCOLHA ?? ''))) {
                    $linha .= "\n   _Motivo: {$c->DS_MOTIVO_ESCOLHA}_";
                }

                return $linha;
            })
            ->join("\n");

        foreach ($aprovadores as $aprov) {
            $user = User::find($aprov['cd_usuario']);

            if (!$user || !$user->phone) {
                Log::warning("WppConnect: aprovador #{$aprov['cd_usuario']} sem telefone cadastrado.");
                continue;
            }

            $token = Str::random(32);

            $linkAcao = rtrim(config('app.url'), '/') . '/compras/acao?token=' . $token;

            $caption = implode("\n", [
                "🛒 *Nova Solicitação de Compra #{$idSolicitacao}*",
                "",
                "📋 *Solicitante:* {$nmSolicitante}",
                "🏢 *Empresa:* {$nmEmpresa}",
                "💰 *Valor Total:* R$ " . number_format($vlTotal, 2, ',', '.'),
                "",
                "*Itens:*",
                $itensTexto,
                "",
                "*Cotações:*",
                $cotacoesTexto !== '' ? $cotacoesTexto : "_Nenhuma cotação registrada._",
                "",
                "Toque no link para aprovar ou reprovar:",
                "🔗 " . $linkAcao,
            ]);

            $fone     = $this->resolverFoneEnvio((string) $user->phone, $user->id);
            $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-message", [
                'phone'   => $fone,
                'message' => $caption,
            ]);

            $sucesso = $this->resolverSucesso($response);

            WppDisparo::create([
                'user_id'         => $user->id,
                'phone'           => preg_replace('/\D/', '', (string) $user->phone),
                'mensagem'        => $caption,
                'status'          => $sucesso ? WppDisparo::STATUS_ENVIADO : WppDisparo::STATUS_FALHA,
                'erro'            => $sucesso ? null : substr($response->body(), 0, 500),
                'token'           => $token,
                'referencia_tipo' => 'compra_etapa',
                'referencia_id'   => $aprov['id_etapa'],
                'dt_envio'        => $sucesso ? now() : null,
                'dt_registro'     => now(),
            ]);
        }
    }

    public function notificarComprador(
        int    $idSolicitacao,
        string $nmEmpresa,
        string $nmSolicitante,
        string $nrCelular,
        array  $itens = []
    ): void {
        $link = rtrim(config('app.url'), '/') . "/compras/solicitacoes/{$idSolicitacao}";

        $linhas = [
            "🛒 *Nova Solicitação de Compra #{$idSolicitacao}*",
            "",
            "🏢 *Empresa:* {$nmEmpresa}",
            "👤 *Solicitante:* {$nmSolicitante}",
        ];

        if (!empty($itens)) {
            $linhas[] = "";
            $linhas[] = "*Itens:*";
            foreach ($itens as $item) {
                $linhas[] = "• {$item->QT_ITEM}x {$item->DS_ITEM}";
            }
        }

        $linhas[] = "";
        $linhas[] = "Acesse o sistema para iniciar a análise:";
        $linhas[] = $link;

        $this->sendText($nrCelular, implode("\n", $linhas));
    }

    public function notificarCompradorAprovacao(
        int    $idSolicitacao,
        string $nmEmpresa,
        float  $vlTotal,
        string $nrCelular
    ): void {
        $link = rtrim(config('app.url'), '/') . "/compras/solicitacoes/{$idSolicitacao}";

        $mensagem = implode("\n", [
            "✅ *Solicitação #{$idSolicitacao} Aprovada!*",
            "",
            "🏢 *Empresa:* {$nmEmpresa}",
            "💰 *Valor Total:* R$ " . number_format($vlTotal, 2, ',', '.'),
            "",
            "Todas as etapas de aprovação foram concluídas.",
            "Você pode prosseguir com a compra.",
            "",
            $link,
        ]);

        $this->sendText($nrCelular, $mensagem);
    }

    public function notificarCompradorReprovacao(
        int    $idSolicitacao,
        string $nmEmpresa,
        string $obs,
        string $nrCelular
    ): void {
        $link = rtrim(config('app.url'), '/') . "/compras/solicitacoes/{$idSolicitacao}";

        $mensagem = implode("\n", [
            "❌ *Solicitação #{$idSolicitacao} Reprovada*",
            "",
            "🏢 *Empresa:* {$nmEmpresa}",
            "📝 *Motivo:* {$obs}",
            "",
            "Acesse o sistema para mais detalhes:",
            $link,
        ]);

        $this->sendText($nrCelular, $mensagem);
    }

    public function reenviarDisparo(WppDisparo $disparo): void
    {
        $newToken = null;

        if ($disparo->referencia_tipo === 'compra_etapa') {
            // Mensagem de aprovação: gera novo token e incorpora o link
            $newToken = Str::random(32);
            $linkAcao = rtrim(config('app.url'), '/') . '/compras/acao?token=' . $newToken;
            $mensagem = $disparo->mensagem . "\n\n🔗 " . $linkAcao;
        } else {
            // Mensagem de texto simples: reenvia o conteúdo original
            $mensagem = $disparo->mensagem;
        }

        $fone     = $this->resolverFoneEnvio($disparo->phone, $disparo->user_id);
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-message", [
            'phone'   => $fone,
            'message' => $mensagem,
        ]);

        $sucesso = $this->resolverSucesso($response);

        $update = [
            'status'   => $sucesso ? WppDisparo::STATUS_ENVIADO : WppDisparo::STATUS_FALHA,
            'erro'     => $sucesso ? null : substr($response->body(), 0, 500),
            'dt_envio' => $sucesso ? now() : null,
        ];
        if ($newToken !== null) {
            $update['token'] = $newToken;
        }
        $disparo->update($update);

        if (!$sucesso) {
            throw new \RuntimeException(substr($response->body(), 0, 200));
        }
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // Resolve o identificador de envio: usa wpp_lid do usuário quando disponível,
    // pois contatos LID não aceitam envio via número no formato @c.us
    private function resolverFoneEnvio(string $phone, ?int $userId = null): string
    {
        if ($userId) {
            $lid = User::where('id', $userId)->value('wpp_lid');
            if ($lid) {
                return $lid . '@lid';
            }
        }

        return $this->formatPhone($phone);
    }

    private function resolverSucesso(\Illuminate\Http\Client\Response $response): bool
    {
        return $response->successful() && $response->json('status') === 'success';
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) <= 11) {
            $digits = '55' . $digits;
        }

        return $digits;
    }

    private function registrarDisparo(
        string $phone,
        string $mensagem,
        \Illuminate\Http\Client\Response $response,
        string $referenciaTipo = '',
        ?int   $referenciaId   = null,
        ?int   $userId         = null
    ): void {
        try {
            $sucesso = $this->resolverSucesso($response);

            $digits = preg_replace('/\D/', '', $phone);
            // LIDs têm 15 dígitos; resolve para o telefone real se possível
            if (strlen($digits) > 13) {
                $digits = User::where('wpp_lid', $digits)->value('phone') ?? $digits;
            }

            WppDisparo::create([
                'user_id'        => $userId ?? Auth::id() ?? 1,
                'phone'          => $digits,
                'mensagem'       => $mensagem,
                'status'         => $sucesso ? WppDisparo::STATUS_ENVIADO : WppDisparo::STATUS_FALHA,
                'erro'           => $sucesso ? null : substr($response->body(), 0, 500),
                'referencia_tipo' => $referenciaTipo ?: null,
                'referencia_id'   => $referenciaId,
                'dt_envio'       => $sucesso ? now() : null,
                'dt_registro'    => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('WppConnect: falha ao registrar disparo', ['error' => $e->getMessage()]);
        }
    }

    private function logResponse(string $method, string $phone, \Illuminate\Http\Client\Response $response): void
    {
        if (! $response->successful()) {
            Log::error("WppConnect: erro em {$method}", [
                'phone'    => $phone,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
        }
    }
}
