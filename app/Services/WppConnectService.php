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

    public function startSession(): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/start-session");

        return $response->json();
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

    public function sendText(string $phone, string $message, string $referenciaTipo = '', int $referenciaId = null): array
    {
        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-message", [
            'phone'   => $this->formatPhone($phone),
            'message' => $message,
        ]);

        $this->logResponse('sendText', $phone, $response);
        $this->registrarDisparo($phone, $message, $response, $referenciaTipo, $referenciaId);

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
        array  $aprovadores  // [['id_etapa' => X, 'cd_usuario' => Y, 'ds_cargo' => Z]]
    ): void {
        $appUrl  = rtrim(config('app.url'), '/');
        $linkSol = "{$appUrl}/compras/solicitacoes/{$idSolicitacao}";

        $itensTexto = collect($itens)
            ->map(fn($i) => "• {$i->QT_ITEM}x {$i->DS_ITEM}")
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
                "Toque no link para aprovar ou reprovar.",
            ]);

            $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-link-preview", [
                'phone'   => [$this->formatPhone((string) $user->phone)],
                'url'     => $linkAcao,
                'caption' => $caption,
            ]);

            $sucesso = $response->successful() && ($response->json('status') === 'success');

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
        $newToken = Str::random(32);
        $linkAcao = rtrim(config('app.url'), '/') . '/compras/acao?token=' . $newToken;

        $response = $this->http()->post("{$this->baseUrl}/api/{$this->session}/send-link-preview", [
            'phone'   => [$this->formatPhone($disparo->phone)],
            'url'     => $linkAcao,
            'caption' => $disparo->mensagem,
        ]);

        $sucesso = $response->successful() && ($response->json('status') === 'success');

        $disparo->update([
            'token'    => $newToken,
            'status'   => $sucesso ? WppDisparo::STATUS_ENVIADO : WppDisparo::STATUS_FALHA,
            'erro'     => $sucesso ? null : substr($response->body(), 0, 500),
            'dt_envio' => $sucesso ? now() : null,
        ]);

        if (!$sucesso) {
            throw new \RuntimeException(substr($response->body(), 0, 200));
        }
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    private function formatPhone(string $phone): string
    {
        // Remove tudo que não é dígito
        $digits = preg_replace('/\D/', '', $phone);

        // Adiciona código do Brasil se não tiver
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
        ?int   $referenciaId   = null
    ): void {
        try {
            $sucesso = $response->successful() && ($response->json('status') === 'success');

            WppDisparo::create([
                'user_id'        => Auth::id() ?? 1,
                'phone'          => preg_replace('/\D/', '', $phone),
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
