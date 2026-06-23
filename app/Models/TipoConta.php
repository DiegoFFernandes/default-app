<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class TipoConta
{
    public static function all(): array
    {
        try {
            $rows = DB::connection('firebird')->select("
                SELECT TC.CD_TIPOCONTA, TC.DS_TIPOCONTA
                FROM TIPOCONTA TC
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
