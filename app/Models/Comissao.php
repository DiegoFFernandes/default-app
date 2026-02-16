<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comissao extends Model
{
    use HasFactory;

    public function getComissaoFaturamento()
    {
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

    public function substituiComissaoAutomatica()
    {
        $query = "
            SELECT DISTINCT
                PP.ID NR_PEDIDO,
                COUNT(PP.ID) QTD_PNEUS,
                --OPR.ID NR_ORDEM,
                PP.IDEMPRESA CD_EMPRESA,
                IPP.IDSERVICOPNEU || '-' || ITEM.DS_ITEM AS DS_ITEM,
                PP.IDPESSOA || '-' || PESSOA.NM_PESSOA NM_PESSOA,
                IPP.VLUNITARIO VALOR,
                --OPR.DTFECHAMENTO,
                PP.DTENTREGA,
                --EF.DTFIM,
                RCH.O_NR_LANCAMENTO,
                IPPB.IDBORRACHEIRO CD_VENDEDOR,
                VENDEDOR.NM_PESSOA NM_VENDEDOR,
                IPPB.PC_COMISSAO PC_MANUAL,
                SUM(IPPB.VL_COMISSAO) VL_MANUAL,
                INV.PC_COMISSAO PC_AUTOMATICO,
                INV.VL_COMISSAO VL_AUTOMATICO,

                INV.ST_COMISSAOMANUAL,
                INV.CD_EMPRESA,
                INV.NR_LANCAMENTO,
                INV.TP_NOTA,
                INV.CD_SERIE,
                INV.CD_ITEM,
                INV.CD_VENDEDOR
            FROM PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPPB ON (IPPB.IDITEMPEDIDOPNEU = IPP.ID
                AND IPPB.CD_TIPO = 1)

            INNER JOIN PESSOA VENDEDOR ON (VENDEDOR.CD_PESSOA = IPPB.IDBORRACHEIRO)
            INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
                --INNER JOIN PNEU P ON (P.ID = IPP.IDPNEU)
                --LEFT JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PLUGORDRECAPPEDIDO POP ON (POP.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PEDIDO PD ON (PD.CD_EMPRESA = POP.CD_EMPRESA
                AND PD.NR_PEDIDO = POP.NR_PEDIDO
                AND PD.TP_PEDIDO = POP.TP_PEDIDO)
            LEFT JOIN ITEMPEDIDO IP ON (IP.CD_EMPRESA = PD.CD_EMPRESA
                AND IP.NR_PEDIDO = PD.NR_PEDIDO
                AND IP.TP_PEDIDO = PD.TP_PEDIDO
                AND IP.CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN RETORNA_CHAVENOTA(PD.CD_EMPRESA, PD.NR_PEDIDO, PD.TP_PEDIDO) RCH ON (RCH.O_CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN ITEMNOTAVENDEDOR INV ON (INV.NR_LANCAMENTO = RCH.O_NR_LANCAMENTO
                AND INV.CD_SERIE = RCH.O_CD_SERIE
                AND INV.CD_EMPRESA = RCH.O_CD_EMPRESA
                AND INV.TP_NOTA = RCH.O_TP_NOTA
                AND INV.CD_ITEM = RCH.O_CD_ITEM
                AND INV.CD_TIPO = 1)
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = PP.IDPESSOA)
            WHERE
                OPR.STORDEM <> 'C'
                AND OPR.STEXAMEFINAL <> 'T'
                AND COALESCE(PD.ST_PEDIDO, 'N') <> 'C'
                AND RCH.O_NR_LANCAMENTO IS NOT NULL
                AND PP.STGERAPEDIDO = 'S'
                AND IPPB.ST_CALCULO = 'M'
                AND INV.PC_COMISSAO IS NOT NULL
                AND INV.ST_COMISSAOMANUAL NOT IN ('S')
                --AND PP.ID IN (221978)

            GROUP BY PP.ID,
                PP.IDEMPRESA,
                IPP.IDSERVICOPNEU,
                ITEM.DS_ITEM,
                PP.IDPESSOA,
                PESSOA.NM_PESSOA,
                IPP.VLUNITARIO,
                PP.DTENTREGA,
                RCH.O_NR_LANCAMENTO,
                IPPB.IDBORRACHEIRO,
                VENDEDOR.NM_PESSOA,
                IPPB.PC_COMISSAO,
                INV.PC_COMISSAO,
                INV.VL_COMISSAO,
                INV.ST_COMISSAOMANUAL,
                INV.CD_EMPRESA,
                INV.NR_LANCAMENTO,
                INV.TP_NOTA,
                INV.CD_SERIE,
                INV.CD_ITEM,
                INV.CD_VENDEDOR  
            ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function updateComissaoAutomatica($pedido)
    {
        return DB::transaction(function () use ($pedido) {

            try {
                DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

                $query = "
                UPDATE ITEMNOTAVENDEDOR INV
                SET INV.PC_COMISSAO = :pc_comissao,
                    INV.VL_COMISSAO = :vl_comissao,
                    INV.ST_COMISSAOMANUAL = 'S'  
                WHERE INV.CD_EMPRESA = :cd_empresa
                    AND INV.NR_LANCAMENTO = :nr_lancamento
                    AND INV.TP_NOTA = :tp_nota
                    AND INV.CD_SERIE = :cd_serie
                    AND INV.CD_ITEM = :cd_item
                    AND INV.CD_VENDEDOR = :cd_vendedor";

                DB::connection('firebird')->update($query, [
                    'pc_comissao' => $pedido['PC_MANUAL'],
                    'vl_comissao' => $pedido['VL_MANUAL'],
                    'cd_empresa' => $pedido['CD_EMPRESA'],
                    'nr_lancamento' => $pedido['NR_LANCAMENTO'],
                    'tp_nota' => $pedido['TP_NOTA'],
                    'cd_serie' => $pedido['CD_SERIE'],
                    'cd_item' => $pedido['CD_ITEM'],
                    'cd_vendedor' => $pedido['CD_VENDEDOR']
                ]);

                return [
                    'success' => true,
                    'nr_pedido' => $pedido['NR_PEDIDO'],
                    'message' => 'Comissão atualizada com sucesso!'
                ];
            } catch (\Throwable $th) {
                return [
                    'success' => false,
                    'nr_pedido' => $pedido['NR_PEDIDO'],
                    'message' => 'Erro ao atualizar comissão!' . $th->getMessage()
                ];
            }
        });
    }
}
