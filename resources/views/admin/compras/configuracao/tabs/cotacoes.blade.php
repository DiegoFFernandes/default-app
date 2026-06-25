<div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
    <p class="text-muted mb-3" style="font-size:0.85rem">
        <i class="fas fa-info-circle mr-1"></i>
        Define quantas cotações são necessárias e quem é o responsável pelas compras de cada empresa.
    </p>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Empresa</th>
                    <th style="width:170px" class="text-center">Mínimo de Cotações</th>
                    <th>Responsável pela Compra</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($empresas as $emp)
                @php
                    $param      = $paramMap->get($emp->CD_EMPRESA);
                    $qtd        = $param->QTD_FORNEC_COT ?? 3;
                    $cdPessoa   = $param->CD_PESSOA_COMPRA ?? '';
                    $nmPessoa   = $param->NM_COMPRADOR ?? '';
                    $nrCelular  = $param->NR_CELULAR ?? '';
                @endphp
                <tr>
                    <td class="align-middle">{{ $emp->NM_EMPRESA ?? $emp->CD_EMPRESA }}</td>
                    <td class="text-center align-middle">
                        <input type="number"
                               class="form-control form-control-sm text-center input-qtd-fornec mx-auto"
                               style="width:90px"
                               data-empresa="{{ $emp->CD_EMPRESA }}"
                               value="{{ $qtd }}"
                               min="1" max="99">
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center" style="gap:8px">
                            <div style="flex:1;min-width:0">
                                <select class="form-control form-control-sm select2-pessoa-compra"
                                        style="width:100%"
                                        data-empresa="{{ $emp->CD_EMPRESA }}"
                                        data-cd="{{ $cdPessoa }}"
                                        data-nm="{{ $nmPessoa }}">
                                </select>
                            </div>
                            @if($nrCelular)
                                <small class="text-muted text-nowrap"><i class="fas fa-mobile-alt mr-1"></i>{{ $nrCelular }}</small>
                            @endif
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <button class="btn btn-primary btn-xs btn-salvar-qtd-fornec"
                                data-empresa="{{ $emp->CD_EMPRESA }}"
                                title="Salvar">
                            <i class="fas fa-save"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
