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
        return $this->buscarMedidas("%COMPLETO $search%");
    }

    public function searchMedidas($search)
    {
        return $this->buscarMedidas("%$search%");
    }

    private function buscarMedidas($like)
    {
        $query = "
            SELECT
                FIRST 10
                ID,
                DSMEDIDAPNEU AS DS_MEDIDA
            FROM MEDIDAPNEU
            WHERE DSMEDIDAPNEU LIKE :like
            ORDER BY DS_MEDIDA";

        $data = DB::connection('firebird')->select($query, ['like' => $like]);

        return Helper::ConvertFormatText($data);
    }
}
