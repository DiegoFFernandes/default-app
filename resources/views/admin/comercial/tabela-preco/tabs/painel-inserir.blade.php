<div class="tab-pane fade show active" id="painel-inserir" role="tabpanel">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="form-group form-group-sm">
                                <label class="small">Nome da Tabela</label>
                                <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group form-group-sm">
                                <label class="small">Selecione o Desenho</label>
                                <select class="form-control form-control-sm select2" id="desenho" name="desenho[]"
                                    style="width: 100%;" multiple>
                                    @foreach ($desenho as $item)
                                        <option value="{{ $item->ID }}">
                                            {{ $item->DESCRICAO }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group form-group-sm">
                                <label class="small">Selecione a Medida</label>
                                <select class="form-control form-control-sm select2" id="medida" name="medida[]"
                                    style="width: 100%" multiple="multiple">
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group form-group-sm">
                                <label class="small">Valor</label>
                                <input type="number" id="valor" class="form-control" placeholder="Digite o Valor"
                                    min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <button id="btn-associar" class="btn btn-danger btn-sm float-right">
                            Incluir na Prévia
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <x-loading-card />
                <div class="card-header">
                    <h3 class="card-title card-title-previa">
                        <i class="fas fa-table"></i>
                        Prévia Tabela
                    </h3>
                    <div class="card-tools m-0">

                        <button type="button" id="btn-recomecar" class="btn btn-secondary btn-xs btn-tools">
                            Recomecar
                        </button>
                        <button type="button" id="btn-deletar-itens" class="btn btn-secondary btn-xs btn-tools">
                            Deletar Itens
                        </button>

                        <button type="button" class="btn btn-primary btn-xs btn-tools" id="btn-adicional">
                            Adicionais
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="item-tabela-preco"
                            class="table compact table-font-small table-striped table-bordered"
                            style="width:100%; font-size: 11px;">
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-danger btn-sm float-right" id="btn-enviar-importar">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
