<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class Garantia extends Model
{
    use HasFactory;

    public function getGarantia($data)
    {
        if (is_null($data)) {
            $nm_usuario = "";
            $ds_motivo = "";
            $nm_cliente = "";
        } else {
            $nm_usuario =  $data['nm_usuario'];
            $ds_motivo = $data['ds_motivo'];
            $nm_pessoa = $data['nm_pessoa'];
            $cd_empresa = $data['cd_empresa'];
            $dt_inicio = $data['dt_inicial'];
            $dt_fim = $data['dt_final'];
        }
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
                U.CD_USUARIO || ' - ' || U.NM_USUARIO NM_USUARIO,
                MES.O_DS_MES NM_MES
            FROM LAUDOTECNICOPNEURECAP LP
            INNER JOIN RETORNA_MES(LP.DT_LAUDO) MES ON (1 = 1)
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
            WHERE LP.ST_LAUDO NOT IN ('C')
                AND LP.TP_LAUDO = 'G'
                " . (($nm_usuario != "") ? "AND U.NM_USUARIO like '%$nm_usuario%'" : "") . "
                " . (($ds_motivo != "") ? "AND M.DSMOTIVO like '%$ds_motivo%'" : "") . "
                " . (($nm_pessoa != "") ? "AND PESSOA.NM_PESSOA  like '%$nm_pessoa%'" : "") . "
                " . (($cd_empresa != 0) ? "AND LP.IDEMPRELAUDO IN ($cd_empresa)" : "") . "
                " . (($dt_inicio != 0) ? "AND LP.DT_LAUDO between '$dt_inicio' and '$dt_fim'" : "AND LP.DT_LAUDO BETWEEN '01.01.2025' AND '15.02.2025'") . "
                --AND LP.NR_LAUDO = 59717
                --AND COALESCE(CL.VL_CLASSIFICACAO, 0) > 0
            ORDER BY M.DSMOTIVO  
        ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
