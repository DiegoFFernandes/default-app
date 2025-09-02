@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-cnpj" type="text" class="form-control" placeholder="Filtrar por CNPJ">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-supervisor" type="text" class="form-control"
                            placeholder="Filtrar por Supervisor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="daterange" type="text" class="form-control" placeholder="Filtrar por Vencimento">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkVencidas" name="filtroVencimento">
                            <label for="checkVencidas" class="custom-control-label">Vencidas</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkAvencer" name="filtroVencimento">
                            <label for="checkAvencer" class="custom-control-label">A Vencer</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox" id="checkAll"
                                name="filtroVencimento" checked>
                            <label for="checkAll" class="custom-control-label">Todas</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default btn-sm float-right" id="btn-limpar">
                                <i class="fas fa-eraser"></i> Limpar
                            </button>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRelatorio" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-relatorio-cobranca" data-toggle="pill"
                                    href="#painel-relatorio-cobranca" role="tab"
                                    aria-controls="painel-relatorio-cobranca" aria-selected="true">
                                    Relatório
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-cartao-cheque" data-toggle="pill" href="#painel-cartao-cheque"
                                    role="tab" aria-controls="painel-cartao-cheque" aria-selected="false">
                                    Cheques e Cartão
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-limite-credito" data-toggle="pill" href="#painel-limite-credito"
                                    role="tab" aria-controls="painel-limite-credito" aria-selected="false">
                                    Limite Crédito
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-prazo-medio" data-toggle="pill" href="#painel-prazo-medio"
                                    role="tab" aria-controls="painel-prazo-medio" aria-selected="false">
                                    Prazo Médio
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentRelatorio">
                            <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                aria-labelledby="tab-relatorio-cobranca">
                                <div class="row">
                                    <div class="col-md-8 col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Relatório</h5>
                                            </div>
                                            <div class="card-body p-2">
                                                <table id="table-rel-cobranca"
                                                    class="table compact table-font-small table-striped table-bordered nowrap"
                                                    style="width:100%; font-size: 12px;">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Inadimplência Mensal</h5>
                                            </div>
                                            <div class="card-body p-2">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="chequesCartao"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                            style="width:100%; font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th>Documento</th>
                                                    <th>Nome</th>
                                                    <th>Cnpj</th>
                                                    <th>Vencimento</th>
                                                    <th>Valor</th>
                                                    <th>Juros</th>
                                                    <th>Dias Atraso</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-limite-credito" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-prazo-medio" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
@stop

@section('js')

    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
        <div class="right badge badge-danger">{{ DS_REGIAOCOMERCIAL }}</div>
        <table class="table row-border" id="regiao-{{ CD_REGIAOCOMERCIAL }}" style="width:100%">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Cliente</th>
                    <th>CNPJ</th>
                    <th>Titulos</th>
                    <th>Tipo Conta</th>
                    <th>Valor</th>
                </tr>
            </thead>
        </table>
        @endverbatim
    </script>

    <script type="text/javascript">
        var template = Handlebars.compile($("#details-template").html());
        var table = $("#table-rel-cobranca").DataTable({
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            },
            pageLength: 50,
            // responsive: true,
            // "searching": true,
            // "bInfo": false,
            // scrollX: true,
            ajax: "{{ route('get-list-cobranca') }}",
            columns: [{
                    data: "actions",
                    name: "actions",
                    title: "Ações",
                    orderable: false,
                    searchable: false,
                    "width": "1%",
                },
                {
                    data: "responsavel",
                    name: "responsavel",
                    title: "Responsável",
                    visible: true,
                },
                {
                    data: "qtd_titulos",
                    name: "qtd_titulos",
                    title: "Títulos",
                    visible: true,
                },
                {
                    data: "saldo",
                    name: "saldo",
                    title: "Valor",
                },
            ]
        });
    </script>

    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/inadimplencia.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}"></script>

@stop
