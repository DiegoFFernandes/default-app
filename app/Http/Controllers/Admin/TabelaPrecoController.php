<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\TabPreco;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TabelaPrecoController extends Controller
{
    public $request, $user_auth, $tipo, $pessoa, $empresa, $user, $tabela;

    public function __construct(
        Empresa $empresa,
        Request $request,
        Pessoa $pessoa,
        User $user,
        TabPreco $tabela
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->pessoa = $pessoa;
        $this->user = $user;
        $this->tabela = $tabela;
        $this->middleware(function ($request, $next) {
            $this->user_auth = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page = 'Tabela de Preço';
        $uri  = $this->request->route()->uri();
        $user_auth = $this->user_auth;        
        $empresas  = $this->empresa->empresa();        

        return view('admin.comercial.tabela-preco', compact(            
            'title_page',            
            'user_auth',
            'empresas',
            'uri'            
        ));
    }
    public function getTabPreco()
    {
        $data = $this->tabela->getTabpreco();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-xs btn-danger btn-ver-itens" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . $row->CD_TABPRECO . '">Itens</button>                    
                ';
            })
            ->addColumn('clientes_associados', function ($row) {
                $btn = '<span class="btn-detalhes details-control mr-2" data-cd_tabela="' . $row->CD_TABPRECO . '"><i class="fas fa-plus-circle"></i></span> ' . $row->CD_TABPRECO;
                return $btn;
            })
            ->setRowClass(function($row){
                return $row->ASSOCIADOS > 0 ? 'bg-green' : '';
            })
            ->rawColumns(['action', 'clientes_associados'])
            ->make(true);
    }
    public function getItemTabPreco()
    {
        $cd_tabela = $this->request->get('cd_tabela');
        $data = $this->tabela->getItemTabPreco($cd_tabela);

        return DataTables::of($data)
            ->make(true);
    }
    public function getTabClientePreco(){
        $cd_tabela = $this->request->cd_tabela;
        $data = $this->tabela->getTabClientePreco($cd_tabela);

        return DataTables::of($data)
            ->make(true);
    }
}
