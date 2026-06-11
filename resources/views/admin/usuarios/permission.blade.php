@extends('layouts.master')

@section('title', 'Permissões')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-success btn-sm" id="btn-create">Criar novo</button>
                    </div>
                    <div class="card-body">
                        <table class="table display table-sm compact table-font-small" id="table-permissions" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Função</th>
                                    <th>Permissões</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-permission" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Adicionar Permissão</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Função:</label>
                            <input type="text" id="nome-role" class="form-control" disabled style="display:none;">
                            <select id="role" class="form-control" style="display:none;"></select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Permissões:</label>
                            <select class="form-control" name="permissoes[]" id="permissoes" multiple required></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-save">Salvar</button>
                    <button type="button" class="btn btn-sm btn-warning d-none" id="btn-update">Atualizar</button>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        var table = $('#table-permissions').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            },
            pagingType: "simple",
            processing: false,
            responsive: true,
            serverSide: false,
            autoWidth: false,
            order: [[0, "desc"]],
            pageLength: 10,
            ajax: {
                url: "{{ route('usuario.permission-role.list') }}",
            },
            columns: [
                { data: 'id',                name: 'id' },
                { data: 'name',              name: 'name' },
                { data: 'permissions_badges', name: 'permissions_badges' },
                { data: 'actions',           name: 'actions' },
                { data: 'permission_names',  name: 'permission_names', visible: false },
            ]
        });

        $('#role').select2({
            theme: 'bootstrap4',
            placeholder: "Selecione a função",
            width: '100%',
            dropdownParent: $('#modal-permission')
        });

        $('#permissoes').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Selecione as permissões",
            dropdownParent: $('#modal-permission')
        });

        // Carrega permissões disponíveis uma vez
        $.get("{{ route('usuario.permission-role.get-permissions') }}", function(data) {
            data.forEach(function(perm) {
                $('#permissoes').append(new Option(perm.name, perm.name, false, false));
            });
            $('#permissoes').trigger('change');
        });

        function resetModal() {
            $('#nome-role').val('').hide();
            $('#role').empty().append('<option></option>').val(null).trigger('change').show();
            $('#role').next('.select2-container').show();
            $('#permissoes').val(null).trigger('change');
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');
            $('#modal-permission').removeData('role-id');
        }

        $('#btn-create').click(function() {
            $('.modal-title').text('Adicionar permissão');
            resetModal();

            $.get("{{ route('usuario.permission-role.get-roles') }}", function(data) {
                data.forEach(function(role) {
                    $('#role').append(new Option(role.name, role.id, false, false));
                });
                $('#role').trigger('change');
            });

            $('#modal-permission').modal('show');
        });

        $('#table-permissions').on('click', '.btn-editar', function() {
            var rowData = table.row($(this).parents('tr')).data();
            resetModal();
            $('.modal-title').text('Editar permissões');
            $('#nome-role').val(rowData.name).show();
            $('#role').hide();
            $('#role').next('.select2-container').hide();
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#modal-permission').data('role-id', rowData.id);
            $('#permissoes').val(rowData.permission_names).trigger('change');
            $('#modal-permission').modal('show');
        });

        $('#btn-save').click(function() {
            var roleId      = $('#role').val();
            var permissoes  = $('#permissoes').val();

            if (!roleId) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione uma função.' });
                return;
            }
            if (!permissoes || permissoes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.permission-role.assign') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', role_id: roleId, permissions: permissoes },
                success: function(response) {
                    $('#modal-permission').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Sucesso', text: response.message, timer: 2000, showConfirmButton: false });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao salvar.';
                    Swal.fire({ icon: 'error', title: 'Erro', text: msg });
                }
            });
        });

        $('#btn-update').click(function() {
            var roleId     = $('#modal-permission').data('role-id');
            var permissoes = $('#permissoes').val();

            if (!permissoes || permissoes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.permission-role.update') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', role_id: roleId, permissions: permissoes },
                success: function(response) {
                    $('#modal-permission').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Sucesso', text: response.message, timer: 2000, showConfirmButton: false });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao atualizar.';
                    Swal.fire({ icon: 'error', title: 'Erro', text: msg });
                }
            });
        });

        $('#table-permissions').on('click', '.btn-delete', function() {
            var rowData = table.row($(this).parents('tr')).data();

            Swal.fire({
                title: 'Confirmar exclusão',
                html: 'Deseja remover todas as permissões da função <strong>' + rowData.name + '</strong>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('usuario.permission-role.remove') }}",
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}', role_id: rowData.id },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            Swal.fire({ icon: 'success', title: 'Sucesso', text: response.message, timer: 2000, showConfirmButton: false });
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao remover.';
                            Swal.fire({ icon: 'error', title: 'Erro', text: msg });
                        }
                    });
                }
            });
        });
    </script>
@stop
