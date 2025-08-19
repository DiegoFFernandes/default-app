@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="table-notas-emitidas" class="table table-bordered table-striped compact table-font-small">
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
    <script>
        $(document).ready(function() {
            $('#table-notas-emitidas').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("get-list-nota-emitida") }}',
                columns: [{
                        data: 'NR_LANCAMENTO',
                        name: 'NR_LANCAMENTO',
                        title: 'Nº Lançamento'
                    },
                    {
                        data: 'NM_EMPRESA',
                        name: 'NM_EMPRESA',
                        title: 'Empresa'
                    },
                    {
                        data: 'NR_NOTA',
                        name: 'NR_NOTA',
                        title: 'Nº Nota'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente'
                    },
                    {
                        data: 'DS_DTEMISSAO',
                        name: 'DS_DTEMISSAO',
                        title: 'Data Emissão'
                    },
                    {
                        data: 'VL_CONTABIL',
                        name: 'VL_CONTABIL',
                        title: 'Valor Total'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Ações',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@stop
