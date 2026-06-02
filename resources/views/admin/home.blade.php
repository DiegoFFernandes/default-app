@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Olá seja bem vindo(a), {{ $user_auth->name }}!</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">View 2.0</h3>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            @canany(['ver-coleta-empresa', 'ver-pedidos-coletados-acompanhamento',
                                'ver-pedidos-coletados-acompanhamento-cliente'])
                                <div class="border-bottom mb-3 pb-1">
                                    <strong>Comercial</strong>
                                </div>
                            @endcanany
                            <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                @haspermission('ver-coleta-empresa')
                                    <a href="{{ route('coleta-empresa-geral') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-truck fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark justify-content-center">
                                                    <div class="small font-weight-bold">
                                                        Coleta Geral
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar coletas
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission

                                @role('admin')
                                    <a href="{{ route('analise-garantia.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-certificate fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Garantia
                                                    </div>

                                                    <small class="text-muted">
                                                        Garantias Pagas
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endrole

                                @haspermission('ver-produzidos-sem-faturar')
                                    <a href="{{ route('produzidos-sem-faturar') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Prontos S/ Faturar
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission

                                @canany(['ver-pedidos-coletados-acompanhamento',
                                    'ver-pedidos-coletados-acompanhamento-cliente'])
                                    <a href="{{ route('bloqueio-pedidos') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-tasks fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Acompanha Pedidos
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar pedidos
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endcanany
                                @haspermission('ver-analise-faturamento')
                                    <a href="{{ route('analise-faturamento.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Análise Faturista
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar análise
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission
                                @haspermission('ver-requisicao-borracharia')
                                    <a href="{{ route('requisicao-borracharia.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-dolly fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Req. Borracharia
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar requisições
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission
                                @haspermission('ver-estoque')
                                    <a href="{{ route('carcaca-casa') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body p-2 mr-2">
                                            <div class="d-flex align-items-center">
                                                <div class="ml-0 mr-3 card-icon">
                                                    <i class="fas fa-boxes fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Estoque Carcacas
                                                    </div>

                                                    <small class="text-muted">
                                                        Consultar estoque
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission
                            </div>
                            @haspermission('ver-rel-cobranca')
                                <div class="col-md-12 mt-2">
                                    <div class="border-bottom mb-3 pb-1">
                                        <strong>Cobrança</strong>
                                    </div>
                                    <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                        <a href="{{ route('rel-cliente') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body p-2 mr-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="ml-0 mr-3 card-icon">
                                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Financeiro Cliente
                                                        </div>

                                                        <small class="text-muted">
                                                            Consultar financeiro
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endhaspermission
                            @haspermission('ver-producao')
                                <div class="col-md-12 mt-2">
                                    <div class="border-bottom mb-3 pb-1">
                                        <strong>Produção</strong>
                                    </div>
                                    <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                        <a href="{{ route('executor-etapas.index') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body p-2 mr-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="ml-0 mr-3 card-icon">
                                                        <i class="fas fa-cogs fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Executor x Produção
                                                        </div>

                                                        <small class="text-muted">
                                                            Consultar execução
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="{{ route('pneus-lote-pcp') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body p-2 mr-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="ml-0 mr-3 card-icon">
                                                        <i class="fas fa-layer-group fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Painel PCP
                                                        </div>

                                                        <small class="text-muted">
                                                            Consultar painel
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endhaspermission
                            @haspermission('ver-nota-devolucao')
                                <div class="col-md-12 mt-2">
                                    <div class="border-bottom mb-3 pb-1">
                                        <strong>Faturamento</strong>
                                    </div>
                                    <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                        <a href="{{ route('nota-devolucao.index') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body p-2 mr-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="ml-0 mr-3 card-icon">
                                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Nota Devolução
                                                        </div>

                                                        <small class="text-muted">
                                                            Consultar notas
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endhaspermission
                            @hasrole('cliente|admin')
                                @haspermission('ver-nota-cliente')
                                    <div class="col-md-12 mt-2">
                                        <div class="border-bottom mb-3 pb-1">
                                            <strong>Faturamento</strong>
                                        </div>
                                        <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                            <a href="{{ route('list-notas-emitidas') }}"
                                                class="card card-outline card-dark dashboard-shortcut mb-0">

                                                <div class="card-body p-2 mr-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="ml-0 mr-3 card-icon">
                                                            <i class="fas fa-receipt fa-2x mb-2"></i>
                                                        </div>

                                                        <div class="text-dark">
                                                            <div class="small font-weight-bold">
                                                                Nota e Boleto
                                                            </div>

                                                            <small class="text-muted">
                                                                Consultar notas
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endhaspermission
                            @endhasrole
                            @haspermission('ver-quadro-tarefa')
                                <div class="col-md-12 mt-2">
                                    <div class="border-bottom mb-3 pb-1">
                                        <strong>Tarefas</strong>
                                    </div>
                                    <div class="atalhos-container d-flex flex-wrap gap-2 mb-2">
                                        <a href="{{ route('area-trabalho-tarefas') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body p-2 mr-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="ml-0 mr-3 card-icon">
                                                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Quadro de tarefas
                                                        </div>

                                                        <small class="text-muted">
                                                            Consultar tarefas
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endhaspermission
                        </div>
                    </div>
                </div>
                <!-- /.row -->
    </section>
@stop
@section('css')
    <style>
        .atalhos-container {
            gap: 10px;
        }

        .dashboard-shortcut {
            transition: all .2s ease;
            text-decoration: none !important;
            color: inherit;
            /* width: 210px; */
            /* border-radius: 8px; */
        }

        .dashboard-shortcut:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, .12);
        }



        /* Ícone mais proporcional */
        .dashboard-shortcut .card-icon {
            font-size: 13px;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            margin: 0 10px 0 10px;
            display: flex !important;
            align-items: center !important;
            /* centraliza vertical */
            justify-content: center !important;
            /* centraliza horizontal */

        }
    </style>
@stop
