@extends('layouts.master')

@section('title', 'Contagem de Estoque')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-4 mb-3">
                <div class="card card-dark card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Contagem Estoque</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="tp_lote" class="small">Tipo Lote</label>
                            <select class="form-control form-control-sm" id="tp_lote">
                                <option value="entrada">Entrada</option>
                                <option value="emprestimo">Empréstimo</option>
                                <option value="inventario">Inventario</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cd_subgrupo" class="small">Produto</label>
                            <select class="form-control form-control-sm" id="cd_subgrupo">
                                <option value="0">Selecione</option>
                                {{-- @foreach ($subgrupo as $s)
                                    <option value="{{ $s->id }}">{{ $s->ds_marca }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cd_marca" class="small">Marca</label>
                            <select class="form-control form-control-sm" id="cd_marca">
                                <option value="0">Selecione</option>
                                {{-- @foreach ($marca as $m)
                                    <option value="{{ $m->id }}">{{ $m->ds_marca }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ds_lote" class="small">Descrição</label>
                            <input type="text" class="form-control form-control-sm" id="ds_lote"
                                placeholder="Descrição para o Lote: Banda/Consertos...">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-success float-right" id="btnCriarLote">Criar Lote</button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8 mb-3">
                <div class="card card-dark card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lotes Criados</h3>
                    </div>
                    <div class="card-body">
                        <table id="table-lote" class="table nowrap table-bordered table-font-small" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <th style="width: 10px">Cód.</th>  
                                    <th>Descrição</th>                                  
                                    <th>Tipo Produto</th>
                                    <th>Marca</th>
                                    <th>Qtda Items</th>
                                    <th>Peso Liquido</th>
                                    <th>Status</th>
                                    <th>Tipo Lote</th>
                                    <th>Usúario</th>                                    
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')

@stop
@section('js')
    <script type="text/javascript">
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
        }
    </script>
@endsection
