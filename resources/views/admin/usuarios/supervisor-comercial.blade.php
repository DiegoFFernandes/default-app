@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            {{-- <div class="col-md-8">
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
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="libera-por-subgrupo">Libera por subgrupo</label>
                                    <div class="form-group">
                                        <select class="form-control" id="libera-por-subgrupo" multiple="multiple"
                                            style="width: 100%;">
                                            <option>Selecione os subgrupos ou deixe em branco</option>
                                            @foreach ($subgrupo as $sg)
                                                <option value="{{ $sg->CD_SUBGRUPO }}">{{ $sg->DS_SUBGRUPO }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- /.card-body -->
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
                                    <label>% Permitida até:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="pc_permitida"
                                            placeholder="%permitida">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="col-md-2" align="center">
                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-danger btn-sm btn-block">Associar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Supervisores Associados</h3>
                        <div class="card-tools pull-right">
                            <button type="button" class="btn btn-danger btn-sm" id="adicionar-supervisor">Adicionar
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table compact table-font-small table-striped table-bordered nowrap"
                            id="table-supervisor" style="width: 100%">
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
    </section>
    {{-- Modal Editar --}}
    <div class="modal" id="ModalAddEdit">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <span class="modal-title">Adicionar Supervisor</span>
                    <button type="button" class="close btn-cancel" data-dismiss="modal" data-keyboard="false"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <div class="col-md-12 d-none" id="div_id_supervisor">
                            <div class="form-group">
                                <label for="name">Cód.</label>
                                <input type="number" class="form-control" name="id" id="id" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Usuário Dashboard</label>
                                <select class="form-control" name="cd_usuario" id="cd_usuario" style="width: 100%">
                                    <option value="" selected="selected">Selecione</option>
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
                                <select class="form-control" name="cd_supervisorcomercial" id="cd_supervisorcomercial"
                                    style="width: 100%;">
                                    <option value="" selected="selected">Selecione</option>
                                    @foreach ($supervisor as $s)
                                        <option value="{{ $s->CD_VENDEDORGERAL }}">{{ $s->NM_SUPERVISOR }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="libera-por-subgrupo">Libera por subgrupo</label>
                                <div class="form-group">
                                    <select class="form-control" id="libera-por-subgrupo" multiple="multiple"
                                        style="width: 100%;">
                                        @foreach ($subgrupo as $sg)
                                            <option value="{{ $sg->CD_SUBGRUPO }}">{{ $sg->DS_SUBGRUPO }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6 d-none" id="div_pc_permitida">
                            <div class="form-group">
                                <label>% Permitida até:</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="pc_permitida" placeholder="%permitida">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-warning btn-update d-none">Editar</button>
                    <button type="button" class="btn btn-sm btn-success btn-add" id="btn-vincular">Vincular</button>
                    <button type="button" class="btn btn-sm btn-danger btn-cancel"
                        data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@stop

@section('style')
    <style>
        /* Para o campo de seleção múltipla */
        .select2-container--bootstrap4 .select2-selection--multiple {
            font-size: 10px !important;
        }

        /* Para a seleção de item único */
        .select2-container--bootstrap4 .select2-selection--single {
            font-size: 10px !important;
        }

        /* Para as opções dentro do dropdown */
        .select2-results__option {
            font-size: 10px !important;
        }
    </style>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            document.querySelectorAll('input[name="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value == "1") {
                        $('#div_pc_permitida').removeClass('d-none');
                    } else if (this.value == "0") {
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

            $('#libera-por-subgrupo').select2({
                theme: 'bootstrap4',
                placeholder: "Selecione os subgrupos ou deixe em branco",
                allowClear: true,
                multiple: true,
                width: '100%'
            });

            $('#adicionar-supervisor').click(function() {
                $('.modal').modal('show');
                // Resetar os campos do modal
                $('#div_id_supervisor').addClass('d-none');
                $('#cd_supervisorcomercial').val('').prop('disabled', false).trigger('change');
                $('#cd_usuario').val('').prop('disabled', false).trigger('change');

                $('#libera-por-subgrupo').val([]).trigger('change');

                $('#pc_permitida').val('');
                $('#radio4').prop('checked', true);
                $('#div_pc_permitida').addClass('d-none');

                $('.modal-title').text('Adicionar Supervisor');
                $('.btn-update').addClass('d-none');
                $('.btn-add').removeClass('d-none');
                $('.modal').modal('show');
            });


            $('#btn-vincular').click(function() {
                let ds_supervisorcomercial = $("#cd_supervisorcomercial option:selected").text();
                let cd_supervisorcomercial = $('#cd_supervisorcomercial').val();
                let cd_usuario = $('#cd_usuario').val();
                let pc_permitida = $('#pc_permitida').val();
                let libera_acima_param = $('input[name="radio"]:checked').val();
                let subgrupos = $('#libera-por-subgrupo').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('get-supervisor-comercial.create') }}",
                    data: {
                        cd_supervisorcomercial: cd_supervisorcomercial,
                        cd_usuario: cd_usuario,
                        ds_supervisorcomercial: ds_supervisorcomercial,
                        pc_permitida: pc_permitida,
                        libera_acima_param: libera_acima_param,
                        subgrupos: subgrupos
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('hidden');
                    },
                    success: function(response) {
                        $("#loading").addClass('hidden');

                        if (response.error) {
                            msgToastr(response.error, 'warning');
                        } else if (response.errors) {
                            msgToastr(response.errors, 'warning');
                        } else {
                            $('.modal').modal('hide');
                            msgToastr(response.message, 'success');
                            $('#table-supervisor').DataTable().ajax.reload();
                            $('#cd_supervisorcomercial').val('').trigger('change');
                            $('#cd_usuario').val('').trigger('change');
                            $('#pc_permitida').val('');
                            $('input[name="radio"]').prop('checked', false);
                            $('#libera-por-subgrupo').val('').trigger('change');
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
                        name: 'id',
                        title: 'Cód.',
                        width: '5%'
                    },
                    {
                        data: 'cd_usuario',
                        name: 'cd_usuario',
                        visible: false,
                        title: 'Cód. Usuário'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Usuário'
                    },
                    {
                        data: 'cd_supervisorcomercial',
                        name: 'cd_supervisorcomercial',
                        visible: false,
                    },
                    {
                        data: 'ds_supervisorcomercial',
                        name: 'ds_supervisorcomercial',
                        title: 'Supervisor'
                    },
                    {
                        data: 'subgrupos',
                        name: 'subgrupos',
                        title: 'Subgrupos'
                    },
                    {
                        data: 'pc_permitida',
                        name: 'pc_permitida',
                        title: '% Permitida'
                    },
                    {
                        data: 'ds_libera_acima',
                        name: 'ds_libera_acima',
                        title: 'Libera Acima?'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        title: 'Ações',
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

                $('#div_id_supervisor').removeClass('d-none');
                $('.modal-title').text('Editar Supervisor');
                $('.btn-add').addClass('d-none');
                $('.btn-update').removeClass('d-none');

                $('#id').val(rowData.id);
                $('#cd_supervisorcomercial').val(rowData.cd_supervisorcomercial).prop('disabled', true)
                    .trigger('change');
                $('#cd_usuario').val(rowData.cd_usuario).prop('disabled', true).trigger('change');

                if (rowData.libera_acima_param == 1) {
                    $('#radio3').prop('checked', true);
                    $('#div_pc_permitida').removeClass('d-none');
                } else {
                    $('#radio4').prop('checked', true);
                    $('#div_pc_permitida').addClass('d-none');
                }
                $('#pc_permitida').val(rowData.pc_permitida);

                $('#libera-por-subgrupo').val(rowData.cd_subgrupos.split(',')).trigger('change');


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
                        let id = $('#id').val();
                        let ds_supervisorcomercial = $(
                            "#cd_supervisorcomercial option:selected").text();
                        let cd_supervisorcomercial = $('#cd_supervisorcomercial').val();
                        let cd_usuario = $('#cd_usuario').val();
                        let pc_permitida = $('#pc_permitida').val();
                        let libera_acima_param = $('input[name="radio"]:checked').val();
                        let subgrupos = $('#libera-por-subgrupo').val();
                        $.ajax({
                            type: 'GET',
                            url: '{{ route('edit-supervisor-usuario') }}',
                            data: {
                                id: id,
                                cd_supervisorcomercial: cd_supervisorcomercial,
                                cd_usuario: cd_usuario,
                                ds_supervisorcomercial: ds_supervisorcomercial,
                                pc_permitida: pc_permitida,
                                libera_acima_param: libera_acima_param,
                                subgrupos: subgrupos,
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
