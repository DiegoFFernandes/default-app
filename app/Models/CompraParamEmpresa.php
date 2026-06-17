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
            SELECT CD_EMPRESA, ST_USA_CENTROCUSTO
            FROM COMPRA_PARAM_EMPRESA
        ");
    }

    public function getMapUsaCentrocusto(): array
    {
        $rows = $this->getAll();
        return collect($rows)->pluck('ST_USA_CENTROCUSTO', 'CD_EMPRESA')->toArray();
    }

    public function upsert(int $cdEmpresa, string $st): void
    {
        DB::connection('firebird')->statement("
            UPDATE OR INSERT INTO COMPRA_PARAM_EMPRESA (CD_EMPRESA, ST_USA_CENTROCUSTO)
            VALUES (:cd, :st) MATCHING (CD_EMPRESA)
        ", ['cd' => $cdEmpresa, 'st' => $st]);
    }
}
