<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MedidaPneu extends Model
{
    use HasFactory;

    public function searchMedidasPneusCasa($search)
    {
        $query = "
            SELECT
                FIRST 10
                ID,
                DSMEDIDAPNEU AS DS_MEDIDA  
            FROM MEDIDAPNEU  
            WHERE DSMEDIDAPNEU LIKE '%COMPLETO $search%'        
            ORDER BY DS_MEDIDA";

        $data = DB::connection('firebird')->select($query);
       
        return Helper::ConvertFormatText($data);
    }
}
