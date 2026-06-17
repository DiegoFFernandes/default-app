<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WppDisparo;
use App\Services\CompraAprovacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WppWebhookController extends Controller
{
    public function __construct(private CompraAprovacaoService $aprovacaoService) {}

    public function handle(Request $request)
    {
        $body = $request->all();

        Log::info('WppConnect webhook recebido', $body);

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

        $partes = preg_split('/\s+/', $texto, 3);
        $cmd    = strtoupper($partes[0] ?? '');
        $token  = $partes[1] ?? '';

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
