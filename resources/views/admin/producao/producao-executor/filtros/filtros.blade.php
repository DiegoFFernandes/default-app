<div class="card card-outline card-dark collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Filtros:</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-2 mb-2">
                <div class="form-group mb-0">
                    <label class="small" for="filtro-empresa">Empresa:</label>
                    <select id="filtro-empresa" class="form-control form-control-sm mt-1">
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->CD_EMPRESA }}">{{ $empresa->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <div class="form-group mb-0">
                    <label class="small" for="daterange">Data:</label>
                    <div class="input-group mt-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm" id="daterange"
                            placeholder="Selecione a Data">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <div class="form-group mb-0">
                    <label class="small" for="filtro-executor">Executor:</label>
                    <select id="filtro-executor" class="form-control form-control-sm mt-1" multiple>
                        <option value="0" selected>Todos</option>
                        @foreach ($executores as $executor)
                            <option value="{{ $executor->ID }}">{{ $executor->NMEXECUTOR }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary btn-block btn-sm" id="submit-seach">Buscar</button>
            </div>
        </div>
    </div>
</div>
