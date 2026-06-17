<div class="tab-pane fade" id="pane-centro" role="tabpanel">
    <div class="card card-outline card-secondary mb-3 mt-1">
        <div class="card-header p-2">
            <h6 class="mb-0"><i class="fas fa-toggle-on mr-1 text-danger"></i> Habilitar Centro de Resultado por Empresa</h6>
        </div>
        <div class="card-body p-2">
            <div class="row">
                @foreach($empresas as $e)
                <div class="col-md-4 col-lg-3 mb-1">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input chk-usa-centro"
                            id="cc_emp_{{ $e->CD_EMPRESA }}"
                            data-empresa="{{ $e->CD_EMPRESA }}"
                            {{ ($paramMap->get($e->CD_EMPRESA) && $paramMap->get($e->CD_EMPRESA)->ST_USA_CENTROCUSTO === 'S') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="cc_emp_{{ $e->CD_EMPRESA }}">
                            {{ $e->NM_EMPRESA }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered table-font-small compact" id="table-centros" style="width:100%">
        <thead>
            <tr>
                <th>Empresa</th>
                <th width="70px">Código</th>                
                <th>Centro de Resultado</th>
                <th width="160px">Orçamento Mensal</th>
                 <th width="70px">Reinicia Dia</th>
                <th>Responsável</th>               
                <th width="60px" class="text-center">Ações</th>
            </tr>
        </thead>
    </table>
</div>
