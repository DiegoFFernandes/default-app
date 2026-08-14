<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WppDisparo;
use App\Models\WppLidPendente;
use App\Models\WppParametro;
use App\Models\WppSessao;
use App\Services\CompraAprovacaoService;
use App\Services\IAService;
use App\Services\WppConnectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WppWebhookController extends Controller
{
    public function __construct(
        private CompraAprovacaoService $aprovacaoService,
        private WppConnectService $wpp,
        private IAService $ia,
    ) {}

    // Ponto de entrada do webhook — roteia para o fluxo correto
    public function handle(Request $request)
    {
        $body  = $request->all();
        $event = $body['event'] ?? null;

        Log::info('WppConnect webhook recebido', ['event' => $event]);

        if ($resposta = $this->handleEvento($event, $body)) {
            return $resposta;
        }

        // Só processa eventos de mensagem recebida
        if ($event !== 'onmessage') {
            return response()->json(['ok' => true]);
        }

        if ($body['fromMe'] ?? false) {
            return response()->json(['ok' => true]);
        }

        // Deduplicação por ID da mensagem: impede processamento duplicado caso
        // o wa-js dispare chat.new_message mais de uma vez para a mesma mensagem
        $msgId = is_array($body['id'] ?? null)
            ? ($body['id']['_serialized'] ?? null)
            : ($body['id'] ?? null);

        if ($msgId) {
            $cacheKey = "wpp_msg_{$msgId}";
            if (Cache::has($cacheKey)) {
                Log::info('WppConnect: mensagem duplicada ignorada', ['id' => $msgId]);
                return response()->json(['ok' => true]);
            }
            Cache::put($cacheKey, true, now()->addMinutes(10));
        }

        $msg = $this->parseMensagem($body);
        if ($msg === null) {
            return response()->json(['ok' => true]);
        }

        // Responde pela mesma sessão em que a mensagem chegou — responder por outro
        // número abriria uma conversa diferente da que o usuário iniciou.
        $wpp = $this->wppDaSessao($body['session'] ?? null);

        $resposta = $this->resolverResposta($msg['cmd'], $msg['texto'], $msg['phone'], $msg['phoneSend'], $msg['userId'], $wpp);
        if ($resposta !== null) {
            $wpp->sendText($msg['phoneSend'], $resposta, '', null, $msg['userId']);
            return response()->json(['ok' => true]);
        }

        return $this->handleAprovacao($msg['cmd'], $msg['token'], $msg['phone'], $msg['partes']);
    }

    // Instância apontando para a sessão que recebeu a mensagem
    private function wppDaSessao(?string $session): WppConnectService
    {
        if ($session && WppSessao::where('session_name', $session)->exists()) {
            return new WppConnectService($session);
        }

        return $this->wpp;
    }

    // Trata eventos de sistema (phoneCode, autocloseCalled) — não são mensagens de usuário
    private function handleEvento(string $event, array $body): ?\Illuminate\Http\JsonResponse
    {
        if ($event === 'phoneCode') {
            $code    = $body['phoneCode'] ?? null;
            $session = $body['session'] ?? 'default';
            if ($code) {
                Cache::put("wppconnect_phone_code_{$session}", $code, now()->addMinutes(5));
                Log::info('WppConnect: phoneCode recebido', ['session' => $session, 'code' => $code]);
            }
            return response()->json(['ok' => true]);
        }

        if ($event === 'status-find' && ($body['status'] ?? null) === 'autocloseCalled') {
            $session = $body['session'] ?? 'default';
            Cache::forget("wppconnect_phone_code_{$session}");
            Log::info('WppConnect: sessão auto-fechada (autocloseCalled)', ['session' => $session]);
            return response()->json(['ok' => true]);
        }

        return null;
    }

    // Extrai e valida os dados da mensagem; captura LIDs desconhecidos; resolve destinatário
    private function parseMensagem(array $body): ?array
    {
        $texto = trim($body['body'] ?? '');
        $from  = $body['from'] ?? '';
        $phone = preg_replace('/\D/', '', explode('@', $from)[0]);

        if (empty($texto) || empty($phone)) {
            return null;
        }

        $pushname = $body['sender']['pushname'] ?? ($body['notifyName'] ?? null);

        if (str_ends_with($from, '@lid') && ! User::where('wpp_lid', $phone)->exists()) {
            WppLidPendente::updateOrCreate(
                ['lid' => $phone],
                ['pushname' => $pushname, 'ultimo_texto' => mb_substr($texto, 0, 200), 'updated_at' => now()],
            );
        }

        $partes = preg_split('/\s+/', $texto, 3);
        $cmd    = mb_strtoupper($partes[0] ?? '', 'UTF-8');
        $token  = $partes[1] ?? '';

        $destinatario = str_ends_with($from, '@lid')
            ? User::where('wpp_lid', $phone)->first(['id', 'phone'])
            : User::where('phone', $phone)->first(['id', 'phone']);

        return [
            'texto'     => $texto,
            'phone'     => $phone,
            'cmd'       => $cmd,
            'token'     => $token,
            'partes'    => $partes,
            'phoneSend' => $destinatario?->phone ?? $phone,
            'userId'    => $destinatario?->id,
        ];
    }

    // Processa comandos APROVAR/REPROVAR enviados via token pelo aprovador
    private function handleAprovacao(string $cmd, string $token, string $phone, array $partes): \Illuminate\Http\JsonResponse
    {
        if (! in_array($cmd, ['APROVAR', 'REPROVAR']) || empty($token)) {
            return response()->json(['ok' => true]);
        }

        $disparo = WppDisparo::where('token', $token)->first();

        if (! $disparo) {
            Log::warning("WppConnect webhook: token não encontrado [{$token}]");
            return response()->json(['ok' => true]);
        }

        if ((string) $disparo->phone !== $phone) {
            Log::warning("WppConnect webhook: phone não confere. Disparo: {$disparo->phone}, Remetente: {$phone}");
            return response()->json(['ok' => true]);
        }

        match ($disparo->referencia_tipo) {
            'compra_etapa' => $this->processarCompraEtapa($cmd, $disparo, $partes[2] ?? ''),
            default        => Log::warning("WppConnect webhook: referencia_tipo desconhecido [{$disparo->referencia_tipo}]"),
        };

        return response()->json(['ok' => true]);
    }

    // Orquestra o fluxo de resposta: fixas → IA
    private function resolverResposta(string $cmd, string $textoOriginal, string $phone, string $phoneSend, ?int $userId, WppConnectService $wpp): ?string
    {
        $fixa = $this->respostaFixa($cmd);
        if ($fixa !== null) return $fixa;

        if (in_array($cmd, ['APROVAR', 'REPROVAR'])) return null;

        if (! $this->podeUsarIA($textoOriginal, $phone)) return null;

        return $this->chamarIA($textoOriginal, $phoneSend, $userId, $wpp);
    }

    // Respostas imediatas para saudações e ajuda — sem chamar a IA
    private function respostaFixa(string $cmd): ?string
    {
        return match ($cmd) {
            'OI', 'OLÁ', 'OLA', 'HELLO', 'HI', 'OIE' =>
                "Olá! 👋 Me faça uma pergunta sobre coletas, pedidos ou resultados. Caso ainda tenha dúvida me envie a palavra *Ajuda*",
            'AJUDA', 'HELP' =>
                "Comandos disponíveis:\n\n💬 Você pode perguntar, por exemplo:\n• _como foi meu dia de coletas hoje_\n• _coletas de junho_\n• _coletas de janeiro da Unidade_",
            default => null,
        };
    }

    // Valida se a mensagem pode ser processada pela IA (tamanho, flag global, permissão do usuário)
    private function podeUsarIA(string $textoOriginal, string $phone): bool
    {
        if (mb_strlen($textoOriginal) < 10) {
            return false;
        }

        if (! WppParametro::get('wpp_ia_ativo', '0')) {
            return false;
        }

        $usuario = User::where('phone', $phone)->orWhere('wpp_lid', $phone)->first();

        if (! $usuario || ! $usuario->hasPermissionTo('wppconnect-ia')) {
            Log::debug('WppWebhook[IA]: acesso negado', ['phone' => $phone, 'usuario' => $usuario?->name]);
            return false;
        }

        return true;
    }

    // Envia "aguarde", chama a IA e retorna a resposta (ou fallback em caso de falha)
    private function chamarIA(string $textoOriginal, string $phoneSend, ?int $userId, WppConnectService $wpp): string
    {
        $wpp->sendText($phoneSend, "⏳ Um momento, estou consultando as informações...", '', null, $userId);

        try {
            $resposta = $this->ia->responderParaWhatsapp($textoOriginal);
            return $resposta ?? "Não consegui interpretar sua pergunta. Tente de outra forma, por exemplo: _coletas de hoje_ ou _coletas de junho_.";
        } catch (\Throwable $e) {
            Log::error('WppWebhook: falha ao consultar IA', ['erro' => $e->getMessage()]);
            return "Ocorreu um erro ao processar sua pergunta. Tente novamente em instantes.";
        }
    }

    // Executa a aprovação ou reprovação de uma etapa de compra e invalida o token
    private function processarCompraEtapa(string $cmd, WppDisparo $disparo, string $obs): void
    {
        $idEtapa   = $disparo->referencia_id;
        $cdUsuario = $disparo->user_id;

        if ($cmd === 'APROVAR') {
            $result = $this->aprovacaoService->aprovar($idEtapa, $cdUsuario, $obs ?: null);
        } else {
            if (empty(trim($obs))) {
                Log::warning("WppConnect webhook: REPROVAR sem motivo. Etapa: {$idEtapa}");
                return;
            }
            $result = $this->aprovacaoService->reprovar($idEtapa, $cdUsuario, $obs);
        }

        // Invalida o token após uso
        $disparo->update(['token' => null]);

        Log::info("WppConnect webhook: {$cmd} etapa #{$idEtapa}", $result);
    }
}
