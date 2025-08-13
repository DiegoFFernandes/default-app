<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;

class Producao extends Model
{
    use HasFactory;

    public function getPneusProduzidosFaturar($empresa = 0, $cd_regiao = "",  $supervisor = 0, $data)
    {

        if (is_null($data)) {
            $pedido = "";
            $pedido_palm = "";
            $nm_cliente = "";
            $nm_vendedor = "";
            $grupo_item = 0;
            $inicioData = 0;
            $fimData = 0;
        } else {
            $empresa = $data['cd_empresa'];
            $pedido = $data['pedido'];
            $pedido_palm = $data['pedido_palm'];
            $nm_cliente = $data['nm_cliente'];
            $nm_vendedor = $data['nm_vendedor'];
            $grupo_item = $data['grupo_item'];
            $inicioData = $data['dt_inicial'];
            $fimData = $data['dt_final'];
        }

        $query = "
            SELECT DISTINCT
                COALESCE(OCP.NR_EMBARQUE, 'SEM EMBARQUE') AS NR_EMBARQUE,
                COUNT(PP.ID) AS PNEUS,
                PP.ID AS NR_COLETA,
                PP.IDEMPRESA AS CD_EMPRESA,
                PP.IDPESSOA || '-' || PESSOA.NM_PESSOA AS NM_PESSOA,
                CAST(SUM(IPP.VLUNITARIO) AS NUMERIC(18,5)) AS VALOR,
                CASE
                WHEN OPRX.IDEXPEDICAOLOTEPNEU IS NULL THEN 'NAO'
                ELSE 'SIM'
                END AS EXPEDICIONADO,
                MAX(EF.DTFIM) AS DTFIM,
                PP.DTENTREGA,
                EP.CD_REGIAOCOMERCIAL,
                V.NM_PESSOA NM_VENDEDOR
            FROM PEDIDOPNEU PP
            INNER JOIN VENDEDOR ON (VENDEDOR.CD_VENDEDOR = PP.IDVENDEDOR)
            LEFT JOIN PEDIDOPNEUMOVEL PPM ON (PPM.ID = PP.ID)
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
            INNER JOIN PNEU P ON (P.ID = IPP.IDPNEU)
            LEFT JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PLUGORDRECAPPEDIDO POP ON (POP.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PEDIDO PD ON (PD.CD_EMPRESA = POP.CD_EMPRESA
                AND PD.NR_PEDIDO = POP.NR_PEDIDO
                AND PD.TP_PEDIDO = POP.TP_PEDIDO)
            LEFT JOIN ITEMPEDIDO IP ON (IP.CD_EMPRESA = PD.CD_EMPRESA
                AND IP.NR_PEDIDO = PD.NR_PEDIDO
                AND IP.TP_PEDIDO = PD.TP_PEDIDO
                AND IP.CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN RETORNA_CHAVENOTA(PD.CD_EMPRESA, PD.NR_PEDIDO, PD.TP_PEDIDO) RCH ON (RCH.O_CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN ORDEMPRODEXPEDICAOLOTEPNEU OPRX ON (OPRX.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN EXPEDICAOLOTEPNEU LEXP ON (LEXP.ID = OPRX.IDEXPEDICAOLOTEPNEU
                AND OPRX.IDEMPRESAEXPEDICAOLOTE = LEXP.IDEMPRESA
                AND LEXP.STLOTE <> 'C')
            LEFT JOIN ORDEMCARREGRECAP OCP ON (OCP.IDITEMPEDIDOPNEU = IPP.ID
                AND OCP.ST_ORDEMCARREGRECAP <> 'C')
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PESSOA.CD_PESSOA
                AND EP.CD_ENDERECO = 1)
            INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)    
            WHERE OPR.STORDEM <> 'C'             
                    " . (($cd_regiao != "") ? "AND EP.CD_REGIAOCOMERCIAL IN ($cd_regiao)" : "") . "
                    " . (($empresa  != 0) ? "AND PP.IDEMPRESA IN ($empresa)" : "") . "
                    " . (($pedido != "") ? "AND PP.ID IN ($pedido)" : "") . "
                    " . (($pedido_palm != "") ? "AND PPM.IDPEDIDOMOVEL IN ($pedido_palm)" : "") . "
                    " . (($nm_cliente != "") ? "AND PESSOA.NM_PESSOA LIKE '%$nm_cliente%'" : "") . "  
                    " . (($nm_vendedor != "") ? "AND V.NM_PESSOA LIKE '%$nm_vendedor%'" : "") . "
                    " . (($inicioData != 0) ? "AND PP.DTEMISSAO >= '$inicioData'" : "") . "
                    " . (($fimData != 0) ? "AND PP.DTEMISSAO <= '$fimData'" : "") . "
                    " . (($supervisor != 0) ? "AND VENDEDOR.CD_VENDEDORGERAL IN ($supervisor)" : "") . "
                AND OPR.STEXAMEFINAL <> 'T'
                AND COALESCE(PD.ST_PEDIDO, 'N') <> 'C'
                AND RCH.O_NR_LANCAMENTO IS NULL
                AND PP.STGERAPEDIDO = 'S'
                AND PD.ST_PEDIDO <> 'A'
                --AND PP.ID IN (75610, 181145, 186604, 169038)
            GROUP BY NR_EMBARQUE,
                PP.ID,
                PP.IDEMPRESA,
                PP.IDPESSOA,
                PESSOA.NM_PESSOA,
                OPRX.IDEXPEDICAOLOTEPNEU,
                PP.DTENTREGA,
                EP.CD_REGIAOCOMERCIAL,
                V.NM_PESSOA";

        $data = DB::connection('firebird')->select($query);

        // Ensure data is properly formatted
        $formattedData = Helper::ConvertFormatText($data);

        return $formattedData;
    }
    public function getPneusProduzidosFaturarDetails($NR_COLETA, $NR_EMBARQUE)
    {
        $query = "
            SELECT DISTINCT
                COALESCE(OCP.NR_EMBARQUE, 'SEM EMBARQUE') NR_EMBARQUE,
                PP.ID NR_COLETA,
                PP.IDEMPRESA CD_EMPRESA,
                IPP.IDSERVICOPNEU || '-' || ITEM.DS_ITEM AS DS_ITEM,
                PP.IDPESSOA || '-' || PESSOA.NM_PESSOA NM_PESSOA,
                IPP.VLUNITARIO VALOR,
                OPR.DTFECHAMENTO,
                PP.DTENTREGA,
                CASE
                WHEN OPRX.IDEXPEDICAOLOTEPNEU IS NULL THEN 'NAO'
                ELSE 'SIM'
                END AS EXPEDICIONADO,
                OPRX.IDEXPEDICAOLOTEPNEU AS NR_LOTEEXP,
                OPR.ID || ' - ' || IPP.NRSEQUENCIA || '/' ||(SELECT
                                                            MAX(IPP2.NRSEQUENCIA)
                                                        FROM ITEMPEDIDOPNEU IPP2
                                                        WHERE
                                                            IPP2.IDPEDIDOPNEU = IPP.IDPEDIDOPNEU) AS NRORDEMPRODUCAO
                --EF.DTFIM                                            
            FROM PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
            INNER JOIN PNEU P ON (P.ID = IPP.IDPNEU)
            LEFT JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PLUGORDRECAPPEDIDO POP ON (POP.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN PEDIDO PD ON (PD.CD_EMPRESA = POP.CD_EMPRESA
                AND PD.NR_PEDIDO = POP.NR_PEDIDO
                AND PD.TP_PEDIDO = POP.TP_PEDIDO)
            LEFT JOIN ITEMPEDIDO IP ON (IP.CD_EMPRESA = PD.CD_EMPRESA
                AND IP.NR_PEDIDO = PD.NR_PEDIDO
                AND IP.TP_PEDIDO = PD.TP_PEDIDO
                AND IP.CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN RETORNA_CHAVENOTA(PD.CD_EMPRESA, PD.NR_PEDIDO, PD.TP_PEDIDO) RCH ON (RCH.O_CD_ITEM = IPP.IDSERVICOPNEU)
            LEFT JOIN ORDEMPRODEXPEDICAOLOTEPNEU OPRX ON (OPRX.IDORDEMPRODUCAORECAP = OPR.ID)
            LEFT JOIN EXPEDICAOLOTEPNEU LEXP ON (LEXP.ID = OPRX.IDEXPEDICAOLOTEPNEU
                AND OPRX.IDEMPRESAEXPEDICAOLOTE = LEXP.IDEMPRESA
                AND LEXP.STLOTE <> 'C')
            LEFT JOIN ORDEMCARREGRECAP OCP ON (OCP.IDITEMPEDIDOPNEU = IPP.ID
                AND OCP.ST_ORDEMCARREGRECAP <> 'C')
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = PP.IDPESSOA)

            WHERE
                OPR.STORDEM <> 'C'
                AND OPR.STEXAMEFINAL <> 'T'
                AND COALESCE(PD.ST_PEDIDO, 'N') <> 'C'
                AND RCH.O_NR_LANCAMENTO IS NULL
                AND PP.STGERAPEDIDO = 'S'
                AND PD.ST_PEDIDO <> 'A'
                AND PP.ID IN ($NR_COLETA)              
                
                 " . (($NR_EMBARQUE != 0) ? "AND OCP.NR_EMBARQUE = $NR_EMBARQUE" : "") . "                 
                
                ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    /**
     * Verifica se a ordem de produção está finalizada no exame final.
     *
     * @param int $idOrdem
     * @return array
     */
    public function OrdemFinalizadaExameFinal($idOrdem)
    {
        $query = "
            SELECT
                EF.IDORDEMPRODUCAORECAP
            FROM EXAMEFINALPNEU EF
            WHERE
                EF.IDORDEMPRODUCAORECAP = $idOrdem
                AND EF.ST_ETAPA = 'F' ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getOrdemProducaoById($input)
    {
        $query = "
                SELECT
                    PP.IDEMPRESA,
                    PP.ID PEDIDO,
                    PP.IDVENDEDOR|| '-' || V.NM_PESSOA NM_VENDEDOR,
                    PP.IDPESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                    OPR.ID NRORDEM,
                    OPR.STORDEM,
                    OPR.DSOBSERVACAO,
                    OPR.STEXAMEFINAL,
                    IPP.IDSERVICOPNEU || '-' || SP.DSSERVICO DSSERVICO
                FROM ORDEMPRODUCAORECAP OPR
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
                INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
                INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)
                WHERE
                    OPR.ID = $input->nr_ordem                   
                    AND PP.IDEMPRESA = $input->empresa
        ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getPneusLotePCP()
    {
        $query = "
                SELECT
                    X.NR_COLETA, X.IDPEDIDOMOVEL, X.DTENTRADA, X.DTENTREGA, X.IDPESSOA, X.IDSERVICOPNEU,
                    P.NM_PESSOA, COALESCE(RC.DS_REGIAOCOMERCIAL,'SEM REGIAO') DS_REGIAOCOMERCIAL, SP.DSSERVICO,
                    X.DSCONTROLELOTEPCP, X.NR_LOTE, X.NRLOTESEQDIA, X.DTFIM, X.HRFIM, X.ID NR_OP,
                    X.DT_EXAME, X.DT_MANCHAO, X.DT_COBER, X.DT_VULC, X.DT_FINAL,
                    X.DS_ETAPA, X.DSOBSERVACAO, MP.DSMOTIVO
                FROM (
                    --EXAME INICIAL
                    SELECT PP.ID NR_COLETA, PP.IDPEDIDOMOVEL, PP.IDPESSOA, IPP.IDSERVICOPNEU,
                    C.DSCONTROLELOTEPCP, M.ID NR_LOTE, M.NRLOTESEQDIA, MLE.DTFIM, MLE.HRFIM, OPR.ID,
                    EI.DTFIM DT_EXAME, NULL DT_MANCHAO, NULL DT_COBER, NULL DT_VULC, NULL DT_FINAL, OPR.DSOBSERVACAO,
                    CASE
                        WHEN EI.ID IS NULL THEN 'SEM EXAME'
                        WHEN EI.ST_ETAPA = 'A' THEN 'EXAME'
                    END DS_ETAPA,
                    OPR.DTENTRADA, OPR.DTENTREGA, OPR.CD_MOTIVOALTDTENTREGA
                    FROM ORDEMPRODUCAORECAP OPR
                    INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                    INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                                                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                    INNER JOIN MONTAGEMLOTEPCPETAPARECAP MLE ON (MLE.IDEMPRESA = M.IDEMPRESA
                                                            AND MLE.IDMONTAGEMLOTEPCPRECAP = M.ID
                                                            AND MLE.IDETAPA = 1) -- EXAME
                    INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                                                        AND C.IDEMPRESA = M.IDEMPRESA)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                    LEFT JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID)
                    WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                        --AND PP.IDEMPRESA IN (1)
                        AND M.STLOTE = 'P'
                        AND OPR.STORDEM = 'A'
                        AND (EI.ID IS NULL OR EI.ST_ETAPA = 'A')
                        AND CAST(MLE.DTFIM || ' ' || MLE.HRFIM AS TIMESTAMP) < CURRENT_TIMESTAMP
                    
                    UNION ALL

                    --APLICAÇÃO COLA
                    SELECT PP.ID NR_COLETA, PP.IDPEDIDOMOVEL, PP.IDPESSOA, IPP.IDSERVICOPNEU,
                    C.DSCONTROLELOTEPCP, M.ID NR_LOTE, M.NRLOTESEQDIA, MLE.DTFIM, MLE.HRFIM, OPR.ID,
                    EI.DTFIM DT_EXAME, LM.DTFIM DT_MANCHAO, NULL DT_COBER, NULL DT_VULC, NULL DT_FINAL, OPR.DSOBSERVACAO,
                    CASE
                        WHEN RP.ID IS NULL THEN 'EXAME'
                        WHEN ES.ID IS NULL THEN 'RASPA'
                        WHEN LM.ID IS NULL THEN 'ESCAREACAO'
                        WHEN LM.ST_ETAPA = 'A' THEN 'COLA'
                    END DS_ETAPA,
                    OPR.DTENTRADA, OPR.DTENTREGA, OPR.CD_MOTIVOALTDTENTREGA
                    FROM ORDEMPRODUCAORECAP OPR
                    INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                    INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                                                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                    INNER JOIN MONTAGEMLOTEPCPETAPARECAP MLE ON (MLE.IDEMPRESA = M.IDEMPRESA
                                                            AND MLE.IDMONTAGEMLOTEPCPRECAP = M.ID
                                                            AND MLE.IDETAPA = 5) -- LIMPEZA
                    INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                                                        AND C.IDEMPRESA = M.IDEMPRESA)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                    INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                                                AND EI.ST_ETAPA = 'F')
                    LEFT JOIN RASPAGEMPNEU RP ON (RP.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN ESCAREACAOPNEU ES ON (ES.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN APLICACAOCOLAPNEU LM ON (LM.IDORDEMPRODUCAORECAP = OPR.ID)
                    WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                        --AND PP.IDEMPRESA IN (1)
                        AND M.STLOTE = 'P'
                        AND OPR.STORDEM = 'A'
                        AND (LM.ID IS NULL OR LM.ST_ETAPA = 'A')
                        AND CAST(MLE.DTFIM || ' ' || MLE.HRFIM AS TIMESTAMP) < CURRENT_TIMESTAMP

                    
                    UNION ALL

                    --COBERTURA
                    SELECT PP.ID NR_COLETA, PP.IDPEDIDOMOVEL, PP.IDPESSOA, IPP.IDSERVICOPNEU,
                    C.DSCONTROLELOTEPCP, M.ID NR_LOTE, M.NRLOTESEQDIA, MLE.DTFIM, MLE.HRFIM, OPR.ID,
                    EI.DTFIM DT_EXAME, LM.DTFIM DT_MANCHAO, eb.DTFIM DT_COBER, NULL DT_VULC, NULL DT_FINAL, OPR.DSOBSERVACAO,
                    CASE
                        WHEN PB.ID IS NULL THEN 'ESCAREACAO'
                        WHEN CP.ID IS NULL THEN 'BANDA'
                        WHEN CS.ID IS NULL THEN 'COLA'
                        WHEN EX.ID IS NULL THEN 'AP. CONS'
                        WHEN EB.ID IS NULL THEN 'EXTRUS'
                        WHEN EB.ST_ETAPA = 'A' THEN 'COBERT'
                    END DS_ETAPA,
                    OPR.DTENTRADA, OPR.DTENTREGA, OPR.CD_MOTIVOALTDTENTREGA
                    FROM ORDEMPRODUCAORECAP OPR
                    INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                    INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                                                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                    INNER JOIN MONTAGEMLOTEPCPETAPARECAP MLE ON (MLE.IDEMPRESA = M.IDEMPRESA
                                                            AND MLE.IDMONTAGEMLOTEPCPRECAP = M.ID
                                                            AND MLE.IDETAPA = 9) -- COBERTURA
                    INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                                                        AND C.IDEMPRESA = M.IDEMPRESA)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                    INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                                                AND EI.ST_ETAPA = 'F')
                    INNER JOIN LIMPEZAMANCHAO LM ON (LM.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN PREPARACAOBANDAPNEU PB ON (PB.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN APLICACAOCOLAPNEU CP ON (CP.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN APLICCONSERTOPNEU CS ON (CS.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN EXTRUSORAPNEU EX ON (EX.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN EMBORRACHAMENTO EB ON (EB.IDORDEMPRODUCAORECAP = OPR.ID)
                    WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                        --AND PP.IDEMPRESA IN (1)
                        AND M.STLOTE = 'P'
                        AND OPR.STORDEM = 'A'
                        AND (EB.ID IS NULL OR EB.ST_ETAPA = 'A')
                        AND CAST(MLE.DTFIM || ' ' || MLE.HRFIM AS TIMESTAMP) < CURRENT_TIMESTAMP

                    
                    UNION ALL

                    --VULCANIZAÇÃO
                    SELECT PP.ID NR_COLETA, PP.IDPEDIDOMOVEL, PP.IDPESSOA, IPP.IDSERVICOPNEU, 
                    C.DSCONTROLELOTEPCP, M.ID NR_LOTE, M.NRLOTESEQDIA, MLE.DTFIM, MLE.HRFIM, OPR.ID,
                    EI.DTFIM DT_EXAME, LM.DTFIM DT_MANCHAO, eb.DTFIM DT_COBER, VP.DTFIM DT_VULC, NULL DT_FINAL, OPR.DSOBSERVACAO,
                    CASE
                        WHEN EN.ID IS NULL THEN 'COBERT'
                        WHEN MO.ID IS NULL THEN 'ENVELOPA'
                        WHEN VP.ID IS NULL THEN 'MONTAGEM'
                        WHEN VP.ST_ETAPA = 'A' THEN 'VULCANIZ'
                    END DS_ETAPA,
                    OPR.DTENTRADA, OPR.DTENTREGA, OPR.CD_MOTIVOALTDTENTREGA
                    FROM ORDEMPRODUCAORECAP OPR
                    INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                    INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                                                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                    INNER JOIN MONTAGEMLOTEPCPETAPARECAP MLE ON (MLE.IDEMPRESA = M.IDEMPRESA
                                                            AND MLE.IDMONTAGEMLOTEPCPRECAP = M.ID
                                                            AND MLE.IDETAPA = 11) -- VULCANIZAÇÃO
                    INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                                                        AND C.IDEMPRESA = M.IDEMPRESA)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                    INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                                                AND EI.ST_ETAPA = 'F')
                    LEFT JOIN LIMPEZAMANCHAO LM ON (LM.IDORDEMPRODUCAORECAP = OPR.ID)
                    INNER JOIN EMBORRACHAMENTO EB ON (EB.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN ENVELOPAMENTO EN ON (EN.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN MONTAGEMRECAP MO ON (MO.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN VULCANIZACAO VP ON (VP.IDORDEMPRODUCAORECAP = OPR.ID)
                    WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                        --AND PP.IDEMPRESA IN (1)
                        AND M.STLOTE = 'P'
                        AND OPR.STORDEM = 'A'
                        AND (VP.ID IS NULL OR VP.ST_ETAPA = 'A')
                        AND CAST(MLE.DTFIM || ' ' || MLE.HRFIM AS TIMESTAMP) < CURRENT_TIMESTAMP

                    
                    UNION ALL

                    --EXAME FINAL
                    SELECT PP.ID NR_COLETA, PP.IDPEDIDOMOVEL, PP.IDPESSOA, IPP.IDSERVICOPNEU, 
                    C.DSCONTROLELOTEPCP, M.ID NR_LOTE, M.NRLOTESEQDIA, MLE.DTFIM, MLE.HRFIM, OPR.ID,
                    EI.DTFIM DT_EXAME, LM.DTFIM DT_MANCHAO, eb.DTFIM DT_COBER, VP.DTFIM DT_VULC, EF.DTFIM DT_FINAL, OPR.DSOBSERVACAO,
                    CASE
                        WHEN DE.ID IS NULL THEN 'VULC'
                        WHEN EF.ID IS NULL THEN 'DESENVEL'
                        WHEN EF.ST_ETAPA = 'A' THEN 'DESENVEL'
                    END DS_ETAPA,
                    OPR.DTENTRADA, OPR.DTENTREGA, OPR.CD_MOTIVOALTDTENTREGA
                    FROM ORDEMPRODUCAORECAP OPR
                    INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                    INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                                                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                    INNER JOIN MONTAGEMLOTEPCPETAPARECAP MLE ON (MLE.IDEMPRESA = M.IDEMPRESA
                                                            AND MLE.IDMONTAGEMLOTEPCPRECAP = M.ID
                                                            AND MLE.IDETAPA = 12) -- EXAME FINAL
                    INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                                                        AND C.IDEMPRESA = M.IDEMPRESA)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                    INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                    INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                                                AND EI.ST_ETAPA = 'F')
                    LEFT JOIN LIMPEZAMANCHAO LM ON (LM.IDORDEMPRODUCAORECAP = OPR.ID)
                    INNER JOIN VULCANIZACAO VP ON (VP.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN EMBORRACHAMENTO EB ON (EB.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN DESENVELOPAMENTO DE ON (DE.IDORDEMPRODUCAORECAP = OPR.ID)
                    LEFT JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID)
                    WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                        --AND PP.IDEMPRESA IN (1)
                        AND M.STLOTE = 'P'
                        AND OPR.STORDEM = 'A'
                        AND (EF.ID IS NULL OR EF.ST_ETAPA = 'A')
                        AND CAST(MLE.DTFIM || ' ' || MLE.HRFIM AS TIMESTAMP) < CURRENT_TIMESTAMP

                ) X
                INNER JOIN SERVICOPNEU SP ON (SP.ID = X.IDSERVICOPNEU)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = X.IDPESSOA)
                INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
                                            AND EP.CD_ENDERECO = 1)
                LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
                LEFT JOIN MOTIVOPNEU MP ON (MP.ID = X.CD_MOTIVOALTDTENTREGA)
                ORDER BY X.DTFIM DESC, X.NR_LOTE, X.NRLOTESEQDIA, X.ID
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getLotePCP()
    {
        $query = "
                SELECT
                    X.IDEMPRESA CD_EMPRESA,
                    X.DSCONTROLELOTEPCP,
                    X.ID NR_LOTE,
                    X.NRLOTESEQDIA,
                    X.DTPRODUCAO,
                    SUM(X.QTDE_TOT) QTDE_TOT_LOTE,
                    SUM(X.QTDE_PROD) QTDE_EM_PROD,
                    SUM(X.QTDE_SEMEXAME) QTDE_SEMEXAME
                FROM (
                --QTDE TOTAL DO LOTE
                SELECT
                    PP.IDEMPRESA,
                    C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    COUNT(OPR.ID) QTDE_TOT,
                    NULL QTDE_PROD,
                    NULL QTDE_SEMEXAME
                FROM ORDEMPRODUCAORECAP OPR
                INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                    AND C.IDEMPRESA = M.IDEMPRESA)
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                    --AND PP.IDEMPRESA IN (101,201)
                    AND M.STLOTE = 'P'
                    -- AND OPR.ID = 905
                GROUP BY C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    PP.IDEMPRESA 

                UNION ALL

                --PNEUS AINDA EM PRODUÇÃO
                SELECT
                    PP.IDEMPRESA,
                    C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    NULL QTDE_TOT,
                    COUNT(OPR.ID) QTDE_PROD,
                    NULL QTDE_SEMEXAME
                FROM ORDEMPRODUCAORECAP OPR
                INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                    AND C.IDEMPRESA = M.IDEMPRESA)
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                    --AND PP.IDEMPRESA IN (101,201)
                    AND M.STLOTE = 'P'
                    AND OPR.STORDEM = 'A'
                GROUP BY C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    PP.IDEMPRESA 

                UNION ALL

                --PNEUS SEM EXAME INICIAL
                SELECT
                    PP.IDEMPRESA,
                    C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    NULL QTDE_TOT,
                    NULL QTDE_PROD,
                    COUNT(OPR.ID) QTDE_SEMEXAME
                FROM ORDEMPRODUCAORECAP OPR
                INNER JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
                INNER JOIN MONTAGEMLOTEPCPRECAP M ON (M.ID = PCP.IDMONTAGEMLOTEPCP
                    AND M.IDEMPRESA = PCP.IDEMPRESA)
                INNER JOIN CONTROLELOTEPCPRECAP C ON (C.ID = M.IDCONTROLELOTEPCPRECAP
                    AND C.IDEMPRESA = M.IDEMPRESA)
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
                INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                LEFT JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID)
                WHERE IPP.STCANCELADO = 'N'
                    AND IPP.STGARANTIA = 'N'
                    --AND PP.IDEMPRESA IN (101,201)
                    AND M.STLOTE = 'P'
                    AND OPR.STORDEM = 'A'
                    AND EI.ID IS NULL
                GROUP BY C.DSCONTROLELOTEPCP,
                    M.ID,
                    M.NRLOTESEQDIA,
                    M.DTPRODUCAO,
                    PP.IDEMPRESA) X
                GROUP BY X.DSCONTROLELOTEPCP,
                    X.ID,
                    X.NRLOTESEQDIA,
                    X.DTPRODUCAO,
                    X.IDEMPRESA
                ORDER BY X.DTPRODUCAO DESC,
                    X.ID  
        ";

        $data = DB::connection('firebird')->select($query); 

        return Helper::ConvertFormatText($data);
    }
}
