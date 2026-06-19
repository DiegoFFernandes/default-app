@extends('layouts.master')

@section('title', $title_page)

@section('content')
<section class="content">
    @php
        $idSolicitacao = $solicitacao->CD_SOLICITACAO ?? null;

        $statusMap = [
            'RAS' => ['secondary', 'Rascunho'],
            'ANA' => ['info',      'Em Análise de Compra'],
            'APR' => ['warning',   'Em Aprovação'],
            'APC' => ['success',   'Aprovada para Compra'],
            'REP' => ['danger',    'Reprovada'],
            'CAN' => ['dark',      'Cancelada'],
        ];

        if ($solicitacao) {
            [$cor, $label] = $statusMap[$solicitacao->ST_SOLICITACAO] ?? ['secondary', $solicitacao->ST_SOLICITACAO];
        } else {
            $cor = 'danger'; $label = 'Nova Solicitação';
        }

        $cotacaoSelecionada = collect($cotacoes ?? [])->firstWhere('ST_SELECIONADA', 'S');

        $isSolicitante = auth()->user()->can('solicitacao-compra-criar')
            && !auth()->user()->can('solicitacao-compra-gerenciar')
            && !auth()->user()->can('solicitacao-compra-aprovar');
    @endphp

    <input type="hidden" id="id_solicitacao" value="{{ $idSolicitacao }}">

    <div class="card card-{{ $cor }} card-outline card-outline-tabs">

        {{-- Nav Tabs --}}
        <div class="card-header p-0 d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs border-bottom-0" id="tabs-solicitacao" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-cabecalho" data-toggle="pill" href="#pane-cabecalho" role="tab">
                        <i class="fas fa-info-circle mr-1"></i> Cabeçalho
                    </a>
                </li>
                @if($isSolicitante)
                    {{-- Solicitante: tab-itens sempre visível, bloqueada até salvar cabeçalho --}}
                    <li class="nav-item">
                        @if($idSolicitacao)
                            <a class="nav-link" id="tab-itens" data-toggle="pill" href="#pane-itens" role="tab">
                                <i class="fas fa-list mr-1"></i> Itens
                                <span class="badge badge-secondary ml-1">{{ count($itens) }}</span>
                            </a>
                        @else
                            <a class="nav-link tab-disabled" id="tab-itens" role="tab"
                               title="Salve o cabeçalho primeiro para adicionar itens">
                                <i class="fas fa-lock mr-1"></i> Itens
                            </a>
                        @endif
                    </li>
                @else
                    @if($idSolicitacao)
                    <li class="nav-item">
                        <a class="nav-link" id="tab-itens" data-toggle="pill" href="#pane-itens" role="tab">
                            <i class="fas fa-list mr-1"></i> Itens
                            <span class="badge badge-secondary ml-1">{{ count($itens) }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-cotacoes" data-toggle="pill" href="#pane-cotacoes" role="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Cotações
                            <span class="badge badge-secondary ml-1">{{ count($cotacoes) }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-fornecedor" data-toggle="pill" href="#pane-fornecedor" role="tab">
                            <i class="fas fa-handshake mr-1"></i> Fornecedor Compra
                            @if($cotacaoSelecionada)
                                <span class="badge badge-success ml-1"><i class="fas fa-check"></i></span>
                            @endif
                        </a>
                    </li>
                    @endif
                @endif
            </ul>

            <div class="card-tools mr-2">
                @if($idSolicitacao)
                    <span class="badge badge-{{ $cor }} mr-2">#{{ $idSolicitacao }} — {{ $label }}</span>
                    @if($solicitacao->ST_SOLICITACAO === 'RAS')
                        <button id="btn-cancelar" class="btn btn-danger btn-xs mr-1">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    @endif
                    @if(in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))
                        <button id="btn-cancelar-sol" class="btn btn-dark btn-xs mr-1">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                    @endif
                    <a id="btn-exportar-excel"
                        href="{{ route('compras.solicitacoes.exportar-excel', $idSolicitacao) }}"
                        class="btn btn-success btn-xs mr-1">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <button id="btn-imprimir" class="btn btn-secondary btn-xs mr-1">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                @endif
                <a href="{{ route('compras.solicitacoes.index') }}" class="btn btn-default btn-xs">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="card-body">
            <div class="tab-content" id="tabs-solicitacao-content">

                {{-- Tab: Cabeçalho --}}
                @include('admin.compras.solicitacoes.show.tabs.cabecalho')

                @if($idSolicitacao)

                {{-- Tab: Itens --}}
                @include('admin.compras.solicitacoes.show.tabs.itens')

                @if(!$isSolicitante)
                {{-- Tab: Cotações --}}
                @include('admin.compras.solicitacoes.show.tabs.cotacoes')

                {{-- Tab: Fornecedor Selecionado --}}
                @include('admin.compras.solicitacoes.show.tabs.fornecedor')
                @endif

                @endif

            </div>
        </div>
    </div>

    @if($idSolicitacao)
        {{-- Modais (apenas quando solicitação existe) --}}
        @if($solicitacao->ST_SOLICITACAO === 'RAS')
            @include('admin.compras.solicitacoes.show.modals.modal-edit-item')
            @include('admin.compras.solicitacoes.show.modals.modal-edit-cotacao')
        @endif

        {{-- Área de Impressão --}}
        @include('admin.compras.solicitacoes.show.prints.solicitacao-compra')
    @endif

</section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
    <style>
        .tab-disabled {
            opacity: 0.45;
            cursor: not-allowed !important;
            pointer-events: none;
            color: #6c757d !important;
        }
        #print-area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area {
                display: block !important;
                position: absolute;
                top: 0; left: 0; width: 100%;
            }
            @page { margin: 1.5cm 1.5cm 1.5cm 0.5cm; size: A4 portrait; }
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function () {

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'itens') {
        $('#tab-itens').tab('show');
        history.replaceState(null, '', window.location.pathname);
    }

    const idSolicitacao       = $('#id_solicitacao').val() || null;
    const token               = $('[name=csrf-token]').attr('content');
    const paramUsaCentrocusto = @json($paramUsaCentrocusto ?? []);
    const currentCentroCusto  = @json($solicitacao->CD_CENTROCUSTO ?? null);

    // -------------------------------------------------------
    // Utilitários
    // -------------------------------------------------------

    function toFloat(val) {
        return parseFloat(String(val).replace(/\./g, '').replace(',', '.')) || 0;
    }

    function handleValidationError(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs });
        }
    }

    // -------------------------------------------------------
    // Select2
    // -------------------------------------------------------

    function initSelect2Ajax(selector) {
        $(selector).each(function () {
            const url         = $(this).data('url');
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

    $('a[data-toggle="pill"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // -------------------------------------------------------
    // Centro de Custo / Saldo (novo ou rascunho)
    // -------------------------------------------------------

    @if(!$idSolicitacao || $solicitacao->ST_SOLICITACAO === 'RAS')

    function loadCentrosCusto(cdEmpresa, preSelected) {
        $.getJSON('{{ route('compras.centros.by-empresa') }}', { cd_empresa: cdEmpresa }, function (data) {
            const sel = $('#cd_centrocusto');
            sel.empty().append('<option value="">Nenhum</option>');
            $.each(data, function (_, c) {
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

    function loadSaldoCiclo(cdEmpresa, cdCentrocusto) {
        $('#div-saldo-ciclo').hide();
        if (!cdEmpresa || !cdCentrocusto) return;

        $.getJSON('{{ route('compras.saldo-ciclo') }}', {
            cd_empresa:     cdEmpresa,
            cd_centrocusto: cdCentrocusto,
        }, function (data) {
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

    $('#cd_empresa').on('change', function () {
        $('#div-saldo-ciclo').hide();
        toggleCentroCusto($(this).val(), null);
    });

    $('#cd_centrocusto').on('change', function () {
        loadSaldoCiclo($('#cd_empresa').val(), $(this).val());
    });

    toggleCentroCusto($('#cd_empresa').val(), currentCentroCusto);

    $('#cd_item').on('select2:select', function (e) {
        const sg = e.params.data.SG_UNIDMED;
        if (sg) $('#ds_unidade').val(sg);
    });

    @endif

    // -------------------------------------------------------
    // Cabeçalho — Salvar nova / Atualizar rascunho
    // -------------------------------------------------------

    @if(!$idSolicitacao)

    $('#btn-salvar').click(function () {
        const cdEmpresa = $('#cd_empresa').val();
        if (paramUsaCentrocusto[cdEmpresa] === 'S' && !$('#cd_centrocusto').val()) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'O Centro de Resultado é obrigatório para esta empresa.', confirmButtonColor: '#dc3545' });
            return;
        }
        $.post('{{ route('compras.solicitacoes.store') }}', {
            _token:           token,
            cd_empresa:       cdEmpresa,
            dt_solicitacao:   $('#dt_solicitacao').val(),
            ds_justificativa: $('#ds_justificativa').val(),
            ds_observacao:    $('#ds_observacao').val(),
            cd_centrocusto:   $('#cd_centrocusto').val() || null,
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Criada!',
                    text: res.success,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.href = '/compras/solicitacoes/' + res.id + '?tab=itens';
                });
            }
        }).fail(handleValidationError);
    });

    @else

    // -------------------------------------------------------
    // Solicitação existente
    // -------------------------------------------------------

    const qtdItens = {{ count($itens ?? []) }};

    $('#btn-imprimir').click(function () {
        if (qtdItens === 0) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'A solicitação não possui itens para imprimir.', confirmButtonColor: '#dc3545' });
            return;
        }
        window.print();
    });

    $('#btn-exportar-excel').on('click', function (e) {
        if (qtdItens === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'A solicitação não possui itens para exportar.', confirmButtonColor: '#dc3545' });
        }
    });

    @if($solicitacao->ST_SOLICITACAO === 'RAS')

    // -------------------------------------------------------
    // Ações de Rascunho
    // -------------------------------------------------------

    $('#btn-atualizar').click(function () {
        const cdEmpresa = $('#cd_empresa').val();
        if (paramUsaCentrocusto[cdEmpresa] === 'S' && !$('#cd_centrocusto').val()) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'O Centro de Resultado é obrigatório para esta empresa.', confirmButtonColor: '#dc3545' });
            return;
        }
        $.post('{{ route('compras.solicitacoes.update', $idSolicitacao) }}', {
            _token:           token,
            cd_empresa:       cdEmpresa,
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

    $('#btn-submeter').click(function () {
        Swal.fire({
            title: 'Enviar para aprovação?',
            text: 'Após enviada, a solicitação não poderá ser editada, e os responsáveis serão notificados!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sim, enviar',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            Swal.fire({ title: 'Enviando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
            $.post('{{ route('compras.solicitacoes.submeter', $idSolicitacao) }}', { _token: token }, function (res) {
                if (res.errors) {
                    Swal.fire('Atenção', res.errors, 'warning');
                } else {
                    Swal.fire('Enviada!', res.success, 'success').then(() => window.location.reload());
                }
            });
        });
    });

    $('#btn-cancelar').click(function () {
        Swal.fire({
            title: 'Excluir rascunho?',
            text: 'A solicitação será excluída permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Não',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '/compras/solicitacoes/{{ $idSolicitacao }}',
                method: 'DELETE',
                data: { _token: token },
                success: function (res) {
                    if (res.errors) Swal.fire('Erro', res.errors, 'error');
                    else Swal.fire('Excluído!', res.success, 'success').then(() => {
                        window.location.href = '{{ route('compras.solicitacoes.index') }}';
                    });
                }
            });
        });
    });

    $('#btn-enviar-solicitacao-analise-compra').click(function () {
        if (dtItens.data().count() === 0) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Adicione pelo menos um item antes de enviar para análise.', confirmButtonColor: '#dc3545' });
            return;
        }
        Swal.fire({
            title: 'Enviar para análise?',
            text: 'A solicitação será encaminhada para análise de compra. Não será mais possível editar os itens.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            confirmButtonText: 'Sim, enviar',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route('compras.solicitacoes.enviar-analise', $idSolicitacao) }}', { _token: token }, function (res) {
                if (res.errors) Swal.fire('Erro', res.errors, 'error');
                else Swal.fire('Enviado!', res.success, 'success').then(() => window.location.reload());
            });
        });
    });

    // -------------------------------------------------------
    // DataTable — Itens
    // -------------------------------------------------------

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
            { data: 'Actions',       name: 'Actions', orderable: false, searchable: false, width: '90px' },
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
            title: 'Remover item?', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Remover', cancelButtonText: 'Cancelar',
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

    // -- Modal: Editar Item --

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
        $('#ei_cd_item').empty().append(new Option(btn.data('ds'), btn.data('cd'), true, true)).trigger('change');
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
                if (res.errors) Swal.fire('Atenção', res.errors, 'warning');
                else { $('#modal-edit-item').modal('hide'); dtItens.ajax.reload(null, false); }
            }
        }).fail(handleValidationError);
    });

    // -------------------------------------------------------
    // DataTable — Cotações
    // -------------------------------------------------------

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

    const dtCotacoes = $('#table-cotacoes').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route('compras.cotacoes.list', $idSolicitacao) }}',
            beforeSend: function () { Swal.fire({ title: 'Carregando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() }); },
            complete:   function () { Swal.close(); }
        },
        columns: [
            { data: 'NM_FORNECEDOR',        name: 'NM_FORNECEDOR' },
            { data: 'NR_PRAZO_ENTREGA',     name: 'NR_PRAZO_ENTREGA',     width: '80px' },
            { data: 'DS_CONDICAO_PAGAMENTO',name: 'DS_CONDICAO_PAGAMENTO' },
            { data: 'vl_total_fmt',         name: 'vl_total_fmt',         orderable: false, width: '110px' },
            { data: 'selecionada_badge',    name: 'selecionada_badge',    orderable: false, width: '110px' },
            { data: 'Actions',              name: 'Actions', orderable: false, searchable: false, width: '150px' },
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

    $('body').on('click', '.btn-edit-cot', function () {
        const btn = $(this);
        $('#edit_id_cotacao').val(btn.data('id'));
        $('#edit_id_sol_cot').val(btn.data('sol'));
        $('#edit_nr_prazo').val(btn.data('prazo'));
        $('#edit_ds_condicao').val(btn.data('cond'));
        $('#edit_vl_total').val(String(btn.data('vl')).replace('.', ','));
        $('#edit_ds_obs').val(btn.data('obs'));
        $('#edit_cd_fornecedor').empty().append(new Option(btn.data('nm'), btn.data('fornecedor'), true, true)).trigger('change');
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
            if (res.errors) Swal.fire('Atenção', res.errors, 'warning');
            else { $('#modal-edit-cot').modal('hide'); dtCotacoes.ajax.reload(); reloadCotacoesSelect(); }
        });
    });

    $('body').on('click', '.btn-delete-cot', function () {
        const id  = $(this).data('id');
        const sol = $(this).data('sol');
        Swal.fire({
            title: 'Remover cotação?', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Remover', cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '/compras/cotacoes/' + id, method: 'DELETE',
                data: { _token: token, id_solicitacao: sol },
                success: res => {
                    if (res.errors) Swal.fire('Erro', res.errors, 'error');
                    else { dtCotacoes.ajax.reload(); reloadCotacoesSelect(); }
                }
            });
        });
    });

    // -------------------------------------------------------
    // Fornecedor Selecionado
    // -------------------------------------------------------

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

    // -- Select2 dos modais --
    (function () {
        const el = document.getElementById('edit_cd_fornecedor');
        if (!el) return;
        $('#edit_cd_fornecedor').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modal-edit-cot'),
            placeholder: $(el).data('placeholder') || 'Buscar...',
            minimumInputLength: 3,
            ajax: {
                url: $(el).data('url'),
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(item => ({ id: item.id ?? item.ID, text: item.text ?? item.TEXT }))
                }),
                cache: true
            }
        });
    })();
    $('#sel_id_cotacao').select2({ theme: 'bootstrap4' });

    @endif {{-- RAS --}}

    @if(in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))

    $('#btn-cancelar-sol').click(function () {
        Swal.fire({
            title: 'Cancelar solicitação?',
            text: 'O status será alterado para Cancelada e o valor será liberado no saldo do ciclo.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#343a40',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route('compras.solicitacoes.cancelar', $idSolicitacao) }}', { _token: token }, function (res) {
                if (res.errors) Swal.fire('Erro', res.errors, 'error');
                else Swal.fire('Cancelada!', res.success, 'success').then(() => window.location.reload());
            });
        });
    });

    @endif

    @endif {{-- $idSolicitacao --}}

});
</script>
@stop
