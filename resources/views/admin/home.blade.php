@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <h2>Olá seja bem vindo(a), {{ $user_auth->name }}!</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">View 2.0</h3>
                    </div>
                    <div class="card-body">
                        @role('admin')
                            <a href="{{ route('coleta-empresa-geral') }}" class="btn btn-sm btn-dark mb-1"
                                style="width: 150px">Coleta Geral</a>
                            <a href="{{ route('analise-faturamento.index') }}" class="btn btn-sm btn-dark mb-1"
                                style="width: 150px">Faturamento</a>
                            <a href="{{ route('analise-garantia.index') }}" class="btn btn-sm btn-dark mb-1"
                                style="width: 150px">Garantia</a>
                        @endrole
                        @haspermission('ver-produzidos-sem-faturar')
                            <a href={{ route('produzidos-sem-faturar') }} class="btn btn-sm btn-dark mb-1"
                                style="width: 150px">Produzidos P/ Faturar</a>
                        @endhaspermission
                        @haspermission('ver-rel-cobranca')
                            <a href={{ route('rel-cobranca') }} class="btn btn-sm btn-dark mb-1" style="width: 150px">Relatório
                                Cobranca</a>
                        @endhaspermission
                        @haspermission('ver-pedidos-coletados-acompanhamento')
                            <a href={{ route('bloqueio-pedidos') }} class="btn btn-sm btn-dark mb-1"
                                style="width: 150px">Acompanha Pedidos</a>
                        @endhaspermission
                    </div>
                </div>
            </div>
            <!-- /.row -->
    </section>
@stop
