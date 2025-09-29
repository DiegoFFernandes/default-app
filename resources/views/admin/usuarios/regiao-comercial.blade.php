@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Regiões Junsoft</label>
                                        <select class="form-control select2" id="cd_regiaocomercial" style="width: 100%;">
                                            <option selected="selected">Selecione</option>
                                            @foreach ($regiao as $r)
                                                <option value="{{ $r->CD_REGIAOCOMERCIAL }}">{{ $r->DS_REGIAOCOMERCIAL }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                            <div class="col-md-2" align="center" style="padding-top: 24px">
                                <div class="form-group">
                                    <button type="submit" id="btn-vincular"
                                        class="btn btn-danger btn-block">Associar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Regiões Associadas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table compact table-font-small table-striped table-bordered" id="table-regiao" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Cd. Usúario</th>
                                    <th>Usúario</th>
                                    <th>Cd. Região</th>
                                    <th>Região</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
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
                    <span class="modal-title">Editar Região</span>
                    <button type="button" class="close btn-cancel" data-dismiss="modal" data-keyboard="false"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Cód.</label>
                            <input type="text" class="form-control" name="id" id="id" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Úsuário Dashboard Junsoft</label>
                            <select class="form-control" name="cd_usuario_modal" id="cd_usuario_modal" style="width: 100%">
                                @foreach ($user as $u)
                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label>Região Junsoft</label>
                            <select class="form-control" name="cd_regiaocomercial_modal" id="cd_regiaocomercial_modal"
                                style="width: 100%;">
                                @foreach ($regiao as $r)
                                    <option value="{{ $r->CD_REGIAOCOMERCIAL }}">{{ $r->DS_REGIAOCOMERCIAL }}
                                    </option>
                                @endforeach
                            </select>
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
            $('#cd_regiaocomercial').select2({
                theme: 'bootstrap4'
            });
            $('#cd_regiaocomercial_modal').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario_modal').select2({
                theme: 'bootstrap4'
            });
            $('#btn-vincular').click(function() {
                let ds_regiaocomercial = $("#cd_regiaocomercial option:selected").text();
                let cd_regiaocomercial = $('#cd_regiaocomercial').val();
                let cd_usuario = $('#cd_usuario').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('get-regiao-comercial.create') }}",
                    data: {
                        cd_regiaocomercial: cd_regiaocomercial,
                        cd_usuario: cd_usuario,
                        ds_regiaocomercial: ds_regiaocomercial
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
                            $('#table-regiao').DataTable().ajax.reload()
                        }
                    }
                });
            });
            var dataTable = $('#table-regiao').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-table-regiao-usuario') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'cd_usuario',
                        name: 'cd_usuario',
                        visible: false,
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'cd_regiaocomercial',
                        name: 'cd_regiaocomercial',
                        visible: false,
                    },
                    {
                        data: 'ds_regiaocomercial',
                        name: 'ds_regiaocomercial'
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
                pagingType: "simple",
                scrollY: 300,
                pageLength: 20,
            });
            //dataTable e um variavel que tras a informação da tabela.
            $('#table-regiao').on('click', '.btn-edit', function() {
                var rowData = dataTable.row($(this).parents('tr')).data();
                $('#id').val(rowData.id);
                $('#cd_regiaocomercial_modal').val(rowData.cd_regiaocomercial).trigger('change');
                $('#cd_usuario_modal').val(rowData.cd_usuario).trigger('change');
                $('.modal').modal('show');
            });
            $('.btn-update').click(function() {

                toastr.info(
                    "<button type='button' id='confirmationButtonYes' class='btn btn-success clear'>Sim</button>" +
                    "<button type='button' id='confirmationButtonNo' class='btn btn-primary clear'>Não</button>",
                    'Você tem certeza que deseja atualizar?', {
                        closeButton: false,
                        allowHtml: true,
                        progressBar: false,
                        timeOut: 0,
                        positionClass: "toast-top-center",
                        onShown: function(toast) {
                            $("#confirmationButtonYes").click(function() {
                                let id = $('#id').val(),
                                    cd_regiaocomercial = $('#cd_regiaocomercial_modal')
                                    .val(),
                                    ds_regiaocomercial = $(
                                        "#cd_regiaocomercial_modal option:selected").text(),
                                    cd_usuario = $('#cd_usuario_modal').val();
                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('edit-regiao-usuario') }}',
                                    data: {
                                        id: id,
                                        cd_regiaocomercial: cd_regiaocomercial,
                                        cd_usuario: cd_usuario,
                                        ds_regiaocomercial: ds_regiaocomercial,
                                        _token: $('#token').val(),
                                    },
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
                                            $('#table-regiao').DataTable().ajax
                                                .reload();
                                        }

                                    }
                                });
                            });
                        }
                    }
                );
            });
            //Delete
            var deleteId;
            $('body').on('click', '#getDeleteId', function() {
                deleteId = $(this).data('id');
                toastr.info(
                    "<button type='button' id='confirmationButtonYes' class='btn btn-danger ml-2'>Sim</button> " +
                    "<button type='button' id='confirmationButtonNo' class='btn btn-primary '>Não</button>",
                    'Deseja realmente excluir o item ' + deleteId + ' ?', {
                        closeButton: false,
                        allowHtml: true,
                        progressBar: false,
                        timeOut: 0,
                        positionClass: "toast-top-center",
                        onShown: function(toast) {
                            $("#confirmationButtonYes").click(function() {
                                $.ajax({
                                    url: "{{ route('regiao-usuario.delete') }}",
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
                                            $('#table-regiao').DataTable().ajax
                                                .reload();
                                        }
                                    }
                                });
                            });
                        }
                    }
                );

            });

        });
    </script>
@stop
