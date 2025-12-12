<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloPneu extends Model
{
    use HasFactory;

    public function searchModeloPneu($search)
    {
        $query = "
            SELECT FIRST 10
                MP.ID IDMODELO,
                MP.DSMODELO || ' - ' || M.DSMARCA DSMODELO
            FROM MODELOPNEU MP
            INNER JOIN MARCAPNEU M ON (M.ID = MP.IDMARCAPNEU)
            WHERE
                 (MP.DSMODELO LIKE '%$search%' or M.DSMARCA LIKE '%$search%')
                AND MP.ST_ATIVO = 'S'
            ORDER BY MP.DSMODELO";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
