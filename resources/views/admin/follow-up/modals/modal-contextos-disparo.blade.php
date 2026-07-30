<div class="modal fade" id="modal-contextos-disparo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cog mr-2"></i>Contextos de Disparo Automático</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-hover mb-0" id="table-contextos-disparo">
                    <thead class="thead-light">
                        <tr>
                            <th>Contexto</th>
                            <th style="width:80px;" class="text-center">Horário</th>
                            <th style="width:90px;" class="text-center">Intervalo</th>
                            <th style="width:110px;" class="text-center">Tentativas</th>
                            <th style="width:150px;">Última Execução</th>
                            <th style="width:150px;">Próxima Execução</th>
                            <th style="width:90px;" class="text-center">Status</th>
                            <th style="width:110px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-contextos-disparo">
                        <tr>
                            <td colspan="8" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-secondary"></div>
                                <span class="ml-2 text-muted">Carregando...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
