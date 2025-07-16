<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use HasFactory;

    public function empresa($empresa = 0)
    {
        //Se usuario por admin retorna todas as empresas
        if ($empresa == 0) {
            $empresa = '1,3,5,6';
        }
        $query = "SELECT
                    EMPRESA.CD_EMPRESA,
                    CASE 
                        WHEN EMPRESA.CD_EMPRESA = 1 THEN 'Cambe'
                        WHEN EMPRESA.CD_EMPRESA = 2 THEN '2'
                        WHEN EMPRESA.CD_EMPRESA = 3 THEN 'Osvaldo Cruz'
                        WHEN EMPRESA.CD_EMPRESA = 4 THEN '4'
                        WHEN EMPRESA.CD_EMPRESA = 5 THEN 'Ponta Grossa'
                        WHEN EMPRESA.CD_EMPRESA = 6 THEN 'Catanduva'
                        ELSE 'OUTROS'
                    END NM_EMPRESA
                FROM EMPRESA
                WHERE EMPRESA.CD_EMPRESA IN ($empresa)";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
