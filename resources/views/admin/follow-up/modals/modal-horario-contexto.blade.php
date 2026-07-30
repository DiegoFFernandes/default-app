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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="horario_hr_execucao">Horário</label>
                            <input type="text" class="form-control form-control-sm" id="horario_hr_execucao"
                                placeholder="HH:MM">
                            <small class="form-text text-muted">Usado como âncora quando o intervalo é de 24h ou
                                mais - define a hora do dia em que sempre roda.</small>
                            <small id="horario_hr_execucao_aviso" class="form-text text-warning d-none">Não se
                                aplica com esse intervalo (menor que 24h) - o disparo roda de forma contínua.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="horario_nr_intervalohoras">Intervalo (horas)</label>
                            <input type="number" min="1" class="form-control form-control-sm"
                                id="horario_nr_intervalohoras" placeholder="Ex.: 1, 6, 24">
                            <small class="form-text text-muted">De quantas em quantas horas o disparo é
                                verificado para este contexto.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-success" id="btn-salvar-horario-contexto">Salvar</button>
            </div>
        </div>
    </div>
</div>
