@extends('layouts.master')

@section('title', 'Configuração de Aprovações')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Faixas de Aprovação por Empresa</h3>
                    <div class="card-tools">
                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-faixa">
                            <i class="fas fa-plus"></i> Nova Faixa
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered compact table-font-small" id="table-faixas" style="width:100%">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Descrição</th>
                                <th>Ordem</th>
                                <th>Valor Mínimo</th>
                                <th>Valor Máximo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal Nova/Editar Faixa --}}
<div class="modal fade" id="modal-faixa">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-faixa-title">Nova Faixa</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faixa_id">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm select2" id="faixa_cd_empresa" style="width:100%">
                        <option value="">Selecione</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Descrição <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="faixa_ds" maxlength="100" placeholder="Ex: Faixa 1 — até R$ 1.000,00">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Ordem <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="faixa_ordem" min="1" placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Mínimo <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="faixa_vl_min" placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Máximo <span class="text-muted">(vazio = ilimitado)</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="faixa_vl_max" placeholder="0,00">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2" id="div-st-ativo" style="display:none">
                    <label class="mb-1"><small>Status</small></label>
                    <select class="form-control form-control-sm" id="faixa_st_ativo">
                        <option value="S">Ativo</option>
                        <option value="N">Inativo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-faixa" class="btn btn-danger btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Aprovadores --}}
<div class="modal fade" id="modal-aprovadores" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Aprovadores — <span id="aprov-ds-faixa"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="aprov_id_faixa">
                <div class="row mb-2">
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Ordem <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="aprov_ordem" min="1" placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Cargo <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="aprov_cargo" maxlength="100" placeholder="Ex: Supervisor">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Usuário Aprovador <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm select2" id="aprov_cd_usuario" style="width:100%">
                                <option value="">Selecione</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-2 w-100">
                            <button id="btn-add-aprov" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-font-small" id="table-aprovadores">
                    <thead>
                        <tr><th>Ordem</th><th>Cargo</th><th>Usuário</th><th>Ação</th></tr>
                    </thead>
                    <tbody id="tbody-aprovadores"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function () {

    const token = $('[name=csrf-token]').attr('content');

    $('.form-control.select2').select2({ theme: 'bootstrap4' });

    function toFloat(val) {
        return parseFloat((val || '0').replace(/\./g, '').replace(',', '.')) || 0;
    }

    // ---- DataTable Faixas ----
    const dt = $('#table-faixas').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('compras.configuracao.list-faixas') }}',
        columns: [
            { data: 'NM_EMPRESA',     name: 'NM_EMPRESA' },
            { data: 'DS_FAIXA',       name: 'DS_FAIXA' },
            { data: 'NR_ORDEM',       name: 'NR_ORDEM', width: '60px' },
            { data: 'vl_minimo_fmt',  name: 'vl_minimo_fmt', orderable: false },
            { data: 'vl_maximo_fmt',  name: 'vl_maximo_fmt', orderable: false },
            { data: 'ativo_badge',    name: 'ativo_badge', orderable: false, width: '70px' },
            { data: 'Actions',        name: 'Actions', orderable: false, searchable: false, width: '220px' },
        ],
        pageLength: 20,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json' },
    });

    // Abrir modal nova faixa
    $('[data-target="#modal-faixa"]').click(function () {
        $('#modal-faixa-title').text('Nova Faixa');
        $('#faixa_id').val('');
        $('#faixa_cd_empresa, #faixa_ds, #faixa_ordem, #faixa_vl_min, #faixa_vl_max').val('');
        $('#div-st-ativo').hide();
    });

    // Editar faixa
    $('body').on('click', '.btn-edit-faixa', function () {
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
    $('#btn-salvar-faixa').click(function () {
        const id = $('#faixa_id').val();
        const data = {
            _token:      token,
            cd_empresa:  $('#faixa_cd_empresa').val(),
            ds_faixa:    $('#faixa_ds').val(),
            nr_ordem:    $('#faixa_ordem').val(),
            vl_minimo:   toFloat($('#faixa_vl_min').val()),
            vl_maximo:   $('#faixa_vl_max').val() ? toFloat($('#faixa_vl_max').val()) : null,
            st_ativo:    $('#faixa_st_ativo').val() || 'S',
        };

        const url = id
            ? '/compras/faixas/' + id + '/update'
            : '{{ route('compras.configuracao.store-faixa') }}';

        $.post(url, data, function (res) {
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
    $('body').on('click', '.btn-delete-faixa', function () {
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
                data: { _token: token },
                success: res => {
                    if (res.errors) Swal.fire('Erro', res.errors, 'error');
                    else { Swal.fire('Excluído!', res.success, 'success'); dt.ajax.reload(); }
                }
            });
        });
    });

    // ---- Gerenciar Aprovadores ----
    $('body').on('click', '.btn-aprovadores', function () {
        const id = $(this).data('id');
        const ds = $(this).data('ds');
        $('#aprov_id_faixa').val(id);
        $('#aprov-ds-faixa').text(ds);
        $('#aprov_ordem, #aprov_cargo').val('');
        $('#aprov_cd_usuario').val('').trigger('change');
        carregarAprovadores(id);
        $('#modal-aprovadores').modal('show');
    });

    function carregarAprovadores(idFaixa) {
        $.get('/compras/faixas/' + idFaixa + '/aprovadores', function (data) {
            const tbody = $('#tbody-aprovadores');
            tbody.empty();
            if (!data.length) {
                tbody.append('<tr><td colspan="4" class="text-center">Nenhum aprovador.</td></tr>');
                return;
            }
            data.forEach(a => {
                tbody.append(`
                    <tr>
                        <td>${a.NR_ORDEM}</td>
                        <td>${a.DS_CARGO}</td>
                        <td>${a.NM_APROVADOR}</td>
                        <td>
                            <button data-id="${a.ID_CONFIG_APROV}" class="btn btn-danger btn-xs btn-del-aprov">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        });
    }

    $('#btn-add-aprov').click(function () {
        const idFaixa     = $('#aprov_id_faixa').val();
        const nmAprovador = $('#aprov_cd_usuario option:selected').text().trim();
        Swal.fire({ title: 'Salvando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        $.post('{{ route('compras.configuracao.store-aprovador') }}', {
            _token:        token,
            id_faixa:      idFaixa,
            nr_ordem:      $('#aprov_ordem').val(),
            ds_cargo:      $('#aprov_cargo').val(),
            cd_usuario:    $('#aprov_cd_usuario').val(),
            nm_aprovador:  nmAprovador,
        }, function (res) {
            if (res.errors) {
                Swal.fire('Atenção', res.errors, 'warning');
            } else {
                Swal.close();
                $('#aprov_ordem, #aprov_cargo').val('');
                $('#aprov_cd_usuario').val('').trigger('change');
                carregarAprovadores(idFaixa);
            }
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs });
            }
        });
    });

    $('body').on('click', '.btn-del-aprov', function () {
        const id      = $(this).data('id');
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
            Swal.fire({ title: 'Removendo...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
            $.ajax({
                url: '/compras/aprovadores/' + id,
                method: 'DELETE',
                data: { _token: token },
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

    $('#aprov_cd_usuario').select2({ theme: 'bootstrap4', dropdownParent: $('#modal-aprovadores') });

});
</script>
@stop
