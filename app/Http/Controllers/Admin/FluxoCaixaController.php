<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contas;
use App\Models\FluxoCaixaCompensacao;
use App\Models\FluxoCaixaParametro;
use App\Models\FluxoCaixaSaldoDia;
use App\Models\SaldoFluxoCaixa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FluxoCaixaController extends Controller
{
    public function index(Request $request)
    {
        // Data Real = DT_VENCIMENTO puro. Data Personalizada = data ajustada pela regra
        // de compensação bancária (hoje só definida para CD_TIPOCONTA = 2). Personalizada é
        // o padrão ao abrir a tela sem parâmetro.
        $tipoData = $request->query('tipo_data') === 'real' ? 'real' : 'personalizada';

        // "ref" é qualquer data dentro da semana que o usuário quer ver — os botões de
        // navegação (anterior/hoje/próxima) só mudam esse valor. Sem parâmetro, ou com um
        // valor inválido, cai na semana atual.
        try {
            $inicio = Carbon::parse($request->query('ref'));
        } catch (\Exception $e) {
            $inicio = Carbon::now();
        }

        // Garante que a semana sempre comece no sábado e termine na sexta-feira
        while (!$inicio->isSaturday()) {
            $inicio->subDay();
        }

        // Quantidade de semanas exibidas lado a lado (visão horizontal). Navegação
        // (anterior/hoje/próxima) sempre desliza 1 semana, independente desse valor.
        $qtdSemanas = max(1, min(12, (int) $request->query('semanas', 1)));
        $qtdDias    = $qtdSemanas * 7;

        $dias = [];
        for ($i = 0; $i < $qtdDias; $i++) {
            $dias[] = $inicio->copy()->addDays($i);
        }

        $dtInicio = $dias[0]->format('Y-m-d');
        $dtFim    = $dias[$qtdDias - 1]->format('Y-m-d');

        // Cada CD_TIPOCONTA tem sua própria condição de forma de pagamento, configurada pelo
        // usuário na tela de Parâmetros (tabela fluxo_caixa_parametros) — consultamos um de
        // cada vez e mesclamos os lançamentos antes de agrupar.
        $lancamentosReceber = [];
        foreach (FluxoCaixaParametro::tipocontasPorTipo('receber') as $cdTipoConta => $formasPagamento) {
            $lancamentosReceber = array_merge(
                $lancamentosReceber,
                Contas::contasReceber($dtInicio, $dtFim, $cdTipoConta, $formasPagamento)
            );
        }

        // Regras de compensação (Data Personalizada) por CD_TIPOCONTA, configuradas na tela de
        // Parâmetros (tabela fluxo_caixa_compensacao) — carregadas uma vez e reaproveitadas em
        // vez de consultar a cada lançamento.
        $offsetPorTipoConta = FluxoCaixaCompensacao::offsetPorTipoConta();

        $contasReceber = $this->agruparContas($lancamentosReceber, $dias, $tipoData, 'Clientes', $offsetPorTipoConta);

        $lancamentoManualEntrada = array_fill(0, $qtdDias, 0);

        $lancamentosPagar = [];
        foreach (FluxoCaixaParametro::tipocontasPorTipo('pagar') as $cdTipoConta => $formasPagamento) {
            $lancamentosPagar = array_merge(
                $lancamentosPagar,
                Contas::contasPagar($dtInicio, $dtFim, $cdTipoConta, $formasPagamento)
            );
        }

        $contasPagar = $this->agruparContas($lancamentosPagar, $dias, $tipoData, 'Fornecedores', $offsetPorTipoConta);

        $lancamentoManualSaida = array_fill(0, $qtdDias, 0);
        

        // Saldo bancário real por dia (forward-fill do que está em fluxo_caixa_saldo) — usado
        // só para saber o valor real informado no dia 0 e nos dias com lançamento manual.
        $saldoBancoRealPorDia = SaldoFluxoCaixa::saldoTotalPorDia($dias);

        // Dias em que existe algum lançamento de saldo bancário com dt_saldo exatamente
        // naquele dia — nesses dias o Saldo Banco usa o valor real informado.
        $diasComLancamentoManual = SaldoFluxoCaixa::diasComLancamento($dias);

        // Valor efetivamente lançado em cada dia (só os bancos atualizados naquele dia, sem
        // forward-fill) — usado pro Saldo Banco bater com o que o drill-down mostra.
        $valorSaldoBancoLancadoPorDia = SaldoFluxoCaixa::valorLancadoPorDia($dias);

        // Prioridade da âncora do dia 0 (mesma regra dos demais dias, só que "ontem" não está
        // dentro da janela exibida):
        // 1) Lançamento manual EXATAMENTE no dia 0 sempre vence (reconciliação, como já era).
        // 2) Sem isso, usa o cache de fluxo_caixa_saldo_dia (Saldo do Dia já calculado pro dia
        //    anterior) — é mais completo que o forward-fill puro, pois já inclui o fluxo do dia
        //    anterior, não só o saldo bancário bruto.
        // 3) Sem cache (ex: primeira vez que esse período é aberto), cai no forward-fill puro —
        //    exceto numa semana totalmente futura sem nenhum lançamento a partir dela, que vira
        //    0,00 em vez de arrastar um saldo antigo pra frente sem confirmação nenhuma.
        if (empty($diasComLancamentoManual[0])) {
            $saldoDoDiaAnterior = FluxoCaixaSaldoDia::buscarPorData($dias[0]->copy()->subDay());

            if ($saldoDoDiaAnterior !== null) {
                $saldoBancoRealPorDia[0] = $saldoDoDiaAnterior;
            } elseif ($dias[0]->greaterThan(Carbon::now()->startOfDay())
                && !SaldoFluxoCaixa::existeLancamentoAPartirDe($dias[0])) {
                $saldoBancoRealPorDia[0] = 0.0;
            }
        }

        $totalContasReceberPorDia = array_fill(0, $qtdDias, 0);
        foreach ($contasReceber as $categoria) {
            foreach ($categoria['totais'] as $i => $v) {
                $totalContasReceberPorDia[$i] += $v;
            }
        }

        // Total Entradas = só Contas a Receber (+ lançamento manual de entrada). O saldo
        // bancário NÃO entra aqui — ele participa direto do Saldo do Dia, não do fluxo do dia.
        $totalEntradasPorDia = array_fill(0, $qtdDias, 0);
        foreach ($totalContasReceberPorDia as $i => $v) {
            $totalEntradasPorDia[$i] = $v + $lancamentoManualEntrada[$i];
        }

        $totalContasPagarPorDia = array_fill(0, $qtdDias, 0);
        foreach ($contasPagar as $categoria) {
            foreach ($categoria['totais'] as $i => $v) {
                $totalContasPagarPorDia[$i] += $v;
            }
        }

        $totalSaidasPorDia = array_fill(0, $qtdDias, 0);
        foreach ($totalContasPagarPorDia as $i => $v) {
            $totalSaidasPorDia[$i] = $v + $lancamentoManualSaida[$i];
        }

        // Saldo Banco não faz "degrau" (não repete um valor antigo indefinidamente):
        // - Num dia com lançamento manual, usa só a soma do que foi lançado NAQUELE dia (não o
        //   total reconciliado de todos os bancos) — assim bate com o que o drill-down mostra.
        // - No dia 0 sem lançamento próprio, usa a âncora (cache do dia anterior ou forward-fill
        //   residual, já resolvidos acima).
        // - Nos demais dias, herda o Saldo do Dia do dia anterior — que já é o resultado
        //   acumulado até ali.
        // Saldo do Dia = Saldo Banco (daquele dia) + Contas a Receber − Contas a Pagar, e vira
        // o Saldo Banco do dia seguinte enquanto não houver um novo saldo real informado.
        $saldoBancoPorDia = [];
        $saldoBancoClicavelPorDia = [];
        $saldoDia = [];
        for ($i = 0; $i < $qtdDias; $i++) {
            if (!empty($diasComLancamentoManual[$i])) {
                $saldoBancoPorDia[$i] = $valorSaldoBancoLancadoPorDia[$i];
            } elseif ($i === 0) {
                $saldoBancoPorDia[$i] = $saldoBancoRealPorDia[0];
            } else {
                $saldoBancoPorDia[$i] = $saldoDia[$i - 1];
            }

            // Só é clicável quando existe de fato um lançamento manual NAQUELE dia — o dia 0
            // pode estar mostrando um valor herdado do cache ou de um lançamento de outro dia
            // (ex: forward-fill), e nesse caso abrir editar/excluir confundiria o usuário, que
            // pode achar que o lançamento é daquele dia e acabar excluindo o errado.
            $saldoBancoClicavelPorDia[$i] = !empty($diasComLancamentoManual[$i]);

            $saldoDia[$i] = $saldoBancoPorDia[$i] + $totalEntradasPorDia[$i] - $totalSaidasPorDia[$i];
        }

        $saldoInicial = $saldoBancoPorDia[0];

        // Exibição da linha "Total Entradas": mostra Saldo Banco + Contas a Receber somados,
        // só pra visualização — o Saldo do Dia continua calculado com $totalEntradasPorDia
        // "puro" (sem o saldo bancário), senão duplicaria o valor do banco todo dia de novo.
        $totalEntradasExibicaoPorDia = [];
        foreach ($totalEntradasPorDia as $i => $v) {
            $totalEntradasExibicaoPorDia[$i] = $saldoBancoPorDia[$i] + $v;
        }

        // Persiste o Saldo do Dia calculado pra cada data exibida — serve de cache pra ancorar
        // a próxima semana (ou qualquer período futuro) quando o usuário navegar pra frente.
        FluxoCaixaSaldoDia::salvarLote($dias, $saldoDia);

        // Lançamentos (por banco) que compõem o total de cada dia — usado no drill-down ao
        // clicar num valor da linha "Saldo Banco".
        $saldoBancoDetalhePorDia = SaldoFluxoCaixa::detalhePorDia($dias);

        // Card "Saldo Banco(s)": mostra só o que foi lançado manualmente hoje (não é a
        // projeção do período exibido, é sempre a data real de hoje).
        $saldoBancoHoje = SaldoFluxoCaixa::saldoLancadoNoDia(Carbon::now());

        return view('admin.financeiro.fluxo-caixa', [
            'dias' => $dias,
            'finsDeSemana' => array_map(fn (Carbon $dia) => $dia->isWeekend(), $dias),
            'tipoData' => $tipoData,
            'qtdSemanas' => $qtdSemanas,
            'refSemanaAtual' => $dias[0]->format('Y-m-d'),
            'refSemanaAnterior' => $dias[0]->copy()->subDays(7)->format('Y-m-d'),
            'refSemanaProxima' => $dias[0]->copy()->addDays(7)->format('Y-m-d'),
            'saldoInicial' => $saldoInicial,
            'saldoBancoHoje' => $saldoBancoHoje,
            'saldoBancoPorDia' => $saldoBancoPorDia,
            'saldoBancoClicavelPorDia' => $saldoBancoClicavelPorDia,
            'saldoBancoDetalhePorDia' => $saldoBancoDetalhePorDia,
            'diasComLancamentoManual' => $diasComLancamentoManual,
            'contasReceber' => $contasReceber,
            'lancamentoManualEntrada' => $lancamentoManualEntrada,
            'contasPagar' => $contasPagar,
            'lancamentoManualSaida' => $lancamentoManualSaida,
            'totalContasReceberPorDia' => $totalContasReceberPorDia,
            'totalContasPagarPorDia' => $totalContasPagarPorDia,
            'totalEntradasPorDia' => $totalEntradasPorDia,
            'totalEntradasExibicaoPorDia' => $totalEntradasExibicaoPorDia,
            'totalSaidasPorDia' => $totalSaidasPorDia,
            'saldoDia' => $saldoDia,
        ]);
    }

    public function salvarSaldoBanco(Request $request)
    {
        $validado = $request->validate([
            'ds_banco' => ['required', 'string', 'max:100'],
            'vl_saldo' => ['required', 'numeric'],
            'dt_saldo' => ['required', 'date'],
        ]);

        if (Carbon::parse($validado['dt_saldo'])->greaterThan(Carbon::now()->endOfDay())) {
            return response()->json([
                'message' => 'A data do lançamento não pode ser maior que hoje.',
            ], 422);
        }

        SaldoFluxoCaixa::create([
            'ds_banco' => $validado['ds_banco'],
            'vl_saldo' => $validado['vl_saldo'],
            'dt_saldo' => $validado['dt_saldo'],
            'id_user' => auth()->id(),
        ]);

        return response()->json([
            'success' => 'Saldo do banco salvo com sucesso!',
        ]);
    }

    public function listarSaldoBanco(Request $request)
    {
        $validado = $request->validate([
            'dt_inicio' => ['required', 'date'],
            'dt_fim' => ['required', 'date'],
        ]);

        $lancamentos = SaldoFluxoCaixa::whereBetween('dt_saldo', [$validado['dt_inicio'], $validado['dt_fim']])
            ->orderByDesc('dt_saldo')
            ->orderByDesc('id')
            ->get()
            ->map(fn (SaldoFluxoCaixa $lancamento) => [
                'id' => $lancamento->id,
                'ds_banco' => $lancamento->ds_banco,
                'vl_saldo' => (float) $lancamento->vl_saldo,
                'dt_saldo' => $lancamento->dt_saldo->format('Y-m-d'),
                'dt_saldo_formatada' => $lancamento->dt_saldo->format('d/m/Y'),
            ]);

        return response()->json([
            'dados' => $lancamentos,
        ]);
    }

    public function atualizarSaldoBanco(Request $request)
    {
        $validado = $request->validate([
            'id' => ['required', 'integer', 'exists:fluxo_caixa_saldo,id'],
            'ds_banco' => ['required', 'string', 'max:100'],
            'vl_saldo' => ['required', 'numeric'],
            'dt_saldo' => ['required', 'date'],
        ]);

        if (Carbon::parse($validado['dt_saldo'])->greaterThan(Carbon::now()->endOfDay())) {
            return response()->json([
                'message' => 'A data do lançamento não pode ser maior que hoje.',
            ], 422);
        }

        $lancamento = SaldoFluxoCaixa::findOrFail($validado['id']);
        $lancamento->update([
            'ds_banco' => $validado['ds_banco'],
            'vl_saldo' => $validado['vl_saldo'],
            'dt_saldo' => $validado['dt_saldo'],
        ]);

        return response()->json([
            'success' => 'Saldo do banco atualizado com sucesso!',
        ]);
    }

    public function excluirSaldoBanco(Request $request)
    {
        $validado = $request->validate([
            'id' => ['required', 'integer', 'exists:fluxo_caixa_saldo,id'],
        ]);

        SaldoFluxoCaixa::findOrFail($validado['id'])->delete();

        return response()->json([
            'success' => 'Lançamento excluído com sucesso!',
        ]);
    }

    /**
     * Agrupa por (tipo, cd_tipoconta) — cada CD_TIPOCONTA pode ter várias formas de pagamento
     * associadas (uma linha por combinação na tabela), mas na tela isso é um único "parâmetro".
     */
    public function listarParametros(Request $request)
    {
        $parametros = FluxoCaixaParametro::orderBy('tipo')->orderBy('cd_tipoconta')->get()
            ->groupBy(fn (FluxoCaixaParametro $parametro) => $parametro->tipo . '|' . $parametro->cd_tipoconta)
            ->map(fn ($linhas) => [
                'ids' => $linhas->pluck('id')->values(),
                'tipo' => $linhas->first()->tipo,
                'cd_tipoconta' => $linhas->first()->cd_tipoconta,
                'ds_tipoconta' => $linhas->first()->ds_tipoconta,
                'formas_pagamento' => $linhas->pluck('cd_formapagto')->filter()->values(),
            ])
            ->values();

        return response()->json([
            'dados' => $parametros,
        ]);
    }

    public function salvarParametro(Request $request)
    {
        $validado = $request->validate([
            'tipo' => ['required', 'in:receber,pagar'],
            'cd_tipoconta' => ['required', 'array', 'min:1'],
            'cd_tipoconta.*' => ['integer'],
            'ds_tipoconta' => ['nullable', 'array'],
            'ds_tipoconta.*' => ['nullable', 'string', 'max:100'],
            'cd_formapagto' => ['nullable', 'array'],
            'cd_formapagto.*' => ['string', 'max:10'],
        ]);

        $this->salvarGrupoParametro($validado);

        return response()->json([
            'success' => 'Parâmetro salvo com sucesso!',
        ]);
    }

    public function atualizarParametro(Request $request)
    {
        $validado = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:fluxo_caixa_parametros,id'],
            'tipo' => ['required', 'in:receber,pagar'],
            'cd_tipoconta' => ['required', 'array', 'min:1'],
            'cd_tipoconta.*' => ['integer'],
            'ds_tipoconta' => ['nullable', 'array'],
            'ds_tipoconta.*' => ['nullable', 'string', 'max:100'],
            'cd_formapagto' => ['nullable', 'array'],
            'cd_formapagto.*' => ['string', 'max:10'],
        ]);

        DB::transaction(function () use ($validado) {
            FluxoCaixaParametro::whereIn('id', $validado['ids'])->delete();
            $this->salvarGrupoParametro($validado);
        });

        return response()->json([
            'success' => 'Parâmetro atualizado com sucesso!',
        ]);
    }

    public function excluirParametro(Request $request)
    {
        $validado = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:fluxo_caixa_parametros,id'],
        ]);

        FluxoCaixaParametro::whereIn('id', $validado['ids'])->delete();

        return response()->json([
            'success' => 'Parâmetro excluído com sucesso!',
        ]);
    }

    public function listarCompensacao(Request $request)
    {
        $compensacoes = FluxoCaixaCompensacao::orderBy('cd_tipoconta')->get()
            ->map(fn (FluxoCaixaCompensacao $compensacao) => [
                'id' => $compensacao->id,
                'cd_tipoconta' => $compensacao->cd_tipoconta,
                'ds_tipoconta' => $compensacao->ds_tipoconta,
                'segunda' => $compensacao->segunda,
                'terca' => $compensacao->terca,
                'quarta' => $compensacao->quarta,
                'quinta' => $compensacao->quinta,
                'sexta' => $compensacao->sexta,
                'sabado' => $compensacao->sabado,
                'domingo' => $compensacao->domingo,
            ]);

        return response()->json([
            'dados' => $compensacoes,
        ]);
    }

    public function salvarCompensacao(Request $request)
    {
        $validado = $request->validate($this->regrasValidacaoCompensacao() + [
            'cd_tipoconta' => ['required', 'integer', 'unique:fluxo_caixa_compensacao,cd_tipoconta'],
        ]);

        FluxoCaixaCompensacao::create($validado + ['updated_by' => auth()->id()]);

        return response()->json([
            'success' => 'Regra de compensação salva com sucesso!',
        ]);
    }

    public function atualizarCompensacao(Request $request)
    {
        $validado = $request->validate([
            'id' => ['required', 'integer', 'exists:fluxo_caixa_compensacao,id'],
            'cd_tipoconta' => [
                'required',
                'integer',
                Rule::unique('fluxo_caixa_compensacao', 'cd_tipoconta')->ignore($request->input('id')),
            ],
        ] + $this->regrasValidacaoCompensacao(semTipoConta: true));

        $compensacao = FluxoCaixaCompensacao::findOrFail($validado['id']);
        $compensacao->update(collect($validado)->except('id')->all() + ['updated_by' => auth()->id()]);

        return response()->json([
            'success' => 'Regra de compensação atualizada com sucesso!',
        ]);
    }

    public function excluirCompensacao(Request $request)
    {
        $validado = $request->validate([
            'id' => ['required', 'integer', 'exists:fluxo_caixa_compensacao,id'],
        ]);

        FluxoCaixaCompensacao::findOrFail($validado['id'])->delete();

        return response()->json([
            'success' => 'Regra de compensação excluída com sucesso!',
        ]);
    }

    private function regrasValidacaoCompensacao(bool $semTipoConta = false): array
    {
        $regras = [
            'ds_tipoconta' => ['nullable', 'string', 'max:100'],
            'segunda' => ['required', 'integer', 'min:0', 'max:31'],
            'terca' => ['required', 'integer', 'min:0', 'max:31'],
            'quarta' => ['required', 'integer', 'min:0', 'max:31'],
            'quinta' => ['required', 'integer', 'min:0', 'max:31'],
            'sexta' => ['required', 'integer', 'min:0', 'max:31'],
            'sabado' => ['required', 'integer', 'min:0', 'max:31'],
            'domingo' => ['required', 'integer', 'min:0', 'max:31'],
        ];

        if (!$semTipoConta) {
            $regras['cd_tipoconta'] = ['required', 'integer'];
        }

        return $regras;
    }

    /**
     * Cria uma linha por combinação de CD_TIPOCONTA (agora multi-select) e forma de pagamento
     * selecionada (ou uma única linha sem forma de pagamento, se nenhuma foi selecionada) —
     * cada CD_TIPOCONTA vira seu próprio grupo depois no listarParametros().
     */
    private function salvarGrupoParametro(array $dados): void
    {
        $formasPagamento = $dados['cd_formapagto'] ?? [];

        if (empty($formasPagamento)) {
            $formasPagamento = [null];
        }

        $dsTipoContaPorCodigo = $dados['ds_tipoconta'] ?? [];

        foreach ($dados['cd_tipoconta'] as $cdTipoConta) {
            foreach ($formasPagamento as $cdFormaPagto) {
                FluxoCaixaParametro::create([
                    'tipo' => $dados['tipo'],
                    'cd_tipoconta' => $cdTipoConta,
                    'ds_tipoconta' => $dsTipoContaPorCodigo[$cdTipoConta] ?? null,
                    'cd_formapagto' => $cdFormaPagto,
                    'updated_by' => auth()->id(),
                ]);
            }
        }
    }

    /**
     * Agrupa os lançamentos de contas (a receber ou a pagar) por categoria (DS_FORMAPAGTO) e,
     * dentro de cada categoria, por pessoa — cada nível com os valores distribuídos por dia da semana.
     *
     * @param  array          $lancamentos      Retorno de Contas::contasReceber()/contasPagar()
     * @param  Carbon\Carbon[] $dias             Os 7 dias (sáb a sex) exibidos no fluxo
     * @param  string         $tipoData         'real' (DT_VENCIMENTO) ou 'personalizada' (data ajustada)
     * @param  string         $prefixoCategoria Usado como fallback de categoria quando DS_FORMAPAGTO vem nulo
     * @param  array<int,array<int,int>> $offsetPorTipoConta Retorno de FluxoCaixaCompensacao::offsetPorTipoConta()
     * @return array<string, array{totais: array<int,float>, detalhe: array<string, array<int,float>>}>
     */
    private function agruparContas(array $lancamentos, array $dias, string $tipoData, string $prefixoCategoria, array $offsetPorTipoConta): array
    {
        $qtdDias = count($dias);

        $indicePorData = [];
        foreach ($dias as $i => $dia) {
            $indicePorData[$dia->format('Y-m-d')] = $i;
        }

        $grupos = [];
        foreach ($lancamentos as $lancamento) {
            $dtVencimento = Carbon::parse($lancamento->DT_VENCIMENTO);
            $dataReferencia = $tipoData === 'personalizada'
                ? $this->calcularDataPersonalizada($dtVencimento, (int) $lancamento->CD_TIPOCONTA, $offsetPorTipoConta)
                : $dtVencimento;

            $dataChave = $dataReferencia->format('Y-m-d');
            if (!isset($indicePorData[$dataChave])) {
                continue;
            }
            $i = $indicePorData[$dataChave];

            $categoria = trim($lancamento->DS_FORMAPAGTO ?? ($prefixoCategoria . ' - ' . $lancamento->DS_TIPOCONTA));
            $cliente = $lancamento->NM_PESSOA;
            $valor = (float) $lancamento->VL_SALDO;

            if (!isset($grupos[$categoria])) {
                $grupos[$categoria] = [
                    'totais' => array_fill(0, $qtdDias, 0),
                    'detalhe' => [],
                    'lancamentos' => array_fill(0, $qtdDias, []),
                ];
            }
            $grupos[$categoria]['totais'][$i] += $valor;

            if (!isset($grupos[$categoria]['detalhe'][$cliente])) {
                $grupos[$categoria]['detalhe'][$cliente] = array_fill(0, $qtdDias, 0);
            }
            $grupos[$categoria]['detalhe'][$cliente][$i] += $valor;

            $grupos[$categoria]['lancamentos'][$i][] = [
                'nr_lancamento' => $lancamento->NR_LANCAMENTO,
                'nm_pessoa' => $cliente,
                'dt_real' => $dtVencimento->format('d/m/Y'),
                'valor' => $valor,
            ];
        }

        return $grupos;
    }

    /**
     * Calcula a data de compensação bancária (Data Personalizada) a partir do vencimento,
     * usando a regra cadastrada pelo usuário na tela de Parâmetros (tabela
     * fluxo_caixa_compensacao) pro CD_TIPOCONTA do lançamento. Tipos de conta sem regra
     * cadastrada usam o vencimento sem ajuste.
     *
     * @param  array<int,array<int,int>> $offsetPorTipoConta Retorno de FluxoCaixaCompensacao::offsetPorTipoConta()
     */
    private function calcularDataPersonalizada(Carbon $dtVencimento, int $cdTipoConta, array $offsetPorTipoConta): Carbon
    {
        $offsetPorDiaSemana = $offsetPorTipoConta[$cdTipoConta] ?? null;

        if ($offsetPorDiaSemana === null) {
            return $dtVencimento->copy();
        }

        return $dtVencimento->copy()->addDays($offsetPorDiaSemana[$dtVencimento->dayOfWeek]);
    }
}
