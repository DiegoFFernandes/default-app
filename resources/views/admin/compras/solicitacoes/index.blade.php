@extends('layouts.master')

@section('title', 'Solicitações de Compra')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
    <style>
        .badge-status { font-size: 0.8rem; }
        #filtro-status label, #filtro-urgencia label {
            display: inline-flex; align-items: center; gap: 5px;
            margin-right: 10px; margin-bottom: 0; cursor: pointer; user-select: none;
        }
        #filtro-status input[type=checkbox], #filtro-urgencia input[type=checkbox] { cursor: pointer; }
        #btn-filtro-todos { font-size: 0.72rem; }
        #table-solicitacoes { margin-bottom: 0 !important; }
        .col-solicitante { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .btn-ver-itens { cursor: pointer; text-decoration: underline dotted; }
        .btn-ver-itens:hover { text-decoration: underline; }
    </style>
@stop

@section('content')
    <section class="content">

        {{-- Cards de status --}}
        @include('admin.compras.solicitacoes.cards.cards-status')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Solicitações de Compra</h3>
                        <div class="card-tools">
                            <a href="{{ route('compras.solicitacoes.kanban') }}" class="btn btn-default btn-xs mr-1" title="Visualização Kanban">
                                <i class="fas fa-columns"></i> Kanban
                            </a>
                            <a href="{{ route('compras.solicitacoes.create') }}" class="btn btn-danger btn-xs">
                                <i class="fas fa-plus"></i> Nova Solicitação
                            </a>
                        </div>
                    </div>
                    <div class="border-bottom" style="background:#f8f9fa;">
                        <div class="px-3 pt-2 pb-1 d-flex flex-wrap align-items-center" id="filtro-status">
                            <small class="text-muted mr-2"><i class="fas fa-filter mr-1"></i>Status:</small>
                            <label><input type="checkbox" class="status-filter" value="RAS">
                                <span class="badge badge-secondary">Rascunho</span></label>
                            <label><input type="checkbox" class="status-filter" value="ANA">
                                <span class="badge badge-info">Em Análise</span></label>
                            <label><input type="checkbox" class="status-filter" value="APR">
                                <span class="badge badge-warning">Em Aprovação</span></label>
                            <label><input type="checkbox" class="status-filter" value="APC">
                                <span class="badge badge-primary">Aprovada</span></label>
                            <label><input type="checkbox" class="status-filter" value="REP">
                                <span class="badge badge-danger">Reprovada</span></label>
                            <label><input type="checkbox" class="status-filter" value="CAN">
                                <span class="badge badge-dark">Cancelada</span></label>
                            <label><input type="checkbox" class="status-filter" value="FIN">
                                <span class="badge badge-success">Finalizada</span></label>
                            <button id="btn-filtro-todos" class="btn btn-link btn-sm text-muted ml-2 p-0">Todos / Nenhum</button>
                        </div>
                        <div class="px-3 mt-1 pb-1 d-flex flex-wrap align-items-center" id="filtro-urgencia">
                            <small class="text-muted mr-2"><i class="fas fa-bolt mr-1"></i>Urgência:</small>
                            <label><input type="checkbox" class="urgencia-filter" value="I">
                                <span class="badge badge-danger">Imediato</span></label>
                            <label><input type="checkbox" class="urgencia-filter" value="U">
                                <span class="badge badge-warning">Urgente</span></label>
                            <label><input type="checkbox" class="urgencia-filter" value="N">
                                <span class="badge badge-secondary">Necessário</span></label>
                        </div>
                        <div class="px-3 pt-2 pb-2 d-flex justify-content-end">
                            <div class="input-group input-group-sm" style="max-width:320px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="search-solicitacoes" class="form-control border-left-0" placeholder="Pesquisar...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" id="btn-limpar-search" type="button" title="Limpar pesquisa">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height:500px; overflow-y:auto; overflow-x:auto;">
                        <table class="table table-striped compact table-bordered table-font-small" id="table-solicitacoes"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Empresa</th>
                                    <th>Centro de Resultado</th>
                                    <th>Solicitante</th>
                                    <th>Data</th>
                                    <th>Urgência</th>
                                    <th>Justificativa</th>
                                    <th>Status</th>
                                    <th>Valor Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Itens da Solicitação --}}
    <div class="modal fade" id="modal-itens-sol" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header py-2 bg-light">
                    <h6 class="modal-title font-weight-bold">
                        <i class="fas fa-list mr-1 text-primary"></i>
                        Itens da Solicitação <span id="modal-itens-title" class="text-primary"></span>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-2">
                    <div id="modal-itens-body"></div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            window.route = {
                languageDatatables: "{{ asset('vendor/datatables/pt-br.json') }}",
                list:      '{{ route('compras.solicitacoes.list') }}',
                destroy:   '/compras/solicitacoes/',
                finalizar: '/compras/solicitacoes/:id/finalizar',
            };

            // Filtro por status e urgência
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, row) {
                if (settings.nTable.id !== 'table-solicitacoes') return true;
                const status   = $('.status-filter:checked').map(function () { return $(this).val(); }).get();
                const urgencia = $('.urgencia-filter:checked').map(function () { return $(this).val(); }).get();
                if (status.length > 0 && !status.includes(row.ST_SOLICITACAO)) return false;
                if (urgencia.length > 0 && !urgencia.includes(row.ST_URGENCIA)) return false;
                return true;
            });

            $('body').on('change', '.status-filter, .urgencia-filter', function () {
                $('#table-solicitacoes').DataTable().draw();
            });

            $('#btn-filtro-todos').on('click', function () {
                const algumMarcado = $('.status-filter:checked').length > 0;
                $('.status-filter').prop('checked', !algumMarcado);
                $('#table-solicitacoes').DataTable().draw();
            });

            const dt = $('#table-solicitacoes').DataTable({
                processing: false,
                serverSide: false,
                ajax: {
                    url: window.route.list,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Carregando...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    complete: function() {
                        Swal.close();
                    }
                },
                dom: 'rt',
                autoWidth: false,
                columns: [
                    {
                        data: 'CD_SOLICITACAO',
                        name: 'CD_SOLICITACAO',
                        width: '60px',
                        className: 'text-center',
                        render: function(data) {
                            return '<span class="btn-ver-itens text-primary font-weight-bold" data-id="' + data + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'NM_EMPRESA',
                        name: 'NM_EMPRESA'
                    },
                    {
                        data: 'DS_CENTROCUSTO',
                        name: 'DS_CENTROCUSTO',
                        defaultContent: '-'
                    },
                    {
                        data: 'nm_solicitante',
                        name: 'nm_solicitante',
                        className: 'col-solicitante',
                        render: function(data) {
                            return '<span title="' + (data || '') + '">' + (data || '-') + '</span>';
                        }
                    },
                    {
                        data: 'DT_SOLICITACAO',
                        name: 'DT_SOLICITACAO',
                        className: 'text-center',
                        width: '100px',
                        render: function(data) {
                            if (!data) return '-';
                            const d = data.substring(0, 10).split('-');
                            return d.length === 3 ? d[2] + '/' + d[1] + '/' + d[0] : data;
                        }
                    },
                    {
                        data: 'ST_URGENCIA',
                        name: 'ST_URGENCIA',
                        className: 'text-center',
                        width: '80px',
                        orderable: false,
                        render: function(data) {
                            const map = { I: ['danger','Imediato'], U: ['warning','Urgente'], N: ['secondary','Necessário'] };
                            const [cor, label] = map[data] || ['secondary', data || '-'];
                            return '<span class="badge badge-status badge-' + cor + '">' + label + '</span>';
                        }
                    },
                    {
                        data: 'DS_JUSTIFICATIVA',
                        name: 'DS_JUSTIFICATIVA'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'vl_total_fmt',
                        name: 'vl_total_fmt',
                        orderable: false,
                        width: '120px'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        width: '180px'
                    },
                ],
                pageLength: -1,
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: window.route.languageDatatables
                },
                paging: false,
                responsive: false,
            });

            // Pesquisa global
            $('#search-solicitacoes').on('keyup', function () {
                dt.search($(this).val()).draw();
            });

            $('#btn-limpar-search').on('click', function () {
                $('#search-solicitacoes').val('');
                dt.search('').draw();
            });

            // excluir rascunho
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Excluir rascunho?',
                    text: 'A solicitação #' + id + ' será excluída permanentemente. Verifique se o solicitante ainda não esta preenchendo a solicitação!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'Não'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: window.route.destroy + id,
                        method: 'DELETE',
                        data: {
                            _token: $('[name=csrf-token]').attr('content')
                        },
                        success: function(res) {
                            if (res.errors) {
                                Swal.fire('Erro', res.errors, 'error');
                            } else {
                                Swal.fire('Excluido!', res.success, 'success').then(() => {
                                    $('#table-solicitacoes').DataTable().ajax.reload();
                                });
                            }
                        }
                    });
                });
            });

            // finalizar compra
            $('body').on('click', '.btn-finalizar', function () {
                const id    = $(this).data('id');
                const token = $('[name=csrf-token]').attr('content');
                Swal.fire({
                    title: 'Finalizar compra?',
                    text: 'A solicitação #' + id + ' será marcada como Finalizada.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Sim, finalizar',
                    cancelButtonText: 'Cancelar',
                }).then(result => {
                    if (!result.isConfirmed) return;
                    Swal.fire({ title: 'Finalizando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                    $.post(window.route.finalizar.replace(':id', id), { _token: token }, function (res) {
                        if (res.errors) {
                            Swal.fire({ icon: 'warning', title: 'Atenção', text: res.errors, confirmButtonColor: '#dc3545' });
                        } else {
                            Swal.fire({ icon: 'success', title: 'Finalizada!', text: res.success, confirmButtonColor: '#28a745' }).then(() => {
                                $('#table-solicitacoes').DataTable().ajax.reload();
                            });
                        }
                    });
                });
            });

            // Modal de itens ao clicar no código da solicitação
            const _itensCache = {};

            function renderModalItens(items) {
                if (!items || items.length === 0) {
                    $('#modal-itens-body').html('<p class="text-muted text-center py-3 mb-0">Sem itens cadastrados.</p>');
                    return;
                }
                let html = '<table class="table table-sm table-bordered table-striped mb-0">';
                html += '<thead class="thead-light"><tr><th class="text-center" style="width:40px">#</th><th>Descrição do Item</th><th class="text-right" style="width:80px">Qtd</th><th style="width:60px">Un</th></tr></thead><tbody>';
                items.forEach(function (item, idx) {
                    html += '<tr>'
                        + '<td class="text-center">' + (idx + 1) + '</td>'
                        + '<td>' + item.ds_item + '</td>'
                        + '<td class="text-right">' + item.qt_item + '</td>'
                        + '<td>' + item.ds_unidade + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                $('#modal-itens-body').html(html);
            }

            $('body').on('click', '.btn-ver-itens', function () {
                const id = $(this).data('id');
                $('#modal-itens-title').text('#' + id);
                $('#modal-itens-body').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-lg text-muted"></i></div>');
                $('#modal-itens-sol').modal('show');

                if (_itensCache[id] !== undefined) {
                    renderModalItens(_itensCache[id]);
                } else {
                    $.get('/compras/solicitacoes/' + id + '/itens-tooltip', function (data) {
                        _itensCache[id] = data;
                        renderModalItens(data);
                    });
                }
            });

        });
    </script>
@stop
