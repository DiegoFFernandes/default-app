<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FormaPagamento extends Model
{
    use HasFactory;

    public function getCondicaoPagamento()
    {
        $query = "
            SELECT
                CP.CD_CONDPAGTO,
                CP.DS_CONDPAGTO
            FROM CONDPAGTO CP
            WHERE
                CP.ST_ATIVO = 'S'   
            ORDER BY CP.DS_CONDPAGTO";

        $data = DB::connection('firebird')
            ->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getFormaPagamento()
    {
        $query = "
            SELECT
                FP.CD_FORMAPAGTO,
                FP.DS_FORMAPAGTO
            FROM FORMAPAGTO FP";

        $data = DB::connection('firebird')
            ->select($query);

        return Helper::ConvertFormatText($data);
    }
}
