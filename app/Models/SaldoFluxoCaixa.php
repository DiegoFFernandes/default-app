<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SaldoFluxoCaixa extends Model
{
    protected $table = 'fluxo_caixa_saldo';

    protected $fillable = [
        'ds_banco',
        'vl_saldo',
        'dt_saldo',
        'id_user',
    ];

    protected $casts = [
        'vl_saldo' => 'decimal:2',
        'dt_saldo' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Soma dos saldos mais recentes de cada banco na data de hoje.
     */
    public static function saldoTotalAtual(): float
    {
        return self::saldoTotalPorDia([Carbon::now()])[0] ?? 0.0;
    }

    /**
     * Soma dos lançamentos com dt_saldo EXATAMENTE no dia informado (sem repetir valor de
     * dias anteriores). Se não houver nenhum lançamento naquele dia, retorna 0.
     */
    public static function saldoLancadoNoDia(Carbon $dia): float
    {
        return (float) self::whereDate('dt_saldo', $dia->format('Y-m-d'))->sum('vl_saldo');
    }

    /**
     * Verifica se existe algum lançamento com dt_saldo maior ou igual à data informada.
     * Usado para não deixar o saldo real conhecido hoje "vazar" para dentro de uma semana
     * totalmente futura sem nenhuma confirmação relevante para aquele período.
     */
    public static function existeLancamentoAPartirDe(Carbon $data): bool
    {
        return self::where('dt_saldo', '>=', $data->format('Y-m-d'))->exists();
    }

    /**
     * Verifica se existe algum lançamento com dt_saldo menor ou igual à data informada —
     * ou seja, se há algum saldo real pra ancorar o forward-fill até esse dia. Usado pra saber
     * se o dia 0 deve cair no fallback do cache de fluxo_caixa_saldo_dia (nenhum saldo real
     * informado ainda) em vez de simplesmente assumir 0.
     */
    public static function existeLancamentoAte(Carbon $data): bool
    {
        return self::where('dt_saldo', '<=', $data->format('Y-m-d'))->exists();
    }

    /**
     * Para cada dia informado, soma o saldo mais recente conhecido de cada banco — a última
     * inserção com dt_saldo menor ou igual àquele dia (somando quando há mais de uma inserção
     * na mesma data). Bancos ainda sem nenhum saldo registrado até aquele dia não entram na
     * soma. O resultado é um "degrau" que só muda quando surge uma nova inserção real.
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function saldoTotalPorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porBanco = self::where('dt_saldo', '<=', end($dias)->copy()->endOfDay())
            ->orderBy('dt_saldo')
            ->get()
            ->groupBy('ds_banco');

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $total = 0.0;
            foreach ($porBanco as $lancamentosBanco) {
                $ateODia = $lancamentosBanco->filter(fn ($lancamento) => $lancamento->dt_saldo->lte($dia));
                if ($ateODia->isEmpty()) {
                    continue;
                }

                $ultimaData = $ateODia->max('dt_saldo');
                $total += $ateODia->filter(fn ($lancamento) => $lancamento->dt_saldo->equalTo($ultimaData))->sum('vl_saldo');
            }
            $resultado[$i] = $total;
        }

        return $resultado;
    }

    /**
     * Para cada dia informado, soma só os lançamentos com dt_saldo EXATAMENTE naquele dia
     * (0 se não houver nenhum) — diferente de saldoTotalPorDia, que faz forward-fill e
     * incluiria bancos não atualizados naquele dia. Usado pra Saldo Banco mostrar só o que foi
     * lançado no dia, batendo com o que o drill-down exibe.
     *
     * @param  Carbon[] $dias
     * @return array<int, float>
     */
    public static function valorLancadoPorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = self::whereBetween('dt_saldo', [
                reset($dias)->copy()->startOfDay(),
                end($dias)->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn ($lancamento) => $lancamento->dt_saldo->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $chave = $dia->format('Y-m-d');
            $resultado[$i] = $porData->has($chave) ? (float) $porData->get($chave)->sum('vl_saldo') : 0.0;
        }

        return $resultado;
    }

    /**
     * Marca, para cada dia informado, se existe algum lançamento com dt_saldo exatamente
     * naquele dia (diferente do degrau de saldoTotalPorDia, que também considera valores
     * carregados de dias anteriores). Usado para saber em quais dias o Saldo Banco deve usar
     * o valor real informado, em vez do resultado do dia anterior.
     *
     * @param  Carbon[] $dias
     * @return array<int, bool>
     */
    public static function diasComLancamento(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $datasComLancamento = self::whereBetween('dt_saldo', [
                reset($dias)->copy()->startOfDay(),
                end($dias)->copy()->endOfDay(),
            ])
            ->pluck('dt_saldo')
            ->map(fn ($data) => $data->format('Y-m-d'))
            ->flip();

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $resultado[$i] = $datasComLancamento->has($dia->format('Y-m-d'));
        }

        return $resultado;
    }

    /**
     * Para cada dia informado, lista só os lançamentos com dt_saldo EXATAMENTE naquele dia
     * (sem forward-fill de outros bancos/dias) — usado no drill-down ao clicar num valor da
     * linha "Saldo Banco", que só é clicável nos dias com lançamento próprio. Evita misturar
     * lançamentos de outros dias/bancos no mesmo modal.
     *
     * @param  Carbon[] $dias
     * @return array<int, array<int, array{id:int, ds_banco:string, vl_saldo:float, dt_saldo:string, dt_saldo_formatada:string}>>
     */
    public static function detalhePorDia(array $dias): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = self::whereBetween('dt_saldo', [
                reset($dias)->copy()->startOfDay(),
                end($dias)->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn ($lancamento) => $lancamento->dt_saldo->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $lancamentosDoDia = $porData->get($dia->format('Y-m-d'), collect());
            $resultado[$i] = $lancamentosDoDia->map(fn ($lancamento) => [
                'id' => $lancamento->id,
                'ds_banco' => $lancamento->ds_banco,
                'vl_saldo' => (float) $lancamento->vl_saldo,
                'dt_saldo' => $lancamento->dt_saldo->format('Y-m-d'),
                'dt_saldo_formatada' => $lancamento->dt_saldo->format('d/m/Y'),
            ])->values()->all();
        }

        return $resultado;
    }
}
