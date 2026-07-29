<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisparoContexto;
use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
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
            'hr_execucao' => 'required|date_format:H:i',
        ]);

        $this->contexto->updateHorario($id, $this->request->hr_execucao . ':00');

        return response()->json(['success' => 'Horário atualizado com sucesso!']);
    }

    public function listEnvios()
    {
        $hoje = now()->format('d.m.Y');

        $filtros = [
            'inicio_data' => $this->converterData($this->request->inicio_data, $hoje),
            'fim_data'    => $this->converterData($this->request->fim_data, $hoje),
            'cd_contexto' => $this->request->cd_contexto,
            'st_envio'    => $this->request->st_envio,
            'nm_pessoa'   => $this->request->nm_pessoa,
        ];

        // // Antes do inicio do disparo automatico (DT_INICIOENVIO) nao existe envio
        // // possivel - toda nota apareceria como "Pendente" sem sentido nenhum, entao
        // // a busca e travada e o motivo e avisado ao usuario em vez de rodar a query.
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
                ];
                return $labels[$row->ST_ENVIO] ?? $row->ST_ENVIO;
            })
            ->addColumn('action', function ($row) {
                if (empty($row->CD_ENVIO)) {
                    return '';
                }

                $btn = '<a href="' . route('disparo-automatico.envios.preview', ['id' => $row->CD_ENVIO]) . '"
                    target="_blank" class="btn btn-xs btn-primary mr-1" title="Pré-visualizar">
                    <i class="fa fa-envelope" aria-hidden="true"></i></a>';

                if (in_array($row->ST_ENVIO, ['F', 'V'])) {
                    $btn .= '<button class="btn btn-xs btn-danger mr-1 btn-motivo-falha-disparo" data-motivo="' . e($row->DS_MOTIVO) . '" title="Ver motivo">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></button>';

                    $btn .= '<button class="btn btn-xs btn-warning mr-1 btn-editar-email-disparo"
                        data-id="' . $row->CD_ENVIO . '" data-email="' . e($row->DS_EMAILDEST) . '" title="Editar e-mail do destinatário">
                        <i class="fa fa-pencil-alt" aria-hidden="true"></i></button>';

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

        return response()->json(['success' => 'Envio marcado para reenvio!']);
    }

    public function atualizarEmailEnvio(int $id)
    {
        $this->request->validate([
            'ds_emaildest' => 'required|email',
        ]);

        $this->envio->atualizarEmailDestino($id, $this->request->ds_emaildest);

        return response()->json(['success' => 'E-mail atualizado com sucesso!']);
    }

    public function previewEnvio(int $id, DisparoHandlerRegistry $registry)
    {
        [$envio, $email] = $this->montarEmailDoEnvio($id, $registry);

        return view('admin.follow-up.disparos.preview', compact('envio', 'email'));
    }

    public function previewAnexo(int $id, int $indice, DisparoHandlerRegistry $registry)
    {
        [, $email] = $this->montarEmailDoEnvio($id, $registry);
        $anexo = $this->anexoOuFalha($email, $indice);

        return response($anexo['conteudo'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $anexo['nome'] . '"',
        ]);
    }

    public function previewAnexoHtml(int $id, int $indice, DisparoHandlerRegistry $registry)
    {
        [, $email] = $this->montarEmailDoEnvio($id, $registry);
        $anexo = $this->anexoOuFalha($email, $indice);

        return response($anexo['html'], 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function montarEmailDoEnvio(int $id, DisparoHandlerRegistry $registry): array
    {
        $envio = $this->envio->find($id);

        if (!$envio) {
            abort(404, 'Envio não encontrado.');
        }

        $contexto = $this->contexto->find($envio->CD_CONTEXTO);
        $handler = $registry->resolve($contexto->CD_HANDLER);

        return [$envio, $handler->montarEmail($envio)];
    }

    private function anexoOuFalha(array $email, int $indice): array
    {
        if (!isset($email['anexos'][$indice])) {
            abort(404, 'Anexo não encontrado.');
        }

        return $email['anexos'][$indice];
    }

    private function converterData(?string $valor, string $default): string
    {
        $valor = $valor ?: $default;

        return Carbon::createFromFormat('d.m.Y', $valor)->format('Y-m-d');
    }
}
