@extends('layouts.master')

@section('title', 'Assistente IA')

@section('content')
    <section class="content-fluid" id="conteudo-ia">
        <div id="resposta-info"></div>
        <div class="row d-none" id="row-resposta">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title card-header-tabela"></h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-8" id="resposta-tabela">

                            </div>
                            <!-- /.col -->
                            <div class="col-md-4">
                                <p class="text-center">
                                    <strong>Goal Completion</strong>
                                </p>

                                <div class="progress-group">
                                    Add Products to Cart
                                    <span class="float-right"><b>160</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: 80%"></div>
                                    </div>
                                </div>
                                <!-- /.progress-group -->

                                <div class="progress-group">
                                    Complete Purchase
                                    <span class="float-right"><b>310</b>/400</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-danger" style="width: 75%"></div>
                                    </div>
                                </div>

                                <!-- /.progress-group -->
                                <div class="progress-group">
                                    <span class="progress-text">Visit Premium Page</span>
                                    <span class="float-right"><b>480</b>/800</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" style="width: 60%"></div>
                                    </div>
                                </div>

                                <!-- /.progress-group -->
                                <div class="progress-group">
                                    Send Inquiries
                                    <span class="float-right"><b>250</b>/500</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" style="width: 50%"></div>
                                    </div>
                                </div>
                                <!-- /.progress-group -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- ./card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-3 col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                        17%</span>
                                    <h5 class="description-header">$35,210.43</h5>
                                    <span class="description-text">TOTAL REVENUE</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                        0%</span>
                                    <h5 class="description-header">$10,390.90</h5>
                                    <span class="description-text">TOTAL COST</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                        20%</span>
                                    <h5 class="description-header">$24,813.53</h5>
                                    <span class="description-text">TOTAL PROFIT</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i>
                                        18%</span>
                                    <h5 class="description-header">1200</h5>
                                    <span class="description-text">GOAL COMPLETIONS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
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
    </style>
@stop

@section('js')
    <script>
        window.routes = {
            perguntar: '{{ route('ia-perguntar') }}',
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
        };

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
                        inicializaTabelaIA(data.tabela.dados);
                    }

                    if (data.componentes) {
                        
                        renderInfoBox(data.componentes);
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

            $('#tabelaIA').DataTable({
                data: dados,
                columns: colunas,
                destroy: true,
                pageLength: 100,
                scrollX: true,
                scrollCollapse: true,
                scrollY: '300px',
                info: false,
                language: {
                    url: window.routes.languageDatatables
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: "excelHtml5",
                                title: `${dados.titulo}`,
                            },
                            {
                                extend: "print",
                                title: `${dados.titulo}`,
                                customize: function(win) {
                                    $(win.document.body)
                                        .find("h1")
                                        .css("font-size", "12pt")
                                        .css("color", "#333");
                                },
                            },
                        ],
                    },
                },
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

                    console.log('Renderizando info-box:', comp);
                    html += `
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
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

            console.log(html);

            document.getElementById('resposta-info').innerHTML = html;
        }
    </script>
@stop
