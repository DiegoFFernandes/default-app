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
                                    <label for="filtro-empresa">Empresa:</label>
                                    <select id="filtro-Empresa" class="form-control mt-1">
                                        @foreach ($empresas as $e)
                                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
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
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-nota-entrada">Nota de Entrada:</label>
                                    <input type="text" class="form-control mt-1" id="filtro-nota-entrada"
                                        placeholder="Nota de Entrada">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-cliente">Cliente:</label>
                                    <input type="text" class="form-control mt-1" id="filtro-cliente"
                                        placeholder="Nome ou Código do Cliente">
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-produto">Produto:</label>
                                    <input type="text" class="form-control mt-1" id="filtro-produto"
                                        placeholder="Nome ou Código do Produto">
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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                        </div>
                        <table id="acompanhaNotaFiscal"
                            class="table compact table-font-small table-striped table-bordered nowrap"
                            style="width:100%; font-size: 12px;">
                        </table>
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

            // const routes = {
            //     searchPessoa: "{{ route('usuario.search-pessoa') }}",
            // }

            // initSelect2Pessoa('#pessoa', routes.searchPessoa);
            var table = [];

            const datasSelecionadas = initDateRangePicker();

            initTable();

            function initTable(dados = null) {
                table = $('#acompanhaNotaFiscal').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    pagingType: "simple",                    
                    autoWidth: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                    pageLength: 20,
                    ajax: {
                        url: '{{ route('get-nota-devolucao.index') }}',
                    },
                    columns: [{
                            data: 'CD_EMPRESA',
                            name: 'CD_EMPRESA',
                            title: 'Código Empresa',
                            visible: false
                        },
                        {
                            data: 'NM_EMPRESA',
                            name: 'NM_EMPRESA',
                            title: 'Empresa'
                        },
                        {
                            data: 'NM_PESSOA',
                            name: 'NM_PESSOA',
                            title: 'Pessoa',
                        },
                        {
                            data: 'DT_EMISSAO',
                            name: 'DT_EMISSAO',
                            title: 'Data de Emissão',
                            render: function(data, type, row) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'NOTA_ENTRADA',
                            name: 'NOTA_ENTRADA',
                            title: 'Nota de Entrada'
                        },
                        {
                            data: 'NOTA_SAIDA',
                            name: 'NOTA_SAIDA',
                            title: 'Nota de Saída'
                        },
                        {
                            data: 'DS_ITEM',
                            name: 'DS_ITEM',
                            title: 'Descrição'
                        },
                        {
                            data: 'QT_ENTRADA',
                            name: 'QT_ENTRADA',
                            title: 'Qtde de Entrada'
                        },
                        {
                            data: 'QT_SAIDA',
                            name: 'QT_SAIDA',
                            title: 'Qtde de Saída'
                        },
                        {
                            data: 'SALDO',
                            name: 'SALDO',
                            title: 'Saldo',
                        },
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

            $('#filtro-cliente').on('keyup change', function() {
                const valor = $(this).val().toLowerCase();
                table.search(valor).draw();
            });

            $('#filtro-produto').on('keyup change', function() {
                const valor = $(this).val();
                table.search(valor).draw();
            });

            $('#filtro-Empresa').on('keyup change', function() {
                const valor = $('#filtro-Empresa').val();
                table.search(valor).draw();
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                const startDate = picker.startDate.format('YYYY-MM-DD');
                const endDate = picker.endDate.format('YYYY-MM-DD');
                table.draw();
            });
            
            $('#filtro-nota-entrada').on('keyup change', function() {
                const valor = $(this).val();
                table.search(valor).draw();
            });
            $('#submit-seach').on('click', function() {
                table.draw();
            });

            // Atualiza as informações dos InfoBoxes
            table.on('draw', function() {
                atualizarInfoBoxes(table.rows({
                    search: 'applied'
                }).data().toArray());
            });
        });
    </script>
@stop
