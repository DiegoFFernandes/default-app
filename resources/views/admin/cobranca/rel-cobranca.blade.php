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


                },
                // Segundo nível: DS_REGIAOCOMERCIAL
                function(value, count, data, group) {
                    let total = data.reduce((sum, row) => sum + Number(row.VL_SALDO || 0), 0);

                    return `${value} - R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${count} cliente${count > 1 ? 's' : ''})`;

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
                    headerFilter: "input"
                },
                {
                    title: "CNPJ",
                    field: "NR_CNPJCPF",
                    width: 200,
                    headerFilter: "input"
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


        tabela.on("dataFiltered", function(filters, rows) {
            let totalFiltrado = rows.reduce((soma, row) => soma + parseFloat(row.getData().VL_SALDO || 0), 0);
            $('#soma-geral').text(totalFiltrado.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));
        });
    </script>
@stop
