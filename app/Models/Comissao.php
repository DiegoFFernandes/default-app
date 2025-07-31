<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comissao extends Model
{
    use HasFactory;

    public function getComissaoFaturamento(){
        $query = "
                SELECT DISTINCT
                    N.CD_EMPRESA,
                    V.CD_PESSOA || '-' || V.NM_PESSOA NM_VENDEDOR,
                    P.NM_PESSOA,
                    N.DT_EMISSAO,
                    N.NR_NOTAFISCAL,
                    INF.CD_ITEM || '-' || ITEM.DS_ITEM DS_ITEM,
                    INF.QT_ITEMNOTA,
                    INF.VL_UNITARIO,
                    COALESCE(INF.VL_DESCONTO, 0) VL_DESCONTO,
                    INF.VL_TOTAL,
                    INFV.PC_COMISSAO,
                    INFV.VL_COMISSAO,
                    INFV.CD_TIPO,
                    INF.CD_MOVIMENTACAO,
                    I.VL_PRECO VL_TABPRECO,
                    TP.CD_TABPRECO,
                    TP.DS_TABPRECO || ' - ' || TP.CD_TABPRECO AS T_PRECO
                FROM ITEMNOTAVENDEDOR INFV
                INNER JOIN PESSOA V ON (V.CD_PESSOA = INFV.CD_VENDEDOR)
                JOIN ITEMNOTA INF ON (INF.CD_EMPRESA = INFV.CD_EMPRESA
                    AND INF.NR_LANCAMENTO = INFV.NR_LANCAMENTO
                    AND INF.TP_NOTA = INFV.TP_NOTA
                    AND INF.CD_SERIE = INFV.CD_SERIE
                    AND INF.CD_ITEM = INFV.CD_ITEM)
                INNER JOIN ITEM ON (ITEM.CD_ITEM = INF.CD_ITEM)
                JOIN NOTA N ON (N.NR_LANCAMENTO = INF.NR_LANCAMENTO
                    AND N.CD_EMPRESA = INF.CD_EMPRESA
                    AND N.CD_SERIE = INF.CD_SERIE
                    AND N.TP_NOTA = INF.TP_NOTA)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = N.CD_PESSOA)
                JOIN MOVIMENTACAO MV ON (MV.CD_MOVIMENTACAO = INF.CD_MOVIMENTACAO)
                LEFT JOIN ITEMTABPRECO I ON (I.CD_TABPRECO = INF.CD_TABPRECO
                    AND I.CD_ITEM = INF.CD_ITEM)
                LEFT JOIN TABPRECO TP ON (TP.CD_TABPRECO = I.CD_TABPRECO)
                WHERE N.DT_EMISSAO BETWEEN DATEADD(-EXTRACT(DAY FROM CURRENT_DATE) + 1 DAY TO DATEADD(-3 MONTH TO CURRENT_DATE)) AND 'TODAY'
                    --and inf.vl_unitario > 0
                    AND N.TP_NOTA = 'S'
                    --and infv.cd_tipo = '1'
                    AND MV.ST_COMISSAO = 'S'
                    --AND N.NR_LANCAMENTO = 17127
                    AND N.ST_NOTA = 'V'";

         $data = DB::connection('firebird')->select($query);

         return Helper::ConvertFormatText($data);

    }
}
