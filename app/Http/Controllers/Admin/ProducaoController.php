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
use Yajra\DataTables\Facades\DataTables;

class ProducaoController extends Controller
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

    public function index()
    {
        $title_page   = 'Produzidos - Sem Faturar';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();

        $user =  $this->user->getData();
        $regiao = "";

        if ($this->user->hasRole('admin|gerente comercial')) {
             $regiao = $this->regiao->regiaoAll();            
        } elseif ($this->user->hasRole('supervisor')) {
            $regiao = $this->regiao->findRegiaoUser($this->user->id);
        } elseif ($this->user->hasRole('gerente unidade')) {            
            $regiao = $this->regiao->regiaoAll();
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
            $empresa = $this->empresa->empresa($cd_empresa);
        }

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.producao.produzidos-sem-faturar', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'user',
            'list_regiao',
            'empresa'
        ));
    }
    public function getListPneusProduzidosFaturar()
    {
        $cd_regiao = "";

        if ($this->user->hasRole('admin|gerente comercial')) {
            $cd_regiao = "";
            $supervisor = 0;
            $cd_empresa = 0;
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            $supervisor = $this->supervisorComercial->getCdSupervisor();
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $supervisor = 0;            
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');           
        }

        if (!empty($this->request->data['regiao'])) {
            $cd_regiao = implode(',', $this->request->data['regiao']);
        }

        $data = $this->producao->getPneusProduzidosFaturar($cd_empresa, $cd_regiao, $supervisor, $this->request->data);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('CD_EMPRESA', function ($row) {
                return '<span class="right details-control mr-2"><i class="fas fa-plus-circle"></i></span> ' . $row->CD_EMPRESA;
            })
            ->addColumn('NM_PESSOA', function ($row) {
                return $row->NM_PESSOA;
            })
            ->addColumn('VALOR', function ($row) {
                return number_format($row->VALOR, 2, ',', '.');
            })
            ->addColumn('PNEUS', function ($row) {
                return $row->PNEUS;
            })
            ->addColumn('EXPEDICIONADO', function ($row) {
                return $row->EXPEDICIONADO;
            })
            ->addColumn('DTENTREGA', function ($row) {
                return date('d/m/Y', strtotime($row->DTENTREGA));
            })
            ->rawColumns(['CD_EMPRESA', 'NM_PESSOA', 'VALOR', 'PNEUS', 'EXPEDICIONADO', 'DTENTREGA'])
            ->make(true);
    }
    public function getListPneusProduzidosFaturarDetails()
    {
        $nr_embarque = $this->request->get('nr_embarque');
        $pedido = $this->request->get('pedido');


        if ($nr_embarque == 'SEM EMBARQUE') {
            $nr_embarque = 0;
        }

        $data = $this->producao->getPneusProduzidosFaturarDetails($pedido, $nr_embarque);

        return DataTables::of($data)->make(true);
    }
}
