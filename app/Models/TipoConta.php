<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class TipoConta
{
    /**
     * @param  string|null $tipo 'pagar' (TP_TIPOCONTA IN ('CP','HP')), 'receber'
     *                           (TP_TIPOCONTA IN ('CR','HR')) ou null (sem filtro).
     */
    public static function all(?string $tipo = null): array
    {
        try {
            $filtroTipo = match ($tipo) {
                'pagar' => "AND TC.TP_TIPOCONTA IN ('CP', 'HP', 'PP', 'AC')",
                'receber' => "AND TC.TP_TIPOCONTA IN ('CR', 'HR', 'CT', 'PR')",
                default => '',
            };

            $rows = DB::connection('firebird')->select("
                SELECT TC.CD_TIPOCONTA, TC.DS_TIPOCONTA
                FROM TIPOCONTA TC
                WHERE 1 = 1
                {$filtroTipo}
                ORDER BY TC.DS_TIPOCONTA
            ");

            $rows = \Helper::ConvertFormatText((array) $rows);

            return array_map(fn ($r) => [
                'id'   => $r->CD_TIPOCONTA ?? $r->cd_tipoconta ?? null,
                'text' => $r->DS_TIPOCONTA ?? $r->ds_tipoconta ?? '',
            ], $rows);
        } catch (\Exception) {
            return [];
        }
    }
}
