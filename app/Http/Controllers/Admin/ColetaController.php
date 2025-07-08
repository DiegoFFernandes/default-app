<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PedidoPneu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ColetaController extends Controller
{
    public $request, $user, $coleta;

    public function __construct(Request $request, 
            User $user, 
            PedidoPneu $coleta
        ){
        $this->request = $request;
        $this->user = $user;
        $this->coleta = $coleta;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function coleta()
    {        
        return view('admin.comercial.coleta-vendedor');
    }

    public function getColeta()
    {
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;
        
        $data = $this->coleta->getPedidoPneu($dt_inicio, $dt_fim);

        return Datatables::of($data)->make('true');

        
    }
}
