<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixaCompensacao extends Model
{
    protected $table = 'fluxo_caixa_compensacao';

    protected $fillable = [
        'cd_tipoconta',
        'ds_tipoconta',
        'segunda',
        'terca',
        'quarta',
        'quinta',
        'sexta',
        'sabado',
        'domingo',
        'updated_by',
    ];

    /**
     * Todas as regras de compensação cadastradas, indexadas por CD_TIPOCONTA e já no formato
     * [Carbon::MONDAY => dias, ...] esperado por calcularDataPersonalizada() — carregada uma
     * vez por request pra evitar uma consulta por lançamento.
     *
     * @return array<int, array<int,int>>
     */
    public static function offsetPorTipoConta(): array
    {
        return self::all()->mapWithKeys(fn (FluxoCaixaCompensacao $registro) => [
            $registro->cd_tipoconta => [
                Carbon::MONDAY => $registro->segunda,
                Carbon::TUESDAY => $registro->terca,
                Carbon::WEDNESDAY => $registro->quarta,
                Carbon::THURSDAY => $registro->quinta,
                Carbon::FRIDAY => $registro->sexta,
                Carbon::SATURDAY => $registro->sabado,
                Carbon::SUNDAY => $registro->domingo,
            ],
        ])->all();
    }
}
