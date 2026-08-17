<div class="tab-pane fade" id="pane-disparos-automaticos" role="tabpanel" aria-labelledby="tab-disparos-automaticos">
    <div class="row">
        <section class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1 text-muted"></i> Filtros</h3>
                    <div class="card-tools m-0">

                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="disparo_nr_contexto" class="form-label small"><i
                                    class="fas fa-tag mr-1 text-muted"></i>Contexto</label>
                            <select class="form-control form-control-sm" id="disparo_nr_contexto"
                                style="width:100%;">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="disparo_st_envio" class="form-label small"><i
                                    class="fas fa-info-circle mr-1 text-muted"></i>Status</label>
                            <select class="form-control form-control-sm" id="disparo_st_envio" style="width:100%;">
                                <option value="">Todos</option>
                                <option value="P">Pendente de Envio</option>
                                <option value="A">Aguardando</option>
                                <option value="E">Enviado</option>
                                <option value="V">Enviado c/ Falha</option>
                                <option value="F">Falha</option>
                                <option value="L">Limite Atingido</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="disparo_nm_pessoa" class="form-label small"><i
                                    class="fas fa-user mr-1 text-muted"></i>Cliente</label>
                            <input type="text" class="form-control form-control-sm" id="disparo_nm_pessoa"
                                placeholder="Nome do Cliente">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="disparo-daterange" class="form-label small"><i
                                    class="fas fa-calendar-alt mr-1 text-muted"></i>Período</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="disparo-daterange"
                                    value="" autocomplete="off" placeholder="Periodo">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success btn-sm" id="submit-search-disparo">
                            <i class="fas fa-search"></i> Pesquisar
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resultado Pesquisa - Disparos Automáticos</h3>
                    
                </div>
                <div class="card-body">
                    <div>
                        <small class="badge badge-danger badge-date-disparo"></small>
                    </div>
                    <table id="table-search-disparo"
                        class="table compact table-font-small table-bordered table-striped">
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
