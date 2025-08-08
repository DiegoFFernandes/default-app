@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fa fa-list-ul"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number text-sm" id="soma-geral">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="far fa-thumbs-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text t-maior-divida">Maior divida</span>
                        <span class="info-box-number text-sm" id="maior-divida">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-sort-amount-up-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text titulos">Quantidade de Titulos</span>
                        <span class="info-box-number text-sm" id="qtd-titulos">

                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-cnpj" type="text" class="form-control" placeholder="Filtrar por CNPJ">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-supervisor" type="text" class="form-control"
                            placeholder="Filtrar por Supervisor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="daterange" type="text" class="form-control" placeholder="Filtrar por Vencimento">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkVencidas" name="filtroVencimento">
                            <label for="checkVencidas" class="custom-control-label">Vencidas</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkAvencer" name="filtroVencimento">
                            <label for="checkAvencer" class="custom-control-label">A Vencer</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox" id="checkAll"
                                name="filtroVencimento" checked>
                            <label for="checkAll" class="custom-control-label">Todas</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default btn-sm float-right" id="btn-limpar">
                                <i class="fas fa-eraser"></i> Limpar
                            </button>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRelatorio" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-relatorio-cobranca" data-toggle="pill"
                                    href="#painel-relatorio-cobranca" role="tab"
                                    aria-controls="painel-relatorio-cobranca" aria-selected="true">
                                    Relatório Cobrança
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-inadimplencia" data-toggle="pill"
                                    href="#painel-inadimplencia" role="tab" aria-controls="painel-inadimplencia"
                                    aria-selected="false">
                                    Inadimplência
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-cartao-cheque" data-toggle="pill"
                                    href="#painel-cartao-cheque" role="tab" aria-controls="painel-cartao-cheque"
                                    aria-selected="false">
                                    Cheques e Cartão
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentRelatorio">
                            <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                aria-labelledby="tab-relatorio-cobranca">
                                <div class="card-body p-2">
                                    {{-- <div class="d-flex flex-wrap gap-4 align-items-center mb-3 border-bottom pb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="checkCartaoCheque"
                                                name="checkCartaoCheque">
                                            <label class="form-check-label" for="checkCartaoCheque">
                                                Exibir apenas com cartão ou cheque
                                            </label>
                                        </div>
                                    </div> --}}
                                    <div class="list-cobranca">
                                        <div id="tabela-relatorio-cobranca"
                                            class="table table-bordered table-hover text-sm"></div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Contas Mensais</h3>
                                            <div class="float-right mb-3">
                                                <button id="btnReset" class="btn btn-danger btn-sm mb-3 "><i
                                                        class="fas fa-retweet"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: auto">
                                                <canvas id="contas-mensal-chart" height="50"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-inadimplencia" role="tabpanel"
                                aria-labelledby="tab-inadimplencia">
                                <div class="card-body p-2">
                                    <div class="d-flex flex-wrap gap-4 align-items-center mb-3 border-bottom pb-2">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="checkSaldo"
                                                name="checkSaldo">
                                            <label class="form-check-label" for="checkSaldo">
                                                Exibir apenas com saldo devedor
                                            </label>
                                        </div>

                                        <div class="form-check form-switch m-0 ml-3">
                                            <input class="form-check-input" type="checkbox" id="checkCartaoCheque"
                                                name="checkCartaoCheque">
                                            <label class="form-check-label" for="checkCartaoCheque">
                                                Exibir apenas com cartão ou cheque
                                            </label>
                                        </div>
                                    </div>
                                    <div id="tabela-inadimplencia" class="table table-bordered table-hover text-sm"></div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="col-lg-12">
                                                    <canvas id="grafico-pizza-atrasado"></canvas>
                                                </div>
                                                <div class="col-lg-12  mt-3">
                                                    <canvas id="grafico-pizza-inadimplente"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="float-right">
                                                    <button id="refresh-inadimplencia" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-retweet"></i></button>
                                                </div>
                                                <canvas id="grafico-inadimplencia"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                    <div id="tabela-cartao-cheque" class="table table-bordered table-hover text-sm">
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Contas Mensais</h3>
                                            <div class="float-right mb-3">
                                                <button id="resetTela2" class="btn btn-danger btn-sm mb-3 "><i
                                                        class="fas fa-retweet"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: auto">
                                                <canvas id="grafico2" height="50"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
@stop
@section('css')
    <style>
        .tabulator .tabulator-row:nth-child(even) {
            background-color: inherit !important;
        }

        .tabulator .tabulator-cell {
            font-size: 13px;
        }

        .tabulator .tabulator-header {
            font-size: 13px;
            font-weight: bold;
        }

        .tabulator .tabulator-group {
            font-size: 13px;
        }
    </style>
@stop

@section('js')
    <script>
        //var dados = [];
        let dados = [];
        var tabelaRelatorioCobranca = null;
        //var tabelaInadimplencia = null;
        var tabelaInadimplencia;
        var inicioData = 0;
        var fimData = 0;
        var ChartContasMes;
        var ChartContasMestab2; // para o grafico da tab2 
        let gerenteSelecionado = null; // gerente filtrado
        let gerenteSelecionadoInadimplencia = null; //gerente da tab inadimplência


        carregaDadosRelatorioCobranca(1, 'tabela-relatorio-cobranca');

        // tela = 1 - Relatório Cobrança
        // tela = 2 - Cartão Cheque
        function carregaDadosRelatorioCobranca(tela = 1, idTabela) {
            tabela = new Tabulator(`#${idTabela}`, {
                ajaxURL: "{{ route('get-relatorio-cobranca') }}",
                ajaxParams: {
                    tela: tela
                },
                layout: "fitDataStretch",
                groupBy: ["DS_AREACOMERCIAL", "NM_SUPERVISOR", "NM_VENDEDOR", "NM_PESSOA"],
                groupHeader: [
                    // Primeiro nível: Gerente Comercial (agrupamento manual)
                    function(value, count, data, group) {
                        let atrasos = data.filter(d => d.NR_DIAS > 0).length;
                        let pc_atrasos = (atrasos / data.length * 100).toFixed(2);
                        let total_atrasos = data.reduce((sum, row) => sum + (row.NR_DIAS > 0 ? Number(row
                            .VL_SALDO || 0) : 0), 0);
                        let receberMaior61dias = data[0]?.RECEBERMAIOR61DIASGERENTECOMERCIAL || 0;
                        let liquidadoMaior61dias = data[0]?.LIQUIDADOMAIOR61DIASGERENTECOMERCIAL || 0;
                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;
                        let pcMaior61dias = (100 - (liquidadoMaior61dias / receberMaior61dias * 100) || 0)
                            .toFixed(2);

                        let receberMenor60dias = data[0]?.RECEBERMENOR60DIASGERENTECOMERCIAL || 0;
                        let liquidadoMenor60dias = data[0]?.LIQUIDADOMENOR60DIASGERENTECOMERCIAL || 0;
                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;
                        let pcMenor60dias = (100 - (liquidadoMenor60dias / receberMenor60dias * 100) || 0)
                            .toFixed(2);

                        let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);

                        return `<div style="display:inline-block; width:97%; background-color:#343a40; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} (R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div> 
                                    <div class="d-inline-block float-right mr-4">Inadimp. 61 a 240 Dias - ${pcMaior61dias}% (R$ ${saldoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                
                                    <div class="d-inline-block float-right mr-4">Atrasos 1 a 60 Dias - ${pcMenor60dias}% (R$ ${saldoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;

                    },
                    // Segundo nível: SUPERVISOR
                    function(value, count, data, group) {
                        let atrasos = data.filter(d => d.NR_DIAS > 0).length;
                        let pc_atrasos = (atrasos / data.length * 100).toFixed(2);
                        let total_atrasos = data.reduce((sum, row) => sum + (row.NR_DIAS > 0 ? Number(row
                            .VL_SALDO || 0) : 0), 0);
                        let receberMaior61dias = data[0]?.RECEBERMAIOR61DIAS_SUPERVISOR || 0;
                        let liquidadoMaior61dias = data[0]?.LIQUIDADOMAIOR61DIAS_SUPERVISOR || 0;
                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;
                        let pcMaior61dias = (100 - (liquidadoMaior61dias / receberMaior61dias * 100) || 0)
                            .toFixed(2);

                        let receberMenor60dias = data[0]?.RECEBERMENOR60DIAS_SUPERVISOR || 0;
                        let liquidadoMenor60dias = data[0]?.LIQUIDADOMENOR60DIAS_SUPERVISOR || 0;
                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;
                        let pcMenor60dias = (100 - (liquidadoMenor60dias / receberMenor60dias * 100) || 0)
                            .toFixed(2);

                        let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);

                        return `<div style="display:inline-block; width:97%; background-color:#495057; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} (R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div> 
                                    <div class="d-inline-block float-right mr-4">Inadimp. 61 a 240 Dias - ${pcMaior61dias}% (R$ ${saldoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                
                                    <div class="d-inline-block float-right mr-4">Atrasos 1 a 60 Dias - ${pcMenor60dias}% (R$ ${saldoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;

                    },
                    // Terceiro nível: NM_VENDEDOR
                    function(value, count, data, group) {
                        let atrasos = data.filter(d => d.NR_DIAS > 0).length;
                        let pc_atrasos = (atrasos / data.length * 100).toFixed(2);
                        let total_atrasos = data.reduce((sum, row) => sum + (row.NR_DIAS > 0 ? Number(row
                            .VL_SALDO || 0) : 0), 0);
                        let receberMaior61dias = data[0]?.RECEBERMAIOR61DIAS_VENDEDOR || 0;
                        let liquidadoMaior61dias = data[0]?.LIQUIDADOMAIOR61DIAS_VENDEDOR || 0;
                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;
                        let pcMaior61dias = (100 - (liquidadoMaior61dias / receberMaior61dias * 100) || 0)
                            .toFixed(2);

                        let receberMenor60dias = data[0]?.RECEBERMENOR60DIAS_VENDEDOR || 0;
                        let liquidadoMenor60dias = data[0]?.LIQUIDADOMENOR60DIAS_VENDEDOR || 0;
                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;
                        let pcMenor60dias = (100 - (liquidadoMenor60dias / receberMenor60dias * 100) || 0)
                            .toFixed(2);

                        let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);

                        return `<div style="display:inline-block; width:97%; background-color:#6c757d; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} (R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div> 
                                    <div class="d-inline-block float-right mr-4">Inadimp. 61 a 240 Dias - ${pcMaior61dias}% (R$ ${saldoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                
                                    <div class="d-inline-block float-right mr-4">Atrasos 1 a 60 Dias - ${pcMenor60dias}% (R$ ${saldoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;
                    },
                    function(value, count, data, group) {
                        let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);
                        return `<div style=" display:inline-block; width:97%; background-color:#dee2e6; color:black; padding:5px; font-weight:bold;">
                                ${value} - R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                            </div>`;
                    }
                ],
                groupStartOpen: false,
                responsiveLayoutCollapseStartOpen: false, // ou true
                responsiveLayoutCollapseUseFormatters: true,
                groupToggleElement: "header",
                columns: [{
                        title: "Documento",
                        field: "NR_DOCUMENTO",
                        hozAlign: "center",
                    },
                    {
                        title: "Nome",
                        field: "NM_PESSOA",
                        widthGrow: 2,
                    },
                    {
                        title: "CNPJ",
                        field: "NR_CNPJCPF",
                        width: 200,
                        hozAlign: "center",
                    },
                    {
                        title: "Vencimento",
                        field: "DT_VENCIMENTO",
                        hozAlign: "center",
                        sorter: "date",
                        formatter: function(cell) {
                            let data = cell.getValue();
                            if (!data) return "";
                            let [ano, mes, dia] = data.split("-");
                            return `${dia}/${mes}/${ano}`;
                        }
                    },
                    {
                        title: "Valor",
                        field: "VL_SALDO",
                        hozAlign: "left",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            symbol: "R$ ",
                            precision: 2
                        }
                    }, {
                        title: "Juros",
                        field: "VL_JUROS",
                        hozAlign: "center",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            precision: 2
                        }
                    },

                    {
                        title: "Dias Atraso",
                        field: "NR_DIAS",
                        hozAlign: "left",
                    }
                ],
                ajaxResponse: function(url, params, response) {
                    dados = response; // Salva os dados para o somatório funcionar

                    atualizaDados(dados); // Atualiza os dados iniciais

                    // configurarFiltroCheckbox("checkCartaoCheque", tabela, aplicarFiltro);
                    return response;
                },
            });
            tabela.on("groupVisibilityChanged", function(group, visible) {
                // DS_AREACOMERCIAL
                if (group.getField() !== "DS_AREACOMERCIAL") return;

                const key = group.getKey();

                if (visible) {
                    // fecha o gerente anterior se abrir um novo sem fechar o anterior
                    if (gerenteSelecionado && gerenteSelecionado !== key) {
                        const prev = tabela.getGroups().find(g => g.getKey() === gerenteSelecionado);
                        if (prev) {
                            prev.hide(); // colapsa o grupo anterior
                        }
                    }

                    gerenteSelecionado = key;
                    const dadosFiltrados = dados.filter(d => d.DS_AREACOMERCIAL === key);
                    atualizaDados(dadosFiltrados);
                } else {
                    // se clicar a segunda vez no mesmo grupo, fecha e reseta a tabela
                    if (gerenteSelecionado === key) {
                        gerenteSelecionado = null;
                        atualizaDados(dados);
                    }
                }
            });
        }

        document.getElementById("resetTela2").addEventListener("click", function() {
            // limpa seleção
            gerenteSelecionado = null;

            // fecha todos os grupos visíveis
            tabela.getGroups().forEach(group => {
                if (group.isVisible()) {
                    group.hide();
                }
            });

            // limpa filtros
            tabela.clearFilter();

            // atualiza gráfico completo
            atualizaDados(dados);
        });

        document.getElementById("btnReset").addEventListener("click", function() {
            // limpa seleção
            gerenteSelecionado = null;

            // fecha todos os grupos visíveis
            tabela.getGroups().forEach(group => {
                if (group.isVisible()) {
                    group.hide();
                }
            });

            // limpa filtros
            tabela.clearFilter();

            // atualiza gráfico completo
            atualizaDados(dados);
        });

        function atualizaDados(dados) {
            let totalGeral = dados.reduce((sum, d) => sum + parseFloat(d.VL_SALDO || 0), 0);

            let maiorValor = 0;
            let regiaoMaior = '';
            let qtdTitulos = 0;

            dados.forEach(function(item) {
                let valor = Number(item.VL_SALDO);
                qtdTitulos++;
                if (valor > maiorValor) {
                    maiorValor = valor;
                    regiaoMaior = item.DS_REGIAOCOMERCIAL;
                }
            });

            carregaChart(dados);

            $('#qtd-titulos').text(qtdTitulos);

            $('#soma-geral').text(totalGeral.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));

            $('#maior-divida').html(maiorValor.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }) + '<small>  ' + regiaoMaior + '</small>')
        }

        function tableFiltred() {
            tabela.on("dataFiltered", function(filters, rows) {

                let dadosFiltrados = rows.map(row => row.getData());
                // Atualiza os dados com os novos filtros
                atualizaDados(dadosFiltrados);
            });
        }

        function aplicarFiltro() {
            const pessoa = document.getElementById("filtro-nome").value.toLowerCase();
            const supervisor = document.getElementById("filtro-supervisor").value.toLowerCase();
            const nr_cnpj = document.getElementById("filtro-cnpj").value.toLowerCase();
            const vendedor = document.getElementById("filtro-vendedor").value.toLowerCase();
            const cartaoCheque = document.getElementById("checkCartaoCheque").checked;

            // console.log(inicioData, fimData);

            const hoje = moment().format('YYYY-MM-DD');

            const filtros = [];

            if (inicioData && fimData) {
                inicioData = moment(inicioData).format('YYYY-MM-DD');
                fimData = moment(fimData).format('YYYY-MM-DD');
                filtros.push({
                    field: "DT_VENCIMENTO",
                    type: ">=",
                    value: inicioData
                });
                filtros.push({
                    field: "DT_VENCIMENTO",
                    type: "<=",
                    value: fimData
                });
            }

            if (pessoa) {
                filtros.push({
                    field: "NM_PESSOA",
                    type: "like",
                    value: pessoa
                });
            }
            if (supervisor) {
                filtros.push({
                    field: "NM_SUPERVISOR",
                    type: "like",
                    value: supervisor
                });
            }
            if (nr_cnpj) {
                filtros.push({
                    field: "NR_CNPJCPF",
                    type: "like",
                    value: nr_cnpj
                });
            }
            if (vendedor) {
                filtros.push({
                    field: "NM_VENDEDOR",
                    type: "like",
                    value: vendedor
                });
            }
            if (cartaoCheque) {
                filtros.push({
                    field: "CD_FORMAPAGTO",
                    type: "in",
                    value: ["CC", "CH"]
                });
            }

            const vencidas = document.getElementById("checkVencidas").checked;
            const avencer = document.getElementById("checkAvencer").checked;
            const todas = document.getElementById("checkAll").checked;

            if (todas) {
                // pega tudo
            } else if (vencidas && !avencer) {
                filtros.push({
                    field: "DT_VENCIMENTO",
                    type: "<",
                    value: hoje
                });
            } else if (avencer && !vencidas) {
                filtros.push({
                    field: "DT_VENCIMENTO",
                    type: ">=",
                    value: hoje
                });
            } else if (!vencidas && !avencer) {
                filtros.push({
                    field: "DT_VENCIMENTO",
                    type: "=",
                    value: "0000-00-00"
                });
            }

            tabela.setFilter(filtros);
            tableFiltred();
        }

        function carregaChart(dadosNovo) {
            var ChartData;
            var MesUnificado = [];


            if (ChartContasMes) {
                ChartContasMes.destroy(); // Destrói o gráfico anterior
            }

            MesUnificado = [...new Set(dadosNovo.map(d => d.MES))];
            areasUnicas = [...new Set(dadosNovo.map(d => d.DS_AREACOMERCIAL))];
            const somaPorMes = {};

            const coresFixas = [{
                    borderColor: 'rgba(60,141,188,0.9)', // Azul
                    backgroundColor: 'rgba(60,141,188,0.4)'
                },
                {
                    borderColor: 'rgba(220, 53, 69,1)', // Cinza
                    backgroundColor: 'rgba(220, 53, 69,0.4)'
                }
            ];

            const datasets = areasUnicas.map((area, index) => {
                // Resetar o acumulador para cada área
                const somaPorMes = {};

                // Para cada mês, soma os valores da área correspondente
                MesUnificado.forEach(mes => {
                    dados
                        .filter(d => d.DS_AREACOMERCIAL === area && d.MES === mes)
                        .forEach(d => {
                            const valor = parseFloat(d.VL_SALDO);
                            somaPorMes[mes] = (somaPorMes[mes] || 0) + valor;
                        });
                });

                // Cria um array na ordem dos meses com valor 0 caso não haja dado
                const dadosPorMes = MesUnificado.map(mes => somaPorMes[mes] || 0);

                const cor = coresFixas[index] || {
                    borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                    backgroundColor: '#' + Math.floor(Math.random() * 16777215).toString(16) + '44'
                };

                return {
                    label: area,
                    data: dadosPorMes,
                    borderColor: cor.borderColor,
                    backgroundColor: cor.backgroundColor,
                    tension: 0.3
                };
            });

            ChartData = {
                labels: MesUnificado,
                datasets: datasets.map(ds => ({
                    label: ds.label,
                    backgroundColor: ds.backgroundColor,
                    borderColor: ds.borderColor,
                    pointRadius: false,
                    pointColor: ds.borderColor,
                    pointStrokeColor: ds.borderColor,
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: ds.borderColor,
                    data: ds.data
                }))
            };

            const ctx = document.getElementById('contas-mensal-chart').getContext('2d');
            ChartContasMes = new Chart(ctx, {
                type: 'bar',
                data: ChartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },

            });

            //grafico da tela 2 
            const ctx1 = document.getElementById('grafico2').getContext('2d');
            if (typeof ChartContasMestab2 !== 'undefined') {
                ChartContasMestab2.destroy();
            }
            ChartContasMestab2 = new Chart(ctx1, {
                type: 'bar',
                data: ChartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });
        }

        // Filtro por pessoa
        document.querySelectorAll('#filtro-nome').forEach(el => {
            el.addEventListener("keyup", aplicarFiltro);
        });

        // Filtro por CNPJ
        document.querySelectorAll('#filtro-cnpj').forEach(el => {
            el.addEventListener("keyup", aplicarFiltro);
        });
        // Filtro por Supervisor
        document.querySelectorAll('#filtro-supervisor').forEach(el => {
            el.addEventListener("keyup", aplicarFiltro);
        });

        // Filtro por Vendedor
        document.querySelectorAll('#filtro-vendedor').forEach(el => {
            el.addEventListener("keyup", aplicarFiltro);
        });

        // Filtro por Vencimento
        document.querySelectorAll('input[name="filtroVencimento"]').forEach(el => {
            el.addEventListener("change", aplicarFiltro);
        });

        // Limpar filtros
        document.getElementById("btn-limpar").addEventListener("click", function() {
            document.getElementById("filtro-nome").value = "";
            document.getElementById("filtro-cnpj").value = "";
            document.getElementById("filtro-vendedor").value = "";
            document.getElementById("filtro-supervisor").value = "";
            // Resetar date range
            const daterangepicker = $('#daterange').data('daterangepicker');
            if (daterangepicker) {
                daterangepicker.setStartDate(moment());
                daterangepicker.setEndDate(moment());
                $('#daterange').val('');
            }

            document.getElementById('checkAll').checked = true;
            document.getElementById('checkVencidas').checked = false;
            document.getElementById('checkAvencer').checked = false;
            tabela.clearFilter();
        });

        // Filtro por Vencimento
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {

            inicioData = picker.startDate.format('YYYY-MM-DD');
            fimData = picker.endDate.format('YYYY-MM-DD');
            aplicarFiltro();

        });

        $('#tab-relatorio-cobranca').click(function() {
            carregaDadosRelatorioCobranca(1, 'tabela-relatorio-cobranca');
        });

        $('#tab-inadimplencia').click(function() {
            tabelaInadimplencia = new Tabulator("#tabela-inadimplencia", {
                ajaxURL: "{{ route('get-recebimento-liquidado') }}",
                layout: "fitDataStretch",
                groupBy: ["DS_AREACOMERCIAL", "NM_SUPERVISOR", "NM_VENDEDOR", "NM_PESSOA"],
                groupHeader: [
                    // Primeiro nível: Gerente Comercial (agrupamento manual)
                    function(value, count, data, group) {

                        let receberMenor60dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMENOR60DIAS || 0), 0);
                        let liquidadoMenor60dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMENOR60DIAS || 0), 0);

                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;

                        let receberMaior61dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMAIOR61DIAS || 0), 0);
                        let liquidadoMaior61dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMAIOR61DIAS || 0), 0);

                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;

                        let saldoTotal = saldoMenor60dias + saldoMaior61dias;

                        return `<div style="display:inline-block; width:97%; background-color:#343a40; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} R$ (${saldoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                     
                                    <div class="d-inline-block float-right mr-4">Liq. 61 a 240 dias (R$ ${liquidadoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Inad. 61 a 240 dias (R$ ${receberMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Liq. ate 60 dias (R$ ${liquidadoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Atra. ate 60 dias (R$ ${receberMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;
                    },

                    function(value, count, data, group) {

                        let receberMenor60dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMENOR60DIAS || 0), 0);
                        let liquidadoMenor60dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMENOR60DIAS || 0), 0);

                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;

                        let receberMaior61dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMAIOR61DIAS || 0), 0);
                        let liquidadoMaior61dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMAIOR61DIAS || 0), 0);

                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;

                        let saldoTotal = saldoMenor60dias + saldoMaior61dias;

                        return `<div style="display:inline-block; width:97%; background-color:#495057; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} R$ (${saldoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                     
                                    <div class="d-inline-block float-right mr-4">Liq. 61 a 240 dias (R$ ${liquidadoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Inad. 61 a 240 dias (R$ ${receberMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Liq. ate 60 dias (R$ ${liquidadoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Atra. ate 60 dias (R$ ${receberMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;
                    },

                    function(value, count, data, group) {

                        let receberMenor60dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMENOR60DIAS || 0), 0);
                        let liquidadoMenor60dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMENOR60DIAS || 0), 0);

                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;

                        let receberMaior61dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMAIOR61DIAS || 0), 0);
                        let liquidadoMaior61dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMAIOR61DIAS || 0), 0);

                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;

                        let saldoTotal = saldoMenor60dias + saldoMaior61dias;

                        return `<div style="display:inline-block; width:97%; background-color:#6c757d; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} R$ (${saldoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                     
                                    <div class="d-inline-block float-right mr-4">Liq. 61 a 240 dias: R$ ${liquidadoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</div>
                                    <div class="d-inline-block float-right mr-4">Inad. 61 a 240 dias: R$ ${receberMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</div>
                                    <div class="d-inline-block float-right mr-4">Liq. ate 60 dias: R$ ${liquidadoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</div>
                                    <div class="d-inline-block float-right mr-4">Atra. ate 60 dias: R$ ${receberMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</div>
                                </div>`;
                    },
                    function(value, count, data, group) {

                        let receberMenor60dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMENOR60DIAS || 0), 0);
                        let liquidadoMenor60dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMENOR60DIAS || 0), 0);

                        let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;

                        let receberMaior61dias = data.reduce((sum, row) => sum + Number(row
                            .RECEBERMAIOR61DIAS || 0), 0);
                        let liquidadoMaior61dias = data.reduce((sum, row) => sum + Number(
                            row
                            .LIQUIDADOMAIOR61DIAS || 0), 0);

                        let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;

                        let saldoTotal = saldoMenor60dias + saldoMaior61dias;

                        let bgSaldo = saldoTotal === 0 ? '#78c2ad' : '#343a40';

                        return `<div style="display:inline-block; width:97%; background-color:${bgSaldo}; color:white; padding:5px; font-weight:bold;">
                                    <div class="d-inline-block mr-4">${value} R$ (${saldoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>                                     
                                    <div class="d-inline-block float-right mr-4">Liq. 61 a 240 dias (R$ ${liquidadoMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Inad. 61 a 240 dias (R$ ${receberMaior61dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Liq. ate 60 dias (R$ ${liquidadoMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                    <div class="d-inline-block float-right mr-4">Atra. ate 60 dias (R$ ${receberMenor60dias.toLocaleString('pt-BR', { minimumFractionDigits: 2 })})</div>
                                </div>`;
                    }

                ],
                groupStartOpen: false,
                responsiveLayoutCollapseStartOpen: false, // ou true
                responsiveLayoutCollapseUseFormatters: true,
                groupToggleElement: "header",
                columns: [{
                        title: "Documento",
                        field: "NR_DOCUMENTO",
                        hozAlign: "center",
                    },
                    {
                        title: "Nome",
                        field: "NM_PESSOA",
                        widthGrow: 2,
                    },
                    {
                        title: "Vencimento",
                        field: "DT_VENCIMENTO",
                        hozAlign: "center",
                        sorter: "date",
                        formatter: function(cell) {
                            let data = cell.getValue();
                            if (!data) return "";
                            let [ano, mes, dia] = data.split("-");
                            return `${dia}/${mes}/${ano}`;
                        }
                    }, {
                        title: "Atrasos 1 a 60 dias",
                        field: "RECEBERMENOR60DIAS",
                        hozAlign: "left",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            symbol: "R$ ",
                            precision: 2
                        }
                    }, {
                        title: "Liquidado 1 a 60 dias",
                        field: "LIQUIDADOMENOR60DIAS",
                        hozAlign: "center",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            precision: 2
                        }
                    },
                    {
                        title: "Inadim. 61 a 240 dias",
                        field: "RECEBERMAIOR61DIAS",
                        hozAlign: "left",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            symbol: "R$ ",
                            precision: 2
                        }
                    }, {
                        title: "Liquidado 61 a 240 dias",
                        field: "LIQUIDADOMAIOR61DIAS",
                        hozAlign: "center",
                        formatter: "money",
                        formatterParams: {
                            decimal: ",",
                            thousand: ".",
                            precision: 2
                        }
                    }
                ],
                ajaxResponse: function(url, params, response) {
                    dados = response; // Salva os dados para o somatório funcionar

                    atualizaDados(dados); // Atualiza os dados iniciais

                    gerarGraficoInadimplencia(dados) //gera o gráfico de inadimplência


                    // Da para reaproveitar a função para gerar os gráficos de pizza
                    // Gráfico: 01 a 60 dias
                    const totais60 = calcularTotais(dados, 'RECEBERMENOR60DIAS',
                        'LIQUIDADOMENOR60DIAS');


                    criarGraficoPizza('grafico-pizza-atrasado', 'Atrasados', totais60);

                    // Gráfico: 61 a 240 dias
                    const totais61a240 = calcularTotais(dados, 'RECEBERMAIOR61DIAS',
                        'LIQUIDADOMAIOR61DIAS');
                    criarGraficoPizza('grafico-pizza-inadimplente', 'Inadimplentes', totais61a240);

                    return response;
                },
            });
            tabelaInadimplencia.on("groupVisibilityChanged", function(group, visible) {
                if (group.getField() !== "DS_AREACOMERCIAL") return;

                const gerente = group.getKey();

                if (visible) {
                    if (gerenteSelecionadoInadimplencia && gerenteSelecionadoInadimplencia !==
                        gerente) {
                        const prev = tabelaInadimplencia.getGroups().find(g => g.getKey() ===
                            gerenteSelecionadoInadimplencia);
                        if (prev) {
                            prev.hide();
                        }
                    }

                    gerenteSelecionadoInadimplencia = gerente;
                    filtrarGraficoPorSupervisor(gerente);
                } else {
                    if (gerenteSelecionadoInadimplencia === gerente) {
                        gerenteSelecionadoInadimplencia = null;
                        gerarGraficoInadimplencia(dados); // gráfico completo
                    }
                }
            });

            document.getElementById("refresh-inadimplencia").addEventListener("click", function() {
                // resetar seleção
                gerenteSelecionadoInadimplencia = null;

                // fechar todos os grupos abertos na tabela
                tabelaInadimplencia.getGroups().forEach(group => {
                    if (group.isVisible()) {
                        group.hide();
                    }
                });

                // limpar filtros
                tabelaInadimplencia.clearFilter();

                // atualizar gráfico completo
                gerarGraficoInadimplencia(dados);
            });

            configurarFiltroCheckbox("checkSaldo", tabelaInadimplencia, filtroSaldo);
        });

        $('#tab-cartao-cheque').click(function() {
            carregaDadosRelatorioCobranca(2, 'tabela-cartao-cheque');
        });

        // função para filtrar o gráfico de inadimplência por supervisor
        function filtrarGraficoPorSupervisor(gerenteSelecionado, cargoSelecionado) {
            const dadosFiltrados = dados.filter(item => item.DS_AREACOMERCIAL === gerenteSelecionado);
            gerarGraficoInadimplencia(dadosFiltrados);

            const totais60 = calcularTotais(dadosFiltrados, 'RECEBERMENOR60DIAS', 'LIQUIDADOMENOR60DIAS');


            criarGraficoPizza('grafico-pizza-atrasado', 'Atrasados', totais60);

            const totais61a240 = calcularTotais(dadosFiltrados, 'RECEBERMAIOR61DIAS',
                'LIQUIDADOMAIOR61DIAS');
            criarGraficoPizza('grafico-pizza-inadimplente', 'Inadimplentes', totais61a240);



        }

        function gerarGraficoInadimplencia(dados) {
            const supervisores = {};

            dados.forEach(item => {
                const supervisor = item.NM_SUPERVISOR || 'Não informado';

                const inad60 = (parseFloat(item.RECEBERMENOR60DIAS || 0) - parseFloat(item.LIQUIDADOMENOR60DIAS ||
                    0));
                const inad240 = (parseFloat(item.RECEBERMAIOR61DIAS || 0) - parseFloat(item.LIQUIDADOMAIOR61DIAS ||
                    0));

                if (!supervisores[supervisor]) {
                    supervisores[supervisor] = {
                        inad60: 0,
                        inad240: 0
                    };
                }

                supervisores[supervisor].inad60 += inad60;
                supervisores[supervisor].inad240 += inad240;
            });

            const labels = [];
            const dados60 = [];
            const dados240 = [];

            Object.entries(supervisores).forEach(([nome, inad]) => {
                if (inad.inad60 !== 0 || inad.inad240 !== 0) {
                    labels.push(nome);
                    dados60.push(inad.inad60);
                    dados240.push(inad.inad240);
                }
            });

            // ajustar a altura do gráfico dinamicamente
            const alturaPorBarra = 40; //altura de cada barra
            const alturaAdicional = 100; // espaço para o título e legendas

            // calcula a altura para o grafico
            let alturaCalculada = (labels.length * alturaPorBarra) + alturaAdicional;

            // altura minima em caso de poucos dados
            if (alturaCalculada < 300) {
                alturaCalculada = 300;
            }

            //aplica a altura calculada ao container do gráfico
            const canvas = document.getElementById('grafico-inadimplencia');
            const containerDoGrafico = canvas.parentNode;
            containerDoGrafico.style.height = alturaCalculada + 'px';


            // cria o grafico
            const ctx = canvas.getContext('2d');

            // verifica se já existe um gráfico e o destrói antes de criar um novo
            if (window.meuGraficoInadimplencia) {
                window.meuGraficoInadimplencia.destroy();
            }

            window.meuGraficoInadimplencia = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Atrasados',
                            data: dados60,
                            backgroundColor: '#343a40'
                        },
                        {
                            label: 'Inadimplentes',
                            data: dados240,
                            backgroundColor: '#dc3545'
                        }
                    ]
                },
                options: {
                    indexAxis: 'y', // gráfico horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Inadimplência por Supervisor'
                        },
                        tooltip: {
                            enabled: false // dasativa o tooltip
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#fff',
                            font: {
                                weight: 'bold'
                            },
                            formatter: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2
                                });
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            type: 'logarithmic', // para mostrar as barras com valores pequenos
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2 //formata valor do eixo x (desativado atualmente)
                                    });
                                }
                            }
                        },
                        y: {
                            ticks: {
                                autoSkip: false,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function calcularTotais(dados, campoReceber, campoLiquidado) {
            let totalReceber = 0;
            let totalLiquidado = 0;

            dados.forEach(item => {
                totalReceber += parseFloat(item[campoReceber] || 0);
                totalLiquidado += parseFloat(item[campoLiquidado] || 0);
            });

            const saldoPendente = totalReceber - totalLiquidado;
            const saldoAjustado = saldoPendente > 0 ? saldoPendente : 0.01;

            return {
                totalReceber,
                totalLiquidado,
                saldoAjustado
            };
        };

        function criarGraficoPizza(canvasId, titulo, totais) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            if (window[canvasId] && typeof window[canvasId].destroy === 'function') {
                window[canvasId].destroy();
            }

            // Cria novo gráfico e armazena
            window[canvasId] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Recebido', 'Pendente'],
                    datasets: [{
                        data: [totais.totalLiquidado, totais.saldoAjustado],
                        backgroundColor: ['#343a40', '#dc3545'],
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: titulo
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (value) => {
                                const percent = ((value / totais.totalReceber) * 100).toFixed(1);
                                const formattedValue = value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2
                                });
                                return `R$ ${formattedValue}\n(${percent}%)`;
                            },
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        };



        function configurarFiltroCheckbox(checkboxId, tabela, filtroCallback) {
            const checkbox = document.getElementById(checkboxId);
            if (!checkbox) return;

            checkbox.addEventListener("change", function() {
                if (this.checked) {
                    tabela.addFilter(filtroCallback);
                } else {
                    tabela.clearFilter(true);
                }
            });
        }

        function filtroSaldo(data) {

            let receberMenor60dias = parseFloat(data.RECEBERMENOR60DIAS) || 0;
            let liquidadoMenor60dias = parseFloat(data.LIQUIDADOMENOR60DIAS) || 0;

            let saldoMenor60dias = receberMenor60dias - liquidadoMenor60dias;

            let receberMaior61dias = parseFloat(data.RECEBERMAIOR61DIAS) || 0;
            let liquidadoMaior61dias = parseFloat(data.LIQUIDADOMAIOR61DIAS) || 0;

            let saldoMaior61dias = receberMaior61dias - liquidadoMaior61dias;

            let saldoTotal = saldoMenor60dias + saldoMaior61dias;

            return saldoTotal > 0;
        }

        tabela.on("dataFiltered", function(filters, rows) {
            let totalFiltrado = rows.reduce((soma, row) => soma + parseFloat(row.getData().VL_SALDO ||
                0), 0);
            $('#soma-geral').text(totalFiltrado.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));
        });
    </script>
@stop
