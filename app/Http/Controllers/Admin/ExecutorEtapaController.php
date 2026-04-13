<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\ExecutorEtapa;
use App\Models\User;
use App\Services\ServiceFiltroGrupoSubgrupo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExecutorEtapaController extends Controller
{
    public
        $request,
        $empresa,
        $user,
        $executorEtapa,
        $serviceFiltroGrupoSubgrupo;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,
        ExecutorEtapa $executorEtapa,
        ServiceFiltroGrupoSubgrupo $serviceFiltroGrupoSubgrupo
    ) {
        $this->request = $request;
        $this->empresa = $empresa;
        $this->user = $user;
        $this->executorEtapa = $executorEtapa;
        $this->serviceFiltroGrupoSubgrupo = $serviceFiltroGrupoSubgrupo;
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
        $classificatabela = $this->classificaEtapa($this->request->tabela);
        $executor = isset($this->request->executor) ? implode(',', $this->request->executor) : 0;
        $painel = $this->request->painel;
        $tabela = $classificatabela['tabela'];
        $etapa = $classificatabela['idetapa'];       

        // 9 - CANCELADAS
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $data = $this->executorEtapa->producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim, $tabela, $executor, $painel, $etapa, $subgrupo['data']);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) use ($etapa, $painel) {
                return '<button class="btn btn-xs btn-detalhes-executor" style="font-size: 10px;" 
                
                    data-cd_empresa="' . $row->IDEMPRESA . '"
                    data-dt_fim="' . $row->DT_FIM . '"
                    data-idexecutor="' . $row->IDEXECUTOR . '"
                    data-tabela="' . $etapa . '"                
                
                ">Detalhes</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detalhesExecutor()
    {
        
        $cd_empresa = $this->request->cd_empresa;
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;
        $executor = is_array($this->request->idexecutor) ? implode(',', $this->request->idexecutor) : $this->request->idexecutor;
        $classificatabela = $this->classificaEtapa($this->request->tabela);        
        $painel = $this->request->painel;
        $tabela = $classificatabela['tabela'];
        $etapa = $classificatabela['idetapa'];        
        $origem = $this->request->origem; //analitico ou sintetico

        // 9 - CANCELADAS
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $data = $this->executorEtapa->producaoDetalhesExecutorEtapa(
            $cd_empresa,
            $dt_inicio,
            $dt_fim,
            $tabela,
            $executor,
            $painel,
            $etapa,
            $subgrupo['data'],
            $origem
        );

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-xs btn-acompanhamento-executor" style="font-size: 10px;"><i class="fa fa-plus-circle"></i></button>';
            })
            ->addColumn('details_item_pedido_url', function ($i) {
                return route('get-detalhe-item-pedido', $i->ID);
            })
            ->editColumn('ST_RETRABALHO', function ($row) use ($painel) {
                if ($painel === 'painel-canceladas') {
                    return '<span class="badge badge-danger">Cancelado</span>';
                }
                return $row->ST_RETRABALHO === 'Sim' ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
            })           

            ->rawColumns(['action', 'ST_RETRABALHO'])
            ->make(true);
    }

    public function classificaEtapa($tabela)
    {
        switch ($tabela) {
            case '1':
                return ['tabela' => 'EXAMEINICIAL', 'idetapa' => 1];
            case '2':
                return ['tabela' => 'RASPAGEMPNEU', 'idetapa' => 2];
            case '3':
                return ['tabela' => 'PREPARACAOBANDAPNEU', 'idetapa' => 3];
            case '4':
                return ['tabela' => 'ESCAREACAOPNEU', 'idetapa' => 4];
            case '5':
                return ['tabela' => 'LIMPEZAMANCHAO', 'idetapa' => 5];
            case '6':
                return ['tabela' => 'APLICACAOCOLAPNEU', 'idetapa' => 6];
            case '9':
                return ['tabela' => 'EMBORRACHAMENTO', 'idetapa' => 9];
            case '11':
                return ['tabela' => 'VULCANIZACAO', 'idetapa' => 11];
            case '12':
                return ['tabela' => 'EXAMEFINALPNEU', 'idetapa' => 12];
        }
    }

    public function resumoProducaoSetor()
    {
        $cd_empresa = $this->request->cd_empresa;
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;
        $executor = isset($this->request->executor) ? implode(',', $this->request->executor) : 0;
        $painel = $this->request->painel;
        $subPainel = $this->request->subPainel;

        // 9 - CANCELADAS
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $data = $this->executorEtapa->resumoExecutorSetor(
            $cd_empresa,
            $dt_inicio,
            $dt_fim,
            $subgrupo['data'],
            $executor,
            $painel,
            $subPainel
        );

        return DataTables::of($data)

            ->addColumn('actions', function ($row) {
                return '<button class="btn btn-xs btn-detalhes-executor" style="font-size: 10px;"             
                   data-idetapa="' . $row->IDETAPA . '"              
                
                ">Detalhes</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
