@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Vendas do Dia</span>
                        <span class="info-box-number" id="total-vendas-dia">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Comissão Total do Dia</span>
                        <span class="info-box-number" id="comissao-total-dia">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Quantidade de Notas Emitidas</span>
                        <span class="info-box-number" id="quantidade-notas">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h5 class="card-title">Filtros</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-empresa">Empresa:</label>
                                    <select id="filtro-empresa" class="form-control mt-1">
                                        <option value="">Todas</option>
                                        @foreach ($empresa as $emp)
                                            <option value="{{ $emp->CD_EMPRESA }}">{{ $emp->NM_EMPRESA }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="daterange">Data:</label>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="daterange"
                                            placeholder="Selecione a Data">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-tabelaVendedores" data-toggle="pill"
                                    href="#painel-tabelaVendedores" role="tab" aria-controls="painel-tabelaVendedores"
                                    aria-selected="true">
                                    Tabela Vendedores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-resumoVendedor" data-toggle="pill" href="#painel-resumoVendedor"
                                    role="tab" aria-controls="painel-resumoVendedor" aria-selected="false">
                                    Resumo por vendedor
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-tabelaVendedores" role="tabpanel"
                                aria-labelledby="tab-tabelaVendedores">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table
                                            id="tabelaComissao"class="table compact table-font-small table-striped table-bordered"
                                            style="width:100%; font-size: 10px;"></table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-resumoVendedor" role="tabpanel"
                                aria-labelledby="tab-resumoVendedor">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="resumo"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                            style="font-size: 12px;"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .info-box-text {
            font-size: 14px;
        }

        .info-box-number {
            font-weight: bold;
            font-size: 18px;
        }
    </style>
@stop

@section('js')
    <script>
        let dadosFiltrados = [];

        $(document).ready(function() {
            $('#tabelaComissao').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
                pageLength: 25,
                processing: true,
                serverSide: false,
                autoWidth: false,
                scrollX: true,
                responsive: false,
                ajax: {
                    url: '{{ route('get-comissao-vendedor-faturamento') }}',
                    type: 'GET',
                    dataSrc: function(json) {
                        const empresaSelecionada = $('#filtro-empresa').val();
                        const dataAlvo = '2025-05-02';

                        dadosFiltrados = json.data.filter(function(item) {
                            const empresaOk = empresaSelecionada === '' || item.CD_EMPRESA ==
                                empresaSelecionada;
                            const dataOk = item.DT_EMISSAO === dataAlvo;
                            return empresaOk && dataOk;
                        });
                        return dadosFiltrados;
                    }
                },

                columns: [{
                        data: 'CD_EMPRESA',
                        title: 'Emp'

                    },
                    {
                        data: 'NM_VENDEDOR',
                        title: 'Vendedor'
                    },
                    {
                        data: 'NM_PESSOA',
                        title: 'Pessoa'
                    },
                    {
                        data: 'DT_EMISSAO',
                        title: ' Emissão'
                    },
                    {
                        data: 'NR_NOTAFISCAL',
                        title: 'Nota'
                    },
                    {
                        data: 'DS_ITEM',
                        title: 'Descrição'
                    },
                    {
                        data: 'QT_ITEMNOTA',
                        title: 'Qtde'
                    },
                    {
                        data: 'VL_UNITARIO',
                        title: 'Vl_Unitario'
                    },
                    {
                        data: 'VL_DESCONTO',
                        title: 'Desconto'
                    },
                    {
                        data: 'VL_TOTAL',
                        title: 'Total'
                    },
                    {
                        data: 'PC_COMISSAO',
                        title: '%Comissão'
                    },
                    {
                        data: 'VL_COMISSAO',
                        title: 'Vl_Comissao'
                    },
                    {
                        data: 'VL_TABPRECO',
                        title: 'Preço'
                    },
                    {
                        data: 'T_PRECO',
                        title: 'Total'
                    },
                ],
                drawCallback: function(settings) {
                    let resumo = {};

                    dadosFiltrados.forEach(row => {
                        let vendedor = row.NM_VENDEDOR || 'Sem nome';
                        let valor = parseFloat(row.VL_TOTAL) || 0;

                        if (!resumo[vendedor]) {
                            resumo[vendedor] = 0;
                        }
                        resumo[vendedor] += valor;
                    });

                    let resumoArray = Object.entries(resumo).map(([vendedor, total]) => ({
                        vendedor,
                        total
                    }));

                    if ($.fn.DataTable.isDataTable('#resumo')) {
                        let tableResumo = $('#resumo').DataTable();
                        tableResumo.clear();
                        tableResumo.rows.add(resumoArray);
                        tableResumo.draw();
                    } else {
                        $('#resumo').DataTable({
                            data: resumoArray,
                            destroy: true,
                            paging: false,
                            searching: false,
                            info: false,
                            columns: [{
                                    data: 'vendedor',
                                    title: 'Vendedor',
                                },
                                {
                                    data: 'total',
                                    title: 'Total Produzido',
                                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                                }
                            ],
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                            }
                        });
                    }

                    let totalVendas = 0;
                    let totalComissao = 0;
                    let notasEmitidas = new Set();

                    dadosFiltrados.forEach(row => {
                        totalVendas += parseFloat(row.VL_TOTAL) || 0;
                        totalComissao += parseFloat(row.VL_COMISSAO) || 0;
                        notasEmitidas.add(row.NR_NOTAFISCAL);
                    });

                    // atualiza os cards
                    $('#total-vendas-dia').text(totalVendas.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }));
                    $('#comissao-total-dia').text(totalComissao.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }));
                    $('#quantidade-notas').text(notasEmitidas.size);
                }
            });
        });
    </script>
@stop
