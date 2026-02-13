{{-- Modal de Itens --}}
<div class="modal modal-default fade" id="modal-table-pedido">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <blockquote class="quote-danger d-none" style="margin: 0">
                    <small class="form-text text-muted">Apenas o Coordenador. Edição
                        permitida.</small>
                </blockquote>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-4 col-md-4">
                            <div class="form-group">
                                <label for="nr_pedido">Pedido</label>
                                <input class="form-control form-control-sm nr_pedido" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-8 col-md-8">
                            <div class="form-group">
                                <label for="pessoa">Pessoa</label>
                                <input id="" class="form-control form-control-sm pessoa" type="text"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="form-group">
                                <label for="vendedor">Vendedor</label>
                                <input id="" class="form-control form-control-sm vendedor" type="text"
                                    readonly>

                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="form-group">
                                <label for="condicao">Condição</label>
                                <input id="" class="form-control form-control-sm condicao" type="text"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2" class="d-none" id="card-pedido">
                    <div class="card-header">
                        <h3 class="card-title">Itens</h3>
                        <div class="card-tools">
                            <button id="btn-observacao" class="btn btn-secondary btn-xs" data-toggle="tooltip"
                                title="">Observação</button>
                        </div>
                    </div>
                    <div id="card-container"></div>
                </div>
                <table class="table compact row-border" id="table-item-pedido" style="font-size:12px">
                </table>
                <div class="modal-footer justify-content-center">
                    <div class="col-md-12">
                        <div class="form-group" style="text-align: left">
                            <label for="liberacao">Motivo Liberação:</label>
                            <textarea id="" class="form-control liberacao" rows="3" cols="50"></textarea>
                        </div>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-alert" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success btn-save-confirm">Liberar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <x-btn-topo-modal :modalId="'modal-table-pedido'" /> --}}
</div>
