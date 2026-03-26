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


    public function producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim, $tabela, $executor = 0, $painel = 'painel-ativos', $etapa = 1, $subgrupo = 9)
    {
        if ($painel === 'painel-ativos') {
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
                    AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                    " . ($executor != 0 ? "AND I.IDEXECUTOR in ($executor)" : "") . "
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    EE.QTMETADIARIA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA,
                    E.NMEXECUTOR
                ";
        } elseif ($painel === 'painel-recusados') {
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
                    AND ITEM.CD_SUBGRUPO IN ($subgrupo)
                    " . ($executor != 0 ? "AND I.IDEXECUTOR in ($executor)" : "") . "
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    EE.QTMETADIARIA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA,
                    E.NMEXECUTOR
                ";
        } elseif ($painel === 'painel-retrabalhos') {
            $query = "
                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') NM_EXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    COUNT(I.ID) QTD,
                    COALESCE(EE.QTMETADIARIA, 0) META,
                    COUNT(
                    CASE
                    WHEN OPPR.IDORDEMRETRABALHO IS NOT NULL THEN 1
                    END) AS QTD_RETRABALHO
                FROM $tabela I
                LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                LEFT JOIN ORDEMPRODUCAORECAPRETRABALHO OPPR ON (OPPR.IDORDEMPRODUCAORECAP = OPR.ID)
                LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                WHERE I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
                    AND I.ST_ETAPA = 'F'                    
                    AND OPR.IDEMPRESA = $cd_empresa
                    AND OPR.STRETRABALHO = 'S'
                    AND OPR.STORDEM <> 'C'
                    AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                GROUP BY DT_FIM, 
                OPR.IDEMPRESA, 
                EE.QTMETADIARIA,
                I.IDEXECUTOR, 
                EE.QTMETADIARIA, 
                E.NMEXECUTOR

             ";
        } elseif ($painel === 'painel-canceladas') {
            $query = "
                SELECT
                    OPR.IDEMPRESA,                    
                    CAST(OPR.DTREGISTRO AS DATE) DT_FIM,
                    COUNT(OPR.ID) QTD                    
                FROM ORDEMPRODUCAORECAP OPR
                LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                WHERE
                    OPR.DTREGISTRO BETWEEN '$dt_inicio' AND '$dt_fim'                
                    AND OPR.STORDEM = 'C'
                    AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY OPR.IDEMPRESA, DT_FIM          
            ";
        } else {
            $query = "";
        }

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function producaoDetalhesExecutorEtapa($cd_empresa, $dt_fim, $tabela, $executor, $painel = 'painel-ativos', $etapa = 1, $subgrupo = 9)
    {

        if ($painel === 'painel-ativos') {
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
                    AND COALESCE(I.IDEXECUTOR, '9999') in ($executor)             
                    AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                    ";
        } elseif ($painel === 'painel-recusados') {
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
                    AND COALESCE(I.IDEXECUTOR, '9999') in ($executor)            
                    AND ITEM.CD_SUBGRUPO IN ($subgrupo)";
        } elseif ($painel === 'painel-retrabalhos') {
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
                    --AND OPR.STORDEM <> 'C'
                    AND OPR.STRETRABALHO = 'S'
                    AND OPR.IDEMPRESA = $cd_empresa
                    AND COALESCE(I.IDEXECUTOR, '9999') in ($executor)             
                    AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)    
             ";
        } elseif ($painel === 'painel-canceladas') {
            $query = "
                SELECT
                    OPR.IDEMPRESA,
                    OPR.ID NR_ORDEM,
                    IPP.IDSERVICOPNEU || '-' || ITEM.DS_ITEM DS_ITEM,
                    CAST(OPR.DTREGISTRO AS DATE) DT_FIM                    
                FROM ORDEMPRODUCAORECAP OPR
                LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                WHERE
                    CAST(OPR.DTREGISTRO AS DATE) = '$dt_fim'                    
                    AND OPR.STORDEM = 'C'
                    AND OPR.IDEMPRESA = $cd_empresa    
            ";
        } else {
            $query = "";
        }


        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function resumoExecutorSetor($cd_empresa, $dt_inicio, $dt_fim, $subgrupo = 9, $executor = 0, $painel = 'painel-ativos', $subPainel = null)
    {
        $setores = [
            ['tabela' => 'EXAMEINICIAL', 'nome' => 'EXAME INICIAL', 'idetapa' => 1],
            ['tabela' => 'RASPAGEMPNEU', 'nome' => 'RASPAGEM', 'idetapa' => 2],
            ['tabela' => 'PREPARACAOBANDAPNEU', 'nome' => 'PREPARACAO BANDA', 'idetapa' => 3],
            ['tabela' => 'ESCAREACAOPNEU', 'nome' => 'ESCAREACAO', 'idetapa' => 4],
            ['tabela' => 'LIMPEZAMANCHAO', 'nome' => 'LIMPEZA MANCHAO', 'idetapa' => 5],
            ['tabela' => 'APLICACAOCOLAPNEU', 'nome' => 'COLA', 'idetapa' => 6],
            ['tabela' => 'EMBORRACHAMENTO', 'nome' => 'APLICACAO BANDA', 'idetapa' => 9],
            ['tabela' => 'VULCANIZACAO', 'nome' => 'VULCANIZACAO', 'idetapa' => 11],
            ['tabela' => 'EXAMEFINALPNEU', 'nome' => 'EXAME FINAL', 'idetapa' => 12],
        ];

        $query = [];
        $queryTop = "SELECT
                        X.SETOR,
                        X.IDETAPA,
                        SUM(X.QTD) QTD
                    FROM (
            ";

        if ($painel == 'painel-ativos' || $painel == 'painel-recusados') {
            foreach ($setores as $setor) {
                $query[] = $this->montarResumoSelectAtivos(
                    $setor['tabela'],
                    $setor['nome'],
                    $setor['idetapa'],
                    $executor,
                    $dt_inicio,
                    $dt_fim,
                    $cd_empresa,
                    $subgrupo,
                    $subPainel
                );
            }
        } elseif ($painel === 'painel-retrabalhos') {

            foreach ($setores as $setor) {
                $query[] = $this->montarResumoSelectRetrabalhos(
                    $setor['nome'],
                    $setor['idetapa'],
                    $executor,
                    $dt_inicio,
                    $dt_fim,
                    $cd_empresa,
                    $subPainel,
                    $setor['tabela'],
                    $subgrupo
                );
            }
        } else {
            $query[] = "";
        }

        $sql = implode(' UNION ALL ', $query);
        $sql = $queryTop . $sql . ") X GROUP BY X.IDETAPA, X.SETOR ORDER BY X.IDETAPA";

        $data = DB::connection('firebird')->select($sql);

        return Helper::ConvertFormatText($data);
    }

    public function montarResumoSelectAtivos($tabela, $setor, $idetapa, $executor = 0, $dt_inicio, $dt_fim, $cd_empresa, $subgrupo, $subPainel = null)
    {
        if ($subPainel == 'resumo-setor-painel-ativos') {
            return "
                    SELECT  
                        '{$setor}' SETOR,
                        COUNT(I.ID) QTD,
                        {$idetapa} IDETAPA
                    FROM {$tabela} I
                    LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                    LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                    INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                    LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    WHERE
                        I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.STORDEM <> 'C'
                        AND OPR.IDEMPRESA = $cd_empresa
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                        AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                    GROUP BY SETOR, IDETAPA         
                ";
        } elseif ($subPainel == 'resumo-setor-painel-recusados') {
            return "
                    SELECT  
                        '{$setor}' SETOR,
                        COUNT(I.ID) QTD,
                        {$idetapa} IDETAPA
                    FROM {$tabela} I
                    LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                    LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                    INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                    LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    WHERE
                        I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.STORDEM <> 'C'
                        AND OPR.IDEMPRESA = $cd_empresa
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                        AND ITEM.CD_SUBGRUPO IN ($subgrupo)
                    GROUP BY SETOR, IDETAPA";
        }

        // aqui para baixo monto select do resumo por executor
        elseif ($subPainel == 'resumo-executor-painel-ativos') {
            return "
                    SELECT
                        COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') SETOR,
                        COUNT(I.ID) QTD,
                        1 IDETAPA
                    FROM {$tabela} I
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
                        AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                    GROUP BY E.NMEXECUTOR, IDETAPA";
        } elseif ($subPainel == 'resumo-executor-painel-recusados') {
            return "
                    SELECT
                        COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') SETOR,
                        COUNT(I.ID) QTD,
                        1 IDETAPA
                    FROM {$tabela} I
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
                        AND ITEM.CD_SUBGRUPO IN ($subgrupo)
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                    GROUP BY E.NMEXECUTOR, IDETAPA";
        }
    }

    public function montarResumoSelectRetrabalhos($setor, $idetapa, $executor = 0, $dt_inicio, $dt_fim, $cd_empresa, $subPainel, $tabela, $subgrupo)
    {
        if ($subPainel == 'resumo-setor-painel-retrabalhos') {
            return "
                SELECT  
                        '{$setor}' SETOR,
                        COUNT(I.ID) QTD,
                        {$idetapa} IDETAPA
                    FROM {$tabela} I
                    LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                    LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                    INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                    LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    WHERE
                        I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.STRETRABALHO = 'S'
                        AND OPR.IDEMPRESA = $cd_empresa
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                        AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                    GROUP BY SETOR, IDETAPA

                                     
            ";
        } elseif ($subPainel == 'resumo-executor-painel-retrabalhos') {
            return "
                    SELECT
                        COALESCE(E.NMEXECUTOR, 'SEM OPERADOR') SETOR,
                        COUNT(I.ID) QTD,
                        1 IDETAPA
                    FROM {$tabela} I
                    LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                    LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                    LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                    LEFT JOIN ORDEMPRODUCAORECAPRETRABALHO OPPR ON (OPPR.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    WHERE
                        I.DTFIM BETWEEN '$dt_inicio' AND '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.STRETRABALHO = 'S'
                        AND OPR.IDEMPRESA = $cd_empresa
                        AND ITEM.CD_SUBGRUPO NOT IN ($subgrupo)
                        " . ($executor != 0 ? "AND I.IDEXECUTOR IN ($executor)" : "") . "
                    GROUP BY E.NMEXECUTOR, IDETAPA";
        }
    }
}
