@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
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
                        <div class="row" id="projetos-container">
                            {{-- Projetos serão carregados via JS --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modal-adicionar-projeto-tarefa">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Adicionar</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nome">Titulo</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Titulo"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descrição" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-sm btn-primary btn-salvar-projeto-tarefa">Salvar</button>
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
        initProjetosTarefas();
        $(document).on('click', '.btn-ir-tarefas', function() {
            var projetoId = $(this).data('id');

            const route = '{{ route('tarefas-quadro', ':id') }}';
            window.location.href = route.replace(':id', projetoId);
        });

        $('.btn-add-projeto').on('click', function() {
            $('#modal-adicionar-projeto-tarefa').modal('show');
        });

        $(document).on('click', '.btn-salvar-projeto-tarefa', function() {
            var nome = $('#nome').val();
            var descricao = $('#descricao').val();

            $.ajax({
                url: '{{ route('salvar-projeto-tarefa') }}',
                method: 'POST',
                data: {
                    nome: nome,
                    descricao: descricao,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao salvar o projeto de tarefa.',
                    });
                }
            });
        });

        $(document).on('click', '.btn-remover-projeto', function() {
            var projetoId = $(this).data('id');

            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação não pode ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('remover-projeto') }}',
                        method: 'POST',
                        data: {
                            id: projetoId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Ocorreu um erro ao remover o projeto de tarefa.',
                            });
                        }
                    });
                }
            });
        });

        function initProjetosTarefas() {
            $.ajax({
                type: "GET",
                url: "{{ route('listar-projetos') }}",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $html = '';
                    response.forEach(projeto => {
                        $html += `
                        <div class="col-md-3">
                            <div class="card card-outline card-secondary card-projeto">
                                <div class="card-header">
                                    <h3 class="card-title">${ projeto.nome }</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-danger btn-xs btn-remover-projeto" data-id="${ projeto.encrypted_id }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs btn-ir-tarefas" data-id="${ projeto.encrypted_id }">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>                                      
                                    </div>
                                </div>
                                <div class="card-body">
                                    ${ projeto.descricao }
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    `;
                    });
                    $('#projetos-container').html($html);
                }
            });
        }
    </script>
@stop
