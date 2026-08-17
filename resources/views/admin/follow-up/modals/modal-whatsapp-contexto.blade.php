<div class="modal fade" id="modal-whatsapp-contexto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fab fa-whatsapp mr-2 text-success"></i>Configurar Disparo por WhatsApp</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="whatsapp_cd_contexto">
                <p class="mb-2"><strong id="whatsapp_ds_contexto"></strong></p>
                <div class="form-group">
                    <label for="whatsapp_nr_limitediario">Limite de envios por dia</label>
                    <input type="number" min="1" class="form-control form-control-sm" id="whatsapp_nr_limitediario"
                        placeholder="Ex.: 25">
                    <small class="form-text text-muted">Aumente aos poucos enquanto o número está esquentando -
                        comece entre 20 e 30.</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="whatsapp_hr_janelainicio">Início da janela</label>
                            <input type="text" class="form-control form-control-sm" id="whatsapp_hr_janelainicio"
                                placeholder="HH:MM">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="whatsapp_hr_janelafim">Fim da janela</label>
                            <input type="text" class="form-control form-control-sm" id="whatsapp_hr_janelafim"
                                placeholder="HH:MM">
                        </div>
                    </div>
                </div>
                <small class="form-text text-muted">Fora desse horário, nenhum envio é feito, mesmo com pendentes na
                    fila. O espaçamento entre envios (2 a 5 min) e o processamento de um por vez são automáticos.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-success" id="btn-salvar-whatsapp-contexto">Salvar</button>
            </div>
        </div>
    </div>
</div>
