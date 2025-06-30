@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <span class="text">Adicionar Itens</span><span
                            class="badge badge-secondary ml-2">{{ $lote }}</span>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm" id="novoLoteExpedicaoBtn" data-toggle="modal"
                                data-target="#CodigoBarrasModal">
                                Código de barras
                            </button>
                        </div>
                    </div>
                    <form id="itemLoteExpedicaoForm">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @csrf
                            <input type="hidden" name="lote" id="lote" value="{{ $lote }}">
                            <input type="hidden" name="idempresa" id="idempresa" value="{{ $idempresa }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="nr_ordem">Ordem</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="nr_ordem" id="nr_ordem"
                                                placeholder="Nr Ordem">
                                            <div class="input-group-append" style="flex: 0 0 60px;">
                                                <button type="button" id="btnSearchOrdem"
                                                    class="btn-secondary input-group-text w-100 d-flex justify-content-center align-items-center">
                                                    Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none row" id="informationOrdem">
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="nm_vendedor">Vendedor</label>
                                            <input type="text" class="form-control" name="nm_vendedor" id="nm_vendedor"
                                                placeholder="Vendedor" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="nm_servico">Serviço</label>
                                            <input type="text" class="form-control" name="nm_servico" id="nm_servico"
                                                placeholder="Serviço" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-none" id="btnInserirItem">
                                <button class="btn btn-sm btn-secondary float-right"
                                    form="itemLoteExpedicaoForm">Inserir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <span class="text">Item Adicionados</span><span
                            class="badge badge-secondary ml-2">{{ $lote }}</span>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped compact" id="itemLoteExpedicaoTable"
                                style="width:100%; font-size:12px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="CodigoBarrasModal" tabindex="-1" role="dialog"
            aria-labelledby="CodigoBarrasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="text">Código Barras</span>
                        <button type="button" class="close closeCam" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pt-2 pb-1">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <div class="mt-2" id="reader" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-secondary closeCam" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        /* Reduz altura e fonte do Select2 com Bootstrap 4 */
        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.8125rem + 2px);
            /* semelhante ao form-control-sm */
            font-size: 12px;
            /* 14px */

        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var scanner = null;
            // Initialize DataTable
            $('#itemLoteExpedicaoTable').DataTable({
                responsive: true,
                "paging": true,
                "bInfo": false,
                "pagingType": "simple",
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                ajax: {
                    url: "{{ route('get-list-item-lote-expedicao') }}",
                    data: function(d) {
                        d.lote = "{{ $lote }}";
                        d.idempresa = "{{ $idempresa }}";
                    }
                },
                columns: [{
                        data: 'ID',
                        name: 'ID',
                        title: 'ID',
                        visible: false
                    },
                    {
                        data: 'NRORDEM',
                        name: 'NRORDEM',
                        title: 'OP'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO',
                        title: 'Serviço'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: '#',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
            });

            $('#vendedor').select2({
                placeholder: "Buscar Vendedor",
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true,
                minimumInputLength: 2,
                dropdownParent: $('#loteExpedicaoModal'),
                ajax: {
                    url: " {{ route('get-search-vendedor') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.NM_VENDEDOR,
                                    id: item.ID
                                }
                            })

                        };
                    },
                    cache: true
                }
            }).change(function(el) {
                var data = $(el.target).select2('data');
                $('#cd_vendedor').val(data[0].id || '');
            });

            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 150
                }
            };

            $('#novoLoteExpedicaoBtn').on('click', function() {

                let leituraFeita = false;

                scanner = new Html5QrcodeScanner(
                    "reader", config,
                    false
                );

                scanner.render(function(decodedText) {
                    if (leituraFeita) return;
                    leituraFeita = true;

                    var nr_ordem = decodedText;
                    var empresa = $('#idempresa').val();

                    searchOrdem(nr_ordem, empresa);

                    if (scanner) {
                        scanner.clear().then(() => {
                            console.log("Câmera encerrada com sucesso.");
                        }).catch(err => {
                            console.error("Erro ao fechar a câmera:", err);
                        });
                    }

                });
            });
            $('.closeCam').on('click', function() {
                if (scanner) {
                    scanner.clear().then(() => {
                        console.log("Câmera encerrada com sucesso.");
                    }).catch(err => {
                        console.error("Erro ao fechar a câmera:", err);
                    });
                }
            });
            $('#btnSearchOrdem').on('click', function() {
                var nr_ordem = $('#nr_ordem').val();
                var empresa = $('#idempresa').val();

                searchOrdem(nr_ordem, empresa);
            });

            $('#itemLoteExpedicaoForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('post-store-item-lote-expedicao') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#informationOrdem').addClass('d-none');
                            $('#btnInserirItem').addClass('d-none');
                            $('#itemLoteExpedicaoForm')[0].reset();
                            $('#itemLoteExpedicaoTable').DataTable().ajax.reload();
                            msgToastr(response.message, 'success');
                        } else {
                            msgToastr(response.errors, 'error');
                        }

                    },
                    error: function(xhr) {
                        msgToastr(xhr.responseJSON.errors, 'error');
                    }
                });
            });

            $(document).on('click', '.btnDelete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var lote = "{{ $lote }}";
                var idempresa = "{{ $idempresa }}";
                var nrOrdem = $(this).data('ordem');

                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Deseja excluir a ordem " + nrOrdem + "?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete-item-lote-expedicao') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                nr_ordem: nrOrdem,
                                lote: lote,
                                idempresa: idempresa
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#itemLoteExpedicaoTable').DataTable().ajax.reload();
                                    msgToastr(response.message, 'success');
                                } else {
                                    msgToastr(response.errors, 'error');
                                }
                            },
                            error: function(xhr) {
                                msgToastr(xhr.responseJSON.errors, 'error');
                            }
                        });
                    }
                });
            });

            function searchOrdem(nr_ordem, empresa) {
                if (nr_ordem) {
                    $.ajax({
                        url: "{{ route('search-ordem-producao') }}",
                        type: 'GET',
                        data: {
                            nr_ordem: nr_ordem,
                            empresa: empresa
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#informationOrdem').removeClass('d-none');
                                $('#btnInserirItem').removeClass('d-none');
                                $('#CodigoBarrasModal').modal('hide');
                                $('#nr_ordem').val(response.data.NRORDEM);
                                $('#idempresa').val(response.data.IDEMPRESA);
                                // $('#nm_cliente').val(response.data.NM_PESSOA);
                                $('#nm_vendedor').val(response.data.NM_VENDEDOR);
                                $('#nr_pedido').val(response.data.PEDIDO);
                                $('#nm_servico').val(response.data.DSSERVICO);
                                $('#itemLoteExpedicaoTable').DataTable().ajax.reload();
                            } else {
                                msgToastr(response.errors, 'error');
                                $('#CodigoBarrasModal').modal('hide');
                                $('#informationOrdem').addClass('d-none');

                            }
                        },
                        error: function(xhr) {
                            msgToastr(xhr.responseJSON.errors, 'error');
                            $('#CodigoBarrasModal').modal('hide');
                        }
                    });
                } else {
                    msgToastr('Por favor, informe o número da ordem.', 'warning');
                }
            }
        });
    </script>

@stop
