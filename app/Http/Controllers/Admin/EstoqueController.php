<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\User;
use App\Services\ServiceEstoqueNegativo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    protected Empresa $empresa;
    protected Request $request;
    protected User $user;
    protected Estoque $estoque;
    protected ServiceEstoqueNegativo $serviceEstoqueNegativo;

    public function __construct(
        Empresa $empresa,
        Request $request,
        User $user,
        Estoque $estoque,
        ServiceEstoqueNegativo $serviceEstoqueNegativo
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->user = $user;
        $this->estoque = $estoque;
        $this->serviceEstoqueNegativo = $serviceEstoqueNegativo;
        
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function estoqueNegativo()
    {
        $title_page  = 'Estoque Negativo';
        $user_auth   = $this->user;
        $uri         = $this->request->route()->uri();

        return view("admin.estoque.estoque-negativo", compact('uri', 'title_page', 'user_auth'));
    }

    public function getEstoqueNegativo()
    {
        $estoqueNegativo = $this->serviceEstoqueNegativo->EstoqueNegativo();

        return datatables()->of($estoqueNegativo)->toJson();
    }
}
