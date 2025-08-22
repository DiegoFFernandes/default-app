@extends('layouts.master')

@section('title', 'Tabela de Preço')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-body">
                        <table class="table table-bordered compact table-responsive table-font-small" id="tabela-preco">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-item-tab-preco" tabindex="-1" role="dialog" aria-labelledby="modal-item-tab-preco"
            aria-hidden="true">
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
    <script>
        $(document).ready(function() {
            $('#tabela-preco').DataTable({
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
                        data: 'CD_TABPRECO',
                        name: 'CD_TABPRECO',
                        title: 'Cód',
                        "width": "1%"
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
        });
    </script>
@stop
