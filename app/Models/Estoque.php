<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Estoque extends Model
{
    use HasFactory;

    public function getEstoqueNegativo()
    {
        $query = "
            SELECT
                1 CD_EMPRESA,
                ITEM.CD_ITEM,
                ITEM.DS_ITEM,
                CAST(R.O_QT_SALDO AS NUMERIC(18)) QTD_SALDO,
                R.O_VL_CUSTO
            FROM ITEM
            INNER JOIN RETORNA_SALDOESTOQUE(1, ITEM.CD_ITEM, 1, 1, CURRENT_DATE, NULL) R ON (1 = 1)
            WHERE ITEM.CD_GRUPO = 113
                AND ITEM.ST_ATIVO = 'S'
                AND R.O_QT_SALDO < 0

                 
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    
    }
}
