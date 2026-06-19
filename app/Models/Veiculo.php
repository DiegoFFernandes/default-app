<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Veiculo
{
    public static function search(string $q, int $limit = 20): array
    {
        $q = strtoupper(trim($q));

        if (strlen($q) < 2) {
            return [];
        }

        try {
            $rows = DB::connection('firebird')->select("
                SELECT FIRST {$limit}
                    V.NR_PLACA,
                    COALESCE(M.DS_MODELOVEICULO, 'SEM MODELO') DS_MODELO,
                    COALESCE(MARCAVEICULO.DS_MARCAVEICULO, 'SEM MARCA') DS_MARCA
                FROM VEICULO V
                LEFT JOIN MODELOVEICULO M
                       ON M.CD_MODELOVEICULO = V.CD_MODELOVEICULO
                      AND M.CD_MARCAVEICULO  = V.CD_MARCAVEICULO
                LEFT JOIN MARCAVEICULO
                       ON MARCAVEICULO.CD_MARCAVEICULO = V.CD_MARCAVEICULO
                WHERE V.NR_PLACA CONTAINING ?
            ", [$q]);

            return array_map(function ($r) {
                $placa  = trim($r->NR_PLACA ?? $r->nr_placa ?? '');
                $marca  = trim($r->DS_MARCA  ?? $r->ds_marca  ?? '');
                $modelo = trim($r->DS_MODELO ?? $r->ds_modelo ?? '');
                return [
                    'id'   => $placa,
                    'text' => $placa . ' — ' . $marca . ' ' . $modelo,
                ];
            }, $rows);
        } catch (\Exception) {
            return [];
        }
    }
}
