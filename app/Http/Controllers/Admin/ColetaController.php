<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcompanhamentoPneu;
use Illuminate\Http\Request;
use App\Models\PedidoPneu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ColetaController extends Controller
{
    public $request, $user, $coleta, $acompanhamento;

    public function __construct(Request $request, 
            User $user, 
            PedidoPneu $coleta,
            AcompanhamentoPneu $acompanhamento
        ){
        $this->request = $request;
        $this->user = $user;
        $this->coleta = $coleta;
        $this->acompanhamento = $acompanhamento;


        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function getColetaGeral(){
        // return $this->request;

       $data = $this->acompanhamento->ListPedidoPneu(0, '', 0, $this->request);

        return Datatables::of($data)->make('true');
    }

    public function coletaMedidas()
    {        
        return view('admin.comercial.coleta-medidas');
    }

    public function getColeta()
    {
        $dt_inicio = $this->request->dt_inicio;
        $dt_fim = $this->request->dt_fim;
        $cd_empresa = $this->request->cd_empresa;

        $data = $this->coleta->getPedidoPneu($dt_inicio, $dt_fim, $cd_empresa);

        return Datatables::of($data)->make('true');
        
    }

    public function coleta()
    {
        return view('admin.comercial.coleta');
    }

    public function coletaVendedor()
    {
        return view('admin.comercial.coleta-vendedor');
    }

    public function vendedor()
    {
        return view('admin.comercial.vendedor');
    }

    public function coletaProducao()
    {
        return view('admin.comercial.coleta-producao');
    }

    public function fichasAbertas()
    {
        return view('admin.comercial.fichas-abertas');
    }

    public function inadimplenciaVendedor()
    {
        return view('admin.comercial.inadimplencia-vendedor');
    }

    public function resumoCarga()
    {
        return view('admin.comercial.resumo-carga');
    }

    public function producaoIndicadores()
    {
        return view('admin.comercial.producao-indicadores');
    }

    public function producaoCarga()
    {
        return view('admin.comercial.producao-carga');
    }

    public function pneusEtapasExecutar()
    {
        return view('admin.comercial.pneus-etapa-executar');
    }
    public function pneusParados()
    {
        return view('admin.comercial.parados-mais-quatro-horas');
    }

    public function exameInicialCobertura()
    {
        return view('admin.comercial.exame-inicia-cobertura');
    }
}
