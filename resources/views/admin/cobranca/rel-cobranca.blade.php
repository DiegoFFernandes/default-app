@extends('adminlte::page')

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
                    <div class="col-md-4">
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4">
                        <input id="filtro-cnpj" type="text" class="form-control" placeholder="Filtrar por CNPJ">
                    </div>
                    <div class="col-md-4">
                        <input id="filtro-regiao" type="text" class="form-control" placeholder="Filtrar por Região">
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="list-cobranca">
                            <div id="tabela-dados"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('js')
    <script>
        let dados = [];
        const tabela = new Tabulator("#tabela-dados", {
            ajaxURL: "{{ route('teste-cobranca') }}",
            layout: "fitDataStretch",
            groupBy: ["DS_AREACOMERCIAL", "DS_REGIAOCOMERCIAL"],
            groupHeader: [
                // Primeiro nível: DS_AREACOMERCIAL (agrupamento manual)
                function(value, count) {
                    let total = dados
                        .filter(d => d.DS_AREACOMERCIAL === value)
                        .reduce((sum, d) => sum + parseFloat(d.VL_SALDO || 0), 0);

                    let titulos = dados
                        .filter(d => d.DS_AREACOMERCIAL === value)
                        .reduce((sum, d) => sum + parseFloat(d.TITULOS || 0), 0);
                    return `${value} - R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${count} cliente${count > 1 ? 's' : ''}) - ${titulos} títulos`;

                    // return `
                //     <div style="display:inline-block; width:100%;">
                //         <span style="display:inline-block; width:15%; color: black"><strong>${value}</strong></span>
                //         <span style="display:inline-block; width:15%; color: black; text-align: right;">R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</span>
                //         <span style="display:inline-block; width:15%; color: black; text-align: right;">${count} cliente${count > 1 ? 's' : ''}</span>
                //         <span style="display:inline-block; width:15%; color: black; text-align: right;">${titulos} título${titulos > 1 ? 's' : ''}</span>
                //     </div>
                //     `;
                },
                // Segundo nível: DS_REGIAOCOMERCIAL
                function(value, count, data, group) {
                    let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);

                    return `${value} - R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${count} cliente${count > 1 ? 's' : ''})`;
                    // return `
                //     <div style="display:inline-block; width:100%;">
                //         <span style="display:inline-block; width:15%; color: black;"><strong>${value}</strong></span>
                //         <span style="display:inline-block; width:15%; color: black; text-align: right;">R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</span>
                //         <span style="display:inline-block; width:15%; color: black; text-align: right;">${count} cliente${count > 1 ? 's' : ''}</span>                            
                //     </div>
                //     `;
                }
            ],
            groupStartOpen: false,
            responsiveLayoutCollapseStartOpen: false, // ou true
            responsiveLayoutCollapseUseFormatters: true,
            groupToggleElement: "header",
            columns: [{
                    title: "Documento",
                    field: "NR_DOCUMENTO",
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
                    hozAlign: "right",
                    formatter: "money",
                    formatterParams: {
                        decimal: ",",
                        thousand: ".",
                        symbol: "R$ ",
                        precision: 2
                    }
                }
            ],
            ajaxResponse: function(url, params, response) {
                dados = response; // Salva os dados para o somatório funcionar
                let totalGeral = dados.reduce((sum, d) => sum + parseFloat(d.VL_SALDO || 0), 0);

                let maiorValor = 0;
                let regiaoMaior = '';
                let qtdTitulos = 0;

                dados.forEach(function(item) {
                    let valor = Number(item.VL_SALDO);
                    qtdTitulos += Number(item.TITULOS);
                    if (valor > maiorValor) {
                        maiorValor = valor;
                        regiaoMaior = item.DS_REGIAOCOMERCIAL;
                    }
                });

                $('#qtd-titulos').text(qtdTitulos);

                $('#soma-geral').text(totalGeral.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));

                $('#maior-divida').html(maiorValor.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }) + '<small>  ' + regiaoMaior + '</small>')


                return response;
            },
        });

        // Filtro por nome
        document.getElementById("filtro-nome").addEventListener("keyup", function() {
            const valor = this.value.toLowerCase();
            tabela.setFilter("NM_PESSOA", "like", valor);
        });

        // Filtro por CNPJ
        document.getElementById("filtro-cnpj").addEventListener("keyup", function() {
            const valor = this.value.toLowerCase();
            tabela.setFilter("NR_CNPJCPF", "like", valor);
        });
        // Filtro por Região
        document.getElementById("filtro-regiao").addEventListener("keyup", function() {
            const valor = this.value.toLowerCase();
            tabela.setFilter("DS_REGIAOCOMERCIAL", "like", valor);
        });

        tabela.on("dataFiltered", function(filters, rows) {
            let totalFiltrado = rows.reduce((soma, row) => soma + parseFloat(row.getData().VL_SALDO || 0), 0);
            $('#soma-geral').text(totalFiltrado.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));
        });
    </script>
@stop
