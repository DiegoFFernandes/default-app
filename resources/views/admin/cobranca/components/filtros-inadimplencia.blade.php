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
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                {{-- <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Cliente"> --}}
                <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select name="gerente" id="filtro-gerente" class="form-control" style="width: 100%">
                    <option value="0">Todos Gerentes</option>
                    @foreach ($gerentes as $g)
                        <option value="{{ $g->cd_usuario }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <input id="filtro-supervisor" type="text" class="form-control" placeholder="Filtrar por Supervisor">
            </div>
            <div class="col-md-4 mb-2">
                <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
            </div>
            <div class="col-md-4 mb-2">
                <input id="filtro-cnpj" type="text" class="form-control" placeholder="Filtrar por CNPJ">
            </div>
            <div class="col-md-4 mb-2">
                <input id="daterange" type="text" class="form-control" placeholder="Filtrar por Vencimento">
            </div>

        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-default btn-sm float-right" id="btn-reset">
                        <i class="fas fa-eraser"></i> Limpar
                    </button>
                    <button type="button" class="btn btn-success btn-sm float-right mr-2" id="btn-search">
                        <i class="fas fa-check"></i> Buscar
                    </button>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
