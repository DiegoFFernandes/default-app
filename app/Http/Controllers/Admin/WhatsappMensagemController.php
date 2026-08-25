<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMensagem;
use App\Services\WhatsappCloudService;
use Illuminate\Http\Request;

class WhatsappMensagemController extends Controller
{
    public function __construct(private WhatsappCloudService $waba) {}

    public function index()
    {
        return response()->json(
            WhatsappMensagem::orderBy('id')->get()
        );
    }

    // Texto livre - so funciona dentro da janela de 24h (o contato ja ter
    // mandado mensagem pra esse numero). Serve tanto pra uso real quanto pra
    // gravar a prova em video da permissao whatsapp_business_messaging.
    public function store(Request $request)
    {
        $dados = $request->validate([
            'telefone' => 'required|string|max:20',
            'mensagem' => 'required|string|max:4096',
        ]);

        $resposta = $this->waba->enviarTexto($dados['telefone'], $dados['mensagem']);

        if (isset($resposta['error'])) {
            $motivo = $resposta['error']['error_data']['details'] ?? $resposta['error']['message'] ?? 'erro desconhecido';

            return response()->json(['errors' => 'Falha ao enviar: ' . $motivo], 422);
        }

        $mensagem = WhatsappMensagem::create([
            'direcao'  => 'enviada',
            'telefone' => $resposta['contacts'][0]['wa_id'] ?? $dados['telefone'],
            'mensagem' => $dados['mensagem'],
            'wamid'    => $resposta['messages'][0]['id'] ?? null,
            'status'   => $resposta['messages'][0]['message_status'] ?? 'accepted',
        ]);

        return response()->json(['success' => 'Mensagem enviada.', 'mensagem' => $mensagem]);
    }
}
