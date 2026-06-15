<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraSolicitacao;
use App\Models\CompraSolicitacaoItem;
use App\Models\CompraCotacao;
use App\Models\CompraEtapaAprov;
use App\Models\Empresa;
use App\Services\CompraFluxoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        protected Empresa               $empresa
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
        $data = $this->solicitacao->getAll($this->user->id);

        return DataTables::of($data)
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'RAS' => ['secondary', 'Rascunho'],
                    'APR' => ['warning',   'Em Aprovação'],
                    'APC' => ['success',   'Aprovada'],
                    'REP' => ['danger',    'Reprovada'],
                ];
                [$color, $label] = $map[$row->ST_SOLICITACAO] ?? ['secondary', $row->ST_SOLICITACAO];
                return "<span class=\"badge badge-{$color}\">{$label}</span>";
            })
            ->addColumn('vl_total_fmt', fn($row) => $row->VL_TOTAL
                ? 'R$ ' . number_format($row->VL_TOTAL, 2, ',', '.')
                : '-')
            ->addColumn('Actions', function ($row) {
                $btn = '<a href="' . route('compras.solicitacoes.show', $row->CD_SOLICITACAO) . '"
                            class="btn btn-info btn-xs mr-1" title="Visualizar">
                            <i class="fas fa-eye"></i></a>';
                if ($row->ST_SOLICITACAO === 'RAS') {
                    $btn .= '<a href="' . route('compras.solicitacoes.edit', $row->CD_SOLICITACAO) . '"
                                class="btn btn-warning btn-xs mr-1" title="Editar">
                                <i class="fas fa-edit"></i></a>';
                    $btn .= '<button data-id="' . $row->CD_SOLICITACAO . '"
                                class="btn btn-danger btn-xs btn-delete" title="Excluir">
                                <i class="fas fa-trash"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['status_badge', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $title_page = 'Nova Solicitação de Compra';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();
        $empresas   = $this->empresa->empresa();

        return view('admin.compras.solicitacoes.form', compact('title_page', 'user_auth', 'uri', 'empresas'));
    }

    public function store()
    {
        $input = $this->_validateSolicitacao($this->request);
        $input['cd_usuario_solicitante'] = $this->user->id;

        try {
            $id = $this->solicitacao->store($input);
            return response()->json(['success' => 'Solicitação criada com sucesso!', 'id' => $id]);
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
        $cotacaoSel = $this->cotacao->getCotacaoSelecionada($id);
        $title_page = 'Solicitação #' . $id;
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        return view('admin.compras.solicitacoes.show', compact(
            'title_page', 'user_auth', 'uri',
            'solicitacao', 'itens', 'cotacoes', 'etapas', 'cotacaoSel'
        ));
    }

    public function edit($id)
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao || $solicitacao->ST_SOLICITACAO !== 'RAS') {
            return redirect()->route('compras.solicitacoes.index');
        }

        $title_page = 'Editar Solicitação #' . $id;
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();
        $empresas   = $this->empresa->empresa();
        $cotacoes   = $this->cotacao->getBySolicitacao($id);

        return view('admin.compras.solicitacoes.form', compact(
            'title_page', 'user_auth', 'uri', 'empresas', 'solicitacao', 'cotacoes'
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

    // --- Itens ---

    public function listItens($idSolicitacao)
    {
        $data = $this->solicitacaoItem->getBySolicitacao($idSolicitacao);

        return DataTables::of($data)
            ->addColumn('Actions', fn($row) =>
                '<button data-id="' . $row->ID . '" class="btn btn-danger btn-xs btn-delete-item" title="Remover"><i class="fas fa-trash"></i></button>'
            )
            ->rawColumns(['Actions'])
            ->make(true);
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
        return $request->validate([
            'cd_empresa'       => 'required|integer',
            'dt_solicitacao'   => 'required|date',
            'ds_justificativa' => 'required|string|max:500',
            'ds_observacao'    => 'nullable|string|max:500',
        ], [
            'cd_empresa.required'       => 'Selecione a empresa.',
            'dt_solicitacao.required'   => 'Informe a data da solicitação.',
            'ds_justificativa.required' => 'A justificativa é obrigatória.',
        ]);
    }
}
