@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="projeto-nome" data-projeto-id="{{ $projeto->id }}">{{ $projeto->nome }}</h3>
                    <div class="card-tools">
                        {{-- Avatares de quem está editando o quadro agora (presença em tempo real) --}}
                        <span id="presenca-editores" class="d-inline-flex align-items-center mr-2"></span>
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
                    style="background-color: #f4f6f9;">
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
        /* Colunas lado a lado com scroll horizontal, sem quebrar para uma nova linha */
        #tarefasContainer {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            margin-left: 0;
            margin-right: 0;
            padding-bottom: 12px;
        }

        /* Largura fixa da coluna (substitui o col-md-2, que quebrava linha com muitas colunas) */
        .kanban-coluna-item {
            flex: 0 0 260px;
            max-width: 260px;
            padding: 0 8px;
        }

        /* Lista de cards da coluna: altura máxima com scroll vertical próprio,
           para não esticar a página inteira quando tem muito cartão */
        .kanban-cards {
            max-height: calc(100vh - 260px);
            overflow-y: auto;
        }

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

        /* Avatares de presença (quem está editando o quadro agora) */
        .avatar-presenca {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            margin-left: -6px;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
            cursor: default;
            text-transform: uppercase;
        }

        .avatar-presenca:first-child {
            margin-left: 0;
        }

        /* Cartões: altura fixa e corpo com texto truncado (...) quando muito longo */
        .card-cartao {
            height: 160px;
            display: flex;
            flex-direction: column;
        }

        /* Header do card é a "alça" de arraste (handle do sortable) */
        .card-cartao .card-header {
            cursor: move;
        }

        .card-cartao .card-title {
            flex: 1 1 auto;
            min-width: 0;
            margin-bottom: 0;
        }

        .card-cartao .card-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        /* Mantém o botão "Ações" visível enquanto o menu estiver aberto (card ou coluna) */
        .column-actions.show {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .dropdown-menu-acoes {
            min-width: 10rem;
        }
    </style>
@stop

@section('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>
    <script>
        /* ---------------------------------------------------------------
            SINCRONIZAÇÃO EM TEMPO REAL (Firestore)
            Quando alguém altera o quadro, os outros que estão vendo o mesmo
            projeto recebem um "sinal" e recarregam as colunas/cards.
        --------------------------------------------------------------- */
        const RT_PROJETO_ID = '{{ $projeto->id }}';
        const RT_MEU_USER_ID = '{{ auth()->user()->id }}';
        const RT_MEU_NOME = @json(auth()->user()->name);
        const RT_FIREBASE_TOKEN = @json($firebaseToken);

        let rtDb = null;
        let arrastandoCard = false;
        let atualizacaoPendente = false;

        (function inicializarSincronizacao() {
            if (typeof firebase === 'undefined') return;

            // compartilha o app default (o mesmo usado pelo messaging)
            if (!firebase.apps.length) {
                firebase.initializeApp({
                    apiKey: "{{ env('FMC_API_KEY') }}",
                    authDomain: "{{ env('FCM_AUTH_DOMAIN') }}",
                    projectId: "{{ env('FCM_PROJECT_ID') }}",
                    storageBucket: "{{ env('FCM_STORAGE_BUCKET') }}",
                    messagingSenderId: "{{ env('FCM_MESSAGING_SENDER_ID') }}",
                    appId: "{{ env('FCM_APP_ID') }}",
                    measurementId: "{{ env('FCM_MEASUREMENT_ID') }}"
                });
            }

            firebase.auth().signInWithCustomToken(RT_FIREBASE_TOKEN)
                .then(function() {
                    rtDb = firebase.firestore();
                    escutarAlteracoesQuadro();
                    iniciarPresenca();
                })
                .catch(function(err) {
                    console.error('Falha ao autenticar no Firebase (sync tempo real):', err);
                });
        })();

        function escutarAlteracoesQuadro() {
            rtDb.collection('quadros').doc(RT_PROJETO_ID).onSnapshot(function(doc) {
                if (!doc.exists) return;

                const data = doc.data();
                if (!data) return;

                // ignora o sinal disparado pelo próprio usuário
                if (String(data.atualizadoPor) === String(RT_MEU_USER_ID)) return;

                // adia se estiver arrastando um card ou com algum modal aberto
                if (quadroOcupado()) {
                    atualizacaoPendente = true;
                    return;
                }

                initColunas();
            }, function(err) {
                console.error('Erro no listener do Firestore:', err);
            });
        }

        // grava o sinal de que este usuário alterou o quadro
        function sinalizarAlteracao() {
            if (!rtDb) return;

            rtDb.collection('quadros').doc(RT_PROJETO_ID).set({
                atualizadoPor: RT_MEU_USER_ID,
                atualizadoEm: firebase.firestore.FieldValue.serverTimestamp()
            }).catch(function(err) {
                console.error('Erro ao sinalizar alteração:', err);
            });
        }

        function quadroOcupado() {
            return arrastandoCard || $('.modal.show').length > 0;
        }

        /* ---------------------------------------------------------------
            PRESENÇA — mostra quem mais está com o quadro aberto
            (heartbeat no Firestore + listener; o Firestore não tem
            onDisconnect, então usamos "último ping" para filtrar offline)
        --------------------------------------------------------------- */
        const RT_PRESENCA_INTERVALO = 20000; // pinga a cada 20s
        const RT_PRESENCA_TIMEOUT = 60000; // sem ping há 60s = considerado offline

        function iniciarPresenca() {
            const meuDoc = rtDb.collection('quadros').doc(RT_PROJETO_ID)
                .collection('presenca').doc(RT_MEU_USER_ID);

            function pingPresenca() {
                meuDoc.set({
                    nome: RT_MEU_NOME,
                    ultimoPing: firebase.firestore.FieldValue.serverTimestamp()
                }, {
                    merge: true
                }).catch(function(err) {
                    console.error('Erro ao registrar presença:', err);
                });
            }

            pingPresenca();
            setInterval(pingPresenca, RT_PRESENCA_INTERVALO);

            // escuta todos os presentes no quadro
            rtDb.collection('quadros').doc(RT_PROJETO_ID).collection('presenca')
                .onSnapshot(function(snap) {
                    const agora = Date.now();
                    const presentes = [];

                    snap.forEach(function(doc) {
                        if (doc.id === String(RT_MEU_USER_ID)) return; // ignora eu mesmo

                        const d = doc.data();
                        if (!d || !d.nome) return;

                        const t = (d.ultimoPing && d.ultimoPing.toMillis) ? d.ultimoPing.toMillis() : 0;
                        if (t && (agora - t) > RT_PRESENCA_TIMEOUT) return; // offline

                        presentes.push({
                            id: doc.id,
                            nome: d.nome
                        });
                    });

                    renderPresenca(presentes);
                }, function(err) {
                    console.error('Erro no listener de presença:', err);
                });

            // remove minha presença ao sair (best-effort)
            window.addEventListener('beforeunload', function() {
                meuDoc.delete();
            });
        }

        function renderPresenca(presentes) {
            const container = $('#presenca-editores');

            if (!presentes.length) {
                container.empty();
                return;
            }

            let html = '';
            presentes.forEach(function(p) {
                html += `<span class="avatar-presenca" style="background-color: ${corPresenca(p.id)};" title="${escapePresenca(p.nome)} (editando agora)">${iniciaisPresenca(p.nome)}</span>`;
            });

            container.html(html);
        }

        function iniciaisPresenca(nome) {
            const partes = String(nome).trim().split(/\s+/);
            const a = partes[0] ? partes[0][0] : '';
            const b = partes.length > 1 ? partes[partes.length - 1][0] : '';
            return (a + b) || '?';
        }

        function corPresenca(chave) {
            const cores = ['#007bff', '#28a745', '#dc3545', '#fd7e14', '#6f42c1', '#17a2b8', '#e83e8c',
                '#20c997'
            ];
            let hash = 0;
            const s = String(chave);
            for (let i = 0; i < s.length; i++) hash = (hash * 31 + s.charCodeAt(i)) >>> 0;
            return cores[hash % cores.length];
        }

        function escapePresenca(texto) {
            return String(texto ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        // aplica uma atualização remota que ficou pendente (após soltar o card / fechar modal)
        function aplicarAtualizacaoPendente() {
            if (atualizacaoPendente && !quadroOcupado()) {
                atualizacaoPendente = false;
                initColunas();
            }
        }

        // reaplica a atualização pendente quando qualquer modal é fechado
        $(document).on('hidden.bs.modal', '.modal', function() {
            aplicarAtualizacaoPendente();
        });

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
                        sinalizarAlteracao();

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
        function abrirModalEditarCard(card) {
            var idCard = card.data('task-id');
            var dadosCard = cartoesCache[idCard] || {};
            var titulo = dadosCard.titulo ?? card.find('.card-title').text();
            var descricao = dadosCard.descricao;
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
            descricao_tarefa.root.innerHTML = descricao ?? '';
            $('#modalCard').modal('show');
        }

        $(document).on('click', '.btn-edit-card', function() {
            abrirModalEditarCard($(this).closest('.card'));
        });

        // clicar em qualquer parte do card abre a edição (exceto no menu "Ações" ou logo após arrastar)
        $(document).on('click', '.card-cartao', function(e) {
            if (arrastandoCard) return;
            if ($(e.target).closest('.dropdown').length) return;

            abrirModalEditarCard($(this));
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
                        sinalizarAlteracao();

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
                            sinalizarAlteracao();
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

            $('#colunaId').val('');
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

        $(document).on('click', '#projeto-nome', function() {
            const projetoId = $(this).data('projeto-id');
            const nomeAtual = $(this).text().trim();
            let $this = $(this);

            if ($this.find('input').length) return; // Já está em modo de edição


            $this.html('<input class="form-control" type="text" value="' + nomeAtual +
                '" id="input-nome-projeto" style="width: 400px;" />');
            let $input = $('#input-nome-projeto');
            $input.focus();

            $input.on('blur keypress', function(e) {
                if (e.type === 'blur' || (e.which === 13 && !e.shiftKey)) {
                    let novoNome = $input.val().trim();

                    // Evita enviar se não mudou
                    if (novoNome === nomeAtual) {
                        $this.text(nomeAtual);
                        return;
                    }
                    $.ajax({
                        url: '{{ route('editar-titulo-projeto') }}',
                        method: 'POST',
                        data: {
                            id: projetoId,
                            nome: novoNome,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            $this.text(novoNome);
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: 'Nome do projeto atualizado.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            $this.text(nomeAtual);
                        }
                    });
                }
            });

        });

        function escapeHtml(texto) {
            return $('<div>').text(texto ?? '').html();
        }

        // cache dos cards carregados (id -> {titulo, descricao, ...}) — guarda o HTML
        // original da descrição para a edição, já que no card só mostramos uma prévia
        // em texto puro (o -webkit-line-clamp não corta de forma confiável quando a
        // descrição tem tags de bloco como <p>, vindas do editor Quill)
        let cartoesCache = {};

        // extrai o texto puro de uma descrição em HTML, para exibir como prévia no card
        function textoSemHtml(html) {
            // adiciona um espaço nas quebras de bloco/linha antes de remover as tags,
            // senão parágrafos e <br> ficam colados (ex: "541756Itens" em vez de "541756 Itens")
            var comEspacos = String(html ?? '')
                .replace(/<\/(p|div|li|h[1-6]|blockquote|tr)>/gi, '$& ')
                .replace(/<br\s*\/?>/gi, ' ');

            return $('<div>').html(comEspacos).text().replace(/\s+/g, ' ').trim();
        }

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
                        <div class="kanban-coluna-item d-flex">
                            <div class="card card-secondary kanban-coluna flex-fill">
                                <div class="card-header card-header-coluna d-flex align-items-center" style="background-color: #${colunas.color};">
                                    <h6 class="card-title card-title-coluna mb-0" style="font-size: 14px;">${escapeHtml(colunas.nome)}</h6>
                                    <div class="dropdown card-tools ml-auto column-actions">
                                        <button type="button" class="btn btn-tool dropdown-toggle btn-acoes-coluna" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações">
                                            Ações
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-acoes">
                                            <button type="button" class="dropdown-item btn-add-card" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}"><i class="fas fa-plus mr-2"></i>Adicionar Tarefa</button>
                                            <button type="button" class="dropdown-item btn-modal-edit-coluna" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}"><i class="fas fa-pen mr-2"></i>Editar Coluna</button>
                                            <button type="button" class="dropdown-item btn-arquivar-coluna" data-coluna-id="coluna_${colunas.id}" data-id="${colunas.id}"><i class="fas fa-archive mr-2"></i>Arquivar Coluna</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body kanban-cards" id="coluna_${colunas.id}" data-coluna-id="${colunas.id}">
                                    <!-- Cards serão carregados aqui -->
                                </div>
                            </div>
                        </div>
                            `;
            });

            html += `<div class="kanban-coluna-item d-flex">
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
                    if (cartoes.length === 0) {
                        $(`#coluna_${colunaId}`).html(
                            '<p class="text-muted text-center small sem-cartao">Nenhum cartão.</p>');
                    } else {
                        cartoes.forEach(function(card) {
                            // guarda os dados completos (HTML original) para a edição
                            cartoesCache[card.id] = card;

                            //cria um card novo
                            var cardHTML = `
                                <div class="card card-info card-outline card-cartao" data-task-id="${card.id}" data-posicao="${card.posicao}">
                                    <div class="card-header card-header-coluna d-flex align-items-center">
                                        <h6 class="card-title text-muted text-truncate" style='font-size: 0.9rem'>${escapeHtml(card.titulo)}</h6>
                                        <div class="dropdown card-tools ml-auto column-actions">
                                            <button type="button" class="btn btn-tool dropdown-toggle btn-acoes-card" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações">
                                                Ações
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-acoes">
                                                <button type="button" class="dropdown-item btn-edit-card"><i class="fas fa-pen mr-2"></i>Editar</button>
                                                <button type="button" class="dropdown-item btn-delete-card"><i class="fas fa-trash mr-2"></i>Excluir</button>
                                            </div>
                                        </div>
                                        </div>

                                     ${card.descricao ? `<div class="card-body" style='font-size: 0.8rem'>${escapeHtml(textoSemHtml(card.descricao))}</div>` : ''}

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
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_card: idCard
                }
            });
        }

        //função para arrastar e soltar os cards
        // remove o "Nenhum cartão" de colunas com card e recoloca nas que ficaram vazias
        function normalizarColunasVazias() {
            $('.kanban-cards').each(function() {
                const $col = $(this);

                if ($col.children('.card').length > 0) {
                    $col.children('.sem-cartao').remove();
                } else if ($col.children('.sem-cartao').length === 0) {
                    $col.html('<p class="text-muted text-center small sem-cartao">Nenhum cartão.</p>');
                }
            });
        }

        function inicializarSortableCards() {
            $('.kanban-cards').sortable({
                connectWith: '.kanban-cards',
                handle: '.card-header',
                forcePlaceholderSize: true,
                placeholder: 'ui-state-highlight',
                start: function() {
                    arrastandoCard = true;
                },
                stop: function() {
                    // corrige o placeholder "Nenhum cartão" das colunas afetadas
                    normalizarColunasVazias();

                    // libera após soltar e aplica atualização remota pendente
                    setTimeout(function() {
                        arrastandoCard = false;
                        aplicarAtualizacaoPendente();
                    }, 100);
                },
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
                                sinalizarAlteracao();
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
                        sinalizarAlteracao();
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
                        sinalizarAlteracao();
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
