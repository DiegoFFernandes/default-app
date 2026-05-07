<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DesenhoPneu extends Model
{
    use HasFactory;

    public function getDesenhoPneu()
    {
        $query = "
                SELECT
                    DP.ID ID_DESENHO,
                    DP.DSDESENHO
                FROM DESENHOPNEU DP ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
