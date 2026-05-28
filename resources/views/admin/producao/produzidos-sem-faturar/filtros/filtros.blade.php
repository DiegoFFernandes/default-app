 <div class="row">
     <div class="col-md-12">
         <div class="card collapsed-card">
             <div class="card-header">
                 <h3 class="card-title">Filtros:</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse">
                         <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                     </button>
                 </div>
             </div>
             <div class="card-body pt-1 pb-2">
                 <div class="row">
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Empresa</label>
                             <select name="cd_empresa" id="cd_empresa" class="form-control form-control-sm"
                                 style="width: 100%;">
                                 <option value="0" selected>Todas</option>
                                 @foreach ($empresa as $e)
                                     <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Dt Emissão</label>
                             <input type="text" class="form-control form-control-sm" id="daterange"
                                 placeholder="Data Emissão">
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Pedido Palm</label>
                             <input type="number" class="form-control form-control-sm" id="pedido_palm"
                                 placeholder="Pedido Palm">
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Pedido</label>
                             <input type="number" class="form-control form-control-sm" id="pedido"
                                 placeholder="Pedido">
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="form-group">
                             <label class="small">Grupo Item</label>
                             <select name="grupo_item" id="grupo_item" class="form-control form-control-sm"
                                 style="width: 100%;">
                                 <option value="0">Todos</option>
                                 {{-- @foreach ($grupo as $g)
                                        <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}
                                        </option>
                                    @endforeach --}}
                             </select>
                         </div>
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Vendedor</label>
                             <input type="text" class="form-control form-control-sm" id="nm_vendedor"
                                 placeholder="Nome Vendedor">
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Supervisor</label>
                             <select name="supervisor" id="supervisor" class="form-control form-control-sm">
                                    <option value="0">Todos</option>
                                 @foreach ($supervisor as $s)
                                     <option value="{{ $s->CD_VENDEDORGERAL }}">{{ $s->NM_SUPERVISOR }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Cliente</label>
                             <input type="text" class="form-control form-control-sm" id="nm_cliente"
                                 placeholder="Nome Cliente">
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="form-group">
                             <label class="small">Status Embarque</label>
                             <select name="st_embarque" id="st_embarque" class="form-control form-control-sm"
                                 style="width: 100%;">
                                 <option value="0">Todos</option>
                                 <option value="1">Com Embarque</option>
                                 <option value="2">Sem Embarque</option>
                             </select>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="form-group">
                             <label class="small">Região</label>
                             <select name="cd_regiaocomercial[]" class="form-control form-control-sm"
                                 id="cd_regiaocomercial" style="width: 100%;" multiple>
                                 @foreach ($regiao as $r)
                                     <option value="{{ $r->CD_REGIAOCOMERCIAL }}">
                                         {{ $r->DS_REGIAOCOMERCIAL }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                 </div>
                 <div class="card-footer p-1">
                     <div class="row">
                         <div class="col-md-12">
                             <button type="button" class="btn btn-primary btn-xs float-right mr-2"
                                 id="search">Filtrar</button>
                         </div>
                         <!-- /.row -->
                     </div>
                 </div>
                 <!-- /.row -->
             </div>
         </div>
     </div>
 </div>
