<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\LiberaOrdemComercial;
use App\Models\PedidoPneu;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\SupervisorSubgrupo;
use App\Models\User;
use App\Services\SupervisorAuthService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class VendedorBorrachariaController extends Controller
{
    public User $user;
    public LiberaOrdemComercial $libera;
    public Request $request;
    public AreaComercial $area;
    public RegiaoComercial $regiao;
    public SupervisorAuthService $supervisorComercial;
    public SupervisorComercial $supervisorComercialModel;
    public SupervisorSubgrupo $supervisorSubgrupo;
    public PedidoPneu $pedido;

    public function __construct(
        User $user,
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        SupervisorAuthService $supervisorComercial,
        SupervisorComercial $supervisorComercialModel,
        SupervisorSubgrupo $supervisorSubgrupo,
        PedidoPneu $pedido,
    ) {
        $this->request = $request;
        $this->pedido = $pedido;
        $this->area = $area;
        $this->supervisorComercialModel = $supervisorComercialModel;
        $this->supervisorComercial = $supervisorComercial;
        $this->supervisorSubgrupo = $supervisorSubgrupo;
        $this->regiao = $regiao;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index() {
        
        $title_page   = 'Requisão Borracharia';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();
            if (is_null($supervisor)) {
                return Redirect::back()->with('warning', 'Usuário sem vinculo com Supervisor Comercial, fale com o Administrador do sistema!');
            }
        }               
        
        return view('admin.comercial.requisicao-borracharia', compact(
            'title_page',
            'user_auth',
            'uri',
            
        ));
    }


    public function getRequisicaoBorracharia() {
        return $this->pedido->getRequisicaoBorrachariaDataTable();

    }
}
