@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $projeto->nome }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool btn-warning btn-modal-add-coluna" title="Adicionar Coluna"
                            id="">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool btn-warning" title="Colunas Arquivadas">
                            <i class="fas fa-archive"></i>
                        </button>
                        <button type="button" class="btn btn-tool btn-primary" onclick="initColunas()" title="Recarregar">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body"
                    style="background-color: #f4f6f9;>
                    <!-- Main content -->
                    <section class="content">
                    <div class="container-fluid">
                        <div class="row d-flex align-items-stretch" id="tarefasContainer">
                            {{-- as colunas serão carregadas aqui via AJAX --}}
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
                                        <input type="hidden" id="id">
                                        <div class="mb-3">
                                            <label for="inputTitulo">Título</label>
                                            <input type="text" class="form-control" id="inputTitulo" required
                                                placeholder="Digite um título">
                                        </div>
                                        <div class="mb-3">
                                            <label for="inputDescricao">Descrição</label>
                                            <div class="form-control" id="inputDescricao" rows="3" required
                                                placeholder="Adicione uma descrição..."></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        data-dismiss="modal">Fechar</button>
                                    <div id="btn-action">
                                        {{-- os botões vem aqui --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalColuna" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalColunaTitle">Editar Coluna</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="colunaId">
                                    <div class="row">
                                        <div class="form-group col-md-10">
                                            <label for="inputNomeColuna">Nome da Coluna</label>
                                            <input type="text" class="form-control" id="inputNomeColuna" required
                                                placeholder="Digite o nome da coluna">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="inputCorColuna">Cor</label>
                                            <input type="color" class="form-control" id="inputCorColuna">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        data-dismiss="modal">Fechar</button>
                                    <div id="btn-action-coluna">
                                        <button type="button" class="btn btn-sm btn-primary"
                                            id="btn-edit-coluna">Editar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@stop
@section('css')
    <style>
        /* Oculta os botões de ação por padrão */
        .column-actions {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        /* Mostra os botões ao passar o mouse no header */
        .card-header-coluna:hover .column-actions {
            opacity: 1;
            visibility: visible;
        }

        /* Ajustes estéticos dos botões */
        .column-actions .btn {
            color: rgba(63, 62, 62, 0.8);
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .column-actions .btn:hover {
            color: rgba(63, 62, 62, 0.8);
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        /* Garante que o título e os botões fiquem bem alinhados */
        .card-header-coluna {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
        }


        /* Garante que o título e os botões fiquem bem alinhados */
        .card-header-cartao {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
        }

        /* Em telas pequenas, mostra sempre os botões (sem hover) */
        @media (max-width: 768px) {
            .column-actions {
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
    </style>
@stop

@section('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        initColunas();

        // define só o que você quer no editor
        const toolbarOptions = [
            ['bold'], // negrito 
            ['italic'], // itálico
            ['underline'], // sublinhado  
            [{
                'color': []
            }, {
                'background': []
            }],
            ['clean'] // limpar formatação
        ];

        const descricao_tarefa = new Quill('#inputDescricao', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow'
        });

        //modal adicionar card
        $(document).on('click', '.btn-add-card', function() {
            var colunaId = $(this).data('coluna-id');
            $('#modalCardTitle').text('Criar Tarefa');
            $('#colunaDestino').val(colunaId);
            $('#cardId').val('');
            $('#inputTitulo').val('');
            descricao_tarefa.root.innerHTML = '';
            $('#modalCard').modal('show');

            $('#btn-action').html(`
                <button type="button" class="btn btn-sm btn-primary" id="btn-save-card">Adicionar</button>                
            `);

            $('#id').val($(this).data('id'));
        });

        //salva o card
        $(document).on('click', '#btn-save-card', function() {
            var idCard = $('#cardId').val();
            if (!idCard) {
                idCard = `card-${Date.now()}`;
            }
            var titulo = $('#inputTitulo').val();
            var descricao = descricao_tarefa.root.innerHTML;
            var coluna = $('#colunaDestino').val();

            if (titulo.trim()) {
                var dados = {
                    id: idCard,
                    titulo: titulo,
                    descricao: descricao,
                    coluna: $('#id').val(),
                };
                salvarTarefas(dados, '{{ route('salvar-tarefas') }}').done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        renderCartoes($('#id').val());
                        $('#modalCard').modal('hide');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message,
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Título é obrigatório.',
                    timer: 2500
                });
            }
        });

        //modal editar card
        $(document).on('click', '.btn-edit-card', function() {
            var card = $(this).closest('.card');
            var idCard = card.data('task-id');
            var titulo = card.find('.card-title').text();
            var descricao = card.find('.card-body').html();
            var coluna = card.closest('.kanban-cards').data('coluna-id');

            $('#btn-action').html(`
                <button type="button" class="btn btn-sm btn-warning" id="btn-save-edit-card">Editar</button>                
            `);
            $('#modalCardTitle').text('Editar Tarefa');
            $('#colunaDestino').val(coluna);
            $('#cardId').val(idCard);
            $('#inputTitulo').val(titulo);
            // $('#inputDescricao').val(descricao);   
            // console.log(descricao);
            descricao_tarefa.root.innerHTML = descricao === undefined ? '' : descricao;
            $('#modalCard').modal('show');
        });

        //edita o card
        $(document).on('click', '#btn-save-edit-card', function() {
            var cardId = $('#cardId').val();
            var titulo = $('#inputTitulo').val();
            var descricao = descricao_tarefa.root.innerHTML;
            const coluna = $('#colunaDestino').val();

            console.log(cardId, titulo, descricao);

            if (titulo.trim()) {
                var dados = {
                    id: cardId,
                    titulo: titulo,
                    descricao: descricao
                };
                salvarTarefas(dados, '{{ route('editar-cartoes') }}').done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        renderCartoes(coluna);
                        $('#modalCard').modal('hide');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message,
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Título é obrigatório.',
                    timer: 2500
                });
            }
        });

        $(document).on('click', '.btn-delete-card', function() {
            var idCard = $(this).closest('.card').data('task-id');

            Swal.fire({
                text: "Tem certeza que deseja excluir este card?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletarCartao(idCard).done(function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1000
                            });
                            $(`[data-task-id='${idCard}']`).remove();
                            $('#modalCard').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message,
                            });
                        }
                    });
                }
            });

        });

        //abre o modal editar coluna
        $(document).on('click', '.btn-modal-edit-coluna', function() {
            var card = $(this).closest('.card');
            var idCard = card.data('task-id');
            var nomeColuna = card.find('.card-title-coluna').text();
            const color = card.find('.card-header').css('background-color');
            const colunaId = $(this).data('id');

            $('#inputNomeColuna').val(nomeColuna);
            $('#colunaId').val(colunaId);
            $('#modalColunaTitle').text('Editar Coluna');
            $('#modalColuna').modal('show');
            $('#inputCorColuna').val(rgbToHex(color));

            $('#btn-action-coluna').html(`
                <button type="button" class="btn btn-sm btn-primary" id="btn-edit-coluna">Editar</button>                
            `);
        });

        //abre o modal criar coluna
        $(document).on('click', '.btn-modal-add-coluna', function() {

            $('#inputNomeColuna').val('');
            $('#inputCorColuna').val('');
            $('#modalColunaTitle').text('Adicionar Coluna');
            $('#btn-action-coluna').html(`
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-coluna">Adicionar</button>                
            `);
            $('#modalColuna').modal('show');

        });

        //Editar Coluna
        $(document).on('click', '#btn-edit-coluna', function() {
            var colunaId = $('#colunaId').val();
            var nomeColuna = $('#inputNomeColuna').val();
            var corColuna = $('#inputCorColuna').val().replace('#', '');

            var dados = {
                id: colunaId,
                nome: nomeColuna,
                color: corColuna
            };

            CriarEditarColuna(dados, '{{ route('editar-coluna') }}');
        });

        //Criar Coluna
        $(document).on('click', '#btn-add-coluna', function() {
            var colunaId = $('#colunaId').val();
            var nomeColuna = $('#inputNomeColuna').val();
            var corColuna = $('#inputCorColuna').val().replace('#', '');
            const idProjeto = '{{ $projeto->id }}';

            var dados = {
                id: colunaId,
                nome: nomeColuna,
                color: corColuna,
                projeto_id: idProjeto
            };

            CriarEditarColuna(dados, '{{ route('add-coluna-card') }}');
        });

        $(document).on('click', '.btn-arquivar-coluna', function() {
            var colunaId = $(this).data('id');

            Swal.fire({
                text: "Tem certeza que deseja arquivar esta coluna?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, arquivar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var dados = {
                        id: colunaId,
                        arquivar: true
                    };
                    arquivarColuna(dados);
                }
            });

        });

        function rgbToHex(rgb) {
            const result = rgb.match(/\d+/g);
            return result ? '#' + result.map(x => {
                const hex = parseInt(x).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('') : '#000000';
        }

        function initColunas(st_colunas = 'P') {
            const colunasTarefas = $('#tarefasContainer');
            const idProjeto = '{{ $projeto->id }}';
            colunasTarefas.html('<p>Carregando Colunas...</p>');            

            $.ajax({
                url: '{{ route('listar-colunas') }}',
                method: 'GET',
                data: {
                    st_colunas: st_colunas,
                    id_projeto: idProjeto
                },
                success: function(colunas) {
                    renderColunas(colunas);
                },
                error: function() {
                    console.error('Erro ao carregar as tarefas.');
                }
            });
        }

        //remove acentos e coloca em minusculo
        function renderColunas(colunas) {
            let html = '';
            colunas.forEach(function(colunas) {
                html += `
                        <div class="col-md-2 col-12 d-flex">
                            <div class="card card-secondary kanban-coluna flex-fill">
                                <div class="card-header card-header-coluna d-flex align-items-center" style="background-color: #${colunas.color};">
                                    <h6 class="card-title card-title-coluna mb-0" style="font-size: 14px;">${colunas.nome}</h6>
                                    <div class="card-tools d-flex ml-auto column-actions">
                                        <button class="btn btn-tool btn-add-card" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}" title="Adicionar Tarefa">
                                            <i class="fas fa-plus"></i>
                                        </button>  
                                         <!-- Adicionando tooltip e ícone de paleta -->
                                        <button class="btn btn-tool btn-modal-edit-coluna" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}" title="Editar Coluna">
                                            <i class="fas fa-pen"></i>
                                        </button> 
                                        <button class="btn btn-tool btn-arquivar-coluna" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}" title="Arquivar Coluna">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </div>                                
                                </div>
                                <div class="card-body kanban-cards" id="coluna_${colunas.id}" data-coluna-id="${colunas.id}">
                                    <!-- Cards serão carregados aqui -->
                                </div>
                            </div>
                        </div>
                            `;
            });

            html += `<div class="col-md-2 col-12 d-flex">
                        <div class="kanban-coluna flex-fill">
                            <div class="card-header card-header-coluna d-flex align-items-center" style="background-color: #e2e3e5;">
                                <h3 class="card-title card-title-coluna mb-0" style="font-size: 14px;">Adicionar Coluna</h3>
                                <div class="card-tools d-flex ml-auto">
                                    <button class="btn btn-tool btn-modal-add-coluna" title="Adicionar Coluna">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;

            $('#tarefasContainer').html(html);

            colunas.forEach(function(coluna) {
                renderCartoes(coluna.id);
            });
        }

        // gera/atualiza os cards dinamicos
        function renderCartoes(colunaId) {
            // Limpa os cartões existentes antes de carregar os novos
            $(`#coluna_${colunaId}`).empty();
            $.ajax({
                url: '{{ route('listar-cartoes') }}',
                method: 'GET',
                dataType: 'json',
                data: {
                    id_coluna: colunaId
                },
                success: function(cartoes) {
                    let cardHTML = '';
                    if (cartoes.length === 0) {
                        cardHTML = '<p class="text-muted text-center small">Nenhum cartão.</p>';
                    } else {
                        cartoes.forEach(function(card) {
                            //cria um card novo
                            var cardHTML = `
                                <div class="card card-info card-outline" data-task-id="${card.id}" data-posicao="${card.posicao}">
                                    <div class="card-header card-header-coluna d-flex align-items-center">
                                        <h6 class="card-title text-muted" style='font-size: 0.9rem'>${card.titulo}</h6>
                                            <div class="card-tools d-flex ml-auto column-actions">
                                                <button type="button" class="btn btn-tool btn-edit-card"><i class="fas fa-pen"></i></button>
                                                <button type="button" class="btn btn-tool btn-delete-card"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </div>

                                     ${card.descricao ? `<div class="card-body" style='font-size: 0.8rem'>${card.descricao}</div>` : ''}
                                    
                                </div>
                                `;
                            $(`#coluna_${card.coluna_id}`).append(cardHTML);
                        });
                    }
                    inicializarSortableCards();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao carregar os cartões.',
                        text: 'Por favor atualize a tela ou tente novamente mais tarde.',
                    });
                }
            });
        }

        //salvar no banco
        function salvarTarefas(dados, route) {
            return $.ajax({
                url: route,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dados: dados
                }
            });
        }

        function deletarCartao(idCard) {
            return $.ajax({
                url: '{{ route('deletar-cartao') }}',
                method: 'GET',
                contentType: 'application/json',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_card: idCard
                }
            });
        }

        //função para arrastar e soltar os cards
        function inicializarSortableCards() {
            $('.kanban-cards').sortable({
                connectWith: '.kanban-cards',
                handle: '.card-header',
                forcePlaceholderSize: true,
                placeholder: 'ui-state-highlight',
                update: function(event, ui) {
                    // coluna de destino
                    const colunaDestino = $(this).data('coluna-id');
                    const cards = $(this).children('.card');
                    const atualizacoes = [];

                    cards.each(function(index) {
                        const id = $(this).data('task-id');
                        atualizacoes.push({
                            id: id,
                            coluna: colunaDestino,
                            posicao: index
                        });
                    });

                    // Envia as atualizações para o servidor
                    $.ajax({
                        url: '{{ route('reordenar-cartao') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            tarefas: atualizacoes
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro ao atualizar tarefas.',
                                    text: response.message,
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao atualizar tarefas.',
                            });
                        }
                    });
                }
            }).disableSelection();
        }

        // function inicializarSortableColunas() {
        //     $('#tarefasContainer').sortable({
        //         handle: '.card-header',
        //     }).disableSelection();
        // }

        function CriarEditarColuna(dados, route) {
            $.ajax({
                url: route,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dados: dados
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        initColunas();
                        $('#modalColuna').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message,
                        });
                    }
                }
            });
        }

        function arquivarColuna(dados) {
            $.ajax({
                url: '{{ route('arquivar-coluna') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dados: dados
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        initColunas();
                        $('#modalColuna').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message,
                        });
                    }
                }
            });
        }
    </script>
@stop
