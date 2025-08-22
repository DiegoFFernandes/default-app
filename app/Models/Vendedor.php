<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;

class Vendedor extends Model
{
    use HasFactory;

    public function FindVendedorJunsoftAll($search)
    {
        $query = "select first 10 p.cd_pessoa id, 
                    cast(p.nm_pessoa as varchar(100) character set utf8) nm_vendedor
                    from vendedor v
                    inner join pessoa p on (v.cd_vendedor = p.cd_pessoa)                    
                    where p.st_ativa = 'S'
                        --and p.cd_tipopessoa in (1,3)
                        and p.nm_pessoa like '%$search%'";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getAcompanhamentoVendedor($cd_empresa)
    {
        $query = "
            SELECT
                X.*,
                CASE
                WHEN COALESCE(PROJECAO_ATUAL, 0) >= QT_METAPADRAO THEN 1
                WHEN COALESCE(PROJECAO_ATUAL, 0) < QT_METAPADRAO THEN 2
                END VENCEU_ATUAL,
                --- carinha das metas atual
                CASE
                WHEN COALESCE(QT_FATURADOPADRAOANTERIOR, 0) >= QT_METAPADRAO THEN 1
                WHEN COALESCE(QT_FATURADOPADRAOANTERIOR, 0) < QT_METAPADRAO THEN 2
                END VENCEU_ANTERIOR --carinha das metas anterior
            FROM (SELECT
                    X.CD_PESSOA,
                    X.NM_PESSOA,
                    SUM(COALESCE(QT_PNEU, 0)) QT_PNEU, -- qtde coleta
                    SUM(COALESCE(QT_COLETAPADRAO, 0)) QT_PNEUPADRAO, --qtde coleta padrão
                    SUM(COALESCE(QT_FATURADO, 0)) QT_FATURADO, --qtde faturado
                    SUM(COALESCE(VL_FATURADO, 0)) VL_FATURADO, -- valor faturado
                    SUM(COALESCE(QT_FATURADOPADRAO, 0)) QT_FATURADOPADRAO, -- qtde faturado padrao
                    SUM(COALESCE(QT_RECUSADO, 0)) QT_RECUSADO, -- qtde recusado
                    SUM(COALESCE(QT_RECUSADOANTERIOR, 0)) QT_RECUSADOANTERIOR, -- qtde recusada mes anterior
                    SUM(COALESCE(QT_PNEUMESANTERIOR, 0)) QT_PNEUANTERIOR, --qtde coleta mes anterior
                    SUM(COALESCE(QT_PNEUPADRAOANTERIOR, 0)) QT_PNEUPADRAOANTERIOR, --qtde coleta padrão mes anterior
                    SUM(COALESCE(QT_FATURADOMESANTERIOR, 0)) QT_FATURADOMESANTERIOR, --qtde faturado mes anterior
                    SUM(COALESCE(VL_FATURADOMESANTERIOR, 0)) VL_FATURADOMESANTERIOR, --valor faturado mes anterior
                    SUM(COALESCE(QT_FATURADOPADRAOANTERIOR, 0)) QT_FATURADOPADRAOANTERIOR, -- qtde faturado padrão mes anterior
                    SUM(COALESCE(QT_PRODUZIDO, 0)) QT_PRODUZIDO, -- qtde produzida mes
                    SUM(COALESCE(QT_PRODUZIDOANTERIOR, 0)) QT_PRODUZIDOANTERIOR, --qtde produzida mes anterior
                    SUM(COALESCE(QT_MEDIAPADRAO, 0)) QT_PRODUZIDOPADRAO, -- qtde prodizdo padrão
                    SUM(COALESCE(QT_MEDIAPADRAOANTERIOR, 0)) QT_PRODUZIDOPADRAOANTERIOR, -- qtde produzido padrao mes anterior
                    SUM(COALESCE(QT_METAPADRAO, 0)) QT_METAPADRAO, --qtde meta padraõ tfa007

                    ROUND(SUM((COALESCE(QT_FATURADOPADRAO, 0) / COALESCE(NULLIF(CAST((J.O_QT_DIASMES - (SELECT
                                                                                                            COUNT(1)
                                                                                                        FROM FERIADO F
                                                                                                        WHERE SUBSTR(F.DT_FERIADO, 4, 5) = EXTRACT(MONTH FROM CURRENT_DATE)
                                                                                                                AND SUBSTR(F.DT_FERIADO, 1, 2) < EXTRACT(DAY FROM CURRENT_DATE))) AS NUMERIC(15,4)), 0), 1)) * ((J.O_QT_DIAS - 1) - (SELECT
                                                                                                                                                                                                                                        COUNT(1)
                                                                                                                                                                                                                                    FROM FERIADO F
                                                                                                                                                                                                                                    WHERE SUBSTRING(F.DT_FERIADO FROM 4 FOR 5) = EXTRACT(MONTH FROM CURRENT_DATE))))) PROJECAO_ATUAL, -- projeção de faturamento padrão
                    0 PROJECAO_ANTERIOR --projeção de faturamento mes anterior

                FROM (

                --QUANTIDADE DE COLETAS MES ATUAL
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    COUNT(IPB.IDITEMPEDIDOPNEU) QT_PNEU,
                    (COUNT(IPB.IDITEMPEDIDOPNEU) * SP.VL_MEDIDAPADRAO) QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)
                INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
                INNER JOIN ITEM IT ON (IT.CD_ITEM = SP.ID)
                WHERE CAST(PN.DTEMISSAO AS TIMESTAMP) BETWEEN '01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AND CURRENT_DATE
                        AND PN.STPEDIDO <> 'C'
                        AND IPP.STCANCELADO = 'N'
                        AND PN.IDEMPRESA = 1
                        AND IT.CD_GRUPO IN (102, 105, 140, 132, 130)
                        AND IPB.CD_TIPO = 1

                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    SP.VL_MEDIDAPADRAO

                UNION ALL

                --FATURADO MES ATUAL
                SELECT
                    LPAD(EXTRACT(MONTH FROM X.DT_EMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM X.DT_EMISSAO) MES_COLETA,
                    X.CD_PESSOA,
                    X.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    SUM(X.QTDE) QT_FATURADO,
                    SUM(X.VL_LIQUIDO) VL_FATURADO,
                    SUM(X.QT_FATURADOPADRAO) QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM (SELECT DISTINCT
                            I.CD_EMPRESA,
                            I.NR_LANCAMENTO,
                            I.TP_NOTA,
                            I.CD_SERIE,
                            I.CD_ITEM,
                            CASE
                            WHEN TP.TP_CALCULO = 'P' THEN I.PS_ITEMNOTA
                            ELSE I.QT_ITEMNOTA
                            END QTDE,
                            PV.CD_PESSOA,
                            PV.NM_PESSOA,
                            N.DT_EMISSAO,
                            I.VL_LIQUIDO,
                            (QT_ITEMNOTA * SP.VL_MEDIDAPADRAO) QT_FATURADOPADRAO
                        FROM NOTA N
                        INNER JOIN ITEMNOTA I ON (I.CD_EMPRESA = N.CD_EMPRESA
                            AND I.NR_LANCAMENTO = N.NR_LANCAMENTO
                            AND I.TP_NOTA = N.TP_NOTA
                            AND I.CD_SERIE = N.CD_SERIE)
                        INNER JOIN ITEM II ON (II.CD_ITEM = I.CD_ITEM)
                        INNER JOIN SERVICOPNEU SP ON (SP.ID = II.CD_ITEM)
                        INNER JOIN TIPOCALCULO TP ON (TP.CD_TIPOCALCULO = II.CD_TIPOCALCULO)
                        LEFT JOIN ITEMNOTAVENDEDOR ITV ON (ITV.CD_EMPRESA = I.CD_EMPRESA
                            AND ITV.NR_LANCAMENTO = I.NR_LANCAMENTO
                            AND ITV.TP_NOTA = I.TP_NOTA
                            AND ITV.CD_SERIE = I.CD_SERIE
                            AND ITV.CD_ITEM = I.CD_ITEM)
                        INNER JOIN PESSOA PV ON (PV.CD_PESSOA = ITV.CD_VENDEDOR)
                        INNER JOIN MOVIMENTACAO M ON (M.CD_MOVIMENTACAO = I.CD_MOVIMENTACAO)
                        WHERE CAST(N.DT_EMISSAO AS DATE) BETWEEN '01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AND CURRENT_DATE
                            AND N.ST_NOTA = 'V'
                            AND M.ST_RECEITA = 'S'
                            AND N.CD_EMPRESA = 1
                            AND ITV.CD_TIPO = 1
                        GROUP BY LPAD(EXTRACT(MONTH FROM N.DT_EMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM N.DT_EMISSAO),
                            I.CD_EMPRESA,
                            I.NR_LANCAMENTO,
                            I.TP_NOTA,
                            I.CD_SERIE,
                            I.CD_ITEM,
                            N.DT_EMISSAO,
                            TP.TP_CALCULO,
                            I.PS_ITEMNOTA,
                            I.QT_ITEMNOTA,
                            VL_LIQUIDO,
                            SP.VL_MEDIDAPADRAO,
                            PV.CD_PESSOA,
                            PV.NM_PESSOA) X
                GROUP BY X.CD_PESSOA,
                    X.NM_PESSOA,
                    MES_COLETA

                UNION ALL

                --RECUSADO
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    COUNT(OPR.ID) QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
                WHERE CAST(PN.DTEMISSAO AS TIMESTAMP) BETWEEN '01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AND CURRENT_DATE
                        AND OPR.STORDEM = 'F'
                        AND OPR.STEXAMEFINAL = 'R'
                        AND PN.IDEMPRESA = 1
                        AND IPB.CD_TIPO = 1
                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA

                UNION ALL

                --RECUSADO ANTERIOR
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    COUNT(OPR.ID) QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
                WHERE CAST(PN.DTEMISSAO AS TIMESTAMP) BETWEEN DATEADD(-1 MONTH TO CAST('01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AS DATE)) AND LASTDAYMONTH(DATEADD(-1 MONTH TO CURRENT_DATE))
                        AND OPR.STORDEM = 'F'
                        AND OPR.STEXAMEFINAL = 'R'
                        AND PN.IDEMPRESA = 1
                        AND IPB.CD_TIPO = 1
                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA

                UNION ALL

                --QUANTIDADE COLETAS MES ANTERIOR
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    COUNT(IPB.IDITEMPEDIDOPNEU) QT_PNEUMESANTERIOR,
                    (COUNT(IPB.IDITEMPEDIDOPNEU) * SP.VL_MEDIDAPADRAO) QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)
                INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
                INNER JOIN ITEM IT ON (IT.CD_ITEM = SP.ID)
                WHERE CAST(PN.DTEMISSAO AS TIMESTAMP) BETWEEN DATEADD(-1 MONTH TO CAST('01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AS DATE)) AND LASTDAYMONTH(DATEADD(-1 MONTH TO CURRENT_DATE))
                        AND PN.STPEDIDO <> 'C'
                        AND IPP.STCANCELADO = 'N'
                        AND IT.CD_GRUPO IN (102, 105, 140, 132, 130)
                        AND PN.IDEMPRESA = 1
                        AND IPB.CD_TIPO = 1
                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    SP.VL_MEDIDAPADRAO

                UNION ALL

                --FATURADO MES ANTERIOR
                SELECT
                    LPAD(EXTRACT(MONTH FROM X.DT_EMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM X.DT_EMISSAO) MES_COLETA,
                    X.CD_PESSOA,
                    X.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    SUM(X.QTDE) QT_FATURADOMESANTERIOR,
                    SUM(X.VL_LIQUIDO) VL_FATURADOMESANTERIOR,
                    SUM(X.QT_FATURADOPADRAOANTERIOR) QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM (SELECT DISTINCT
                            I.CD_EMPRESA,
                            I.NR_LANCAMENTO,
                            I.TP_NOTA,
                            I.CD_SERIE,
                            I.CD_ITEM,
                            CASE
                            WHEN TP.TP_CALCULO = 'P' THEN I.PS_ITEMNOTA
                            ELSE I.QT_ITEMNOTA
                            END QTDE,
                            PV.CD_PESSOA,
                            PV.NM_PESSOA,
                            N.DT_EMISSAO,
                            I.VL_LIQUIDO,
                            (QT_ITEMNOTA * SP.VL_MEDIDAPADRAO) QT_FATURADOPADRAOANTERIOR
                        FROM NOTA N
                        INNER JOIN ITEMNOTA I ON (I.CD_EMPRESA = N.CD_EMPRESA
                            AND I.NR_LANCAMENTO = N.NR_LANCAMENTO
                            AND I.TP_NOTA = N.TP_NOTA
                            AND I.CD_SERIE = N.CD_SERIE)
                        INNER JOIN ITEM II ON (II.CD_ITEM = I.CD_ITEM)
                        INNER JOIN TIPOCALCULO TP ON (TP.CD_TIPOCALCULO = II.CD_TIPOCALCULO)
                        INNER JOIN ITEMNOTAVENDEDOR ITV ON (ITV.CD_EMPRESA = I.CD_EMPRESA
                            AND ITV.NR_LANCAMENTO = I.NR_LANCAMENTO
                            AND ITV.TP_NOTA = I.TP_NOTA
                            AND ITV.CD_SERIE = I.CD_SERIE
                            AND ITV.CD_ITEM = I.CD_ITEM)
                        INNER JOIN PESSOA PV ON (PV.CD_PESSOA = ITV.CD_VENDEDOR)
                        INNER JOIN SERVICOPNEU SP ON (SP.ID = II.CD_ITEM)
                        INNER JOIN MOVIMENTACAO M ON (M.CD_MOVIMENTACAO = I.CD_MOVIMENTACAO)
                        WHERE CAST(N.DT_EMISSAO AS TIMESTAMP) BETWEEN DATEADD(-1 MONTH TO CAST('01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AS DATE)) AND LASTDAYMONTH(DATEADD(-1 MONTH TO CURRENT_DATE))
                            AND N.ST_NOTA = 'V'
                            AND M.ST_RECEITA = 'S'
                            AND N.CD_EMPRESA = 1
                            AND ITV.CD_TIPO = 1
                        GROUP BY LPAD(EXTRACT(MONTH FROM N.DT_EMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM N.DT_EMISSAO),
                            I.CD_EMPRESA,
                            I.NR_LANCAMENTO,
                            I.TP_NOTA,
                            I.CD_SERIE,
                            I.CD_ITEM,
                            N.DT_EMISSAO,
                            TP.TP_CALCULO,
                            I.PS_ITEMNOTA,
                            I.QT_ITEMNOTA,
                            I.VL_LIQUIDO,
                            SP.VL_MEDIDAPADRAO,
                            PV.CD_PESSOA,
                            PV.NM_PESSOA) X
                GROUP BY X.CD_PESSOA,
                    X.NM_PESSOA,
                    MES_COLETA

                UNION ALL

                --PNEUS PRODUZIDOS
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    COUNT(IPB.IDITEMPEDIDOPNEU) QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    (COUNT(IPB.IDITEMPEDIDOPNEU) * SP.VL_MEDIDAPADRAO) QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                        AND EI.STEXAMEINICIAL <> 'R')
                INNER JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID
                        AND EF.STEXAMEFINAL <> 'R'
                        AND EF.ST_ETAPA = 'F')
                INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)

                WHERE CAST(OPR.DTFECHAMENTO AS TIMESTAMP) BETWEEN '01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AND CURRENT_DATE
                        AND PN.IDEMPRESA = 1
                        AND IPB.CD_TIPO = 1

                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    SP.VL_MEDIDAPADRAO

                UNION ALL

                --PNEUS PRODUZIDOS NO MES ANTERIOR
                SELECT
                    LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO) MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    COUNT(IPB.IDITEMPEDIDOPNEU) QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    (COUNT(IPB.IDITEMPEDIDOPNEU) * SP.VL_MEDIDAPADRAO) QT_MEDIAPADRAOANTERIOR,
                    NULL QT_METAPADRAO
                FROM PEDIDOPNEU PN
                INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PN.ID)
                INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID
                        AND EI.STEXAMEINICIAL <> 'R')
                INNER JOIN EXAMEFINALPNEU EF ON (EF.IDORDEMPRODUCAORECAP = OPR.ID
                        AND EF.STEXAMEFINAL <> 'R'
                        AND EF.ST_ETAPA = 'F')
                INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
                INNER JOIN ITEMPEDIDOPNEUBORRACHEIRO IPB ON (IPB.IDITEMPEDIDOPNEU = IPP.ID)
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = IPB.IDBORRACHEIRO)

                WHERE CAST(OPR.DTFECHAMENTO AS TIMESTAMP) BETWEEN DATEADD(-1 MONTH TO CAST('01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AS DATE)) AND LASTDAYMONTH(DATEADD(-1 MONTH TO CURRENT_DATE))
                        AND PN.IDEMPRESA = 1
                        AND IPB.CD_TIPO = 1

                GROUP BY LPAD(EXTRACT(MONTH FROM PN.DTEMISSAO), 2, 0) || '/' || EXTRACT(YEAR FROM PN.DTEMISSAO),
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    SP.VL_MEDIDAPADRAO

                UNION ALL

                SELECT
                    NULL MES_COLETA,
                    PV.CD_PESSOA,
                    PV.NM_PESSOA,
                    NULL QT_PNEU,
                    NULL QT_COLETAPADRAO,
                    NULL QT_FATURADO,
                    NULL VL_FATURADO,
                    NULL QT_FATURADOPADRAO,
                    NULL QT_RECUSADO,
                    NULL QT_RECUSADOANTERIOR,
                    NULL QT_PNEUMESANTERIOR,
                    NULL QT_PNEUPADRAOANTERIOR,
                    NULL QT_FATURADOMESANTERIOR,
                    NULL VL_FATURADOMESANTERIOR,
                    NULL QT_FATURADOPADRAOANTERIOR,
                    NULL QT_PRODUZIDO,
                    NULL QT_PRODUZIDOANTERIOR,
                    NULL QT_MEDIAPADRAO,
                    NULL QT_MEDIAPADRAOANTERIOR,
                    MT.QT_META QT_METAPADRAO
                FROM VENDEDOR V
                INNER JOIN PESSOA PV ON (PV.CD_PESSOA = V.CD_VENDEDOR)
                INNER JOIN METAVENDEDOR MT ON (MT.CD_VENDEDOR = V.CD_VENDEDOR)
                WHERE CAST(MT.DT_META AS TIMESTAMP) BETWEEN '01.' || EXTRACT(MONTH FROM CURRENT_DATE) || '.' || EXTRACT(YEAR FROM CURRENT_DATE) AND CURRENT_DATE

                ) X
                LEFT JOIN JV_RETORNADIASUTEIS(CURRENT_DATE) J ON (1 = 1)
                GROUP BY X.NM_PESSOA,
                    X.CD_PESSOA --, VENCEU_ATUAL
                HAVING SUM(COALESCE(QT_PNEU, 0)) > 0
                ORDER BY PROJECAO_ATUAL DESC) X";

        $key = "acompanhamento-vendedor" . Auth::user()->id;

        return Cache::remember($key, now()->addMinutes(30), function () use ($query) {
            $data = DB::connection('firebird')->select($query);
            return Helper::ConvertFormatText($data);
        });
            
    }
}
