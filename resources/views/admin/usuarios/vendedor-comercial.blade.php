@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Vendedor Junsoft</label>
                                    <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Usúarios Dashboard</label>
                                    <select class="form-control select2" id="cd_usuario" style="width: 100%; ">
                                        <option selected="selected">Selecione</option>
                                        @foreach ($user as $u)
                                            <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="card-footer">
                        <div class="col-md-2" align="center">
                            <div class="form-group">
                                <button type="submit" id="btn-vincular"
                                    class="btn btn-danger btn-sm btn-block">Associar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header with-border">
                            <h3 class="card-title">Vendedores Associados</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table compact table-font-small table-striped table-bordered nowrap" id="table-vendedor" style="width: 100%, font-size: 10px;">
                                    <thead>
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Cd. Usúario</th>
                                            <th>Usúario</th>
                                            <th>Cd. Vendedor</th>
                                            <th>Vendedor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
    </section>
    {{-- Modal Editar --}}
    <div class="modal" id="CreateVendedorModal">
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
                                <label>Pessoa</label>
                                <select name="cd_pessoa_modal" class="form-control" id="cd_pessoa_modal"
                                    data-modal="#CreateVenddorModal" style="width: 100%">
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
        $(document).ready(function() {

            const routes = {
                searchPessoa: "{{ route('usuario.search-pessoa') }}",
            }

            initSelect2Pessoa('#pessoa', routes.searchPessoa);


            $('#cd_pessoa').select2({
                theme: 'bootstrap4'
            });
            $('#cd_pessoa_modal').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario_modal').select2({
                theme: 'bootstrap4'
            });
            $('#btn-vincular').click(function() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('vendedor-comercial.create') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ds_vendedor: $("#pessoa option:selected").text(),
                        cd_vendedor: $('#pessoa').val(),
                        cd_usuario: $('#cd_usuario').val()
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
                            $('#table-vendedor').DataTable().ajax.reload()
                        }
                    }
                });
            });
            var dataTable = $('#table-vendedor').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-table-vendedor-usuario') }}',
                columns: [{
                        data: 'id',
                        name: 'id',
                        responsivePriority: 1
                    },
                    {
                        data: 'cd_usuario',
                        name: 'cd_usuario',
                        visible: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                        responsivePriority: 2
                    },
                    {
                        data: 'cd_vendedorcomercial',
                        name: 'cd_vendedorcomercial',
                        visible: false,
                    },
                    {
                        data: 'ds_vendedorcomercial',
                        name: 'ds_vendedorcomercial',
                        responsivePriority: 3
                    },
                    {
                        data: 'Actions',
                        name: 'Actions'
                    }
                ],
                pageLength: 20,
                order: [2, 'asc'],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                responsive: true,
            });
            //dataTable e um variavel que tras a informação da tabela.
            $('#table-vendedor').on('click', '.btn-edit', function() {

                initSelect2Pessoa('#cd_pessoa_modal', routes.searchPessoa, '#CreateVendedorModal');

                var rowData = dataTable.row($(this).parents('tr')).data();
                $('#id').val(rowData.id);
                var newOption = new Option(rowData.nm_pessoa, rowData.cd_pessoa, true, true);
                $('#cd_pessoa_modal').append(newOption).trigger('change');
                $('#cd_usuario_modal').val(rowData.cd_usuario).trigger('change');
                $('.modal').modal('show');
            });
            $('.btn-update').click(function() {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Deseja alterar o vendedor " +  $('#id').val() + "?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, alterar!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        let formData = {
                            id: $('#id').val(),
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ds_vendedor: $("#cd_pessoa_modal option:selected").text(),
                            cd_vendedor: $('#cd_pessoa_modal').val(),                            
                            cd_usuario: $('#cd_usuario_modal').val(),
                        };

                        $.ajax({
                            type: 'POST',
                            url: '{{ route("edit-vendedor-comercial") }}',
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
                                    $('#table-vendedor').DataTable()
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
                            url: "{{ route('vendedor-comercial.delete') }}",
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
                                    $('#table-vendedor').DataTable()
                                        .ajax
                                        .reload();
                                }
                            }
                        });
                    }
                });

            });

        });
    </script>
@stop
