<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faturamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaturamentoController extends Controller
{
    public $request, $user, $faturamento;


    public function __construct(
        Request $request, 
        Faturamento $faturamento
        ) {
        $this->middleware('auth');
        $this->request = $request;
        $this->faturamento = $faturamento;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_auth = $this->user;
        $uri       = $this->request->route()->uri();
        $title_page = "Análise Faturamento";       

        return view('admin.faturamento.index', compact(
            'user_auth',
            'uri',
            'title_page'
        ));
    }
    public function getAnaliseFaturamento()
    {
        $data = $this->faturamento->listFaturamentoUser();
        return response()->json($data);
    }
}
