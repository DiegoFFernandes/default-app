<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EnviaDisparoAutomaticoJob;
use App\Models\DisparoContexto;
use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
use App\Services\Disparos\DisparoHandlerInterface;
use App\Services\Disparos\DisparoHandlerRegistry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DisparoAutomaticoController extends Controller
{
    protected Request $request;
    protected DisparoContexto $contexto;
    protected DisparoEnvio $envio;
    protected NotaCliente $notaCliente;

    public function __construct(
        Request $request,
        DisparoContexto $contexto,
        DisparoEnvio $envio,
        NotaCliente $notaCliente
    ) {
        $this->request = $request;
        $this->contexto = $contexto;
        $this->envio = $envio;
        $this->notaCliente = $notaCliente;
    }

    public function listContextos()
    {
        return response()->json($this->contexto->getAll());
    }

    public function toggleContexto($id)
    {
        $status = $this->contexto->toggleAtivo($id);

        return response()->json([
            'success'  => $status === 'S' ? 'Contexto ativado com sucesso!' : 'Contexto desativado com sucesso!',
            'st_ativo' => $status,
        ]);
    }

    public function updateHorarioContexto($id)
    {
        $this->request->validate([
            'hr_execucao'       => 'required|date_format:H:i',
            'nr_intervalohoras' => 'required|integer|min:1',
        ], [
            'hr_execucao.required'    => 'Informe o horário de execução.',
            'hr_execucao.date_format' => 'Horário inválido - use o formato HH:MM.',
            'nr_intervalohoras.required' => 'Informe o intervalo em horas.',
            'nr_intervalohoras.integer'  => 'O intervalo deve ser um número inteiro.',
            'nr_intervalohoras.min'      => 'O intervalo deve ser de pelo menos 1 hora.',
        ]);

        $this->contexto->updateHorario($id, $this->request->hr_execucao . ':00', (int) $this->request->nr_intervalohoras);

        return response()->json(['success' => 'Horário atualizado com sucesso!']);
    }

    public function updateWhatsAppContexto($id)
    {
        $this->request->validate([
            'nr_limitediario'   => 'required|integer|min:1',
            'hr_janelainicio'   => 'required|date_format:H:i',
            'hr_janelafim'      => 'required|date_format:H:i|after:hr_janelainicio',
        ], [
            'nr_limitediario.required' => 'Informe o limite diário de envios.',
            'nr_limitediario.min'      => 'O limite diário deve ser de pelo menos 1.',
            'hr_janelainicio.required' => 'Informe o início da janela de horário.',
            'hr_janelainicio.date_format' => 'Horário inválido - use o formato HH:MM.',
            'hr_janelafim.required'    => 'Informe o fim da janela de horário.',
            'hr_janelafim.date_format' => 'Horário inválido - use o formato HH:MM.',
            'hr_janelafim.after'       => 'O fim da janela deve ser depois do início.',
        ]);

        $this->contexto->updateWhatsApp(
            $id,
            (int) $this->request->nr_limitediario,
            $this->request->hr_janelainicio,
            $this->request->hr_janelafim
        );

        return response()->json(['success' => 'Configuração de WhatsApp atualizada com sucesso!']);
    }

    public function listEnvios()
    {
        // Sem nenhum contexto ativo, o disparo automatico proprio nem esta
        // configurado - a busca nem roda, so avisa o usuario.
        if (!$this->contexto->existeAtivo()) {
            return response()->json([
                'draw'            => (int) $this->request->input('draw'),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'aviso'           => 'Não foi configurado nenhum envio próprio, somente no Junsoft.',
                'avisoTitulo'     => 'Disparo automático não configurado',
            ]);
        }

        $hoje = now()->format('d.m.Y');

        $filtros = [
            'inicio_data' => $this->converterData($this->request->inicio_data, $hoje),
            'fim_data'    => $this->converterData($this->request->fim_data, $hoje),
            'cd_contexto' => $this->request->cd_contexto,
            'st_envio'    => $this->request->st_envio,
            'nm_pessoa'   => $this->request->nm_pessoa,
        ];

        // Antes do inicio do disparo automatico (DT_INICIOENVIO) nao existe envio
        // possivel - toda nota apareceria como "Pendente" sem sentido nenhum, entao
        // a busca e travada e o motivo e avisado ao usuario em vez de rodar a query.
        // $dtInicioEnvio = $this->contexto->dataInicioMaisAntiga(
        //     $filtros['cd_contexto'] ? (int) $filtros['cd_contexto'] : null
        // );

        // if ($dtInicioEnvio && $filtros['inicio_data'] < $dtInicioEnvio) {
        //     return response()->json([
        //         'draw'            => (int) $this->request->input('draw'),
        //         'recordsTotal'    => 0,
        //         'recordsFiltered' => 0,
        //         'data'            => [],
        //         'aviso'           => 'O disparo automático começou em ' . Carbon::parse($dtInicioEnvio)->format('d/m/Y')
        //             . '. Antes dessa data não há envios - ajuste o período da busca.',
        //         'avisoTitulo'     => 'Período fora do disparo automático',
        //     ]);
        // }

        $data = $this->notaCliente->listarNotasEmitidas($filtros);

        return DataTables::of($data)
            ->addColumn('status_badge', function ($row) {
                $labels = [
                    'P' => '<span class="badge badge-secondary">Pendente de Envio</span>',
                    'A' => '<span class="badge badge-warning">Aguardando</span>',
                    'E' => '<span class="badge badge-success">Enviado</span>',
                    'V' => '<span class="badge badge-info">Enviado c/ Falha</span>',
                    'F' => '<span class="badge badge-danger">Falha</span>',
                    'L' => '<span class="badge badge-dark">Limite Atingido</span>',
                ];
                return $labels[$row->ST_ENVIO] ?? $row->ST_ENVIO;
            })
            ->addColumn('action', function ($row) {
                if (empty($row->CD_ENVIO)) {
                    // 'P' - nota emitida mas sem nenhuma linha em DISPARO_ENVIO ainda.
                    // Cria o pendente sob demanda em vez de esperar a marca d'agua.
                    return '<button class="btn btn-xs btn-success btn-criar-envio-disparo"
                        data-nr-lancamento="' . $row->NR_LANCAMENTO . '" data-cd-pessoa="' . $row->CD_PESSOA . '" title="Enviar">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                }

                $btn = '<a href="' . route('disparo-automatico.envios.preview', ['id' => $row->CD_ENVIO]) . '"
                    target="_blank" class="btn btn-xs btn-primary mr-1" title="Pré-visualizar">
                    <i class="fa fa-envelope" aria-hidden="true"></i></a>';

                if (in_array($row->ST_ENVIO, ['F', 'V', 'L'])) {
                    $btn .= '<button class="btn btn-xs btn-danger mr-1 btn-motivo-falha-disparo" data-motivo="' . e($row->DS_MOTIVO) . '" title="Ver motivo">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></button>';
                }

                if (in_array($row->ST_ENVIO, ['F', 'V', 'E', 'L'])) {
                    // Canal decide o que o botao de editar corrige - e-mail
                    // (destinatario + copia) ou telefone (WhatsApp).
                    if ($row->TP_CANAL === 'W') {
                        $btn .= '<button class="btn btn-xs btn-warning mr-1 btn-editar-telefone-disparo"
                            data-id="' . $row->CD_ENVIO . '" data-telefone="' . e($row->DS_TELEFONE) . '"
                            title="Editar telefone do destinatário">
                            <i class="fa fa-pencil-alt" aria-hidden="true"></i></button>';
                    } else {
                        $btn .= '<button class="btn btn-xs btn-warning mr-1 btn-editar-email-disparo"
                            data-id="' . $row->CD_ENVIO . '" data-email="' . e($row->DS_EMAILDEST) . '"
                            data-emailcopia="' . e($row->DS_EMAILCOPIA) . '" title="Editar e-mail do destinatário">
                            <i class="fa fa-pencil-alt" aria-hidden="true"></i></button>';
                    }

                    $btn .= '<button class="btn btn-xs btn-success btn-reenviar-disparo"
                        data-id="' . $row->CD_ENVIO . '" title="Reenviar">
                        <i class="fa fa-redo" aria-hidden="true"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function reenviarEnvio(int $id)
    {
        $this->envio->reenviar($id);

        // Acao manual do usuario e imediata - nao espera o proximo ciclo do
        // disparo automatico (nem o de e-mail, nem o de WhatsApp). Vale
        // tambem pra 'L' (Limite Atingido): reenviar e o jeito do usuario
        // dizer "manda mesmo assim, esse aqui e importante" - bypassa o
        // limite diario de proposito, sem refletir no NR_LIMITEDIARIO.
        $envio = $this->envio->find($id);
        $contexto = $this->contexto->find($envio->CD_CONTEXTO);
        EnviaDisparoAutomaticoJob::dispatch($id, $contexto->CD_HANDLER);

        return response()->json(['success' => 'Envio marcado para reenvio!']);
    }

    /**
     * Cria a linha em DISPARO_ENVIO para uma nota que ainda nao tem nenhum
     * registro (status 'P' na listagem) - acionado manualmente pelo usuario,
     * sem esperar a marca d'agua de gerarPendentes().
     */
    public function criarEnvioPendente(DisparoHandlerRegistry $registry)
    {
        $this->request->validate([
            'nr_lancamento' => 'required|integer',
            'cd_pessoa'     => 'required|integer',
        ]);

        $contexto = $this->contexto->porHandler('NOTA_BOLETO');

        if (!$contexto) {
            return response()->json(['message' => 'Nenhum contexto de disparo cadastrado.'], 422);
        }

        $handler = $registry->resolve($contexto->CD_HANDLER);
        $id = $handler->criarPendenteAvulso($contexto, (int) $this->request->nr_lancamento, (int) $this->request->cd_pessoa);

        if (!$id) {
            return response()->json(['message' => 'Já existe um envio registrado para esta nota.'], 422);
        }

        // Acao manual do usuario e imediata - mesma logica do reenviarEnvio().
        EnviaDisparoAutomaticoJob::dispatch($id, $contexto->CD_HANDLER);

        return response()->json(['success' => 'Envio criado e marcado para disparo!']);
    }

    public function atualizarEmailEnvio(int $id)
    {
        $this->request->validate([
            'ds_emaildest'  => 'required|email',
            'ds_emailcopia' => ['nullable', 'string', function ($attribute, $value, $fail) {
                foreach (explode(';', $value) as $email) {
                    $email = trim($email);

                    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $fail("O e-mail \"{$email}\" em cópia é inválido.");
                    }
                }
            }],
        ]);

        $this->envio->atualizarEmailDestino($id, $this->request->ds_emaildest, $this->request->ds_emailcopia);

        return response()->json(['success' => 'E-mail atualizado com sucesso!']);
    }

    public function atualizarTelefoneEnvio(int $id)
    {
        $this->request->validate([
            'ds_telefone' => ['required', 'regex:/^\d{10,11}$/'],
        ], [
            'ds_telefone.required' => 'Informe o telefone do destinatário.',
            'ds_telefone.regex'    => 'Telefone inválido - informe DDD + número, só números (10 ou 11 dígitos).',
        ]);

        $this->envio->atualizarTelefoneDestino($id, $this->request->ds_telefone);

        return response()->json(['success' => 'Telefone atualizado com sucesso!']);
    }

    public function previewEnvio(int $id, DisparoHandlerRegistry $registry)
    {
        [$envio, $handler] = $this->envioEHandler($id, $registry);
        $contexto = $this->contexto->find($envio->CD_CONTEXTO);
        // Sem PDF nenhum aqui - so lista titulo/nome dos anexos. Cada PDF so e
        // gerado (Chromium) se o usuario clicar no anexo especifico abaixo.
        $email = $handler->montarPreview($envio);

        return view('admin.follow-up.disparos.preview', compact('envio', 'email', 'contexto'));
    }

    public function previewAnexo(int $id, int $indice, DisparoHandlerRegistry $registry)
    {
        [$envio, $handler] = $this->envioEHandler($id, $registry);
        $anexo = $this->gerarAnexoOuFalha($handler, $envio, $indice);

        return response($anexo['conteudo'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $anexo['nome'] . '"',
        ]);
    }

    public function previewAnexoHtml(int $id, int $indice, DisparoHandlerRegistry $registry)
    {
        [$envio, $handler] = $this->envioEHandler($id, $registry);
        $anexo = $this->gerarAnexoOuFalha($handler, $envio, $indice);

        return response($anexo['html'], 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function envioEHandler(int $id, DisparoHandlerRegistry $registry): array
    {
        $envio = $this->envio->find($id);

        if (!$envio) {
            abort(404, 'Envio não encontrado.');
        }

        $contexto = $this->contexto->find($envio->CD_CONTEXTO);
        $handler = $registry->resolve($contexto->CD_HANDLER);

        return [$envio, $handler];
    }

    private function gerarAnexoOuFalha(DisparoHandlerInterface $handler, object $envio, int $indice): array
    {
        try {
            return $handler->gerarAnexo($envio, $indice);
        } catch (\OutOfRangeException) {
            abort(404, 'Anexo não encontrado.');
        }
    }

    private function converterData(?string $valor, string $default): string
    {
        $valor = $valor ?: $default;

        return Carbon::createFromFormat('d.m.Y', $valor)->format('Y-m-d');
    }
}
