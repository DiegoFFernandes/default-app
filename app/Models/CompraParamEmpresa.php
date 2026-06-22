<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraParamEmpresa extends Model
{
    use HasFactory;

    public function getAll()
    {
        return DB::connection('firebird')->select("
            SELECT CD_EMPRESA, ST_USA_CENTROCUSTO, QTD_FORNEC_COT
            FROM COMPRA_PARAM_EMPRESA
        ");
    }

    public function getMapUsaCentrocusto(): array
    {
        $rows = $this->getAll();
        return collect($rows)->pluck('ST_USA_CENTROCUSTO', 'CD_EMPRESA')->toArray();
    }

    public function getQtdFornecCot(int $cdEmpresa, int $default = 3): int
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT QTD_FORNEC_COT FROM COMPRA_PARAM_EMPRESA WHERE CD_EMPRESA = :cd
        ", ['cd' => $cdEmpresa]);

        $qtd = $row->QTD_FORNEC_COT ?? null;
        return ($qtd !== null && $qtd > 0) ? (int) $qtd : $default;
    }

    public function upsert(int $cdEmpresa, string $st): void
    {
        DB::connection('firebird')->statement("
            UPDATE OR INSERT INTO COMPRA_PARAM_EMPRESA (CD_EMPRESA, ST_USA_CENTROCUSTO)
            VALUES (:cd, :st) MATCHING (CD_EMPRESA)
        ", ['cd' => $cdEmpresa, 'st' => $st]);
    }

    public function updateQtdFornecCot(int $cdEmpresa, int $qtd): void
    {
        DB::connection('firebird')->statement("
            UPDATE OR INSERT INTO COMPRA_PARAM_EMPRESA (CD_EMPRESA, QTD_FORNEC_COT)
            VALUES (:cd, :qtd) MATCHING (CD_EMPRESA)
        ", ['cd' => $cdEmpresa, 'qtd' => $qtd]);
    }
}
