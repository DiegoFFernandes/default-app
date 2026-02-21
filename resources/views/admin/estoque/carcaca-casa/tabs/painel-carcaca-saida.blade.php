<div class="tab-pane fade" id="painel-carcaca-saida" role="tabpanel" aria-labelledby="tab-carcaca-saida">
    <div class="card-body p-2">
        <div class="col-md-8" id="div-tabela-carcacas" @if ($canEdit) style="display:none;" @endif>
            <div class="card-header">
                <h6 class="card-title">Baixas</h6>
            </div>
            <div class="card-body pb-0">
                <table class="table table-bordered compact table-font-small table-responsive"
                    id="estoque-carcacas-baixas">
                </table>
            </div>
        </div>
    </div>
</div>
