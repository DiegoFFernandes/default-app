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
        $tabela = $this->request->tabela;
        $executor = isset($this->request->executor) ? implode(',', $this->request->executor) : 0;
        $painel = $this->request->painel;
        $etapa = $this->classificaEtapa($tabela);

        // 9 - CANCELADAS
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $data = $this->executorEtapa->producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim, $tabela, $executor, $painel, $etapa, $subgrupo['data']);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) use ($tabela, $painel) {
                return '<button class="btn btn-xs btn-detalhes-executor" style="font-size: 10px;" 
                
                    data-cd_empresa="' . $row->IDEMPRESA . '"
                    data-dt_fim="' . $row->DT_FIM . '"

                    ' . ($painel === 'painel-canceladas' ? '' : 'data-idexecutor="' . $row->IDEXECUTOR . '"') . '
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
        $executor = $this->request->idexecutor; //sempre vai vir um executor, pois o botão de detalhes só é exibido para quem tem executor definido
        $tabela = $this->request->tabela;
        $painel = $this->request->painel;
        $etapa = $this->classificaEtapa($tabela);

        // 9 - CANCELADAS
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $data = $this->executorEtapa->producaoDetalhesExecutorEtapa($cd_empresa, $dt_fim, $tabela, $executor, $painel, $etapa, $subgrupo['data']);

        return DataTables::of($data)
            ->editColumn('ST_RETRABALHO', function ($row) use ($painel) {
                if ($painel === 'painel-canceladas') {
                    return '<span class="badge badge-danger">Cancelado</span>';
                }
                return $row->ST_RETRABALHO === 'Sim' ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
            })
            ->rawColumns(['ST_RETRABALHO'])
            ->make(true);
    }

    public function classificaEtapa($tabela)
    {
        switch ($tabela) {
            case 'EXAMEINICIAL':
                return 1;
            case 'RASPAGEMPNEU':
                return 2;
            case 'PREPARACAOBANDAPNEU':
                return 3;
            case 'ESCAREACAOPNEU':
                return 4;
            case 'LIMPEZAMANCHAO':
                return 5;
            case 'APLICACAOCOLAPNEU':
                return 6;
            case 'EMBORRACHAMENTO':
                return 9;
            case 'VULCANIZACAO':
                return 11;
            case 'EXAMEFINALPNEU':
                return 12;
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

            ->make(true);
    }
}
