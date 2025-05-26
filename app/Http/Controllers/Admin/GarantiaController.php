<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Garantia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GarantiaController extends Controller
{
    public $request, $user_auth, $tipo, $pessoa, $empresa, $user, $garantia;

    public function __construct(
        Empresa $empresa,
        Request $request,
        User $user,
        Garantia $garantia       
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->user = $user;
        $this->garantia = $garantia;
        $this->middleware(function ($request, $next) {
            $this->user_auth = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page = 'Análise Garantia';
        $uri  = $this->request->route()->uri();
        $user_auth = $this->user_auth;
        $empresas  = $this->empresa->empresa();


        return view('admin.comercial.garantia', compact(
            'title_page',
            'user_auth',
            'empresas',

        ));
    }
    public function getAnaliseGarantia()
    {
        $data = $this->garantia->getGarantia();
        return response()->json($data);
    }
}
