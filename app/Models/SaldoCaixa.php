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

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                S.CD_EMPRESA,
                S.CD_CONTA,
                PC.DS_CONTA DS_BANCO,
                S.DT_CAIXA DT_SALDO,
                S.VL_SALDOCAIXA VL_SALDO
            FROM SALDOCAIXA S
            INNER JOIN PLANOCONTAS PC ON (PC.CD_CONTA = S.CD_CONTA)
            WHERE {$filtroData}
                  S.CD_CONTA IN (" . implode(', ', $placeholders) . ")
        ", $bindings));
    }

    /**
     * Para cada dia informado, soma o saldo mais recente conhecido de cada par
     * (CD_EMPRESA, CD_CONTA) — a última linha com DT_CAIXA menor ou igual àquele dia. Mesmo
     * "degrau" de SaldoFluxoCaixa::saldoTotalPorDia(); pares ainda sem registro até aquele dia
     * não entram na soma.
     *
     * O agrupamento é por empresa+conta, não só por conta: no SALDOCAIXA cada empresa mantém sua
     * própria parcela na mesma conta contábil, e o saldo é cumulativo por empresa (o
     * VL_SALDOANTERIOR de uma linha é o VL_SALDOCAIXA da linha anterior da mesma empresa+conta).
     * Agrupar só por conta faria a empresa que movimentou por último "apagar" as parcelas das
     * demais, que continuam existindo mesmo sem movimento recente.
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function saldoTotalPorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porEmpresaConta = collect(self::buscar(null, end($dias)->format('Y-m-d')))
            ->map(fn ($linha) => (object) [
                'chave' => $linha->CD_EMPRESA . '|' . $linha->CD_CONTA,
                'vl_saldo' => (float) $linha->VL_SALDO,
                'dt_saldo' => Carbon::parse($linha->DT_SALDO),
            ])
            ->groupBy('chave');

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $total = 0.0;
            foreach ($porEmpresaConta as $linhasConta) {
                $ateODia = $linhasConta->filter(fn ($linha) => $linha->dt_saldo->lte($dia));
                if ($ateODia->isEmpty()) {
                    continue;
                }

                $ultimaData = $ateODia->max('dt_saldo');
                $total += $ateODia->filter(fn ($linha) => $linha->dt_saldo->equalTo($ultimaData))->sum('vl_saldo');
            }
            $resultado[$i] = $total;
        }

        return $resultado;
    }

    /**
     * Soma dos saldos (todas as contas configuradas) com DT_CAIXA EXATAMENTE em cada dia — 0
     * nos dias sem nenhum registro. Mesmo formato de SaldoFluxoCaixa::valorLancadoPorDia().
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function valorLancadoPorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = collect(self::buscar(reset($dias)->format('Y-m-d'), end($dias)->format('Y-m-d')))
            ->groupBy(fn ($linha) => Carbon::parse($linha->DT_SALDO)->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $chave = $dia->format('Y-m-d');
            $resultado[$i] = $porData->has($chave) ? (float) $porData->get($chave)->sum('VL_SALDO') : 0.0;
        }

        return $resultado;
    }

    /**
     * Marca, para cada dia informado, se existe algum registro com DT_CAIXA exatamente
     * naquele dia. Mesmo formato de SaldoFluxoCaixa::diasComLancamento().
     *
     * @param  Carbon[] $dias
     * @return array<int, bool>
     */
    public static function diasComLancamento(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $datasComLancamento = collect(self::buscar(reset($dias)->format('Y-m-d'), end($dias)->format('Y-m-d')))
            ->map(fn ($linha) => Carbon::parse($linha->DT_SALDO)->format('Y-m-d'))
            ->flip();

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $resultado[$i] = $datasComLancamento->has($dia->format('Y-m-d'));
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
     * Para cada dia informado, lista os registros por conta (CD_CONTA/DS_BANCO/VL_SALDO) com
     * DT_CAIXA EXATAMENTE naquele dia — mesmo formato de SaldoFluxoCaixa::detalhePorDia(), usado
     * no drill-down. Como SALDOCAIXA não tem um id de linha próprio, sintetiza um a partir de
     * CD_CONTA+DT_CAIXA (só pra identificação na tela — não é editável/excluível como o saldo
     * digitado).
     *
     * @param  Carbon[] $dias
     * @return array<int, array<int, array{id:string, ds_banco:string, vl_saldo:float, dt_saldo:string, dt_saldo_formatada:string}>>
     */
    public static function detalhePorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = collect(self::buscar(reset($dias)->format('Y-m-d'), end($dias)->format('Y-m-d')))
            ->groupBy(fn ($linha) => Carbon::parse($linha->DT_SALDO)->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $linhasDoDia = $porData->get($dia->format('Y-m-d'), collect());
            $resultado[$i] = $linhasDoDia->map(fn ($linha) => [
                'id' => $linha->CD_CONTA . '-' . Carbon::parse($linha->DT_SALDO)->format('Ymd'),
                'ds_banco' => $linha->DS_BANCO,
                'vl_saldo' => (float) $linha->VL_SALDO,
                'dt_saldo' => Carbon::parse($linha->DT_SALDO)->format('Y-m-d'),
                'dt_saldo_formatada' => Carbon::parse($linha->DT_SALDO)->format('d/m/Y'),
            ])->values()->all();
        }

        return $resultado;
    }

    /**
     * Soma dos registros do dia mais recente (DT_CAIXA) que tem algum saldo, entre as contas
     * configuradas. Mesmo formato de SaldoFluxoCaixa::saldoUltimoDiaLancado().
     *
     * Ignora datas futuras: o card que consome isso é "Saldo Banco(s) Hoje", e o SALDOCAIXA
     * pode conter lançamento com data à frente (visto na base: registros até 31/12). No saldo
     * digitado isso não acontece porque salvarSaldoBanco() valida a data na entrada — aqui o
     * dado vem de fora, sem passar por validação nenhuma, então o filtro é aplicado na leitura.
     */
    public static function saldoUltimoDiaLancado(): float
    {
        $contas = self::contasConfiguradas();

        if (empty($contas)) {
            return 0.0;
        }

        [$bindings, $placeholders] = self::bindingsContas($contas);

        $row = DB::connection('firebird')->selectOne("
            SELECT MAX(S.DT_CAIXA) DT_MAX
            FROM SALDOCAIXA S
            WHERE S.CD_CONTA IN (" . implode(', ', $placeholders) . ")
                  AND S.DT_CAIXA <= CURRENT_DATE
        ", $bindings);

        $dtMax = $row->DT_MAX ?? $row->dt_max ?? null;

        if ($dtMax === null) {
            return 0.0;
        }

        return self::valorLancadoPorDia([Carbon::parse($dtMax)])[0] ?? 0.0;
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
