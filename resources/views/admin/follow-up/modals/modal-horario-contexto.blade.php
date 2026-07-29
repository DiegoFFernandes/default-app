<div class="modal fade" id="modal-horario-contexto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Horário de Execução</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="horario_cd_contexto">
                <p class="mb-2"><strong id="horario_ds_contexto"></strong></p>
                <div class="form-group">
                    <label for="horario_hr_execucao">Editar Horário de Execução</label>
                    <input type="text" class="form-control form-control-sm" id="horario_hr_execucao"
                        placeholder="HH:MM">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-success" id="btn-salvar-horario-contexto">Salvar</button>
            </div>
        </div>
    </div>
</div>
