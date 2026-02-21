 <div class="tab-pane fade show active" id="painel-carcaca-entrada" role="tabpanel" aria-labelledby="tab-carcaca-entrada">
     <div class="card-body p-2">
         <div class="row">
             <div class="col-md-8" id="div-tabela-carcacas">
                 <div class="card-header">
                     @include('admin.estoque.components.buttons-estoque-carcaca')
                     <div class="card-tools m-0">
                         <button class="btn btn-xs btn-danger" id="btn-add-carcaca" title="Adicionar Carcaça"
                             @if ($canEdit) style="display:none;" @endif>
                             <i class="fas fa-plus"></i></button>
                         <button class="btn btn-xs btn-danger" id="download-itens" title="Fazer Download"><i
                                 class="fas fa-download"></i></button>
                     </div>
                 </div>
                 <div class="card-body pb-0">
                     <table class="table table-bordered compact table-font-small" id="estoque-carcacas">
                     </table>
                 </div>
                 <div class="card-footer pt-0">
                     @include('admin.estoque.components.buttons-estoque-carcaca')
                 </div>
             </div>

             <div class="col-md-4">
                 <div class="row">
                     <div class="col-12 col-sm-12 col-md-12">
                         <div class="info-box">
                             <div class="info-box-content">
                                 <span class="info-box-text">Total</span>
                                 <span class="info-box-number">
                                     <span id="total-carcacas"></span>
                                     <small>Unidades</small>
                                 </span>
                             </div>
                             <!-- /.info-box-content -->
                         </div>
                         <!-- /.info-box -->
                     </div>
                     <div class="col-12 col-md-12">
                         <div class="card">
                             <div class="card-header">
                                 <h6 class="card-title">Resumo Local</h6>
                                 <div class="card-tools m-0">
                                     <button class="btn btn-xs btn-danger" id="download-resumo-local"><i
                                             class="fas fa-download"></i></button>
                                 </div>
                             </div>
                             <div class="card-body">
                                 <div id="accordionResumoLocal" class="d-none"></div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

     </div>
 </div>
