@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-success btn-sm" id="btn-create">Criar novo</button>
                    </div>
                    <!-- /.box-header -->
                    <div class="card-body">
                        <table class="table display table-sm compact" id="table-roles" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Empresa</th>
                                    <th>Funções</th>
                                    <th>Acões</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
    </section>
    <!-- /.content -->
@stop


@section('js')
    <script>
        $('#table-roles').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            },
            pagingType: "simple",
            processing: false,
            responsive: true,
            serverSide: false,
            autoWidth: false,
            order: [
                [0, "desc"]
            ],
            "pageLength": 10,
            ajax: {
                url: "{{ route('usuario.list_role') }}",
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'empresa',
                    name: 'empresa'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ]
        });
        $('#table-roles').on('click', '.btn-delete', function(e) {
            // var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            var rowData = $('#table-roles').DataTable().row($(this).parents('tr')).data();
            console.log(rowData);
        });
        $('#btn-create').click(function(){
            
        });
    </script>
@stop
