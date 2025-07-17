<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LiberaOrdemComercial extends Model
{
    use HasFactory;

    public function listOrdensBloqueadas($cd_regiao = 0, $pedidos = 0)
    {
        $query = "
                SELECT
                    PP.IDEMPRESA EMP,
                    PP.DTEMISSAO,
                    PP.ID PEDIDO,
                    PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.IDPEDIDOMOVEL,
                    CAST(P.NM_PESSOA AS VARCHAR(1000) CHARACTER SET ISO8859_1) PESSOA,
                    --CAST(PP.DSBLOQUEIO AS VARCHAR(8100) CHARACTER SET UTF8) DSBLOQUEIO,
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
                    AND PP.TP_BLOQUEIO <> 'F'
                    --" . (($cd_regiao != 0) ? "and ep.cd_regiaocomercial in ($cd_regiao)" : "") . "
                    --" . (($pedidos != 0) ? "and pp.id in ($pedidos)" : "and pp.id = 0") . "
                    --and ipb.iditempedidopneu = 466381                    
                GROUP BY PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.IDEMPRESA,
                    PP.DTEMISSAO,
                    PESSOA,
                    --PP.DSBLOQUEIO,
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
                ITP.CD_TABPRECO,

                IPB.PC_COMISSAO,
                IPB.VL_COMISSAO
            FROM
                PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID
                                                            AND IPB.CD_TIPO = 1)
            INNER JOIN ITEM I ON (IPP.IDSERVICOPNEU = I.CD_ITEM)
            LEFT JOIN ITEMTABPRECO ITP ON (ITP.CD_TABPRECO = 1
                                    AND ITP.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN PESSOA PV ON (PV.CD_PESSOA = PP.IDVENDEDOR)
            WHERE                
                PP.STPEDIDO IN ('B') AND
                PP.TP_BLOQUEIO <> 'F'
                " . (($id <> 0) ? " and pp.id = '" . $id . "'" : "") . "";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function updateValueItempedidoPneu($pneu)
    {
        self::updateValueItempedidoPneuBorracheiro($pneu);

        return DB::transaction(function () use ($pneu) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "UPDATE ITEMPEDIDOPNEU IPP SET IPP.VLUNITARIO = " . $pneu['VL_VENDA'] . " WHERE IPP.ID = " . $pneu['ID'] . "";

            return DB::connection('firebird')->statement($query);
        });
    }
    public function updateValueItempedidoPneuBorracheiro($pneu)
    {
        return DB::transaction(function () use ($pneu) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                    SELECT
                        IIPB.ID,
                        IIPB.IDITEMPEDIDOPNEU,
                        IIPB.IDBORRACHEIRO,
                        IIPB.PC_COMISSAO,
                        IIPB.VL_COMISSAO,
                        IIPB.DT_REGISTRO
                    FROM ITEMPEDIDOPNEUBORRACHEIRO IIPB
                    WHERE IIPB.IDITEMPEDIDOPNEU = " . $pneu['ID'] . " ";

            $data = DB::connection('firebird')->select($query);

            foreach ($data as $d) {
                $VL_COMISSAO = ($pneu['VL_VENDA'] * $d->PC_COMISSAO) / 100;

                $query = "UPDATE ITEMPEDIDOPNEUBORRACHEIRO IIPB SET IIPB.VL_COMISSAO = $VL_COMISSAO WHERE IIPB.ID = $d->ID";
                DB::connection('firebird')->statement($query);
            }
        });
    }
}
