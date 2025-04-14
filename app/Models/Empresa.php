<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use HasFactory;

    public function empresa()
    {
        $query = "SELECT
                    EMPRESA.CD_EMPRESA,
                    EMPRESA.NM_EMPRESA
                FROM EMPRESA";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
