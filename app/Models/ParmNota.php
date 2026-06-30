<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ParmNota extends Model
{
    protected $connection = 'firebird';

    public function getSeriesNota(): array
    {
        return DB::connection('firebird')->select("
            SELECT DISTINCT P.CD_SERIE
            FROM PARMNOTA P
            WHERE P.TP_NOTA = 'S'
            ORDER BY P.CD_SERIE
        ");
    }
}
