<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixaSaldoDia extends Model
{
    protected $table = 'fluxo_caixa_saldo_dia';

    protected $fillable = [
        'dt_saldo',
        'origem',
        'vl_saldo',
    ];

    protected $casts = [
        'vl_saldo' => 'decimal:2',
        'dt_saldo' => 'date',
    ];

    /**
     * Grava (ou atualiza) o Saldo do Dia calculado para cada data exibida — serve de cache
     * para ancorar a semana seguinte quando o usuário navegar pra frente.
     *
     * O cache é por origem ('digitado'/'firebird', ver fluxo_caixa_config): o Saldo do Dia
     * depende de qual fonte alimentou o Saldo Banco, então as duas convivem sem se sobrescrever
     * — trocar o toggle e voltar reaproveita o cache anterior em vez de ancorar num valor da
     * outra fonte.
     *
     * @param  Carbon[]        $dias
     * @param  array<int,float> $valores
     */
    public static function salvarLote(array $dias, array $valores, string $origem): void
    {
        if (empty($dias)) {
            return;
        }

        $linhas = [];
        foreach ($dias as $i => $dia) {
            $linhas[] = [
                'dt_saldo' => $dia->format('Y-m-d'),
                'origem' => $origem,
                'vl_saldo' => $valores[$i] ?? 0.0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        self::upsert($linhas, ['dt_saldo', 'origem'], ['vl_saldo', 'updated_at']);
    }

    /**
     * Busca o Saldo do Dia já calculado/persistido para uma data e origem específicas. Retorna
     * null se nunca foi calculado (ex: a data nunca foi exibida na tela naquela origem).
     */
    public static function buscarPorData(Carbon $data, string $origem): ?float
    {
        $registro = self::whereDate('dt_saldo', $data->format('Y-m-d'))
            ->where('origem', $origem)
            ->first();

        return $registro ? (float) $registro->vl_saldo : null;
    }
}
