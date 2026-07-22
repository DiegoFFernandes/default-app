<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Acesso ao saldo de caixa real do Firebird (tabela SALDOCAIXA), filtrado pelas contas
 * cadastradas em fluxo_caixa_saldo_conta (MySQL) — fonte alternativa ao saldo digitado
 * manualmente (SaldoFluxoCaixa/fluxo_caixa_saldo), configurável em fluxo_caixa_config
 * ('origem_saldo_banco' = 'digitado' ou 'firebird'). Mesmos nomes/formatos de retorno de
 * SaldoFluxoCaixa, pra poder ser usada como substituta direta no FluxoCaixaController::index().
 */
class SaldoCaixa
{
    /** Memo por request de buscar(), já que vários métodos leem a mesma faixa na mesma tela. */
    private static array $cacheBusca = [];

    private static function contasConfiguradas(): array
    {
        return FluxoCaixaSaldoConta::pluck('cd_conta')->all();
    }

    /**
     * Monta os bindings/placeholders (:cd_conta_0, :cd_conta_1...) pra um IN() com a lista de
     * contas configuradas, evitando repetir isso em cada método.
     */
    private static function bindingsContas(array $contas): array
    {
        $bindings = [];
        $placeholders = [];

        foreach (array_values($contas) as $i => $cdConta) {
            $placeholders[] = ":cd_conta_{$i}";
            $bindings["cd_conta_{$i}"] = $cdConta;
        }

        return [$bindings, $placeholders];
    }

    /**
     * Lista os saldos (SALDOCAIXA + PLANOCONTAS) das contas configuradas. $dtInicio/$dtFim
     * aceitam null pra não limitar aquele lado do período (ex: forward-fill precisa de "até
     * uma data", sem limite inferior). Retorna vazio se nenhuma conta estiver cadastrada em
     * fluxo_caixa_saldo_conta.
     */
    private static function buscar(?string $dtInicio, ?string $dtFim): array
    {
        $chaveCache = ($dtInicio ?? '') . '..' . ($dtFim ?? '');
        if (isset(self::$cacheBusca[$chaveCache])) {
            return self::$cacheBusca[$chaveCache];
        }

        $contas = self::contasConfiguradas();

        if (empty($contas)) {
            return [];
        }

        [$bindings, $placeholders] = self::bindingsContas($contas);

        $condicoesData = [];
        if ($dtInicio !== null) {
            $bindings['dt_inicio'] = $dtInicio;
            $condicoesData[] = 'S.DT_CAIXA >= CAST(:dt_inicio AS DATE)';
        }
        if ($dtFim !== null) {
            $bindings['dt_fim'] = $dtFim;
            $condicoesData[] = 'S.DT_CAIXA <= CAST(:dt_fim AS DATE)';
        }
        $filtroData = $condicoesData ? implode(' AND ', $condicoesData) . ' AND' : '';

        return self::$cacheBusca[$chaveCache] = \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                S.CD_EMPRESA,
                S.CD_CONTA,
                S.CD_EMPRESA||' - '||S.CD_CONTA||' - '||PC.DS_CONTA DS_BANCO,
                S.DT_CAIXA DT_SALDO,
                S.VL_SALDOCAIXA VL_SALDO
            FROM SALDOCAIXA S
            INNER JOIN PLANOCONTAS PC ON (PC.CD_CONTA = S.CD_CONTA)
            WHERE {$filtroData}
                  S.CD_CONTA IN (" . implode(', ', $placeholders) . ")
        ", $bindings));
    }

    /**
     * Normaliza o retorno de buscar() e agrupa por (CD_EMPRESA, CD_CONTA).
     *
     * O agrupamento é por empresa+conta, não só por conta: no SALDOCAIXA cada empresa mantém sua
     * própria parcela na mesma conta contábil, e o saldo é cumulativo por empresa (o
     * VL_SALDOANTERIOR de uma linha é o VL_SALDOCAIXA da linha anterior da mesma empresa+conta).
     * Agrupar só por conta faria a empresa que movimentou por último "apagar" as parcelas das
     * demais, que continuam existindo mesmo sem movimento recente.
     */
    private static function agruparPorEmpresaConta(array $linhas): \Illuminate\Support\Collection
    {
        return collect($linhas)
            ->map(fn ($linha) => (object) [
                'chave' => $linha->CD_EMPRESA . '|' . $linha->CD_CONTA,
                'cd_empresa' => $linha->CD_EMPRESA,
                'cd_conta' => $linha->CD_CONTA,
                'ds_banco' => $linha->DS_BANCO,
                'vl_saldo' => (float) $linha->VL_SALDO,
                'dt_saldo' => Carbon::parse($linha->DT_SALDO),
            ])
            ->groupBy('chave');
    }

    /**
     * Último saldo conhecido de cada empresa+conta até $dia (inclusive) — o forward-fill que
     * sustenta todo o resto da classe.
     *
     * É o ponto central da diferença entre esta origem e o saldo digitado: o Firebird só grava
     * uma linha em SALDOCAIXA quando a conta teve movimento naquele dia. Uma conta parada desde
     * 21/07 continua valendo o mesmo saldo em 22/07, mas não tem linha nessa data — somar só as
     * linhas do dia faria essa conta sumir do total. Aqui ela é arrastada com a data original,
     * que é justamente o que o drill-down exibe ("atualizado em 21/07").
     *
     * Empresa+conta ainda sem nenhum registro até $dia fica de fora (não vira zero).
     */
    private static function ultimosSaldosAte(\Illuminate\Support\Collection $porEmpresaConta, Carbon $dia): \Illuminate\Support\Collection
    {
        return $porEmpresaConta
            ->map(function ($linhas) use ($dia) {
                $ateODia = $linhas->filter(fn ($linha) => $linha->dt_saldo->lte($dia));

                if ($ateODia->isEmpty()) {
                    return null;
                }

                // Pode haver mais de uma linha na mesma data (a soma acompanha o original).
                $ultimaData = $ateODia->max('dt_saldo');

                return $ateODia->filter(fn ($linha) => $linha->dt_saldo->equalTo($ultimaData));
            })
            ->filter()
            ->flatten(1);
    }

    /**
     * Para cada dia informado, soma o saldo mais recente conhecido de cada par
     * (CD_EMPRESA, CD_CONTA) — a última linha com DT_CAIXA menor ou igual àquele dia. Mesmo
     * "degrau" de SaldoFluxoCaixa::saldoTotalPorDia(); pares ainda sem registro até aquele dia
     * não entram na soma.
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function saldoTotalPorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porEmpresaConta = self::agruparPorEmpresaConta(self::buscar(null, end($dias)->format('Y-m-d')));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $resultado[$i] = (float) self::ultimosSaldosAte($porEmpresaConta, $dia)->sum('vl_saldo');
        }

        return $resultado;
    }

    /**
     * Total do Saldo Banco a exibir num dia que tem movimento — aqui, igual ao saldo total
     * forward-filled do dia.
     *
     * No saldo digitado esses dois métodos são diferentes: um lançamento manual é uma
     * reconciliação de TODOS os bancos, então o dia vale só o que foi lançado nele. No Firebird
     * uma linha significa apenas "esta conta movimentou", nunca o total — somar só as linhas do
     * dia derrubaria do total toda conta parada (ver ultimosSaldosAte()). Por isso, nesta origem,
     * os dois convergem para o mesmo cálculo, e o drill-down (detalhePorDia) segue a mesma regra
     * para continuar batendo com o total exibido.
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function valorLancadoPorDia(array $dias): array
    {
        return self::saldoTotalPorDia($dias);
    }

    /**
     * Marca, para cada dia informado, se existe algum registro com DT_CAIXA exatamente
     * naquele dia. Mesmo formato de SaldoFluxoCaixa::diasComLancamento().
     *
     * É essa marcação que faz o FluxoCaixaController usar o saldo real do dia em vez de projetar
     * a partir do Saldo do Dia anterior. Dias futuros nunca entram: mesmo que o SALDOCAIXA tenha
     * linha com data à frente, deixar o valor real vencer ali descartaria o a receber/a pagar já
     * acumulado na projeção da janela. De amanhã em diante, sempre projeta.
     *
     * @param  Carbon[] $dias
     * @return array<int, bool>
     */
    public static function diasComLancamento(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $hoje = Carbon::now()->startOfDay();

        $datasComLancamento = collect(self::buscar(reset($dias)->format('Y-m-d'), end($dias)->format('Y-m-d')))
            ->map(fn ($linha) => Carbon::parse($linha->DT_SALDO)->format('Y-m-d'))
            ->flip();

        $resultado = [];
        foreach ($dias as $i => $dia) {
            // startOfDay() antes de comparar: $dia pode chegar com a hora do request (a tela sem
            // ?ref monta os dias a partir de Carbon::now()), e aí o próprio dia de hoje ficaria
            // "no futuro" contra a meia-noite de hoje — descartando o saldo real lançado hoje.
            $ehPassadoOuHoje = $dia->copy()->startOfDay()->lte($hoje);

            $resultado[$i] = $ehPassadoOuHoje && $datasComLancamento->has($dia->format('Y-m-d'));
        }

        return $resultado;
    }

    /**
     * Verifica se existe algum registro com DT_CAIXA maior ou igual à data informada. Mesmo
     * formato de SaldoFluxoCaixa::existeLancamentoAPartirDe().
     */
    public static function existeLancamentoAPartirDe(Carbon $data): bool
    {
        $contas = self::contasConfiguradas();

        if (empty($contas)) {
            return false;
        }

        [$bindings, $placeholders] = self::bindingsContas($contas);
        $bindings['dt_data'] = $data->format('Y-m-d');

        $row = DB::connection('firebird')->selectOne("
            SELECT COUNT(*) CNT
            FROM SALDOCAIXA S
            WHERE S.DT_CAIXA >= CAST(:dt_data AS DATE)
                  AND S.CD_CONTA IN (" . implode(', ', $placeholders) . ")
        ", $bindings);

        return ((int) ($row->CNT ?? $row->cnt ?? 0)) > 0;
    }

    /**
     * Para cada dia informado, lista o último saldo conhecido de cada conta (CD_CONTA/DS_BANCO/
     * VL_SALDO) — mesmo formato de SaldoFluxoCaixa::detalhePorDia(), usado no drill-down.
     *
     * Segue o mesmo forward-fill do total (valorLancadoPorDia), então a soma das linhas aqui bate
     * com o Saldo Banco exibido. Uma conta parada aparece com a data do seu último movimento
     * (a tela mostra dt_saldo_formatada por linha), deixando visível que aquele saldo foi
     * arrastado e não lançado no dia.
     *
     * Como SALDOCAIXA não tem um id de linha próprio, sintetiza um a partir de
     * empresa+conta+DT_CAIXA (só pra identificação na tela — não é editável/excluível como o
     * saldo digitado).
     *
     * @param  Carbon[] $dias
     * @return array<int, array<int, array{id:string, ds_banco:string, vl_saldo:float, dt_saldo:string, dt_saldo_formatada:string}>>
     */
    public static function detalhePorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porEmpresaConta = self::agruparPorEmpresaConta(self::buscar(null, end($dias)->format('Y-m-d')));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $resultado[$i] = self::ultimosSaldosAte($porEmpresaConta, $dia)
                ->sortBy('ds_banco')
                ->map(fn ($linha) => [
                    'id' => $linha->cd_empresa . '-' . $linha->cd_conta . '-' . $linha->dt_saldo->format('Ymd'),
                    'ds_banco' => $linha->ds_banco,
                    'vl_saldo' => $linha->vl_saldo,
                    'dt_saldo' => $linha->dt_saldo->format('Y-m-d'),
                    'dt_saldo_formatada' => $linha->dt_saldo->format('d/m/Y'),
                ])->values()->all();
        }

        return $resultado;
    }

    /**
     * Saldo bancário consolidado de hoje — último saldo conhecido de cada conta configurada.
     * Mesmo formato de SaldoFluxoCaixa::saldoUltimoDiaLancado(), alimenta o card
     * "Saldo Banco(s) Hoje".
     *
     * Não usa MAX(DT_CAIXA) pra escolher "o último dia lançado" e somar só ele: isso devolveria
     * apenas as contas que movimentaram nessa data, ignorando as paradas. O forward-fill em hoje
     * já resolve os dois casos (inclusive o de nenhuma conta ter movimentado hoje) e, por ser
     * limitado a hoje, também ignora eventual lançamento com data à frente.
     */
    public static function saldoUltimoDiaLancado(): float
    {
        return self::saldoTotalPorDia([Carbon::now()->startOfDay()])[0] ?? 0.0;
    }

    /**
     * Contas ativas (PARMSALDO + PLANOCONTAS) de um determinado TP_SALDO — usado pra popular o
     * select de "Conta" na tela de configuração, depois que o usuário escolhe o "Tipo Conta
     * Saldo" (valores fixos definidos na view, não vêm do banco).
     *
     * @return array<int, array{id:int, text:string}>
     */
    public static function buscarContasPorTipoSaldo(string $tpSaldo): array
    {
        $rows = \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT DISTINCT
                P.CD_CONTA,
                P.CD_CONTA || ' - ' || PC.DS_CONTA DS_CONTA
            FROM PARMSALDO P
            INNER JOIN PLANOCONTAS PC ON (PC.CD_CONTA = P.CD_CONTA)
            WHERE P.ST_ATIVO = 'S'
                  AND P.TP_SALDO = :tp_saldo
            ORDER BY DS_CONTA
        ", ['tp_saldo' => $tpSaldo]));

        return array_map(fn ($r) => [
            'id' => $r->CD_CONTA ?? $r->cd_conta ?? null,
            'text' => $r->DS_CONTA ?? $r->ds_conta ?? '',
        ], $rows);
    }
}
