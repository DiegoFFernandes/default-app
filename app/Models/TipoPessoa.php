<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoPessoa extends Model
{
    use HasFactory;

    public function TipoPessoa(){
        $query = "
            SELECT
                TP.CD_TIPOPESSOA,
                TP.DS_TIPOPESSOA
            FROM TIPOPESSOA TP";
        RETURN DB::connection('firebird')->select($query);
    }
}
