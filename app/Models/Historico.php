<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Historico
{
    public static function byTipoConta(int $cdTipoConta): array
    {
        try {
            $rows = DB::connection('firebird')->select("
                SELECT H.CD_HISTORICO, H.DS_HISTORICO
                FROM HISTORICO H
                WHERE H.CD_TIPOCONTA = ?
                  AND H.ST_ATIVO = 'S'
                ORDER BY H.DS_HISTORICO
            ", [$cdTipoConta]);

            $rows = \Helper::ConvertFormatText((array) $rows);

            return array_map(fn ($r) => [
                'id'   => $r->CD_HISTORICO ?? $r->cd_historico ?? null,
                'text' => $r->DS_HISTORICO ?? $r->ds_historico ?? '',
            ], $rows);
        } catch (\Exception) {
            return [];
        }
    }
}
