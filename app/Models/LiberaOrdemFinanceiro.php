<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LiberaOrdemFinanceiro extends Model
{
    use HasFactory;

    public function listOrdensBloqueadas($cd_regiao, $pedidos)
    {
        $query = "
                SELECT
                    PP.IDEMPRESA EMP,
                    PP.DTEMISSAO,
                    PP.ID PEDIDO,
                    PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.DTBLOQUEIO,
                    PP.IDPEDIDOMOVEL,
                    CAST(P.NM_PESSOA AS VARCHAR(1000) CHARACTER SET ISO8859_1) PESSOA,                    
                    PP.DSLIBERACAO,
                    CAST(PV.NM_PESSOA AS VARCHAR(1000) CHARACTER SET UTF8) VENDEDOR,
                    EP.CD_REGIAOCOMERCIAL,
                    CASE
                        WHEN COALESCE(T.NR_SEQUENCIA, 1) = 1 THEN 'NAO'
                        ELSE 'SIM'
                    END TABPRECO,
                    TABPRECO.DS_TABPRECO,
                    TABPRECO.DT_VALIDADE,
                    COUNT(IPP.id) QTDPNEUS
                FROM PEDIDOPNEU PP
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
                INNER JOIN ITEM I ON (IPP.IDSERVICOPNEU = I.CD_ITEM)
                LEFT JOIN ITEMTABPRECO ITP ON (ITP.CD_TABPRECO = IPP.IDTABPRECO
                    AND ITP.CD_ITEM = IPP.IDSERVICOPNEU)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = PP.IDVENDEDOR)
                INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
                    AND EP.CD_ENDERECO = 1)
                LEFT JOIN PARMTABPRECO T ON (T.CD_PESSOA = PP.IDPESSOA
                    AND PP.IDEMPRESA = T.CD_EMPRESA)
                LEFT JOIN TABPRECO ON (TABPRECO.CD_TABPRECO = T.CD_TABPRECO)
                WHERE PP.STPEDIDO IN ('B')
                    AND PP.IDTIPOPEDIDO <> 2
                    AND PP.TP_BLOQUEIO <> 'C'
                    " . (($cd_regiao != "") ? "and ep.cd_regiaocomercial in ($cd_regiao)" : "") . "
                    " . (($pedidos != "") ? "and pp.id in ($pedidos)" : "and pp.id = 0") . "
                    --and ipb.iditempedidopneu = 466381                    
                GROUP BY PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.IDEMPRESA,
                    PP.DTEMISSAO,
                    PESSOA,
                    PP.DTBLOQUEIO,
                    PP.DSLIBERACAO,
                    VENDEDOR,
                    EP.CD_REGIAOCOMERCIAL,
                    PP.ID,
                    PP.IDPEDIDOMOVEL,
                    T.NR_SEQUENCIA,
                    TABPRECO.DS_TABPRECO,
                    TABPRECO.DT_VALIDADE";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function listPneusOrdensBloqueadas($id)
    {
        $query = "
                SELECT
                IPP.ID,
                PP.STPEDIDO,
                PP.TP_BLOQUEIO,
                PP.DSBLOQUEIO,
                PP.ID PEDIDO,
                PP.IDEMPRESA EMP,
                PP.DTEMISSAO,
                CAST(P.NM_PESSOA AS VARCHAR(1000) CHARACTER SET UTF8) PESSOA,
                I.CD_SUBGRUPO,
                CAST(PV.NM_PESSOA AS VARCHAR(1000) CHARACTER SET UTF8) VENDEDOR,
                IPP.NRSEQCRIACAO SEQ,
                PP.IDPEDIDOMOVEL,
                I.DS_ITEM,
                IPP.VLUNITARIO VL_VENDA,
                CAST(ITP.VL_PRECO AS NUMERIC(15,2)) VL_PRECO,
                CAST(100 * (1 - (IPP.VLUNITARIO /
                CASE
                WHEN ITP.VL_PRECO = 0 THEN 1
                ELSE ITP.VL_PRECO
                END)) AS NUMERIC(15,2)) PC_DESCONTO,
                ITP.CD_TABPRECO
            FROM
                PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)            
            INNER JOIN ITEM I ON (IPP.IDSERVICOPNEU = I.CD_ITEM)
            LEFT JOIN ITEMTABPRECO ITP ON (ITP.CD_TABPRECO = 1 
                                AND ITP.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN PESSOA PV ON (PV.CD_PESSOA = PP.IDVENDEDOR)
            WHERE                
                PP.STPEDIDO IN ('B') AND
                PP.TP_BLOQUEIO <> ''
                " . (($id <> 0) ? " and pp.id = '" . $id . "'" : "") . "";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
