<?php

namespace App\Models;

use GPBMetadata\Google\Api\Auth;
use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LiberaOrdemComercial extends Model
{
    use HasFactory;

    public function listPedidosBloqueadas(
        $cd_regiao = 0,
        $pedidos = 0,
        $iditempedidopneu = 0,
        $supervisor = null,
        $st_comercial = null,
        $subgruposLiberados = null
    ) {
        $query = "
                SELECT
                    PP.IDEMPRESA EMP,
                    PP.DTEMISSAO,
                    PP.ID PEDIDO,
                    PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.IDPEDIDOMOVEL,
                    PP.DSOBSFATURAMENTO,
                    P.CD_PESSOA||'-'||P.NM_PESSOA PESSOA,
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
                    COUNT(IPP.id) QTDPNEUS,
                    COALESCE(PP.ST_COMERCIAL, 'S') ST_COMERCIAL,
                    SUPERVISOR.NM_PESSOA NM_SUPERVISOR,
                    CONDPAGTO.DS_CONDPAGTO
                FROM PEDIDOPNEU PP
                INNER JOIN VENDEDOR V ON (V.CD_VENDEDOR = PP.IDVENDEDOR)
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
                INNER JOIN ITEM I ON (IPP.IDSERVICOPNEU = I.CD_ITEM)
                LEFT JOIN ITEMTABPRECO ITP ON (ITP.CD_TABPRECO = COALESCE(IPP.IDTABPRECO, 1)
                    AND ITP.CD_ITEM = IPP.IDSERVICOPNEU)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = PP.IDVENDEDOR)
                LEFT JOIN PESSOA SUPERVISOR ON (SUPERVISOR.CD_PESSOA = V.CD_VENDEDORGERAL)
                INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
                    AND EP.CD_ENDERECO = 1)
                LEFT JOIN PARMTABPRECO T ON (T.CD_PESSOA = PP.IDPESSOA
                    AND PP.IDEMPRESA = COALESCE(T.CD_EMPRESA, PP.IDEMPRESA))
                LEFT JOIN TABPRECO ON (TABPRECO.CD_TABPRECO = T.CD_TABPRECO)
                LEFT JOIN CONDPAGTO ON (CONDPAGTO.CD_CONDPAGTO = PP.IDCONDPAGTO)
                WHERE PP.STPEDIDO IN ('B')
                    AND PP.IDTIPOPEDIDO = 1
                    AND PP.TP_BLOQUEIO <> 'F'
                    " . (($cd_regiao != 0) ? "and ep.cd_regiaocomercial in ($cd_regiao)" : "") . "
                    " . (($pedidos != 0) ? "and pp.id in ($pedidos)" : "") . "
                    " . (($iditempedidopneu != 0) ? "and ipb.iditempedidopneu = $iditempedidopneu" : "") . "
                    " . (($supervisor != null) ? "and v.CD_VENDEDORGERAL = $supervisor" : "") . "
                    " . (($st_comercial != null) ? "and pp.st_comercial = '$st_comercial'" : "") . "
                    " . (($subgruposLiberados != null) ? "and I.CD_SUBGRUPO in ($subgruposLiberados)" : "") . "
                GROUP BY PP.STPEDIDO,
                    PP.TP_BLOQUEIO,
                    PP.IDEMPRESA,
                    PP.DTEMISSAO,
                    PESSOA,
                    --PP.DSBLOQUEIO,
                    PP.DSOBSFATURAMENTO,
                    PP.DSLIBERACAO,
                    VENDEDOR,
                    EP.CD_REGIAOCOMERCIAL,
                    PP.ID,
                    PP.IDPEDIDOMOVEL,
                    T.NR_SEQUENCIA,
                    TABPRECO.DS_TABPRECO,
                    TABPRECO.DT_VALIDADE,
                    PP.ST_COMERCIAL,
                    NM_SUPERVISOR,
                    CONDPAGTO.DS_CONDPAGTO
                    ";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function listPneusOrdensBloqueadas($id = 0, $iditempedidopneu = 0, $st_comercial = 0)
    {
        $query = "
                SELECT
                IPP.ID,                
                PP.STPEDIDO,
                PP.TP_BLOQUEIO,
                PP.ID PEDIDO,
                PP.IDEMPRESA EMP,
                PP.DTEMISSAO,
                PP.IDPESSOA IDPESSOA,
                PP.DTEMISSAO,
                pp.IDCONDPAGTO,
                CAST(P.NM_PESSOA AS VARCHAR(1000) CHARACTER SET UTF8) PESSOA,
                I.CD_SUBGRUPO,
                PP.IDVENDEDOR CD_VENDEDOR,
                CAST(PV.NM_PESSOA AS VARCHAR(1000) CHARACTER SET UTF8) VENDEDOR,
                IPP.NRSEQCRIACAO SEQ,
                PP.IDPEDIDOMOVEL,
                I.CD_ITEM,
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
                IPB.VL_COMISSAO,
                TABPRECO.DS_TABPRECO,
                COALESCE(PP.ST_COMERCIAL, 'S') ST_COMERCIAL,
                COALESCE(IPB.ST_CALCULO, 'A') ST_CALCULO
            FROM
                PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            LEFT JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID
                                                            AND IPB.CD_TIPO = 1)
            INNER JOIN ITEM I ON (IPP.IDSERVICOPNEU = I.CD_ITEM)
            LEFT JOIN ITEMTABPRECO ITP ON (ITP.CD_TABPRECO = COALESCE(IPP.IDTABPRECO, 1)
                                            AND ITP.CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN TABPRECO ON (TABPRECO.CD_TABPRECO = ITP.CD_TABPRECO)
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN PESSOA PV ON (PV.CD_PESSOA = PP.IDVENDEDOR)
            WHERE
                PP.STPEDIDO IN ('B')
                AND PP.IDTIPOPEDIDO = 1
                AND PP.TP_BLOQUEIO <> 'F'
               
                " . (($id <> 0) ? " and pp.id = '" . $id . "'" : "") . "
                " . (($iditempedidopneu != 0) ? "and ipb.iditempedidopneu = $iditempedidopneu" : "") . "
                " . (($st_comercial != 0) ? "and pp.st_comercial not in ('G')" : "") . "
            ";


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
                    WHERE IIPB.IDITEMPEDIDOPNEU = " . $pneu['ID'] . "
                     AND IIPB.CD_TIPO = 1
                    ";

            $data = DB::connection('firebird')->select($query);

            foreach ($data as $d) {

                $VL_COMISSAO = $pneu['VL_COMISSAO'];
                $PC_COMISSAO = $pneu['PC_COMISSAO'];

                if ($pneu['ST_CALCULO'] == 'A') {
                    $ST_CALCULO = 'A';
                } else {
                    $ST_CALCULO = 'M';  
                    PedidoPneu::updateDesconto($pneu['PEDIDO'], 'G');
                }

                $query = "
                    UPDATE ITEMPEDIDOPNEUBORRACHEIRO IIPB 
                        SET IIPB.VL_COMISSAO = $VL_COMISSAO, 
                            IIPB.PC_COMISSAO = $PC_COMISSAO,
                            IIPB.ST_CALCULO = '$ST_CALCULO',
                            IIPB.NMUSUARIOALT = '" . Auth()->user()->name . "'
                    WHERE IIPB.ID = $d->ID";
                DB::connection('firebird')->statement($query);
            }
        });
    }
    public function calculaComissao($input, $venda)
    {
        $cd_empresa = $input[0]->EMP;
        $cd_pessoa = $input[0]->IDPESSOA;
        $cd_item = $input[0]->CD_ITEM;
        $cd_vendedor = $input[0]->CD_VENDEDOR;
        $cd_movimentacao = $input[0]->CD_MOVIMENTACAO ?? 20;
        $dt_emissao = $input[0]->DTEMISSAO;
        $cd_cond_pagto = $input[0]->IDCONDPAGTO;
        $cd_tabpreco = $input[0]->CD_TABPRECO;
        $cd_preco_venda = $venda;
        $cd_preco_tabela = $input[0]->VL_PRECO ?? 0;

        // $query = "
        // SELECT
        //     CAST(C.V_PC_COMISSAO AS NUMERIC(15,2)) PC_COMISSAO,
        //     CAST(C.V_VL_COMISSAO AS NUMERIC(15,2)) VL_COMISSAO
        // FROM CALCULA_COMISSAO($cd_empresa, $cd_vendedor, NULL, $cd_item, $cd_movimentacao, NULL, $cd_tabpreco, NULL, $cd_preco_venda, NULL, $cd_preco_tabela) C";

        $query = "
            SELECT
                CAST(COALESCE(C.V_PC_COMISSAO, 0) AS NUMERIC(15,2)) PC_COMISSAO,
                CAST(COALESCE(C.V_VL_COMISSAO, 0) AS NUMERIC(15,2)) VL_COMISSAO
            FROM CALCULA_COMISSAO_V2($cd_empresa, $cd_vendedor, 1, $cd_pessoa, $cd_item, $cd_movimentacao, 
                $cd_cond_pagto, $cd_tabpreco, '$dt_emissao', $cd_preco_venda, NULL, $cd_preco_venda, 1) C";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function updateStatusPedidos()
    {

        //Lista somente as que estão bloqueadas e já foram analisadas com o st_gerente 'G'
        $pneus = self::listPneusOrdensBloqueadas(0, 0, 1) ?? [];

        if (empty($pneus)) {
            return;
        }

        $foraTabela  = collect($pneus)
            ->groupBy('PEDIDO')
            ->filter(function ($grupo) {
                // Supervisor e Gerente → basta UM item dentro da faixa
                return $grupo->contains(
                    fn($pneu) =>
                    $pneu->CD_TABPRECO != 1 and $pneu->PC_DESCONTO > 0.00
                );
            })
            ->keys()
            ->implode(',');

        PedidoPneu::updateDesconto($foraTabela, 'G');

        $percentual = PercentualDescontoComissao::listAllPercDesconto();
        $resultado = [];

        foreach ($percentual as $p) {
            $pedidos = collect($pneus)
                ->groupBy('PEDIDO')
                ->filter(function ($grupo) use ($p) {
                    // Automático precisa que TODOS estejam dentro da faixa
                    if ($p->tp_cargo === 'A') {
                        return $grupo->every(
                            fn($pneu) =>
                            $pneu->PC_DESCONTO >= $p->perc_desconto_min &&
                                $pneu->PC_DESCONTO <= $p->perc_desconto_max &&
                                $pneu->CD_TABPRECO == 1
                        );
                    }

                    // Supervisor e Gerente → basta UM item dentro da faixa
                    return $grupo->contains(
                        fn($pneu) =>
                        $pneu->PC_DESCONTO >= $p->perc_desconto_min &&
                            $pneu->PC_DESCONTO <= $p->perc_desconto_max &&
                            $pneu->CD_TABPRECO == 1
                    );
                })
                ->keys()
                ->implode(',');

            $resultado[$p->tp_cargo] = $pedidos;
        }

        // Atualiza em lote
        foreach (['G', 'S', 'A'] as $cargo) {
            PedidoPneu::updateDesconto($resultado[$cargo] ?? '', $cargo);
        }
        return;
    }
}
