@extends('layouts.master')

@section('title', 'Assistente IA')

@section('content_top_nav_right')

    <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block" style="display: none;">
            <div id="searchBox">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Pesquisar..."
                        aria-label="Pesquisar" id="customSearch">
                    <div class="input-group-append">

                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </li>
@endsection

@section('content')
    <section class="content-fluid" id="conteudo-ia">
        <div id="resposta-info"></div>
        <div class="row d-none" id="row-resposta">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title card-header-tabela"></h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-default" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-2 pb-0 pr-3">
                        <div class="row">
                            <div class="col-md-8 pr-3" id="resposta-tabela" style="border-right: 1px solid #dee2e6;">
                            </div>

                            <div class="col-md-4">
                                <p id="resposta-progress-title-vendedor" class="text-center">
                                </p>
                                <div id="resposta-progress-vendedor" style="max-height: 400px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"></h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-default" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-2 pb-0 pr-3">
                        <div class="row">
                            <div class="col-md-8 pr-3" style="border-right: 1px solid #dee2e6;">

                            </div>

                            <div class="col-md-4">
                                <p id="resposta-progress-title-cliente" class="text-center">
                                    <strong>Coletas por cliente</strong>
                                </p>
                                <div id="resposta-progress-cliente" style="max-height: 400px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="row d-none" id="row-resumo-ia">
            <div class="card">
                <div class="card-header">Resumo IA</div>
                <div class="card-body">
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center align-items-center" style="height: 70vh;" id="bloco-central">
            <div class="col-md-6">
                <h3 class="mb-2 text-center title-ia">
                    E ai {{ Auth()->user()->name }}, vamos começar?
                </h3>
                <div class="input-group p-2" id="input-area">
                    <input type="text" class="form-control form-control-lg rounded-pill" id="pergunta"
                        placeholder="Pergunte algo para a IA, por exemplo: Quantos pneus foram coletados em 25 de fevereiro de 2026?"
                        value="Quantos pneus foram coletados em 25 de fevereiro de 2026?"
                        onkeypress="if(event.key === 'Enter') perguntarIA()">
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        .modo-chat #bloco-central {
            height: auto !important;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 5px 0;
            background-color: #33393f75;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .modo-chat #input-area {
            max-width: 800px;
            margin: 0 auto;
            transition: all .3s ease-in-out;
        }

        .modo-chat .title-ia {
            display: none;
        }

        .modo-chat #row-resposta {
            padding-bottom: 66px;
            display: block !important;
        }

        .title-ia {
            margin-bottom: 3.5rem !important;
        }

        .form-control-lg {
            height: calc(3rem + 2px) !important;
            font-size: 1rem !important;
        }

        .resumo-ia {
            font-size: 0.9rem;

        }

        /* quando sidebar aberta */
        body:not(.sidebar-collapse) .modo-chat #input-area {
            margin-left: 125px;
            /* ajuste fino */
        }

        /* quando sidebar fechada */
        body.sidebar-collapse .modo-chat #input-area {
            margin-left: auto;
        }

        .info-box-custom {
            border-radius: 12px;
            padding: 6px;
            transition: all 0.2s ease;
            min-height: 60px !important;
            display: flex;
            justify-content: center;
            /* centraliza horizontalmente */
            align-items: center;
            /* centraliza verticalmente */
        }

        /* Hover suave */
        .info-box-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Ícone mais proporcional */
        .info-box-custom .info-box-icon {
            font-size: 20px;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            margin: 0 10px 0 10px;
            display: flex !important;
            align-items: center !important;
            /* centraliza vertical */
            justify-content: center !important;
            /* centraliza horizontal */

        }

        /* Texto */
        .info-box-custom .info-box-text {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        /* Número principal */
        .info-box-custom .info-box-number {
            font-size: 20px;
            margin-top: -4px !important;
            font-weight: 600;
        }

        /* Percentual */
        .info-box-custom .percentual {
            font-size: 12px;
            color: #6c757d;
            margin-left: 4px;
        }

        @media (max-width: 767.98px) {
            .card-title {
                font-size: 1rem !important;
            }

            .info-box-number {
                font-size: 16px !important;
            }

            .info-box-custom .info-box-text {
                font-size: 10px !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        window.routes = {
            perguntar: '{{ route('ia-perguntar') }}',
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
        };

        let progressDataGlobal = null;
        let tabelaIA = null;

        function perguntarIA() {

            let pergunta = document.getElementById('pergunta').value;

            Swal.fire({
                title: 'Processando pergunta...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(window.routes.perguntar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pergunta
                    })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();

                    if (!data.tabela || data.tabela.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sem dados',
                            text: 'Nenhum resultado encontrado'
                        });
                        return;
                    }

                    if (data.texto) {
                        Swal.fire({
                            title: data.titulo,
                            text: data.dados,
                        });
                        return;
                    }

                    if (data.tabela) {
                        tabelaIA = inicializaTabelaIA(data.tabela.dados);
                    }

                    if (data.componentes) {

                        renderInfoBox(data.componentes);
                    }

                    if (data.progress_vendedores) {

                        progressDataGlobal = data.progress_vendedores;

                        document.getElementById('resposta-progress-title-vendedor').innerHTML =
                            `<strong> ${data.progress_vendedores.titulo}</strong>`;

                        renderProgressBarItens(data.progress_vendedores.progress);

                    }

                    if (data.resumo_ia) {
                        document.getElementById('resumo-ia').innerHTML = `                            
                            <p class="text-bold">${data.resumo_ia}</p>
                        `;
                    }

                    $('.title-ia').addClass('d-none');
                    $('.card-header-tabela').text(data.tabela.titulo);
                    $('#row-resposta').removeClass('d-none');
                    $('#conteudo-ia').addClass('modo-chat');

                }).catch(error => {
                    Swal.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao processar a pergunta. Tente novamente.',
                    });

                    console.error('Erro ao perguntar IA:', error);
                });
        }

        $('#customSearch').on('input', function() {
            tabelaIA.search(this.value).draw();

            const termo = this.value.toLowerCase();

            if (progressDataGlobal) {
                const filtrado = progressDataGlobal.progress.filter(item => {
                    return Object.values(item)
                        .join(' ')
                        .toLowerCase()
                        .includes(termo)

                });

                renderProgressBarItens(filtrado);

            }

        });

        $(document).on('click', '.filtro-vendedor', function(e) {
            e.preventDefault();
            const vendedor = $(this).data('vendedor');
            $('#customSearch').val(vendedor).trigger('input');

            $('.navbar-search-block').css('display', 'flex');
        });

        function formatarColunaTabela(col) {
            if (col == 'NM_VENDEDOR') {
                return {
                    title: 'Vendedor',
                    data: col,
                    className: 'no-wrap'
                }
            };
            if (col === 'VALOR_MEDIO') {
                return {
                    title: 'P.Médio',
                    data: col,
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                };
            }
            if (col === 'VL_TOTAL') {
                return {
                    title: 'Total',
                    data: col,
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                };
            }
            if (col === 'QTD') {
                return {
                    title: 'Qtd',
                    data: col,
                    className: 'text-center'
                };
            }
            if (col === 'DS_SERVICOPNEU') {
                return {
                    title: 'Serviço',
                    data: col,
                    className: 'no-wrap'
                };
            }
            if (col === 'NM_PESSOA') {
                return {
                    title: 'Pessoa',
                    data: col,
                    className: 'no-wrap'
                };
            }
            if (col == 'DT_EMISSAO') {
                return {
                    title: 'Emissão',
                    data: col,
                    render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                };
            }
            if (col == 'DSDESENHO') {
                return {
                    title: 'Desenho',
                    data: col,
                    visible: false,
                    className: 'no-wrap'
                };
            }
            if (col == 'IDDESENHOPNEU') {
                return {
                    title: 'ID Desenho',
                    data: col,
                    visible: false
                };
            }
            if (col == 'CD_SUBGRUPO') {
                return {
                    title: 'Subgrupo',
                    data: col,
                    visible: false
                };
            }

            return {
                title: col,
                data: col
            };
        }

        function inicializaTabelaIA(dados) {

            const html = `
                        <table id="tabelaIA" class="table table-bordered compact table-font-small">
                            <thead></thead>
                            <tbody></tbody>
                        </table>                    
                        `;

            document.getElementById('resposta-tabela').innerHTML = html;

            let colunas = Object.keys(dados[0]).map(col => {
                return formatarColunaTabela(col);
            });

            return $('#tabelaIA').DataTable({
                data: dados,
                columns: colunas,
                destroy: true,
                // pageLength: 100,
                paging: false,
                scrollX: true,
                scrollCollapse: true,
                scrollY: '300px',
                info: false,
                dom: 'lrtip',
                language: {
                    url: window.routes.languageDatatables
                },
                // layout: {
                //     topStart: {
                //         buttons: [{
                //                 extend: "excelHtml5",
                //                 title: `${dados.titulo}`,
                //             },
                //             {
                //                 extend: "print",
                //                 title: `${dados.titulo}`,
                //                 customize: function(win) {
                //                     $(win.document.body)
                //                         .find("h1")
                //                         .css("font-size", "12pt")
                //                         .css("color", "#333");
                //                 },
                //             },
                //         ],
                //     },
                // },
                columnDefs: [{
                    targets: '_all',
                    className: 'text-nowrap'
                }],
            });


        }

        function renderInfoBox(componente) {

            let html = '<div class="row">';

            componente.forEach(comp => {

                if (comp.tipo === 'info_box') {
                    html += `
                        <div class="col-md-2 col-sm-6 col-6">
                            <div class="info-box info-box-custom">
                                <span class="info-box-icon bg-${comp.cor}"><i class="${comp.icone}"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">${comp.titulo}</span>
                                    <span class="info-box-number">${comp.valor}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            html += '</div>';

            document.getElementById('resposta-info').innerHTML = html;
        }

        function renderProgressBarItens(itens) {
            let html = ``;

            itens.forEach(item => {
                html += `
                        <div class="progress-group" style="font-size: 0.8rem;">
                            <a href="" class='filtro-vendedor' data-vendedor="${item.vendedor}">${item.vendedor}</a>                           
                            <span class="float-right badge badge-primary ml-2">
                                <b>${item.qtdColetado}/${item.totalPneus}</b>
                            </span>
                            <span class="float-right badge badge-success ml-2">
                                (${item.valor})
                            </span>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-primary" style="width: ${item.percQtd}%"></div>
                            </div>   
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-success" style="width: ${item.percValor}%"></div>
                            </div>                               
                        </div>
                    `;
            });

            document.querySelector('#resposta-progress-vendedor').innerHTML = `${html}`;
        }
    </script>
@stop
