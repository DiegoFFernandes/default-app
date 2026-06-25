<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SolicitacaoCompraExport;
use App\Http\Controllers\Controller;
use App\Models\CompraSolicitacao;
use App\Models\CompraSolicitacaoItem;
use App\Models\CompraCotacao;
use App\Models\CompraCentroCusto;
use App\Models\CompraEtapaAprov;
use App\Models\CompraParamEmpresa;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Services\CompraFluxoService;
use App\Services\WppConnectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SolicitacaoComprasController extends Controller
{
    public $user;

    public function __construct(
        protected Request               $request,
        protected CompraSolicitacao     $solicitacao,
        protected CompraSolicitacaoItem $solicitacaoItem,
        protected CompraCotacao         $cotacao,
        protected CompraEtapaAprov      $etapaAprov,
        protected CompraFluxoService    $fluxoService,
        protected Empresa               $empresa,
        protected CompraParamEmpresa    $paramEmpresa,
        protected CompraCentroCusto     $centroCusto,
        protected WppConnectService     $wpp
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Solicitações de Compra';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        $isSolicitante = $this->user->can('solicitacao-compra-criar')
            && !$this->user->can('solicitacao-compra-gerenciar')
            && !$this->user->can('solicitacao-compra-aprovar');

        $stats  = $this->solicitacao->getStats($isSolicitante ? $this->user->id : null);
        $counts = $stats->map(fn($r) => $r->QT ?? 0);

        return view('admin.compras.solicitacoes.index', compact('title_page', 'user_auth', 'uri', 'counts', 'stats'));
    }

    public function kanban()
    {
        $title_page = 'Kanban — Solicitações de Compra';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        $isSolicitante = $this->user->can('solicitacao-compra-criar')
            && !$this->user->can('solicitacao-compra-gerenciar')
            && !$this->user->can('solicitacao-compra-aprovar');

        $data = $this->solicitacao->getAll($isSolicitante ? $this->user->id : null);

        $userIds = collect($data)->pluck('CD_USUARIO_SOLICITANTE')->unique()->filter();
        $users   = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');

        $colunas = [
            'RAS' => ['secondary', 'fa-file-alt',      'Rascunho'],
            'ANA' => ['info',      'fa-search',         'Em Análise'],
            'APR' => ['warning',   'fa-clock',          'Em Aprovação'],
            'APC' => ['primary',   'fa-check-circle',   'Aprovada'],
            'REP' => ['danger',    'fa-times-circle',   'Reprovada'],
            'FIN' => ['success',   'fa-flag-checkered', 'Finalizada'],
        ];

        $grupos = collect($data)->groupBy('ST_SOLICITACAO');

        return view('admin.compras.solicitacoes.kanban', compact(
            'title_page', 'user_auth', 'uri', 'colunas', 'grupos', 'users'
        ));
    }

    public function list()
    {
        $isSolicitante = $this->user->can('solicitacao-compra-criar')
            && !$this->user->can('solicitacao-compra-gerenciar')
            && !$this->user->can('solicitacao-compra-aprovar');

        $data = $this->solicitacao->getAll($isSolicitante ? $this->user->id : null);

        $userIds = collect($data)->pluck('CD_USUARIO_SOLICITANTE')->unique()->filter();
        $users   = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');

        return DataTables::of($data)
            ->addColumn('nm_solicitante', fn($row) => $users[$row->CD_USUARIO_SOLICITANTE] ?? '-')
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'RAS' => ['secondary', 'Rascunho'],
                    'ANA' => ['info',      'Em Análise'],
                    'APR' => ['warning',   'Em Aprovação'],
                    'APC' => ['primary',   'Compra Aprovada'],
                    'REP' => ['danger',    'Reprovada'],
                    'CAN' => ['dark',      'Cancelada'],
                    'FIN' => ['success',   'Compra Finalizada'],
                ];
                [$color, $label] = $map[$row->ST_SOLICITACAO] ?? ['secondary', $row->ST_SOLICITACAO];
                return "<span class=\"badge badge-{$color} badge-status\">{$label}</span>";
            })
            ->addColumn('vl_total_fmt', fn($row) => $row->VL_TOTAL
                ? 'R$ ' . number_format($row->VL_TOTAL, 2, ',', '.')
                : '-')
            ->addColumn('Actions', function ($row) {
                $btn = '<a href="' . route('compras.solicitacoes.show', $row->CD_SOLICITACAO) . '"
                            class="btn btn-primary btn-xs mr-1" title="Visualizar">
                            <i class="fas fa-eye" style="color: white"></i></a>';
                if (in_array($row->ST_SOLICITACAO, ['RAS', 'ANA'])) {
                    $btn .= '<a href="' . route('compras.solicitacoes.edit', $row->CD_SOLICITACAO) . '"
                                class="btn btn-warning btn-xs mr-1" title="Editar">
                                <i class="fas fa-edit"></i></a>';
                }
                if ($row->ST_SOLICITACAO === 'RAS') {
                    $btn .= '<button data-id="' . $row->CD_SOLICITACAO . '"
                                class="btn btn-danger btn-xs btn-delete" title="Excluir Rascunho">
                                <i class="fas fa-trash"></i></button>';
                }
                if ($row->ST_SOLICITACAO === 'APC') {
                    $btn .= '<button data-id="' . $row->CD_SOLICITACAO . '"
                                class="btn btn-success btn-xs btn-finalizar" title="Finalizar Compra">
                                <i class="fas fa-flag-checkered"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['status_badge', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $title_page          = 'Nova Solicitação de Compra';
        $user_auth           = $this->user;
        $uri                 = $this->request->route()->uri();
        $empresas            = $this->empresa->empresa();
        $paramUsaCentrocusto = $this->paramEmpresa->getMapUsaCentrocusto();
        $solicitacao         = null;
        $itens               = [];
        $cotacoes            = [];
        $etapas              = [];
        $saldoCiclo          = null;

        return view('admin.compras.solicitacoes.edit', compact(
            'title_page', 'user_auth', 'uri', 'empresas', 'paramUsaCentrocusto',
            'solicitacao', 'itens', 'cotacoes', 'etapas', 'saldoCiclo'
        ));
    }

    public function store()
    {
        $input = $this->_validateSolicitacao($this->request);
        $input['cd_usuario_solicitante'] = $this->user->id;

        try {
            $id = $this->solicitacao->store($input);
            return response()->json(['success' => 'Solicitação criada com sucesso, vamos adicionar os itens!', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao criar solicitação: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao) {
            return redirect()->route('compras.solicitacoes.index');
        }

        $itens      = $this->solicitacaoItem->getBySolicitacao($id);
        $cotacoes   = $this->cotacao->getBySolicitacao($id);
        $etapas     = $this->etapaAprov->getBySolicitacao($id);
        $title_page = 'Solicitação #' . $id;
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        $saldoCiclo = $solicitacao->CD_CENTROCUSTO
            ? $this->centroCusto->getSaldoCiclo($solicitacao->CD_EMPRESA, $solicitacao->CD_CENTROCUSTO)
            : null;

        return view('admin.compras.solicitacoes.show', compact(
            'title_page', 'user_auth', 'uri',
            'solicitacao', 'itens', 'cotacoes', 'etapas', 'saldoCiclo'
        ));
    }

    public function edit($id)
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao || !in_array($solicitacao->ST_SOLICITACAO, ['RAS', 'ANA'])) {
            return redirect()->route('compras.solicitacoes.show', $id);
        }

        $itens               = $this->solicitacaoItem->getBySolicitacao($id);
        $cotacoes            = $this->cotacao->getBySolicitacao($id);
        $etapas              = $this->etapaAprov->getBySolicitacao($id);
        $empresas            = $this->empresa->empresa();
        $paramUsaCentrocusto = $this->paramEmpresa->getMapUsaCentrocusto();
        $title_page          = 'Editar Solicitação #' . $id;
        $user_auth           = $this->user;
        $uri                 = $this->request->route()->uri();

        $saldoCiclo = $solicitacao->CD_CENTROCUSTO
            ? $this->centroCusto->getSaldoCiclo($solicitacao->CD_EMPRESA, $solicitacao->CD_CENTROCUSTO)
            : null;

        return view('admin.compras.solicitacoes.edit', compact(
            'title_page', 'user_auth', 'uri',
            'solicitacao', 'itens', 'cotacoes', 'etapas', 'saldoCiclo',
            'empresas', 'paramUsaCentrocusto'
        ));
    }

    public function update($id)
    {
        $input = $this->_validateSolicitacao($this->request);

        try {
            $this->solicitacao->updateData($id, $input);
            return response()->json(['success' => 'Solicitação atualizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->solicitacao->deleteById($id);
            return response()->json(['success' => 'Solicitação excluída com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao excluir.']);
        }
    }

    public function submeter($id)
    {
        $result = $this->fluxoService->submeter($id, $this->user->id);

        return isset($result['errors'])
            ? response()->json(['errors' => $result['errors']])
            : response()->json(['success' => $result['success']]);
    }

    public function enviarAnalise($id)
    {
        $itens = $this->solicitacaoItem->getBySolicitacao($id);

        if (empty($itens)) {
            return response()->json(['errors' => 'Adicione pelo menos um item antes de enviar para análise.']);
        }

        try {
            $this->solicitacao->enviarAnalise((int) $id);

            try {
                $sol       = $this->solicitacao->findById((int) $id);
                $comprador = $this->paramEmpresa->getCompradorByEmpresa((int) $sol->CD_EMPRESA);

                if ($comprador && !empty($comprador->NR_CELULAR)) {
                    $this->wpp->notificarComprador(
                        (int) $id,
                        $sol->NM_EMPRESA,
                        $this->user->name,
                        $comprador->NR_CELULAR,
                        $itens
                    );
                }
            } catch (\Throwable) {
                // notificação falhou mas não impede o fluxo
            }

            return response()->json(['success' => 'Solicitação enviada para análise de compra!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()]);
        }
    }

    public function finalizar(int $id)
    {
        $solicitacao = $this->solicitacao->findById((int) $id);

        if (!$solicitacao) {
            return response()->json(['errors' => 'Solicitação não encontrada.']);
        }

        if ($solicitacao->ST_SOLICITACAO !== 'APC') {
            return response()->json(['errors' => 'Apenas solicitações Aprovadas para Compra podem ser finalizadas.']);
        }

        try {
            $this->solicitacao->updateStatus((int) $id, 'FIN');
            return response()->json(['success' => 'Compra finalizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao finalizar: ' . $e->getMessage()]);
        }
    }

    public function cancelar($id)
    {
        try {
            $this->solicitacao->cancelar((int) $id);
            return response()->json(['success' => 'Solicitação cancelada com sucesso!']);
        } catch (\Exception) {
            return response()->json(['errors' => 'Erro ao cancelar solicitação.']);
        }
    }

    public function exportarExcel($id)
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao) {
            return redirect()->route('compras.solicitacoes.index');
        }

        $itens = $this->solicitacaoItem->getBySolicitacao($id);

        $filename = 'Solicitacao_'
            . str_pad($id, 6, '0', STR_PAD_LEFT)
            . '_' . Str::slug($solicitacao->NM_EMPRESA)
            . '.xlsx';

        return Excel::download(new SolicitacaoCompraExport($solicitacao, $itens), $filename);
    }

    // --- Itens ---

    public function itensTooltip(int $id)
    {
        $itens = $this->solicitacaoItem->getBySolicitacao($id);
        return response()->json(
            collect($itens)->map(fn($i) => [
                'ds_item'    => $i->DS_ITEM,
                'qt_item'    => number_format((float) $i->QT_ITEM, 3, ',', '.'),
                'ds_unidade' => $i->DS_UNIDADE,
            ])->values()
        );
    }

    public function listItens($idSolicitacao)
    {
        $data = $this->solicitacaoItem->getBySolicitacao($idSolicitacao);

        return DataTables::of($data)
            ->addColumn('Actions', function ($row) {
                $edit = '<button '
                    . 'data-id="' . $row->ID . '" '
                    . 'data-cd="' . $row->CD_ITEM . '" '
                    . 'data-ds="' . e($row->DS_ITEM) . '" '
                    . 'data-qt="' . $row->QT_ITEM . '" '
                    . 'data-un="' . e($row->DS_UNIDADE) . '" '
                    . 'data-obs="' . e($row->DS_OBSERVACAO ?? '') . '" '
                    . 'class="btn btn-warning btn-xs btn-edit-item mr-1" title="Editar">'
                    . '<i class="fas fa-edit"></i></button>';
                $del = '<button data-id="' . $row->ID . '" '
                    . 'class="btn btn-danger btn-xs btn-delete-item" title="Remover">'
                    . '<i class="fas fa-trash"></i></button>';
                return $edit . $del;
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function updateItem($id)
    {
        $input = $this->request->validate([
            'cd_item'       => 'required|integer',
            'qt_item'       => 'required|numeric|min:0.001',
            'ds_unidade'    => 'required|string|max:10',
            'ds_observacao' => 'nullable|string|max:300',
        ], [
            'cd_item.required'    => 'Selecione o produto.',
            'qt_item.required'    => 'Informe a quantidade.',
            'qt_item.numeric'     => 'A quantidade deve ser numérica.',
            'qt_item.min'         => 'A quantidade deve ser maior que zero.',
            'ds_unidade.required' => 'Informe a unidade.',
        ]);

        try {
            $this->solicitacaoItem->updateData($id, $input);
            return response()->json(['success' => 'Item atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar item.']);
        }
    }

    public function storeItem()
    {
        $input = $this->request->validate([
            'id_solicitacao' => 'required|integer',
            'cd_item'        => 'required|integer',
            'qt_item'        => 'required|numeric|min:0.001',
            'ds_unidade'     => 'required|string|max:10',
            'ds_observacao'  => 'nullable|string|max:300',
        ], [
            'cd_item.required'   => 'Selecione o produto.',
            'qt_item.required'   => 'Informe a quantidade.',
            'qt_item.numeric'    => 'A quantidade deve ser numérica.',
            'qt_item.min'        => 'A quantidade deve ser maior que zero.',
            'ds_unidade.required'=> 'Informe a unidade.',
            'ds_unidade.max'     => 'A unidade deve ter no máximo 10 caracteres.',
        ]);

        try {
            $id = $this->solicitacaoItem->store($input);
            return response()->json(['success' => 'Item adicionado!', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao adicionar item: ' . $e->getMessage()]);
        }
    }

    public function destroyItem($id)
    {
        try {
            $this->solicitacaoItem->deleteById($id);
            return response()->json(['success' => 'Item removido!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao remover item.']);
        }
    }

    // --- AJAX Search ---

    public function searchVeiculos()
    {
        $q = $this->request->get('q', '');
        return response()->json(Veiculo::search($q));
    }

    public function searchItem()
    {
        $term = $this->request->get('q', '');

        if (mb_strlen($term) < 3) {
            return response()->json([]);
        }

        return response()->json(\Helper::ConvertFormatText($this->solicitacaoItem->searchItem($term)));
    }

    public function searchFornecedor()
    {
        $term = $this->request->get('q', '');

        if (mb_strlen($term) < 3) {
            return response()->json([]);
        }

        return response()->json(\Helper::ConvertFormatText($this->cotacao->searchFornecedor($term)));
    }

    private function _validateSolicitacao($request)
    {
        $paramMap       = $this->paramEmpresa->getMapUsaCentrocusto();
        $usaCentrocusto = ($paramMap[$request->cd_empresa] ?? 'N') === 'S';
        $isLogistica    = (int) $request->cd_centrocusto === 1700;

        return $request->validate([
            'cd_empresa'       => 'required|integer',
            'dt_solicitacao'   => 'required|date',
            'ds_justificativa' => 'required|string|max:500',
            'ds_observacao'    => 'nullable|string|max:500',
            'cd_centrocusto'   => $usaCentrocusto ? 'required|integer' : 'nullable|integer',
            'st_urgencia'      => 'required|in:I,U,N',
            'tp_solicitacao'   => $isLogistica ? 'required|in:C,P'        : 'nullable|in:C,P',
            'nr_km'            => $isLogistica ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'nr_placa'         => $isLogistica ? 'required|string|max:10' : 'nullable|string|max:10',
        ], [
            'cd_empresa.required'       => 'Selecione a empresa.',
            'dt_solicitacao.required'   => 'Informe a data da solicitação.',
            'ds_justificativa.required' => 'A justificativa é obrigatória.',
            'cd_centrocusto.required'   => 'O Centro de Resultado é obrigatório para esta empresa.',
            'st_urgencia.required'      => 'Selecione a urgência.',
            'st_urgencia.in'            => 'Urgência inválida.',
            'tp_solicitacao.required'   => 'Selecione o tipo (Corretiva/Preventiva) para solicitações de Logística.',
            'nr_km.required'            => 'O KM atual é obrigatório para solicitações de Logística.',
            'nr_placa.required'         => 'A placa é obrigatória para solicitações de Logística.',
        ]);
    }
}
