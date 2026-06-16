@extends('layouts.master')

@section('title', 'Configuração de Aprovações')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-danger card-outline card-outline-tabs">

                    {{-- Nav Tabs --}}
                    <div class="card-header p-0 d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs border-bottom-0" id="tabs-config" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-faixas" data-toggle="pill" href="#pane-faixas"
                                    role="tab">
                                    <i class="fas fa-layer-group mr-1"></i> Faixas de Aprovação
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-centro" data-toggle="pill" href="#pane-centro" role="tab">
                                    <i class="fas fa-chart-bar mr-1"></i> Centro de Resultado
                                </a>
                            </li>
                        </ul>
                        <div class="card-tools mr-2">
                            <button id="btn-nova-faixa" class="btn btn-danger btn-xs" data-toggle="modal"
                                data-target="#modal-faixa">
                                <i class="fas fa-plus"></i> Nova Faixa
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="card-body">
                        <div class="tab-content">

                            {{-- Tab: Faixas de Aprovação --}}
                            @include('admin.compras.configuracao.tabs.faixas')

                            {{-- Tab: Centro de Resultado --}}
                            @include('admin.compras.configuracao.tabs.centro-resultado')

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Modal Nova/Editar Faixa --}}
    @include('admin.compras.configuracao.modals.modal-faixa')

    {{-- Modal Aprovadores --}}
    @include('admin.compras.configuracao.modals.modal-aprovadores')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {

            const token = $('[name=csrf-token]').attr('content');

            // Oculta/exibe botão Nova Faixa conforme a tab ativa
            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                const isTabFaixas = $(e.target).attr('href') === '#pane-faixas';
                $('#btn-nova-faixa').toggle(isTabFaixas);
            });

            $('.form-control.select2').select2({
                theme: 'bootstrap4'
            });

            function toFloat(val) {
                return parseFloat((val || '0').replace(/\./g, '').replace(',', '.')) || 0;
            }

            // ---- DataTable Faixas ----
            const dt = $('#table-faixas').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('compras.configuracao.list-faixas') }}',
                columns: [{
                        data: 'NM_EMPRESA',
                        name: 'NM_EMPRESA'
                    },
                    {
                        data: 'DS_FAIXA',
                        name: 'DS_FAIXA'
                    },
                    {
                        data: 'NR_ORDEM',
                        name: 'NR_ORDEM',
                        width: '60px'
                    },
                    {
                        data: 'vl_minimo_fmt',
                        name: 'vl_minimo_fmt',
                        orderable: false
                    },
                    {
                        data: 'vl_maximo_fmt',
                        name: 'vl_maximo_fmt',
                        orderable: false
                    },
                    {
                        data: 'ativo_badge',
                        name: 'ativo_badge',
                        orderable: false,
                        width: '70px'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        orderable: false,
                        searchable: false,
                        width: '220px'
                    },
                ],
                pageLength: 20,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json'
                },
            });

            // Abrir modal nova faixa
            $('[data-target="#modal-faixa"]').click(function() {
                $('#modal-faixa-title').text('Nova Faixa');
                $('#faixa_id').val('');
                $('#faixa_cd_empresa, #faixa_ds, #faixa_ordem, #faixa_vl_min, #faixa_vl_max').val('');
                $('#div-st-ativo').hide();
            });

            // Editar faixa
            $('body').on('click', '.btn-edit-faixa', function() {
                const btn = $(this);
                $('#modal-faixa-title').text('Editar Faixa');
                $('#faixa_id').val(btn.data('id'));
                $('#faixa_cd_empresa').val(btn.data('empresa')).trigger('change');
                $('#faixa_ds').val(btn.data('ds'));
                $('#faixa_ordem').val(btn.data('ordem'));
                $('#faixa_vl_min').val(String(btn.data('min')).replace('.', ','));
                $('#faixa_vl_max').val(btn.data('max') ? String(btn.data('max')).replace('.', ',') : '');
                $('#faixa_st_ativo').val(btn.data('ativo'));
                $('#div-st-ativo').show();
                $('#modal-faixa').modal('show');
            });

            // Salvar faixa
            $('#btn-salvar-faixa').click(function() {
                const id = $('#faixa_id').val();
                const data = {
                    _token: token,
                    cd_empresa: $('#faixa_cd_empresa').val(),
                    ds_faixa: $('#faixa_ds').val(),
                    nr_ordem: $('#faixa_ordem').val(),
                    vl_minimo: toFloat($('#faixa_vl_min').val()),
                    vl_maximo: $('#faixa_vl_max').val() ? toFloat($('#faixa_vl_max').val()) : null,
                    st_ativo: $('#faixa_st_ativo').val() || 'S',
                };

                const url = id ?
                    '/compras/faixas/' + id + '/update' :
                    '{{ route('compras.configuracao.store-faixa') }}';

                $.post(url, data, function(res) {
                    if (res.errors) {
                        Swal.fire('Atenção', res.errors, 'warning');
                    } else {
                        $('#modal-faixa').modal('hide');
                        Swal.fire('Salvo!', res.success, 'success');
                        dt.ajax.reload();
                    }
                });
            });

            // Excluir faixa
            $('body').on('click', '.btn-delete-faixa', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Excluir faixa?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Excluir',
                    cancelButtonText: 'Cancelar',
                }).then(r => {
                    if (!r.isConfirmed) return;
                    $.ajax({
                        url: '/compras/faixas/' + id,
                        method: 'DELETE',
                        data: {
                            _token: token
                        },
                        success: res => {
                            if (res.errors) Swal.fire('Erro', res.errors, 'error');
                            else {
                                Swal.fire('Excluído!', res.success, 'success');
                                dt.ajax.reload();
                            }
                        }
                    });
                });
            });

            // ---- Gerenciar Aprovadores ----
            $('body').on('click', '.btn-aprovadores', function() {
                const id = $(this).data('id');
                const ds = $(this).data('ds');
                $('#aprov_id_faixa').val(id);
                $('#aprov-ds-faixa').text(ds);
                $('#aprov_ordem, #aprov_cargo').val('');
                $('#aprov_cd_usuario').val('').trigger('change');
                carregarAprovadores(id);
                $('#modal-aprovadores').modal('show');
            });

            let sortableOrdem = null;

            function carregarAprovadores(idFaixa) {
                $('#btn-salvar-ordem').hide();
                $.get('/compras/faixas/' + idFaixa + '/aprovadores', function(data) {
                    const tbody = $('#tbody-aprovadores');
                    tbody.empty();
                    if (!data.length) {
                        tbody.append('<tr><td colspan="5" class="text-center">Nenhum aprovador.</td></tr>');
                        if (sortableOrdem) {
                            sortableOrdem.destroy();
                            sortableOrdem = null;
                        }
                        return;
                    }
                    data.forEach(a => {
                        tbody.append(`
                    <tr data-id="${a.ID_CONFIG_APROV}">
                        <td class="text-center text-muted sortable-handle" style="cursor:grab">
                            <i class="fas fa-grip-vertical"></i>
                        </td>
                        <td>${a.NR_ORDEM}</td>
                        <td>${a.DS_CARGO}</td>
                        <td>${a.NM_APROVADOR}</td>
                        <td>
                            <button data-id="${a.ID_CONFIG_APROV}" class="btn btn-danger btn-xs btn-del-aprov" title="Remover">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
                    });
                    if (sortableOrdem) sortableOrdem.destroy();
                    sortableOrdem = new Sortable(tbody[0], {
                        handle: '.sortable-handle',
                        animation: 150,
                        onEnd: function() {
                            $('#btn-salvar-ordem').show();
                        }
                    });
                });
            }

            $('#btn-salvar-ordem').click(function() {
                const ids = [];
                $('#tbody-aprovadores tr[data-id]').each(function() {
                    ids.push($(this).data('id'));
                });
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                $.post('{{ route('compras.configuracao.reordenar-aprovadores') }}', {
                    _token: token,
                    ids: ids,
                }, function(res) {
                    if (res.errors) {
                        Swal.fire('Atenção', res.errors, 'warning');
                    } else {
                        $('#btn-salvar-ordem').hide();
                        carregarAprovadores($('#aprov_id_faixa').val());
                        Swal.fire('Salvo!', res.success, 'success');
                    }
                });
            });

            $('#btn-add-aprov').click(function() {
                const idFaixa = $('#aprov_id_faixa').val();
                const nmAprovador = $('#aprov_cd_usuario option:selected').text().trim();
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                $.post('{{ route('compras.configuracao.store-aprovador') }}', {
                    _token: token,
                    id_faixa: idFaixa,
                    nr_ordem: $('#aprov_ordem').val(),
                    ds_cargo: $('#aprov_cargo').val(),
                    cd_usuario: $('#aprov_cd_usuario').val(),
                    nm_aprovador: nmAprovador,
                }, function(res) {
                    if (res.errors) {
                        Swal.fire('Atenção', res.errors, 'warning');
                    } else {
                        Swal.close();
                        $('#aprov_ordem, #aprov_cargo').val('');
                        $('#aprov_cd_usuario').val('').trigger('change');
                        carregarAprovadores(idFaixa);
                    }
                }).fail(function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            html: msgs
                        });
                    }
                });
            });

            $('body').on('click', '.btn-del-aprov', function() {
                const id = $(this).data('id');
                const idFaixa = $('#aprov_id_faixa').val();
                Swal.fire({
                    title: 'Remover aprovador?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Remover',
                    cancelButtonText: 'Cancelar',
                }).then(r => {
                    if (!r.isConfirmed) return;
                    Swal.fire({
                        title: 'Removendo...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                    $.ajax({
                        url: '/compras/aprovadores/' + id,
                        method: 'DELETE',
                        data: {
                            _token: token
                        },
                        success: res => {
                            if (res.errors) {
                                Swal.fire('Erro', res.errors, 'error');
                            } else {
                                Swal.close();
                                carregarAprovadores(idFaixa);
                            }
                        }
                    });
                });
            });

            $('#aprov_cd_usuario').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modal-aprovadores')
            });

        });
    </script>
@stop
