<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\NotaDevolucao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaDevolucaoController extends Controller
{

    public $empresa, $request, $user, $devolucao;
    public function __construct(
        Request $request,
        Empresa $empresa,
        NotaDevolucao $notaDevolucao

    ) {
        $this->request = $request;
        $this->empresa = $empresa;
        $this->devolucao = $notaDevolucao;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page = 'Nota Devolução';
        $user_auth = auth()->user();
        $empresa = $user_auth->empresa;        
        return view('admin.comercial.nota-devolucao');
    }

    public function getNotaDevolucao()
    {
       return $data = $this->devolucao->getNotaDevolucao();

        return datatables()->of($data)
            ->addColumn('DTFIM', function ($data) {
                return date('d/m/Y H:i', strtotime($data->DTFIM));
            })
            ->addColumn('VALOR', function ($data) {
                return number_format($data->VALOR, 2, ',', '.');
            })
            ->make(true);
    }
}
