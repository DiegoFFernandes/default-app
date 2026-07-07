<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WppDisparo;
use App\Models\WppLidPendente;
use App\Models\WppParametro;
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

    public function handle(Request $request)
    {
        $body  = $request->all();
        $event = $body['event'] ?? null;

        Log::info('WppConnect webhook recebido', ['event' => $event]);

        // Código de pareamento por número de telefone
        if ($event === 'phoneCode') {
            $code = $body['phoneCode'] ?? null;
            if ($code) {
                Cache::put('wppconnect_phone_code', $code, now()->addMinutes(5));
                Log::info('WppConnect: phoneCode recebido', ['code' => $code]);
            }
            return response()->json(['ok' => true]);
        }

        // Sessão fechada automaticamente (timeout de login)
        if ($event === 'status-find' && ($body['status'] ?? null) === 'autocloseCalled') {
            Cache::forget('wppconnect_phone_code');
            Log::info('WppConnect: sessão auto-fechada (autocloseCalled)');
            return response()->json(['ok' => true]);
        }

        // Ignora mensagens enviadas pelo próprio bot
        if ($body['fromMe'] ?? false) {
            return response()->json(['ok' => true]);
        }

        $texto  = trim($body['body'] ?? '');
        $from   = $body['from'] ?? ''; // Ex: 554184042323@c.us
        $phone  = preg_replace('/\D/', '', explode('@', $from)[0]);

        if (empty($texto) || empty($phone)) {
            return response()->json(['ok' => true]);
        }

        $pushname = $body['sender']['pushname'] ?? ($body['notifyName'] ?? null);

        // Se for LID (@lid) sem usuário mapeado, captura para associação posterior
        if (str_ends_with($from, '@lid')) {
            $jaExiste = User::where('wpp_lid', $phone)->exists();
            if (! $jaExiste) {
                WppLidPendente::updateOrCreate(
                    ['lid' => $phone],
                    ['pushname' => $pushname, 'ultimo_texto' => mb_substr($texto, 0, 200), 'updated_at' => now()],
                );
            }
        }

        $partes = preg_split('/\s+/', $texto, 3);
        $cmd    = strtoupper($partes[0] ?? '');
        $token  = $partes[1] ?? '';

        // Se o remetente usou LID, resolve para o telefone real cadastrado no usuário
        $destinatario = str_ends_with($from, '@lid')
            ? User::where('wpp_lid', $phone)->first(['id', 'phone'])
            : User::where('phone', $phone)->first(['id', 'phone']);

        $phoneSend = $destinatario?->phone ?? $phone;

        // Respostas automáticas — adicione seus casos aqui
        $resposta = $this->resolverResposta($cmd, $texto, $phone);
        if ($resposta !== null) {
            $this->wpp->sendText($phoneSend, $resposta, '', null, $destinatario?->id);
            return response()->json(['ok' => true]);
        }

        if (!in_array($cmd, ['APROVAR', 'REPROVAR']) || empty($token)) {
            return response()->json(['ok' => true]);
        }

        $disparo = WppDisparo::where('token', $token)->first();

        if (!$disparo) {
            Log::warning("WppConnect webhook: token não encontrado [{$token}]");
            return response()->json(['ok' => true]);
        }

        // Valida que quem responde é o dono do disparo
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

    private function resolverResposta(string $cmd, string $textoOriginal, string $phone): ?string
    {
        // Respostas fixas (sem chamar LLM)
        $fixa = match ($cmd) {
            'OI', 'OLÁ', 'OLA', 'HELLO', 'HI', 'Oie', 'Oi' =>
            "Olá! 👋Me faça uma pergunta sobre coletas, pedidos ou resultados!",
            'AJUDA', 'HELP' =>
            "Comandos disponíveis:\n\n✅ 💬 Você pode perguntar, por exemplo:\n• _como foi meu dia de coletas hoje_\n• _coletas de junho_",
            default => null,
        };

        if ($fixa !== null) {
            return $fixa;
        }

        // Comandos de aprovação não passam para o LLM
        if (in_array($cmd, ['APROVAR', 'REPROVAR'])) {
            return null;
        }

        // Mensagens muito curtas provavelmente não são perguntas de negócio
        if (mb_strlen($textoOriginal) < 10) {
            Log::debug('WppWebhook[IA]: mensagem muito curta, ignorando', ['texto' => $textoOriginal]);
            return null;
        }

        // IA desativada globalmente
        $iaAtivo = WppParametro::get('wpp_ia_ativo', '0');
        Log::debug('WppWebhook[IA]: wpp_ia_ativo', ['valor' => $iaAtivo]);
        if (! $iaAtivo) {
            return null;
        }

        // Busca por phone (formato @c.us) ou por wpp_lid (formato @lid)
        $usuario = User::where('phone', $phone)
            ->orWhere('wpp_lid', $phone)
            ->first();
        Log::debug('WppWebhook[IA]: busca usuário', ['identifier' => $phone, 'encontrado' => $usuario?->name]);
        if (! $usuario) {
            Log::debug('WppWebhook[IA]: identificador não encontrado em users', ['identifier' => $phone]);
            return null;
        }
        if (! $usuario->hasPermissionTo('wppconnect-ia')) {
            Log::debug('WppWebhook[IA]: usuário sem permissão wppconnect-ia', ['user' => $usuario->name]);
            return null;
        }

        Log::debug('WppWebhook[IA]: chamando IA', ['texto' => $textoOriginal, 'user' => $usuario->name]);

        // Tenta responder com IA
        try {
            $resposta = $this->ia->responderParaWhatsapp($textoOriginal);
            Log::debug('WppWebhook[IA]: resposta gerada', ['resposta' => $resposta]);
            return $resposta;
        } catch (\Throwable $e) {
            Log::error('WppWebhook: falha ao consultar IA', ['erro' => $e->getMessage()]);
            return null;
        }
    }

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
