<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Faturamento extends Model
{
    use HasFactory;

    public function listFaturamentoUser($inicioData, $fimData){
        $query = "
                    SELECT
                        N.CD_EMPRESA,
                        N.NR_LANCAMENTO,
                        MES.O_DS_MES DS_MES,
                        N.DT_EMISSAO,
                        N.DT_CANCELAMENTO,
                        N.CD_SERIE,
                        
                        U.NM_USUARIO USUARIO,
                        UC.NM_USUARIO USUARIOCANC,
                        N.CD_MOTIVO,
                        COUNT(I.CD_ITEM) QTD_ITENS
                    FROM NOTA N
                    INNER JOIN ITEMNOTA I ON (I.CD_EMPRESA = N.CD_EMPRESA AND
                        I.NR_LANCAMENTO = N.NR_LANCAMENTO AND
                        I.TP_NOTA = N.TP_NOTA AND
                        I.CD_SERIE = N.CD_SERIE)
                    INNER JOIN USUARIO U ON (U.CD_USUARIO = N.CD_USUARIO)
                    LEFT JOIN USUARIO UC ON (UC.CD_USUARIO = N.CD_USUARIOCANC)
                    INNER JOIN MES_EXTENSO(N.DT_EMISSAO) MES ON (1 = 1)
                    WHERE N.TP_NOTA = 'S'
                        AND  N.DT_EMISSAO BETWEEN ".($inicioData == 0 ? "DATEADD(-EXTRACT(DAY FROM CURRENT_DATE) + 1 DAY TO DATEADD(0 MONTH TO CURRENT_DATE)) AND CURRENT_DATE" : "'$inicioData' AND '$fimData'") . "
                        AND N.ST_NOTA NOT IN ('C')
                        AND I.CD_MOVIMENTACAO NOT IN (172,175,66,14,58,160)
                        --and U.CD_USUARIO  = '48'
                    GROUP BY N.CD_EMPRESA,
                        N.NR_LANCAMENTO,
                        N.DT_EMISSAO,
                        MES.O_DS_MES,
                        N.DT_CANCELAMENTO,
                        N.CD_SERIE,
                        N.CD_USUARIO,
                        U.NM_USUARIO,
                        N.CD_USUARIOCANC,
                        UC.NM_USUARIO,
                        N.CD_MOTIVO
                    ORDER BY N.DT_EMISSAO ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
