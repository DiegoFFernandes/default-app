<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\NotaCliente;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AcessoClienteController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $nota;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,
        NotaCliente $nota,

    ) {
        $this->request = $request;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->nota = $nota;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function listNotasEmitidasCliente()
    {
        $title_page   = 'Notas Emitidas';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();


        return view('admin.cliente.notas', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa'
        ));
    }

    public function getListNotasEmitidasCliente()
    {
       $data = $this->nota->getListNotaCliente();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-danger btn-xs">Nota</a>';
                $btn .= '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-secondary btn-xs ml-1">Boleto</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function layoutNotaEmitidaCliente()
    {

        $data = $this->nota->getListNotaCliente(164973);
        $title_page   = 'Notas Emitidas';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();


        return view('admin.cliente.layout-nota', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa',
            'data'
        ));
    }
}
