<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExecutorEtapa extends Model
{
    use HasFactory;

    public function getExecutores()
    {
        $query = "SELECT ID, NMEXECUTOR FROM EXECUTORETAPA";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }


    public function producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim, $tabela, $executor = 0, $painel = 'painel-ativos')
    {
        if ($painel === 'painel-ativos') {
            $query = "
                AND ITEM.CD_SUBGRUPO NOT IN (10211)";
        } elseif ($painel === 'painel-recusados') {
            $query = "
                AND ITEM.CD_SUBGRUPO = 10211";
        } else {
            $query = "";
        }


        $query = "
        SELECT
            OPR.IDEMPRESA,
            COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
            COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') NM_EXECUTOR,
            CAST(I.DTFIM AS DATE) DT_FIM,
            COUNT(I.ID) QTD,
            COALESCE(EE.QTMETADIARIA, 0) META,
            COUNT (CASE WHEN OPPR.IDORDEMRETRABALHO IS NOT NULL THEN 1 END) AS QTD_RETRABALHO  

        FROM $tabela I
        LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
        LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
        LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
        LEFT JOIN ORDEMPRODUCAORECAPRETRABALHO OPPR ON (OPPR.IDORDEMPRODUCAORECAP = OPR.ID)
        LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
        INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
        WHERE
            I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
            AND I.ST_ETAPA = 'F'
            AND OPR.STORDEM <> 'C'
            AND OPR.IDEMPRESA = $cd_empresa
            $query
            " . ($executor != 0 ? "AND I.IDEXECUTOR = $executor" : "") . "
        GROUP BY DT_FIM,
            OPR.IDEMPRESA,
            EE.QTMETADIARIA,
            I.IDEXECUTOR,
            EE.QTMETADIARIA,
            E.NMEXECUTOR
     ";


        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function producaoDetalhesExecutorEtapa($cd_empresa, $dt_fim, $tabela, $executor, $painel = 'painel-ativos')
    {

        if ($painel === 'painel-ativos') {
            $query = "
                AND ITEM.CD_SUBGRUPO NOT IN (10211)";
        } elseif ($painel === 'painel-recusados') {
            $query = "
                AND ITEM.CD_SUBGRUPO = 10211";
        } else {
            $query = "";
        }


        $query = "
        SELECT
            OPR.IDEMPRESA,
            COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
            COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') NM_EXECUTOR,
            OPR.ID NR_ORDEM,
            IPP.IDSERVICOPNEU || '-' || ITEM.DS_ITEM DS_ITEM,
            I.DTFIM DT_FIM,
            CASE WHEN OPPR.IDORDEMRETRABALHO IS NOT NULL THEN 'Sim' ELSE 'Nao' END AS ST_RETRABALHO
        FROM $tabela I
        LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
        LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
        LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
        LEFT JOIN ORDEMPRODUCAORECAPRETRABALHO OPPR ON (OPPR.IDORDEMPRODUCAORECAP = OPR.ID)
        LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
        INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
        INNER JOIN ITEM ON ITEM.CD_ITEM = IPP.IDSERVICOPNEU
        WHERE CAST(I.DTFIM AS DATE) = '$dt_fim'
            AND I.ST_ETAPA = 'F'
            AND OPR.STORDEM <> 'C'
            AND OPR.IDEMPRESA = $cd_empresa
            AND COALESCE(I.IDEXECUTOR, '9999') = $executor  
            $query
     ";


        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
