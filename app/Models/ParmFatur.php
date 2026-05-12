<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ParmFatur extends Model
{
    use HasFactory;

    protected $table = 'PARMFATUR';

    static function getParmFatur(int $cdEmpresa)
    {
        $query = "
                SELECT
                    P.CD_EMPRESA,
                    P.CD_TABCOMPRA,
                    P.CD_TABPRECO
                FROM PARMFATUR P
                WHERE P.CD_EMPRESA = ?";

        $data = DB::connection('firebird')
            ->select($query, [$cdEmpresa]);

        return Helper::ConvertFormatText($data);
    }
}
