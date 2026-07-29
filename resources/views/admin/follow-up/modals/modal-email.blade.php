<div class="modal fade" id="modal-email" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disparo Automático</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Essa mensagem é de um disparo automático, cliente deve verificar se não está na caixa de spam
                    ou no lixo eletrônico, caso ele não receber!
                </div>
                <div class="form-group">
                    <label for="assunto">Assunto:</label>
                    <input type="text" class="form-control form-control-sm" id="assunto" disabled>
                </div>
                <div class="form-group">
                    <label for="from">De:</label>
                    <input type="text" class="form-control form-control-sm" id="from" disabled>
                </div>
                <div class="form-group">
                    <label for="to">Para:</label>
                    <input type="text" class="form-control form-control-sm" id="to" disabled>
                </div>
                <div class="form-group">
                    <label for="message">Mensagem:</label>
                    <textarea class="form-control form-control-sm" type="textarea" id="message" rows="7" disabled></textarea>
                </div>
                <div class="anexos">
                    <label>Anexos:</label>
                    <div id="lista-anexos">
                        <!-- Lista de anexos será carregada aqui -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
