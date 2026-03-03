@extends('layouts.master')

@section('title', 'Lotes de Estoque')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-inbox"></i>
                        {{ 'Itens / Lote: ' . $lote->id . ' / Criado em: ' . $lote->created_at }}
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mt-2" id="tab-lote-fechado" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link " id="itens-tab" data-toggle="tab" href="#tab-itens" role="tab"
                                aria-controls="itens" aria-selected="false">
                                Itens
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="resumo-tab" data-toggle="tab" href="#tab-resumo" role="tab"
                                aria-controls="resumo" aria-selected="true">
                                Resumo
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2">
                        <div class="tab-pane" id="tab-resumo" role="tabpanel" aria-labelledby="resumo-tab">
                            <table id="table-item-resumo" class="table compact table-bordered table-font-small">
                                <thead>
                                    <tr>
                                        <th>Cód. Item</th>
                                        <th>Descrição</th>
                                        <th>Peso</th>
                                        <th>Entrada em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemlote as $i)
                                        <tr>
                                            <td>{{ $i->cd_item }}</td>
                                            <td>{{ $i->ds_item }}</td>
                                            @if ($i->peso < $i->ps_liquido)
                                                <td class="bg-red color-palette">{{ number_format($i->peso, 2) }}</td>
                                            @else
                                                <td class="bg-green color-palette">{{ number_format($i->peso, 2) }}</td>
                                            @endif
                                            <td>{{ \Carbon\Carbon::parse($i->created_at)->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center">Total peso</th>
                                        <th>{{ $itemlote->sum(function ($i) {
                                            return $i->peso;
                                        }) }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane active" id="tab-itens" role="tabpanel" aria-labelledby="itens-tab">
                            <table id="table-item" class="table compact table-bordered table-font-small">
                                <thead>
                                    <tr>
                                        <th>Cód. Item</th>
                                        <th>Descrição</th>
                                        <th>Quantidade</th>
                                        <th>Soma Peso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemgroup as $i)
                                        <tr>
                                            <td>{{ $i->cd_item }}</td>
                                            <td>{{ $i->ds_item }}</td>
                                            <td>{{ $i->qtditem }}</td>
                                            <td>{{ number_format($i->peso, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center">Total</th>
                                        <th>{{ $itemgroup->sum(function ($i) {
                                            return $i->qtditem;
                                        }) }}
                                        </th>
                                        <th>{{ $itemgroup->sum(function ($i) {
                                            return $i->peso;
                                        }) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript"></script>
@endsection
