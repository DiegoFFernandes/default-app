@extends('layouts.master')

@section('title', 'Pedido Pneu')

@section('content')
    <section class="content">
        <!-- row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buscar Pedido</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="small" for="pedido">Pedido:</label>
                                    <input type="text" class="form-control form-control-sm" id="pedido" name="pedido"
                                        placeholder="Digite o número do pedido">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="small" for="ordem">Ordem:</label>
                                    <input type="text" class="form-control form-control-sm" id="ordem" name="ordem"
                                        placeholder="Digite o número da ordem">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="float-right">
                            <button type="button" class="btn btn-primary btn-xs" id="btn-pesquisar">Pesquisar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <div class="row d-none" id="resultado-pesquisa">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Resultados da Pesquisa</h3>
                    </div>
                    <div class="card-body">
                        <table id="table-pedido-pneus"
                            class="table table-bordered table-striped compact table-font-small table-responsive">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>

    <div class="modal" id="modal-detalhes-pneu" tabindex="-1" role="dialog" aria-labelledby="modal-detalhes-pneu-label"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-detalhes-pneu-label">Editar Pneu</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detalhes-pneu-content">
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="nrSerie">Série:</label>
                                <input type="text" class="form-control form-control-sm" id="nrSerie" name="nrSerie">
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="fogo">Fogo:</label>
                                <input type="number" class="form-control form-control-sm" id="fogo" name="fogo">
                            </div>
                        </div><div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="dot">Dot:</label>
                                <input type="number" class="form-control form-control-sm" id="dot" name="dot">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-xs" id="btn-atualizar-pneu"
                        data-dismiss="modal">Atualizar</button>
                    <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>    
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        var tablePedidoPneus;

        $(document).on('click', '#btn-pesquisar', function() {
            let pedido = $('#pedido').val();
            let ordem = $('#ordem').val();

            if (tablePedidoPneus) {
                tablePedidoPneus.destroy();
            }
            tablePedidoPneus = $('#table-pedido-pneus').DataTable({
                processing: false,
                serverSide: false,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: '{{ route('search-pedido-pneu') }}',
                    type: 'GET',
                    paginType: "simple",
                    data: {
                        pedido: pedido,
                        ordem: ordem
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {

                            let response = xhr.responseJSON;

                            let mensagens = '<ul class="text-left">';

                            response.errors.forEach(function(erro) {
                                mensagens += `<li>${erro}</li>`;
                            });

                            mensagens += '</ul>';

                            Swal.fire({
                                icon: 'warning',
                                title: 'Erro de validação',
                                html: mensagens
                            });
                        }
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        title: 'Ações',
                        width: '1%',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        title: 'Emp.',
                        width: '1%',
                    },
                    {
                        data: 'ID_PEDIDO',
                        name: 'ID_PEDIDO',
                        className: 'text-center',
                        title: 'Pedido'
                    },
                    {
                        data: 'NR_ORDEM',
                        name: 'NR_ORDEM',
                        className: 'text-center',
                        title: 'Ordem'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente'
                    },
                    {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        title: 'Item'
                    },
                    {
                        data: 'NRSERIE',
                        name: 'NRSERIE',
                        className: 'text-center',
                        title: 'Série'
                    },
                    {
                        data: 'NRFOGO',
                        name: 'NRFOGO',
                        className: 'text-center',
                        title: 'Fogo'
                    },
                    {
                        data: 'NRDOT',
                        name: 'NRDOT',
                        className: 'text-center',
                        title: 'Dot'
                    },

                ],
                footerCallback: function(row, data, start, end, display) {
                    $('#resultado-pesquisa').removeClass('d-none');
                }

            });
        });
        $(document).on('click', '.btn-editar-pneu', function() {
            let idPneu = $(this).data('id');
            let rowData = tablePedidoPneus.row($(this).parents('tr')).data();
            let nrSerie = rowData.NRSERIE;
            let fogo = rowData.NRFOGO;
            let dot = rowData.NRDOT;

            $('#nrSerie').val(nrSerie);
            $('#fogo').val(fogo);
            $('#dot').val(dot);

            $('#modal-detalhes-pneu').modal('show');

        });
    </script>
@stop
