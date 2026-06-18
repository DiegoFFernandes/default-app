<div class="tab-pane fade show active" id="painel-registrar" role="tabpanel" aria-labelledby="tab-registrar">
    <div class="card-body p-2 pt-3">
        <form id="form-registrar-comprovante" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-12 col-md-3 mb-3">
                    <label class="small" for="cd_pessoa">Solicitante <span class="text-danger">*</span></label>
                    <select name="cd_pessoa" id="cd_pessoa" class="form-control form-control-sm" style="width:100%;"></select>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <label class="small" for="tp_despesa">Tipo de Despesa <span class="text-danger">*</span></label>
                    <select name="tp_despesa" id="tp_despesa" class="form-control form-control-sm select2">
                        <option value="">Selecione...</option>
                        <option value="ALI">Alimentação</option>
                        <option value="COM">Combustível</option>
                        <option value="HOS">Hospedagem</option>
                        <option value="PED">Pedágio</option>
                    </select>
                </div>                

                <div class="col-12 col-md-3 mb-3">
                    <label class="small" for="vl_consumido">Valor <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="text" name="vl_consumido" id="vl_consumido"
                            class="form-control form-control-sm money" placeholder="0,00">
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <label class="small" for="dt_despesa">Data da Despesa <span class="text-danger">*</span></label>
                    <input type="date" name="dt_despesa" id="dt_despesa" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>

                {{-- Campos extras: Combustível (direto no row para participar do grid) --}}
                <div class="col-12 col-md-3 mb-3 campo-combustivel" style="display:none;">
                    <label class="small" for="km">KM do Veículo <span class="text-danger">*</span></label>
                    <input type="number" name="km" id="km" class="form-control form-control-sm"
                        placeholder="Ex: 125000" min="0" step="1">
                </div>
                <div class="col-12 col-md-3 mb-3 campo-combustivel" style="display:none;">
                    <label class="small" for="nr_placa">Placa do Veículo <span class="text-danger">*</span></label>
                    <select name="nr_placa" id="nr_placa" class="form-control form-control-sm" style="width:100%;"></select>
                </div>                

                <div class="col-12 col-md-6 mb-3">
                    <label class="small" for="ds_observacao">Observação</label>
                    <textarea name="ds_observacao" id="ds_observacao" class="form-control form-control-sm" rows="1"
                        placeholder="Observações adicionais..."></textarea>
                </div>

                <div class="col-12 mb-2">
                    <label class="small">Fotos do Comprovante</label>
                    <div class="d-flex" style="gap:8px;">
                        <button type="button" class="btn btn-outline-dark btn-sm" id="btn-abrir-camera">
                            <i class="fas fa-camera mr-1"></i> Câmera
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-abrir-galeria">
                            <i class="fas fa-images mr-1"></i> Galeria
                        </button>
                        <input type="file" id="fotos-galeria" class="d-none" accept="image/*" multiple>
                    </div>
                    <small class="text-muted d-block mt-1">Use Câmera para capturar ao vivo ou Galeria para selecionar
                        arquivos.</small>
                </div>

                <div class="col-12 mb-3" id="preview-fotos" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="small mb-0">Pré-visualização <span class="badge badge-secondary"
                                id="qtd-preview">0</span></label>
                        <button type="button" class="btn btn-xs btn-outline-danger" id="btn-limpar-fotos">
                            <i class="fas fa-trash mr-1"></i> Limpar
                        </button>
                    </div>
                    <div class="d-flex flex-wrap" id="preview-container"></div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-danger" id="btn-salvar">
                        <i class="fas fa-save mr-1"></i> Registrar Comprovante
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
