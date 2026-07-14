<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FluxoCaixaLancAvulso extends Model
{
    protected $table = 'fluxo_caixa_lanc_avulso';

    protected $fillable = [
        'tipo',
        'dt_lancamento',
        'cd_pessoa',
        'nm_pessoa',
        'vl_documento',
        'cd_tipoconta',
        'ds_tipoconta',
        'cd_formapagto',
        'ds_formapagto',
        'updated_by',
    ];

    protected $casts = [
        'vl_documento' => 'decimal:2',
        'dt_lancamento' => 'date',
    ];

    /**
     * Soma dos lançamentos avulsos (do tipo informado) com dt_lancamento EXATAMENTE em cada
     * dia — 0 nos dias sem nenhum lançamento. Mesmo formato de SaldoFluxoCaixa::valorLancadoPorDia().
     *
     * @param  \Carbon\Carbon[] $dias
     * @return array<int, float>
     */
    public static function valorPorDia(array $dias, string $tipo): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = self::where('tipo', $tipo)
            ->whereBetween('dt_lancamento', [
                reset($dias)->copy()->startOfDay(),
                end($dias)->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn ($lancamento) => $lancamento->dt_lancamento->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $chave = $dia->format('Y-m-d');
            $resultado[$i] = $porData->has($chave) ? (float) $porData->get($chave)->sum('vl_documento') : 0.0;
        }

        return $resultado;
    }

    /**
     * Para cada dia informado, lista os lançamentos avulsos (do tipo informado) com
     * dt_lancamento EXATAMENTE naquele dia — usado no hover da linha "Lançamento Manual" pra
     * mostrar o que existe e permitir editar/excluir. Mesmo formato de SaldoFluxoCaixa::detalhePorDia().
     *
     * @param  \Carbon\Carbon[] $dias
     * @return array<int, array<int, array>>
     */
    public static function detalhePorDia(array $dias, string $tipo): array
    {
        if (empty($dias)) {
            return [];
        }

        $porData = self::where('tipo', $tipo)
            ->whereBetween('dt_lancamento', [
                reset($dias)->copy()->startOfDay(),
                end($dias)->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn ($lancamento) => $lancamento->dt_lancamento->format('Y-m-d'));

        $resultado = [];
        foreach ($dias as $i => $dia) {
            $lancamentosDoDia = $porData->get($dia->format('Y-m-d'), collect());
            $resultado[$i] = $lancamentosDoDia->map(fn (self $lancamento) => [
                'id' => $lancamento->id,
                'tipo' => $lancamento->tipo,
                'dt_lancamento' => $lancamento->dt_lancamento->format('Y-m-d'),
                'cd_pessoa' => $lancamento->cd_pessoa,
                'nm_pessoa' => $lancamento->nm_pessoa,
                'vl_documento' => (float) $lancamento->vl_documento,
                'cd_tipoconta' => $lancamento->cd_tipoconta,
                'ds_tipoconta' => $lancamento->ds_tipoconta,
                'cd_formapagto' => $lancamento->cd_formapagto,
                'ds_formapagto' => $lancamento->ds_formapagto,
            ])->values()->all();
        }

        return $resultado;
    }
}
