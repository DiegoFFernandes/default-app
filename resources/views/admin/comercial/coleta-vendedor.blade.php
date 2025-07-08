@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Coletas por Medida Hoje</h3>
                </div>
                <div class="card-body">
                    <table id="coletasMedidasHoje" class="table table-striped table-bordered dt-responsive nowrap"
                        style="width:100%">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Codigo</th>
                                <th>Descrição</th>
                                <th>Qtde</th>
                                <th>Valor Médio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>João</td>
                                <td>Medida A</td>
                                <td>10</td>
                                <td>2023-10-01</td>
                            </tr>
                            <tr>
                                <td>Maria</td>
                                <td>Medida B</td>
                                <td>5</td>
                                <td>2023-10-01</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Coletas por Medida Ontem</h3>
                </div>
                <div class="card-body">
                    <table id="coletasMedidasHoje2" class="table table-striped table-bordered dt-responsive nowrap"
                        style="width:100%">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Codigo</th>
                                <th>Descrição</th>
                                <th>Qtde</th>
                                <th>Valor Médio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Pedro</td>
                                <td>Medida C</td>
                                <td>8</td>
                                <td>2023-10-02</td>
                            </tr>
                            <tr>
                                <td>Ana</td>
                                <td>Medida D</td>
                                <td>12</td>
                                <td>2023-10-02</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Coletas por Medida Mês</h3>
                </div>
                <div class="card-body">
                    <table id="coletasPorMedidaMes" class="table table-striped table-bordered dt-responsive nowrap"
                        style="width:100%">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Mês</th>
                                <th>Coletas</th>
                                <th>Total Faturado</th>
                                <th>Valor Médio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Janeiro</td>
                                <td>150</td>
                                <td>R$ 15.000,00</td>
                                <td>R$ 100,00</td>
                            </tr>
                            <tr>
                                <td>Fevereiro</td>
                                <td>120</td>
                                <td>R$ 10.800,00</td>
                                <td>R$ 90,00</td>
                            </tr>
                            <tr>
                                <td>Março</td>
                                <td>180</td>
                                <td>R$ 21.600,00</td>
                                <td>R$ 120,00</td>
                            </tr>
                            <tr>
                                <td>Abril</td>
                                <td>200</td>
                                <td>R$ 24.000,00</td>
                                <td>R$ 120,00</td>
                            </tr>
                            <tr>
                                <td>Maio</td>
                                <td>170</td>
                                <td>R$ 18.700,00</td>
                                <td>R$ 110,00</td>
                            </tr>
                            <tr>
                                <td>Junho</td>
                                <td>190</td>
                                <td>R$ 22.800,00</td>
                                <td>R$ 120,00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
