<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendedorController extends Controller
{
    public $request, $regiao, $area, $empresa, $user, $vendedor;

    public function __construct(
        Request $request,        
        RegiaoComercial $regiao,
        AreaComercial $area,
        Empresa $empresa,
        Vendedor $vendedor
    ) {
        $this->request = $request;  
        $this->vendedor = $vendedor;      
        $this->regiao = $regiao;
        $this->area = $area;        
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function searchVendedor()
    {
        // Helper::searchCliente($this->user_auth->conexao)
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->vendedor->FindVendedorJunsoftAll($search);
        }
        return response()->json($data);
    }
}
