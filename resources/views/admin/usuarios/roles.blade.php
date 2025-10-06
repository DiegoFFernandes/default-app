@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-success btn-sm" id="btn-create">Criar novo</button>
                    </div>
                    <!-- /.box-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display table-sm compact" id="table-roles" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Empresa</th>
                                        <th>Funções</th>
                                        <th>Acões</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
    </section>
    <!-- /.content -->
    <div class="modal fade" id="modal-user" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adicionar Usuário</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nome">Nome:</label>
                            <input type="text" id="nome" class="form-control" disabled>
                            <select name="user_id" id="usuario" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ds_rolepessoa">Função:</label>
                            <select class="form-control select2" name="ds_rolepessoa[]" id="rolepessoa" multiple required>
                                @foreach ($all_roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Salvar</button>
                    <button type="button" class="btn btn-warning d-none" id="btn-update">Atualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop


@section('js')
    <script>
        $('#usuario').select2({
            theme: 'bootstrap4',
            placeholder: "Selecione o usuário",
            width: '100%'
        });

        $('#rolepessoa').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Selecione as funções",
        });

        $('#table-roles').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            },
            pagingType: "simple",
            processing: false,
            responsive: true,
            serverSide: false,
            autoWidth: false,
            order: [
                [0, "desc"]
            ],
            "pageLength": 10,
            ajax: {
                url: "{{ route('usuario.list_role') }}",
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'empresa',
                    name: 'empresa'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ]
        });

        $('#modal-user').on('shown.bs.modal', function() {
            $('#rolepessoa').val(null).trigger('change');
        });

        $('#btn-create').click(function() {
            $('.modal-title').text('Adicionar usuário');
            resetModal();
            $('#modal-user').modal('show');
        });

        $('#table-roles').on('click', '.btn-editar', function(e) {
            $('.modal-title').text('Editar função');
            var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#nome').val(rowData['name']).show();
            $('#usuario').hide();
            $('#usuario').next('.select2-container').hide();
            $('#nome').data('user-id', rowData['id']);
            $('#modal-user').modal('show');
        });

        $('#btn-save').click(function() {
            dataUser("{{ route('usuario.role.create.do') }}", 1);
        });

        $('#btn-update').click(function() {
            dataUser("{{ route('usuario.role.edit.do') }}", 2);
        });

        function dataUser(route, action) {
            // Captura os valores
            const userId = (action === 1) ? $('#usuario').val() : $('#nome').data('user-id');
            const roles = $('#rolepessoa').val();

            // Valida se todos estão preenchidos
            if (!roles || roles.length === 0) {
                msgToastr('Por favor, selecione pelo menos uma função', 'error');
                return false;
            }

            if (action === 1 && !userId) {
                msgToastr('Selecione um usuário para adicionar', 'error');
                return false;
            }

            $.ajax({
                type: "POST",
                url: route,
                data: {
                    _token: $("[name=csrf-token]").attr("content"),
                    user_id: userId,
                    roles: roles,
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-user').modal('hide');
                        $('#table-roles').DataTable().ajax.reload();
                        msgToastr(response.success, 'success');
                        //remove o usuário da lista
                        if (action === 1) {
                            $('#usuario option[value="' + userId + '"]').remove();
                        }
                        $('#usuario').trigger('change');
                        $('#rolepessoa').val(null).trigger('change');
                    } else if (response.warning) {
                        msgToastr(response.warning, 'warning');
                    } else if (response.error) {
                        msgToastr(response.error, 'error');
                    }
                },
            });
        }

        $('#table-roles').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var rowData = $('#table-roles').DataTable().row($(this).closest('tr')).data();
            const deleteId = rowData.id;
            const deletName = rowData.name;
            if (!confirm('Deseja realmente excluir a função do usúario?')) return;
            $.ajax({
                url: "{{ route('usuario.role.delete') }}",
                method: 'DELETE',
                data: {
                    "id": deleteId,
                    "_token": $("[name=csrf-token]").attr("content"),
                },
                success: function(response) {
                    if (response.success) {
                        msgToastr(response.success, 'success');
                        //adiciona o usuário de volta à lista
                        var voltaUsuario = new Option(deletName, deleteId);
                        $('#usuario').append(voltaUsuario);
                        $('#usuario').trigger('change'); //atualiza o select2
                        $('#table-roles').DataTable().ajax.reload();
                    }
                },
            });
        });

        function resetModal() {
            $('#nome').val('').hide();
            $('#usuario').val(null).trigger('change').show();
            $('#usuario').next('.select2-container').show();
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');
        }
    </script>
@stop
