<?php

namespace App\Models;

use Carbon\Carbon;
use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
                INNER JOIN ITEM ON (ITEM.CD_ITEM = SP.ID)
                LEFT JOIN BANDAPNEU BP ON (BP.ID = SP.IDBANDAPNEU)
                LEFT JOIN DESENHOPNEU DP ON (DP.ID = BP.IDDESENHOPNEU)
                WHERE
                    " . (!$idItem == null ? "SP.ID = $idItem" : "1=1") . " 
                    " . (!$idMedidaPneu == null ? "AND SP.IDMEDIDAPNEU = $idMedidaPneu" : "") . "   
                    AND SP.STATIVO = 'S'
                    AND ITEM.ST_ATIVO = 'S'                                   
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

    public function ImportaItemJunsoft($cd_marca, $isValidSubgrupoReformaCarga)
    {       
        
        $bindings = [
            'cd_marca' => $cd_marca            
        ];

        $cd_subgrupo = $isValidSubgrupoReformaCarga['data'];
    
        $query = "
            SELECT
                I.CD_CODBARRAEMB,
                I.CD_ITEM,
                I.DS_ITEM,
                I.PS_LIQUIDO,
                I.SG_UNIDMED,
                I.CD_SUBGRUPO,
                I.CD_MARCA,
                I.DT_REGISTRO,
                I.ST_ATIVO
            FROM ITEM I
            WHERE I.CD_MARCA = :cd_marca
                AND I.TP_ITEM IN ('I', 'S', 'P')                
                AND I.CD_SUBGRUPO IN ($cd_subgrupo)
                AND I.ST_ATIVO = 'S'   
      ";

        $itens = DB::connection('firebird')->select($query, $bindings);

        $status = $this->InsertItem($itens);
        if ($status == 1) {
            return 1;
        } else {
            return $status;
        }
    }

    public function InsertItem($itens)
    {
        $this->connection = 'mysql';

        foreach ($itens as $i) {
            try {
                Item::updateOrInsert(
                    ['cd_item' => $i->CD_ITEM],
                    [
                        'cd_codbarraemb' => $i->CD_CODBARRAEMB,
                        'cd_item' => $i->CD_ITEM,
                        'ds_item' => $i->DS_ITEM,
                        'ps_liquido' => $i->PS_LIQUIDO,
                        'sg_unidmed' => $i->SG_UNIDMED,
                        'cd_subgrupo' => $i->CD_SUBGRUPO,
                        'cd_marca' => $i->CD_MARCA,
                        'st_ativo' => $i->ST_ATIVO,
                        'cd_usuario' => Auth::user()->id,
                        'created_at' => $i->DT_REGISTRO,
                        'updated_at' => Carbon::now()->format('Y-m-d H:m:s')
                    ]
                );
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return 1;
    }
}
