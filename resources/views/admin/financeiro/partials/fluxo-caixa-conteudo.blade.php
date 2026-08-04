{{-- Conteúdo pesado do Fluxo de Caixa (cards, tabela, gráficos), carregado por AJAX pelo shell
     (fluxo-caixa.blade.php) via a rota fluxo-caixa.conteudo. Os handlers/modais ficam no shell
     (delegados), aqui só vão os dados e a inicialização dos gráficos, que rodam a cada carga. --}}
@php
    // Previsto = fluxo operacional projetado pelas contas (Entradas − Saídas puras).
    // Realizado = variação real da posição de caixa (Saldo Final − Saldo Inicial).
    // Sem reconciliação no meio do período os dois são iguais; a diferença entre eles
    // é o quanto o caixa se moveu SEM passar pelas contas rastreadas (reconciliação).
    $totalEntradas = array_sum($totalEntradasPorDia);
    $totalSaidas = array_sum($totalSaidasPorDia);
    $saldoFinal = end($saldoDia);
    $fluxoPrevisto = $totalEntradas - $totalSaidas;
    $variacaoRealizada = $saldoFinal - $saldoInicial;
    $diferencaPrevRreal = $variacaoRealizada - $fluxoPrevisto;
@endphp

<div class="row mb-2">
    {{-- 1) Saldo Banco(s) Hoje --}}
    <div class="col-6 col-md-3 mb-2">
        <div class="stat-card stat-primary">
            <div class="stat-title">
                <span><i class="fas fa-wallet"></i> Saldo Banco(s) Hoje</span>
                @can('ver-fluxo-caixa-saldo-dia')
                    @if ($origemSaldoBanco !== 'firebird')
                        <span class="stat-title-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-listar-saldo-banco"
                                title="Ver lançamentos de saldo">
                                <i class="fas fa-list mr-1"></i>Ver
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="btn-add-saldo-banco"
                                title="Adicionar saldo de banco/financeira">
                                <i class="fas fa-plus-circle mr-1"></i>Adicionar
                            </button>
                        </span>
                    @endif
                @endcan
            </div>
            <div class="stat-value">R$ {{ number_format($saldoBancoHoje, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- 2) Total Entradas / Saídas --}}
    <div class="col-6 col-md-3 mb-2">
        <div class="stat-card stat-info">
            <div class="stat-title"><i class="fas fa-exchange-alt"></i> Total Entradas/Saídas</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Entradas</span>
                    <span class="stat-row-val text-success">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Saídas</span>
                    <span class="stat-row-val text-danger">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 3) Saldo Final da Semana --}}
    <div class="col-6 col-md-3 mb-2">
        <div class="stat-card {{ $saldoFinal >= 0 ? 'stat-success' : 'stat-danger' }}">
            <div class="stat-title"><i class="fas fa-flag-checkered"></i> Saldo Final da Semana</div>
            <div class="stat-value">R$ {{ number_format($saldoFinal, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- 4) Análise: previsto × realizado × diferença --}}
    <div class="col-6 col-md-3 mb-2">
        <div class="stat-card stat-purple">
            <div class="stat-title"><i class="fas fa-balance-scale"></i> Previsto × Realizado</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Fluxo Previsto
                        <i class="fas fa-info-circle"
                            title="Total Entradas − Total Saídas (fluxo projetado pelas contas a receber/pagar)"></i>
                    </span>
                    <span class="stat-row-val {{ $fluxoPrevisto >= 0 ? 'text-success' : 'text-danger' }}">R$ {{ number_format($fluxoPrevisto, 2, ',', '.') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Variação Realizada
                        <i class="fas fa-info-circle"
                            title="Saldo Final − Saldo Inicial (variação real da posição de caixa no período)"></i>
                    </span>
                    <span class="stat-row-val {{ $variacaoRealizada >= 0 ? 'text-success' : 'text-danger' }}">R$ {{ number_format($variacaoRealizada, 2, ',', '.') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Diferença (Real − Previsto)
                        <i class="fas fa-info-circle"
                            title="Movimento de caixa não explicado pelas contas (reconciliação/ajuste de saldo). Próximo de zero = fluxo bate com a posição de caixa."></i>
                    </span>
                    <span class="stat-row-val {{ abs($diferencaPrevRreal) < 0.01 ? 'text-success' : 'text-amber' }}">R$ {{ number_format($diferencaPrevRreal, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table mr-1 text-muted"></i> Fluxo de Caixa Semanal
        </h3>
        <span class="badge {{ $tipoData === 'personalizada' ? 'badge-primary' : 'badge-info' }} ml-1">
            {{ $tipoData === 'personalizada' ? 'Data Personalizada' : 'Data Real' }}
        </span>
        <div class="card-tools">
            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-toggle-dias"
                title="Ocultar dias da semana, exibindo só os totais">
                <i class="fas fa-eye-slash mr-1"></i>Ocultar Dias
            </button>
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
                                class="text-center col-dia col-dia-semana-{{ intdiv($i, 7) + 1 }} {{ $dia->isWeekend() ? 'dia-fim-semana' : '' }} {{ $dia->isToday() ? 'dia-atual' : '' }}">
                                {{ $dia->translatedFormat('D') }}<br>
                                <small>{{ $dia->format('d/m') }}</small>
                            </th>
                            @if (($i + 1) % 7 === 0 && count($dias) > 7)
                                <th class="text-center col-subtotal-semana">
                                    <button type="button" class="btn-toggle-semana"
                                        data-semana="{{ intdiv($i, 7) + 1 }}"
                                        title="Recolher/expandir semana">
                                        <i class="fas fa-minus"></i>
                                    </button><br>
                                    Total<br>
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
                    @foreach ($contasReceber as $cdTipoConta => $dadosTipoConta)
                        @php $slugTipoConta = \Illuminate\Support\Str::slug($dadosTipoConta['ds_tipoconta'] . '-' . $cdTipoConta); @endphp
                        <tr class="grupo-receber linha-detalhe linha-tipoconta"
                            data-slug="{{ $slugTipoConta }}" data-valores="{{ json_encode($dadosTipoConta['totais']) }}">
                            <td class="pl-4">
                                <button type="button" class="btn-detalhe-tipoconta" data-grupo="receber"
                                    data-slug="{{ $slugTipoConta }}" title="Ver categorias deste tipo de conta">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                {{ $dadosTipoConta['ds_tipoconta'] }}
                            </td>
                            <x-fluxo-celulas :valores="$dadosTipoConta['totais']" modo="detalhe"
                                :fins-de-semana="$finsDeSemana" ordenar-grupo="receber"
                                :ordenar-escopo="$slugTipoConta" />
                        </tr>
                        @foreach ($dadosTipoConta['categorias'] as $categoria => $dadosCategoria)
                            @php $slugCategoria = \Illuminate\Support\Str::slug($slugTipoConta . '-' . $categoria); @endphp
                            <tr class="grupo-receber linha-detalhe linha-categoria"
                                data-slug-pai-tipoconta="{{ $slugTipoConta }}"
                                data-valores="{{ json_encode($dadosCategoria['totais']) }}" style="display:none;">
                                <td class="pl-5">
                                    <button type="button" class="btn-detalhe-categoria" data-grupo="receber"
                                        data-slug="{{ $slugCategoria }}" title="Ver clientes deste total">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    {{ $categoria }}
                                </td>
                                <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true" tipo="receber"
                                    :categoria="$categoria" :cd-tipo-conta="$cdTipoConta" :fins-de-semana="$finsDeSemana" />
                            </tr>
                            @foreach ($dadosCategoria['detalhe'] as $cliente => $valores)
                                <tr class="grupo-receber linha-detalhe linha-cliente"
                                    data-slug-pai="{{ $slugCategoria }}" data-valores="{{ json_encode($valores) }}"
                                    style="display:none;">
                                    <td style="padding-left:4.5rem;">{{ $cliente }}</td>
                                    <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                        tipo="receber" :categoria="$categoria" :cliente="$cliente" :cd-tipo-conta="$cdTipoConta"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                    <tr class="grupo-receber linha-detalhe">
                        <td class="pl-4">Lançamento Manual (Entrada)
                            <i class="fas fa-pencil-alt text-muted btn-add-lancamento" style="font-size:.65rem; cursor:pointer;"
                                data-tipo="receber" title="Adicionar lançamento manual"></i>
                        </td>
                        <x-fluxo-celulas :valores="$lancamentoManualEntrada" modo="detalhe" :fins-de-semana="$finsDeSemana"
                            lanc-avulso-tipo="receber" />
                    </tr>

                    <tr class="linha-total linha-total-entrada">
                        <td>Total Entradas</td>
                        <x-fluxo-celulas :valores="$totalEntradasExibicaoPorDia" modo="total" subtotal-modo="traco"
                            total-modo="traco" :fins-de-semana="$finsDeSemana" />
                    </tr>

                    <tr class="linha-grupo linha-grupo-pagar">
                        <td><i class="fas fa-caret-down grupo-icone mr-1"></i> Contas a Pagar</td>
                        <x-fluxo-celulas :valores="$totalContasPagarPorDia" modo="total" classe-celula="valor-negativo"
                            :fins-de-semana="$finsDeSemana" />
                    </tr>
                    @foreach ($contasPagar as $cdTipoConta => $dadosTipoConta)
                        @php $slugTipoContaPagar = \Illuminate\Support\Str::slug($dadosTipoConta['ds_tipoconta'] . '-' . $cdTipoConta); @endphp
                        <tr class="grupo-pagar linha-detalhe linha-tipoconta"
                            data-slug="{{ $slugTipoContaPagar }}" data-valores="{{ json_encode($dadosTipoConta['totais']) }}">
                            <td class="pl-4">
                                <button type="button" class="btn-detalhe-tipoconta" data-grupo="pagar"
                                    data-slug="{{ $slugTipoContaPagar }}" title="Ver categorias deste tipo de conta">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                {{ $dadosTipoConta['ds_tipoconta'] }}
                            </td>
                            <x-fluxo-celulas :valores="$dadosTipoConta['totais']" modo="detalhe"
                                :fins-de-semana="$finsDeSemana" ordenar-grupo="pagar"
                                :ordenar-escopo="$slugTipoContaPagar" />
                        </tr>
                        @foreach ($dadosTipoConta['categorias'] as $categoria => $dadosCategoria)
                            @php $slugCategoriaPagar = \Illuminate\Support\Str::slug($slugTipoContaPagar . '-' . $categoria); @endphp
                            <tr class="grupo-pagar linha-detalhe linha-categoria"
                                data-slug-pai-tipoconta="{{ $slugTipoContaPagar }}"
                                data-valores="{{ json_encode($dadosCategoria['totais']) }}" style="display:none;">
                                <td class="pl-5">
                                    <button type="button" class="btn-detalhe-categoria" data-grupo="pagar"
                                        data-slug="{{ $slugCategoriaPagar }}"
                                        title="Ver fornecedores deste total">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    {{ $categoria }}
                                </td>
                                <x-fluxo-celulas :valores="$dadosCategoria['totais']" modo="detalhe" :clicavel="true"
                                    tipo="pagar" :categoria="$categoria" :cd-tipo-conta="$cdTipoConta" :fins-de-semana="$finsDeSemana" />
                            </tr>
                            @foreach ($dadosCategoria['detalhe'] as $fornecedor => $valores)
                                <tr class="grupo-pagar linha-detalhe linha-cliente"
                                    data-slug-pai="{{ $slugCategoriaPagar }}" data-valores="{{ json_encode($valores) }}"
                                    style="display:none;">
                                    <td style="padding-left:4.5rem;">{{ $fornecedor }}</td>
                                    <x-fluxo-celulas :valores="$valores" modo="detalhe" :clicavel="true"
                                        tipo="pagar" :categoria="$categoria" :cliente="$fornecedor" :cd-tipo-conta="$cdTipoConta"
                                        :fins-de-semana="$finsDeSemana" />
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                    <tr class="grupo-pagar linha-detalhe">
                        <td class="pl-4">Lançamento Manual (Saída)
                            <i class="fas fa-pencil-alt text-muted btn-add-lancamento" style="font-size:.65rem; cursor:pointer;"
                                data-tipo="pagar" title="Adicionar lançamento manual"></i>
                        </td>
                        <x-fluxo-celulas :valores="$lancamentoManualSaida" modo="detalhe" :fins-de-semana="$finsDeSemana"
                            lanc-avulso-tipo="pagar" />
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

<div class="card mt-2">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-bar mr-1 text-muted"></i> Entradas x Saídas por
            Dia</h3>
    </div>
    <div class="card-body" style="height:260px;">
        <canvas id="grafico-entradas-saidas"></canvas>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 col-md-6 mb-2">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-1 text-success"></i> Contas a Receber
                    por Tipo de Conta</h3>
            </div>
            <div class="card-body" style="height:280px;">
                <canvas id="grafico-pizza-receber"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 mb-2">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-1 text-danger"></i> Contas a Pagar por
                    Tipo de Conta</h3>
            </div>
            <div class="card-body" style="height:280px;">
                <canvas id="grafico-pizza-pagar"></canvas>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Dados usados pelos handlers/modais (definidos no shell) e pela inicialização dos gráficos.
    // Rodam a cada carga do conteúdo via AJAX; usam window.* pra ficarem globais mesmo sendo
    // injetados depois. fluxoOrigemSaldoBanco fica no shell (é barato e já vem do index()).
    window.fluxoContasReceber = @json($contasReceber);
    window.fluxoContasPagar = @json($contasPagar);
    window.fluxoSaldoBancoDetalhePorDia = @json($saldoBancoDetalhePorDia);
    window.fluxoLancAvulsoDetalhePorDia = {
        receber: @json($lancAvulsoDetalheEntradaPorDia),
        pagar: @json($lancAvulsoDetalheSaidaPorDia)
    };

    // Gráfico divergente: Entradas pra cima (verde), Saídas pra baixo do zero (vermelho negado)
    // — mesmas cores dos cards. pizzaStatic/barVertical (chart-helpers) destroem o chart anterior
    // do mesmo canvas antes de recriar, então é seguro rodar isto de novo a cada recarga.
    (function() {
        var labelsDias = @json(collect($dias)->map(fn($d) => $d->format('d/m'))->all());
        var totalEntradas = @json($totalEntradasExibicaoPorDia);
        var totalSaidas = @json($totalSaidasPorDia).map(function(v) {
            return -v;
        });

        barVertical('grafico-entradas-saidas', labelsDias, [{
                label: 'Entradas',
                data: totalEntradas,
                color: '#28a745'
            },
            {
                label: 'Saídas',
                data: totalSaidas,
                color: '#dc3545'
            }
        ]);

        var pizzaReceber = @json(
            collect($contasReceber)
                ->map(fn ($g) => ['nome' => $g['ds_tipoconta'], 'valor' => array_sum($g['totais'])])
                ->filter(fn ($x) => $x['valor'] > 0)
                ->values());
        var pizzaPagar = @json(
            collect($contasPagar)
                ->map(fn ($g) => ['nome' => $g['ds_tipoconta'], 'valor' => array_sum($g['totais'])])
                ->filter(fn ($x) => $x['valor'] > 0)
                ->values());

        pizzaStatic('grafico-pizza-receber', pizzaReceber);
        pizzaStatic('grafico-pizza-pagar', pizzaPagar);
    })();
</script>
