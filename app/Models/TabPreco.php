<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TabPreco extends Model
{
    use HasFactory;

    public function getTabpreco()
    {
        $query = "
            SELECT
                T.CD_TABPRECO,
                T.DS_TABPRECO,
                COUNT(I.cd_item) QTD_ITENS
            FROM TABPRECO T
            INNER JOIN ITEMTABPRECO I ON (I.CD_TABPRECO = T.CD_TABPRECO)
            GROUP BY T.CD_TABPRECO,
                T.DS_TABPRECO
            ORDER BY T.CD_TABPRECO
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getItemTabPreco($cd_tabela)
    {
        $query = "
            SELECT
                I.CD_TABPRECO,
                I.CD_ITEM||' - '||ITEM.DS_ITEM DS_ITEM,
                CAST(I.VL_PRECO AS numeric(12,2)) VL_PRECO
            FROM ITEMTABPRECO I
            INNER JOIN ITEM  ON (ITEM.CD_ITEM = I.CD_ITEM)
            WHERE I.cd_tabpreco = $cd_tabela
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
