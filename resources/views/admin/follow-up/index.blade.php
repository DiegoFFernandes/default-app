@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <section class="col-md-4">
                <div class="card">
                    <div class="card-header" style="">
                        <h3 class="card-title" style="text-align: center">Pesquisar Envios Automaticos
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="padding-top: 15px">
                                    <label for="search-number">Nº Nota/Boleto</label>
                                    <input type="number" class="form-control form-control-sm" id="search-number"
                                        placeholder="Número" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group border-bottom">
                                    <label for="search-number">Pesquisa Avançada</label>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-box-tool" data-toggle="collapse"
                                            data-target="#search-advanced"><i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="search-advanced">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cd_pessoa">Cd. Cliente</label>
                                        <input type="number" class="form-control form-control-sm" id="cd_pessoa"
                                            placeholder="Código">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="cd_pessoa">Cpf/CNPJ</label>
                                        <input type="text" class="form-control form-control-sm" id="cpf_cnpj"
                                            placeholder="Cpf/CNPJ">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nm_pessoa">Razão Social</label>
                                        <input type="text" class="form-control form-control-sm" id="nm_pessoa"
                                            placeholder="Nome Pessoa">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ds_email_pessoa">Email</label>
                                        <input type="email" class="form-control form-control-sm" id="ds_email_pessoa"
                                            placeholder="Email Pessoa">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nr_contexto">Tipo de Disparo</label>
                                        <select class="form-control form-control-sm" name="nr_contexto" id="nr_contexto"
                                            style="width: 100%;">
                                            <option value="0" selected="selected">Selecione</option>
                                            @foreach ($contexto as $c)
                                                <option value="{{ $c->NR_CONTEXTO }}">{{ $c->DS_CONTEXTO }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Periodo</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            <input type="text" class="form-control form-control-sm float-right"
                                                id="daterange" value="" autocomplete="off">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success float-right"
                            id="submit-seach">Pesquisar</button>
                    </div>
                </div>
            </section>
            <section class="col-md-8">
                <div class="card">
                    <div class="card-header" style="">
                        <h3 class="card-title">Resultado pesquisa - Envios Automaticos</h3>                       
                    </div>
                    <div class="card-body">
                        <table id="table-search" class="table compact table-font-small table-bordered table-striped">
                        </table>
                    </div>

                </div>
            </section>
        </div>

        <div class="modal fade" id="modal-email" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Disparo Automático</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Essa mensagem é de um disparo automático, cliente deve verificar se não está na caixa de spam
                            ou no lixo eletrônico, caso ele não receber!
                        </div>
                        <div class="form-group">
                            <label for="assunto">Assunto:</label>
                            <input type="text" class="form-control form-control-sm" id="assunto" disabled>
                        </div>
                        <div class="form-group">
                            <label for="from">De:</label>
                            <input type="text" class="form-control form-control-sm" id="from" disabled>
                        </div>
                        <div class="form-group">
                            <label for="to">Para:</label>
                            <input type="text" class="form-control form-control-sm" id="to" disabled>
                        </div>
                        <div class="form-group">
                            <label for="message">Mensagem:</label>
                            <textarea class="form-control form-control-sm" type="textarea" id="message" rows="7" disabled></textarea>
                        </div>
                        <div class="anexos">
                            <label>Anexos:</label>
                            <div id="lista-anexos">
                                <!-- Lista de anexos será carregada aqui -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>




        <!-- /.row -->
    </section>
@stop
@section('css')
    <style>
        /* Ajuste para form-control-sm */
        .select2-container .select2-selection--single {
            font-size: .875rem;
            /* font-size do form-control-sm */
            /* height: calc(1.5em + .5rem + 2px); */
            /* padding: .25rem .5rem; */
        }

        .select2-selection__rendered {
            font-size: .875rem !important;
            /* line-height: 1.5 !important; */
        }

        /* Tamanho da fonte da lista de opções (dropdown) */
        .select2-results__option {
            font-size: .875rem !important;
            /* ou o tamanho que quiser */
            padding: 4px 8px;
            /* opcional, para diminuir o espaçamento */
        }

        .my-small-title {
            font-size: 20px !important;
        }

        .my-small-text {
            font-size: 16px !important;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            const datasSelecionadas = initDateRangePicker();

            $('#nr_contexto').select2({
                theme: 'bootstrap4'
            });

            $('#submit-seach').click(function() {
                let cd_number = $("#search-number").val();
                if (cd_number == "") {
                    $('#search-number').attr('title', 'Código para buscar é obrigatório!').tooltip('show');
                    return false;
                }
                let cd_pessoa = $("#cd_pessoa").val();
                let nm_pessoa = $("#nm_pessoa").val();
                let cpf_cnpj = $("#cpf_cnpj").val();
                let nr_contexto = $("#nr_contexto").val();
                let ds_email_pessoa = $("#ds_email_pessoa").val();

                $("#table-search").DataTable().destroy();

                $("#table-search").DataTable({
                    pagingType: "simple",
                    pageLength: 10,
                    language: {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    ajax: {
                        url: "{{ route('get-search-envio') }}",
                        method: "GET",
                        data: {
                            cd_number: cd_number,
                            cd_pessoa: cd_pessoa,
                            nm_pessoa: nm_pessoa,
                            cpf_cnpj: cpf_cnpj,
                            ds_email: ds_email_pessoa,
                            nr_contexto: nr_contexto,
                            inicio_data: datasSelecionadas.inicioData,
                            fim_data: datasSelecionadas.fimData,
                        },
                    },
                    columns: [{
                            title: 'Descrição',
                            data: 'DS_CONTEXTO',
                            width: '22%',
                        },
                        {
                            title: 'Agenda',
                            data: 'NR_AGENDA',
                            width: '8%',
                            sClass: 'text-center',
                        },
                        {
                            title: 'Cliente',
                            data: 'NM_PESSOA',
                            width: '30%',
                        },
                        {
                            title: 'Data Envio',
                            data: 'DT_ENVIO',
                            width: '15%',
                        },
                        {
                            title: '#',
                            data: 'action',
                            width: '10%',
                        }
                    ]
                });
            });

            $(document).on('click', '.ver-email', function(e) {

                $.ajax({
                    url: "{{ route('get-email-follow') }}",
                    method: 'GET',
                    data: {
                        // _token: {{ csrf_token() }},
                        nr_envio: $(this).data('id'),
                        nr_agenda: $(this).data('nr_agenda'),
                        nr_contexto: $(this).data('nr_contexto')
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('hidden');
                    },
                    success: function(result) {
                        $("#loading").addClass('hidden');
                        $('#modal-email').modal('show');
                        // $('.modal-title').text();
                        // let ds_mensagem = result[0].DS_MENSAGEM;
                        // //ds_mensagem = ds_mensagem.replace(/[#10]/g, "");
                        // $('.modal-body').html(ds_mensagem);
                        $('#assunto').val(result[0].DS_ASSUNTO);
                        $('#from').val(result[0].DS_EMAILREM);
                        $('#to').val(result[0].DS_EMAILDEST);
                        // $('#message').val(text().html());
                        $('#message').val($('<div/>').html(result[0].DS_MENSAGEM).text());

                        let anexos = '';
                        result[0].ANEXOS.forEach(item => {                            
                            anexos +=
                                `<button class="btn btn-secondary btn-sm mr-1">${item.TITULO}</button>`;
                        });
                        $('#lista-anexos').html(anexos);
                    }
                });
            });

            $(document).on('click', '.reenviar-email', function(e) {
                let nr_envio = $(this).data('id');

                Swal.fire({
                    title: 'Atenção!',
                    text: "Deseja realmente reenviar esse email?",                    
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, reenviar!',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        title: 'my-small-title',
                        htmlContainer: 'my-small-text',
                        confirmButton: 'btn btn-primary btn-sm',
                        cancelButton: 'btn btn-secondary btn-sm',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        update_email = 1;
                        ReenviaFollow(nr_envio, update_email);
                    }
                });
            });

            $(document).on('click', '.btn-motivo-falha', function(e) {
                let motivo = $(this).data('motivo');

                Swal.fire({
                    title: 'Motivo da Falha',
                    text: motivo,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Fechar',
                    customClass: {
                        title: 'my-small-title',
                        htmlContainer: 'my-small-text',
                        confirmButton: 'btn btn-secondary btn-sm',
                    },
                    buttonsStyling: false
                });
            });


            function ReenviaFollow(nr_envio, update_email) {
                $.ajax({
                    url: "{{ route('reenvia-follow') }}",
                    method: 'POST',
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        nr_envio: nr_envio,
                        email: update_email
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('hidden');
                    },
                    success: function(response) {
                        $("#loading").addClass('hidden');
                        if (response.error) {
                            msgToastr(response.error, 'warning');
                        } else {
                            msgToastr(response.success, 'success');
                        }
                    }
                });
            }

        });
    </script>
@stop
