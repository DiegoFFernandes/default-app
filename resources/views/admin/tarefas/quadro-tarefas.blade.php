@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row d-flex align-items-stretch">
                <div class="col-md-3 d-flex">
                    <div class="card card-secondary kanban-column flex-fill">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Pendências</h3>
                            <button class="btn btn-tool btn-add-card ml-auto" data-coluna="pendencias">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="card-body kanban-cards" id="pendencias">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card card-primary kanban-column flex-fill">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">A Fazer</h3>
                            <button class="btn btn-tool btn-add-card ml-auto" data-coluna="fazer">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="card-body kanban-cards" id="fazer">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card card-info kanban-column flex-fill">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Em Progresso</h3>
                            <button class="btn btn-tool btn-add-card ml-auto" data-coluna="progresso">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="card-body kanban-cards" id="progresso">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card card-success kanban-column flex-fill">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Concluído</h3>
                            <button class="btn btn-tool btn-add-card ml-auto" data-coluna="concluido">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="card-body kanban-cards" id="concluido">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCard" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCardTitle">Criar/Editar Card</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formCard">
                            <input type="hidden" id="colunaDestino">
                            <input type="hidden" id="cardId">
                            <div class="mb-3">
                                <label for="inputTitulo">Título</label>
                                <input type="text" class="form-control" id="inputTitulo" required  placeholder="Digite um título">
                            </div>
                            <div class="mb-3">
                                <label for="inputDescricao">Descrição</label>
                                <textarea class="form-control" id="inputDescricao" rows="3" required  placeholder="Adicione uma descrição..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Fechar</button>
                        <div>
                            <button type="button" class="btn btn-sm btn-danger" id="btn-delete">Excluir</button>
                            <button type="button" class="btn btn-sm btn-primary" id="btn-save">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script>
        $(function() {
            $('.kanban-cards').sortable({
                connectWith: '.kanban-cards',
                handle: '.card-header',
                forcePlaceholderSize: true,
            }).disableSelection();

            //modal adicionar card
            $('.btn-add-card').click(function() {
                var coluna = $(this).data('coluna');
                $('#modalCardTitle').text('Criar Card');
                $('#colunaDestino').val(coluna);
                $('#cardId').val('');
                $('#inputTitulo').val('');
                $('#inputDescricao').val('');
                $('#btn-delete').hide();
                $('#modalCard').modal('show');
            });

            //salva/atualiza o card
            $('#btn-save').click(function() {
                var idCard = $('#cardId').val();
                if (!idCard) {
                    idCard = `card-${Date.now()}`;
                }
                var titulo = $('#inputTitulo').val();
                var descricao = $('#inputDescricao').val();
                var coluna = $('#colunaDestino').val();

                if (titulo.trim() && descricao.trim()) {
                    gerarcard(idCard, titulo, descricao, coluna);
                    $('#modalCard').modal('hide');
                } else {
                    alert("Por favor, preencha o título e a descrição.");
                }
            });

            //modal editar card
            $(document).on('click', '.btn-edit-card', function() {
                var card = $(this).closest('.card');
                var idCard = card.data('task-id');
                var titulo = card.find('.card-title').text();
                var descricao = card.find('.card-body').text();
                var coluna = card.closest('.kanban-cards').attr('id');

                $('#modalCardTitle').text('Editar Card');
                $('#colunaDestino').val(coluna);
                $('#cardId').val(idCard);
                $('#inputTitulo').val(titulo);
                $('#inputDescricao').val(descricao);
                $('#btn-delete').show();
                $('#modalCard').modal('show');
            });

            $('#btn-delete').click(function() {
                var idCard = $('#cardId').val();
                if (idCard) {
                    $(`[data-task-id='${idCard}']`).remove();
                    $('#modalCard').modal('hide');
                }
            });

            // gera/atualiza os cards dinamicos
            function gerarcard(idCard, titulo, descricao, colunaId) {
                var card = $(`[data-task-id='${idCard}']`);
                if (card.length > 0) {
                    //atualiza o card
                    card.find('.card-title').text(titulo);
                    card.find('.card-body').text(descricao);
                } else {
                    //cria um card novo
                    var cardHTML = `
                        <div class="card card-light card-outline" data-task-id="${idCard}">
                            <div class="card-header">
                                <h5 class="card-title">${titulo}</h5>
                                    <div class="card-tools">
                                       <button type="button" class="btn btn-tool btn-edit-card"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>
                            <div class="card-body">${descricao}</div>
                        </div>
                     `;
                    $(`#${colunaId}`).append(cardHTML);
                }
            }

            function initColunas() {
                $.ajax({
                    url: '{{ route("listar-tarefas") }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(function(tarefa) {
                            var colunaId = '';
                            switch (tarefa.status) {
                                case 'pendencias':
                                    colunaId = 'pendencias';
                                    break;
                                case 'fazer':
                                    colunaId = 'fazer';
                                    break;
                                case 'progresso':
                                    colunaId = 'progresso';
                                    break;
                                case 'concluido':
                                    colunaId = 'concluido';
                                    break;
                                default:
                                    colunaId = 'pendencias';
                            }
                            gerarcard(tarefa.id, tarefa.titulo, tarefa.descricao, colunaId);
                        });
                    },
                    error: function() {
                        console.error('Erro ao carregar as tarefas.');
                    }
                });
            }
        });
    </script>
@stop