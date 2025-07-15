@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Gerente Unidade</h3>
                    </div>
                    <div class="card-body">
                        <form id="formGerenteUnidade">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Empresa</label>
                                            <select class="form-control select2" name="cd_empresa" id="cd_empresa"
                                                style="width: 100%;">
                                                <option selected="selected">Selecione</option>
                                                @foreach ($empresa as $e)
                                                    <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Gerente Unidade</label>
                                            <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Usúarios Dashboard</label>
                                            <select class="form-control select2" id="cd_usuario" name="cd_usuario"
                                                style="width: 100%; ">
                                                <option selected="selected">Selecione</option>
                                                @foreach ($user as $u)
                                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" align="center" style="padding-top: 24px">
                                    <div class="form-group">
                                        <button form="formGerenteUnidade" class="btn btn-danger btn-block">Associar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Gerente Unidade</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table compact" id="table-gerente-unidade" style="width: 100%">
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
    </section>
    {{-- Modal Editar --}}
    <div class="modal" id="CreatePessoaModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <span class="modal-title">Editar Supervisor</span>
                    <button type="button" class="close btn-cancel" data-dismiss="modal" data-keyboard="false"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Cód.</label>
                                <input type="text" class="form-control" name="id" id="id" disabled>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Empresa</label>
                                <select class="form-control select2" name="cd_empresa_modal" id="cd_empresa_modal"
                                    style="width: 100%;">
                                    @foreach ($empresa as $e)
                                        <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Úsuário Dashboard Junsoft</label>
                                <select class="form-control" name="cd_usuario_modal" id="cd_usuario_modal"
                                    style="width: 100%">
                                    @foreach ($user as $u)
                                        <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label>Gerente Unidade</label>
                                <select name="cd_pessoa_modal" class="form-control" id="cd_pessoa_modal"
                                    data-modal="#CreatePessoaModal" style="width: 100%">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-update">Editar</button>
                    <button type="button" class="btn btn-danger btn-cancel" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@stop

@section('js')
    <script type="text/javascript">
        const routes = {
            searchPessoa: "{{ route('usuario.search-pessoa') }}",
        }

        initSelect2Pessoa('#pessoa', routes.searchPessoa);
        initSelect2Pessoa('#cd_pessoa_modal', routes.searchPessoa, '#CreatePessoaModal');

        $('#cd_supervisorcomercial_modal').select2({
            theme: 'bootstrap4'
        });
        $('#cd_usuario').select2({
            theme: 'bootstrap4'
        });
        $('#cd_usuario_modal').select2({
            theme: 'bootstrap4'
        });
        $('#formGerenteUnidade').on('submit', function(e) {
            e.preventDefault();            

            $.ajax({
                type: "POST",
                url: "{{ route('gerente-unidade.create') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ds_gerenteunidade: $("#pessoa option:selected").text(),
                    cd_gerenteunidade: $('#pessoa').val(),
                    cd_usuario: $('#cd_usuario').val(),
                    cd_empresa: $('#cd_empresa').val()
                },
                beforeSend: function() {
                    $("#loading").removeClass('hidden');
                },
                success: function(result) {
                    $("#loading").addClass('hidden');

                    if (result.errors) {
                        msgToastr(result.errors, 'warning');
                    } else {
                        $('.modal').modal('hide');
                        msgToastr(result.success, 'success');
                        $('#table-gerente-unidade').DataTable().ajax.reload()
                    }
                }
            });
        });
        var dataTable = $('#table-gerente-unidade').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('get-table-gerente-usuario') }}',
            columns: [{
                    data: 'id',
                    name: 'id',
                    title: 'Cód.',
                },
                {
                    data: 'cd_empresa',
                    name: 'cd_empresa',
                    visible: true,
                    title: 'Empresa',
                },
                {
                    data: 'cd_usuario',
                    name: 'cd_usuario',
                    visible: false,
                    title: 'Cód. Usuário',
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Usuário',
                },
                {
                    data: 'cd_gerenteunidade',
                    name: 'cd_gerenteunidade',
                    visible: false,
                    title: 'Cód. Gerente',
                },
                {
                    data: 'ds_gerenteunidade',
                    name: 'ds_gerenteunidade',
                    title: 'Gerente Unidade'
                },
                {
                    data: 'Actions',
                    name: 'Actions',
                    title: 'Ações',
                }
            ],
            order: [2, 'asc'],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
            },
            responsive: true,
        });
        //dataTable e um variavel que tras a informação da tabela.
        $('#table-gerente-unidade').on('click', '.btn-edit', function() {
            var rowData = dataTable.row($(this).parents('tr')).data();
            $('#id').val(rowData.id);
            var newOption = new Option(rowData.ds_gerenteunidade, rowData.cd_gerenteunidade, true, true);
            $('#cd_pessoa_modal').append(newOption).trigger('change');

            $('#cd_usuario_modal').val(rowData.cd_usuario).trigger('change');
            $('#cd_empresa_modal').val(rowData.cd_empresa).trigger('change');
            $('.modal').modal('show');
        });
        $('.btn-update').click(function() {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja alterar o supervisor " + deleteId + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, alterar!'
            }).then((result) => {
                if (result.isConfirmed) {

                    let formData = {
                        id: $('#id').val(),
                        ds_gerenteunidade: $("#cd_pessoa_modal option:selected").text(),
                        cd_gerenteunidade: $('#cd_pessoa_modal').val(),
                        cd_empresa: $('#cd_empresa_modal').val(),
                        cd_usuario: $('#cd_usuario_modal').val(),
                    };

                    $.ajax({
                        type: 'GET',
                        url: '{{ route('edit-gerente-unidade') }}',
                        data: formData,
                        beforeSend: function() {

                        },
                        success: function(result) {
                            $("#loading").addClass('hidden');

                            if (result.errors) {
                                msgToastr(result.errors, 'warning');
                            } else {
                                $('.modal').modal('hide');
                                msgToastr(result.success,
                                    'success');
                                $('.modal').modal('hide');
                                $('#table-gerente-unidade').DataTable()
                                    .ajax.reload();
                            }

                        }
                    });
                }
            });
        });
        //Delete
        var deleteId;
        $('body').on('click', '#getDeleteId', function() {
            deleteId = $(this).data('id');

            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja excluir o código " + deleteId + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('gerente-unidade.delete') }}",
                        method: 'DELETE',
                        data: {
                            id: deleteId,
                            "_token": $("[name=csrf-token]").attr(
                                "content"),
                        },
                        beforeSend: function() {
                            $("#loading").removeClass('hidden');
                        },
                        success: function(result) {
                            $("#loading").addClass('hidden');
                            if (result.errors) {
                                msgToastr(result.errors, 'warning');
                            } else {
                                $('.modal').modal('hide');
                                msgToastr(result.success,
                                    'success');
                                $('.modal').modal('hide');
                                $('#table-gerente-unidade').DataTable()
                                    .ajax
                                    .reload();
                            }
                        }
                    });
                }
            });

        });
    </script>
@stop
