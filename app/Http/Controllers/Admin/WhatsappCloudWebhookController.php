<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMensagem;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappCloudWebhookController extends Controller
{
    // Handshake de verificação exigido pela Meta ao salvar a Callback URL
    public function verify(Request $request)
    {
        if ($request->query('hub_mode') === 'subscribe'
            && hash_equals(config('services.whatsapp_cloud.verify_token'), (string) $request->query('hub_verify_token'))
        ) {
            return response($request->query('hub_challenge'), 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    // Recebe os eventos (mensagens, status de entrega, etc.)
    public function handle(Request $request)
    {
        if (! $this->assinaturaValida($request)) {
            Log::warning('WhatsApp Cloud webhook: assinatura inválida');
            return response('Forbidden', 403);
        }

        // Logado como string crua (nao o array parseado) pra nao esbarrar no
        // limite de profundidade do Monolog, que trunca campos aninhados
        // (ex: statuses[].errors) com "Over 9 levels deep, aborting normalization".
        Log::info('WhatsApp Cloud webhook recebido: ' . $request->getContent());

        $this->sincronizarStatusTemplate($request);
        $this->sincronizarMensagens($request);

        return response()->json(['ok' => true]);
    }

    // Grava mensagem recebida (aba Mensagens) e atualiza o status de entrega
    // das que a gente mandou (accepted -> sent -> delivered -> read/failed).
    private function sincronizarMensagens(Request $request): void
    {
        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) !== 'messages') {
                    continue;
                }

                $valor = $change['value'] ?? [];

                foreach ($valor['messages'] ?? [] as $msg) {
                    if (empty($msg['id']) || WhatsappMensagem::where('wamid', $msg['id'])->exists()) {
                        continue; // Meta pode reentregar o mesmo evento
                    }

                    WhatsappMensagem::create([
                        'direcao'  => 'recebida',
                        'telefone' => $msg['from'] ?? 'desconhecido',
                        'mensagem' => $msg['text']['body'] ?? ('[' . ($msg['type'] ?? 'mensagem') . ']'),
                        'wamid'    => $msg['id'],
                    ]);
                }

                foreach ($valor['statuses'] ?? [] as $status) {
                    if (empty($status['id'])) {
                        continue;
                    }

                    WhatsappMensagem::where('wamid', $status['id'])->update([
                        'status' => $status['status'] ?? null,
                    ]);
                }
            }
        }
    }

    // Mantem a tabela local em dia com aprovação/rejeição de template sem
    // precisar clicar em "Sincronizar status" - a Meta manda esse evento assim
    // que termina a analise (automatica ou manual).
    private function sincronizarStatusTemplate(Request $request): void
    {
        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) !== 'message_template_status_update') {
                    continue;
                }

                $valor = $change['value'] ?? [];
                $nome  = $valor['message_template_name'] ?? null;

                if (!$nome) {
                    continue;
                }

                WhatsappTemplate::where('nome', $nome)->update([
                    'status'          => strtolower($valor['event'] ?? 'enviado'),
                    'motivo_rejeicao' => $valor['rejection_info']['reason'] ?? null,
                ]);
            }
        }
    }

    private function assinaturaValida(Request $request): bool
    {
        $secret = config('services.whatsapp_cloud.app_secret');
        if (! $secret) {
            return true; // ainda não configurado (fase de teste)
        }

        $assinatura = $request->header('X-Hub-Signature-256', '');
        $esperada   = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($esperada, $assinatura);
    }
}
