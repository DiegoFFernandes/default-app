<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Garantia extends Model
{
    use HasFactory;

    public function getGarantia()
    {
        $query = "
            SELECT
                PP.IDTIPOPEDIDO,
                LP.NR_LAUDO,
                LP.DT_LAUDO,
                COALESCE(LP.CD_PESSOA, PP.IDPESSOA) || '-' || PESSOA.NM_PESSOA NM_PESSOA,
                --LP.CD_VENDEDOR,
                LP.TP_LAUDO,
                --LP.TP_REFORMA,
                LP.ST_LAUDO,
                LP.IDEMPRELAUDO,
                COALESCE(CL.PC_CLASSIFICACAO, 0) PC_CLASSIFICACAO,
                --CL.DT_REGISTRO,
                CAST(LP.VL_SERVICO AS NUMERIC(12,2)) VL_SERVICO,
                CAST(COALESCE(CL.VL_CLASSIFICACAO, 0) AS NUMERIC(12,2)) VL_CLASSIFICACAO,
                --CL.ID_ORDEMPRODUCAORECAP,
                COALESCE(LP.IDITEMPEDIDOPNEUORIGEM, LP.IDITEMPEDIDOPNEURECUSA) ITEMPEDIDO,
                COALESCE(LP.CD_MARCA, MDP.IDMARCAPNEU) CD_MARCA,
                --MP.ID_MOTIVO||' - '||
                M.DSMOTIVO DSMOTIVO,
                --M.TPMOTIVO,
                --M.TPCAUSALAUDO,
                CG.CD_CLASSIFICACAO,
                CG.DS_CLASSIFICACAO,
                OPR.ID NR_ORDEM,
                U.CD_USUARIO || ' - ' || U.NM_USUARIO NM_USUARIO
            FROM LAUDOTECNICOPNEURECAP LP
            LEFT JOIN CLASSIFICACAOLAUDO CL ON (CL.ID_LAUDOTECNICO = LP.ID)
            LEFT JOIN CLASSIFICACAOGARANTIA CG ON (CG.CD_CLASSIFICACAO = CL.CD_CLASSIFICACAO)
            INNER JOIN MOTIVOLAUDO MP ON (MP.NR_LAUDO = LP.NR_LAUDO)
            INNER JOIN MOTIVOPNEU M ON (M.ID = MP.ID_MOTIVO)
            LEFT JOIN ITEMPEDIDOPNEU IPP ON (COALESCE(LP.IDITEMPEDIDOPNEUORIGEM, LP.IDITEMPEDIDOPNEURECUSA) = IPP.ID)
            LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID AND
                OPR.STORDEM <> 'C')
            LEFT JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = COALESCE(LP.CD_PESSOA, PP.IDPESSOA))
            LEFT JOIN PNEU P ON (IPP.IDPNEU = P.ID)
            LEFT JOIN MODELOPNEU MDP ON (MDP.ID = P.IDMODELOPNEU)
            LEFT JOIN USUARIO U ON (U.CD_USUARIO = LP.CD_USUARIO)
            WHERE LP.DT_LAUDO BETWEEN '01.01.2025' AND '15.01.2025'
                AND LP.ST_LAUDO NOT IN ('C')
                AND LP.TP_LAUDO = 'G'
                --AND LP.NR_LAUDO = 59717
                --AND COALESCE(CL.VL_CLASSIFICACAO, 0) > 0
            ORDER BY M.DSMOTIVO  
        ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);    
    }
}
