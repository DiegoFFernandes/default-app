@extends('layouts.master')

@section('title', 'Permissões de Usuários')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-success btn-sm" id="btn-create">Criar novo</button>
                    </div>
                    <div class="card-body">
                        <table class="table display table-sm compact table-font-small" id="table-perm-user" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Usuário</th>
                                    <th>Email</th>
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

    <div class="modal fade" id="modal-perm-user" style="display: none;" aria-hidden="true">
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
                            <label>Usuário:</label>
                            <input type="text" id="nome-user" class="form-control" disabled style="display:none;">
                            <select id="usuario" class="form-control" style="display:none;"></select>
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
        var table = $('#table-perm-user').DataTable({
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
                url: "{{ route('usuario.permission-user.list') }}",
            },
            columns: [
                { data: 'id',                name: 'id' },
                { data: 'name',              name: 'name' },
                { data: 'email',             name: 'email' },
                { data: 'permissions_badges', name: 'permissions_badges' },
                { data: 'actions',           name: 'actions' },
                { data: 'permission_names',  name: 'permission_names', visible: false },
            ]
        });

        $('#usuario').select2({
            theme: 'bootstrap4',
            placeholder: "Selecione o usuário",
            width: '100%',
            dropdownParent: $('#modal-perm-user')
        });

        $('#permissoes').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Selecione as permissões",
            dropdownParent: $('#modal-perm-user')
        });

        $.get("{{ route('usuario.permission-user.get-permissions') }}", function(data) {
            data.forEach(function(perm) {
                $('#permissoes').append(new Option(perm.name, perm.name, false, false));
            });
            $('#permissoes').trigger('change');
        });

        function resetModal() {
            $('#nome-user').val('').hide();
            $('#usuario').empty().append('<option></option>').val(null).trigger('change').show();
            $('#usuario').next('.select2-container').show();
            $('#permissoes').val(null).trigger('change');
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');
            $('#modal-perm-user').removeData('user-id');
        }

        $('#btn-create').click(function() {
            $('.modal-title').text('Adicionar permissão');
            resetModal();

            $.get("{{ route('usuario.permission-user.get-users') }}", function(data) {
                data.forEach(function(user) {
                    var label = user.name + ' — ' + user.email;
                    $('#usuario').append(new Option(label, user.id, false, false));
                });
                $('#usuario').trigger('change');
            });

            $('#modal-perm-user').modal('show');
        });

        $('#table-perm-user').on('click', '.btn-editar', function() {
            var rowData = table.row($(this).parents('tr')).data();
            resetModal();
            $('.modal-title').text('Editar permissões');
            $('#nome-user').val(rowData.name).show();
            $('#usuario').hide();
            $('#usuario').next('.select2-container').hide();
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#modal-perm-user').data('user-id', rowData.id);
            $('#permissoes').val(rowData.permission_names).trigger('change');
            $('#modal-perm-user').modal('show');
        });

        $('#btn-save').click(function() {
            var userId     = $('#usuario').val();
            var permissoes = $('#permissoes').val();

            if (!userId) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione um usuário.' });
                return;
            }
            if (!permissoes || permissoes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.permission-user.assign') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', user_id: userId, permissions: permissoes },
                success: function(response) {
                    $('#modal-perm-user').modal('hide');
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
            var userId     = $('#modal-perm-user').data('user-id');
            var permissoes = $('#permissoes').val();

            if (!permissoes || permissoes.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.permission-user.update') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', user_id: userId, permissions: permissoes },
                success: function(response) {
                    $('#modal-perm-user').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Sucesso', text: response.message, timer: 2000, showConfirmButton: false });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao atualizar.';
                    Swal.fire({ icon: 'error', title: 'Erro', text: msg });
                }
            });
        });

        $('#table-perm-user').on('click', '.btn-delete', function() {
            var rowData = table.row($(this).parents('tr')).data();

            Swal.fire({
                title: 'Confirmar exclusão',
                html: 'Deseja remover todas as permissões do usuário <strong>' + rowData.name + '</strong>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('usuario.permission-user.remove') }}",
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}', user_id: rowData.id },
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
