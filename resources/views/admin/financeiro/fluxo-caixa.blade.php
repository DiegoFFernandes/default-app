@extends('layouts.master')

@section('title', 'Fluxo de Caixa')

@section('content')
    <section class="content">
        <div class="content-fluid">

            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="btn-group btn-group-toggle w-100" role="group">
                                <a href="{{ route('fluxo-caixa.index', ['tipo_data' => 'real', 'ref' => $refSemanaAtual, 'semanas' => $qtdSemanas]) }}"
                                    class="btn btn-sm {{ $tipoData === 'real' ? 'btn-primary active' : 'btn-outline-primary' }}">Data
                                    Real</a>
                                <a href="{{ route('fluxo-caixa.index', ['tipo_data' => 'personalizada', 'ref' => $refSemanaAtual, 'semanas' => $qtdSemanas]) }}"
                                    class="btn btn-sm {{ $tipoData === 'personalizada' ? 'btn-primary active' : 'btn-outline-primary' }}">Data
                                    Personalizada</a>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-week"></i></span>
                                </div>
                                <input type="text" class="form-control" id="periodo-fluxo" readonly
                                    value="{{ $dias[0]->format('d/m/Y') }} a {{ $dias[count($dias) - 1]->format('d/m/Y') }}">
                                <div class="input-group-append">
                                    <a class="btn btn-outline-secondary"
                                        href="{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'ref' => $refSemanaAnterior, 'semanas' => $qtdSemanas]) }}"
                                        title="Semana anterior"><i class="fas fa-chevron-left"></i></a>
                                    <a class="btn btn-outline-secondary"
                                        href="{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'semanas' => $qtdSemanas]) }}"
                                        title="Semana atual">Hoje</a>
                                    <a class="btn btn-outline-secondary"
                                        href="{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'ref' => $refSemanaProxima, 'semanas' => $qtdSemanas]) }}"
                                        title="Próxima semana"><i class="fas fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-md-right">
                            <button class="btn btn-sm btn-success" id="btn-novo-lancamento"><i
                                    class="fas fa-plus mr-1"></i>Lançamento Manual</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center mt-2">
                        <div class="col-12 text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <span class="btn btn-sm btn-light disabled" style="cursor:default;">Semanas exibidas:</span>
                                @foreach ([1, 2, 4] as $opcaoSemanas)
                                    <a href="{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'ref' => $refSemanaAtual, 'semanas' => $opcaoSemanas]) }}"
                                        class="btn btn-sm {{ $qtdSemanas === $opcaoSemanas ? 'btn-primary active' : 'btn-outline-primary' }}">{{ $opcaoSemanas }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-6 col-md-3 mb-2">
                    <div class="stat-card stat-primary">
                        <div class="stat-title">
                            <span><i class="fas fa-wallet"></i> Saldo Banco(s)</span>
                            <button type="button" class="btn-add-saldo-banco" id="btn-add-saldo-banco"
                                title="Adicionar saldo de banco/financeira">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                        <div class="stat-value">R$ {{ number_format($saldoInicial, 2, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="stat-card stat-success">
                        <div class="stat-title"><i class="fas fa-arrow-circle-up"></i> Total Entradas</div>
                        <div class="stat-value">R$ {{ number_format(array_sum($totalEntradasPorDia), 2, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="stat-card stat-danger">
                        <div class="stat-title"><i class="fas fa-arrow-circle-down"></i> Total Saídas</div>
                        <div class="stat-value">R$ {{ number_format(array_sum($totalSaidasPorDia), 2, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="stat-card {{ end($saldoAcumulado) >= 0 ? 'stat-info' : 'stat-danger' }}">
                        <div class="stat-title"><i class="fas fa-flag-checkered"></i> Saldo Final da Semana</div>
                        <div class="stat-value">R$ {{ number_format(end($saldoAcumulado), 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table mr-1 text-muted"></i> Fluxo de Caixa Semanal
                        <span class="badge {{ $tipoData === 'personalizada' ? 'badge-primary' : 'badge-info' }} ml-1">
                            {{ $tipoData === 'personalizada' ? 'Data Personalizada' : 'Data Real' }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary"><i class="fas fa-info-circle"></i> Dados fictícios - mockup</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive fluxo-table-wrapper">
                        <table class="table table-sm table-bordered mb-0 tabela-fluxo-caixa">
                            <thead>
                                <tr>
                                    <th class="col-categoria">Categoria</th>
                                    @foreach ($dias as $i => $dia)
                                        <th
                                            class="text-center col-dia {{ $dia->isWeekend() ? 'dia-fim-semana' : '' }} {{ $dia->isToday() ? 'dia-atual' : '' }}">
                                            {{ $dia->translatedFormat('D') }}<br>
                                            <small>{{ $dia->format('d/m') }}</small>
                                        </th>
                                        @if ((($i + 1) % 7 === 0) && ($i + 1 < count($dias)))
                                            <th class="text-center col-subtotal-semana">Total<br>
                                                <small>Sem {{ intdiv($i, 7) + 1 }}</small>
                                            </th>
                                        @endif
                                    @endforeach
                                    <th class="text-center col-total">Total Geral</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="linha-saldo-inicial">
                                    <td>Saldo Inicial</td>
                                    <td colspan="{{ $colspanSaldoInicial }}" class="text-center">R$
                                        {{ number_format($saldoInicial, 2, ',', '.') }}</td>
                                    <td class="text-center">-</td>
                                </tr>

                                <tr class="linha-grupo linha-grupo-receber">
                                    <td><i class="fas fa-caret-down grupo-icone mr-1"></i> Contas a Receber</td>
                                    <x-fluxo-celulas :valores="$totalContasReceberPorDia" modo="total"
                                        classe-celula="valor-positivo" :fins-de-semana="$finsDeSemana" />
                                </tr>
                                @foreach ($contasReceber as $categoria => $dadosCategoria)
                                    @php $slugCategoria = \Illuminate\Support\Str::slug($categoria); @endphp
                                    <tr class="grupo-receber linha-detalhe linha-categoria">
                                        <td class="pl-4">
                                            <button type="button" class="btn-detalhe-categoria" data-grupo="receber"
                                                data-slug="{{ $slugCategoria }}" title="Ver clientes deste total">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            {{ $categoria }}
                                        </td>
                                        <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true"
                                            tipo="receber" :categoria="$categoria" :fins-de-semana="$finsDeSemana" />
                                    </tr>
                                    @foreach ($dadosCategoria['detalhe'] as $cliente => $valores)
                                        <tr class="grupo-receber linha-detalhe linha-cliente" data-slug-pai="{{ $slugCategoria }}"
                                            style="display:none;">
                                            <td class="pl-5">{{ $cliente }}</td>
                                            <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                                tipo="receber" :categoria="$categoria" :cliente="$cliente"
                                                :fins-de-semana="$finsDeSemana" />
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="grupo-receber linha-detalhe">
                                    <td class="pl-4">Lançamento Manual (Entrada)
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;" title="Editável"></i>
                                    </td>
                                    <x-fluxo-celulas :valores="$lancamentoManualEntrada" modo="detalhe"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-total linha-total-entrada">
                                    <td>Total Entradas</td>
                                    <x-fluxo-celulas :valores="$totalEntradasPorDia" modo="total" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-grupo linha-grupo-pagar">
                                    <td><i class="fas fa-caret-down grupo-icone mr-1"></i> Contas a Pagar</td>
                                    <x-fluxo-celulas :valores="$totalContasPagarPorDia" modo="total"
                                        classe-celula="valor-negativo" :fins-de-semana="$finsDeSemana" />
                                </tr>
                                @foreach ($contasPagar as $categoria => $dadosCategoria)
                                    @php $slugCategoriaPagar = \Illuminate\Support\Str::slug($categoria); @endphp
                                    <tr class="grupo-pagar linha-detalhe linha-categoria">
                                        <td class="pl-4">
                                            <button type="button" class="btn-detalhe-categoria" data-grupo="pagar"
                                                data-slug="{{ $slugCategoriaPagar }}" title="Ver fornecedores deste total">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            {{ $categoria }}
                                        </td>
                                        <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true"
                                            tipo="pagar" :categoria="$categoria" :fins-de-semana="$finsDeSemana" />
                                    </tr>
                                    @foreach ($dadosCategoria['detalhe'] as $fornecedor => $valores)
                                        <tr class="grupo-pagar linha-detalhe linha-cliente" data-slug-pai="{{ $slugCategoriaPagar }}"
                                            style="display:none;">
                                            <td class="pl-5">{{ $fornecedor }}</td>
                                            <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                                tipo="pagar" :categoria="$categoria" :cliente="$fornecedor"
                                                :fins-de-semana="$finsDeSemana" />
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="grupo-pagar linha-detalhe">
                                    <td class="pl-4">Lançamento Manual (Saída)
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;" title="Editável"></i>
                                    </td>
                                    <x-fluxo-celulas :valores="$lancamentoManualSaida" modo="detalhe"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-total linha-total-saida">
                                    <td>Total Saídas</td>
                                    <x-fluxo-celulas :valores="$totalSaidasPorDia" modo="total" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-saldo-dia">
                                    <td>Saldo do Dia</td>
                                    <x-fluxo-celulas :valores="$saldoDia" modo="total" :colorir-por-sinal="true"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>
                                <tr class="linha-saldo-acumulado">
                                    <td>Saldo Acumulado</td>
                                    <x-fluxo-celulas :valores="$saldoAcumulado" modo="total" :colorir-por-sinal="true"
                                        subtotal-modo="ultimo" total-modo="traco" :fins-de-semana="$finsDeSemana" />
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
@stop

@section('css')
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .09);
            border-left: 4px solid;
            border-radius: 4px;
            padding: 10px 12px;
            height: 100%;
        }

        .stat-card .stat-title {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card .stat-value {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .btn-add-saldo-banco {
            border: none;
            background: transparent;
            padding: 0;
            margin-left: auto;
            color: #007bff;
            font-size: .8rem;
            cursor: pointer;
            line-height: 1;
        }

        .btn-add-saldo-banco:hover {
            color: #0056b3;
        }

        .stat-primary {
            border-left-color: #007bff;
        }

        .stat-primary .stat-title i,
        .stat-primary .stat-value {
            color: #007bff;
        }

        .stat-success {
            border-left-color: #28a745;
        }

        .stat-success .stat-title i,
        .stat-success .stat-value {
            color: #28a745;
        }

        .stat-danger {
            border-left-color: #dc3545;
        }

        .stat-danger .stat-title i,
        .stat-danger .stat-value {
            color: #dc3545;
        }

        .stat-info {
            border-left-color: #17a2b8;
        }

        .stat-info .stat-title i,
        .stat-info .stat-value {
            color: #17a2b8;
        }

        .fluxo-table-wrapper {
            overflow-x: auto;
        }

        .tabela-fluxo-caixa {
            font-size: 11.5px;
            white-space: nowrap;
        }

        .tabela-fluxo-caixa th,
        .tabela-fluxo-caixa td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        .tabela-fluxo-caixa thead th {
            background-color: #444B53;
            color: #fff;
            text-align: center;
            font-weight: 600;
            border-color: #2d3238 !important;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .col-categoria {
            min-width: 220px;
            position: sticky;
            left: 0;
            background: #444B53;
            z-index: 3;
        }

        tbody .col-categoria,
        tbody td:first-child {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1;
        }

        .col-dia {
            min-width: 90px;
        }

        .col-total {
            min-width: 100px;
        }

        .dia-atual {
            background-color: #2d6da3 !important;
        }

        .tabela-fluxo-caixa thead th.dia-fim-semana {
            background-color: #ffe9a8;
            color: #6b5300;
        }

        .dia-fim-semana-cel {
            background-color: #fff8e1;
        }

        .col-subtotal-semana {
            min-width: 95px;
            background-color: #eef1f4;
            border-left: 2px solid #444B53 !important;
        }

        .tabela-fluxo-caixa thead th.col-subtotal-semana {
            background-color: #2d3238;
        }

        .linha-grupo {
            background-color: #eef1f4;
            font-weight: 700;
            cursor: pointer;
        }

        tbody .linha-grupo td:first-child {
            background-color: #eef1f4;
        }

        .grupo-icone {
            transition: transform .15s ease;
        }

        .linha-grupo.collapsed .grupo-icone {
            transform: rotate(-90deg);
        }

        .linha-detalhe td {
            color: #495057;
        }

        .btn-detalhe-categoria {
            border: none;
            background: transparent;
            padding: 0 4px 0 0;
            color: #6c757d;
            cursor: pointer;
        }

        .btn-detalhe-categoria:hover {
            color: #444B53;
        }

        .linha-cliente td {
            color: #868e96;
            font-style: italic;
        }

        tbody .linha-cliente td:first-child {
            background-color: #fff;
        }

        .linha-total {
            font-weight: 700;
            border-top: 2px solid #444B53;
        }

        .linha-total-entrada td {
            color: #1e7e34;
        }

        tbody .linha-total-entrada td:first-child {
            background-color: #fff;
        }

        .linha-total-saida td {
            color: #a71d2a;
        }

        .linha-saldo-inicial td {
            background-color: #f8f9fa;
            font-weight: 700;
        }

        tbody .linha-saldo-inicial td:first-child {
            background-color: #f8f9fa;
        }

        .linha-saldo-dia,
        .linha-saldo-acumulado {
            font-weight: 700;
            background-color: #fffbe6;
        }

        tbody .linha-saldo-dia td:first-child,
        tbody .linha-saldo-acumulado td:first-child {
            background-color: #fffbe6;
        }

        .linha-saldo-acumulado {
            border-top: 2px solid #444B53;
            border-bottom: 3px double #444B53;
        }

        .valor-positivo {
            color: #1e7e34;
        }

        .valor-negativo {
            color: #a71d2a;
        }

        .valor-clicavel {
            cursor: pointer;
        }

        .valor-clicavel:hover {
            background-color: #d7e6f5;
            text-decoration: underline;
        }

        .swal-title-fluxo {
            font-size: 1.1rem !important;
        }

        .swal-confirm-fluxo {
            font-size: .8rem !important;
            padding: .4rem 1rem !important;
        }
    </style>
@stop

@section('js')
    <script type="text/javascript">
        var fluxoContasReceber = @json($contasReceber);
        var fluxoContasPagar = @json($contasPagar);

        // Abre um modal com os lançamentos (NR_LANCAMENTO, Data Real, Cliente/Fornecedor e
        // Valor) que compõem o total clicado — na linha de categoria mostra todas as pessoas
        // daquele dia, na linha de pessoa mostra só os lançamentos dela.
        $(document).on('click', '.valor-clicavel', function() {
            var tipo = $(this).data('tipo') || 'receber';
            var categoria = $(this).data('categoria');
            var cliente = $(this).data('cliente');
            var dia = $(this).data('dia');

            var fonte = tipo === 'pagar' ? fluxoContasPagar : fluxoContasReceber;
            var grupo = fonte[categoria];
            if (!grupo) {
                return;
            }

            var itens = grupo.lancamentos[dia] || [];
            if (cliente) {
                itens = itens.filter(function(item) {
                    return item.nm_pessoa === cliente;
                });
            }

            if (itens.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sem lançamentos',
                    text: 'Não há lançamentos para compor este total.'
                });
                return;
            }

            var rotuloPessoa = tipo === 'pagar' ? 'Fornecedor' : 'Cliente';
            var totalItens = 0;
            var linhas = itens.map(function(item) {
                totalItens += item.valor;
                return '<tr>' +
                    '<td>' + item.nr_lancamento + '</td>' +
                    '<td>' + item.dt_real + '</td>' +
                    '<td>' + item.nm_pessoa + '</td>' +
                    '<td class="text-right">' + item.valor.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }) + '</td>' +
                    '</tr>';
            }).join('');

            Swal.fire({
                title: categoria,
                width: 650,
                confirmButtonText: 'Fechar',
                customClass: {
                    title: 'swal-title-fluxo',
                    confirmButton: 'swal-confirm-fluxo'
                },
                html: '<div style="max-height:320px; overflow-y:auto; text-align:left;">' +
                    '<table class="table table-sm table-striped" style="font-size:12px;">' +
                    '<thead><tr><th>Nº Lançamento</th><th>Data Real</th><th>' + rotuloPessoa +
                    '</th><th class="text-right">Valor</th></tr></thead>' +
                    '<tbody>' + linhas + '</tbody>' +
                    '<tfoot><tr><th colspan="3">Total</th><th class="text-right">R$ ' +
                    totalItens.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }) + '</th></tr></tfoot>' +
                    '</table></div>'
            });
        });

        // Colapsa/expande os grupos Contas a Receber / Contas a Pagar
        $('.linha-grupo-receber').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.grupo-receber').not('.linha-cliente').toggle();
            // Ao recolher/expandir o grupo, sempre fecha o detalhamento por cliente
            $('.grupo-receber.linha-cliente').hide();
            $('.btn-detalhe-categoria[data-grupo="receber"] i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        $('.linha-grupo-pagar').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.grupo-pagar').not('.linha-cliente').toggle();
            // Ao recolher/expandir o grupo, sempre fecha o detalhamento por fornecedor
            $('.grupo-pagar.linha-cliente').hide();
            $('.btn-detalhe-categoria[data-grupo="pagar"] i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        // Detalha (mostra/oculta) os clientes/fornecedores que compõem o total de uma categoria
        $(document).on('click', '.btn-detalhe-categoria', function(e) {
            e.stopPropagation();
            var grupo = $(this).data('grupo');
            var slug = $(this).data('slug');
            $('.grupo-' + grupo + '.linha-cliente[data-slug-pai="' + slug + '"]').toggle();
            $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
        });

        $('#btn-novo-lancamento').on('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Lançamento manual',
                text: 'Tela de cadastro de lançamento adicional será implementada nas próximas etapas do projeto.',
                confirmButtonText: 'Ok'
            });
        });

        // Modal (somente front, ainda sem integração com o backend) para cadastrar o saldo
        // de um banco/financeira.
        $('#btn-add-saldo-banco').on('click', function() {
            Swal.fire({
                title: 'Adicionar Saldo Banco/Financeira',
                width: 500,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    title: 'swal-title-fluxo',
                    confirmButton: 'swal-confirm-fluxo',
                    cancelButton: 'swal-confirm-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Data Lançamento</label>' +
                    '<input type="date" id="swal-data-lancamento" class="form-control form-control-sm">' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Nome Banco/Financeira</label>' +
                    '<input type="text" id="swal-nome-banco" class="form-control form-control-sm" placeholder="Ex: Banco do Brasil">' +
                    '</div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Saldo Banco</label>' +
                    '<input type="number" step="0.01" id="swal-saldo-banco" class="form-control form-control-sm" placeholder="0,00">' +
                    '</div>' +
                    '</div>',
                preConfirm: function() {
                    return {
                        dataLancamento: document.getElementById('swal-data-lancamento').value,
                        nomeBanco: document.getElementById('swal-nome-banco').value,
                        saldoBanco: document.getElementById('swal-saldo-banco').value
                    };
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Ainda não salva',
                        text: 'Esse cadastro será integrado ao backend nas próximas etapas do projeto.',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });
    </script>
@stop
