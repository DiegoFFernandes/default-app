<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Municipio extends Model
{
    use HasFactory;

    /** Busca para o Select2 (por nome ou UF). */
    public function search(string $term)
    {
        $termIso = \Helper::ToIso($term);

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT FIRST 20
                M.CD_MUNICIPIO AS id,
                M.DS_MUNICIPIO || ' - ' || M.SG_ESTADO AS text,
                M.SG_ESTADO,
                M.CD_IBGE
            FROM MUNICIPIO M
            WHERE M.DS_MUNICIPIO CONTAINING :term
            ORDER BY M.DS_MUNICIPIO
        ", ['term' => $termIso]));
    }

    /** Localiza o município do ERP pelo código IBGE (para autopreencher via CNPJ). */
    public function findByIbge(int $cdIbge): ?object
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT M.CD_MUNICIPIO, M.DS_MUNICIPIO, M.SG_ESTADO, M.CD_IBGE
            FROM MUNICIPIO M
            WHERE M.CD_IBGE = :ibge
        ", ['ibge' => $cdIbge]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }
}
