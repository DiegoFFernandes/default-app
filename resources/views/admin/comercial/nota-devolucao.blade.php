@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box ">
                    <span class="info-box-icon bg-success"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus Entrada</span>
                        <span class="info-box-number" id="pneus-entrada">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-sign-out-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus Saída</span>
                        <span class="info-box-number" id="pneus-saida">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo P/ Devolução</span>
                        <span class="info-box-number" id="saldo-devolucao">0</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filtro (mudar de acordo com a nescessidade da página) -->
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
                                    <label class="small" for="filtro-empresa">Empresa:</label>
                                    <input type="text" class="form-control form-control-sm mt-1" id="filtro-Empresa"
                                        placeholder="Nome da Empresa">
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small" for="daterange">Data:</label>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm" id="daterange"
                                            placeholder="Selecione a Data">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small" for="filtro-nota-entrada">Nota de Entrada:</label>
                                    <input type="text" class="form-control form-control-sm mt-1" id="filtro-nota-entrada"
                                        placeholder="Nota de Entrada">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small" for="filtro-cliente">Cliente:</label>
                                    <input type="text" class="form-control form-control-sm mt-1" id="filtro-cliente"
                                        placeholder="Nome ou Código do Cliente">
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small" for="filtro-municipio">Municipio:</label>
                                    <input type="text" class="form-control form-control-sm mt-1" id="filtro-municipio"
                                        placeholder="Municipio">
                                </div>
                            </div>
                            <div class="col-12 col-md-3 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small" for="filtro-produto">Produto:</label>
                                    <input type="text" class="form-control form-control-sm mt-1" id="filtro-produto"
                                        placeholder="Nome ou Código do Produto">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="reset-filters" class="btn btn-xs btn-secondary float-right">Limpar Filtros</button>
                        <button id="submit-seach" class="btn btn-xs btn-primary float-right mr-2">Pesquisar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-warning">Devolução Parcial</span>
                            <span class="badge border">Devolução Total</span>
                        </div>
                        <div class="table-responsive">
                            <table id="acompanhaNotaFiscal"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-nota-devolucao-detalhes" tabindex="-1" role="dialog"
            aria-labelledby="modal-nota-devolucao-detalhes-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-nota-devolucao-detalhes-label">Detalhes da Nota de Devolução
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="detalhesNotaDevolucao"
                                class="table table-striped table-bordered compact table-font-small" style="width:100%;">
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
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

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.6);
        }

        .btn-primary {
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.6);
        }
    </style>

@stop

@section('js')
    <script>
        $(document).ready(function() {

            window.routes = {
                getNotaDevolucao: "{{ route('get-nota-devolucao.index') }}",
                languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
                getNotaDevolucaoDetalhes: "{{ route('get-nota-devolucao.detalhes') }}",
            }

            // initSelect2Pessoa('#pessoa', routes.searchPessoa);
            var table = [];

            const datasSelecionadas = initDateRangePicker();

            initTable();

            $('#filtro-municipio').on('change', function() {
                const valor = $(this).val();
                table.column(10).search(valor).draw();
            });

            $('#filtro-cliente').on('keyup change', function() {
                const valor = $(this).val().toLowerCase();
                table.column(2).search(valor).draw();
            });

            $('#filtro-produto').on('keyup change', function() {
                const valor = $(this).val();
                table.column(6).search(valor).draw();
            });

            $('#filtro-Empresa').on('keyup change', function() {
                const valor = $(this).val().toLowerCase();
                table.column(1).search(valor).draw();
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                const startDate = picker.startDate.format('YYYY-MM-DD');
                const endDate = picker.endDate.format('YYYY-MM-DD');
                table.column(4).search(startDate + '|' + endDate, true, false).draw();
            });

            $('#filtro-nota-entrada').on('keyup change', function() {
                const valor = $(this).val();
                table.column(3).search(valor).draw();
            });
            $('#submit-seach').on('click', function() {
                table.draw();
            });
            $('#reset-filters').on('click', function() {
                $('#filtro-cliente').val('');
                $('#filtro-produto').val('');
                $('#filtro-Empresa').val('');
                $('#daterange').val('');
                $('#filtro-nota-entrada').val('');
                table.search('').columns().search('').draw();
            });

            // Atualiza as informações dos InfoBoxes
            table.on('draw', function() {
                atualizarInfoBoxes(table.rows({
                    search: 'applied'
                }).data().toArray());
            });

            $(document).on('click', '.btn-detalhes-nota', function(e) {
                const data = table.row($(this).closest('tr')).data();
                const nrLancamento = data.NR_LANCORIG;
                const cdEmpresa = data.CD_EMPRESA;
                const cdItem = data.CD_ITEM;

                $('#modal-nota-devolucao-detalhes').modal('show');

                $('#detalhesNotaDevolucao').DataTable({
                    processing: false,
                    serverSide: false,
                    language: {
                        url: window.routes.languageDatatables
                    },
                    destroy: true,
                    pagingType: "simple",
                    autoWidth: true,
                    ajax: {
                        url: "{{ route('get-nota-devolucao.detalhes') }}",
                        method: 'POST',
                        data: {
                            nr_lancamento: nrLancamento,
                            cd_empresa: cdEmpresa,
                            cd_item: cdItem,
                            _token: "{{ csrf_token() }}",
                        }
                    },
                    columns: [{
                            data: 'NM_PESSOA',
                            name: 'NM_PESSOA',
                            title: 'Pessoa',
                        },
                        {
                            data: 'DT_EMISSAO',
                            name: 'DT_EMISSAO',
                            title: 'Emissão',
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (!data) return '';
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'NR_NOTAFISCAL',
                            name: 'NR_NOTAFISCAL',
                            title: 'NF Saída',
                            className: 'text-center',
                        },
                        {
                            data: 'DS_ITEM',
                            name: 'DS_ITEM',
                            title: 'Descrição'
                        },
                        {
                            data: 'QT_DEVOLUCAO',
                            name: 'QT_DEVOLUCAO',
                            title: 'Qtde Saída'
                        },
                        {
                            data: 'ST_MDFE',
                            name: 'ST_MDFE',
                            title: 'MDFE'
                        }
                    ],
                });
            });

            function initTable(dados = null) {
                table = $('#acompanhaNotaFiscal').DataTable({
                    processing: false,
                    serverSide: false,
                    pagingType: "simple",
                    autoWidth: true,
                    scrollY: '400px',
                    language: {
                        url: window.routes.languageDatatables
                    },
                    pageLength: 25,
                    ajax: {
                        url: window.routes.getNotaDevolucao,
                    },
                    columns: [{
                            data: 'actions',
                            name: 'actions',
                            title: '#',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },

                        {
                            data: 'CD_EMPRESA',
                            name: 'CD_EMPRESA',
                            title: 'Código Empresa',
                            visible: false
                        },
                        {
                            data: 'NM_EMPRESA',
                            name: 'NM_EMPRESA',
                            title: 'Empresa',
                            className: 'text-center'
                        },
                        {
                            data: 'NM_PESSOA',
                            name: 'NM_PESSOA',
                            title: 'Pessoa',
                        },
                        {
                            data: 'DT_EMISSAO',
                            name: 'DT_EMISSAO',
                            title: 'Emissão',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'NOTA_ENTRADA',
                            name: 'NOTA_ENTRADA',
                            title: 'NF Entrada',
                            className: 'text-center',
                        },
                        {
                            data: 'DS_ITEM',
                            name: 'DS_ITEM',
                            title: 'Descrição'
                        },
                        {
                            data: 'QT_ENTRADA',
                            name: 'QT_ENTRADA',
                            title: 'Qtde Entrada'
                        },
                        {
                            data: 'QT_SAIDA',
                            name: 'QT_SAIDA',
                            title: 'Qtde Saída'
                        },
                        {
                            data: 'SALDO',
                            name: 'SALDO',
                            title: 'Saldo',
                        },
                        {
                            data: 'DS_MUNICIPIO',
                            name: 'DS_MUNICIPIO',
                            title: 'Municipio'
                        }
                    ],

                });
            }

            function atualizarInfoBoxes(dados) {
                let totalEntrada = 0;
                let totalSaida = 0;

                dados.forEach(item => {
                    totalEntrada += parseInt(item.QT_ENTRADA) || 0;
                    totalSaida += parseInt(item.QT_SAIDA) || 0;
                });
                const saldo = totalEntrada - totalSaida;

                $('#pneus-entrada').text(totalEntrada);
                $('#pneus-saida').text(totalSaida);
                $('#saldo-devolucao').text(saldo);
            }

        });
    </script>
@stop
