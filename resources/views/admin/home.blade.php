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
            @role('admin')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">View 2.0</h3>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('analise-faturamento.index') }}" class="btn btn-sm btn-dark"
                                style="width: 150px">Faturamento</a>
                            <a href="" class="btn btn-sm btn-dark" style="width: 150px">Garantia</a>
                            <a href={{ route('produzidos-sem-faturar') }} class="btn btn-sm btn-dark" style="width: 150px">Produzidos P/ Faturar</a>
                        </div>
                    </div>
                </div>
            @endrole
            <!-- /.row -->
    </section>
@stop
