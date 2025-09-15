
<div class="row">
    <div class="col-md-4 col-12">
        <div class="card card-secondary card-outline mb-4">
            <div class="card-header">
                <h5 class="card-title">Canhatos Mensal</h5>                
            </div>
            <div class="card-body p-2" id="card-canhoto-meses">
                {{-- Icon loading --}}
                <div class="invisible loading-card">
                    <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2"></div>
                    </div>
                </div>
                <div class="container-tabela">
                    <div class="table-responsive">
                        <table id="{{ $tabela_canhoto_mensal }}" class="table compact table-font-small nowrap"
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
            </div>
            <div class="modal fade" id="{{ $modal_canhoto_table }}" tabindex="-1" role="dialog"
                aria-labelledby="modal-table-canhoto-label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex flex-wrap align-items-center">
                            <h6 class="modal-title flex-md-grow-1 modal-table-canhoto-label" id="">
                                Detalhes Canhoto NF
                            </h6>
                            <button type="button" class="close order-2 order-md-3 ml-auto ml-md-0" data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>                            
                        </div>
                        <div class="modal-body">
                            <div class="accordion" id="{{ $accordion_canhoto_id }}">
                            </div>
                            <x-btn-topo-modal :modalId="$modal_canhoto_table" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-12">
        <div class="card card-secondary card-outline mb-4">
            <div class="card-header">
                <h5 class="card-title">Relatório Canhotos</h5>
            </div>
            <div class="card-body p-2" id="{{ $card_canhoto }}">
                {{-- Icon loading --}}
                <div class="invisible loading-card">
                    <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2"></div>
                    </div>
                </div>
                <div class="accordion" id="{{ $treeAccordionCanhoto }}">
                    <!-- Gerente -->
                    <div class="card">
                        <div class="card-header p-1">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                    data-target="#sup1">
                                </button>
                            </h2>
                        </div>
                        <div id="sup1" class="collapse" data-parent="#{{ $treeAccordionCanhoto }}">
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
            </div>
            <div class="card-footer text-right">
                <strong>Total geral: </strong><span class="valorTotalCanhoto" id=""></span>
            </div>
        </div>
    </div>
</div>
