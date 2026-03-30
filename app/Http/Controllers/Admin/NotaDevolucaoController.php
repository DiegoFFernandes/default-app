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
            ->addcolumn('actions', function ($data) {
                return '<button class="btn btn-xs btn-primary btn-detalhes-nota" style="font-size: 10px;"
                    data-nr-lancamento="' . $data->NR_LANCORIG . '"
                    data-cd-empresa="' . $data->CD_EMPRESA . '"
                    data-cd-item="' . $data->CD_ITEM . '"
                >Saidas</button>';
            })
            ->setRowClass(function ($d) {
                if ($d->SALDO < $d->QT_ENTRADA) {
                    return 'bg-warning';
                }
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getNotaDevolucaoDetalhes()
    {
        $cd_empresa = $this->request->get('cd_empresa');
        $nr_lancamento = $this->request->get('nr_lancamento');
        $cd_item = $this->request->get('cd_item');

        $data = $this->devolucao->getNotaDevolucaoDetalhes($cd_empresa, $nr_lancamento, $cd_item);

        return DataTables::of($data)
            ->editcolumn('ST_MDFE', function ($data) {
                return $data->ST_MDFE == 1 ? '<span class="badge badge-success">C/ MDFE</span>' : '<span class="badge badge-danger">S/ MDFE</span>';
            })
            ->rawColumns(['ST_MDFE'])
            ->make(true);
    }
}
