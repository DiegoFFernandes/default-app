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
                        <table class="table display table-sm compact table-font-small" id="table-roles" style="width: 100%">
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
                    <h6 class="modal-title">Adicionar Usúario</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Nome:</label>
                            <input type="text" id="nome" class="form-control" disabled>
                            <select id="usuario" class="form-control" style="display:none;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ds_rolepessoa">Função:</label>
                            <select class="form-control select2" name="ds_rolepessoa[]" id="rolepessoa" multiple required>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-save">Salvar</button>
                    <button type="button" class="btn btn-sm btn-warning d-none" id="btn-update">Atualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop


@section('js')
    <script>
        var table = $('#table-roles').DataTable({
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
                {
                    data: 'role_names',
                    name: 'role_names',
                    visible: false
                },
            ]
        });

        $('#usuario').select2({
            theme: 'bootstrap4',
            placeholder: "Selecione o usuário",
            width: '100%',
            dropdownParent: $('#modal-user')
        });

        $('#rolepessoa').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Selecione as funções",
            dropdownParent: $('#modal-user')
        });

        // Carrega as funções disponíveis uma vez
        $.get("{{ route('usuario.role.get-roles') }}", function(data) {
            data.forEach(function(role) {
                $('#rolepessoa').append(new Option(role.name, role.name, false, false));
            });
            $('#rolepessoa').trigger('change');
        });

        function resetModal() {
            $('#nome').val('').hide();
            $('#usuario').empty().append('<option></option>').val(null).trigger('change').show();
            $('#usuario').next('.select2-container').show();
            $('#rolepessoa').val(null).trigger('change');
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');
            $('#modal-user').removeData('user-id');
        }

        $('#btn-create').click(function() {
            $('.modal-title').text('Adicionar usuário');
            resetModal();

            $.get("{{ route('usuario.role.get-users') }}", function(data) {
                data.forEach(function(user) {
                    var label = user.name + ' — ' + user.email;
                    $('#usuario').append(new Option(label, user.id, false, false));
                });
                $('#usuario').trigger('change');
            });

            $('#modal-user').modal('show');
        });

        $('#table-roles').on('click', '.btn-editar', function() {
            var rowData = table.row($(this).parents('tr')).data();
            resetModal();
            $('.modal-title').text('Editar função');
            $('#nome').val(rowData.name).show();
            $('#usuario').hide();
            $('#usuario').next('.select2-container').hide();
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#modal-user').data('user-id', rowData.id);
            $('#rolepessoa').val(rowData.role_names).trigger('change');
            $('#modal-user').modal('show');
        });

        $('#btn-save').click(function() {
            var userId = $('#usuario').val();
            var roles = $('#rolepessoa').val();

            if (!userId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione um usuário.'
                });
                return;
            }
            if (!roles || roles.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione pelo menos uma função.'
                });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.role.assign') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    roles: roles
                },
                success: function(response) {
                    $('#modal-user').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message :
                        'Erro ao salvar.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg
                    });
                }
            });
        });

        $('#btn-update').click(function() {
            var userId = $('#modal-user').data('user-id');
            var roles = $('#rolepessoa').val();

            if (!roles || roles.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione pelo menos uma função.'
                });
                return;
            }

            $.ajax({
                url: "{{ route('usuario.role.update') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    roles: roles
                },
                success: function(response) {
                    $('#modal-user').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message :
                        'Erro ao atualizar.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg
                    });
                }
            });
        });

        $('#table-roles').on('click', '.btn-delete', function() {
            var rowData = table.row($(this).parents('tr')).data();

            Swal.fire({
                title: 'Confirmar exclusão',
                html: 'Deseja remover todas as funções e excluir o usuário <strong>' + rowData.name +
                    '</strong>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('usuario.role.remove') }}",
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            user_id: rowData.id
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                                .responseJSON.message : 'Erro ao excluir.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: msg
                            });
                        }
                    });
                }
            });
        });
    </script>
@stop
