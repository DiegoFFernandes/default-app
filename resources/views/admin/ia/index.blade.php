@extends('layouts.master')

@section('title', 'Assistente IA')

@section('content')
    <section class="content" id="conteudo-ia" style="padding-bottom: 100px;">
        <div class="row" id="row-resposta">
            <div class="col-md-12">
                <div class="card card-primary card-outline">                    
                    <div class="card-body">
                        <div id="resumo-ia">                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">                    
                    <div class="card-body">
                        <div id="resposta">                            
                        </div>
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
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .modo-chat #input-area {
            max-width: 600px;
            margin: 0 auto;
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
    </style>
@stop

@section('js')
    <script>
        window.routes = {
            perguntar: '{{ route('ia-perguntar') }}',
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
        };

        function perguntarIA() {

            $('.title-ia').addClass('d-none');
            $('#row-resposta').removeClass('d-none');
            $('#conteudo-ia').addClass('modo-chat');

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

                    if (!data.dados || data.dados.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sem dados',
                            text: 'Nenhum resultado encontrado'
                        });
                        return;
                    }

                    if (data.tipo === 'texto') {
                        Swal.fire({
                            title: data.titulo,
                            text: data.dados,
                        });
                        return;
                    }

                    if (data.tipo === 'tabela') {
                        inicializaTabelaIA(data);
                    }

                    if (data.resumo_ia) {
                        document.getElementById('resumo-ia').innerHTML = `                            
                            <p>${data.resumo_ia}</p>
                        `;
                    }

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
            if (col === 'VALOR_MEDIO') {
                return {
                    title: 'Valor Médio (R$)',
                    data: col,
                    render: function(data) {
                        return 'R$ ' + parseFloat(data).toFixed(2);
                    }
                };
            }
            if (col === 'QTD') {
                return {
                    title: 'Quantidade',
                    data: col
                };
            }
            if (col === 'DS_SERVICOPNEU') {
                return {
                    title: 'Serviço',
                    data: col
                };
            }
            if (col === 'NM_PESSOA') {
                return {
                    title: 'Pessoa',
                    data: col
                };
            }
            if (col == 'DT_EMISSAO') {
                return {
                    title: 'Data Emissão',
                    data: col,
                    render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                };
            }

            return {
                title: col,
                data: col
            };
        }

        function inicializaTabelaIA(data) {

            const html = `
                        <div class="col-md-8">
                            <div class="card card-primary card-outline mb-3">
                                <div class="card-header">${data.titulo}</div>
                                <div class="card-body">
                                    <table id="tabelaIA" class="table table-bordered compact table-font-small">
                                        <thead></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>   
                        </div>                    
                        `;

            document.getElementById('resposta').innerHTML = html;

            let colunas = Object.keys(data.dados[0]).map(col => {
                return formatarColunaTabela(col);
            });

            $('#tabelaIA').DataTable({
                data: data.dados,
                columns: colunas,
                destroy: true,
                pageLength: 100,
                scrollY: '300px',
                language: {
                    url: window.routes.languageDatatables
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: "excelHtml5",
                                title: `${data.titulo}`,
                            },
                            {
                                extend: "print",
                                title: `${data.titulo}`,
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
            });
        }
    </script>
@stop
