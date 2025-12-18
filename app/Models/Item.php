<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    public function getGroupItem()
    {
        $query = "
                SELECT
                    G.CD_GRUPO,
                    G.DS_GRUPO
                FROM GRUPO G ";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function servicoPneuMedida($idMedidaPneu)
    {
        $query = "
                SELECT
                    SP.ID,
                    SP.DSSERVICO
                FROM SERVICOPNEU SP
                WHERE
                    SP.IDMEDIDAPNEU = $idMedidaPneu";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
