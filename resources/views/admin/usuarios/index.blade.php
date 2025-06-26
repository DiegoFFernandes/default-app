@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="content-fluid">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('usuario.role') }}" class="btn btn-danger float-right btn-sm" style="margin-right: 5px;">Permissão</a>
                    <a href="{{ route('usuario.role') }}"  class="btn btn-danger float-right btn-sm" style="margin-right: 5px;">Função</a>
                    <button class="btn btn-success btn-sm" style="margin-right: 5px;" id="add-user">Adicionar</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="table-user" class="table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Empresa</th>
                                        <th>Status</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>

        </div>

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
                        <div class="form-group">
                            <input type="hidden" name="id_user" id="id_user">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pessoa">Pesquisar Pessoa:</label>
                                <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nome:*</label>
                                <input type="name" name='name' class="form-control" id="name"
                                    placeholder="Nome usuario" required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="email">Email:*</label>
                            <div class="form-group">
                                <input type="email" name='email' class="form-control" id="email" placeholder="Email"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="email">Celular:*</label>
                            <div class="form-group">
                                <input type="text" name='phone' class="form-control" id="phone"
                                    placeholder="(99)99999-9999" value="{{ isset($user_id->phone) ? $user_id->phone : '' }}"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="password">Senha:*</label>
                                <input type="password" name='password' class="form-control" id="password"
                                    placeholder="Digite uma senha" required>
                            </div>
                        </div>
                        <!-- select -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Empresa Principal:*</label>
                                <select class="form-control" name="empresa" id=empresa>
                                    {{-- Condição para editar usuario --}}
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->CD_EMPRESA }}">
                                            {{ $empresa->NM_EMPRESA }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ds_tipopessoa">Tipo Pessoa:*</label>
                                <select class="form-control" name="ds_tipopessoa" id="tipopessoa" required>
                                    {{-- Condição para editar usuario --}}
                                    @foreach ($tipopessoa as $t)
                                        <option value="{{ $t->CD_TIPOPESSOA }}">
                                            {{ ucfirst(strtolower($t->DS_TIPOPESSOA)) }}
                                        </option>
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
    </section>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        $('#table-user').DataTable({
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
                url: "{{ route('usuario.list') }}",
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
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ]
        });
        $('#add-user').click(function() {
            $('.modal-title').text('Adicionar usuário');
            $('#modal-user').modal('show');
            $('#btn-save').removeClass('d-none');
            $('#btn-update').addClass('d-none');

        });
        $('#pessoa').select2({
            placeholder: "Pessoa",
            theme: 'bootstrap4',
            width: '100%',            
            allowClear: true,
            minimumInputLength: 2,
            dropdownParent: $('#modal-user'),
            ajax: {
                url: " {{ route('usuario.search-pessoa') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.NM_PESSOA,
                                id: item.ID,
                                email: item.DS_EMAIL,
                                tipopessoa: item.CD_TIPOPESSOA,
                                phone: item.NR_CELULAR
                            }
                        })

                    };
                },
                cache: true
            }
        }).change(function(el) {
            var data = $(el.target).select2('data');
            $('#name').val(data[0].text);
            $('#email').val(data[0].email);
            $('#ds_tipopessoa').val(data[0].tipopessoa);
            $('#phone').val(data[0].phone);
        });
        $('#table-user').on('click', '.btn-editar', function(e) {
            $('.modal-title').text('Editar usuário');
            var rowData = $('#table-user').DataTable().row($(this).parents('tr')).data();
            console.log(rowData);
            $('#btn-save').addClass('d-none');
            $('#btn-update').removeClass('d-none');
            $('#id_user').val(rowData['id']);
            $('#name').val(rowData['name']);
            $('#password').val(rowData[''])
            $('#email').val(rowData['email']);
            $('#phone').val(rowData['phone']);
            // $('#').val('');
            $('#modal-user').modal('show');
        });
        $('#btn-save').click(function(e) {
            dataUser("{{ route('usuario.create.do') }}", 1);
        });

        $('#btn-update').click(function() {
            dataUser("{{ route('usuario.update') }}"), 2;
        });

        function dataUser(route, action) {
            // e.preventDefault();
            // Captura os valores
            const id_user = $('#id_user').val();
            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const phone = $('#phone').val().trim();
            let password = $('#password').val().trim();
            const empresa = $('#empresa').val().trim();
            const tipopessoa = $('#tipopessoa').val().trim();


            // Valida se todos estão preenchidos
            if (!password && action == 1) {
                msgToastr('Por favor, informe uma senha', 'error');
                return false;
            }

            if (!password && action == 2) {
                password = "";
            } else {
                // manter a senha vazia caso o usuario não digitou a senha para alterar...                
                password = $('#password').val().trim();
            }

            // Validação de e-mail simples
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(email)) {
                msgToastr('E-mail inválido!', 'error');
                return false;
            }

            $.ajax({
                type: "POST",
                url: route,
                data: {
                    _token: $("[name=csrf-token]").attr("content"),
                    name: name,
                    id: id_user,
                    email: email,
                    phone: phone,
                    password: password,
                    empresa: empresa,
                    tipopessoa: tipopessoa
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-user').modal('hide');
                        $('#table-user').DataTable().ajax.reload();
                        msgToastr(response.success, 'success');
                        $('#pessoa').val('').trigger('change');
                        $('#name').val('');
                        $('#email').val('');
                        $('#phone').val('');
                        $('#password').val('');
                        $('#empresa').val(1);
                        $('#tipopessoa').val(1);

                    } else if (response.warning) {
                        msgToastr(response.warning, 'warning');
                    } else if (response.error) {
                        msgToastr(response.error, 'error');
                    }

                }
            });
        }

        $('#table-user').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            deleteId = $(this).data('id');
            if (!confirm('Deseja realmente excluir o usuario ' + deleteId + ' ?')) return;
            $.ajax({
                url: "{{ route('usuario.delete') }}",
                method: 'DELETE',
                data: {
                    "id": deleteId,
                    "_token": $("[name=csrf-token]").attr("content"),
                },
                beforeSend: function() {
                    // $("#loading").removeClass('hidden');
                },
                success: function(response) {
                    $('#table-user').DataTable().ajax.reload();
                    msgToastr(response.success, 'success');
                }
            });

        });
    </script>
@stop
