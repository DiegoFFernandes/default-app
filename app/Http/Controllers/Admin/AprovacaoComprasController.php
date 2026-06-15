<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraEtapaAprov;
use App\Services\CompraAprovacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AprovacaoComprasController extends Controller
{
    public $user;

    public function __construct(
        protected Request                $request,
        protected CompraEtapaAprov       $etapaAprov,
        protected CompraAprovacaoService $aprovacaoService
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Aprovações Pendentes';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        return view('admin.compras.aprovacoes.index', compact('title_page', 'user_auth', 'uri'));
    }

    public function list()
    {
        $data = $this->etapaAprov->getPendentesParaUsuario($this->user->id);

        return DataTables::of($data)
            ->addColumn('vl_total_fmt', fn($row) =>
                $row->VL_TOTAL ? 'R$ ' . number_format($row->VL_TOTAL, 2, ',', '.') : '-')
            ->addColumn('Actions', function ($row) {
                return '
                    <a href="' . route('compras.solicitacoes.show', $row->CD_SOLICITACAO) . '"
                        class="btn btn-info btn-xs mr-1" title="Visualizar">
                        <i class="fas fa-eye"></i></a>
                    <button data-id="' . $row->ID_ETAPA . '"
                        class="btn btn-success btn-xs btn-aprovar mr-1" title="Aprovar">
                        <i class="fas fa-check"></i></button>
                    <button data-id="' . $row->ID_ETAPA . '"
                        class="btn btn-danger btn-xs btn-reprovar" title="Reprovar">
                        <i class="fas fa-times"></i></button>
                ';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function aprovar()
    {
        $input = $this->request->validate([
            'id_etapa'      => 'required|integer',
            'ds_observacao' => 'nullable|string|max:500',
        ]);

        $result = $this->aprovacaoService->aprovar(
            $input['id_etapa'],
            $this->user->id,
            $input['ds_observacao'] ?? null
        );

        return response()->json($result);
    }

    public function reprovar()
    {
        $input = $this->request->validate([
            'id_etapa'      => 'required|integer',
            'ds_observacao' => 'required|string|max:500',
        ], [
            'ds_observacao.required' => 'O motivo da reprovação é obrigatório.',
        ]);

        $result = $this->aprovacaoService->reprovar(
            $input['id_etapa'],
            $this->user->id,
            $input['ds_observacao']
        );

        return response()->json($result);
    }
}
