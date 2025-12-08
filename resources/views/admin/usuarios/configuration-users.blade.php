@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Notificações Estoque</h6>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="float-right mb-2">
                            <button class="btn btn-xs btn-danger" id="btn-add-notification-user">Adicionar</button>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <div class="modal fade" id="modal-add-notification" tabindex="-1" role="dialog"
            aria-labelledby="modal-add-notification-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Adicionar Usuário</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">Usuário</label>
                            <select class="form-control" name="cd_usuario" id="cd_usuario" style="width: 100%">
                                <option value="" selected="selected">Selecione</option>
                                @foreach ($user as $u)
                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo_notificacao">Tipo de Notificação</label>
                            <select class="form-control" name="tipo_notificacao" id="tipo_notificacao" style="width: 100%">
                                <option value="EstoqueNegativo" selected="selected">Estoque</option>
                                <option value="PedidoBloqueado" selected="selected">Pedidos Bloqueados</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary btn-xs" id="btn-save-notification">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            $('#cd_usuario').select2({
                theme: 'bootstrap4'
            });
            $('#btn-add-notification-user').on('click', function() {
                $('#modal-add-notification').modal('show');
            });
        });
    </script>
@endsection
