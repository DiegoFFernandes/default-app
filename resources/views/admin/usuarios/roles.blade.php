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
                    <h4 class="modal-title">Adicionar Usúario</h4>
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

        function resetModal() {
            $('#nome').val('').hide();
            $('#usuario').val(null).trigger('change').show();
            $('#usuario').next('.select2-container').show();
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');
        }

        $('#btn-create').click(function() {
            $('.modal-title').text('Adicionar usuário');
            resetModal();
            $('#modal-user').modal('show');
        });

        $('#table-roles').on('click', '.btn-editar', function(e) {
            $('.modal-title').text('Editar função');
            var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            resetModal();
            $('#modal-user').modal('show');
            $('#nome').val(rowData['name']).show();
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#usuario').hide();
            $('#usuario').next('.select2-container').hide();

        });
        $('#table-roles').on('click', '.btn-delete', function(e) {
            // var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            console.log(rowData);
        });

        $('#modal-user').on('shown.bs.modal', function() {
            $('#rolepessoa').val(null).trigger('change');
        });

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
    </script>
@stop
