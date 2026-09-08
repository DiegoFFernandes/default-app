<div class="modal fade" id="modal-transferir-carcaca" tabindex="-1" role="dialog"
    aria-labelledby="modal-transferir-carcaca-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-exchange-alt mr-1"></i>Transferir Carcaça</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cd_local_transferir">Local Estoque</label>
                    <select class="form-control form-control-sm" name="cd_local_transferir" id="cd_local_transferir"
                        style="width: 100%">
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->CD_EMPRESA }}" @if ($loop->first) selected="selected" @endif>{{ $empresa->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-xs" id="btn-transferir-carcaca">Confirmar
                    Transferencia</button>
            </div>
        </div>
    </div>
</div>
