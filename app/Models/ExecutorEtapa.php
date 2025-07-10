<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExecutorEtapa extends Model
{
    use HasFactory;

    public function producaoExecutorEtapa($cd_empresa, $dt_inicio, $dt_fim){
        $query = "        
            SELECT
                X.DT_FIM,
                X.IDEMPRESA,
                X.IDEXECUTOR || '-' || E.NMEXECUTOR NM_EXECUTOR,
                cast(SUM(X.EXAMEINICIAL) AS INTEGER) EXAME_INI,
                cast(SUM(X.RASPA) AS INTEGER) RASPA,
                cast(SUM(X.PREPBANDA) AS INTEGER) PREPBANDA,
                cast(SUM(X.ESCAREACAO) AS INTEGER) ESCAREACAO,
                cast(SUM(X.LIMPEZAMANCHAO) AS INTEGER) LIMPEZAMANCHAO,
                cast(SUM(X.APLICOLA) AS INTEGER) APLICOLA,
                cast(SUM(X.EMBORRACHAMENTO) AS INTEGER) EMBORRACHAMENTO,
                cast(SUM(X.VULCANIZACAO) AS INTEGER) VULCANIZACAO,
                cast(SUM(X.EXAMEFINAL) AS INTEGER) EXAMEFINAL
            FROM (SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    COUNT(I.ID) EXAMEINICIAL,
                    EE.QTMETADIARIA METAEXAMEINI,
                    0 RASPA,
                    0 METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM EXAMEINICIAL I
                LEFT JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.STORDEM <> 'C'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    EE.QTMETADIARIA,
                    I.IDEXECUTOR,
                    METAEXAMEINI

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    COUNT(I.ID) RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM RASPAGEMPNEU I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    METARASPA

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    0 METARASPA,
                    COUNT(I.ID) PREPBANDA,
                    EE.QTMETADIARIA METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM PREPARACAOBANDAPNEU I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    METAPREPBANDA

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    0 METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    COUNT(I.ID) ESCAREACAO,
                    EE.QTMETADIARIA METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM ESCAREACAOPNEU I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    METAESCAREACAO

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    COUNT(I.ID) LIMPEZAMANCHAO,
                    EE.QTMETADIARIA METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM LIMPEZAMANCHAO I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    METALIMPEZAMANCHAO

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    COUNT(I.ID) APLICOLA,
                    EE.QTMETADIARIA METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM APLICACAOCOLAPNEU I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    COUNT(I.ID) EMBORRACHAMENTO,
                    EE.QTMETADIARIA METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM EMBORRACHAMENTO I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    COUNT(I.ID) VULCANIZACAO,
                    EE.QTMETADIARIA METAVULC,
                    0 EXAMEFINAL,
                    0 METAEXAMEFINAL
                FROM VULCANIZACAO I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA

                UNION ALL

                SELECT
                    OPR.IDEMPRESA,
                    COALESCE(I.IDEXECUTOR, '9999') IDEXECUTOR,
                    CAST(I.DTFIM AS DATE) DT_FIM,
                    0 EXAMEINICIAL,
                    0 METAEXAMEINI,
                    0 RASPA,
                    EE.QTMETADIARIA METARASPA,
                    0 PREPBANDA,
                    0 METAPREPBANDA,
                    0 ESCAREACAO,
                    0 METAESCAREACAO,
                    0 LIMPEZAMANCHAO,
                    0 METALIMPEZAMANCHAO,
                    0 APLICOLA,
                    0 METAAPLICCOLA,
                    0 EMBORRACHAMENTO,
                    0 METAEMBORRACHAMENTO,
                    0 VULCANIZACAO,
                    0 METAVULC,
                    COUNT(I.ID) EXAMEFINAL,
                    EE.QTMETADIARIA METAEXAMEFINAL
                FROM EXAMEFINALPNEU I
                INNER JOIN EXECUTORETAPA E ON (I.IDEXECUTOR = E.ID)
                LEFT JOIN ETAPASPRODUCAOEXECUTORRECAP EE ON (EE.IDEXECUTOR = E.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = I.IDORDEMPRODUCAORECAP)
                WHERE I.DTFIM between '$dt_inicio' and '$dt_fim'
                        AND I.ST_ETAPA = 'F'
                        AND OPR.IDEMPRESA = $cd_empresa
                GROUP BY DT_FIM,
                    OPR.IDEMPRESA,
                    I.IDEXECUTOR,
                    EE.QTMETADIARIA

            ) X
            INNER JOIN EXECUTORETAPA E ON (E.ID = X.IDEXECUTOR)
            GROUP BY X.IDEMPRESA,
                E.NMEXECUTOR,
                X.IDEXECUTOR,
                X.DT_FIM
            ORDER BY E.NMEXECUTOR,
                X.DT_FIM ASC
                    ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
