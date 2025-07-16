<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
}
