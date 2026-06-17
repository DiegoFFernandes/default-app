<div class="modal fade" id="modal-aprovadores" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Aprovadores — <span id="aprov-ds-faixa"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="aprov_id_faixa">
                <div class="row mb-2">
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Ordem <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="aprov_ordem" min="1" placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Cargo <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="aprov_cargo">
                                <option value="">Selecione...</option>
                                <option value="Diretoria">Diretoria</option>
                                <option value="Gerente">Gerente</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="Coordenador">Coordenador</option>
                                <option value="Usuario">Usuario</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Usuário Aprovador <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm select2" id="aprov_cd_usuario" style="width:100%">
                                <option value="">Selecione</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-2 w-100">
                            <button id="btn-add-aprov" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-font-small" id="table-aprovadores">
                    <thead>
                        <tr>
                            <th style="width:30px"></th>
                            <th>Ordem</th>
                            <th>Cargo</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-aprovadores"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-ordem" class="btn btn-success btn-sm" style="display:none">
                    <i class="fas fa-save mr-1"></i> Salvar Ordem
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
