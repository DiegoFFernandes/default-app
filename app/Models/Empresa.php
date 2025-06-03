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
                    CASE 
                        WHEN EMPRESA.CD_EMPRESA = 1 THEN 'CAMBE'
                        WHEN EMPRESA.CD_EMPRESA = 2 THEN '2'
                        WHEN EMPRESA.CD_EMPRESA = 3 THEN 'OSVALDO CRUZ'
                        WHEN EMPRESA.CD_EMPRESA = 4 THEN '4'
                        WHEN EMPRESA.CD_EMPRESA = 5 THEN 'PONTA GROSSA'
                        WHEN EMPRESA.CD_EMPRESA = 6 THEN 'CATANDUVA'                       
                        ELSE 'OUTROS'
                    END NM_EMPRESA
                FROM EMPRESA";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
