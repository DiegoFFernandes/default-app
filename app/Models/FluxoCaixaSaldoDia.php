<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixaSaldoDia extends Model
{
    protected $table = 'fluxo_caixa_saldo_dia';

    protected $fillable = [
        'dt_saldo',
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
     * @param  Carbon[]        $dias
     * @param  array<int,float> $valores
     */
    public static function salvarLote(array $dias, array $valores): void
    {
        if (empty($dias)) {
            return;
        }

        $linhas = [];
        foreach ($dias as $i => $dia) {
            $linhas[] = [
                'dt_saldo' => $dia->format('Y-m-d'),
                'vl_saldo' => $valores[$i] ?? 0.0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        self::upsert($linhas, ['dt_saldo'], ['vl_saldo', 'updated_at']);
    }

    /**
     * Busca o Saldo do Dia já calculado/persistido para uma data específica. Retorna null se
     * nunca foi calculado (ex: a data nunca foi exibida na tela).
     */
    public static function buscarPorData(Carbon $data): ?float
    {
        $registro = self::whereDate('dt_saldo', $data->format('Y-m-d'))->first();

        return $registro ? (float) $registro->vl_saldo : null;
    }
}
