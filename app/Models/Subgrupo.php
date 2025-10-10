<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subgrupo extends Model
{
    use HasFactory;

    public function subgrupoAll()
    {
       $query = "
                SELECT
                    CD_SUBGRUPO,
                    CD_SUBGRUPO||' - '||DS_SUBGRUPO DS_SUBGRUPO
                FROM SUBGRUPO
                ORDER BY DS_SUBGRUPO
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
