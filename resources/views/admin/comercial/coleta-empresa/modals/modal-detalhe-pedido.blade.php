<div class="modal fade" id="modal-detalhes-pedido" tabindex="-1" role="dialog" aria-labelledby="modal-default-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">

                <span class="badge badge-danger mr-1" id="badge-num-pedido"></span>
                <span class="badge badge-warning mr-1" id="badge-dt-registro-palm"></span>
                <span class="badge badge-info mr-1" id="badge-dt-sinc"></span>
                <span class="badge badge-secondary mr-1" id="badge-ds-motivo"></span>
                <span class="badge badge-primary mr-1" id="badge-ds-liberacao-anterior">
                    <i class="fa fa-exclamation-circle"></i>
                </span>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2 pb-1">
                <div class="row">
                    <div class="col-md-3">
                        <label for="dsEmpresa" class="mb-0">Empresa:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="dsEmpresa" readonly>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group mb-2">
                            <label for="nomePessoa" class="mb-0">Pessoa:</label>
                            <input type="text" class="form-control form-control-sm" id="nomePessoa" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label for="nomeVendedor" class="mb-0">Vendedor:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="nomeVendedor" readonly>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label for="pedidoPalm" class="mb-0">Pedido Palm</label>
                            <input type="text" class="form-control form-control-sm" id="pedidoPalm" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label for="pedido" class="mb-0">Pedido</label>
                            <input type="text" class="form-control form-control-sm" id="pedidoColeta" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label for="condicaoDetails" class="mb-0">Condição Pagamento:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="condicaoDetails" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="formaDetails" class="mb-0">Forma Pagamento:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="formaDetails" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="dtEmissao" class="mb-0">Data Emissão:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="dtEmissao" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="dtEntrega" class="mb-0">Data Entrega:</label>
                        <input type="text" class="form-control form-control-sm mb-2" id="dtEntrega" readonly>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label for="observacaoDetails" class="mb-0">Observação:</label>
                    <textarea class="form-control form-control-sm" id="observacaoDetails" rows="1" readonly></textarea>
                </div>
                <div class="form-group mb-2 d-none form-group-bloqueio">
                    <label for="dsBloqueioDetails" class="mb-0">Bloqueio:</label>
                    <textarea class="form-control form-control-sm" id="dsBloqueioDetails" rows="4" readonly></textarea>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm compact" id="item-pedido"
                        style="width:100%; font-size:12px">
                        <thead>
                            <tr>
                                <th>Sq</th>
                                <th>Ordem</th>
                                <th>Serviço</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
