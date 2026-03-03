<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    public function getGroupItem()
    {
        $query = "
                SELECT
                    G.CD_GRUPO,
                    G.DS_GRUPO
                FROM GRUPO G ";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function servicoPneu($idMedidaPneu = null, $idItem = null)
    {
        $query = "
                SELECT
                    SP.ID,
                    SP.DSSERVICO,
                    SP.IDBANDAPNEU,
                    SP.IDITEMCARCACA,
                    BP.ID IDBANDAPNEU,                    
                    DP.ID IDDESENHOPNEU                                    
                FROM SERVICOPNEU SP
                LEFT JOIN BANDAPNEU BP ON (BP.ID = SP.IDBANDAPNEU)
                LEFT JOIN DESENHOPNEU DP ON (DP.ID = BP.IDDESENHOPNEU)
                WHERE
                    " . (!$idItem == null ? "SP.ID = $idItem" : "1=1") . " 
                    " . (!$idMedidaPneu == null ? "AND SP.IDMEDIDAPNEU = $idMedidaPneu" : "") . "
                                     
                    ";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function ItemFind($cd_barra)
    {
        $this->connection = 'mysql';

        return Item::where('cd_codbarraemb', $cd_barra)->firstOr(function () {
            return 0;
        });
    }

    public function FindProdutoAll($term)
    {
        $this->connection = 'mysql';

        return Item::where('ds_item', 'like', '%' . $term . '%')->where('st_ativo', 'S')
            ->limit(10)->get();
    }
}
