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
                            <div class="tab-pane fade show active" id="painel-pedidos-alterados-sem-faturar" role="tabpanel"
                                aria-labelledby="tab-pedidos-alterados-sem-faturar">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-8" id="div-tabela-pedidos-alterados-sem-faturar">
                                            <div class="card-header">
                                                <h3 class="card-title">Pedidos Alterados - Sem faturar</h3>
                                            </div>
                                            <div class="card-body pb-0">
                                                <table class="table table-bordered compact table-font-small"
                                                    id="pedidos-alterados-sem-faturar">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="painel-pedidos-alterados-faturados" role="tabpanel"
                                aria-labelledby="tab-pedidos-alterados-faturados">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-8" id="div-tabela-pedidos-alterados-faturados">
                                            <div class="card-header">
                                                <h3 class="card-title">Pedidos Alterados - Faturados</h3>
                                            </div>
                                            <div class="card-body pb-0">
                                                <h3> teste</h3>
                                                <table class="table table-bordered compact table-font-small"
                                                    id="pedidos-alterados-faturados">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            getPedidosAlteradosData: "{{ route('get-pedidos-alterados') }}"
        }

        initTablePedidosAlterados('pedidos-alterados-sem-faturar', 'N');
        // initTablePedidosAlterados('pedidos-alterados-faturados', 'F');

        function initTablePedidosAlterados(idTabela, statusPedido) {
            console.log('aqui');
            $('#' + idTabela).DataTable({
                processing: false,
                serverSide: false,
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
                        title: 'Emp.'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente'
                    },
                    {
                        data: 'DS_VELHO',
                        name: 'DS_VELHO',
                        title: 'Ser. Antigo'
                    },
                    {
                        data: 'DS_NOVO',
                        name: 'DS_NOVO',
                        title: 'Ser. Novo'
                    },
                    {
                        data: 'VLSERVCOLETA',
                        name: 'VLSERVCOLETA',
                        title: 'Valor Coleta.'
                    },
                    {
                        data: 'VLSERVALTERADO',
                        name: 'VLSERVALTERADO',
                        title: 'Valor Alterado'
                    },
                    {
                        data: 'DTALTERACAO',
                        name: 'DTALTERACAO',
                        title: 'Data Alteração'
                    },
                    {
                        data: 'STORDEM',
                        name: 'STORDEM',
                        title: 'Status Ordem'
                    },
                    {
                        data: 'NR_NOTAFISCAL',
                        name: 'NR_NOTAFISCAL',
                        title: 'Nota Fiscal'
                    }
                ]
            });
        }
    </script>
@stop
