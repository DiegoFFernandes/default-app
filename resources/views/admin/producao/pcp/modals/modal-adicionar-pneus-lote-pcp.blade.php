<div class="modal fade" id="modal-adicionar-pneus-lote-pcp" tabindex="-1" role="dialog"
    aria-labelledby="modal-adicionar-pneus-lote-pcp-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title modal-title-adicionar-pneus-lote-pcp">Adicionar Pneus Lote PCP</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card mb-3">
                    <form id="form-adicionar-pneus-lote-pcp">
                        <div class="card-body pb-0 pt-0">
                            <div class="row ">
                                @csrf
                                <input type="hidden" id="empresa-adicionar-lote-pcp" name="empresa">
                                <input type="hidden" id="nrlote-adicionar-lote-pcp" name="nrlote">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small" for="select-pedidopneu">Pedidos</label>
                                        <select name="select-pedidopneu[]" id="select-pedidopneu"
                                            class="form-control form-control-sm" style="width: 100%;" multiple>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small" for="select-ordens-producao">Ordens de Produção</label>
                                        <select name="select-ordens-producao[]" id="select-ordens-producao"
                                            class="form-control form-control-sm" style="width: 100%;" multiple>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-1">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary btn-xs w-10"
                                    id="btn-buscar-pneus-lote-pcp">Buscar Pneus</button>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <div class="card pt-0" id="div-tabela-adicionar-pneus-lote-pcp" style="display: none;">
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-font-small compact"
                                        id="tabela-adicionar-pneus-lote-pcp">
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-1">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-warning btn-xs w-10" id="btn-salvar-pneus-lote-pcp">
                                Adicionar ao Lote</button>
                        </div>
                    </div>
                </div>
            </div>        

        </div>
    </div>
</div>
