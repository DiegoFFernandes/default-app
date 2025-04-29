<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProducaoController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user,
        Producao $producao

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->producao = $producao;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Produzidos - Sem Faturar';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);

        $regiao = $this->regiao->regiaoAll();
        $user =  $this->user->getData();

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.producao.produzidos-sem-faturar', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'user',
            'list_regiao'
        ));
    }
    public function getListPneusProduzidosFaturar()
    {
        $data = $this->producao->getPneusProduzidosFaturar();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('CD_EMPRESA', function ($row) {
                return '<span class="right badge badge-danger details-control mr-2"><i class="fa fa-plus-circle"></i></span> ' . $row->CD_EMPRESA;
            })
            ->addColumn('NM_PESSOA', function ($row) {
                return $row->NM_PESSOA;
            })
            ->addColumn('VALOR', function ($row) {
                return number_format($row->VALOR, 2, ',', '.');
            })
            ->addColumn('PNEUS', function ($row) {
                return $row->PNEUS;
            })
            ->addColumn('EXPEDICIONADO', function ($row) {
                return $row->EXPEDICIONADO;
            })
            ->addColumn('DTENTREGA', function ($row) {
                return date('d/m/Y', strtotime($row->DTENTREGA));
            })
            ->rawColumns(['CD_EMPRESA', 'NM_PESSOA', 'VALOR', 'PNEUS', 'EXPEDICIONADO', 'DTENTREGA'])
            ->make(true);
    }
    public function getListPneusProduzidosFaturarDetails()
    {
        $nr_embarque = $this->request->get('nr_embarque');
        $pedido = $this->request->get('pedido');

       
        if ($nr_embarque == 'SEM EMBARQUE') {
            $nr_embarque = 0;
        }

        $data = $this->producao->getPneusProduzidosFaturarDetails($pedido, $nr_embarque);

        return DataTables::of($data)->make(true);
        

    }
}
