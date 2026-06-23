<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Veiculo
{
    /**
     * Busca um lote de placas com uma única query IN.
     * Retorna mapa indexado pela placa: ['GJW6H72' => ['cd_motorista' => ..., 'marca' => ..., 'modelo' => ...]]
     */
    public static function findByPlacas(array $placas): array
    {
        $placas = array_values(array_unique(array_filter(array_map('trim', $placas))));

        if (empty($placas)) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($placas), '?'));

            $rows = DB::connection('firebird')->select("
                SELECT
                    TRIM(V.NR_PLACA)                                       NR_PLACA,
                    COALESCE(TRIM(MARCAVEICULO.DS_MARCAVEICULO), '')        DS_MARCA,
                    COALESCE(TRIM(M.DS_MODELOVEICULO), '')                  DS_MODELO,
                    COALESCE(V.CD_MOTORISTA, 0)                             CD_MOTORISTA,
                    COALESCE(PESSOA.NM_PESSOA, 'SEM MOTORISTA')             NM_MOTORISTA
                FROM VEICULO V
                LEFT JOIN PESSOA ON (PESSOA.CD_PESSOA = V.CD_MOTORISTA)
                LEFT JOIN MODELOVEICULO M
                       ON M.CD_MODELOVEICULO = V.CD_MODELOVEICULO
                      AND M.CD_MARCAVEICULO  = V.CD_MARCAVEICULO
                LEFT JOIN MARCAVEICULO
                       ON MARCAVEICULO.CD_MARCAVEICULO = V.CD_MARCAVEICULO
                WHERE TRIM(V.NR_PLACA) IN ($placeholders)
            ", $placas);

            $rows = \Helper::ConvertFormatText((array) $rows);

            $mapa = [];
            foreach ($rows as $r) {
                $placa = trim($r->NR_PLACA ?? $r->nr_placa ?? '');
                if ($placa === '') continue;
                $mapa[$placa] = [
                    'cd_motorista' => $r->CD_MOTORISTA  ?? $r->cd_motorista  ?? null,
                    'nm_motorista' => $r->NM_MOTORISTA  ?? $r->nm_motorista  ?? '',
                    'marca'        => $r->DS_MARCA      ?? $r->ds_marca      ?? '',
                    'modelo'       => $r->DS_MODELO     ?? $r->ds_modelo     ?? '',
                ];
            }

            return $mapa;
        } catch (\Exception) {
            return [];
        }
    }

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
                    COALESCE(MARCAVEICULO.DS_MARCAVEICULO, 'SEM MARCA') DS_MARCA,
                    COALESCE(V.CD_MOTORISTA, '9999') CD_MOTORISTA
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
