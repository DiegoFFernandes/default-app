@extends('layouts.master')

@section('title', 'Tabela de Preço')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-4 align-items-center mb-3 border-bottom pb-2">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="checkNaoAssociadas"
                                    name="checkNaoAssociadas">
                                <label class="form-check-label" for="checkNaoAssociadas">
                                    Tabelas não Associadas
                                </label>
                            </div>

                            <div class="form-check form-switch m-0 ml-3">
                                <input class="form-check-input" type="checkbox" id="checkAssociadas" name="checkAssociadas">
                                <label class="form-check-label" for="checkAssociadas">
                                    Tabelas Associadas
                                </label>
                            </div>
                        </div>
                        <table class="table table-bordered compact table-responsive table-font-small" id="tabela-preco">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-item-tab-preco" tabindex="-1" role="dialog"
            aria-labelledby="modal-item-tab-preco" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title title-nm-tabela">Item Tabela Preço</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="table-item-tab-preco"
                            class="table table-bordered compact table-responsive table-font-small">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border table-left" id="cliente-tabela-{{ CD_TABPRECO }}" style="width:80%; ">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Supervisor</th>                       
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script>
        $(document).ready(function() {
            var tabelaClientesTabela = Handlebars.compile($("#details-template").html());
            var tabelaPreco = $('#tabela-preco').DataTable({
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 50,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: '{{ route('get-tabela-preco') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'clientes_associados',
                        name: 'clientes_associados',
                        title: 'Cód',
                        className: 'text-center',
                    },
                    {
                        data: 'DS_TABPRECO',
                        name: 'DS_TABPRECO',
                        title: 'Descrição'
                    }, {
                        data: 'QTD_ITENS',
                        name: 'QTD_ITENS',
                        title: 'Itens'
                    }, {
                        data: 'ASSOCIADOS',
                        name: 'ASSOCIADOS',
                        title: 'Clientes'
                    }, {
                        data: 'action',
                        name: 'action',
                        title: 'Ações',
                        className: 'text-center',

                    }
                ]
            });

            $('#tabela-preco').on('click', '.btn-ver-itens', function() {
                var cd_tabela = $(this).data('cd_tabela');
                $('.title-nm-tabela').html($(this).data('nm_tabela'));
                $("#modal-item-tab-preco").modal("show");
                if ($.fn.DataTable.isDataTable('#table-item-tab-preco')) {
                    $('#table-item-tab-preco').DataTable().clear().destroy();
                }
                $('#table-item-tab-preco').DataTable({
                    processing: false,
                    serverSide: false,
                    pagingType: "simple",
                    pageLength: 50,
                    layout: {
                        topStart: {
                            buttons: [{
                                    extend: 'excelHtml5',
                                    title: $('.title-nm-tabela').html()
                                },
                                {
                                    extend: 'print',
                                    title: $('.title-nm-tabela').text(),
                                    customize: function(win) {
                                        $(win.document.body).find('h1')
                                            .css('font-size', '12pt')
                                            .css('color', '#333');
                                    }

                                }
                            ]
                        }
                    },
                    language: {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    ajax: {
                        url: '{{ route('get-item-tabela-preco') }}',
                        type: 'GET',
                        data: {
                            cd_tabela: cd_tabela
                        }
                    },
                    columns: [{
                        data: 'CD_TABPRECO',
                        name: 'CD_TABPRECO',
                        title: 'Cód Tabela',
                        visible: false
                    }, {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        "width": "80%",
                        title: 'Descrição'
                    }, {
                        data: 'VL_PRECO',
                        name: 'VL_PRECO',
                        title: 'Valor',
                        // render: $.fn.dataTable.render.number('.', ',', 2),
                    }],
                    //    columnsDefs: [{
                    //        className: 'dt-body-right',
                    //        targets: [2]
                    //    }],
                });
            });

            //Aguarda Click para buscar os detalhes dos pedidos dos vendedores
            configurarDetalhesLinha('.details-control', {
                idPrefixo: 'cliente-tabela-',
                idCampo: 'CD_TABPRECO',
                templateFn: tabelaClientesTabela,
                initFn: initTableClienteTabela,
                iconeMais: 'fa-plus-circle',
                iconeMenos: 'fa-minus-circle'
            });




            function initTableClienteTabela(tableId, data) {
                console.log(data);

                const tableTabelaCliente = $('#' + tableId).DataTable({
                    "searching": false,
                    "paging": false,
                    sDom: 't',
                    processing: false,
                    serverSide: false,
                    language: {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    ajax: {
                        url: '{{ route('get-tabela-cliente-preco') }}',
                        type: 'GET',
                        data: {
                            cd_tabela: data.CD_TABPRECO
                        }
                    },
                    columns: [{
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente'
                    }, {
                        data: 'VENDEDOR',
                        name: 'VENDEDOR',
                        title: 'Vendedor'
                    }, {
                        data: 'SUPERVISOR',
                        name: 'SUPERVISOR',
                        title: 'Supervisor'
                    }]
                });

            }

            const chkNaoAssociados = document.getElementById("checkNaoAssociadas");
            const chkAssociados = document.getElementById("checkAssociadas");

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const valor = parseInt(data[3]) || 0; // Use data for the "Associados" column

                const naoAssociados = chkNaoAssociados && chkNaoAssociados.checked;
                const associados = chkAssociados && chkAssociados.checked;

                // Ambos marcados → mostra tudo
                if (naoAssociados && associados) {
                    return true;
                }
                // Só não associados
                if (naoAssociados) {
                    return valor === 0;
                }
                // Só associados
                if (associados) {
                    return valor > 0;
                }
                return true;

            });

            if (chkNaoAssociados) {
                chkNaoAssociados.addEventListener("change", function() {
                    tabelaPreco.draw();
                });
            }
            if (chkAssociados) {
                chkAssociados.addEventListener("change", function() {
                    tabelaPreco.draw();
                });
            }
        });
    </script>
@stop
