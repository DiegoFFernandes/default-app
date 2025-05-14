<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Faturamento extends Model
{
    use HasFactory;

    public function listFaturamentoUser(){
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
                    WHERE N.DT_EMISSAO BETWEEN '01.03.2025' AND '31.03.2025'
                        AND N.TP_NOTA = 'S'
                        AND I.CD_MOVIMENTACAO NOT IN (172)
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
                        N.CD_MOTIVO";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
