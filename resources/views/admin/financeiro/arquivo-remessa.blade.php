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
                            <div class="stat-row">
                                <span class="stat-row-label">Registro Recusado</span>
                                <span class="stat-row-val" id="qtd-registro-recusado">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
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
                        <div class="col-md-2 mb-1">
                            <label for="daterange-remessa" class="form-label small"><i
                                    class="fas fa-calendar-alt mr-1 text-muted"></i>Emissão</label>
                            <input id="daterange-remessa" type="text" class="form-control form-control-sm"
                                placeholder="Filtrar por Emissão">
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="filtro-cd-empresa" class="form-label small"><i
                                    class="fas fa-building mr-1 text-muted"></i>Empresa</label>
                            <select id="filtro-cd-empresa" class="form-control form-control-sm" style="width:100%;">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label for="filtro-cliente" class="form-label small"><i
                                    class="fas fa-user mr-1 text-muted"></i>Cliente</label>
                            <select id="filtro-cliente" class="form-control form-control-sm" style="width:100%;">
                                <option value="">Filtrar por Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <label for="filtro-forma-pagto" class="form-label small"><i
                                    class="fas fa-credit-card mr-1 text-muted"></i>Forma de Pagamento</label>
                            <select id="filtro-forma-pagto" class="form-control form-control-sm">
                                <option value="">Todas as Formas de Pagamento</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-1 d-flex align-items-end">
                            <button id="btn-search-remessa" class="btn btn-primary btn-sm btn-block"
                                title="Pesquisar com os filtros selecionados">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-2 d-flex justify-content-between align-items-center flex-wrap">
                        <small class="badge badge-danger badge-date"></small>
                    </div>
                    <table class="table stripe compact" id="table-arquivo-remessa" style="width:100%;">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="dt-select-all-contas" title="Selecionar todos"
                                        style="margin:0;"></th>
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
                                <th>Registrado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="card-footer">                    
                    <button id="btn-confirmar-selecionados" class="btn btn-success btn-sm"
                        title="Confirmar o registro no banco de todos os títulos marcados na tabela">
                        <i class="fas fa-check mr-1"></i>Confirmar Registro Selecionados
                    </button>
                    <span class="badge badge-warning registros-count-badge mr-2"
                        style="display:none; font-size:.85rem;"></span>
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

        /* --- Coluna checkbox --- */
        td.text-center:has(.dt-row-checkbox-contas),
        th:has(.dt-select-all-contas) {
            width: 30px !important;
            min-width: 30px !important;
            max-width: 30px !important;
            padding: 4px !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        .dt-row-checkbox-contas,
        .dt-select-all-contas {
            cursor: pointer;
            vertical-align: middle;
        }

        /* --- Coluna Registro --- */
        #table-arquivo-remessa .btn-xs {
            font-size: 0.65rem;
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
        var selectedRegistros = new Map();

        function updateRegistrosBadge() {
            var count = selectedRegistros.size;
            var $badge = $('.registros-count-badge');
            if (count > 0) {
                $badge.text(count + ' selecionado' + (count > 1 ? 's' : '')).show();
            } else {
                $badge.hide();
            }
        }

        function confirmarRegistroBoleto(contas) {
            if (!contas.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhum título selecionado',
                    text: 'Selecione ao menos um título para confirmar o registro.',
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-warning',
                    },
                });
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Confirmar registro no banco?',
                text: contas.length + ' título(s) selecionado(s).',
                showCancelButton: true,
                confirmButtonText: 'Sim, confirmar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success mr-2',
                    cancelButton: 'btn btn-secondary',
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    method: 'post',
                    url: "{{ route('contas-boleto.update') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        contas: contas,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processando...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: response.success,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        selectedRegistros.clear();
                        updateRegistrosBadge();
                        $('.dt-select-all-contas').prop('checked', false);
                        tableArquivoRemessa.ajax.reload();
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocorreu um erro ao processar a solicitação.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            });
        }

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

        $.get("{{ route('firebird.empresas') }}", function(empresas) {
            (empresas || []).forEach(function(e) {
                $('#filtro-cd-empresa').append(new Option(e.text, e.id));
            });
        });

        tableArquivoRemessa = initTableArquivoRemessa();

        $('#btn-search-remessa').on('click', function() {
            if (datasSelecionadasRemessa.getInicio() != 0) {
                dtInicioRemessa = datasSelecionadasRemessa.getInicio();
                dtFimRemessa = datasSelecionadasRemessa.getFim();
            }
            $('.badge-date').text('Período: ' + dtInicioRemessa + ' a ' + dtFimRemessa);
            selectedRegistros.clear();
            updateRegistrosBadge();
            tableArquivoRemessa.ajax.reload();
        });

        $('#btn-confirmar-selecionados').on('click', function() {
            confirmarRegistroBoleto(Array.from(selectedRegistros.values()));
        });

        // Confirmação individual (botão na linha)
        $('tbody').on('click', '.btn-confirmar-registro', function() {
            var $btn = $(this);
            confirmarRegistroBoleto([{
                cd_empresa: $btn.data('cd-empresa'),
                nr_boleto: $btn.data('nr-boleto'),
                cd_formapagto: $btn.data('cd-formapagto'),
            }]);
        });

        // Quantidade de linhas selecionaveis (com boleto e ainda nao confirmadas) no filtro atual
        function totalRegistrosSelecionaveis() {
            return tableArquivoRemessa.rows({ search: 'applied' }).data().toArray().filter(function(row) {
                return row.ST_REGISTRO !== 'S' && !!row.NR_BOLETO;
            }).length;
        }

        // Select all — le os dados direto da API do DataTables (nao do DOM), pra nao depender
        // dos checkboxes sobreviverem a clonagem do cabecalho feita pelo scrollX/scrollY.
        $(document).on('click', '.dt-select-all-contas', function(e) {
            e.stopPropagation();
            var checked = $(this).is(':checked');

            tableArquivoRemessa.rows({ search: 'applied' }).every(function() {
                var row = this.data();
                if (row.ST_REGISTRO === 'S' || !row.NR_BOLETO) return;

                var key = row.CD_EMPRESA + '-' + row.NR_BOLETO + '-' + row.CD_FORMAPAGTO;
                if (checked) {
                    selectedRegistros.set(key, {
                        cd_empresa: row.CD_EMPRESA,
                        nr_boleto: row.NR_BOLETO,
                        cd_formapagto: row.CD_FORMAPAGTO,
                    });
                } else {
                    selectedRegistros.delete(key);
                }
            });

            // sincroniza visualmente os checkboxes das linhas (tbody nao eh clonado pelo scrollX/scrollY)
            tableArquivoRemessa.rows({ search: 'applied' }).nodes().to$()
                .find('.dt-row-checkbox-contas').prop('checked', checked);

            updateRegistrosBadge();
        });

        // Checkbox individual
        $(document).on('click', '.dt-row-checkbox-contas', function(e) {
            e.stopPropagation();
            var tr = $(this).closest('tr');
            var row = tableArquivoRemessa.row(tr).data();
            if (!row) return;

            var key = row.CD_EMPRESA + '-' + row.NR_BOLETO + '-' + row.CD_FORMAPAGTO;
            if ($(this).is(':checked')) {
                selectedRegistros.set(key, {
                    cd_empresa: row.CD_EMPRESA,
                    nr_boleto: row.NR_BOLETO,
                    cd_formapagto: row.CD_FORMAPAGTO,
                });
            } else {
                selectedRegistros.delete(key);
            }

            var total = totalRegistrosSelecionaveis();
            $('.dt-select-all-contas').prop('checked', total > 0 && selectedRegistros.size === total);
            updateRegistrosBadge();
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
                        d.cd_empresa = $('#filtro-cd-empresa').val();
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
                        $('#qtd-registro-recusado').text(json.qtd_registro_recusado);

                        return json.datatables.data;
                    }
                },
                columns: [{
                        data: null,
                        width: '1%',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type !== 'display') return '';
                            if (row.ST_REGISTRO === 'S' || !row.NR_BOLETO) return '';
                            return '<input type="checkbox" class="dt-row-checkbox-contas" aria-label="Selecionar linha" style="margin:0;">';
                        }
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        width: '1%',
                        className: 'text-center'
                    },
                    {
                        data: 'DT_LANCAMENTO',
                        name: 'DT_LANCAMENTO',   
                        className: 'text-center'                     
                    },
                    {
                        data: 'DT_VENCIMENTO',
                        name: 'DT_VENCIMENTO', 
                        className: 'text-center'                       
                    },
                    {
                        data: null,
                        width: '2%',
                        render: function(data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                return String(row.NR_DOCUMENTO).padStart(10, '0') + '-' + String(row
                                    .NR_PARCELA).padStart(5, '0');
                            }
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
                    {
                        data: 'registro',
                        name: 'registro',
                        orderable: false,
                        className: 'text-center',
                    }
                ],
                columnDefs: [{
                        targets: [2, 3],
                        render: function(data, type, row) {
                            if (!data) return '';
                            if (type !== 'display') return data;
                            var parts = data.substring(0, 10).split('-');
                            if (parts.length !== 3) return data;
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                    },
                    {
                        targets: [7],
                        render: $.fn.dataTable.render.number('.', ',', 2),
                    }
                ],
                order: [
                    [2, 'asc']
                ],
            });
        }
    </script>
@stop
