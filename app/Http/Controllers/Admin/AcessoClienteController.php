<?php

namespace App\Http\Controllers\admin;

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

class AcessoClienteController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,

    ) {
        $this->request = $request;
        $this->user = $user;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function notasEmitidasCliente()
    {
        $title_page   = 'Notas Emitidas';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();


        return view('admin.cliente.cliente', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa'
        ));
    }
}
