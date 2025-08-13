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
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();


        return view('admin.producao.pcp-producao', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa'
        ));
    }

    //tras as informaçõs dos pneus do lote de PCP
    public function getPneusLotePCP()
    {
        $data = $this->producao->getPneusLotePCP();
        return DataTables::of($data)
            ->make(true);
    }

    public function getLotePCP()
    {
        $data = $this->producao->getLotePCP();
        return DataTables::of($data)
            ->make(true);
    }



}
