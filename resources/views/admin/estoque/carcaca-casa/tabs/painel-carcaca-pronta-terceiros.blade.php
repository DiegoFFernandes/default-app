<div class="tab-pane fade" id="painel-carcaca-pronta-terceiros" role="tabpanel"
    aria-labelledby="tab-carcaca-pronta-terceiros">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-md-8" id="div-tabela-carcacas-prontas">
                <div class="card-header">
                   <h6 class="card-title">Pronto em estoque de Terceiros</h6>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered compact table-font-small" id="table-carcacas-prontas-terceiros">
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
                                <th id="total-carcacas-prontas-terceiros-footer"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1">
                                <i class="fas fa-cubes"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number">
                                    <span id="total-carcacas-prontas-terceiros"></span>
                                    <small>Unidades</small>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Resumo Terceiros</h6>
                                <div class="card-tools m-0">
                                    <button class="btn btn-xs btn-danger" id="download-resumo-terceiros"><i
                                            class="fas fa-download"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="accordionResumoCarcacaProntaTerceiros" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
