@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body p-3">
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
                            <div class="atalhos-container mb-2">
                                @haspermission('ver-coleta-empresa')
                                    <a href="{{ route('coleta-empresa-geral') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-truck fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark justify-content-center">
                                                    <div class="small font-weight-bold">
                                                        Coleta Geral
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission

                                @role('admin')
                                    <a href="{{ route('analise-garantia.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-certificate fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Garantia
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endrole

                                @haspermission('ver-produzidos-sem-faturar')
                                    <a href="{{ route('produzidos-sem-faturar') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Prontos S/ Faturar
                                                    </div>                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission

                                @canany(['ver-pedidos-coletados-acompanhamento',
                                    'ver-pedidos-coletados-acompanhamento-cliente'])
                                    <a href="{{ route('bloqueio-pedidos') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-tasks fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Acompanha Pedidos
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endcanany
                                @haspermission('ver-analise-faturamento')
                                    <a href="{{ route('analise-faturamento.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Análise Faturista
                                                    </div>

                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission
                                @haspermission('ver-requisicao-borracharia')
                                    <a href="{{ route('requisicao-borracharia.index') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-dolly fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Req. Borracharia
                                                    </div>

                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endhaspermission
                                @haspermission('ver-estoque')
                                    <a href="{{ route('carcaca-casa') }}"
                                        class="card card-outline card-dark dashboard-shortcut mb-0">

                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="card-icon">
                                                    <i class="fas fa-boxes fa-2x mb-2"></i>
                                                </div>

                                                <div class="text-dark">
                                                    <div class="small font-weight-bold">
                                                        Estoque Carcacas
                                                    </div>

                                                    
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
                                    <div class="atalhos-container mb-2">
                                        <a href="{{ route('rel-cliente') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="card-icon">
                                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Financeiro Cliente
                                                        </div>
                                                        
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
                                    <div class="atalhos-container mb-2">
                                        <a href="{{ route('executor-etapas.index') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="card-icon">
                                                        <i class="fas fa-cogs fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Executor x Produção
                                                        </div>

                                                      
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="{{ route('pneus-lote-pcp') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="card-icon">
                                                        <i class="fas fa-layer-group fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Painel PCP
                                                        </div>

                                                        
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
                                    <div class="atalhos-container mb-2">
                                        <a href="{{ route('nota-devolucao.index') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="card-icon">
                                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Nota Devolução
                                                        </div>

                                                     
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
                                        <div class="atalhos-container mb-2">
                                            <a href="{{ route('list-notas-emitidas') }}"
                                                class="card card-outline card-dark dashboard-shortcut mb-0">

                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon">
                                                            <i class="fas fa-receipt fa-2x mb-2"></i>
                                                        </div>

                                                        <div class="text-dark">
                                                            <div class="small font-weight-bold">
                                                                Nota e Boleto
                                                            </div>

                                                            
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
                                    <div class="atalhos-container mb-2">
                                        <a href="{{ route('area-trabalho-tarefas') }}"
                                            class="card card-outline card-dark dashboard-shortcut mb-0">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="card-icon">
                                                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                                    </div>

                                                    <div class="text-dark">
                                                        <div class="small font-weight-bold">
                                                            Quadro de tarefas
                                                        </div>

                                                       
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
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }

        .dashboard-shortcut {
            transition: all .2s ease;
            text-decoration: none !important;
            color: inherit;
            border-radius: 8px !important;
        }

        .dashboard-shortcut .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-shortcut:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .14) !important;
            border-color: #adb5bd !important;
        }

        .dashboard-shortcut .card-icon {
            border-radius: 8px;
            width: 42px;
            height: 42px;
            min-width: 42px;
            margin: 0 10px 0 0;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: rgba(0, 0, 0, .06);
        }

        .dashboard-shortcut .card-icon i {
            margin: 0 !important;
            font-size: 1.2rem;
        }

        /* Tablet: 3 colunas mínimas */
        @media (max-width: 768px) {
            .atalhos-container {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        /* Mobile: 2 colunas fixas */
        @media (max-width: 480px) {
            .atalhos-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .dashboard-shortcut .card-body {
                padding: 8px 10px !important;
            }

            .dashboard-shortcut .card-icon {
                width: 36px;
                height: 36px;
                min-width: 36px;
                margin-right: 8px;
            }

            .dashboard-shortcut .card-icon i {
                font-size: 1rem;
            }

            .dashboard-shortcut .small {
                font-size: 0.72rem;
            }

            .dashboard-shortcut small.text-muted {
                font-size: 0.65rem;
            }
        }
    </style>
@stop
