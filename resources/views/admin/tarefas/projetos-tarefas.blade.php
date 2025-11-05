@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Áreas de Trabalho</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-add-projeto" data-toggle="modal"
                                data-target="#modal-adicionar-projeto-tarefa">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($projeto as $p)
                                <div class="col-md-3">
                                    <div class="card card-outline card-secondary card-projeto">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ $p->nome }}</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool btn-ir-tarefas">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                                <input type="text" class="d-none projeto-id" value="{{ $p->encrypted_id }}">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            {{ $p->descricao }}
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        .card-projeto {            
            height: 150px;
        }

        .card-projeto:hover {
            cursor: pointer;
            background-color: #cacbcc;
            color: rgb(0, 0, 0);
        }

        .card-projeto:hover .btn-ir-tarefas {

            color: rgb(0, 0, 0);
        }
    </style>
@stop
@section('js')
    <script>
        $(document).on('click', '.card-projeto', function() {
            var projetoId = $(this).find('.projeto-id').val();          

            const route = '{{ route('tarefas-quadro', ':id') }}';
            window.location.href = route.replace(':id', projetoId);
        });
    </script>
@stop
