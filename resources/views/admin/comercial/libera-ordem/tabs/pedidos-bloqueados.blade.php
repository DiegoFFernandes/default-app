<div class="tab-pane fade show active" id="pedidos-bloqueados" role="tabpanel" aria-labelledby="tab-inserir">
    <div class="card collapsed-card mb-4">
        <div class="card-header">
            <h3 class="card-title mt-2">Filtros:</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small">Supervisor</label>
                        <input type="text" class="form-control form-control-sm" id="nm_supervisor"
                            placeholder="Nome Supervisor">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small">Vendedor</label>
                        <input type="text" class="form-control form-control-sm" id="nm_vendedor"
                            placeholder="Nome Vendedor">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small">Cliente</label>
                        <input type="text" class="form-control form-control-sm" id="nm_cliente"
                            placeholder="Nome Cliente">
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary btn-xs float-right mr-2"
                        id="btn-limpar">Limpar</button>
                </div>
                <!-- /.row -->
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pedidos Bloqueados</h3>
            <button class="btn btn-primary btn-xs float-right" id="btn-liberar">Liberar Abaixo
                {{ intval($percentual[0]->perc_desconto_max) }}%</button>

        </div>
        <div class="card-body">
            <span class="badge bg-warning">Coordenador</span>
            <span class="badge bg-secondary mr-2">Supervisor</span>
            @hasrole('admin|gerente comercial')
                <a class="btn btn-info btn-xs float-right mr-2" href="{{ route('tabela-preco.index') }}">Tabela Preço</a>
            @endhasrole

            <table class="table table-responsive compact table-font-small" id="table-ordem-block">
            </table>
        </div>
    </div>
</div>
