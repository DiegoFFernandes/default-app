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
                                <span class="btn btn-sm btn-light disabled" style="cursor:default;">Semanas Projetadas:</span>
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
                            <span><i class="fas fa-wallet"></i> Saldo Banco(s) Hoje</span>
                            <span class="stat-title-actions">
                                <button type="button" class="btn-add-saldo-banco" id="btn-listar-saldo-banco"
                                    title="Ver lançamentos de saldo">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" class="btn-add-saldo-banco" id="btn-add-saldo-banco"
                                    title="Adicionar saldo de banco/financeira">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </span>
                        </div>
                        <div class="stat-value">R$ {{ number_format($saldoBancoHoje, 2, ',', '.') }}</div>
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
                    <div class="stat-card {{ end($saldoDia) >= 0 ? 'stat-info' : 'stat-danger' }}">
                        <div class="stat-title"><i class="fas fa-flag-checkered"></i> Saldo Final da Semana</div>
                        <div class="stat-value">R$ {{ number_format(end($saldoDia), 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table mr-1 text-muted"></i> Fluxo de Caixa Semanal
                    </h3>
                    <div class="card-tools">
                        <span class="badge {{ $tipoData === 'personalizada' ? 'badge-primary' : 'badge-info' }} ml-1">
                            {{ $tipoData === 'personalizada' ? 'Data Personalizada' : 'Data Real' }}
                        </span>
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
                                        @if (($i + 1) % 7 === 0 && $i + 1 < count($dias))
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
                                    <td>Saldo Banco</td>
                                    <x-fluxo-celulas :valores="$saldoBancoPorDia" modo="total" :colorir-por-sinal="true"
                                        subtotal-modo="traco" total-modo="traco" :fins-de-semana="$finsDeSemana" :destaques="$diasComLancamentoManual"
                                        :clicavel-por-dia="$saldoBancoClicavelPorDia" />
                                </tr>

                                <tr class="linha-grupo linha-grupo-receber">
                                    <td><i class="fas fa-caret-down grupo-icone mr-1"></i> Contas a Receber</td>
                                    <x-fluxo-celulas :valores="$totalContasReceberPorDia" modo="total" classe-celula="valor-positivo"
                                        :fins-de-semana="$finsDeSemana" />
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
                                        <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true" tipo="receber"
                                            :categoria="$categoria" :fins-de-semana="$finsDeSemana" />
                                    </tr>
                                    @foreach ($dadosCategoria['detalhe'] as $cliente => $valores)
                                        <tr class="grupo-receber linha-detalhe linha-cliente"
                                            data-slug-pai="{{ $slugCategoria }}" style="display:none;">
                                            <td class="pl-5">{{ $cliente }}</td>
                                            <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                                tipo="receber" :categoria="$categoria" :cliente="$cliente" :fins-de-semana="$finsDeSemana" />
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="grupo-receber linha-detalhe">
                                    <td class="pl-4">Lançamento Manual (Entrada)
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;"
                                            title="Editável"></i>
                                    </td>
                                    <x-fluxo-celulas :valores="$lancamentoManualEntrada" modo="detalhe" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-total linha-total-entrada">
                                    <td>Total Entradas</td>
                                    <x-fluxo-celulas :valores="$totalEntradasExibicaoPorDia" modo="total"
                                        subtotal-modo="traco" total-modo="traco" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-grupo linha-grupo-pagar">
                                    <td><i class="fas fa-caret-down grupo-icone mr-1"></i> Contas a Pagar</td>
                                    <x-fluxo-celulas :valores="$totalContasPagarPorDia" modo="total" classe-celula="valor-negativo"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>
                                @foreach ($contasPagar as $categoria => $dadosCategoria)
                                    @php $slugCategoriaPagar = \Illuminate\Support\Str::slug($categoria); @endphp
                                    <tr class="grupo-pagar linha-detalhe linha-categoria">
                                        <td class="pl-4">
                                            <button type="button" class="btn-detalhe-categoria" data-grupo="pagar"
                                                data-slug="{{ $slugCategoriaPagar }}"
                                                title="Ver fornecedores deste total">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            {{ $categoria }}
                                        </td>
                                        <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true"
                                            tipo="pagar" :categoria="$categoria" :fins-de-semana="$finsDeSemana" />
                                    </tr>
                                    @foreach ($dadosCategoria['detalhe'] as $fornecedor => $valores)
                                        <tr class="grupo-pagar linha-detalhe linha-cliente"
                                            data-slug-pai="{{ $slugCategoriaPagar }}" style="display:none;">
                                            <td class="pl-5">{{ $fornecedor }}</td>
                                            <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                                tipo="pagar" :categoria="$categoria" :cliente="$fornecedor" :fins-de-semana="$finsDeSemana" />
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="grupo-pagar linha-detalhe">
                                    <td class="pl-4">Lançamento Manual (Saída)
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;"
                                            title="Editável"></i>
                                    </td>
                                    <x-fluxo-celulas :valores="$lancamentoManualSaida" modo="detalhe" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-total linha-total-saida">
                                    <td>Total Saídas</td>
                                    <x-fluxo-celulas :valores="$totalSaidasPorDia" modo="total" :fins-de-semana="$finsDeSemana" />
                                </tr>

                                <tr class="linha-saldo-dia">
                                    <td>Saldo do Dia</td>
                                    <x-fluxo-celulas :valores="$saldoDia" modo="total" :colorir-por-sinal="true"
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

        .stat-title-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add-saldo-banco {
            border: none;
            background: transparent;
            padding: 0;
            color: #28a745;
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

        .dia-lancamento-manual {
            background-color: #cfe2ff !important;
            color: #084298 !important;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px #6ea8fe;
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

        .linha-saldo-dia {
            font-weight: 700;
            background-color: #fffbe6;
            border-top: 2px solid #444B53;
            border-bottom: 3px double #444B53;
        }

        tbody .linha-saldo-dia td:first-child {
            background-color: #fffbe6;
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
    <script src="{{ asset('js/dashboard/swal-draggable.js') }}?v={{ time() }}"></script>
    <script type="text/javascript">
        var fluxoContasReceber = @json($contasReceber);
        var fluxoContasPagar = @json($contasPagar);
        var fluxoSaldoBancoDetalhePorDia = @json($saldoBancoDetalhePorDia);

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
                    '<td style="width:85px; white-space:nowrap;">' + item.dt_real + '</td>' +
                    '<td>' + item.nm_pessoa + '</td>' +
                    '<td class="text-right" style="width:100px; white-space:nowrap;">' + item.valor.toLocaleString(
                        'pt-BR', {
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
                    '<thead><tr style="white-space:nowrap;"><th>Nº Lanc</th><th style="width:85px; white-space:nowrap;">Data Real</th><th>' +
                    rotuloPessoa + '</th><th class="text-right" style="width:100px; white-space:nowrap;">Valor</th></tr></thead>' +
                    '<tbody>' + linhas + '</tbody>' +
                    '<tfoot><tr><th colspan="3">Total</th><th class="text-right" style="white-space:nowrap;">R$ ' +
                    totalItens.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }) + '</th></tr></tfoot>' +
                    '</table></div>',
                didOpen: function() {
                    makeSwalDraggable();
                }
            });
        });

        // Colapsa/expande os grupos Contas a Receber / Contas a Pagar
        $('.linha-grupo-receber').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.grupo-receber').not('.linha-cliente').toggle();
            // Ao recolher/expandir o grupo, sempre fecha o detalhamento por cliente
            $('.grupo-receber.linha-cliente').hide();
            $('.btn-detalhe-categoria[data-grupo="receber"] i').removeClass('fa-chevron-down').addClass(
                'fa-chevron-right');
        });

        $('.linha-grupo-pagar').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.grupo-pagar').not('.linha-cliente').toggle();
            // Ao recolher/expandir o grupo, sempre fecha o detalhamento por fornecedor
            $('.grupo-pagar.linha-cliente').hide();
            $('.btn-detalhe-categoria[data-grupo="pagar"] i').removeClass('fa-chevron-down').addClass(
                'fa-chevron-right');
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

        // Modal para cadastrar o saldo de um banco/financeira (grava em fluxo_caixa_saldo via AJAX).
        $('#btn-add-saldo-banco').on('click', function() {
            Swal.fire({
                title: 'Adicionar Saldo Banco/Financeira',
                width: 500,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
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
                    '<input type="text" inputmode="decimal" id="swal-saldo-banco" class="form-control form-control-sm" placeholder="R$ 0,00">' +
                    '</div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    $('#swal-saldo-banco').inputmask({
                        mask: ['R$ 9,99', 'R$ 99,99', 'R$ 999,99', 'R$ 9.999,99',
                            'R$ 99.999,99',
                            'R$ 999.999,99', 'R$ 9.999.999,99'
                        ],
                        radixPoint: ','
                    });
                },
                preConfirm: function() {
                    var dsBanco = document.getElementById('swal-nome-banco').value.trim();
                    var vlSaldo = $('#swal-saldo-banco').val().replace('R$', '').trim()
                        .replace(/\./g, '').replace(',', '.');
                    var dtSaldo = document.getElementById('swal-data-lancamento').value;

                    if (!dsBanco || vlSaldo === '' || !dtSaldo) {
                        Swal.showValidationMessage('Preencha todos os campos.');
                        return false;
                    }

                    return $.ajax({
                        method: 'POST',
                        url: '{{ route('fluxo-caixa.salvar-saldo-banco') }}',
                        data: {
                            _token: $('[name="csrf-token"]').attr('content'),
                            ds_banco: dsBanco,
                            vl_saldo: vlSaldo,
                            dt_saldo: dtSaldo
                        }
                    }).catch(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Erro ao salvar o saldo.';
                        Swal.showValidationMessage(msg);
                        // Retornar aqui "recupera" a promise (resolve em vez de rejeitar) —
                        // senão o Swal fica preso no loading do botão em vez de voltar ao form.
                        return false;
                    });
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saldo salvo com sucesso!',
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        window.location.reload();
                    });
                }
            });
        });

        // Monta as linhas (com botões de editar/excluir) usadas tanto no drill-down por dia
        // quanto na busca por período.
        function montarLinhasSaldoBanco(itens) {
            return itens.map(function(item) {
                return '<tr>' +
                    '<td>' + item.ds_banco + '</td>' +
                    '<td>' + item.dt_saldo_formatada + '</td>' +
                    '<td class="text-right">' + item.vl_saldo.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }) + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-xs btn-outline-primary btn-editar-saldo-banco" data-id="' +
                    item.id + '" data-banco="' + item.ds_banco + '" data-valor="' + item.vl_saldo +
                    '" data-data="' + item.dt_saldo +
                    '" title="Editar"><i class="fas fa-pencil-alt"></i></button> ' +
                    '<button type="button" class="btn btn-xs btn-outline-danger btn-excluir-saldo-banco" data-id="' +
                    item.id + '" title="Excluir"><i class="fas fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');
        }

        // Clique num valor da linha "Saldo Banco": mostra os lançamentos (por banco) que
        // compõem o total daquele dia.
        $(document).on('click', '.saldo-banco-clicavel', function() {
            var dia = $(this).data('dia');
            var itens = fluxoSaldoBancoDetalhePorDia[dia] || [];

            if (itens.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sem lançamentos',
                    text: 'Nenhum saldo de banco informado até esse dia.'
                });
                return;
            }

            Swal.fire({
                title: 'Saldos considerados neste dia',
                width: 650,
                confirmButtonText: 'Fechar',
                customClass: {
                    title: 'swal-title-fluxo',
                    confirmButton: 'swal-confirm-fluxo'
                },
                html: '<div style="max-height:320px; overflow-y:auto; text-align:left;">' +
                    '<table class="table table-sm table-striped" style="font-size:12px;">' +
                    '<thead><tr><th>Banco</th><th>Data</th><th class="text-right">Valor</th><th class="text-center">Ações</th></tr></thead>' +
                    '<tbody>' + montarLinhasSaldoBanco(itens) + '</tbody>' +
                    '</table></div>',
                didOpen: function() {
                    makeSwalDraggable();
                }
            });
        });

        // Botão "Ver lançamentos de saldo": busca por período com botão de busca. Recebe
        // datas opcionais para reabrir já com o filtro anterior (ex: depois de editar/excluir).
        function abrirListaSaldoBanco(dtInicioParam, dtFimParam) {
            var dtInicioPadrao = dtInicioParam || '{{ $dias[0]->format('Y-m-d') }}';
            var dtFimPadrao = dtFimParam || '{{ end($dias)->format('Y-m-d') }}';

            Swal.fire({
                title: 'Lançamentos de Saldo Banco/Financeira',
                width: 700,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    title: 'swal-title-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="form-row align-items-end mb-2">' +
                    '<div class="col-5"><label class="mb-1" style="font-size:12px;">De</label>' +
                    '<input type="date" id="swal-filtro-dt-inicio" class="form-control form-control-sm" value="' +
                    dtInicioPadrao + '"></div>' +
                    '<div class="col-5"><label class="mb-1" style="font-size:12px;">Até</label>' +
                    '<input type="date" id="swal-filtro-dt-fim" class="form-control form-control-sm" value="' +
                    dtFimPadrao + '"></div>' +
                    '<div class="col-2"><button type="button" id="swal-btn-buscar-saldo" class="btn btn-sm btn-primary btn-block">Buscar</button></div>' +
                    '</div>' +
                    '<div id="swal-resultado-saldo-banco" style="max-height:320px; overflow-y:auto;"></div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    document.getElementById('swal-btn-buscar-saldo').addEventListener('click',
                        buscarSaldoBanco);
                    buscarSaldoBanco();
                }
            });
        }

        $('#btn-listar-saldo-banco').on('click', function() {
            abrirListaSaldoBanco();
        });

        function buscarSaldoBanco() {
            var dtInicio = document.getElementById('swal-filtro-dt-inicio').value;
            var dtFim = document.getElementById('swal-filtro-dt-fim').value;
            var $resultado = $('#swal-resultado-saldo-banco');

            if (!dtInicio || !dtFim) {
                return;
            }

            $resultado.html('<div class="text-center text-muted py-3">Carregando...</div>');

            $.ajax({
                method: 'GET',
                url: '{{ route('fluxo-caixa.listar-saldo-banco') }}',
                data: {
                    dt_inicio: dtInicio,
                    dt_fim: dtFim
                }
            }).done(function(response) {
                if (!response.dados || response.dados.length === 0) {
                    $resultado.html('<div class="text-center text-muted py-3">Nenhum lançamento no período.</div>');
                    return;
                }

                $resultado.html(
                    '<table class="table table-sm table-striped" style="font-size:12px;">' +
                    '<thead><tr><th>Banco</th><th>Data</th><th class="text-right">Valor</th><th class="text-center">Ações</th></tr></thead>' +
                    '<tbody>' + montarLinhasSaldoBanco(response.dados) + '</tbody>' +
                    '</table>'
                );
            }).fail(function() {
                $resultado.html('<div class="text-center text-danger py-3">Erro ao buscar lançamentos.</div>');
            });
        }

        // Editar um lançamento de saldo (abre modal pré-preenchido, salva via AJAX e recarrega).
        $(document).on('click', '.btn-editar-saldo-banco', function() {
            var id = $(this).data('id');
            var banco = $(this).data('banco');
            var valor = $(this).data('valor');
            var data = $(this).data('data');

            Swal.fire({
                title: 'Editar Saldo Banco/Financeira',
                width: 500,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                customClass: {
                    title: 'swal-title-fluxo',
                    confirmButton: 'swal-confirm-fluxo',
                    cancelButton: 'swal-confirm-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Data Lançamento</label>' +
                    '<input type="date" id="swal-edit-data" class="form-control form-control-sm" value="' +
                    data +
                    '"></div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Nome Banco/Financeira</label>' +
                    '<input type="text" id="swal-edit-banco" class="form-control form-control-sm" value="' +
                    banco +
                    '"></div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Saldo Banco</label>' +
                    '<input type="text" inputmode="decimal" id="swal-edit-valor" class="form-control form-control-sm" value="' +
                    Number(valor).toFixed(2).replace('.', ',') + '"></div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    $('#swal-edit-valor').inputmask({
                        mask: ['R$ 9,99', 'R$ 99,99', 'R$ 999,99', 'R$ 9.999,99',
                            'R$ 99.999,99',
                            'R$ 999.999,99', 'R$ 9.999.999,99'
                        ],
                        radixPoint: ','
                    });
                },
                preConfirm: function() {
                    var dsBanco = document.getElementById('swal-edit-banco').value.trim();
                    var vlSaldo = $('#swal-edit-valor').val().replace('R$', '').trim()
                        .replace(/\./g, '').replace(',', '.');
                    var dtSaldo = document.getElementById('swal-edit-data').value;

                    if (!dsBanco || vlSaldo === '' || !dtSaldo) {
                        Swal.showValidationMessage('Preencha todos os campos.');
                        return false;
                    }

                    return $.ajax({
                        method: 'POST',
                        url: '{{ route('fluxo-caixa.atualizar-saldo-banco') }}',
                        data: {
                            _token: $('[name="csrf-token"]').attr('content'),
                            id: id,
                            ds_banco: dsBanco,
                            vl_saldo: vlSaldo,
                            dt_saldo: dtSaldo
                        }
                    }).catch(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Erro ao salvar o saldo.';
                        Swal.showValidationMessage(msg);
                        // Retornar aqui "recupera" a promise (resolve em vez de rejeitar) —
                        // senão o Swal fica preso no loading do botão em vez de voltar ao form.
                        return false;
                    });
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saldo atualizado com sucesso!',
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        window.location.reload();
                    });
                }
            });
        });

        // Excluir um lançamento de saldo (confirma, exclui via AJAX e recarrega).
        $(document).on('click', '.btn-excluir-saldo-banco', function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Excluir lançamento?',
                text: 'Essa ação não pode ser desfeita.',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'swal-confirm-fluxo',
                    cancelButton: 'swal-confirm-fluxo'
                }
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    method: 'POST',
                    url: '{{ route('fluxo-caixa.excluir-saldo-banco') }}',
                    data: {
                        _token: $('[name="csrf-token"]').attr('content'),
                        id: id
                    }
                }).done(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Excluído com sucesso!',
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        window.location.reload();
                    });
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                        'Erro ao excluir o lançamento.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg
                    });
                });
            });
        });
    </script>
@stop
