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
                <label for="{{ $pessoa }}" class="form-label small">Pessoa</label>
                <select name='pessoa' class="form-control form-control-sm" id="{{ $pessoa }}"
                    style="width: 100%">
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_gerente }}" class="form-label small">Gerente Comercial</label>
                <select name="gerente" id="{{ $filtro_gerente }}" class="form-control form-control-sm"
                    style="width: 100%">
                    <option value="0">Todos Gerentes</option>
                    @foreach ($gerentes as $g)
                        <option value="{{ $g->cd_usuario }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_supervisor }}" class="form-label small">Supervisor</label>
                <input id="{{ $filtro_supervisor }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por Supervisor">
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_vendedor }}" class="form-label small">Vendedor</label>
                <input id="{{ $filtro_vendedor }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por Vendedor">
            </div>
            <div class="col-md-3 mb-2">
                <label for="{{ $filtro_cnpj }}" class="form-label small">CNPJ</label>
                <input id="{{ $filtro_cnpj }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por CNPJ">
            </div>
            <div class="col-md-3 mb-2">
                <label for="{{ $daterange }}" class="form-label small">Período</label>
                <input id="{{ $daterange }}" type="text" class="form-control form-control-sm"
                    placeholder="{{ $placeholderDatarange }}">
            </div>
            <div class="col-md-2 mb-2">
                <label for="{{ $filtro_cartorio }}" class="form-label small">Cartório/Protesto</label>
                <select name="cartorio" id="{{ $filtro_cartorio }}" class="form-control form-control-sm"
                    style="width: 100%">
                    <option value="0">Nenhum</option>
                    <option value="1">Com Cartorio</option>
                    <option value="2">Sem Cartorio</option>
                </select>
            </div>

        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-default btn-sm float-right" id="{{ $btn_reset }}">
                        <i class="fas fa-eraser"></i> Limpar
                    </button>
                    <button type="button" class="btn btn-success btn-sm float-right mr-2" id="{{ $btn_search }}">
                        <i class="fas fa-check"></i> Buscar
                    </button>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
