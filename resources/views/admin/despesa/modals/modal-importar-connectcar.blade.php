<div class="modal fade" id="modal-importar-connectcar" tabindex="-1" role="dialog" aria-labelledby="modal-importar-connectcar-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modal-importar-connectcar-label">
                    <i class="fas fa-file-import mr-2"></i> Importar Pedágio — ConnectCar
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- Upload --}}
                <div id="area-upload-connectcar">
                    <p class="text-muted small mb-3">
                        Selecione o arquivo Excel (.xlsx / .xls) exportado pelo ConnectCar para importar os registros de pedágio.
                    </p>

                    <div class="form-group">
                        <label class="small font-weight-bold" for="arquivo-connectcar">Arquivo Excel</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="arquivo-connectcar"
                                accept=".xlsx,.xls" lang="pt">
                            <label class="custom-file-label" for="arquivo-connectcar">Nenhum arquivo selecionado</label>
                        </div>
                        <small class="text-muted">Formatos aceitos: .xlsx, .xls</small>
                    </div>

                    <div id="preview-connectcar" style="display:none;">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bold small">Pré-visualização</span>
                            <span class="badge badge-secondary" id="badge-total-connectcar">0 registros</span>
                        </div>
                        <div class="table-responsive" style="max-height:320px; overflow-y:auto;">
                            <table class="table table-sm table-bordered table-hover mb-0" id="tabela-preview-connectcar">
                                <thead class="thead-light">
                                    <tr id="header-preview-connectcar"></tr>
                                </thead>
                                <tbody id="body-preview-connectcar"></tbody>
                            </table>
                        </div>
                        <div id="alertas-connectcar" class="mt-2"></div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Fechar
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btn-processar-connectcar" style="display:none;">
                    <i class="fas fa-check mr-1"></i> Importar
                </button>
            </div>

        </div>
    </div>
</div>
