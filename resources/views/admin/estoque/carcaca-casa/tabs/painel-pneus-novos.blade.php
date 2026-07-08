<div class="tab-pane fade" id="painel-pneus-novos" role="tabpanel"
    aria-labelledby="tab-pneus-novos">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-md-8" id="div-tabela-pneus-novos">
                <div class="card-header">
                   <h6 class="card-title">Pneus Novos em Estoque</h6>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered compact table-font-small" id="table-pneus-novos">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Item</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th class="text-right">Total:</th>
                                <th id="total-pneus-novos-footer"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-tire"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number">
                                    <span id="total-pneus-novos"></span>
                                    <small>Unidades</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Resumo Pneus Novos</h6>
                                <div class="card-tools m-0">
                                    <button class="btn btn-xs btn-danger" id="download-resumo-pneus-novos"><i
                                            class="fas fa-download"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="accordionResumoPneusNovos" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
