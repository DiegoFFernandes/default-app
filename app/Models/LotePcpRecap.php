<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LotePcpRecap extends Model
{
    use HasFactory;

    public function verificaPneusLotePcpRecapAberto($nrLote)
    {
        $query = "
           SELECT
                COUNT(OPR.STORDEM) OP_ABERTA
            FROM LOTEPCPORDEMPRODUCAORECAP LPP
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = LPP.IDORDEMPRODUCAO)
            WHERE LPP.IDMONTAGEMLOTEPCP = :nrLote
            AND OPR.STORDEM = 'A'            
            GROUP BY OPR.STORDEM
        ";

        return DB::connection('firebird')->select(
            $query,
            ['nrLote' => $nrLote]
        )[0]->OP_ABERTA ?? 0;
    }

    public function fecharLotePcpRecap(int $nrLote, int $cdEmpresa)
    {
        $query = "
            UPDATE MONTAGEMLOTEPCPRECAP
            SET STLOTE = 'F'
            WHERE ID = :nrLote
            AND IDEMPRESA = :cdEmpresa
        ";

        return DB::connection('firebird')->update(
            $query,
            [
                'nrLote' => $nrLote,
                'cdEmpresa' => $cdEmpresa
            ]
        );
    }
}
