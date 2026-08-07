{{-- Modal de cadastro rápido de fornecedor (PESSOA + ENDERECOPESSOA). HTML apenas.
     O script fica em modals.modal-fornecedor-script, incluído no @section('js') da página. --}}
<div class="modal fade" id="modal-fornecedor" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck mr-1"></i> Cadastrar Fornecedor</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>CNPJ/CPF <span class="text-danger">*</span></small></label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" id="fp_cnpj"
                                    placeholder="CNPJ ou CPF">
                                <div class="input-group-append">
                                    <button type="button" id="btn-buscar-cnpj" class="btn btn-info" title="Consultar CNPJ/CPF">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Tipo <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="fp_tipo">
                                <option value="2">Fornecedor</option>
                                <option value="1">Cliente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Razão Social / Nome <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="fp_nm_pessoa" maxlength="60">
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Município</small></label>
                            <select class="form-control form-control-sm" id="fp_municipio" style="width:100%"></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>CEP</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_cep" placeholder="00000-000">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Endereço</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_ds_endereco" maxlength="60">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Número</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_nr_endereco" maxlength="10">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Bairro</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_ds_bairro" maxlength="60">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Telefone</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_nr_fone" maxlength="15">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Celular</small></label>
                            <input type="text" class="form-control form-control-sm" id="fp_nr_celular" maxlength="15">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-fornecedor" class="btn btn-primary btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
