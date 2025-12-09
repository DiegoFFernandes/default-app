@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Itens Estoque - Negativo</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-font-small table-bordered" id="estoque-negativo">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Ds Item</th>
                                    <th>Saldo</th>
                                    <th>Custo</th>
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
    </section>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            $('#estoque-negativo').DataTable({
                processing: false,
                serverSide: false,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                pagingType: "simple",
                ajax: {

                    url: '{{ route("get-estoque-negativo") }}'
                },
                columns: [{
                        data: 'CD_ITEM',
                        name: 'CD_ITEM',
                        title: 'id'
                    },
                    {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        title: 'Ds Item'
                    },
                    {
                        data: 'QTD_SALDO',
                        name: 'QTD_SALDO',
                        title: 'Saldo'
                    },
                    {
                        data: 'O_VL_CUSTO',
                        name: 'O_VL_CUSTO',
                        title: 'Custo'
                    }
                ],
                order: [
                    [0, "desc"]
                ],
            });
        });
    </script>
@endsection
