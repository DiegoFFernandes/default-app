<div class="card card-outline card-secondary mb-3">
    <div class="card-header py-2">
        <h6 class="card-title mb-0">
            <i class="fas fa-filter mr-1 text-secondary"></i> Filtros
        </h6>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body py-2">
        <div class="row align-items-end">
            <div class="col-12 col-md-2 mb-2">
                <label class="small font-weight-bold mb-1" for="filtro-empresa">
                    <i class="fas fa-building mr-1 text-muted"></i> Empresa
                </label>
                <select id="filtro-empresa" class="form-control form-control-sm">
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->CD_EMPRESA }}">{{ $empresa->NM_EMPRESA }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <label class="small font-weight-bold mb-1" for="daterange">
                    <i class="fas fa-calendar-alt mr-1 text-muted"></i> Período
                </label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="far fa-calendar-alt text-secondary"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-sm" id="daterange"
                        placeholder="Selecione o período">
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <label class="small font-weight-bold mb-1" for="filtro-executor">
                    <i class="fas fa-user-hard-hat mr-1 text-muted"></i> Executor
                </label>
                <select id="filtro-executor" class="form-control form-control-sm" multiple>
                    <option value="0" selected>Todos</option>
                    @foreach ($executores as $executor)
                        <option value="{{ $executor->ID }}">{{ $executor->NMEXECUTOR }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2">
                <button type="button" class="btn btn-primary btn-block btn-sm" id="submit-seach">
                    <i class="fas fa-search mr-1"></i> Buscar
                </button>
            </div>
        </div>
    </div>
</div>
