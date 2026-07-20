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

        {{-- Projetos compartilhados comigo (exibido somente quando houver) --}}
        <div class="row" id="secao-compartilhados" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-friends mr-1"></i> Compartilhados comigo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row" id="projetos-compartilhados-container">
                            {{-- Projetos compartilhados serão carregados via JS --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="modal-adicionar-projeto-tarefa">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="modalProjetoTitle">Adicionar</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="projetoId">
                        <div class="row">
                            <div class="form-group col-md-10">
                                <label for="nome">Titulo</label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Titulo"
                                    required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="corProjeto">Cor</label>
                                <input type="color" class="form-control" id="corProjeto">
                            </div>
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

        {{-- Modal de compartilhamento --}}
        <div class="modal" id="modal-compartilhar-projeto">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Compartilhar Projeto</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="compartilharProjetoId">
                        <div class="form-group">
                            <label for="usuariosCompartilhar">Selecione os usuários</label>
                            <select class="form-control select2" id="usuariosCompartilhar" multiple
                                style="width: 100%;">
                                @foreach ($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ strtoupper($u->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <label>Usuários com acesso</label>
                        <ul class="list-group" id="lista-usuarios-acesso">
                            {{-- Preenchido via JS --}}
                        </ul>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-sm btn-primary btn-confirmar-compartilhar">Compartilhar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        .card-projeto {
            min-height: 150px;
            display: flex;
            flex-direction: column;
        }

        .card-projeto:hover {
            cursor: pointer;
            background-color: #f4f6f9;
        }

        .card-projeto .card-body {
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
        }

        .card-projeto .btn-remover-projeto,
        .card-projeto .btn-editar-projeto,
        .card-projeto .btn-compartilhar-projeto {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .card-projeto:hover .btn-remover-projeto,
        .card-projeto:hover .btn-editar-projeto,
        .card-projeto:hover .btn-compartilhar-projeto {
            opacity: 1;
            visibility: visible;
        }

        .card-projeto-novo {
            min-height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #ced4da;
            border-radius: 0.25rem;
            color: #6c757d;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .card-projeto-novo:hover {
            background-color: #f4f6f9;
            color: #495057;
            border-color: #adb5bd;
        }

        .card-projeto-novo i {
            font-size: 1.75rem;
            margin-bottom: 6px;
        }

        .card-header-coluna {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
        }

        /* Handle de arraste no cabeçalho do card */
        .card-projeto .card-header {
            cursor: move;
        }

        /* Projetos compartilhados não são arrastáveis */
        .card-compartilhado .card-header {
            cursor: pointer;
        }

        /* Placeholder exibido durante o arraste dos projetos */
        .card-projeto-placeholder {
            min-height: 150px;
            border: 2px dashed #adb5bd;
            border-radius: 0.25rem;
            background-color: #f4f6f9;
        }

        /* Em telas pequenas, mantém os botões de ação sempre visíveis (sem hover) */
        @media (max-width: 768px) {

            .card-projeto .btn-remover-projeto,
            .card-projeto .btn-editar-projeto,
            .card-projeto .btn-compartilhar-projeto {
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
    </style>
@stop
@section('js')
    <script src="{{ asset('vendor/adminlte/dist/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.ui.touch-punch.min.js') }}"></script>
    <script>
        let projetoArrastado = false;

        initProjetosTarefas();
        initProjetosCompartilhados();

        $('#usuariosCompartilhar').select2({
            dropdownParent: $('#modal-compartilhar-projeto'),
            width: '100%',
            placeholder: 'Selecione os usuários'
        });

        $(document).on('click', '.card-projeto', function() {
            // ignora o clique disparado logo após arrastar o card
            if (projetoArrastado) return;

            var projetoId = $(this).data('id');

            const route = '{{ route('tarefas-quadro', ':id') }}';
            window.location.href = route.replace(':id', projetoId);
        });

        $(document).on('click', '.btn-add-projeto', function() {
            $('#projetoId').val('');
            $('#nome').val('');
            $('#descricao').val('');
            $('#corProjeto').val('#6c757d');
            $('#modalProjetoTitle').text('Adicionar');
            $('#modal-adicionar-projeto-tarefa').modal('show');
        });

        $(document).on('click', '.btn-editar-projeto', function(e) {
            e.stopPropagation();
            var projetoId = $(this).data('id');
            var projeto = projetosCarregados.find(p => p.encrypted_id === projetoId);

            if (!projeto) return;

            $('#projetoId').val(projeto.encrypted_id);
            $('#nome').val(projeto.nome);
            $('#descricao').val(projeto.descricao);
            $('#corProjeto').val('#' + (projeto.color || '6c757d'));
            $('#modalProjetoTitle').text('Editar');
            $('#modal-adicionar-projeto-tarefa').modal('show');
        });

        $(document).on('click', '.btn-salvar-projeto-tarefa', function() {
            var projetoId = $('#projetoId').val();
            var nome = $('#nome').val();
            var descricao = $('#descricao').val();
            var color = $('#corProjeto').val().replace('#', '');

            if (!nome.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Título é obrigatório.',
                    timer: 2500
                });
                return;
            }

            var dados = {
                nome: nome,
                descricao: descricao,
                color: color,
                _token: '{{ csrf_token() }}'
            };

            var route = '{{ route('salvar-projeto-tarefa') }}';

            if (projetoId) {
                dados.id = projetoId;
                route = '{{ route('editar-projeto') }}';
            }

            $.ajax({
                url: route,
                method: 'POST',
                data: dados,
                success: function(response) {
                    $('#modal-adicionar-projeto-tarefa').modal('hide');
                    initProjetosTarefas();
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

        $(document).on('click', '.btn-remover-projeto', function(e) {
            e.stopPropagation();
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
                    removerProjeto(projetoId, false);
                }
            });
        });

        function removerProjeto(projetoId, forcar) {
            $.ajax({
                url: '{{ route('remover-projeto') }}',
                method: 'POST',
                data: {
                    id: projetoId,
                    forcar: forcar,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.confirmar_exclusao) {
                        Swal.fire({
                            title: 'Atenção!',
                            text: response.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sim, excluir tudo!',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                removerProjeto(projetoId, true);
                            }
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        text: response.success,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    initProjetosTarefas();
                },
                error: function(xhr) {
                    let mensagem = 'Ocorreu um erro ao remover o projeto de tarefa.';

                    if (xhr.responseJSON?.errors) {
                        mensagem = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.error) {
                        mensagem = xhr.responseJSON.error;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        html: mensagem,
                    });
                }
            });
        }

        let projetosCarregados = [];

        function escapeHtml(texto) {
            return String(texto ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function initProjetosTarefas() {
            $('#projetos-container').html(
                '<div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-lg"></i><br>Carregando quadros...</div>'
            );

            $.ajax({
                type: "GET",
                url: "{{ route('listar-projetos') }}",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    projetosCarregados = response;

                    let html = '';
                    response.forEach(projeto => {
                        const cor = projeto.color || '6c757d';
                        html += `
                        <div class="col-md-3 projeto-item">
                            <div class="card card-outline card-projeto" data-id="${ projeto.encrypted_id }" style="border-top: 3px solid #${cor};">
                                <div class="card-header card-header-coluna">
                                    <h3 class="card-title">${ escapeHtml(projeto.nome) }</h3>
                                    <div class="card-tools d-flex ml-auto">
                                        <button type="button" class="btn btn-secondary btn-xs btn-editar-projeto mr-1" data-id="${ projeto.encrypted_id }">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn-xs btn-compartilhar-projeto mr-1" data-id="${ projeto.encrypted_id }">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs btn-remover-projeto mr-1" data-id="${ projeto.encrypted_id }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    ${ escapeHtml(projeto.descricao) }
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    `;
                    });

                    html += `
                        <div class="col-md-3">
                            <div class="card-projeto-novo btn-add-projeto" data-toggle="modal" data-target="#modal-adicionar-projeto-tarefa">
                                <i class="fas fa-plus"></i>
                                <span>Novo quadro</span>
                            </div>
                        </div>
                    `;

                    $('#projetos-container').html(html);
                    inicializarSortableProjetos();
                }
            });
        }

        // habilita arrastar e soltar para reordenar os projetos
        function inicializarSortableProjetos() {
            $('#projetos-container').sortable({
                items: '> .projeto-item',
                handle: '.card-header',
                cancel: '.card-tools, button',
                placeholder: 'col-md-3 card-projeto-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                start: function() {
                    projetoArrastado = true;
                },
                stop: function() {
                    // pequena espera para o clique pós-arraste não navegar
                    setTimeout(function() {
                        projetoArrastado = false;
                    }, 100);
                },
                update: function() {
                    salvarOrdemProjetos();
                }
            });
        }

        function salvarOrdemProjetos() {
            const ids = $('#projetos-container .card-projeto').map(function() {
                return $(this).data('id');
            }).get();

            $.ajax({
                url: '{{ route('reordenar-projetos') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    projetos: ids
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao reordenar os quadros.',
                        text: 'Por favor atualize a tela e tente novamente.',
                    });
                }
            });
        }

        /* ---------------------------------------------------------------
            COMPARTILHAMENTO
        --------------------------------------------------------------- */

        // abre o modal de compartilhamento (somente dono vê o botão)
        $(document).on('click', '.btn-compartilhar-projeto', function(e) {
            e.stopPropagation();
            var projetoId = $(this).data('id');

            $('#compartilharProjetoId').val(projetoId);
            $('#usuariosCompartilhar').val(null).trigger('change');
            carregarUsuariosAcesso(projetoId);

            $('#modal-compartilhar-projeto').modal('show');
        });

        // confirma o compartilhamento com os usuários selecionados
        $(document).on('click', '.btn-confirmar-compartilhar', function() {
            var projetoId = $('#compartilharProjetoId').val();
            var usuarios = $('#usuariosCompartilhar').val();

            if (!usuarios || usuarios.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione ao menos um usuário.',
                    timer: 2500
                });
                return;
            }

            $.ajax({
                url: '{{ route('compartilhar-projeto') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: projetoId,
                    usuarios: usuarios
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: response.success,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#usuariosCompartilhar').val(null).trigger('change');
                    carregarUsuariosAcesso(projetoId);
                },
                error: function(xhr) {
                    let mensagem = 'Ocorreu um erro ao compartilhar o projeto.';
                    if (xhr.responseJSON?.error) mensagem = xhr.responseJSON.error;

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: mensagem,
                    });
                }
            });
        });

        // revoga o acesso de um usuário
        $(document).on('click', '.btn-revogar-acesso', function() {
            var projetoId = $('#compartilharProjetoId').val();
            var idUser = $(this).data('id-user');

            $.ajax({
                url: '{{ route('revogar-compartilhamento') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: projetoId,
                    id_user: idUser
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.success,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    carregarUsuariosAcesso(projetoId);
                },
                error: function(xhr) {
                    let mensagem = 'Ocorreu um erro ao remover o compartilhamento.';
                    if (xhr.responseJSON?.error) mensagem = xhr.responseJSON.error;

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: mensagem,
                    });
                }
            });
        });

        // carrega a lista de usuários que já têm acesso ao projeto
        function carregarUsuariosAcesso(projetoId) {
            $('#lista-usuarios-acesso').html(
                '<li class="list-group-item text-muted small">Carregando...</li>');

            $.ajax({
                url: '{{ route('listar-compartilhamentos') }}',
                method: 'GET',
                data: {
                    id: projetoId
                },
                success: function(usuarios) {
                    if (!usuarios.length) {
                        $('#lista-usuarios-acesso').html(
                            '<li class="list-group-item text-muted small">Ninguém tem acesso ainda.</li>');
                        return;
                    }

                    let html = '';
                    usuarios.forEach(function(u) {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center py-1">
                                ${ escapeHtml(u.name) }
                                <button type="button" class="btn btn-danger btn-xs btn-revogar-acesso" data-id-user="${ u.id }">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>`;
                    });

                    $('#lista-usuarios-acesso').html(html);
                },
                error: function() {
                    $('#lista-usuarios-acesso').html(
                        '<li class="list-group-item text-danger small">Erro ao carregar os acessos.</li>');
                }
            });
        }

        // carrega os projetos compartilhados com o usuário logado
        function initProjetosCompartilhados() {
            $.ajax({
                type: 'GET',
                url: '{{ route('listar-projetos-compartilhados') }}',
                success: function(response) {
                    if (!response.length) {
                        $('#secao-compartilhados').hide();
                        $('#projetos-compartilhados-container').html('');
                        return;
                    }

                    let html = '';
                    response.forEach(projeto => {
                        const cor = projeto.color || '6c757d';
                        html += `
                        <div class="col-md-3">
                            <div class="card card-outline card-projeto card-compartilhado" data-id="${ projeto.encrypted_id }" style="border-top: 3px solid #${cor};">
                                <div class="card-header card-header-coluna">
                                    <h3 class="card-title">${ escapeHtml(projeto.nome) }</h3>
                                </div>
                                <div class="card-body">
                                    ${ escapeHtml(projeto.descricao) }
                                </div>
                                <div class="card-footer text-muted small py-1">
                                    <i class="fas fa-user mr-1"></i> ${ escapeHtml(projeto.proprietario) }
                                </div>
                            </div>
                        </div>`;
                    });

                    $('#projetos-compartilhados-container').html(html);
                    $('#secao-compartilhados').show();
                }
            });
        }
    </script>
@stop
