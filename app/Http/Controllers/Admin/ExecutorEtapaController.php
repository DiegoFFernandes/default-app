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
        return view('admin.producao.producao-executor', compact('empresas'));
    }


    public function getProducaoExecutorEtapa()
    {
        $cd_empresa = $this->request->cd_empresa;
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;

        $data = $this->executorEtapa->producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim);

        return DataTables::of($data)
            ->make(true);
    }
}
