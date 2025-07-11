@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card card-outline card-danger text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100">Inadimplência x Vendedor</h3>
                    </div>
                    <canvas id="inadimpenciaVendedor" style="min-height: 150px; height: 150px;"></canvas>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-4">
                <div class="card card-outline card-danger text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100">5 Mais Inadimplentes</h3>
                    </div>
                    <div class="card-body p-2">
                        <canvas id="maisInadimplentes" style="min-height: 150px; height: 150px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-4">
                <div class="card card-outline card-danger text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100">Total por Período</h3>
                    </div>
                    <div class="card-body p-2">
                        <canvas id="totalPeriodo" style="min-height: 150px; height: 150px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
