<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\ServiceFiltroGrupoSubgrupo;
use App\Services\SupervisorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

class PcpProducaoController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $serviceFiltroGrupoSubgrupo;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user,
        Producao $producao,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        ServiceFiltroGrupoSubgrupo $serviceFiltroGrupoSubgrupo

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->producao = $producao;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->serviceFiltroGrupoSubgrupo = $serviceFiltroGrupoSubgrupo;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function pneusLotePCP()
    {
        $title_page   = 'Painel de PCP';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        if ($this->user->hasRole('admin')) {
            $empresa = $this->empresa->empresa();
        } else if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else {
            $empresa = $this->empresa->empresa($this->user->empresa);
        }
        return view('admin.producao.pcp.pcp-producao', compact(
            'title_page',
            'uri',
            'empresa'
        ));
    }

    //tras as informaçõs dos pneus do lote de PCP em atraso
    public function getPneusAtrasoLotePCP()
    {
        $cd_empresa = $this->request->validate([
            'cd_empresa' => 'required|integer',
        ])['cd_empresa'];

        $lote = $this->getLotePCP()->getData(true)['data'];

        $data = $this->producao->getPneusAtrasoLotePCP($cd_empresa);

        $datatables = DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return "<button class='btn btn-xs btn-danger btn-remover-pneus-lote' 
                            data-empresa='" . $row->IDEMPRESA . "' 
                            data-lote='" . $row->NR_LOTE . "'>
                             <i class='fa fa-trash'></i>
                        </button>
                        <button class='btn btn-xs btn-primary btn-transferir-pneus-lote' 
                            data-empresa='" . $row->IDEMPRESA . "' 
                            data-lote='" . $row->NR_LOTE . "'>
                             <i class='fa fa-exchange-alt'></i>
                        </button>
                        
                        ";
            })
            ->rawColumns(['actions'])
            ->make(true)
            ->getData();


        return response()->json([
            'lote' => $lote,
            'datatables' => $datatables,
        ]);
    }

    public function getLotePCP()
    {
        if ($this->user->hasRole('admin')) {
            $empresa = $this->empresa->empresa();
            $empresa = collect($empresa)->pluck('CD_EMPRESA')->implode(',');
        } else if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else {
            $empresa = $this->user->empresa;
        }
        $data = $this->producao->getLotePCP($empresa);
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return '<button class="btn btn-xs btn-secondary btn-pneus-lote" 
                            data-empresa="' . $row->CD_EMPRESA . '" 
                            data-lote="' . $row->NR_LOTE . '">
                        <i class="fa fa-eye" style="color: white;"></i></button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detalhesPneusLotePCP()
    {
        $validated = $this->request->validate([
            'empresa' => 'required|integer',
            'lote' => 'required|integer',
        ]);

        $cd_empresa = $validated['empresa'];
        $lote = $validated['lote'];

        $data = $this->producao->getDetalhesPneusLotePCP($cd_empresa, $lote);

        return DataTables::of($data)
            ->editColumn('STORDEM', function ($row) {
                return "<span class='badge 
                    badge-" . ($row->STORDEM === 'A' ? 'warning' : 
                                ($row->STORDEM === 'F' ? 'success' : 'danger')) . "'>" . $row->STATUS . 
                                
                                "</span>";
            })
            ->rawColumns(['STORDEM'])
            ->make(true);
    }

    public function consumoEstoqueLoteMateriaPrima()
    {
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos('5,6,7')['data'];

        $localestoque = 1;
        $tipolocalestoque = 1;

        $data = $this->producao->consumoEstoqueLoteMateriaPrima($subgrupo, $localestoque, $tipolocalestoque);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {

                if ($row->ST_BANDA === 'NAO') {
                    return '<button class="btn btn-xs btn-outline-warning btn-banda-sem-associacao">
                        <i class="fa fa-exclamation-circle"></i> VER</button>';
                } else {
                    return '<button class="btn btn-xs btn-outline-success btn-banda-com-associacao">
                        <i class="fa fa-check"></i></button>';
                }
            })
            ->editColumn('QT_CONSUMO', function ($row) {
                if (($row->QT_CONSUMO >= $row->QT_ESTOQUE)) {
                    return '<span class="badge badge-danger text-xs">' . $row->QT_CONSUMO . '</span>';
                } else {
                    return '<span class="badge badge-success text-xs">' . $row->QT_CONSUMO . '</span>';
                }
            })
            ->editColumn('QT_ESTOQUE', function ($row) {
                if (($row->QT_CONSUMO >= $row->QT_ESTOQUE)) {
                    return '<span class="badge badge-danger text-xs">' . $row->QT_ESTOQUE . '</span>';
                } else {
                    return '<span class="badge badge-success text-xs">' . $row->QT_ESTOQUE . '</span>';
                }
            })
            ->rawColumns(['QT_CONSUMO', 'QT_ESTOQUE', 'actions'])
            ->make(true);
    }

    public function bandasSemAssociacao()
    {
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos('5,6,7')['data'];
        
        $data = $this->producao->bandasSemAssociacao($subgrupo);

        return DataTables::of($data)
            ->make(true);
    }
}
