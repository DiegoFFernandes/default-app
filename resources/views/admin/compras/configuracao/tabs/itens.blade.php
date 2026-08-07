<div class="tab-pane fade" id="pane-itens-fonte" role="tabpanel">
    <p class="text-muted mb-3" style="font-size:0.85rem">
        <i class="fas fa-info-circle mr-1"></i>
        Define de onde vêm os itens ao montar uma solicitação de compra.
    </p>

    <div class="form-group">
        <div class="custom-control custom-radio mb-2">
            <input type="radio" id="fonte_junsoft" name="st_fonte_item" value="J"
                class="custom-control-input input-fonte-item" {{ ($fonteItem ?? 'J') === 'J' ? 'checked' : '' }}>
            <label class="custom-control-label" for="fonte_junsoft">
                <strong>Usar itens Junsoft</strong>
                <br><small class="text-muted">Busca na base do ERP (tabela ITEM).</small>
            </label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" id="fonte_proprio" name="st_fonte_item" value="P"
                class="custom-control-input input-fonte-item" {{ ($fonteItem ?? 'J') === 'P' ? 'checked' : '' }}>
            <label class="custom-control-label" for="fonte_proprio">
                <strong>Usar itens Próprios</strong>
                <br><small class="text-muted">Busca no catálogo próprio (menu "Novo Item"), com cadastro manual.</small>
            </label>
        </div>
    </div>
</div>
