<div class="tab-pane fade show active" id="notas-vendedores-divergentes" role="tabpanel" aria-labelledby="tab-inserir">
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header py-2">
                    <div class="d-flex align-items-center" style="gap:6px;">
                        <button type="button" id="btn-alterar-vendedor" class="btn btn-primary btn-xs btn-sm-phone mr-1"
                            title="Alterar o vendedor da nota para o vendedor da comissão">
                            <i class="fas fa-exchange-alt mr-1"></i> Substituir pelo Vendedor da Nota
                        </button>
                        <button type="button" id="btn-manter-vendedor" class="btn btn-success btn-xs btn-sm-phone mr-1"
                            title="Manter o vendedor da comissão igual ao vendedor da nota">
                            <i class="fas fa-user-check mr-1"></i> Manter Vendedor da Comissão
                        </button>
                        <span id="vendedor-nota-count-badge" class="badge badge-warning"
                            style="display:none; font-size:0.8rem;"></span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table compact table-font-small" id="table-notas-vendedores-divergentes">
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
