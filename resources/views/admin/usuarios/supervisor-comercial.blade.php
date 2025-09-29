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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Supervisor Junsoft</label>
                                    <select class="form-control select2" id="cd_supervisorcomercial" style="width: 100%;">
                                        <option selected="selected">Selecione</option>
                                        @foreach ($supervisor as $s)
                                            <option value="{{ $s->CD_VENDEDORGERAL }}">{{ $s->NM_SUPERVISOR }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Libera Acima Param?</label>
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="radio3" name="radio"
                                                value="1">
                                            <label class="form-check-label" for="radio3">Sim</label><br>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="radio4" name="radio"
                                                value="0" checked>
                                            <label class="form-check-label" for="radio4">Não</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-none" id="div_pc_permitida">
                                <div class="form-group">
                                    <label>% Permitida</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="pc_permitida"
                                            placeholder="%permitida">
                                    </div>
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
                            <h3 class="card-title">Supervisores Associados</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table compact table-font-small table-striped table-bordered nowrap" id="table-supervisor" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Cód.</th>
                                        <th>Cd. Usúario</th>
                                        <th>Usúario</th>
                                        <th>Cd. Supervisor</th>
                                        <th>Supervisor</th>
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
                    <span class="modal-title">Editar Supervisor</span>
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
                            <label>Supervisor Junsoft</label>
                            <select class="form-control" name="cd_supervisorcomercial_modal"
                                id="cd_supervisorcomercial_modal" style="width: 100%;">
                                @foreach ($supervisor as $s)
                                    <option value="{{ $s->CD_VENDEDORGERAL }}">{{ $s->NM_SUPERVISOR }}
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
            document.querySelectorAll('input[name="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === "1") {
                        $('#div_pc_permitida').removeClass('d-none');
                    } else if (this.value === "2") {
                        $('#div_pc_permitida').addClass('d-none');
                    }
                });
            });
            $('#cd_supervisorcomercial').select2({
                theme: 'bootstrap4'
            });
            $('#cd_supervisorcomercial_modal').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario').select2({
                theme: 'bootstrap4'
            });
            $('#cd_usuario_modal').select2({
                theme: 'bootstrap4'
            });
            $('#btn-vincular').click(function() {
                let ds_supervisorcomercial = $("#cd_supervisorcomercial option:selected").text();
                let cd_supervisorcomercial = $('#cd_supervisorcomercial').val();
                let cd_usuario = $('#cd_usuario').val();
                let pc_permitida = $('#pc_permitida').val();
                let libera_acima_param = $('input[name="radio"]:checked').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('get-supervisor-comercial.create') }}",
                    data: {
                        cd_supervisorcomercial: cd_supervisorcomercial,
                        cd_usuario: cd_usuario,
                        ds_supervisorcomercial: ds_supervisorcomercial,
                        pc_permitida: pc_permitida,
                        libera_acima_param: libera_acima_param
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
                            $('#table-supervisor').DataTable().ajax.reload()
                        }
                    }
                });
            });
            var dataTable = $('#table-supervisor').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-table-supervisor-usuario') }}',
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
                        data: 'cd_supervisorcomercial',
                        name: 'cd_supervisorcomercial',
                        visible: false,
                    },
                    {
                        data: 'ds_supervisorcomercial',
                        name: 'ds_supervisorcomercial'
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
                scrollCollapse: true,
                pageLength: 20,
            });
            //dataTable e um variavel que tras a informação da tabela.
            $('#table-supervisor').on('click', '.btn-edit', function() {
                var rowData = dataTable.row($(this).parents('tr')).data();
                $('#id').val(rowData.id);
                $('#cd_supervisor_comercial_modal').val(rowData.cd_supervisor_comercial).trigger('change');
                $('#cd_usuario_modal').val(rowData.cd_usuario).trigger('change');
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
                        let id = $('#id').val(),
                            cd_supervisor_comercial = $(
                                '#cd_supervisor_comercial_modal')
                            .val(),
                            ds_regiaocomercial = $(
                                "#cd_supervisor_comercial_modal option:selected")
                            .text(),
                            cd_usuario = $('#cd_usuario_modal').val();
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('edit-regiao-usuario') }}',
                            data: {
                                id: id,
                                cd_supervisor_comercial: cd_supervisor_comercial,
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
                                    $('#table-supervisor').DataTable()
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
                            url: "{{ route('supervisor-usuario.delete') }}",
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
                                    $('#table-supervisor').DataTable()
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
