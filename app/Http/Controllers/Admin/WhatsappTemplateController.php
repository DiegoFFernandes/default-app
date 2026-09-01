<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappTemplate;
use App\Services\WhatsappCloudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhatsappTemplateController extends Controller
{
    public function __construct(private WhatsappCloudService $waba) {}

    public function index()
    {
        $templates = WhatsappTemplate::orderByDesc('id')->get();

        return view('admin.whatsapp.oficial.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regrasValidacao($request));

        if ($dados['header_tipo'] === 'DOCUMENT' && !$request->hasFile('header_arquivo')) {
            return response()->json(['errors' => 'Envie um PDF de amostra para o cabeçalho do tipo Documento.'], 422);
        }

        $template = WhatsappTemplate::create([
            'nome'        => $dados['nome'],
            'categoria'   => $dados['categoria'],
            'idioma'      => $dados['idioma'],
            'componentes' => $this->montarComponentes($dados),
        ]);

        if ($request->hasFile('header_arquivo')) {
            $template->update(['header_documento_path' => $this->salvarArquivoHeader($request, $template)]);
        }

        return response()->json(['success' => 'Template salvo como rascunho.', 'template' => $template]);
    }

    // Rascunho: nunca foi enviado, edita livre. Rejeitado: ja foi enviado e
    // recusado - edita pra corrigir e volta pra rascunho, pra poder reenviar.
    // Enviado/aprovado nao edita (ja esta em analise ou valendo na Meta).
    public function update(Request $request, WhatsappTemplate $template)
    {
        if (!in_array($template->status, ['rascunho', 'rejeitado', 'rejected'])) {
            return response()->json(['errors' => 'Só é possível editar templates em rascunho ou rejeitados.'], 422);
        }

        $dados = $request->validate($this->regrasValidacao($request, $template));

        $temArquivo = $request->hasFile('header_arquivo') || $template->header_documento_path;

        if ($dados['header_tipo'] === 'DOCUMENT' && !$temArquivo) {
            return response()->json(['errors' => 'Envie um PDF de amostra para o cabeçalho do tipo Documento.'], 422);
        }

        $caminhoArquivo = $template->header_documento_path;

        if ($dados['header_tipo'] !== 'DOCUMENT') {
            // Deixou de ser documento - o arquivo antigo (se tinha) nao serve mais.
            if ($caminhoArquivo) {
                Storage::delete($caminhoArquivo);
            }
            $caminhoArquivo = null;
        } elseif ($request->hasFile('header_arquivo')) {
            if ($caminhoArquivo) {
                Storage::delete($caminhoArquivo);
            }
            $caminhoArquivo = $this->salvarArquivoHeader($request, $template);
        }

        $template->update([
            'categoria'             => $dados['categoria'],
            'idioma'                => $dados['idioma'],
            'componentes'           => $this->montarComponentes($dados),
            'header_documento_path' => $caminhoArquivo,
            'status'                => 'rascunho',
            'motivo_rejeicao'       => null,
        ]);

        return response()->json(['success' => 'Template atualizado.', 'template' => $template]);
    }

    private function salvarArquivoHeader(Request $request, WhatsappTemplate $template): string
    {
        return $request->file('header_arquivo')->storeAs('whatsapp-templates', $template->id . '.pdf');
    }

    private function regrasValidacao(Request $request, ?WhatsappTemplate $template = null): array
    {
        return [
            'nome'         => $template
                ? 'required|string|max:512|regex:/^[a-z0-9_]+$/|unique:whatsapp_templates,nome,' . $template->id
                : 'required|string|max:512|regex:/^[a-z0-9_]+$/|unique:whatsapp_templates,nome',
            'categoria'    => 'required|in:UTILITY,MARKETING,AUTHENTICATION',
            'idioma'       => 'required|string|max:10',
            'header_tipo'    => 'nullable|in:TEXT,DOCUMENT',
            'header_texto'   => 'nullable|string|max:60',
            'header_arquivo' => 'nullable|file|mimes:pdf|max:5120',
            'corpo'        => 'required|string|max:1024',
            'rodape'       => 'nullable|string|max:60',
            // A Meta exige um exemplo de preenchimento pra cada {{n}} do corpo -
            // sem isso ela rejeita o template ("nao tem amostras de texto").
            'exemplos'     => ['nullable', 'string', 'max:512', function ($attribute, $value, $fail) use ($request) {
                $qtd = preg_match_all('/\{\{\d+\}\}/', (string) $request->input('corpo'));

                if ($qtd === 0) {
                    return;
                }

                $partes = array_filter(array_map('trim', explode(',', (string) $value)));

                if (count($partes) < $qtd) {
                    $fail("Preencha um exemplo para cada variável do corpo ({$qtd} no total).");
                }
            }],
        ];
    }

    private function montarComponentes(array $dados): array
    {
        $componentes = [];

        if (($dados['header_tipo'] ?? null) === 'TEXT' && !empty($dados['header_texto'])) {
            $componentes[] = ['type' => 'HEADER', 'format' => 'TEXT', 'text' => $dados['header_texto']];
        } elseif (($dados['header_tipo'] ?? null) === 'DOCUMENT') {
            $componentes[] = ['type' => 'HEADER', 'format' => 'DOCUMENT'];
        }

        $body = ['type' => 'BODY', 'text' => $dados['corpo']];

        if (!empty($dados['exemplos'])) {
            $exemplos = array_values(array_filter(array_map('trim', explode(',', $dados['exemplos']))));
            $body['example'] = ['body_text' => [$exemplos]];
        }

        $componentes[] = $body;

        if (!empty($dados['rodape'])) {
            $componentes[] = ['type' => 'FOOTER', 'text' => $dados['rodape']];
        }

        return $componentes;
    }

    // Submete o rascunho pra analise da Meta. Se ja existe na Meta (ex: foi
    // rejeitado e corrigido), edita pelo ID em vez de criar - a API nao deixa
    // criar de novo com o mesmo nome, mesmo que o anterior tenha sido recusado.
    public function submeter(WhatsappTemplate $template)
    {
        $componentes = $template->componentes;
        $indiceHeaderDocumento = collect($componentes)->search(
            fn($c) => $c['type'] === 'HEADER' && ($c['format'] ?? null) === 'DOCUMENT'
        );

        if ($indiceHeaderDocumento !== false) {
            if (!$template->header_documento_path || !Storage::exists($template->header_documento_path)) {
                return response()->json(['errors' => 'Falta o PDF de amostra do cabeçalho - edite o template e envie um arquivo.'], 422);
            }

            // Gerado na hora (nao guardado) porque o handle da Meta expira -
            // reaproveitar um handle antigo falharia num reenvio mais tarde.
            $handle = $this->waba->obterHandleDocumento(Storage::get($template->header_documento_path));

            if (!$handle) {
                return response()->json(['errors' => 'Falha ao enviar o PDF de amostra para a Meta.'], 422);
            }

            $componentes[$indiceHeaderDocumento]['example'] = ['header_handle' => [$handle]];
        }

        $definicao = [
            'nome'        => $template->nome,
            'categoria'   => $template->categoria,
            'idioma'      => $template->idioma,
            'componentes' => $componentes,
        ];

        $resposta = $template->meta_template_id
            ? $this->waba->editarTemplateRemoto($template->meta_template_id, $definicao)
            : $this->waba->criarTemplate($definicao);

        if (isset($resposta['error'])) {
            // error_user_msg e o mais util quando existe (ex: "variaveis nao podem
            // estar no inicio/fim do modelo") - message sozinho costuma ser generico
            // demais ("Invalid parameter") pra entender o que corrigir.
            $motivo = $resposta['error']['error_user_msg'] ?? $resposta['error']['message'] ?? 'erro desconhecido';

            return response()->json(['errors' => 'Falha ao enviar: ' . $motivo], 422);
        }

        // A criacao as vezes ja recusa na hora (escaneamento automatico), sem
        // passar por "PENDING" - a edicao normalmente so devolve {success:true}
        // e o status real chega depois via webhook/sincronizar.
        $statusMeta = strtoupper($resposta['status'] ?? 'PENDING');
        $rejeitado  = $statusMeta === 'REJECTED';

        $template->update([
            'status'            => $rejeitado ? 'rejeitado' : 'enviado',
            'meta_template_id'  => $resposta['id'] ?? $template->meta_template_id,
            'motivo_rejeicao'   => null,
        ]);

        if ($rejeitado) {
            return response()->json(['errors' => 'Template criado, mas rejeitado pela Meta na análise automática. O motivo detalhado deve chegar em instantes via webhook - clique em "Sincronizar status" se não aparecer.'], 422);
        }

        return response()->json(['success' => 'Template enviado para análise da Meta.']);
    }

    // Consulta a Meta e atualiza o status real (aprovado/rejeitado/em analise)
    // de todos os templates que ja foram enviados - cobre tambem os que foram
    // criados manualmente no WhatsApp Manager, pelo nome.
    public function sincronizar()
    {
        $remotos = collect($this->waba->listarTemplatesRemoto())->keyBy('name');

        foreach (WhatsappTemplate::where('status', '!=', 'rascunho')->get() as $template) {
            $remoto = $remotos->get($template->nome);

            if (!$remoto) {
                continue;
            }

            $template->update([
                'status'           => strtolower($remoto['status'] ?? $template->status),
                'meta_template_id' => $remoto['id'] ?? $template->meta_template_id,
                'motivo_rejeicao'  => $remoto['rejected_reason'] ?? null,
            ]);
        }

        return response()->json(['success' => 'Status sincronizado com a Meta.']);
    }

    public function destroy(WhatsappTemplate $template)
    {
        if ($template->status !== 'rascunho') {
            $this->waba->apagarTemplateRemoto($template->nome);
        }

        if ($template->header_documento_path) {
            Storage::delete($template->header_documento_path);
        }

        $template->delete();

        return response()->json(['success' => 'Template removido.']);
    }
}
