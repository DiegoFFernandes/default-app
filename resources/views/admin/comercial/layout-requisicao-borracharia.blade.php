@extends('layouts.master-simple')

@section('title', 'Requisição Borracharia')
@section('content')
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4">
                    <img src="{{ asset('img/atz-logo.png') }}" alt="">
                    <h4>
                        <span class="">Requisição Borracharia</span>
                        <small class="float-right">Periodo: {{ $datas['filtro']['dtInicio'] }} - {{ $datas['filtro']['dtFim'] }}</small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <table class="table table-striped mb-2 w-100">
                    @php
                        $valorTotal = 0;
                        $qtdTotal = 0;
                    @endphp
                    <thead>
                        <tr>
                            <th class="p-0">Pessoa</th>
                            <th class="p-0 text-center" width="15%">Qtd</th>
                            <th class="pt-0 pb-0 text-right" width="20%">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hierarquia as $gerente)
                            @php
                                $valorTotal += $gerente['vl_comissao'];
                                $qtdTotal += $gerente['qtd_item'];
                            @endphp
                            <tr class="p-0">
                                <td class="p-0"><strong>{{ $gerente['nome'] }}</strong></td>
                                <td class="p-0 text-center" width="15%"><strong>{{ $gerente['qtd_item'] }}</strong></td>
                                <td class="pt-0 pb-0 text-right" width="20%"><strong>R$
                                    {{ number_format($gerente['vl_comissao'], 2, ',', '.') }}</strong></td>
                            </tr>
                            @foreach ($gerente['supervisores'] as $supervisor)
                                <tr class="p-0">
                                    <td style="padding: 0 0 0 10px;"><strong>{{ $supervisor['nome'] }}</strong></td>
                                    <td class="p-0 text-center" width="15%"><strong>{{ $supervisor['qtd_item'] }}</strong></td>
                                    <td class="pt-0 pb-0 text-right" width="20%"><strong>R$
                                        {{ number_format($supervisor['vl_comissao'], 2, ',', '.') }}</strong></td>
                                </tr>
                                @foreach ($supervisor['vendedores'] as $vendedor)
                                    <tr class="p-0">
                                        <td style="padding: 0 0 0 20px;"><strong>{{ $vendedor['nome'] }}</strong></td>
                                        <td class="p-0 text-center" width="15%"><strong>{{ $vendedor['qtd_item'] }}</strong></td>
                                        <td class="pt-0 pb-0 text-right" width="20%"><strong>R$
                                            {{ number_format($vendedor['vl_comissao'], 2, ',', '.') }}</strong></td>
                                    </tr>
                                    @foreach ($vendedor['borracheiros'] as $borracheiro)
                                        <tr class="p-0">
                                            <td style="padding: 0 0 0 30px;"><strong>{{ $borracheiro['nome'] }}</strong></td>
                                            <td class="p-0 text-center" width="15%"><strong>{{ $borracheiro['qtd_item'] }}</strong></td>
                                            <td class="pt-0 pb-0 text-right" width="20%"><strong>R$
                                                {{ number_format($borracheiro['vl_comissao'], 2, ',', '.') }}</strong></td>
                                        </tr>
                                        @foreach ($borracheiro['clientes'] as $cliente)
                                            <tr class="p-0">
                                                <td style="padding: 0 0 0 40px;">{{ $cliente['PESSOA'] }}</td>
                                                <td class="p-0 text-center" width="15%">{{ $cliente['QTD_ITEM'] }}
                                                </td>
                                                <td class="pt-0 pb-0 text-right" width="20%">R$
                                                    {{ number_format($cliente['VL_COMISSAO'], 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-striped mb-2 w-100">
                    <thead>
                        <tr>
                            <th class="p-0"></th>
                            <th class="p-0 text-center" width="15%">Quantidade Total</th>
                            <th class="pt-0 pb-0 text-right" width="20%">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="p-0">
                            <td class="p-0"></td>
                            <td class="p-0 text-center" width="15%">{{ $qtdTotal }}</td>
                            <td class="pt-0 pb-0 text-right" width="20%">R$
                                {{ number_format($valorTotal, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        /* thead {
                    display: table-header-group;
                    /* força header correto
                }

                tfoot {
                    display: table-footer-group;
                } */

        tr {
            page-break-inside: avoid;
            /* evita quebra no meio da linha */
        }

        td,
        th {
            padding: 6px;
            border: 1px solid #000;
            font-size: 12px;
        }
    </style>

@stop
