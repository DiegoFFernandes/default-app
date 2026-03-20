<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\ExecutorEtapa;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use Yajra\DataTables\Facades\DataTables;

class ExecutorEtapaController extends Controller
{
    public
        $request,
        $empresa,
        $user,
        $executorEtapa;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,
        ExecutorEtapa $executorEtapa
    ) {
        $this->request = $request;
        $this->empresa = $empresa;
        $this->user = $user;
        $this->executorEtapa = $executorEtapa;
    }

    public function producaoExecutorEtapa()
    {
        $empresas = $this->empresa->empresa();
        $executores = $this->executorEtapa->getExecutores();
        return view(
            'admin.producao.producao-executor.producao-executor',
            compact(
                'empresas',
                'executores'
            )
        );
    }


    public function getProducaoExecutorEtapa()
    {
        $cd_empresa = $this->request->cd_empresa;
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;
        $tabela = $this->request->tabela;
        $executor = $this->request->executor;
        $painel = $this->request->painel;

        $data = $this->executorEtapa->producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim, $tabela, $executor, $painel);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) use ($tabela) {
                return '<button class="btn btn-xs btn-detalhes-executor" style="font-size: 10px;" 
                
                    data-cd_empresa="' . $row->IDEMPRESA . '"
                    data-dt_fim="' . $row->DT_FIM . '"
                    data-idexecutor="' . $row->IDEXECUTOR . '"
                    data-tabela="' . $tabela . '"
                
                
                ">Detalhes</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detalhesExecutor()
    {
        $cd_empresa = $this->request->cd_empresa;
        $dt_fim = $this->request->dt_fim;
        $idexecutor = $this->request->idexecutor;
        $tabela = $this->request->tabela;
        $painel = $this->request->painel;

        $data = $this->executorEtapa->producaoDetalhesExecutorEtapa($cd_empresa, $dt_fim, $tabela, $idexecutor, $painel);

        return DataTables::of($data)
            ->editColumn('ST_RETRABALHO', function ($row) {
                return $row->ST_RETRABALHO === 'Sim' ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
            })
            ->rawColumns(['ST_RETRABALHO'])
            ->make(true);
    }
}
