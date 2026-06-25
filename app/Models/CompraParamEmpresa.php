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
            SELECT
                C.CD_EMPRESA,
                C.ST_USA_CENTROCUSTO,
                C.QTD_FORNEC_COT,
                C.CD_PESSOA_COMPRA,
                P.NM_PESSOA NM_COMPRADOR,
                EP.NR_CELULAR
            FROM COMPRA_PARAM_EMPRESA C
            LEFT JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA_COMPRA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
                AND EP.CD_ENDERECO = 1) 
        ");
    }

    public function getCompradorByEmpresa(int $cdEmpresa): ?object
    {
        return DB::connection('firebird')->selectOne("
            SELECT C.CD_PESSOA_COMPRA, P.NM_PESSOA NM_COMPRADOR, EP.NR_CELULAR
            FROM COMPRA_PARAM_EMPRESA C
            LEFT JOIN PESSOA P  ON (P.CD_PESSOA  = C.CD_PESSOA_COMPRA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA AND EP.CD_ENDERECO = 1)
            WHERE C.CD_EMPRESA = :cd
        ", ['cd' => $cdEmpresa]);
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

    public function updateCotacoesParam(int $cdEmpresa, int $qtd, ?int $cdPessoa): void
    {
        DB::connection('firebird')->statement("
            UPDATE OR INSERT INTO COMPRA_PARAM_EMPRESA (CD_EMPRESA, QTD_FORNEC_COT, CD_PESSOA_COMPRA)
            VALUES (:cd, :qtd, :pessoa) MATCHING (CD_EMPRESA)
        ", ['cd' => $cdEmpresa, 'qtd' => $qtd, 'pessoa' => $cdPessoa]);
    }
}
