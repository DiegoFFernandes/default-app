@extends('layouts.master')

@section('title', $title_page)

@section('content')
<section class="content">
    @php $idSolicitacao = $solicitacao->CD_SOLICITACAO ?? null; @endphp

    <input type="hidden" id="id_solicitacao" value="{{ $idSolicitacao }}">

    <div class="card card-danger card-outline card-outline-tabs">

        {{-- Nav Tabs --}}
        <div class="card-header p-0 d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs border-bottom-0" id="tabs-form" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="pill" href="#pane-cabecalho" role="tab">
                        <i class="fas fa-info-circle mr-1"></i> Cabeçalho
                    </a>
                </li>
                @if($idSolicitacao)
                <li class="nav-item">
                    <a class="nav-link" id="tab-itens-link" data-toggle="pill" href="#pane-itens" role="tab">
                        <i class="fas fa-list mr-1"></i> Itens
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-cotacoes-link" data-toggle="pill" href="#pane-cotacoes" role="tab">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Cotações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-fornecedor-link" data-toggle="pill" href="#pane-fornecedor" role="tab">
                        <i class="fas fa-handshake mr-1"></i> Fornecedor Ganhador
                    </a>
                </li>
                @endif
            </ul>

            <div class="card-tools mr-2">
                @if($idSolicitacao)
                    <button id="btn-submeter" class="btn btn-danger btn-xs mr-1">
                        <i class="fas fa-paper-plane"></i> Enviar para Aprovação
                    </button>
                    <a href="{{ route('compras.solicitacoes.show', $idSolicitacao) }}"
                        class="btn btn-info btn-xs mr-1">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                @endif
                <a href="{{ route('compras.solicitacoes.index') }}" class="btn btn-default btn-xs">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="card-body">
            <div class="tab-content" id="tabs-form-content">

                {{-- Tab: Cabeçalho --}}
                <div class="tab-pane fade show active" id="pane-cabecalho" role="tabpanel">
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                                <select class="form-control form-control-sm select2" id="cd_empresa" style="width:100%">
                                    <option value="">Selecione</option>
                                    @foreach($empresas as $e)
                                        <option value="{{ $e->CD_EMPRESA }}"
                                            {{ isset($solicitacao) && $solicitacao->CD_EMPRESA == $e->CD_EMPRESA ? 'selected' : '' }}>
                                            {{ $e->NM_EMPRESA }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Data <span class="text-danger">*</span></small></label>
                                <input type="date" class="form-control form-control-sm" id="dt_solicitacao"
                                    value="{{ isset($solicitacao) ? $solicitacao->DT_SOLICITACAO : date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Justificativa <span class="text-danger">*</span></small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_justificativa" maxlength="500"
                                    value="{{ $solicitacao->DS_JUSTIFICATIVA ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Observações</small></label>
                                <textarea class="form-control form-control-sm" id="ds_observacao" rows="2" maxlength="500">{{ $solicitacao->DS_OBSERVACAO ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            @if(!$idSolicitacao)
                                <button id="btn-salvar" class="btn btn-danger btn-sm">
                                    <i class="fas fa-save"></i> Salvar Rascunho
                                </button>
                            @else
                                <button id="btn-atualizar" class="btn btn-warning btn-sm">
                                    <i class="fas fa-save"></i> Atualizar Cabeçalho
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                @if($idSolicitacao)

                {{-- Tab: Itens --}}
                <div class="tab-pane fade" id="pane-itens" role="tabpanel">
                    <div class="row mt-1 mb-1">
                        <div class="col-md-5">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Produto <span class="text-danger">*</span></small></label>
                                <select class="form-control form-control-sm select2-ajax" id="cd_item" style="width:100%"
                                    data-url="{{ route('compras.search-item') }}"
                                    data-placeholder="Buscar produto (mín. 3 caracteres)"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Quantidade <span class="text-danger">*</span></small></label>
                                <input type="number" class="form-control form-control-sm" id="qt_item" min="0.001" step="0.001" placeholder="0.000">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Unidade <span class="text-danger">*</span></small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_unidade" maxlength="10" placeholder="UN">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Observação</small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_obs_item" maxlength="300">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <button id="btn-add-item" class="btn btn-danger btn-sm">
                                <i class="fas fa-plus"></i> Adicionar Item
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered compact table-font-small" id="table-itens" style="width:100%">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Un.</th>
                                <th>Observação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tab: Cotações --}}
                <div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
                    <div class="row mt-1 mb-1">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Fornecedor <span class="text-danger">*</span></small></label>
                                <select class="form-control form-control-sm select2-ajax" id="cd_fornecedor" style="width:100%"
                                    data-url="{{ route('compras.search-fornecedor') }}"
                                    data-placeholder="Buscar fornecedor (mín. 3 caracteres)"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Prazo (dias) <span class="text-danger">*</span></small></label>
                                <input type="number" class="form-control form-control-sm" id="nr_prazo" min="1" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Condição de Pagamento <span class="text-danger">*</span></small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_condicao" maxlength="200" placeholder="Ex: 30 dias">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Valor Total (R$) <span class="text-danger">*</span></small></label>
                                <input type="text" class="form-control form-control-sm money-mask" id="vl_total_cot" placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Obs.</small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_obs_cot" maxlength="500">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <button id="btn-add-cot" class="btn btn-info btn-sm">
                                <i class="fas fa-plus"></i> Adicionar Cotação
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered compact table-font-small" id="table-cotacoes" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fornecedor</th>
                                <th>Prazo</th>
                                <th>Condição</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tab: Fornecedor Selecionado --}}
                <div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
                    <div class="row mt-1">
                        <div class="col-md-5">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Cotação Vencedora <span class="text-danger">*</span></small></label>
                                <select class="form-control form-control-sm select2" id="sel_id_cotacao" style="width:100%">
                                    <option value="">Selecione</option>
                                    @foreach($cotacoes ?? [] as $c)
                                        <option value="{{ $c->ID_COTACAO }}"
                                            {{ $c->ST_SELECIONADA === 'S' ? 'selected' : '' }}>
                                            {{ $c->NM_FORNECEDOR }} — R$ {{ number_format($c->VL_TOTAL, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group mb-2">
                                <label class="mb-1"><small>Motivo da Escolha <span class="text-danger">*</span></small></label>
                                <input type="text" class="form-control form-control-sm" id="ds_motivo_escolha" maxlength="500"
                                    value="{{ isset($cotacoes) ? collect($cotacoes)->firstWhere('ST_SELECIONADA', 'S')->DS_MOTIVO_ESCOLHA ?? '' : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            <button id="btn-selecionar-forn" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Confirmar Seleção
                            </button>
                        </div>
                    </div>
                </div>

                @endif

            </div>
        </div>
    </div>

</section>

{{-- Modal editar cotação --}}
<div class="modal fade" id="modal-edit-cot">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Cotação</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id_cotacao">
                <input type="hidden" id="edit_id_sol_cot">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Fornecedor</small></label>
                    <select class="form-control form-control-sm select2-ajax" id="edit_cd_fornecedor" style="width:100%"
                        data-url="{{ route('compras.search-fornecedor') }}"
                        data-placeholder="Buscar fornecedor"></select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Prazo (dias)</small></label>
                    <input type="number" class="form-control form-control-sm" id="edit_nr_prazo" min="1">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Condição de Pagamento</small></label>
                    <input type="text" class="form-control form-control-sm" id="edit_ds_condicao" maxlength="200">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Valor Total (R$)</small></label>
                    <input type="text" class="form-control form-control-sm money-mask" id="edit_vl_total">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Observação</small></label>
                    <input type="text" class="form-control form-control-sm" id="edit_ds_obs" maxlength="500">
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-save-edit-cot" class="btn btn-warning">Salvar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function () {

    const idSolicitacao = $('#id_solicitacao').val();
    const token = $('[name=csrf-token]').attr('content');

    // Ajusta colunas do DataTable ao trocar de tab
    $('a[data-toggle="pill"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // Select2 com AJAX
    function initSelect2Ajax(selector) {
        $(selector).each(function () {
            const url = $(this).data('url');
            const placeholder = $(this).data('placeholder') || 'Buscar...';
            $(this).select2({
                theme: 'bootstrap4',
                placeholder: placeholder,
                minimumInputLength: 3,
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 300,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id:   item.id   ?? item.ID,
                            text: item.text ?? item.TEXT
                        }))
                    }),
                    cache: true
                }
            });
        });
    }

    initSelect2Ajax('.select2-ajax');
    $('.form-control.select2:not(.select2-ajax)').select2({ theme: 'bootstrap4' });

    // Máscara monetária simples
    function toFloat(val) {
        return parseFloat(val.replace(/\./g, '').replace(',', '.')) || 0;
    }

    // Exibe erros de validação Laravel (HTTP 422) no Swal
    function handleValidationError(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs });
        }
    }

    // ---- Cabeçalho ----
    @if(!$idSolicitacao)
    $('#btn-salvar').click(function () {
        $.post('{{ route('compras.solicitacoes.store') }}', {
            _token: token,
            cd_empresa:       $('#cd_empresa').val(),
            dt_solicitacao:   $('#dt_solicitacao').val(),
            ds_justificativa: $('#ds_justificativa').val(),
            ds_observacao:    $('#ds_observacao').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire('Criada!', res.success, 'success').then(() => {
                    window.location.href = '/compras/solicitacoes/' + res.id + '/editar';
                });
            }
        });
    });
    @else
    $('#btn-atualizar').click(function () {
        $.post('{{ route('compras.solicitacoes.update', $idSolicitacao) }}', {
            _token: token,
            cd_empresa:       $('#cd_empresa').val(),
            dt_solicitacao:   $('#dt_solicitacao').val(),
            ds_justificativa: $('#ds_justificativa').val(),
            ds_observacao:    $('#ds_observacao').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire('Atualizado!', res.success, 'success');
            }
        });
    });

    // ---- DataTable Itens ----
    const dtItens = $('#table-itens').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('compras.itens.list', $idSolicitacao) }}',
        columns: [
            { data: 'CD_ITEM',       name: 'CD_ITEM',       width: '70px' },
            { data: 'DS_ITEM',       name: 'DS_ITEM' },
            { data: 'QT_ITEM',       name: 'QT_ITEM',       width: '80px' },
            { data: 'DS_UNIDADE',    name: 'DS_UNIDADE',    width: '60px' },
            { data: 'DS_OBSERVACAO', name: 'DS_OBSERVACAO' },
            { data: 'Actions',       name: 'Actions', orderable: false, searchable: false, width: '80px' },
        ],
        pageLength: 10,
        language: { url: '{{ asset('vendor/datatables/pt-br.json') }}' },
    });

    $('#btn-add-item').click(function () {
        $.post('{{ route('compras.itens.store') }}', {
            _token:         token,
            id_solicitacao: idSolicitacao,
            cd_item:        $('#cd_item').val(),
            qt_item:        $('#qt_item').val(),
            ds_unidade:     $('#ds_unidade').val(),
            ds_observacao:  $('#ds_obs_item').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                dtItens.ajax.reload();
                $('#cd_item').val(null).trigger('change');
                $('#qt_item, #ds_unidade, #ds_obs_item').val('');
            }
        }).fail(handleValidationError);
    });

    $('body').on('click', '.btn-delete-item', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Remover item?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Remover',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '/compras/itens/' + id,
                method: 'DELETE',
                data: { _token: token },
                success: res => {
                    if (res.errors) Swal.fire('Erro', res.errors, 'error');
                    else dtItens.ajax.reload();
                }
            });
        });
    });

    // Recarrega o select de cotação vencedora sem recarregar a página
    function reloadCotacoesSelect() {
        $.get('{{ route('compras.cotacoes.list', $idSolicitacao) }}', function (res) {
            const sel = $('#sel_id_cotacao');
            sel.empty().append('<option value="">Selecione</option>');
            (res.data || []).forEach(function (c) {
                const selected = c.ST_SELECIONADA === 'S' ? 'selected' : '';
                sel.append(`<option value="${c.ID_COTACAO}" ${selected}>${c.NM_FORNECEDOR} — ${c.vl_total_fmt}</option>`);
            });
            sel.trigger('change');
        });
    }

    // ---- DataTable Cotações ----
    const dtCotacoes = $('#table-cotacoes').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route('compras.cotacoes.list', $idSolicitacao) }}',
            beforeSend: function () {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            complete: function () {
                Swal.close();
            }
        },
        columns: [
            { data: 'NM_FORNECEDOR',          name: 'NM_FORNECEDOR' },
            { data: 'NR_PRAZO_ENTREGA',        name: 'NR_PRAZO_ENTREGA',        width: '80px' },
            { data: 'DS_CONDICAO_PAGAMENTO',   name: 'DS_CONDICAO_PAGAMENTO' },
            { data: 'vl_total_fmt',            name: 'vl_total_fmt',            orderable: false, width: '110px' },
            { data: 'selecionada_badge',        name: 'selecionada_badge',       orderable: false, width: '110px' },
            { data: 'Actions',                 name: 'Actions', orderable: false, searchable: false, width: '150px' },
        ],
        pageLength: 10,
        language: { url: '{{ asset('vendor/datatables/pt-br.json') }}' },
    });

    $('#btn-add-cot').click(function () {
        $.post('{{ route('compras.cotacoes.store') }}', {
            _token:                token,
            id_solicitacao:        idSolicitacao,
            cd_fornecedor:         $('#cd_fornecedor').val(),
            nr_prazo_entrega:      $('#nr_prazo').val(),
            ds_condicao_pagamento: $('#ds_condicao').val(),
            vl_total:              toFloat($('#vl_total_cot').val()),
            ds_observacao:         $('#ds_obs_cot').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                dtCotacoes.ajax.reload();
                reloadCotacoesSelect();
                $('#cd_fornecedor').val(null).trigger('change');
                $('#nr_prazo, #ds_condicao, #vl_total_cot, #ds_obs_cot').val('');
            }
        }).fail(handleValidationError);
    });

    // Editar cotação
    $('body').on('click', '.btn-edit-cot', function () {
        const btn = $(this);
        $('#edit_id_cotacao').val(btn.data('id'));
        $('#edit_id_sol_cot').val(btn.data('sol'));
        $('#edit_nr_prazo').val(btn.data('prazo'));
        $('#edit_ds_condicao').val(btn.data('cond'));
        $('#edit_vl_total').val(String(btn.data('vl')).replace('.', ','));
        $('#edit_ds_obs').val(btn.data('obs'));

        const fornecedorOption = new Option(btn.data('nm'), btn.data('fornecedor'), true, true);
        $('#edit_cd_fornecedor').append(fornecedorOption).trigger('change');

        $('#modal-edit-cot').modal('show');
    });

    $('#btn-save-edit-cot').click(function () {
        const id = $('#edit_id_cotacao').val();
        $.post('/compras/cotacoes/' + id + '/update', {
            _token:                token,
            id_solicitacao:        $('#edit_id_sol_cot').val(),
            cd_fornecedor:         $('#edit_cd_fornecedor').val(),
            nr_prazo_entrega:      $('#edit_nr_prazo').val(),
            ds_condicao_pagamento: $('#edit_ds_condicao').val(),
            vl_total:              toFloat($('#edit_vl_total').val()),
            ds_observacao:         $('#edit_ds_obs').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                $('#modal-edit-cot').modal('hide');
                dtCotacoes.ajax.reload();
                reloadCotacoesSelect();
            }
        });
    });

    // Remover cotação
    $('body').on('click', '.btn-delete-cot', function () {
        const id  = $(this).data('id');
        const sol = $(this).data('sol');
        Swal.fire({
            title: 'Remover cotação?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Remover',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '/compras/cotacoes/' + id,
                method: 'DELETE',
                data: { _token: token, id_solicitacao: sol },
                success: res => {
                    if (res.errors) Swal.fire('Erro', res.errors, 'error');
                    else { dtCotacoes.ajax.reload(); reloadCotacoesSelect(); }
                }
            });
        });
    });

    // ---- Selecionar Fornecedor ----
    $('#btn-selecionar-forn').click(function () {
        $.post('{{ route('compras.cotacoes.selecionar') }}', {
            _token:         token,
            id_solicitacao: idSolicitacao,
            id_cotacao:     $('#sel_id_cotacao').val(),
            ds_motivo:      $('#ds_motivo_escolha').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire('Selecionado!', res.success, 'success').then(() => {
                    dtCotacoes.ajax.reload();
                    reloadCotacoesSelect();
                });
            }
        }).fail(handleValidationError);
    });

    // ---- Submeter ----
    $('#btn-submeter').click(function () {
        Swal.fire({
            title: 'Enviar para aprovação?',
            text: 'Após enviada, a solicitação não poderá ser editada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sim, enviar',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route('compras.solicitacoes.submeter', $idSolicitacao) }}', {
                _token: token
            }, function (res) {
                if (res.errors) {
                    Swal.fire('Atenção', res.errors, 'warning');
                } else {
                    Swal.fire('Enviada com sucesso!', res.success, 'success').then(() => {
                        window.location.href = '{{ route('compras.solicitacoes.show', $idSolicitacao) }}';
                    });
                }
            });
        });
    });

    @endif

    // Select2 modal
    initSelect2Ajax('#edit_cd_fornecedor');
    $('#sel_id_cotacao').select2({ theme: 'bootstrap4' });

});
</script>
@stop
