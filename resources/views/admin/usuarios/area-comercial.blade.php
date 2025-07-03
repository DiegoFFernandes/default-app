@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- /.box-header -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Area Junsoft</label>
                                        <select class="form-control select2" id="cd_areacomercial" style="width: 100%;">
                                            <option selected="selected">Selecione</option>
                                            @foreach ($area as $r)
                                                <option value="{{ $r->CD_AREACOMERCIAL }}">{{ $r->DS_AREACOMERCIAL }}
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
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Areas Associadas</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="card-body">
                        <table class="table display table-sm" id="table-area" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Cd. Usúario</th>
                                    <th>Usúario</th>
                                    <th>Cd. Area</th>
                                    <th>Area</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        {{-- Modal Editar --}}
        <div class="modal" id="CreatePessoaModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Editar Area</h4>
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
                                <label>Area Junsoft</label>
                                <select class="form-control" name="cd_areacomercial_modal" id="cd_areacomercial_modal"
                                    style="width: 100%;">
                                    @foreach ($area as $r)
                                        <option value="{{ $r->CD_AREACOMERCIAL }}">{{ $r->DS_AREACOMERCIAL }}
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
    </section>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#cd_areacomercial').select2({
                theme: 'bootstrap4'
            });
            $('#cd_areacomercial_modal').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario_modal').select2({
                theme: 'bootstrap4'
            });
            $('#btn-vincular').click(function() {
                let ds_areacomercial = $("#cd_areacomercial option:selected").text()
                let cd_areacomercial = $('#cd_areacomercial').val();
                let cd_usuario = $('#cd_usuario').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('get-area-comercial.create') }}",
                    data: {
                        cd_areacomercial: cd_areacomercial,
                        cd_usuario: cd_usuario,
                        ds_areacomercial: ds_areacomercial
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('hidden');
                    },
                    success: function(result) {
                        $("#loading").addClass('hidden');
                        if (result.errors) {
                            msgToastr(result.errors, 'warning');
                        } else {
                            msgToastr(result.success, 'success');
                            $('#table-area').DataTable().ajax.reload()
                        }
                    }
                });
            });
            var dataTable = $('#table-area').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-table-area-usuario') }}',
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
                        data: 'cd_areacomercial',
                        name: 'cd_areacomercial',
                        visible: false,
                    },
                    {
                        data: 'ds_areacomercial',
                        name: 'ds_areacomercial'
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
            $('#table-area').on('click', '.btn-edit', function() {
                var rowData = dataTable.row($(this).parents('tr')).data();
                // console.log(rowData);
                $('#id').val(rowData.id);
                $('#cd_areacomercial_modal').val(rowData.cd_areacomercial).trigger('change');
                $('#cd_usuario_modal').val(rowData.cd_usuario).trigger('change');
                $('.modal').modal('show');
            });
            $('.btn-update').click(function() {
                toastr.warning(
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
                                    cd_areacomercial = $('#cd_areacomercial_modal').val(),
                                    ds_areacomercial = $(
                                        "#cd_areacomercial_modal option:selected").text(),
                                    cd_usuario = $('#cd_usuario_modal').val();

                                // console.log(cd_areacomercial);
                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('edit-area-usuario') }}',
                                    data: {
                                        id: id,
                                        cd_areacomercial: cd_areacomercial,
                                        cd_usuario: cd_usuario,
                                        ds_areacomercial: ds_areacomercial,
                                        _token: $('#token').val(),
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
                                            $('#table-area').DataTable().ajax
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

                toastr.warning(
                    "<button type='button' id='confirmationButtonYes' class='btn btn-success clear'>Sim</button>" +
                    "<button type='button' id='confirmationButtonNo' class='btn btn-primary clear'>Não</button>",
                    'Deseja realmente excluir o item ' + deleteId + ' ?', {
                        closeButton: false,
                        allowHtml: true,
                        progressBar: false,
                        timeOut: 0,
                        positionClass: "toast-top-center",
                        onShown: function(toast) {
                            $("#confirmationButtonYes").click(function() {
                                $.ajax({
                                    url: "{{ route('area-usuario.delete') }}",
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
                                            msgToastr(result.success,
                                                'success');
                                            $('#table-area').DataTable().ajax
                                                .reload()
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
