@extends('layouts.master')

@section('title', 'Item Lote Estoque')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-6 col-xs-12">
                <div class="card card-dark card-outline">
                    <div class="card-header with-border">
                        <h3 class="card-title">{{ $title_page }}</h3>
                        <div class="card-tools float-right">
                            <span class="label label-danger" id="qtd_itens_coleta">{{ $qtde_coleta }} Itens</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input type="hidden" class="form-control form-control-sm" id="tp_produto"
                            value="{{ $lote->tp_produto }}">
                        <input type="hidden" class="form-control form-control-sm" id="id_marca"
                            value="{{ $lote->id_marca }}">
                        <div class="row">
                            <div class="col-4 col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label class="small" for="lote">Cód. Lote</label>
                                    <input type="text" class="form-control form-control-sm" id="id_lote"
                                        value="{{ $lote->id }}" disabled>
                                </div>
                            </div>
                            {{-- <div class="col-md-3 hidden-xs">
                                <div class="form-group">
                                    <label class="small" for="ds_lote">Descrição</label>
                                    <input type="text" class="form-control form-control-sm" id="ds_lote"
                                        value="{{ $lote->descricao }}" disabled>
                                </div>
                            </div> --}}
                            <div class="col-8 col-md-5 hidden-xs">
                                <div class="form-group">
                                    <label class="small" for="responsavel">Responsável</label>
                                    <input type="text" class="form-control form-control-sm" id="responsavel"
                                        value="{{ $lote->nm_usuario }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-5  col-xs-6">
                                <div class="form-group">
                                    <label class="small" for="created_at">Criado em:</label>
                                    <input type="text" class="form-control form-control-sm" id="created_at"
                                        value="{{ $lote->created_at_formatado }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-6">
                                <div class="form-group">
                                    <label class="small" for="cd_barras">Cód. Barras Prod.</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm pula" id="cd_barras"
                                            placeholder="Cód. Barras">
                                        <button class="btn btn-sm btn-outline-secondary" id="search-cd-barras" type="button"
                                            data-toggle="modal" data-target="#modal-search">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-3 hidden-xs">
                                <div class="form-group">
                                    <label class="small" for="cd_item">Cód. Produto</label>
                                    <input type="text" class="form-control form-control-sm" id="cd_item" disabled
                                        required>
                                </div>
                            </div>
                            <div class="col-8 col-md-5">
                                <div class="form-group">
                                    <label class="small" for="ds_produto">Descrição Produto</label>
                                    <input type="text" class="form-control form-control-sm" id="ds_produto" disabled>
                                </div>
                            </div>
                            @if ($lote->tp_produto == 1)
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="small" for="cd_barras_peso">Cód. Barras Peso</label>
                                        <input type="text" class="form-control form-control-sm pula" id="cd_barras_peso"
                                            placeholder="Cód. Peso">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="small" for="peso">Peso Kg</label>
                                        <input type="text" class="form-control form-control-sm" id="peso" disabled>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="submit-add-item" class="btn btn-secondary btn-xs float-right">Adicionar item</button>
                    </div>
                </div>

            </div>
            <div class="col-12 col-md-6 col-xs-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#table-itens" data-toggle="pill"
                                    aria-expanded="true">Itens</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#table-resumo" data-toggle="pill"
                                    aria-expanded="false">Resumo</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#finalizar" data-toggle="pill"
                                    aria-expanded="false">Finalizar</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="table-itens">
                                <table id="table-item-lote"
                                    class="table compact table-striped table-bordered table-font-small">
                                </table>
                            </div>
                            <div class="tab-pane" id="table-resumo">
                                <table id="table-item-group"
                                    class="table compact table-striped table-bordered table-font-small"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Cód. Item</th>
                                            <th>Descrição</th>
                                            <th>Qtd.</th>
                                            <th>Kg/Unid.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <th style="text-align: right">Total</th>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="tab-pane" id="finalizar">
                                <div class="card">
                                    <div class="card-body">
                                        <button type="button" id="finalizar-lote" data-id="{{ $lote->id }}"
                                            class="btn btn-success btn-xs center-block">Finalizar Lote</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <!-- /.content -->
    {{-- Modal de Pesquisa de Produto --}}
    <div class="modal fade" id="modal-search" role="dialog" aria-labelledby="ModalSearch">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">Pesquisar por descrição</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span
                            aria-hidden="true">&times;</span></button>

                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select class="form-control form-control-sm" id="item" style="width: 100%"></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        window.route = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getItemLote: "{{ route('get-item-lote', ':cd_barras') }}",
            addItemLote: "{{ route('add-item-lote.store') }}",
            getItensLote: "{{ route('estoque.get-itens-lote') }}",
            deleteItemLote: "{{ route('delete-item-lote') }}",
            finishLote: "{{ route('estoque.finish-lote') }}",
            getResumeItensLote: "{{ route('estoque.get-resume-itens-lote') }}",
            searchProduct: "{{ route('search-product') }}",
        }

        var pesoitem, subgrupo;
        var subgrupo = $('#id_subgrupo').val();
        var marca = $('#id_marca').val();

        var id_lote = $("#id_lote").val();
        let token = $("meta[name='csrf-token']").attr("content");


        // $("#cd_barras").inputmask({
        //     mask: ['A99999999', '9999999999999']
        // });

        // $("#cd_barras_peso").inputmask({
        //     mask: ['99.99', 'Q99.99', '9Q99.99', '999999']
        // });

        $("#cd_barras").on("keydown input blur", function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            var cd_barras = $("#cd_barras").val();
            url = window.route.getItemLote;
            url = url.replace(':cd_barras', cd_barras);
            if (keycode == '9' || keycode == '13' || event.type == "focusout") {
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(result) {
                        if (result.error) {
                            msgToastr("Cód: " + cd_barras + " - " + result.error,
                                "warning");
                            return false;
                        } else {
                            $("#ds_produto").val(result.ds_item);
                            $("#cd_item").val(result.cd_item);
                            //Essa variavel alimenta a condição #cd_barras_peso
                            pesoitem = parseFloat(result.ps_liquido);
                        }
                    }
                });
            }
        });

        $("#cd_barras_peso").on("keydown input blur", function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if (keycode == '9' || keycode == '13' || event.type == "focusout") {
                processarCodigoPeso(marca);
            }
        });

        $("#submit-add-item").on('click', function() {

            let cd_item = $("#cd_item").val();
            let cd_peso = $("#peso").val();
            if (cd_item == "") {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Informe o código do produto antes de ler o código de barras do peso!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (cd_peso == "") {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Informe o peso antes de adicionar o item!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            $.ajax({
                method: "POST",
                url: window.route.addItemLote,
                data: {
                    _token: $('#token').val(),
                    cd_lote: id_lote,
                    cd_item: $("#cd_item").val(),
                    peso: $("#peso").val(),
                    tp_produto: $("#tp_produto").val(),
                    id_marca: marca
                },
                beforeSend: function() {
                    $("#loading").removeClass('hidden');
                },
                success: function(result) {

                    $("#loading").addClass('hidden');
                    if (result.errors) {
                        msgToastr(result.errors, 'warning');

                    } else {
                        //console.log(result);  
                        $("#cd_barras").val("");
                        $('#cd_item').val("");
                        $('#peso').val("");
                        $('#cd_barras_peso').val("");
                        msgToastr(result.success, 'success');
                        $('#table-item-lote').DataTable().ajax.reload();
                        $('#qtd_itens_coleta').text(result.qtde + " Itens");
                    }
                }
            });
        });

        $("#finalizar-lote").on('click', function() {
            let id_lote = $(this).data();
            $.ajax({
                method: "POST",
                url: "{{ route('estoque.finish-lote') }}",
                data: {
                    id: id_lote['id'],
                    _token: token,
                },
                beforeSend: function() {
                    $("#loading").removeClass('hidden');
                },
                success: function(result) {
                    $("#loading").addClass('hidden');
                    msgToastr(result.success, "success");
                    window.location.replace("{{ route('entrada-estoque.index') }}");
                }
            });
        });

        $('#cd_barras').focus();

        $('.pula').keypress(function(e) {
            var tecla = (e.keyCode ? e.keyCode : e.which);
            if (tecla == 13) {
                campo = $('.pula');
                indice = campo.index(this);
                if (campo[indice + 1] != null) {
                    proximo = campo[indice + 1];
                    proximo.focus();
                }
            }
        });

        $("#table-item-lote").DataTable({
            language: {
                url: window.route.languageDatatables,
            },
            pagingType: "simple",
            responsive: true,
            "order": [
                [3, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 75, -1],
                [10, 25, 50, 75, "Todos"]
            ],
            pageLength: 10,
            ajax: {
                url: window.route.getItensLote,
                data: {
                    id_lote: id_lote
                }
            },
            columns: [{
                    data: 'cd_item',
                    name: 'cd_item',
                    title: 'Cód. Item'
                },
                {
                    data: 'ds_item',
                    name: 'ds_item',
                    title: 'Descrição'
                },
                {
                    data: 'peso',
                    name: 'peso',
                    title: 'Peso',
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Responsável'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Criado em',
                },
                {
                    data: 'actions',
                    name: 'actions',
                    title: 'Ações',
                },

            ],
            createdRow: (row, data, dataIndex, cells) => {
                $(cells[2]).css('background-color', data.ps);
            },
        });

        $("#table-item-lote").on('click', '.delete', function() {
            let rowId = $(this).data();
            toastr.error(
                "<button type='button' id='confirmationButtonYes' class='btn btn-danger'>Sim</button><button type='button' id='confirmationButtonNo' class='btn btn-warning'>Não</button>",
                'Deletar item?', {
                    closeButton: false,
                    allowHtml: true,
                    onShown: function(toast) {
                        $("#confirmationButtonYes").click(function() {
                            $.ajax({
                                method: "DELETE",
                                url: "{{ route('delete-item-lote') }}",
                                data: {
                                    id: rowId['id'],
                                    _token: token
                                },
                                beforeSend: function() {
                                    $("#loading").removeClass('hidden');
                                },
                                success: function(result) {
                                    $("#loading").addClass('hidden');
                                    msgToastr(result.success, "success");
                                    location.reload()
                                }
                            });
                        });
                    }
                });
        });

        $('.nav-tabs a[href="#table-resumo"]').on('click', function() {
            $('#table-item-group').DataTable().destroy();
            $("#table-item-group").DataTable({
                language: {
                    url: window.route.languageDatatables,
                },
                pagingType: "simple",

                lengthMenu: [
                    [10, 25, 50, 75, -1],
                    [10, 25, 50, 75, "Todos"]
                ],
                pageLength: 10,
                ajax: {
                    url: window.route.getResumeItensLote,
                    data: {
                        id_lote: id_lote
                    }
                },
                columns: [{
                    data: 'cd_item',
                    name: 'cd_item',
                    title: 'Cód. Item'
                }, {
                    data: 'ds_item',
                    name: 'ds_item',
                    title: 'Descrição'
                }, {
                    data: 'qtditem',
                    name: 'qtditem',
                    className: 'text-center',
                    title: 'Qtd.',
                }, {
                    data: 'peso',
                    name: 'peso',
                    title: 'KG/Unid.',
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Total da coluna "qtditem"
                    var totalQtdItem = api.column(2, {
                        page: 'current'
                    }).data().sum();

                    var totalPesoItem = api.column(3, {
                        page: 'current'
                    }).data().sum();

                    // Adiciona o valor no rodapé da coluna "qtditem"
                    $(api.column(2).footer()).html(totalQtdItem);
                    $(api.column(3).footer()).html(totalPesoItem.toFixed(2));
                }

            });
        });

        $('#modal-search').on('shown.bs.modal', function() {
            $('#item').select2({
                    placeholder: "Ex: BANDA 240",
                    allowClear: true,
                    ajax: {
                        url: '{{ route('search-product') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.ds_item,
                                        id: item.cd_item,
                                        cd_barras: item.cd_codbarraemb
                                    }
                                })

                            };
                        },
                        cache: true
                    }
                })
                .change(function(el) {
                    var data = $(el.target).select2('data');
                    $('#ds_produto').val(data[0].text);
                    $('#cd_item').val(data[0].id);
                    $('#cd_barras').val(data[0].cd_barras);
                });
        });

        function processarCodigoPeso(marca) {
            let codigoLido = $("#cd_barras_peso").val();
            if (marca == 1) {
                if (codigoLido.length > 6 && codigoLido.length < 31) {
                    Swal.fire({
                        title: 'Atenção',
                        text: 'O código de barras lido não é válido para essa marca, leia o código correto!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                } else if (codigoLido.length > 32) {
                    Swal.fire({
                        title: 'Atenção',
                        text: 'O código de barras lido não é válido para essa marca, leia o código correto!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                } else if (codigoLido.length == 5) {
                    console.log(codigoLido);
                    let peso = parseFloat(codigoLido.replace(',', '.')).toFixed(2);
                    return $("#peso").val(peso);
                }

                let partes = codigoLido.trim().split(/\s+/);
                let peso = (parseInt(partes[1].substring(0, 4)) / 100).toFixed(2);

                return $("#peso").val(peso);
            }
        }
    </script>
@endsection
