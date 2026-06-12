<div class="row">
    <div class="col-md-4 col-12">
        <div class="card card-secondary card-outline mb-4">
            <div class="card-header">
                <h5 class="card-title">Inadimplência Mensal</h5>
                <div class="float-right">
                    <button class="btn btn-secondary btn-xs btn-hover btn-toggle-chart">
                        <i class="fas fa-chart-bar mr-1"></i>Gráfico
                    </button>
                </div>
            </div>
            <div class="card-body p-2" id="card-inadimplencia-meses">
                {{-- Icon loading --}}
                <div class="invisible loading-card">
                    <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2"></div>
                    </div>
                </div>
                <div class="container-tabela">
                    <div class="table-responsive">
                        <table id="{{ $tabela_mensal }}" class="table compact table-font-small nowrap"
                            style="width:100%; font-size: 12px;">
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="container-grafico" style="display:none;">
                    <div class="chart-type-btns d-flex justify-content-end mb-1" style="gap:3px;">
                        <button class="btn btn-xs btn-secondary btn-hover btn-chart-type active"
                                data-type="bar" title="Barras + % Inadimplência">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-secondary btn-hover btn-chart-type"
                                data-type="line" title="Tendência %">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    </div>
                    <canvas id="{{ $grafico_mensal }}" style="height:260px;"></canvas>
                </div>
            </div>
            <div class="modal fade" id="{{ $modal_table }}" tabindex="-1" role="dialog"
                aria-labelledby="modal-table-cliente-label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex flex-wrap align-items-center">
                            <h6 class="modal-title flex-md-grow-1 modal-table-cliente-label" id="">
                                Detalhes Inadimplência
                            </h6>
                            <button type="button" class="close order-2 order-md-3 ml-auto ml-md-0" data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <input type="text" id="{{ $buscarCliente }}"
                                class="form-control input-busca order-3 order-md-2 mt-3 mt-md-0 mr-md-2"
                                placeholder="Buscar Cliente...">
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="{{ $accordion_id }}">
                            </div>
                            <x-btn-topo-modal :modalId="$modal_table" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-12">
        <div class="card card-secondary card-outline mb-4 card-input-busca"
            data-tree-accordion="{{ $treeAccordionGerente }}">
            <div class="card-header">
                <h5 class="card-title">Relatório Vencidos</h5>
                <div class="float-right d-flex align-items-center flex-wrap" style="gap:5px;">
                    <select class="form-control form-control-sm select-vencimento"
                            id="select-venc-{{ $treeAccordionGerente }}"
                            data-accordion="{{ $treeAccordionGerente }}"
                            style="width:105px; font-size:0.78rem;"
                            title="Filtrar por mês">
                        <option value="">Mês</option>
                    </select>
                    <select class="form-control form-control-sm select-dia-vencimento"
                            id="select-dia-{{ $treeAccordionGerente }}"
                            data-accordion="{{ $treeAccordionGerente }}"
                            style="width:115px; font-size:0.78rem;"
                            disabled
                            title="Filtrar por data">
                        <option value="">Data</option>
                    </select>
                    <input type="text"
                        class="form-control form-control-sm input-busca input-busca-cliente"
                        style="width:145px; font-size:0.78rem;"
                        placeholder="Buscar Cliente...">
                </div>
            </div>
            <div class="card-body p-2" id="{{ $card_inadimplencia }}">
                {{-- Icon loading --}}
                <div class="invisible loading-card">
                    <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2"></div>
                    </div>
                </div>
                <div class="accordion" id="{{ $treeAccordionGerente }}">
                    <!-- Gerente -->
                    <div class="card">
                        <div class="card-header p-1">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#sup1">
                                </button>
                            </h2>
                        </div>
                        <div id="sup1" class="collapse" data-parent="#{{ $treeAccordionGerente }}">
                            <div class="card-body">
                                <!-- Supervisor -->
                                <button class="btn btn-sm btn-secondary" data-toggle="collapse" data-target="#vend1">
                                    🛡️ Supervisor
                                </button>
                                <div id="vend1" class="collapse mt-2">
                                    <!-- Vendedor -->
                                    <button class="btn btn-sm btn-info" data-toggle="collapse" data-target="#cli1">
                                        👤 Vendedor
                                    </button>
                                    <div id="cli1" class="collapse mt-2">
                                        <!-- Clientes -->
                                        <ul class="list-group">
                                            <li class="list-group-item">🏢 Cliente </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráficos de pizza: gerente (esq.) e supervisor (dir.) --}}
                <div class="row mt-2">
                    <div class="col-6 pr-1">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-header p-2" style="background:#f8f9fa; border-bottom:1px solid #e9ecef;">
                                <small class="font-weight-bold text-muted" style="font-size:0.74rem;">
                                    <i class="fas fa-chart-pie mr-1 text-primary"></i> % Gerentes
                                </small>
                            </div>
                            <div class="card-body p-1" style="height:175px; position:relative;">
                                <canvas id="pizza-g-{{ $treeAccordionGerente }}"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 pl-1">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-header p-2" style="background:#f8f9fa; border-bottom:1px solid #e9ecef;">
                                <small class="font-weight-bold text-muted" style="font-size:0.74rem;">
                                    <i class="fas fa-chart-bar mr-1 text-info"></i> % Supervisores
                                </small>
                            </div>
                            <div class="card-body p-1" style="height:175px; position:relative;">
                                <canvas id="pizza-s-{{ $treeAccordionGerente }}"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <strong>Total geral: </strong>
                <span class="valorTotalGerente" id="">
                </span>
            </div>
        </div>
    </div>
</div>
