<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Role;
use Yajra\DataTables\Facades\DataTables;

class PcpProducaoController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user,
        Producao $producao,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->producao = $producao;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;

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
                    badge-" . ($row->STORDEM === 'A' ? 'warning' : ($row->STORDEM === 'F' ? 'success' : 'secondary')) . "'>" . $row->STATUS . "</span>";
            })
            ->rawColumns(['STORDEM'])
            ->make(true);
    }
}
