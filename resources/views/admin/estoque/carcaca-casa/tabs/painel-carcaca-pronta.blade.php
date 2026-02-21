<div class="tab-pane fade" id="painel-carcaca-pronta" role="tabpanel" aria-labelledby="tab-carcaca-pronta">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-md-8" id="div-tabela-carcacas-prontas">
                <div class="card-header">                    
                    <button type="button" class="btn btn-secondary btn-xs" style="width: 100px;" id="btn-reservar-carcaca">
                        Reservar Pneus
                    </button>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered compact table-font-small" id="table-carcacas-prontas">
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="info-box">
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number">
                                    <span id="total-carcacas-prontas"></span>
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
                                <h6 class="card-title">Resumo Local</h6>
                                <div class="card-tools m-0">
                                    <button class="btn btn-xs btn-danger" id="download-resumo-local"><i
                                            class="fas fa-download"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="accordionResumoCarcacaProntaLocal" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
