<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoteExpedicao extends Model
{
    use HasFactory;

    public function ListLoteExpedicao()
    {
        $query = "
            SELECT
                E.ID LOTE,
                E.IDEMPRESA,
                E.IDVENDEDOR,
                V.NM_PESSOA NM_VENDEDOR,
                E.DSOBSERVACAO,
                E.DTLOTE EMISSAO,
                CASE
                WHEN E.STLOTE = 'A' THEN 'ABERTO'
                WHEN E.STLOTE = 'F' THEN 'FINALIZADO'
                ELSE E.STLOTE
                END SITUACAO,
                E.DTREGISTRO
            FROM EXPEDICAOLOTEPNEU E
            INNER JOIN PESSOA V ON (V.CD_PESSOA = E.IDVENDEDOR)
            WHERE
                E.DTREGISTRO >= '04.04.2025'
                AND E.IDEMPRESA = 1
                AND E.STLOTE NOT IN ('C')
            ORDER BY E.DTREGISTRO DESC";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
