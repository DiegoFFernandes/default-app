<div class="modal fade" id="modal-adicionar-lote-pcp" tabindex="-1" role="dialog"
    aria-labelledby="modal-adicionar-lote-pcp-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">
            <form id="form-adicionar-lote-pcp">
                <div class="modal-header">
                    <h6 class="modal-title modal-title-lote">Adicionar Lote PCP</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="select-empresa-lote-pcp">Empresa</label>
                                <select class="form-control form-control-sm" id="select-empresa-lote-pcp" name="empresa"
                                    required>
                                    <option value="">Selecione a empresa</option>
                                    @foreach ($empresa as $emp)
                                        <option value="{{ $emp->CD_EMPRESA }}">{{ $emp->NM_EMPRESA }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="select-lote-pcp">Lote PCP</label>
                                <select class="form-control form-control-sm" id="select-lote-pcp" name="lote_pcp"
                                    required>
                                    <option value="">Selecione uma Empresa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="select-responsavel-lote-pcp">Responsavel</label>
                                <select class="form-control form-control-sm" id="select-responsavel-lote-pcp"
                                    name="responsavel" required>
                                    <option value="">Selecione um Lote</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="data-producao-lote-pcp">Data de Produção</label>
                                <input type="date" class="form-control form-control-sm" id="data-producao-lote-pcp"
                                    name="data_producao" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary btn-xs" id="btn-adicionar-lote-pcp">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
