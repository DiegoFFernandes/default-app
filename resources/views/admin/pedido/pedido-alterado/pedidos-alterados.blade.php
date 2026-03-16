@extends('layouts.master')

@section('title', 'Trocas de Valor')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabPedidosAlterados" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-pedidos-alterados-sem-faturar" data-toggle="pill"
                                    href="#painel-pedidos-alterados-sem-faturar" role="tab"
                                    aria-controls="painel-pedidos-alterados-sem-faturar" aria-selected="true">
                                    Pedidos Alterados - Sem faturar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-pedidos-alterados-faturados" data-toggle="pill"
                                    href="#painel-pedidos-alterados-faturados" role="tab"
                                    aria-controls="painel-pedidos-alterados-faturados" aria-selected="false">
                                    Pedidos Alterados - Faturados
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="tabContentPedidosAlterados">
                            @include('admin.pedido.pedido-alterado.tabs.tab-pedidos-alterados-sem-faturar')

                            @include('admin.pedido.pedido-alterado.tabs.tab-pedidos-alterados-faturados')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script>
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getPedidosAlteradosData: "{{ route('get-pedidos-alterados-valor') }}"
        }

        initTablePedidosAlterados('table-pedidos-alterados-sem-faturar', 'N');

        $('#tab-pedidos-alterados-sem-faturar').on('click', function() {

            $('#table-pedidos-alterados-sem-faturar').DataTable().clear().destroy();

            initTablePedidosAlterados('table-pedidos-alterados-sem-faturar', 'N');
        });

        $('#tab-pedidos-alterados-faturados').on('click', function() {

            $('#table-pedidos-alterados-faturados').DataTable().clear().destroy();

            initTablePedidosAlterados('table-pedidos-alterados-faturados', 'F');
        });

        $('#btn-atualizar-pedidos-alterados-sem-faturar').on('click', function() {
            Swal.fire({
                text: 'Alterar os pedidos alterados para o valor atualizado? Essa ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, atualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('atualizar-pedidos-alterados') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            statusFaturamento: 'N'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: response.message,
                                html: response.details.join('<br>'),
                                icon: 'success'
                            });
                            $('#table-pedidos-alterados-sem-faturar').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                text: 'Ocorreu um erro ao atualizar os pedidos alterados.',
                                icon: 'error'
                            });
                        }
                    });
                }

            });
        });

        function initTablePedidosAlterados(idTabela, statusPedido) {

            $('#' + idTabela).DataTable({
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 20,
                scrollY: '400px',
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: {
                    url: window.routes.getPedidosAlteradosData,
                    data: {
                        status_pedido: statusPedido
                    }
                },
                columns: [{
                        data: 'IDEMPRESA',
                        name: 'IDEMPRESA',
                        title: 'Emp.',
                        width: '1%',
                        className: 'pl-1 text-nowrap text-center'
                    },
                    {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        title: 'Pedido',
                        className: 'text-nowrap text-center'
                    },
                    {
                        data: 'NR_ORDEM',
                        name: 'NR_ORDEM',
                        title: 'Ordem',
                        className: 'text-nowrap text-center'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'DS_VELHO',
                        name: 'DS_VELHO',
                        title: 'Ser. Antigo',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'DS_NOVO',
                        name: 'DS_NOVO',
                        title: 'Ser. Novo',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'VLSERVCOLETA',
                        name: 'VLSERVCOLETA',
                        title: 'R$ Coleta'
                    },
                    {
                        data: 'VLSERVALTERADO',
                        name: 'VLSERVALTERADO',
                        title: 'R$ Alterado'
                    },
                    {
                        data: 'DTALTERACAO',
                        name: 'DTALTERACAO',
                        title: 'Data Alteração',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'STORDEM',
                        name: 'STORDEM',
                        title: 'Status Ordem',
                        className: 'text-nowrap text-center'
                    },

                    {
                        data: 'NR_PEDIDO',
                        name: 'NR_PEDIDO',
                        title: 'Pedido'
                    },
                    {
                        data: 'NR_NOTAFISCAL',
                        name: 'NR_NOTAFISCAL',
                        title: 'Nota Fiscal'
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });
        }
    </script>
@stop
