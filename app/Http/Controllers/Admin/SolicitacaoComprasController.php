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
use App\Services\CompraFluxoService;
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
        protected CompraCentroCusto     $centroCusto
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
        $counts     = $this->solicitacao->getCounts($this->user->id);

        return view('admin.compras.solicitacoes.index', compact('title_page', 'user_auth', 'uri', 'counts'));
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
                    'APC' => ['success',   'Aprovada'],
                    'REP' => ['danger',    'Reprovada'],
                    'CAN' => ['dark',      'Cancelada'],
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
                if ($row->ST_SOLICITACAO === 'RAS') {
                    $btn .= '<button data-id="' . $row->CD_SOLICITACAO . '"
                                class="btn btn-danger btn-xs btn-delete" title="Excluir Rascunho">
                                <i class="fas fa-trash"></i></button>';
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

        return view('admin.compras.solicitacoes.show', compact(
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

        $itens               = $this->solicitacaoItem->getBySolicitacao($id);
        $cotacoes            = $this->cotacao->getBySolicitacao($id);
        $etapas              = $this->etapaAprov->getBySolicitacao($id);
        $empresas            = $this->empresa->empresa();
        $paramUsaCentrocusto = $this->paramEmpresa->getMapUsaCentrocusto();
        $title_page          = 'Solicitação #' . $id;
        $user_auth           = $this->user;
        $uri                 = $this->request->route()->uri();

        $saldoCiclo = $solicitacao->CD_CENTROCUSTO
            ? $this->centroCusto->getSaldoCiclo($solicitacao->CD_EMPRESA, $solicitacao->CD_CENTROCUSTO)
            : null;

        return view('admin.compras.solicitacoes.show', compact(
            'title_page', 'user_auth', 'uri',
            'solicitacao', 'itens', 'cotacoes', 'etapas', 'saldoCiclo',
            'empresas', 'paramUsaCentrocusto'
        ));
    }

    public function edit($id)
    {
        return redirect()->route('compras.solicitacoes.show', $id);
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
            return response()->json(['success' => 'Solicitação enviada para análise de compra!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()]);
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

        return $request->validate([
            'cd_empresa'       => 'required|integer',
            'dt_solicitacao'   => 'required|date',
            'ds_justificativa' => 'required|string|max:500',
            'ds_observacao'    => 'nullable|string|max:500',
            'cd_centrocusto'   => $usaCentrocusto ? 'required|integer' : 'nullable|integer',
        ], [
            'cd_empresa.required'       => 'Selecione a empresa.',
            'dt_solicitacao.required'   => 'Informe a data da solicitação.',
            'ds_justificativa.required' => 'A justificativa é obrigatória.',
            'cd_centrocusto.required'   => 'O Centro de Resultado é obrigatório para esta empresa.',
        ]);
    }
}
