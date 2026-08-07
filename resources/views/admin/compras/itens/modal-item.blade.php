{{-- Modal de cadastro/edição de item próprio (COMPRA_ITEM). HTML apenas.
     O script fica em itens.modal-item-script, incluído no @section('js') da página. --}}
@php
    // Unidades de medida padronizadas. Editar aqui para incluir/remover.
    $unidadesCompra = [
        'UN' => 'UNIDADE',
        'PC' => 'PEÇA',
        'CX' => 'CAIXA',
        'KG' => 'QUILOGRAMA',
        'LT' => 'LITRO',
        'ML' => 'MILILITRO',
        'MT' => 'METRO',
        'M2' => 'METRO QUADRADO',
        'M3' => 'METRO CÚBICO',
        'RL' => 'ROLO',
        'TB' => 'TAMBOR',
        'GL' => 'GALÃO',
        'BD' => 'BALDE',
        'PC' => 'PACOTE',
        'FD' => 'FARDO',
        'SC' => 'SACO',
        'PA' => 'PAR',
        'JG' => 'JOGO',
        'KT' => 'KIT',
    ];
@endphp
<div class="modal fade" id="modal-compra-item" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-compra-item-title">Novo Item</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ci_cd">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Descrição <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="ci_ds_item" maxlength="200"
                        placeholder="Ex: Parafuso">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Subgrupo</small></label>
                    <select class="form-control form-control-sm" id="ci_subgrupo" style="width:100%">
                        <option value="">Selecione o subgrupo</option>
                        @foreach(($subgruposCompra ?? []) as $sg)
                            <option value="{{ $sg->CD_SUBGRUPO }}">{{ $sg->DS_SUBGRUPO }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Unidade<span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="ci_sg_unidmed">
                                <option value="">Selecione...</option>
                                @foreach ($unidadesCompra as $sigla => $descricao)
                                    <option value="{{ $sigla }}">{{ $sigla }} — {{ $descricao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Status</small></label>
                            <select class="form-control form-control-sm" id="ci_st_ativo">
                                <option value="S">Ativo</option>
                                <option value="N">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-compra-item" class="btn btn-primary btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
