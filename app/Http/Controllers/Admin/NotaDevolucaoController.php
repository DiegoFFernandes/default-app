<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaDevolucao;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
        $this->empresa = $empresa;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page = 'Nota Devolução';
        $user_auth = auth()->user();
        $empresas = $this->empresa->empresa();

        return view('admin.comercial.nota-devolucao', compact(
            'title_page',
            'user_auth',
            'empresas'
        ));
    }

    public function getNotaDevolucao()
    {
        $data = $this->devolucao->getNotaDevolucao();

        return DataTables::of($data)
            ->make(true);
    }
}
