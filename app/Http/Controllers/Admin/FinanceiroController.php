<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Financeiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class FinanceiroController extends Controller
{
    public $request, $financeiro, $user, $empresa;
    public function __construct(
        Request $request,
        Financeiro $financeiro,
        Empresa $empresa,
    ) {
        $this->empresa = $empresa;
        $this->request = $request;
        $this->financeiro = $financeiro;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function liberaContas()
    {
        $title_page   = 'Liberação de Contas a Pagar';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();
        $uri = explode('/', $uri);
        $uri = $uri[1];

        return view('admin.financeiro.libera-contas', compact(
            'title_page',
            'user_auth',
            'uri'

        ));
    }
    public function listContasBloqueadas()
    {
        $status = $this->request->st_visto;
        $data = $this->financeiro->ContasBloqueadas($status);

        $datatables = DataTables::of($data)
            ->addColumn('actions', function ($d) {
                return '<button class="btn-detalhes details-control fas fa-plus-circle btn-table" aria-hidden="true"></button>
                        <button class="btn-detalhes details-centrocusto fas fa-align-justify btn-open btn-table" aria-hidden="true"></button>
                        <button class="btn-detalhes details-motivo far fa-comment-alt btn-open btn-table" aria-hidden="true"></button>
                                                
                        ';
            })
            ->rawColumns(['actions'])
            ->make(true)
            ->getData();

        $qtd_bloqueadas = count($data);
        $vlr_bloqueadas = array_sum(array_map(function ($item) {
            return $item->VL_DOCUMENTO;
        }, $data));

        $qtd_aguardando_analise = count(array_filter($data, function ($item) {
            return $item->ST_VISTO === 'N';
        }));
        $qtd_pendentes_bloqueadas = count(array_filter($data, function ($item) {
            return $item->ST_VISTO === 'S';
        }));


        $vlr_aguardando_analise = array_sum(array_map(function ($item) {
            return $item->ST_VISTO === 'N' ? $item->VL_DOCUMENTO : 0;
        }, $data));

        $vlr_pendentes_bloqueadas = array_sum(array_map(function ($item) {
            return $item->ST_VISTO === 'S' ? $item->VL_DOCUMENTO : 0;
        }, $data));

        return response()->json(
            [
                'datatables' => $datatables,
                'qtd_bloqueadas' => number_format($qtd_bloqueadas),
                'vlr_bloqueadas' => number_format($vlr_bloqueadas, 2, ',', '.'),

                'qtd_aguardando_analise' => number_format($qtd_aguardando_analise),
                'qtd_pendentes_bloqueadas' => number_format($qtd_pendentes_bloqueadas),

                'vlr_aguardando_analise' => number_format($vlr_aguardando_analise, 2, ',', '.'),
                'vlr_pendentes_bloqueadas' => number_format($vlr_pendentes_bloqueadas, 2, ',', '.')
            ]
        );
    }
    public function listHistoricoContasBloqueadas()
    {
        $cd_empresa = $this->request->cd_empresa;
        $nr_lancamento = $this->request->nr_lancamento;

        $data = $this->financeiro->listHistoricoContasBloqueadas($cd_empresa, $nr_lancamento);

        return DataTables::of($data)->make(true);
    }
    public function updateStatusContasBloqueadas()
    {
        $data = $this->request->all();

        foreach ($data['contas'] as $c) {
            $this->financeiro->updateStatusContasBloqueadas(
                $c['cd_empresa'],
                $c['nr_lancamento'],
                $c['status'],
                mb_convert_encoding($c['ds_liberacao'] . ' / ' . $data['ds_liberacao'], 'ISO-8859-1', 'UTF-8')


            );
            $status = $c['status'];
        }
        if ($status == 'S') {
            return response()->json(['warning' => 'Contas ainda esta bloqueada, movidas para bloqueadas pendentes!']);
        } else {
            return response()->json(['success' => 'Contas liberadas com sucesso!']);
        }
    }
    public function listCentroCustoContasBloqueadas()
    {
        $cd_empresa = $this->request->cd_empresa;
        $nr_lancamento = $this->request->nr_lancamento;

        $data = $this->financeiro->listCentroCustoContasBloqueadas($cd_empresa, $nr_lancamento);

        return Datatables::of($data)->make(true);
    }
}
