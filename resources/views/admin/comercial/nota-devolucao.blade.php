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
                                        <option value="1" selected>Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2">
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
                                    <label for="filtro-notas-entrada">Nota de Entrada:</label>
                                    <select id="filtro-notas-entrada" class="form-control mt-1">
                                        <option value="1" selected>Todas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-cliente">Cliente:</label>
                                    <select id="filtro-cliente" class="form-control mt-1">
                                        <option value="1" selected>Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-produto">Produto:</label>
                                    <select id="filtro-produto" class="form-control mt-1">
                                        <option value="1" selected>Todos</option>
                                    </select>
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
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Cd_Empresa</th>
                                    <th>Pessoa</th>
                                    <th>Nota de Entrada</th>
                                    <th>Nota de Saída</th>
                                    <th>Descrição</th>
                                    <th>Qtde de Entrada</th>
                                    <th>Qtde de Saída</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
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
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('get-nota-devolucao.index') }}',
                type: 'GET',
                success: function(data) {
                    const dataFixa = '2025-03-07';
                    const dadosFiltrados = data.filter(item => item.DT_EMISSAO && item.DT_EMISSAO.startsWith(dataFixa));atualizarInfoBoxes(dadosFiltrados);

                    $('#acompanhaNotaFiscal').DataTable({
                        processing: true,
                        serverSide: false,
                        data: dadosFiltrados, // Agora deve ter dados!
                        columns: [{
                                data: 'CD_EMPRESA',
                                name: 'CD_EMPRESA'
                            },
                            {
                                data: 'NM_PESSOA',
                                name: 'NM_PESSOA'
                            },
                            {
                                data: 'NOTA_ENTRADA',
                                name: 'NOTA_ENTRADA'
                            },
                            {
                                data: 'NOTA_SAIDA',
                                name: 'NOTA_SAIDA'
                            },
                            {
                                data: 'DS_ITEM',
                                name: 'DS_ITEM'
                            },
                            {
                                data: 'QT_ENTRADA',
                                name: 'QT_ENTRADA'
                            },
                            {
                                data: 'QT_SAIDA',
                                name: 'QT_SAIDA'
                            },
                            {
                                data: null,
                                name: 'SALDO',
                                render: function(data, type, row) {
                                    const entrada = parseInt(row.QT_ENTRADA) || 0;
                                    const saida = parseInt(row.QT_SAIDA) || 0;
                                    const saldo = entrada - saida;
                                    return saldo;
                                }
                            },
                        ],
                        language: {
                            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                        },
                        pageLength: 100
                    });
                },
                error: function(xhr) {
                    console.error('Erro ao carregar os dados:', xhr);
                }
            });
        });
    </script>
@stop
