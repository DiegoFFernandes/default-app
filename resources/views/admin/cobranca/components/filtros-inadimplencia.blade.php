<div class="card collapsed-card mb-3">
    <div class="card-header ui-sortable-handle">
        <h3 class="card-title"><i class="fas fa-filter mr-1 text-muted"></i> Filtros</h3>
        <div class="card-tools m-0">
           
            @role('admin')
                @if(!isset($tela) || $tela == 1)
                <button type="button" class="btn btn-tool btn-parametros-cogs"
                        data-toggle="modal" data-target="#modal-parametros-cogs"
                        title="Parâmetros de inadimplência">
                    <i class="fas fa-cogs"></i>
                </button>
                @endif
            @endrole
             <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label for="{{ $pessoa }}" class="form-label small"><i class="fas fa-user mr-1 text-muted"></i>Pessoa</label>
                <select name='pessoa' class="form-control form-control-sm" id="{{ $pessoa }}"
                    style="width: 100%"
                    @if(isset($pessoa_multiple) && $pessoa_multiple)
                        multiple
                        data-placeholder="Selecione uma ou mais pessoas"
                    @else
                        data-placeholder="Selecione uma pessoa"
                    @endif>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_gerente }}" class="form-label small"><i class="fas fa-user-tie mr-1 text-muted"></i>Gerente Comercial</label>
                <select name="gerente" id="{{ $filtro_gerente }}" class="form-control form-control-sm"
                    style="width: 100%">
                    <option value="0">Todos Gerentes</option>
                    @foreach ($gerentes as $g)
                        <option value="{{ $g->cd_usuario }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_supervisor }}" class="form-label small"><i class="fas fa-user-shield mr-1 text-muted"></i>Supervisor</label>
                <input id="{{ $filtro_supervisor }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por Supervisor">
            </div>
            <div class="col-md-4 mb-2">
                <label for="{{ $filtro_vendedor }}" class="form-label small"><i class="fas fa-user-tag mr-1 text-muted"></i>Vendedor</label>
                <input id="{{ $filtro_vendedor }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por Vendedor">
            </div>
            <div class="col-md-3 mb-2">
                <label for="{{ $filtro_cnpj }}" class="form-label small"><i class="fas fa-id-card mr-1 text-muted"></i>CNPJ</label>
                <input id="{{ $filtro_cnpj }}" type="text" class="form-control form-control-sm"
                    placeholder="Filtrar por CNPJ">
            </div>
            <div class="col-md-3 mb-2">
                <label for="{{ $daterange }}" class="form-label small"><i class="fas fa-calendar-alt mr-1 text-muted"></i>Período</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input id="{{ $daterange }}" type="text" class="form-control form-control-sm"
                        placeholder="{{ $placeholderDatarange }}">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <label for="{{ $filtro_cartorio }}" class="form-label small"><i class="fas fa-university mr-1 text-muted"></i>Cartório/Protesto</label>
                <select name="cartorio" id="{{ $filtro_cartorio }}" class="form-control form-control-sm"
                    style="width: 100%">
                    <option value="0">Nenhum</option>
                    <option value="1">Com Cartorio</option>
                    <option value="2">Sem Cartorio</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-default btn-sm mr-2" id="{{ $btn_reset }}">
                <i class="fas fa-eraser"></i> Limpar
            </button>
            <button type="button" class="btn btn-success btn-sm" id="{{ $btn_search }}">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </div>
</div>
