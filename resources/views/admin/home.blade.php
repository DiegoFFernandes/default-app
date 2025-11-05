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
                            @canany(['ver-coleta-empresa', 'ver-pedidos-coletados-acompanhamento'])
                                <p>Comercial</p>
                            @endcanany
                            @haspermission('ver-coleta-empresa')
                                <a href="{{ route('coleta-empresa-geral') }}" class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Coleta Geral</a>
                            @endhaspermission
                            @role('admin')
                                <a href="{{ route('analise-faturamento.index') }}" class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Faturamento</a>
                                <a href="{{ route('analise-garantia.index') }}" class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Garantia</a>
                            @endrole
                            @haspermission('ver-produzidos-sem-faturar')
                                <a href={{ route('produzidos-sem-faturar') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Produzidos P/ Faturar</a>
                            @endhaspermission

                            @haspermission('ver-pedidos-coletados-acompanhamento')
                                <a href={{ route('bloqueio-pedidos') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Acompanha Pedidos</a>
                            @endhaspermission
                            @haspermission('ver-analise-faturamento')
                                <a href={{ route('analise-faturamento.index') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Análise Faturista</a>
                            @endhaspermission
                        </div>
                        @haspermission('ver-rel-cobranca')
                            <div class="col-md-12 mt-2">
                                <p>Cobrança</p>
                                <a href={{ route('rel-cliente') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Financeiro Cliente</a>
                            </div>
                        @endhaspermission
                        @haspermission('ver-producao')
                            <div class="col-md-12 mt-2">
                                <p>Produção</p>
                                <a href={{ route('executor-etapas.index') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Executor x Produção</a>
                                <a href={{ route('pneus-lote-pcp') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Painel PCP</a>
                            </div>
                        @endhaspermission
                        @haspermission('ver-nota-devolucao')
                            <div class="col-md-12 mt-2">
                                <p>Faturamento</p>
                                <a href={{ route('nota-devolucao.index') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Nota Devolução</a>
                            </div>
                        @endhaspermission
                        @hasrole('cliente|admin')
                            @haspermission('ver-nota-cliente')
                                <div class="col-md-12 mt-2">
                                    <p>Faturamento</p>
                                    <a href={{ route('list-notas-emitidas') }} class="btn btn-sm btn-dark mb-1"
                                        style="width: 150px">Nota e Boleto</a>
                                </div>
                            @endhaspermission
                        @endhasrole
                        @haspermission('ver-quadro-tarefa')
                            <div class="col-md-12 mt-2">
                                <p>Tarefas</p>
                                <a href={{ route('listar-projeto') }} class="btn btn-sm btn-dark mb-1"
                                    style="width: 150px">Quadro de tarefas</a>
                            </div>
                        @endhasrole
                    </div>
                </div>
            </div>
            <!-- /.row -->
    </section>
@stop
