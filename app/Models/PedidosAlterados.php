<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Helper;

class PedidosAlterados extends Model
{
    use HasFactory;

    public function getPedidosAlterados($statusFaturamento)
    {
        $query = "
            SELECT
                PP.IDEMPRESA,
                P.NM_PESSOA,
                CASE
                WHEN OPR.STORDEM = 'A' THEN 'EM PRODUCAO'
                WHEN OPR.STORDEM = 'F' THEN 'FINALIZADA'
                ELSE OPR.STORDEM
                END STORDEM,
                TD.R_IDORDEMPRODUCAORECAO NR_ORDEM,
                IPP.ID IDITEMPEDIDOPNEU,

                TD.R_IDSERVANTIGO,
                SA.DSSERVICO DS_VELHO,

                SN.ID IDSERVNOVO,
                SN.DSSERVICO DS_NOVO,

                --DN.DSDESENHO || ' ' || BN.NRLARGURA DSDESENHO_NOVO,
                --DA.DSDESENHO || ' ' || BA.NRLARGURA DSDESENHO_VELHO,

                CAST(TD.R_VLSERVANTIGO AS NUMERIC(16,2)) VLSERVCOLETA,
                IPP.VLUNITARIO VLSERVALTERADO,
                TD.R_DTREGISTRO,
                N.NR_NOTAFISCAL,

                PLUG.CD_EMPRESA,
                PLUG.NR_PEDIDO,
                PLUG.TP_PEDIDO

            FROM ORDEMPRODUCAORECAP OPR
            LEFT JOIN RETORNA_ULTIMATROCASERVICO(OPR.ID) TD ON (1 = 1)
            LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
            LEFT JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
            LEFT JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
                -- SERVICO NOVO
            LEFT JOIN SERVICOPNEU SN ON (SN.ID = IPP.IDSERVICOPNEU)
            LEFT JOIN BANDAPNEU BN ON (BN.ID = SN.IDBANDAPNEU)
                --LEFT JOIN DESENHOPNEU DN ON (DN.ID = BN.IDDESENHOPNEU)

                --SERVICO ORIGEM
            LEFT JOIN SERVICOPNEU SA ON (SA.ID = TD.R_IDSERVANTIGO)
            LEFT JOIN BANDAPNEU BA ON (BA.ID = SA.IDBANDAPNEU)
                --LEFT JOIN DESENHOPNEU DA ON (DA.ID = BA.IDDESENHOPNEU)

            LEFT JOIN PLUGORDRECAPPEDIDO PLUG ON (PLUG.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN RETORNA_CHAVENOTA(PLUG.CD_EMPRESA, PLUG.NR_PEDIDO, PLUG.TP_PEDIDO) RCN ON (1 = 1)
            LEFT JOIN NOTA N ON (N.CD_EMPRESA = RCN.O_CD_EMPRESA
                AND N.NR_LANCAMENTO = RCN.O_NR_LANCAMENTO
                AND N.TP_NOTA = RCN.O_TP_NOTA
                AND N.CD_SERIE = RCN.O_CD_SERIE)

            LEFT JOIN ITEM ON (ITEM.CD_ITEM = RCN.O_CD_ITEM
                AND ITEM.CD_GRUPO NOT IN (104))

            WHERE BA.IDDESENHOPNEU = BN.IDDESENHOPNEU
                AND CAST(TD.R_DTREGISTRO AS DATE) > CURRENT_DATE - 7
                --AND PP.ID = 234495
                AND TD.R_VLSERVANTIGO <> IPP.VLUNITARIO
                AND OPR.DTENTRADA >= '01.08.2025'
                AND OPR.STORDEM NOT IN ('C')
                --AND PLUG.NR_PEDIDO IS NULL
                AND N.NR_NOTAFISCAL IS NULL
                --AND IPP.ID = 855237    
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
