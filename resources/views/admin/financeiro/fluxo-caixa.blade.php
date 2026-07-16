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
                            @can('config-fluxo-caixa')
                                <button class="btn btn-sm btn-outline-secondary" id="btn-parametros-fluxo"
                                    title="Parâmetros"><i class="fas fa-cogs"></i></button>
                            @endcan
                            <button class="btn btn-sm btn-success" id="btn-novo-lancamento"><i
                                    class="fas fa-plus mr-1"></i>Lançamento Manual
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center mt-2">
                        <div class="col-12 text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <span class="btn btn-sm btn-light disabled" style="cursor:default;">Semanas
                                    Projetadas:</span>
                                @foreach ([1, 2, 4] as $opcaoSemanas)
                                    <a href="{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'ref' => $refSemanaAtual, 'semanas' => $opcaoSemanas]) }}"
                                        class="btn btn-sm {{ $qtdSemanas === $opcaoSemanas ? 'btn-primary active' : 'btn-outline-primary' }}">{{ $opcaoSemanas }}</a>
                                @endforeach
                            </div>
                            <div class="btn-group btn-group-sm ml-1" role="group">
                                <input type="number" min="1" max="12" id="input-semanas-custom"
                                    class="form-control form-control-sm text-center" style="width:60px;"
                                    placeholder="N"
                                    value="{{ in_array($qtdSemanas, [1, 2, 4]) ? '' : $qtdSemanas }}">
                                <button type="button"
                                    class="btn btn-sm {{ in_array($qtdSemanas, [1, 2, 4]) ? 'btn-outline-primary' : 'btn-primary' }}"
                                    id="btn-semanas-custom" title="Projetar N semanas">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
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
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;"
                                            title="Editável"></i>
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
                                        <i class="fas fa-pencil-alt text-muted" style="font-size:.65rem;"
                                            title="Editável"></i>
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

        .stat-title-actions .btn {
            font-size: .68rem;
            padding: .15rem .4rem;
            white-space: nowrap;
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

        .tabela-fluxo-caixa tbody .col-categoria,
        .tabela-fluxo-caixa tbody td:first-child {
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

        /* Botão "Ocultar Dias": esconde só as colunas de dia (th/td.col-dia), mantendo
               visíveis o subtotal semanal e o Total Geral. */
        .tabela-fluxo-caixa.oculta-dias .col-dia {
            display: none;
        }

        /* Botão +/- por semana (estilo agrupamento do Excel): recolhe só os dias daquela
               semana, mantendo visível a coluna "Sem N" com o total. Cobre até 12 semanas —
               mesmo limite máximo aplicado no controller (max(1, min(12, ...))). */
        .tabela-fluxo-caixa.oculta-semana-1 .col-dia-semana-1,
        .tabela-fluxo-caixa.oculta-semana-2 .col-dia-semana-2,
        .tabela-fluxo-caixa.oculta-semana-3 .col-dia-semana-3,
        .tabela-fluxo-caixa.oculta-semana-4 .col-dia-semana-4,
        .tabela-fluxo-caixa.oculta-semana-5 .col-dia-semana-5,
        .tabela-fluxo-caixa.oculta-semana-6 .col-dia-semana-6,
        .tabela-fluxo-caixa.oculta-semana-7 .col-dia-semana-7,
        .tabela-fluxo-caixa.oculta-semana-8 .col-dia-semana-8,
        .tabela-fluxo-caixa.oculta-semana-9 .col-dia-semana-9,
        .tabela-fluxo-caixa.oculta-semana-10 .col-dia-semana-10,
        .tabela-fluxo-caixa.oculta-semana-11 .col-dia-semana-11,
        .tabela-fluxo-caixa.oculta-semana-12 .col-dia-semana-12 {
            display: none;
        }

        .btn-toggle-semana {
            border: none;
            background: rgba(255, 255, 255, .15);
            color: inherit;
            width: 16px;
            height: 16px;
            line-height: 1;
            font-size: 9px;
            border-radius: 2px;
            padding: 0;
            margin-bottom: 2px;
            cursor: pointer;
        }

        .btn-toggle-semana:hover {
            background: rgba(255, 255, 255, .3);
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

        .btn-detalhe-categoria,
        .btn-detalhe-tipoconta {
            border: none;
            background: transparent;
            padding: 0 4px 0 0;
            color: #6c757d;
            cursor: pointer;
        }

        .btn-detalhe-categoria:hover,
        .btn-detalhe-tipoconta:hover {
            color: #444B53;
        }

        .linha-tipoconta td {
            font-weight: 600;
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

        .lanc-avulso-cel.tem-lancamento {
            cursor: pointer;
            text-decoration: underline dotted;
        }

        .lanc-avulso-cel.tem-lancamento:hover {
            background-color: #d7e6f5;
        }

        .ordenar-dia-cel {
            cursor: pointer;
        }

        .ordenar-dia-cel:hover {
            text-decoration: underline;
        }

        .icone-ordenacao {
            font-size: 9px;
            margin-left: 2px;
        }

        .swal-title-fluxo {
            font-size: 1.1rem !important;
        }

        /* Cabeçalho fixo dentro da área com rolagem (drill-down de lançamentos) — fica sempre
           visível mesmo com a tabela rolada pra baixo. O Bootstrap usa border-collapse:collapse
           por padrão, que quebra o position:sticky em th (o cabeçalho "sobe"/desalinha ao
           rolar) — por isso força separate aqui. */
        .tabela-thead-fixo {
            border-collapse: separate;
            border-spacing: 0;
        }

        .tabela-thead-fixo thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
        }

        .swal-confirm-fluxo {
            font-size: .8rem !important;
            padding: .4rem 1rem !important;
        }

        /* Deixa o select2 de Formas de Pagamento no mesmo padrão compacto dos demais campos
               (.form-control-sm) do formulário de parâmetros. Usa o id do select original (via
               combinador ~) além da classe, com !important, pra garantir que vença o CSS do
               vendor independente da ordem de carregamento. */
        #swal-param-formapagto~.select2-container .select2-selection--multiple,
        .select2-fluxo-sm.select2-container--bootstrap4 .select2-selection--multiple {
            min-height: 31px !important;
            font-size: 11px !important;
        }

        #swal-param-formapagto~.select2-container .select2-selection__choice,
        .select2-fluxo-sm .select2-selection__choice {
            font-size: 11px !important;
            padding-right: .5rem !important;
        }

        #swal-param-formapagto~.select2-container .select2-selection__rendered,
        .select2-fluxo-sm .select2-selection__rendered {
            font-size: 11px !important;
        }

        #swal-param-formapagto~.select2-container .select2-search__field,
        .select2-fluxo-sm .select2-search__field {
            font-size: 11px !important;
        }

        .select2-fluxo-sm.select2-container--bootstrap4 .select2-results__option {
            font-size: 12px !important;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/dashboard/swal-draggable.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/chart-helpers.js') }}?v={{ time() }}"></script>
    <script type="text/javascript">
        var fluxoContasReceber = @json($contasReceber);
        var fluxoContasPagar = @json($contasPagar);
        var fluxoSaldoBancoDetalhePorDia = @json($saldoBancoDetalhePorDia);
        var fluxoLancAvulsoDetalhePorDia = {
            receber: @json($lancAvulsoDetalheEntradaPorDia),
            pagar: @json($lancAvulsoDetalheSaidaPorDia)
        };
        var fluxoOrigemSaldoBanco = @json($origemSaldoBanco);

        // Gráfico divergente: Entradas pra cima (verde), Saídas pra baixo do zero (vermelho,
        // valores negados) — mesmas cores dos cards "Total Entradas"/"Total Saídas" acima,
        // pra manter a mesma linguagem visual da página. Usa o helper já existente
        // (barVertical) em vez de configurar o Chart.js na mão.
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
        })();

        // Abre um modal com os lançamentos (Nº Lanc, Nr Docto, Nr Parc., Data Real,
        // Cliente/Fornecedor e Valor) que compõem o total clicado — na linha de categoria
        // mostra todas as pessoas daquele dia, na linha de pessoa mostra só os lançamentos dela.
        $(document).on('click', '.valor-clicavel', function() {
            var tipo = $(this).data('tipo') || 'receber';
            var categoria = $(this).data('categoria');
            var cliente = $(this).data('cliente');
            var cdTipoConta = $(this).data('tipoconta');
            var dia = $(this).data('dia');

            var fonte = tipo === 'pagar' ? fluxoContasPagar : fluxoContasReceber;
            var grupoTipoConta = fonte[cdTipoConta];
            var grupo = grupoTipoConta ? grupoTipoConta.categorias[categoria] : null;
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

            abrirListaLancamentosContas(categoria, tipo, itens);
        });

        // Lista de lançamentos do drill-down: busca por cliente/valor, ordenação por Valor e
        // total sempre visível (fora da área com rolagem — com muitos itens não precisa rolar
        // até o fim pra saber o total).
        function abrirListaLancamentosContas(categoria, tipo, itensOriginais) {
            var rotuloPessoa = tipo === 'pagar' ? 'Fornecedor' : 'Cliente';
            var ordemValor = null; // null (original), 'asc' ou 'desc'
            var filtroTexto = '';

            function formatarValor(valor) {
                return valor.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2
                });
            }

            function itensExibidos() {
                var lista = itensOriginais.slice();

                if (filtroTexto) {
                    var termo = filtroTexto.toLowerCase();
                    lista = lista.filter(function(item) {
                        return item.nm_pessoa.toLowerCase().indexOf(termo) !== -1 ||
                            formatarValor(item.valor).indexOf(termo) !== -1;
                    });
                }

                if (ordemValor) {
                    lista.sort(function(a, b) {
                        return ordemValor === 'asc' ? (a.valor - b.valor) : (b.valor - a.valor);
                    });
                }

                return lista;
            }

            function atualizarTabela() {
                var lista = itensExibidos();
                var total = lista.reduce(function(acc, item) {
                    return acc + item.valor;
                }, 0);

                var linhas = lista.map(function(item) {
                    return '<tr>' +
                        '<td>' + item.nr_lancamento + '</td>' +
                        '<td>' + (item.nr_documento || '-') + '</td>' +
                        '<td class="text-center">' + (item.nr_parcela || '-') + '</td>' +
                        '<td style="width:85px; white-space:nowrap;">' + item.dt_real + '</td>' +
                        '<td>' + item.nm_pessoa + '</td>' +
                        '<td class="text-right" style="width:100px; white-space:nowrap;">' +
                        formatarValor(item.valor) + '</td>' +
                        '</tr>';
                }).join('');

                $('#swal-lanc-contas-tbody').html(linhas);
                $('#swal-lanc-contas-total').text('R$ ' + formatarValor(total) + ' (' + lista.length +
                    ' lançamento' + (lista.length === 1 ? '' : 's') + ')');
            }

            Swal.fire({
                title: categoria,
                width: 700,
                confirmButtonText: 'Fechar',
                customClass: {
                    title: 'swal-title-fluxo',
                    confirmButton: 'swal-confirm-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="d-flex justify-content-between align-items-center mb-2">' +
                    '<input type="text" id="swal-lanc-contas-filtro" class="form-control form-control-sm"' +
                    ' style="max-width:220px;" placeholder="Buscar cliente ou valor...">' +
                    '<strong id="swal-lanc-contas-total" style="font-size:12px;"></strong>' +
                    '</div>' +
                    '<div style="max-height:320px; overflow-y:auto;">' +
                    '<table class="table table-sm table-striped tabela-thead-fixo" style="font-size:12px;">' +
                    '<thead><tr style="white-space:nowrap;">' +
                    '<th>Nº Lanc</th><th>Docto</th><th class="text-center">Parc.</th>' +
                    '<th style="width:85px;">Data Real</th><th>' + rotuloPessoa + '</th>' +
                    '<th class="text-right" id="swal-lanc-contas-th-valor" style="cursor:pointer;" title="Ordenar por valor">Valor <i class="fas fa-sort"></i></th>' +
                    '</tr></thead>' +
                    '<tbody id="swal-lanc-contas-tbody"></tbody>' +
                    '</table>' +
                    '</div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    atualizarTabela();

                    $('#swal-lanc-contas-filtro').on('input', function() {
                        filtroTexto = $(this).val().trim();
                        atualizarTabela();
                    });

                    $('#swal-lanc-contas-th-valor').on('click', function() {
                        ordemValor = ordemValor === 'desc' ? 'asc' : 'desc';
                        $(this).find('i').attr('class', 'fas fa-sort-amount-' + (ordemValor ===
                            'desc' ? 'down' : 'up'));
                        atualizarTabela();
                    });
                }
            });
        }

        // Colapsa/expande os grupos Contas a Receber / Contas a Pagar — só a linha de Tipo de
        // Conta fica visível de novo; categorias e clientes voltam sempre fechados.
        $('.linha-grupo-receber').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.linha-tipoconta.grupo-receber').toggle();
            $('.linha-categoria.grupo-receber, .linha-cliente.grupo-receber').hide();
            $('.btn-detalhe-tipoconta[data-grupo="receber"] i, .btn-detalhe-categoria[data-grupo="receber"] i')
                .removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        $('.linha-grupo-pagar').on('click', function() {
            $(this).toggleClass('collapsed');
            $('.linha-tipoconta.grupo-pagar').toggle();
            $('.linha-categoria.grupo-pagar, .linha-cliente.grupo-pagar').hide();
            $('.btn-detalhe-tipoconta[data-grupo="pagar"] i, .btn-detalhe-categoria[data-grupo="pagar"] i')
                .removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        // Detalha (mostra/oculta) as categorias (formas de pagamento) que compõem o total de um
        // Tipo de Conta. Sempre fecha o detalhamento por cliente/fornecedor mais fundo, senão
        // um cliente pode ficar "aberto" escondido quando o tipo de conta é recolhido de novo.
        $(document).on('click', '.btn-detalhe-tipoconta', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var $icone = $btn.find('i');
            var grupo = $btn.data('grupo');
            var slug = $btn.data('slug');

            $btn.prop('disabled', true);
            $icone.removeClass('fa-chevron-right fa-chevron-down').addClass('fa-spinner fa-spin');

            setTimeout(function() {
                var $categorias = $('.linha-categoria.grupo-' + grupo + '[data-slug-pai-tipoconta="' +
                    slug + '"]');
                $categorias.toggle();

                $categorias.find('.btn-detalhe-categoria i').removeClass('fa-chevron-down').addClass(
                    'fa-chevron-right');
                $categorias.each(function() {
                    var slugCategoria = $(this).find('.btn-detalhe-categoria').data('slug');
                    $('.linha-cliente.grupo-' + grupo + '[data-slug-pai="' + slugCategoria + '"]').hide();
                });

                var expandido = $categorias.is(':visible');
                $icone.removeClass('fa-spinner fa-spin')
                    .addClass(expandido ? 'fa-chevron-down' : 'fa-chevron-right');
                $btn.prop('disabled', false);
            }, 50);
        });

        // Detalha (mostra/oculta) os clientes/fornecedores que compõem o total de uma categoria.
        // Com muita informação essa expansão trava a thread por 2-3s — mostra um loader antes
        // de fazer o toggle (via setTimeout) pra dar tempo do spinner aparecer na tela.
        $(document).on('click', '.btn-detalhe-categoria', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var $icone = $btn.find('i');
            var grupo = $btn.data('grupo');
            var slug = $btn.data('slug');

            $btn.prop('disabled', true);
            $icone.removeClass('fa-chevron-right fa-chevron-down').addClass('fa-spinner fa-spin');

            setTimeout(function() {
                var $linhas = $('.grupo-' + grupo + '.linha-cliente[data-slug-pai="' + slug + '"]');
                $linhas.toggle();

                var expandido = $linhas.is(':visible');
                $icone.removeClass('fa-spinner fa-spin')
                    .addClass(expandido ? 'fa-chevron-down' : 'fa-chevron-right');
                $btn.prop('disabled', false);
            }, 50);
        });

        // Ordenação por dia específico: clique numa célula de dia da linha de um Tipo de Conta
        // ordena as categorias (e os clientes dentro de cada uma) DAQUELE tipo de conta por
        // aquele dia, maior pro menor — clicar de novo no mesmo dia inverte a ordem. Cada tipo
        // de conta ordena só as suas próprias categorias/clientes (escopo = slug do tipoconta).
        var ordenacaoAtualPorEscopo = {};

        function ordenarCategoriasPorDia(grupo, escopo, dia, direcao) {
            var $linhaTipoConta = $('.linha-tipoconta.grupo-' + grupo + '[data-slug="' + escopo + '"]');

            var itens = $('tr.linha-categoria.grupo-' + grupo + '[data-slug-pai-tipoconta="' + escopo +
                '"]').get().map(function(tr) {
                var $tr = $(tr);
                var slug = $tr.find('.btn-detalhe-categoria').data('slug');
                var valores = $tr.data('valores') || [];

                var clientes = $('tr.linha-cliente.grupo-' + grupo + '[data-slug-pai="' + slug +
                        '"]').get()
                    .map(function(trCliente) {
                        var $trCliente = $(trCliente);
                        var valoresCliente = $trCliente.data('valores') || [];
                        return {
                            el: $trCliente,
                            valor: parseFloat(valoresCliente[dia]) || 0
                        };
                    })
                    .sort(function(a, b) {
                        return direcao === 'asc' ? (a.valor - b.valor) : (b.valor - a.valor);
                    });

                return {
                    el: $tr,
                    valor: parseFloat(valores[dia]) || 0,
                    clientes: clientes
                };
            }).sort(function(a, b) {
                return direcao === 'asc' ? (a.valor - b.valor) : (b.valor - a.valor);
            });

            var $ultimo = $linhaTipoConta;
            itens.forEach(function(item) {
                item.el.insertAfter($ultimo);
                $ultimo = item.el;

                item.clientes.forEach(function(cliente) {
                    cliente.el.insertAfter($ultimo);
                    $ultimo = cliente.el;
                });
            });
        }

        // Ligado direto no elemento (não delegado via document) — precisa disparar antes do
        // clique borbulhar pra .linha-grupo-receber/pagar, senão o stopPropagation() chega
        // tarde demais e a linha recolhe/expande junto com a ordenação.
        $('.ordenar-dia-cel').on('click', function(e) {
            e.stopPropagation();

            var $cel = $(this);
            var grupo = $cel.data('ordenarGrupo');
            var escopo = $cel.data('ordenarEscopo');
            var dia = $cel.data('dia');
            var chave = grupo + '|' + escopo;
            var atual = ordenacaoAtualPorEscopo[chave];
            var direcao = (atual && atual.dia === dia && atual.direcao === 'desc') ? 'asc' :
                'desc';

            ordenacaoAtualPorEscopo[chave] = {
                dia: dia,
                direcao: direcao
            };

            $('.linha-tipoconta.grupo-' + grupo + '[data-slug="' + escopo + '"] .icone-ordenacao')
                .remove();
            $cel.append(' <i class="fas fa-sort-amount-' + (direcao === 'desc' ? 'down' : 'up') +
                ' icone-ordenacao"></i>');

            ordenarCategoriasPorDia(grupo, escopo, dia, direcao);
        });

        // Botão global: em vez de ter sua própria classe/estado, "clica" em cada botão +/-
        // de semana, reaproveitando o mesmo mecanismo (fica tudo sincronizado, sem dois
        // estados independentes). Se não tem semana pra recolher (visão de 1 semana só, sem
        // coluna "Sem N"), cai no fallback direto via .oculta-dias.
        $('#btn-toggle-dias').on('click', function() {
            var $botoesSemana = $('.btn-toggle-semana');

            if (!$botoesSemana.length) {
                var oculto = $('.tabela-fluxo-caixa').toggleClass('oculta-dias').hasClass(
                    'oculta-dias');

                $(this).html(oculto ?
                    '<i class="fas fa-eye mr-1"></i>Mostrar Dias' :
                    '<i class="fas fa-eye-slash mr-1"></i>Ocultar Dias');
                return;
            }

            var algumaExpandida = $botoesSemana.filter(function() {
                return $(this).find('i').hasClass('fa-minus');
            }).length > 0;

            $botoesSemana.each(function() {
                var estaExpandida = $(this).find('i').hasClass('fa-minus');

                if (estaExpandida === algumaExpandida) {
                    $(this).trigger('click');
                }
            });

            $(this).html(algumaExpandida ?
                '<i class="fas fa-eye mr-1"></i>Mostrar Dias' :
                '<i class="fas fa-eye-slash mr-1"></i>Ocultar Dias');
        });

        // Botão +/- de cada coluna "Sem N" (estilo agrupamento do Excel): recolhe/expande só
        // os dias daquela semana.
        $(document).on('click', '.btn-toggle-semana', function(e) {
            e.stopPropagation();
            var semana = $(this).data('semana');
            var classe = 'oculta-semana-' + semana;
            var oculto = $('.tabela-fluxo-caixa').toggleClass(classe).hasClass(classe);
            var $icone = $(this).find('i');

            $icone.toggleClass('fa-minus', !oculto).toggleClass('fa-plus', oculto);
            $(this).attr('title', oculto ? 'Expandir semana' : 'Recolher semana');
        });

        // Semanas Projetadas customizado: reaproveita a mesma rota dos atalhos (1/2/4), só
        // trocando o parâmetro "semanas" pelo valor digitado.
        function irParaSemanasCustomizadas() {
            var valor = parseInt($('#input-semanas-custom').val(), 10);

            if (!valor || valor < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informe um número de semanas válido.'
                });
                return;
            }

            window.location.href =
                '{{ route('fluxo-caixa.index', ['tipo_data' => $tipoData, 'ref' => $refSemanaAtual]) }}&semanas=' +
                valor;
        }

        $('#btn-semanas-custom').on('click', irParaSemanasCustomizadas);
        $('#input-semanas-custom').on('keypress', function(e) {
            if (e.key === 'Enter') {
                irParaSemanasCustomizadas();
            }
        });

        // Modal de Lançamento Manual (fluxo_caixa_lanc_avulso) — funciona tanto pra entrada
        // (Contas a Receber) quanto saída (Contas a Pagar), a depender do "Tipo" escolhido.
        // Reaproveitada tanto pro botão "Lançamento Manual" (adicionar) quanto pro editar via
        // hover na linha da grade (dadosExistentes vem preenchido nesse caso).
        function abrirFormularioLancamento(dadosExistentes, aoFechar) {
            var editando = !!(dadosExistentes && dadosExistentes.id);
            var tipoInicial = editando ? dadosExistentes.tipo : 'receber';

            Swal.fire({
                title: editando ? 'Editar Lançamento Manual' : 'Lançamento Manual',
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
                    '<label class="mb-1" style="font-size:12px;">Tipo <span class="text-danger">*</span></label>' +
                    '<select id="swal-lanc-tipo" class="form-control form-control-sm">' +
                    '<option value="receber"' + (tipoInicial === 'receber' ? ' selected' : '') +
                    '>Entrada (Contas a Receber)</option>' +
                    '<option value="pagar"' + (tipoInicial === 'pagar' ? ' selected' : '') +
                    '>Saída (Contas a Pagar)</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Data Lançamento <span class="text-danger">*</span></label>' +
                    '<input type="date" id="swal-lanc-data" class="form-control form-control-sm" value="' +
                    (editando ? dadosExistentes.dt_lancamento : '{{ now()->format('Y-m-d') }}') +
                    '">' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Pessoa (opcional)</label>' +
                    '<select id="swal-lanc-cd-pessoa" class="w-100"></select>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Tipo Conta (opcional)</label>' +
                    '<select id="swal-lanc-cd-tipoconta" class="w-100"></select>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Forma de Pagamento (opcional)</label>' +
                    '<select id="swal-lanc-cd-formapagto" class="w-100"></select>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Valor do Documento <span class="text-danger">*</span></label>' +
                    '<input type="text" inputmode="decimal" id="swal-lanc-valor" class="form-control form-control-sm" placeholder="R$ 0,00" value="' +
                    (editando ? 'R$ ' + Number(dadosExistentes.vl_documento).toFixed(2).replace(
                        '.', ',') : '') + '">' +
                    '</div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Observação <span class="text-danger">*</span></label>' +
                    '<textarea id="swal-lanc-observacao" class="form-control form-control-sm" rows="2" maxlength="255">' +
                    (editando ? (dadosExistentes.ds_observacao || '') : '') + '</textarea>' +
                    '</div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    carregarOpcoesTipoConta(tipoInicial, editando ? dadosExistentes.cd_tipoconta :
                        null, '#swal-lanc-cd-tipoconta', true);
                    carregarOpcoesFormaPagamento(editando ? dadosExistentes.cd_formapagto : null,
                        '#swal-lanc-cd-formapagto', true);

                    if (editando && dadosExistentes.cd_pessoa) {
                        $('#swal-lanc-cd-pessoa').append('<option value="' + dadosExistentes.cd_pessoa +
                            '" selected>' + dadosExistentes.cd_pessoa + '-' + dadosExistentes.nm_pessoa +
                            '</option>');
                    }

                    $('#swal-lanc-cd-pessoa').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Buscar pessoa...',
                        allowClear: true,
                        minimumInputLength: 2,
                        language: {
                            inputTooShort: function() {
                                return 'Digite ao menos 2 caracteres...';
                            },
                            searching: function() {
                                return 'Buscando...';
                            },
                            noResults: function() {
                                return 'Nenhuma pessoa encontrada';
                            }
                        },
                        dropdownParent: $(Swal.getPopup()),
                        containerCssClass: 'select2-fluxo-sm',
                        dropdownCssClass: 'select2-fluxo-sm',
                        ajax: {
                            url: '{{ route('pessoa.search') }}',
                            dataType: 'json',
                            delay: 300,
                            data: function(params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function(dados) {
                                return {
                                    results: dados
                                };
                            }
                        }
                    });

                    $('#swal-lanc-cd-tipoconta').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Selecione (opcional)',
                        allowClear: true,
                        dropdownParent: $(Swal.getPopup()),
                        containerCssClass: 'select2-fluxo-sm',
                        dropdownCssClass: 'select2-fluxo-sm'
                    });

                    $('#swal-lanc-cd-formapagto').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Selecione (opcional)',
                        allowClear: true,
                        dropdownParent: $(Swal.getPopup()),
                        containerCssClass: 'select2-fluxo-sm',
                        dropdownCssClass: 'select2-fluxo-sm'
                    });

                    $('#swal-lanc-valor').inputmask({
                        mask: ['R$ 9,99', 'R$ 99,99', 'R$ 999,99', 'R$ 9.999,99',
                            'R$ 99.999,99',
                            'R$ 999.999,99', 'R$ 9.999.999,99'
                        ],
                        radixPoint: ','
                    });

                    $('#swal-lanc-tipo').on('change', function() {
                        carregarOpcoesTipoConta($(this).val(), null, '#swal-lanc-cd-tipoconta',
                            true);
                    });
                },
                preConfirm: function() {
                    var tipo = document.getElementById('swal-lanc-tipo').value;
                    var dtLancamento = document.getElementById('swal-lanc-data').value;
                    var vlDocumento = $('#swal-lanc-valor').val().replace('R$', '').trim()
                        .replace(/\./g, '').replace(',', '.');

                    var observacao = $('#swal-lanc-observacao').val().trim();

                    var $pessoaSelecionada = $('#swal-lanc-cd-pessoa option:selected');
                    var cdPessoa = $pessoaSelecionada.val() || null;
                    var nmPessoa = cdPessoa ? $pessoaSelecionada.text().replace(cdPessoa + '-',
                        '') : null;

                    var $tipoContaSelecionada = $('#swal-lanc-cd-tipoconta option:selected');
                    var cdTipoConta = $tipoContaSelecionada.val() || null;
                    var dsTipoConta = cdTipoConta ? $tipoContaSelecionada.text().replace(
                        cdTipoConta + ' - ', '') : null;

                    var $formaPagtoSelecionada = $('#swal-lanc-cd-formapagto option:selected');
                    var cdFormaPagto = $formaPagtoSelecionada.val() || null;
                    var dsFormaPagto = cdFormaPagto ? $formaPagtoSelecionada.text().replace(
                        cdFormaPagto + ' - ', '') : null;

                    if (!dtLancamento || vlDocumento === '' || !observacao) {
                        Swal.showValidationMessage(
                            'Preencha a data, o valor do documento e a observação.');
                        return false;
                    }

                    var dadosEnvio = {
                        _token: $('[name="csrf-token"]').attr('content'),
                        tipo: tipo,
                        dt_lancamento: dtLancamento,
                        cd_pessoa: cdPessoa,
                        nm_pessoa: nmPessoa,
                        vl_documento: vlDocumento,
                        cd_tipoconta: cdTipoConta,
                        ds_tipoconta: dsTipoConta,
                        cd_formapagto: cdFormaPagto,
                        ds_formapagto: dsFormaPagto,
                        ds_observacao: observacao
                    };

                    if (editando) {
                        dadosEnvio.id = dadosExistentes.id;
                    }

                    return $.ajax({
                        method: 'POST',
                        url: editando ?
                            '{{ route('fluxo-caixa.atualizar-lancamento') }}' :
                            '{{ route('fluxo-caixa.salvar-lancamento') }}',
                        data: dadosEnvio
                    }).catch(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Erro ao salvar o lançamento.';
                        Swal.showValidationMessage(msg);
                        return false;
                    });
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.reload();
                    return;
                }

                // Cancelou/fechou (X/ESC) — se veio da lista do dia (aoFechar informado),
                // volta pra ela em vez de deixar o usuário sem tela nenhuma.
                if (aoFechar) {
                    aoFechar();
                }
            });
        }

        $('#btn-novo-lancamento').on('click', function() {
            abrirFormularioLancamento();
        });

        // Clique na linha "Lançamento Manual" (.lanc-avulso-cel.tem-lancamento): abre um Swal
        // com os lançamentos daquele dia e botões de editar/excluir — mesmo padrão de clique já
        // usado nas outras linhas da grade (Contas a Receber/Pagar, Saldo Banco).
        function abrirListaLancAvulsoDoDia(tipo, dia) {
            var itens = (fluxoLancAvulsoDetalhePorDia[tipo] || {})[dia] || [];

            if (!itens.length) {
                return;
            }

            var linhas = itens.map(function(item) {
                var detalhes = [item.ds_tipoconta, item.ds_formapagto, item.ds_observacao]
                    .filter(function(v) {
                        return !!v;
                    })
                    .join(' · ');

                return '<tr>' +
                    '<td>' + (item.nm_pessoa || '-') +
                    (detalhes ? '<br><small class="text-muted">' + detalhes + '</small>' : '') +
                    '</td>' +
                    '<td class="text-right">R$ ' + Number(item.vl_documento).toFixed(2).replace(
                        '.', ',') + '</td>' +
                    '<td class="text-center text-nowrap">' +
                    '<button type="button" class="btn-xs btn-outline-primary btn-lanc-avulso-editar" data-item=\'' +
                    JSON.stringify(item) +
                    '\' title="Editar"><i class="fas fa-pencil-alt"></i></button> ' +
                    '<button type="button" class="btn-xs btn-outline-danger btn-lanc-avulso-excluir" data-id="' +
                    item.id + '" title="Excluir"><i class="fas fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');

            Swal.fire({
                title: 'Lançamentos Manuais do Dia',
                width: 500,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    title: 'swal-title-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<table class="table table-sm table-striped" style="font-size:12px;">' +
                    '<tbody>' + linhas + '</tbody>' +
                    '</table>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();

                    $(Swal.getPopup()).on('click', '.btn-lanc-avulso-editar', function() {
                        var item = $(this).data('item');
                        abrirFormularioLancamento(item, function() {
                            abrirListaLancAvulsoDoDia(tipo, dia);
                        });
                    });

                    $(Swal.getPopup()).on('click', '.btn-lanc-avulso-excluir', function() {
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
                                abrirListaLancAvulsoDoDia(tipo, dia);
                                return;
                            }

                            $.ajax({
                                method: 'POST',
                                url: '{{ route('fluxo-caixa.excluir-lancamento') }}',
                                data: {
                                    _token: $('[name="csrf-token"]').attr('content'),
                                    id: id
                                }
                            }).done(function() {
                                window.location.reload();
                            }).fail(function(xhr) {
                                var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                                    'Erro ao excluir o lançamento.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: msg
                                }).then(function() {
                                    abrirListaLancAvulsoDoDia(tipo, dia);
                                });
                            });
                        });
                    });
                }
            });
        }

        $(document).on('click', '.lanc-avulso-cel.tem-lancamento', function() {
            abrirListaLancAvulsoDoDia($(this).data('tipo'), $(this).data('dia'));
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
                    '<label class="mb-1" style="font-size:12px;">Data Lançamento <span class="text-danger">*</span></label>' +
                    '<input type="date" id="swal-data-lancamento" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Nome Banco/Financeira <span class="text-danger">*</span></label>' +
                    '<input type="text" id="swal-nome-banco" class="form-control form-control-sm" placeholder="Ex: Banco do Brasil">' +
                    '</div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Saldo Banco <span class="text-danger">*</span></label>' +
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

        // Monta as linhas usadas tanto no drill-down por dia quanto na busca por período.
        // `editavel` controla se mostra os botões de editar/excluir — a busca por período
        // (buscarSaldoBanco) é sempre sobre fluxo_caixa_saldo de verdade, então sempre editável;
        // já o drill-down por dia mostra o que estiver configurado como origem do Saldo Banco
        // (digitado ou Firebird/SALDOCAIXA), e registros do Firebird não são editáveis/excluíveis
        // por aqui — mostrar os botões nesse caso confundiria o usuário (o clique falharia).
        function montarLinhasSaldoBanco(itens, editavel) {
            return itens.map(function(item) {
                var acoes = editavel ?
                    ('<button type="button" class="btn btn-xs btn-outline-primary btn-editar-saldo-banco" data-id="' +
                        item.id + '" data-banco="' + item.ds_banco + '" data-valor="' + item.vl_saldo +
                        '" data-data="' + item.dt_saldo +
                        '" title="Editar"><i class="fas fa-pencil-alt"></i></button> ' +
                        '<button type="button" class="btn btn-xs btn-outline-danger btn-excluir-saldo-banco" data-id="' +
                        item.id + '" title="Excluir"><i class="fas fa-trash"></i></button>') :
                    '<i class="fas fa-lock text-muted" title="Saldo vindo do Firebird — somente leitura"></i>';

                return '<tr>' +
                    '<td>' + item.ds_banco + '</td>' +
                    '<td>' + item.dt_saldo_formatada + '</td>' +
                    '<td class="text-right">' + item.vl_saldo.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }) + '</td>' +
                    '<td class="text-center">' + acoes + '</td>' +
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
                    '<tbody>' + montarLinhasSaldoBanco(itens, fluxoOrigemSaldoBanco !== 'firebird') + '</tbody>' +
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
                    '<tbody>' + montarLinhasSaldoBanco(response.dados, true) + '</tbody>' +
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
                    '<label class="mb-1" style="font-size:12px;">Data Lançamento <span class="text-danger">*</span></label>' +
                    '<input type="date" id="swal-edit-data" class="form-control form-control-sm" value="' +
                    data +
                    '"></div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Nome Banco/Financeira <span class="text-danger">*</span></label>' +
                    '<input type="text" id="swal-edit-banco" class="form-control form-control-sm" value="' +
                    banco +
                    '"></div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Saldo Banco <span class="text-danger">*</span></label>' +
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

        // Rastreia se alguma alteração real (parâmetro ou compensação) foi salva durante a
        // navegação dentro do modal de Parâmetros, pra só forçar o reload da página quando
        // fizer sentido — o fluxo de caixa exibido atrás é todo calculado no servidor a partir
        // dessas tabelas, então sem reload a tela fica com dado velho mesmo após salvar.
        var fluxoParametrosAlterado = false;

        // Mesma lógica de supressão usada na Compensação: como o SweetAlert2 só mostra 1 popup
        // por vez, abrir qualquer modal por cima (Adicionar/Editar/Excluir/Compensação) também
        // resolve a promise do modal de Parâmetros — por isso o reload só é avaliado no
        // fechamento de verdade (X/ESC/fora), nunca nas reaberturas internas.
        var suprimirRecargaParametros = false;

        function avisarRecargaFluxo() {
            Swal.fire({
                icon: 'info',
                title: 'Página será recarregada',
                text: 'Foram detectadas alterações nos parâmetros/compensação do fluxo de caixa — a página vai recarregar para refletir os novos valores.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal-confirm-fluxo'
                }
            }).then(function() {
                window.location.reload();
            });
        }

        // Botão "Parâmetros": lista, adiciona, edita e exclui os CD_TIPOCONTA/forma de
        // pagamento considerados no Fluxo de Caixa (tabela fluxo_caixa_parametros). Ainda não
        // busca nada do Firebird — os campos são preenchidos manualmente por enquanto.
        $('#btn-parametros-fluxo').on('click', function() {
            fluxoParametrosAlterado = false;
            abrirParametrosFluxo();
        });

        // Placeholder: configuração de origem do Saldo Banco (digitado x direto do Firebird,
        // via SALDOCAIXA) e das contas (CD_CONTA) consideradas — será implementada nas
        // próximas etapas do projeto.
        function abrirConfigSaldoCaixaFirebird() {
            Swal.fire({
                icon: 'info',
                title: 'Saldo Caixa (Junsoft)',
                text: 'Tela de configuração da origem do saldo bancário (digitado ou direto do Firebird) e das contas consideradas será implementada nas próximas etapas do projeto.',
                confirmButtonText: 'Ok'
            }).then(function() {
                abrirParametrosFluxo();
            });
        }

        function abrirParametrosFluxo() {
            suprimirRecargaParametros = false;

            Swal.fire({
                title: 'Parâmetros do Fluxo de Caixa',
                width: 700,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    title: 'swal-title-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="custom-control custom-switch mb-2">' +
                    '<input type="checkbox" class="custom-control-input" id="swal-toggle-origem-saldo"' +
                    (fluxoOrigemSaldoBanco === 'firebird' ? ' checked' : '') + '>' +
                    '<label class="custom-control-label" for="swal-toggle-origem-saldo" style="font-size:12px;">Saldo Banco via Junsoft</label>' +
                    '</div>' +
                    '<div class="text-right mb-2">' +
                    '<button type="button" id="swal-btn-compensacao" class="btn btn-sm btn-outline-primary mr-1">' +
                    '<i class="fas fa-clock mr-1"></i>Compensação Bancária</button>' +
                    '<button type="button" id="swal-btn-saldo-caixa-firebird" class="btn btn-sm btn-outline-primary mr-1">' +
                    '<i class="fas fa-university mr-1"></i>Contas Saldo (Junsoft)</button>' +
                    '<button type="button" id="swal-btn-add-parametro" class="btn btn-sm btn-outline-primary mr-1">' +
                    '<i class="fas fa-plus mr-1"></i>Add Tipo Contas</button>' +
                    '</div>' +
                    '<div id="swal-resultado-parametros" style="max-height:360px; overflow-y:auto;"></div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    document.getElementById('swal-btn-add-parametro').addEventListener('click', function() {
                        suprimirRecargaParametros = true;
                        abrirFormularioParametro();
                    });
                    document.getElementById('swal-btn-compensacao').addEventListener('click', function() {
                        suprimirRecargaParametros = true;
                        abrirCompensacaoFluxo();
                    });
                    document.getElementById('swal-btn-saldo-caixa-firebird').addEventListener('click',
                        function() {
                            suprimirRecargaParametros = true;
                            abrirConfigSaldoCaixaFirebird();
                        });
                    document.getElementById('swal-toggle-origem-saldo').addEventListener('change',
                        function() {
                            var $toggle = $(this);
                            var novaOrigem = this.checked ? 'firebird' : 'digitado';

                            $.ajax({
                                method: 'POST',
                                url: '{{ route('fluxo-caixa.salvar-origem-saldo-banco') }}',
                                data: {
                                    _token: $('[name="csrf-token"]').attr('content'),
                                    origem_saldo_banco: novaOrigem
                                }
                            }).done(function() {
                                fluxoOrigemSaldoBanco = novaOrigem;
                                fluxoParametrosAlterado = true;

                                // Um Swal.fire aqui substituiria o modal de Parâmetros que já
                                // está aberto (só 1 popup por vez) — em vez disso, um feedback
                                // visual rápido ao lado do próprio toggle.
                                var $label = $toggle.siblings('.custom-control-label');
                                $label.find('.icone-salvo').remove();
                                $label.append(
                                    ' <i class="fas fa-check text-success icone-salvo"></i>');
                                setTimeout(function() {
                                    $label.find('.icone-salvo').fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }, 1500);
                            }).fail(function(xhr) {
                                $toggle.prop('checked', !$toggle.prop('checked'));
                                var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                                    'Erro ao salvar a configuração.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: msg
                                });
                            });
                        });
                    buscarParametros();
                }
            }).then(function() {
                if (suprimirRecargaParametros) {
                    return;
                }

                if (fluxoParametrosAlterado) {
                    avisarRecargaFluxo();
                }
            });
        }

        function buscarParametros() {
            var $resultado = $('#swal-resultado-parametros');
            $resultado.html('<div class="text-center text-muted py-3">Carregando...</div>');

            $.ajax({
                method: 'GET',
                url: '{{ route('fluxo-caixa.listar-parametros') }}'
            }).done(function(response) {
                renderizarParametros(response.dados || []);
            }).fail(function() {
                $resultado.html(
                    '<div class="text-center text-danger py-3">Erro ao buscar parâmetros.</div>');
            });
        }

        function renderizarParametros(dados) {
            var $resultado = $('#swal-resultado-parametros');

            if (dados.length === 0) {
                $resultado.html(
                    '<div class="text-center text-muted py-3">Nenhum parâmetro cadastrado.</div>');
                return;
            }

            function montarTabela(titulo, itens) {
                if (itens.length === 0) {
                    return '';
                }

                var linhas = itens.map(function(item) {
                    var formasPagto = item.formas_pagamento && item.formas_pagamento.length ?
                        item.formas_pagamento.join(', ') : '-';

                    return '<tr>' +
                        '<td>' + item.cd_tipoconta + '</td>' +
                        '<td>' + (item.ds_tipoconta || '-') + '</td>' +
                        '<td>' + formasPagto + '</td>' +
                        '<td class="text-center">' +
                        '<button type="button" class="btn btn-xs btn-outline-primary btn-editar-parametro" data-parametro=\'' +
                        JSON.stringify(item) +
                        '\' title="Editar"><i class="fas fa-pencil-alt"></i></button> ' +
                        '<button type="button" class="btn btn-xs btn-outline-danger btn-excluir-parametro" data-ids="' +
                        JSON.stringify(item.ids) +
                        '" title="Excluir"><i class="fas fa-trash"></i></button>' +
                        '</td>' +
                        '</tr>';
                }).join('');

                return '<h6 class="mt-2">' + titulo + '</h6>' +
                    '<table class="table table-sm table-striped" style="font-size:12px;">' +
                    '<thead><tr><th>Cód.</th><th>Descrição</th><th>Formas Pagto</th><th class="text-center">Ações</th></tr></thead>' +
                    '<tbody>' + linhas + '</tbody>' +
                    '</table>';
            }

            var receber = dados.filter(function(item) {
                return item.tipo === 'receber';
            });
            var pagar = dados.filter(function(item) {
                return item.tipo === 'pagar';
            });

            $resultado.html(montarTabela('Contas a Receber', receber) + montarTabela('Contas a Pagar',
                pagar));
        }

        $(document).on('click', '.btn-editar-parametro', function() {
            suprimirRecargaParametros = true;
            abrirFormularioParametro($(this).data('parametro'));
        });

        $(document).on('click', '.btn-excluir-parametro', function() {
            var ids = $(this).data('ids');
            suprimirRecargaParametros = true;

            Swal.fire({
                icon: 'warning',
                title: 'Excluir parâmetro?',
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
                    url: '{{ route('fluxo-caixa.excluir-parametro') }}',
                    data: {
                        _token: $('[name="csrf-token"]').attr('content'),
                        ids: ids
                    }
                }).done(function() {
                    fluxoParametrosAlterado = true;
                    abrirParametrosFluxo();
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                        'Erro ao excluir o parâmetro.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg
                    });
                });
            });
        });

        // Busca os CD_TIPOCONTA válidos no Firebird (filtrados por receber/pagar) e popula o
        // select, mantendo selecionado(s) o(s) valor(es) atual(is) quando informado (edição).
        // `valorSelecionado` aceita tanto um valor único (select simples, ex. Compensação)
        // quanto um array (select múltiplo, ex. Parâmetro) — `.val()` trata os dois casos.
        function carregarOpcoesTipoConta(tipo, valorSelecionado, seletor, incluirOpcaoVazia) {
            var $select = $(seletor || '#swal-param-cd-tipoconta');
            $select.prop('disabled', true).html('<option>Carregando...</option>').trigger('change');

            $.ajax({
                method: 'GET',
                url: '{{ route('firebird.tipos-conta') }}',
                data: {
                    tipo: tipo
                }
            }).done(function(opcoes) {
                $select.empty();

                if (incluirOpcaoVazia) {
                    $select.append('<option value=""></option>');
                }

                if (!opcoes || opcoes.length === 0) {
                    $select.append('<option value="">Nenhum tipo de conta encontrado</option>');
                    $select.prop('disabled', false).trigger('change');
                    return;
                }

                opcoes.forEach(function(opcao) {
                    $select.append('<option value="' + opcao.id + '">' + opcao.id + ' - ' + opcao
                        .text + '</option>');
                });

                $select.val(valorSelecionado || []).trigger('change');
                $select.prop('disabled', false).trigger('change');
            }).fail(function() {
                $select.html('<option value="">Erro ao carregar do Firebird</option>');
                $select.prop('disabled', false).trigger('change');
            });
        }

        // Busca as formas de pagamento no Firebird e popula o select (simples ou select2
        // multi-select, dependendo do elemento), mantendo selecionada(s) as informadas em
        // `selecionadas` (edição). `seletor` é opcional — usado pra reaproveitar essa função
        // fora do form de Parâmetros (ex: Lançamento Manual, com select simples).
        function carregarOpcoesFormaPagamento(selecionadas, seletor, incluirOpcaoVazia) {
            var $select = $(seletor || '#swal-param-formapagto');

            $.ajax({
                method: 'GET',
                url: '{{ route('get-form-pagamento') }}'
            }).done(function(opcoes) {
                $select.empty();

                if (incluirOpcaoVazia) {
                    $select.append('<option value=""></option>');
                }

                (opcoes || []).forEach(function(opcao) {
                    $select.append('<option value="' + opcao.CD_FORMAPAGTO + '">' + opcao
                        .CD_FORMAPAGTO + ' - ' + opcao.DS_FORMAPAGTO + '</option>');
                });

                $select.val(selecionadas || []).trigger('change');
            }).fail(function() {
                $select.empty().append(
                    '<option value="">Erro ao carregar formas de pagamento</option>');
            });
        }

        function abrirFormularioParametro(dadosExistentes) {
            var editando = !!(dadosExistentes && dadosExistentes.ids && dadosExistentes.ids.length);
            var tipo = dadosExistentes ? dadosExistentes.tipo : 'receber';
            var cdTipoConta = dadosExistentes && dadosExistentes.cd_tipoconta ? [dadosExistentes.cd_tipoconta] : [];
            var formasPagamento = dadosExistentes ? (dadosExistentes.formas_pagamento || []) : [];

            Swal.fire({
                title: editando ? 'Editar Parâmetro' : 'Adicionar Parâmetro',
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
                    '<label class="mb-1" style="font-size:12px;">Tipo <span class="text-danger">*</span></label>' +
                    '<select id="swal-param-tipo" class="form-control form-control-sm">' +
                    '<option value="receber"' + (tipo === 'receber' ? ' selected' : '') +
                    '>Contas a Receber</option>' +
                    '<option value="pagar"' + (tipo === 'pagar' ? ' selected' : '') +
                    '>Contas a Pagar</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label class="mb-1" style="font-size:12px;">Tipo Conta <span class="text-danger">*</span></label>' +
                    '<select id="swal-param-cd-tipoconta" class="w-100" multiple></select>' +
                    '</div>' +
                    '<div class="form-group mb-0">' +
                    '<label class="mb-1" style="font-size:12px;">Formas de Pagamento (opcional)</label>' +
                    '<select id="swal-param-formapagto" class="w-100" multiple></select>' +
                    '</div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    carregarOpcoesTipoConta(tipo, cdTipoConta);
                    carregarOpcoesFormaPagamento(formasPagamento);

                    $('#swal-param-cd-tipoconta').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Selecione um ou mais',
                        allowClear: true,
                        dropdownParent: $(Swal.getPopup()),
                        containerCssClass: 'select2-fluxo-sm',
                        dropdownCssClass: 'select2-fluxo-sm'
                    });

                    $('#swal-param-formapagto').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Selecione (opcional)',
                        allowClear: true,
                        dropdownParent: $(Swal.getPopup()),
                        containerCssClass: 'select2-fluxo-sm',
                        dropdownCssClass: 'select2-fluxo-sm'
                    });

                    $('#swal-param-tipo').on('change', function() {
                        carregarOpcoesTipoConta($(this).val(), []);
                    });
                },
                preConfirm: function() {
                    var tipoSelecionado = document.getElementById('swal-param-tipo').value;
                    var cdTipoContaSelecionados = $('#swal-param-cd-tipoconta').val() || [];
                    var dsTipoContaPorCodigo = {};

                    $('#swal-param-cd-tipoconta option:selected').each(function() {
                        dsTipoContaPorCodigo[this.value] = $(this).text().replace(this.value +
                            ' - ', '');
                    });
                    var formasPagtoSelecionadas = $('#swal-param-formapagto').val() || [];

                    if (!cdTipoContaSelecionados.length) {
                        Swal.showValidationMessage('Selecione ao menos um Tipo de Conta.');
                        return false;
                    }

                    var dadosEnvio = {
                        _token: $('[name="csrf-token"]').attr('content'),
                        tipo: tipoSelecionado,
                        cd_tipoconta: cdTipoContaSelecionados,
                        ds_tipoconta: dsTipoContaPorCodigo,
                        cd_formapagto: formasPagtoSelecionadas
                    };

                    if (editando) {
                        dadosEnvio.ids = dadosExistentes.ids;
                    }

                    return $.ajax({
                        method: 'POST',
                        url: editando ?
                            '{{ route('fluxo-caixa.atualizar-parametro') }}' :
                            '{{ route('fluxo-caixa.salvar-parametro') }}',
                        data: dadosEnvio
                    }).catch(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Erro ao salvar o parâmetro.';
                        Swal.showValidationMessage(msg);
                        return false;
                    });
                }
            }).then(function(result) {
                // Salvou, cancelou ou fechou (X/ESC) — em todos os casos volta pro modal de
                // Parâmetros, senão o usuário fica sem tela nenhuma. Esse formulário não abre
                // nenhum outro Swal por cima, então não precisa da flag de supressão usada na
                // Compensação Bancária.
                if (result.isConfirmed) {
                    fluxoParametrosAlterado = true;
                }

                abrirParametrosFluxo();
            });
        }

        // Controla a "volta" pro modal de Parâmetros quando o de Compensação é fechado. Como o
        // SweetAlert2 só mostra um popup por vez, abrir qualquer modal de dentro da lista de
        // Compensação (adicionar/editar/excluir) também resolve a promise da lista — por isso
        // essa flag: só volta pros Parâmetros quando o fechamento foi de verdade (X/ESC/fora),
        // não quando estamos de propósito navegando pra outro modal.
        var suprimirVoltaCompensacao = false;

        // Botão "Compensação Bancária" (dentro do modal de Parâmetros): lista, adiciona, edita
        // e exclui as regras de fluxo_caixa_compensacao (dias a somar por CD_TIPOCONTA).
        function abrirCompensacaoFluxo() {
            suprimirVoltaCompensacao = false;

            Swal.fire({
                title: 'Regras de Compensação Bancária (D+)',
                width: 750,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    title: 'swal-title-fluxo'
                },
                html: '<div style="text-align:left;">' +
                    '<div class="text-right mb-2">' +
                    '<button type="button" id="swal-btn-add-compensacao" class="btn btn-sm btn-success">' +
                    '<i class="fas fa-plus mr-1"></i>Adicionar</button>' +
                    '</div>' +
                    '<div id="swal-resultado-compensacao" style="max-height:360px; overflow-y:auto;"></div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    document.getElementById('swal-btn-add-compensacao').addEventListener('click',
                        function() {
                            suprimirVoltaCompensacao = true;
                            abrirFormularioCompensacao();
                        });
                    buscarCompensacoes();
                }
            }).then(function() {
                // Esse modal não tem botão de confirmar (só X/ESC/clique fora) — ao fechar de
                // verdade, volta pro modal de Parâmetros em vez de deixar o usuário "sem tela".
                if (!suprimirVoltaCompensacao) {
                    abrirParametrosFluxo();
                }
            });
        }

        function buscarCompensacoes() {
            var $resultado = $('#swal-resultado-compensacao');
            $resultado.html('<div class="text-center text-muted py-3">Carregando...</div>');

            $.ajax({
                method: 'GET',
                url: '{{ route('fluxo-caixa.listar-compensacao') }}'
            }).done(function(response) {
                renderizarCompensacoes(response.dados || []);
            }).fail(function() {
                $resultado.html(
                    '<div class="text-center text-danger py-3">Erro ao buscar regras de compensação.</div>'
                );
            });
        }

        function renderizarCompensacoes(dados) {
            var $resultado = $('#swal-resultado-compensacao');

            if (dados.length === 0) {
                $resultado.html(
                    '<div class="text-center text-muted py-3">Nenhuma regra cadastrada.</div>');
                return;
            }

            var linhas = dados.map(function(item) {
                return '<tr>' +
                    '<td>' + item.cd_tipoconta + '</td>' +
                    '<td>' + (item.ds_tipoconta || '-') + '</td>' +
                    '<td class="text-center">' + item.segunda + '</td>' +
                    '<td class="text-center">' + item.terca + '</td>' +
                    '<td class="text-center">' + item.quarta + '</td>' +
                    '<td class="text-center">' + item.quinta + '</td>' +
                    '<td class="text-center">' + item.sexta + '</td>' +
                    '<td class="text-center">' + item.sabado + '</td>' +
                    '<td class="text-center">' + item.domingo + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-xs btn-outline-primary btn-editar-compensacao" data-compensacao=\'' +
                    JSON.stringify(item) +
                    '\' title="Editar"><i class="fas fa-pencil-alt"></i></button> ' +
                    '<button type="button" class="btn btn-xs btn-outline-danger btn-excluir-compensacao" data-id="' +
                    item.id + '" title="Excluir"><i class="fas fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');

            $resultado.html(
                '<table class="table table-sm table-striped" style="font-size:11px;">' +
                '<thead><tr><th>Cód.</th><th>Descrição</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th><th>Dom</th><th class="text-center">Ações</th></tr></thead>' +
                '<tbody>' + linhas + '</tbody>' +
                '</table>');
        }

        $(document).on('click', '.btn-editar-compensacao', function() {
            suprimirVoltaCompensacao = true;
            abrirFormularioCompensacao($(this).data('compensacao'));
        });

        $(document).on('click', '.btn-excluir-compensacao', function() {
            var id = $(this).data('id');
            suprimirVoltaCompensacao = true;

            Swal.fire({
                icon: 'warning',
                title: 'Excluir regra de compensação?',
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
                    url: '{{ route('fluxo-caixa.excluir-compensacao') }}',
                    data: {
                        _token: $('[name="csrf-token"]').attr('content'),
                        id: id
                    }
                }).done(function() {
                    fluxoParametrosAlterado = true;
                    abrirCompensacaoFluxo();
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                        'Erro ao excluir a regra de compensação.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg
                    });
                });
            });
        });

        function abrirFormularioCompensacao(dadosExistentes) {
            var editando = !!(dadosExistentes && dadosExistentes.id);
            var cdTipoConta = dadosExistentes ? dadosExistentes.cd_tipoconta : '';
            var dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            var rotulos = {
                segunda: 'Segunda',
                terca: 'Terça',
                quarta: 'Quarta',
                quinta: 'Quinta',
                sexta: 'Sexta',
                sabado: 'Sábado',
                domingo: 'Domingo'
            };

            var camposDias = dias.map(function(dia) {
                var valor = dadosExistentes && dadosExistentes[dia] !== undefined ? dadosExistentes[
                    dia] : 0;
                return '<div class="col-6 col-md-3 mb-2">' +
                    '<label class="mb-1" style="font-size:11px;">' + rotulos[dia] +
                    ' <span class="text-danger">*</span></label>' +
                    '<input type="number" min="0" max="31" id="swal-comp-' + dia +
                    '" class="form-control form-control-sm" value="' + valor + '">' +
                    '</div>';
            }).join('');

            Swal.fire({
                title: editando ? 'Editar Regra de Compensação' : 'Adicionar Regra de Compensação',
                width: 550,
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
                    '<label class="mb-1" style="font-size:12px;">Tipo Conta <span class="text-danger">*</span></label>' +
                    '<select id="swal-comp-cd-tipoconta" class="form-control form-control-sm">' +
                    '<option>Carregando...</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="row">' + camposDias + '</div>' +
                    '<div class="form-group mb-2 mt-2" style="border-top:1px solid #dee2e6; padding-top:8px;">' +
                    '<label class="mb-1" style="font-size:12px;">Testar Data de Vencimento</label>' +
                    '<input type="date" id="swal-comp-teste-data" class="form-control form-control-sm">' +
                    '<div id="swal-comp-teste-resultado" style="font-size:12px; margin-top:6px; color:#555;"></div>' +
                    '</div>' +
                    '</div>',
                didOpen: function() {
                    makeSwalDraggable();
                    carregarOpcoesTipoConta(null, cdTipoConta, '#swal-comp-cd-tipoconta');

                    // Validação visual: só pra ajudar o usuário a conferir a regra que está
                    // digitando, sem depender do controller/model. Recalcula a cada mudança
                    // na data de teste ou em qualquer campo de dia.
                    var mapaDias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta',
                        'sabado'
                    ];

                    function recalcularCompensacaoTeste() {
                        var dataValor = document.getElementById('swal-comp-teste-data').value;
                        var $resultado = $('#swal-comp-teste-resultado');

                        if (!dataValor) {
                            $resultado.html('');
                            return;
                        }

                        var partes = dataValor.split('-');
                        var dataBase = new Date(partes[0], partes[1] - 1, partes[2]);
                        var diaCampo = mapaDias[dataBase.getDay()];
                        var offset = parseInt(document.getElementById('swal-comp-' + diaCampo)
                            .value, 10) || 0;
                        var dataCompensada = new Date(dataBase.getTime());
                        dataCompensada.setDate(dataCompensada.getDate() + offset);

                        var formatarData = function(d) {
                            var dd = String(d.getDate()).padStart(2, '0');
                            var mm = String(d.getMonth() + 1).padStart(2, '0');
                            return dd + '/' + mm + '/' + d.getFullYear();
                        };

                        $resultado.html(
                            'Vencimento cai em <strong>' + rotulos[diaCampo] + '</strong> (' +
                            formatarData(dataBase) + ')' +
                            (offset > 0 ? ' + ' + offset + ' dia(s)' : ' + 0 dia(s)') +
                            ' → compensa em <strong>' + formatarData(dataCompensada) +
                            '</strong>'
                        );
                    }

                    $('#swal-comp-teste-data').on('input change', recalcularCompensacaoTeste);
                    dias.forEach(function(dia) {
                        $('#swal-comp-' + dia).on('input', recalcularCompensacaoTeste);
                    });
                },
                preConfirm: function() {
                    var $opcaoSelecionada = $('#swal-comp-cd-tipoconta option:selected');
                    var cdTipoContaValor = $opcaoSelecionada.val();
                    var dsTipoContaValor = cdTipoContaValor ?
                        $opcaoSelecionada.text().replace(cdTipoContaValor + ' - ', '') : '';

                    if (!cdTipoContaValor) {
                        Swal.showValidationMessage('Selecione o CD_TIPOCONTA.');
                        return false;
                    }

                    var dadosEnvio = {
                        _token: $('[name="csrf-token"]').attr('content'),
                        cd_tipoconta: cdTipoContaValor,
                        ds_tipoconta: dsTipoContaValor
                    };

                    dias.forEach(function(dia) {
                        dadosEnvio[dia] = document.getElementById('swal-comp-' + dia).value || 0;
                    });

                    if (editando) {
                        dadosEnvio.id = dadosExistentes.id;
                    }

                    return $.ajax({
                        method: 'POST',
                        url: editando ?
                            '{{ route('fluxo-caixa.atualizar-compensacao') }}' :
                            '{{ route('fluxo-caixa.salvar-compensacao') }}',
                        data: dadosEnvio
                    }).catch(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Erro ao salvar a regra de compensação.';
                        Swal.showValidationMessage(msg);
                        return false;
                    });
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    fluxoParametrosAlterado = true;
                    abrirCompensacaoFluxo();
                }
            });
        }
    </script>
@stop
