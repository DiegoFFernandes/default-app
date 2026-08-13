@extends('layouts.master')

@section('title', 'Arquivo Remessa')

@section('content')
    <section class="content">
        <div class="content-fluid">
            <div class="row mb-2">
                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <div class="stat-card stat-primary">
                        <div class="stat-title"><i class="fas fa-info-circle"></i> Informações</div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Quantidade de Títulos</span>
                                <span class="stat-row-val" id="qtd-titulos">0</span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-row-label">Valor Acumulado</span>
                                <span class="stat-row-val" id="valor-titulos">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <div class="stat-card stat-warning">
                        <div class="stat-title"><i class="fas fa-file-invoice"></i> Status</div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Boleto Impresso</span>
                                <span class="stat-row-val" id="qtd-boleto-impresso">0</span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-row-label">Sem Boleto</span>
                                <span class="stat-row-val" id="qtd-sem-boleto">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <div class="stat-card stat-danger">
                        <div class="stat-title"><i class="fas fa-university"></i> Remessa</div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Sem Arquivo Remessa</span>
                                <span class="stat-row-val" id="qtd-sem-remessa">0</span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-row-label">Registrar no Banco</span>
                                <span class="stat-row-val" id="qtd-registrar-remessa">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1 text-muted"></i> Filtros</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <input id="daterange-remessa" type="text" class="form-control form-control-sm"
                                placeholder="Filtrar por Emissão">
                        </div>
                        <div class="col-md-5 mb-1">
                            <select id="filtro-cliente" class="form-control form-control-sm" style="width:100%;">
                                <option value="">Filtrar por Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <select id="filtro-forma-pagto" class="form-control form-control-sm">
                                <option value="">Todas as Formas de Pagamento</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-1">
                            <button id="btn-search-remessa" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <small class="badge badge-danger badge-date"></small>
                    </div>
                    <table class="table stripe compact" id="table-arquivo-remessa" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Emp</th>
                                <th>Emissão</th>
                                <th>Vencimento</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>Forma Pagto</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Remessa</th>
                                <th>Nome Arquivo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .09);
            border-left: 4px solid;
            border-radius: 4px;
            padding: 10px 12px;
            height: 100%;
            position: relative;
        }

        .stat-card .stat-title {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card .stat-title i {
            font-size: .7rem;
        }

        .stat-card .stat-value {
            font-size: 1rem;
            font-weight: 700;
            word-break: break-all;
            line-height: 1.3;
        }

        .stat-card .stat-rows {
            margin-top: 1px;
        }

        .stat-card .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: .71rem;
            padding: 2px 0;
            border-top: 1px solid rgba(0, 0, 0, .05);
        }

        .stat-card .stat-row-label {
            color: #6c757d;
            flex-shrink: 0;
        }

        .stat-card .stat-row-val {
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 58%;
        }

        .stat-primary {
            border-left-color: #007bff;
        }

        .stat-primary .stat-title i,
        .stat-primary .stat-value {
            color: #007bff;
        }

        .stat-info {
            border-left-color: #17a2b8;
        }

        .stat-info .stat-title i,
        .stat-info .stat-value {
            color: #17a2b8;
        }

        .stat-warning {
            border-left-color: #e0a800;
        }

        .stat-warning .stat-title i,
        .stat-warning .stat-value {
            color: #c89100;
        }

        .stat-danger {
            border-left-color: #dc3545;
        }

        .stat-danger .stat-title i,
        .stat-danger .stat-value {
            color: #dc3545;
        }

        table.dataTable thead tr {
            background-color: #444B53;
            color: #ffffff;
        }

        table.dataTable thead th {
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .3px;
            padding: 8px 10px;
            border-bottom: 2px solid #2d3238 !important;
            white-space: nowrap;
        }
    </style>
@stop

@section('js')
    <script type="text/javascript">
        var tableArquivoRemessa;

        // Obtem o primeiro dia do mes atual
        var dtInicioRemessa = moment().subtract(90, 'days').startOf('day').format('DD.MM.YYYY');
        var dtFimRemessa = moment().format('DD.MM.YYYY');

        var datasSelecionadasRemessa = initDateRangePicker('#daterange-remessa', dtInicioRemessa, dtFimRemessa);

        $('.badge-date').text('Período: ' + dtInicioRemessa + ' a ' + dtFimRemessa);

        $('#filtro-cliente').select2({
            theme: 'bootstrap4',
            language: 'pt-BR',
            placeholder: 'Filtrar por Cliente',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('usuario.search-pessoa') }}",
                type: 'POST',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term,
                        _token: '{{ csrf_token() }}'
                    };
                },
                processResults: function(items) {
                    return {
                        results: items.map(function(item) {
                            return {
                                text: item.NM_PESSOA,
                                id: item.ID
                            };
                        })
                    };
                }
            }
        });

        $.get("{{ route('get-form-pagamento') }}", function(formas) {
            formas.forEach(function(forma) {
                $('#filtro-forma-pagto').append(
                    '<option value="' + forma.CD_FORMAPAGTO + '">' + forma.DS_FORMAPAGTO + '</option>'
                );
            });
        });

        tableArquivoRemessa = initTableArquivoRemessa();

        $('#btn-search-remessa').on('click', function() {
            if (datasSelecionadasRemessa.getInicio() != 0) {
                dtInicioRemessa = datasSelecionadasRemessa.getInicio();
                dtFimRemessa = datasSelecionadasRemessa.getFim();
            }
            tableArquivoRemessa.ajax.reload();
        });

        function initTableArquivoRemessa() {
            return $('#table-arquivo-remessa').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                searching: true,
                paging: false,
                processing: false,
                serverSide: false,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('arquivo-remessa.list') }}",
                    data: function(d) {
                        d.dt_inicio = dtInicioRemessa;
                        d.dt_fim = dtFimRemessa;
                        d.cd_pessoa = $('#filtro-cliente').val();
                        d.cd_formapagto = $('#filtro-forma-pagto').val();
                    },
                    beforeSend: function() {
                        window._swalRemessaTimer = setTimeout(function() {
                            Swal.fire({
                                title: 'Carregando títulos...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        }, 400);
                    },
                    complete: function() {
                        clearTimeout(window._swalRemessaTimer);
                        Swal.close();
                    },
                    dataSrc: function(json) {
                        $('#qtd-titulos').text(json.qtd_titulos);
                        $('#valor-titulos').text('R$ ' + json.vlr_titulos);

                        $('#qtd-boleto-impresso').text(json.qtd_boleto_impresso);
                        $('#qtd-sem-boleto').text(json.qtd_sem_boleto);

                        $('#qtd-sem-remessa').text(json.qtd_sem_remessa);
                        $('#qtd-registrar-remessa').text(json.qtd_registrar_remessa);

                        return json.datatables.data;
                    }
                },
                columns: [{
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        width: '1%',
                        className: 'text-center'
                    },
                    {
                        data: 'DT_LANCAMENTO',
                        name: 'DT_LANCAMENTO',
                        width: '2%',
                    },
                    {
                        data: 'DT_VENCIMENTO',
                        name: 'DT_VENCIMENTO',
                        width: '2%',
                    },
                    {
                        data: null,
                        width: '2%',
                        render: function(data, type, row) {
                            return row.NR_DOCUMENTO + ' / ' + row.NR_PARCELA;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.CD_PESSOA + ' - ' + row.NM_PESSOA;
                        }
                    },
                    {
                        data: 'DS_FORMAPAGTO',
                        name: 'DS_FORMAPAGTO',
                    },
                    {
                        data: 'VL_SALDO',
                        name: 'VL_SALDO',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'remessa',
                        name: 'remessa',
                        orderable: false,
                    },
                    {
                        data: 'R_NR_ARQUIVO',
                        name: 'R_NR_ARQUIVO',
                        orderable: false,
                        visible: false
                    },
                ],
                columnDefs: [{
                        targets: [1, 2],
                        render: function(data, type, row) {
                            if (!data) return '';
                            var parts = data.substring(0, 10).split('-');
                            if (parts.length !== 3) return data;
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                    },
                    {
                        targets: [6],
                        render: $.fn.dataTable.render.number('.', ',', 2),
                    }
                ],
                order: [
                    [1, 'desc']
                ],
            });
        }
    </script>
@stop
