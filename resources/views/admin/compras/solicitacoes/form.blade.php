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
                    <button id="btn-submeter" class="btn btn-primary btn-xs mr-1">
                        <i class="fas fa-paper-plane"></i> Enviar para Aprovação
                    </button>
                    <a id="btn-exportar-excel"
                        href="{{ route('compras.solicitacoes.exportar-excel', $idSolicitacao) }}"
                        class="btn btn-success btn-xs mr-1">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <button id="btn-imprimir" class="btn btn-secondary btn-xs mr-1">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="{{ route('compras.solicitacoes.show', $idSolicitacao) }}"
                        class="btn btn-info btn-xs mr-1">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <button id="btn-cancelar" class="btn btn-danger btn-xs mr-1">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
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
                @include('admin.compras.solicitacoes.form.tabs.cabecalho')

                @if($idSolicitacao)

                {{-- Tab: Itens --}}
                @include('admin.compras.solicitacoes.form.tabs.itens')

                {{-- Tab: Cotações --}}
                @include('admin.compras.solicitacoes.form.tabs.cotacoes')

                {{-- Tab: Fornecedor Selecionado --}}
                @include('admin.compras.solicitacoes.form.tabs.fornecedor')

                @endif

            </div>
        </div>
    </div>

</section>

{{-- Modal editar item --}}
@include('admin.compras.solicitacoes.form.modals.modal-edit-item')

{{-- Modal editar cotação --}}
@include('admin.compras.solicitacoes.form.modals.modal-edit-cotacao')

{{-- Área de Impressão --}}
@if($idSolicitacao)
    @include('admin.compras.solicitacoes.show.prints.solicitacao-compra')
@endif
@stop

@section('css')
    @if($idSolicitacao)
    <style>
        #print-area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #print-area,
            #print-area * { visibility: visible; }
            #print-area {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            @page { margin: 1.5cm 1.5cm 1.5cm 0.5cm; size: A4 portrait; }
        }
    </style>
    @endif
@stop

@section('js')
@if($idSolicitacao)
<script>
$(document).ready(function () {
    const qtdItens = {{ count($itens ?? []) }};

    $('#btn-imprimir').click(function () {
        if (qtdItens === 0) {
            Swal.fire('Atenção', 'A solicitação não possui itens para imprimir.', 'warning');
            return;
        }
        window.print();
    });

    $('#btn-exportar-excel').on('click', function (e) {
        if (qtdItens === 0) {
            e.preventDefault();
            Swal.fire('Atenção', 'A solicitação não possui itens para exportar.', 'warning');
        }
    });

    $('#btn-cancelar').click(function () {
        Swal.fire({
            title: 'Excluir rascunho?',
            text: 'A solicitação será excluída permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '/compras/solicitacoes/{{ $idSolicitacao }}',
                method: 'DELETE',
                data: { _token: $('[name=csrf-token]').attr('content') },
                success: function (res) {
                    if (res.errors) {
                        Swal.fire('Erro', res.errors, 'error');
                    } else {
                        Swal.fire('Cancelado!', res.success, 'success').then(() => {
                            window.location.href = '{{ route('compras.solicitacoes.index') }}';
                        });
                    }
                }
            });
        });
    });
});
</script>
@endif
<script>
$(document).ready(function () {

    const idSolicitacao = $('#id_solicitacao').val();
    const token = $('[name=csrf-token]').attr('content');
    const paramUsaCentrocusto  = @json($paramUsaCentrocusto ?? []);
    const currentCentroCusto   = @json($solicitacao->CD_CENTROCUSTO ?? null);

    function loadCentrosCusto(cdEmpresa, preSelected) {
        $.getJSON('{{ route('compras.centros.by-empresa') }}', { cd_empresa: cdEmpresa }, function(data) {
            const sel = $('#cd_centrocusto');
            sel.empty().append('<option value="">Nenhum</option>');
            $.each(data, function(_, c) {
                const opt = $('<option>', { value: c.CD_CENTROCUSTO, text: c.DS_CENTROCUSTO });
                if (preSelected && c.CD_CENTROCUSTO == preSelected) opt.prop('selected', true);
                sel.append(opt);
            });
            sel.trigger('change');
        });
    }

    function toggleCentroCusto(cdEmpresa, preSelected) {
        if (paramUsaCentrocusto[cdEmpresa] === 'S') {
            $('#div-centrocusto').show();
            loadCentrosCusto(cdEmpresa, preSelected);
        } else {
            $('#div-centrocusto').hide();
            $('#cd_centrocusto').empty().append('<option value="">Nenhum</option>').trigger('change');
        }
    }

    $('#cd_empresa').on('change', function () {
        $('#div-saldo-ciclo').hide();
        toggleCentroCusto($(this).val(), null);
    });

    toggleCentroCusto($('#cd_empresa').val(), currentCentroCusto);

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
                            ...item,
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

    $('#cd_item').on('select2:select', function (e) {
        const sg = e.params.data.SG_UNIDMED;
        if (sg) $('#ds_unidade').val(sg);
    });

    function loadSaldoCiclo(cdEmpresa, cdCentrocusto) {
        $('#div-saldo-ciclo').hide();
        if (!cdEmpresa || !cdCentrocusto) return;

        $.getJSON('{{ route('compras.saldo-ciclo') }}', {
            cd_empresa:     cdEmpresa,
            cd_centrocusto: cdCentrocusto,
        }, function(data) {
            if (!data || data.vl_orcado == null) return;

            $('#saldo-periodo').text(data.dt_inicio + ' a ' + data.dt_fim);
            $('#saldo-orcado').text('R$ ' + data.vl_orcado_fmt);
            $('#saldo-utilizado').text('R$ ' + data.vl_utilizado_fmt);
            const saldo = data.vl_saldo;
            $('#saldo-valor')
                .text((saldo < 0 ? '− ' : '') + 'R$ ' + data.vl_saldo_fmt)
                .removeClass('text-success text-danger')
                .addClass(saldo >= 0 ? 'text-success' : 'text-danger');
            $('#div-saldo-ciclo').show();
        });
    }

    $('#cd_centrocusto').on('change', function() {
        loadSaldoCiclo($('#cd_empresa').val(), $(this).val());
    });

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
            cd_centrocusto:   $('#cd_centrocusto').val() || null,
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire('Criada!', res.success, 'success').then(() => {
                    window.location.href = '/compras/solicitacoes/' + res.id + '/editar';
                });
            }
        }).fail(handleValidationError);
    });
    @else
    $('#btn-atualizar').click(function () {
        $.post('{{ route('compras.solicitacoes.update', $idSolicitacao) }}', {
            _token: token,
            cd_empresa:       $('#cd_empresa').val(),
            dt_solicitacao:   $('#dt_solicitacao').val(),
            ds_justificativa: $('#ds_justificativa').val(),
            ds_observacao:    $('#ds_observacao').val(),
            cd_centrocusto:   $('#cd_centrocusto').val() || null,
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire('Atualizado!', res.success, 'success');
            }
        }).fail(handleValidationError);
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

    // ---- Edição de Item ----
    $('#ei_cd_item').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modal-edit-item'),
        placeholder: 'Buscar produto (mín. 3 caracteres)',
        minimumInputLength: 3,
        ajax: {
            url: '{{ route('compras.search-item') }}',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({ id: item.id ?? item.ID, text: item.text ?? item.TEXT }))
            }),
            cache: true
        }
    });

    $('body').on('click', '.btn-edit-item', function () {
        const btn = $(this);
        $('#ei_id').val(btn.data('id'));
        $('#ei_qt_item').val(btn.data('qt'));
        $('#ei_ds_unidade').val(btn.data('un'));
        $('#ei_ds_observacao').val(btn.data('obs'));

        const option = new Option(btn.data('ds'), btn.data('cd'), true, true);
        $('#ei_cd_item').empty().append(option).trigger('change');

        $('#modal-edit-item').modal('show');
    });

    $('#btn-salvar-edit-item').click(function () {
        $.ajax({
            url: '/compras/itens/' + $('#ei_id').val(),
            method: 'PUT',
            data: {
                _token:        token,
                cd_item:       $('#ei_cd_item').val(),
                qt_item:       $('#ei_qt_item').val(),
                ds_unidade:    $('#ei_ds_unidade').val(),
                ds_observacao: $('#ei_ds_observacao').val(),
            },
            success: function (res) {
                if (res.errors) {
                    Swal.fire('Atenção', res.errors, 'warning');
                } else {
                    $('#modal-edit-item').modal('hide');
                    dtItens.ajax.reload(null, false);
                }
            }
        }).fail(handleValidationError);
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
