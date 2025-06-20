<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AcompanhamentoPneu extends Model
{

    use HasFactory;
    protected $connection;


    public function IdOrdemProducao($consulta)
    {
        $query = "select IDITEMPEDIDOPNEU from ordemproducaorecap where id = $consulta";
        return DB::connection('firebird')->select($query);
    }
    public function BuscaSetores($nr_ordem)
    {
        $query = "
                SELECT
                    CAST(O_DS_ETAPA AS VARCHAR(200) CHARACTER SET UTF8) AS O_DS_ETAPA,
                    O_HR_ENTRADA,
                    O_HR_SAIDA,
                    CAST(O_NM_USUARIO AS VARCHAR(200) CHARACTER SET UTF8) AS O_NM_USUARIO,
                    CAST(O_DS_COMPLEMENTOETAPA AS VARCHAR(1000) CHARACTER SET UTF8) AS O_DS_COMPLEMENTOETAPA,
                    O_DT_ENTRADA,
                    O_DT_SAIDA,
                    (
                    CASE O_ST_RETRABALHO
                    WHEN 'N' THEN 'NAO'
                    ELSE 'SIM'
                    END) O_ST_RETRABALHO
                FROM RETORNA_ACOMPANHAMENTOPNEU($nr_ordem) R
                ORDER BY CAST(R.O_DT_ENTRADA || ' ' || R.O_HR_ENTRADA AS DOM_TIMESTAMP)";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function showDataPneus($nr_ordem)
    {
        $query = "select IPP.idpedidopneu PEDIDO, OPR.id ORDEM, PP.idpessoa ||' - '|| P.NM_PESSOA CLIENTE, SP.dsservico SERVICO,
        MP.dsmodelo||' - '||M.dsmarca as MODELO, MD.dsmedidapneu MEDIDA,
        PN.nrserie SERIE, PN.nrfogo FOGO, PN.nrdot DOT, LOPR.idmontagemlotepcp LOTE, OPR.stalterando alterando
        FROM ITEMPEDIDOPNEU IPP
        INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.idpedidopneu)
        INNER JOIN PNEU PN on (PN.ID = IPP.idpneu)
        INNER JOIN PESSOA P ON (P.cd_pessoa = PP.idpessoa)
        INNER JOIN servicopneu SP ON (SP.id = IPP.idservicopneu)
        INNER JOIN ORDEMPRODUCAORECAP OPR ON ( OPR.iditempedidopneu = IPP.id)
        INNER JOIN MODELOPNEU MP ON ( MP.id = PN.idmodelopneu)
        INNER JOIN MARCAPNEU M ON (M.id = MP.idmarcapneu)
        INNER JOIN MEDIDAPNEU MD ON (MD.id = PN.idmedidapneu)
        LEFT JOIN LOTEPCPORDEMPRODUCAORECAP LOPR ON (LOPR.idordemproducao = OPR.ID)
        LEFT JOIN MONTAGEMLOTEPCPRECAP MLP ON (MLP.id = LOPR.idmontagemlotepcp)
        LEFT JOIN controlelotepcprecap CLR ON (CLR.id = MLP.idcontrolelotepcprecap)
        where OPR.id = $nr_ordem
        group by IPP.idpedidopneu, OPR.id, PP.idpessoa, P.NM_PESSOA, SP.dsservico, MODELO, MD.dsmedidapneu,
        PN.nrserie, PN.nrfogo, PN.nrdot, LOPR.idmontagemlotepcp, OPR.stalterando";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function ListPedidoPneu($cd_regiao, $data)
    {
        if (is_null($data)) {
            $pedido = "";
            $pedido_palm = "";
            $nm_cliente = "";
            $nm_vendedor = "";
            $idvendedor = "";
            $empresa = 0;
            $grupo_item = 0;
            $inicioData = 0;
            $fimData = 0;
        } else {
            $empresa = $data['cd_empresa'];
            $pedido = $data['pedido'];
            $pedido_palm = $data['pedido_palm'];
            $nm_cliente = $data['nm_cliente'];
            $nm_vendedor = $data['nm_vendedor'];
            $idvendedor = $data['idvendedor'];
            $grupo_item = $data['grupo_item'];
            $inicioData = $data['dt_inicial'];
            $fimData = $data['dt_final'];
        }

        $query = "
                    SELECT
                        PP.IDEMPRESA CD_EMPRESA,
                        PP.ID,
                        PPM.IDPEDIDOMOVEL,
                        PP.IDVENDEDOR,
                        CAST(PP.IDPESSOA || ' - ' || PC.NM_PESSOA AS VARCHAR(200) CHARACTER SET UTF8) PESSOA,
                        PP.IDVENDEDOR||' - '||V.NM_PESSOA NM_VENDEDOR,
                        EP.CD_REGIAOCOMERCIAL,
                        PPM.DTREGISTROPALM,
                        PP.DTEMISSAO,
                        PP.DTENTREGA DTENTREGAPED,
                        (
                        CASE PP.STPEDIDO
                            WHEN 'A' THEN 'ATENDIDO'
                            WHEN 'C' THEN 'CANCELADO'
                            WHEN 'T' THEN 'EM PRODUCAO'
                            WHEN 'N' THEN 'AGUARDANDO'
                            WHEN 'B' THEN 'BLOQUEADO'
                            WHEN 'P' THEN 'PRODUCAO PARCIAL'
                            ELSE PP.STPEDIDO
                        END) STPEDIDO,
                        TP.DSTIPOPEDIDO,
                        COUNT(IPP.id) QTDPNEUS,
                        CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO, 
                        FORMAPAGTO.DS_FORMAPAGTO,
                        CONDPAGTO.DS_CONDPAGTO,
                        CASE
                            WHEN PP.TP_BLOQUEIO = 'F' AND PP.STPEDIDO = 'B' THEN 'FINANCEIRO'
                            WHEN PP.TP_BLOQUEIO = 'C' AND PP.STPEDIDO = 'B' THEN 'COMERCIAL'
                            WHEN PP.TP_BLOQUEIO = 'A' AND PP.STPEDIDO = 'B' THEN 'AMBOS'
                            ELSE 'LIBERADO'
                        END MOTIVO,
                        PP.DSOBSERVACAO,
                        pp.DSBLOQUEIO
                        
                    FROM PEDIDOPNEU PP
                    INNER JOIN TIPOPEDIDOPNEU TP ON (TP.ID = PP.IDTIPOPEDIDO)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.idpedidopneu = PP.id)  
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)                  
                    INNER JOIN PESSOA PC ON (PC.CD_PESSOA = PP.IDPESSOA)
                    INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)
                    
                    INNER JOIN CONDPAGTO ON (CONDPAGTO.CD_CONDPAGTO = PP.IDCONDPAGTO)
                    INNER JOIN FORMAPAGTO ON (FORMAPAGTO.CD_FORMAPAGTO = PP.CDFORMAPAGTO)

                    LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PC.CD_PESSOA
                                                        AND EP.CD_ENDERECO = PP.IDENDERECO)
                    LEFT JOIN PEDIDOPNEUMOVEL PPM ON (PPM.ID = PP.ID)
                    WHERE  
                        --" . (($inicioData != 0) ? "PP.DTEMISSAO between '$inicioData' and '$fimData' " : "PP.DTEMISSAO BETWEEN CURRENT_DATE - 120 AND CURRENT_DATE") . " 
                        PP.DTEMISSAO BETWEEN '04.02.2025' AND '05.02.2025'
                        " . (($cd_regiao != "") ? "AND EP.cd_regiaocomercial IN ($cd_regiao)" : "") . "
                        " . (($empresa  != 0) ? "AND PP.IDEMPRESA IN ($empresa)" : "") . "
                        " . (($pedido != "") ? "AND PP.ID IN ($pedido)" : "") . "
                        " . (($pedido_palm != "") ? "AND PPM.IDPEDIDOMOVEL IN ($pedido_palm)" : "") . "
                        " . (($nm_cliente != "") ? "AND PC.NM_PESSOA LIKE '%$nm_cliente%'" : "") . "  
                        " . (($nm_vendedor != "") ? "AND V.NM_PESSOA LIKE '%$nm_vendedor%'" : "") . "
                        " . (($idvendedor != "") ? "AND PP.IDVENDEDOR IN ($idvendedor)" : "") . "
                        " . (($grupo_item != 0) ? "AND ITEM.CD_GRUPO IN ($grupo_item)" : "") . "   
                        AND PP.STPEDIDO <> 'C'               
                    GROUP BY PP.IDEMPRESA,
                        PP.ID,
                        PPM.IDPEDIDOMOVEL,
                        PP.IDPESSOA,
                        PC.NM_PESSOA,
                        PP.IDVENDEDOR,
                        V.NM_PESSOA,
                        EP.CD_REGIAOCOMERCIAL,
                        PP.DTEMISSAO,
                        PP.DTENTREGA,
                        PPM.DTREGISTROPALM,
                        PP.STPEDIDO,  
                        PP.TP_BLOQUEIO,                      
                        TP.DSTIPOPEDIDO,
                        FORMAPAGTO.DS_FORMAPAGTO,
                        CONDPAGTO.DS_CONDPAGTO,
                        PP.DSOBSERVACAO, 
                        PP.DSBLOQUEIO

                    ORDER BY PP.IDEMPRESA  
                ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);

        $key = "PedidoAll_2024" . Auth::user()->id;
        return Cache::remember($key, now()->addMinutes(15), function () use ($query) {
            return DB::connection('firebird')->select($query);
        });
    }
    public function ItemPedidoPneu($pedido)
    {
        $query = "
            SELECT PP.IDEMPRESA CD_EMPRESA, PP.ID PEDIDO, PPM.idpedidomovel, OPR.id NRORDEM, IPP.id, IPP.nrsequencia, 
            (PP.IDPESSOA||' - '||PC.NM_PESSOA) PESSOA, SP.dsservico,
            MAC.DSMARCA, MOP.DSMODELO, P.NRDOT, P.NRSERIE, DP.DSDESENHO, IPP.VLUNITARIO,
            IPP.ID IDITEMPEDPNEU, PP.IDVENDEDOR, PP.DTEMISSAO, 
            COALESCE(
                CASE
                WHEN OPR.STORDEM = 'A' THEN 'EM PRODUCAO'
                WHEN OPR.STORDEM = 'F' THEN 'FINALIZADA'
                END, 'SEM OP') STORDEM    
            FROM PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN PESSOA PC ON (PC.CD_PESSOA = PP.IDPESSOA)
            LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID AND OPR.STORDEM <> 'C')
            INNER JOIN PNEU P ON (P.ID = IPP.IDPNEU)
            INNER JOIN MODELOPNEU MOP ON (MOP.ID = P.IDMODELOPNEU)
            INNER JOIN MARCAPNEU MAC ON (MAC.ID = MOP.IDMARCAPNEU)
            INNER JOIN MEDIDAPNEU MD ON(MD.ID = P.IDMEDIDAPNEU)
            INNER JOIN DESENHOPNEU DP ON (DP.ID = IPP.IDDESENHOPNEU)
            INNER JOIN SERVICOPNEU SP ON(SP.ID = IPP.IDSERVICOPNEU)
            LEFT JOIN PEDIDOPNEUMOVEL PPM ON(PPM.ID = PP.ID)
            LEFT JOIN ITEMPEDIDOPNEUBORRACHEIRO IPPB ON(IPPB.IDITEMPEDIDOPNEU = IPP.ID)
            LEFT JOIN PESSOA PV ON (PV.CD_PESSOA = IPPB.IDBORRACHEIRO)
            WHERE PP.ID = $pedido /**informar o numero do pedido aqui */
            GROUP BY PP.IDEMPRESA, PP.ID, PPM.idpedidomovel, OPR.id, IPP.id, IPP.nrsequencia, PESSOA, SP.dsservico,
            MAC.DSMARCA, MOP.DSMODELO, P.NRDOT, P.NRSERIE, DP.DSDESENHO, IPP.VLUNITARIO,
            IPP.ID, PP.IDVENDEDOR, PP.DTEMISSAO, OPR.STORDEM
            ORDER BY PP.IDEMPRESA, IPP.ID";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getColetaEmpresa($data)
    {
        if (is_null($data)) {
            $cd_empresa = 0;
            $inicioData = 0;
            $fimData = 0;
        } else {
            $cd_empresa = $data['cd_empresa'];
            $inicioData = $data['dt_inicial'];
            $fimData = $data['dt_final'];
            $cd_regiaocomercial = $data['cd_regiaocomercial'];
        }
        $query = "
                    SELECT
                        PP.IDEMPRESA CD_EMPRESA,
                        PP.IDVENDEDOR,
                        PP.IDVENDEDOR || ' - ' || V.NM_PESSOA NM_VENDEDOR,
                        --PC.NM_PESSOA,
                        COUNT(DISTINCT
                        CASE
                        WHEN PP.STPEDIDO = 'B' THEN PP.ID
                        ELSE NULL
                        END) BLOQUEADAS,
                        COUNT(DISTINCT PP.ID) QTDPEDIDOS,
                        COUNT(IPP.ID) QTDPNEUS,
                        CAST(SUM(IPP.VLUNITARIO) AS DECIMAL(12,2)) VALOR,
                        CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO
                    FROM PEDIDOPNEU PP
                    INNER JOIN TIPOPEDIDOPNEU TP ON (TP.ID = PP.IDTIPOPEDIDO)
                    INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
                    
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    INNER JOIN PESSOA PC ON (PC.CD_PESSOA = PP.IDPESSOA)
                    INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)
                    LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PC.CD_PESSOA
                                                        AND EP.CD_ENDERECO = PP.IDENDERECO)
                    LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
                    LEFT JOIN PEDIDOPNEUMOVEL PPM ON (PPM.ID = PP.ID)
                    WHERE                         
                        --" . (($inicioData != 0) ? "PP.DTEMISSAO between '$inicioData' and '$fimData' " : "PP.DTEMISSAO BETWEEN CURRENT_DATE AND CURRENT_DATE") . "                                 
                        PP.DTEMISSAO BETWEEN '04.02.2025' AND '05.02.2025'
                        " . (($cd_regiaocomercial != 0) ? "AND RC.CD_REGIAOCOMERCIAL = $cd_regiaocomercial" : "") . "
                        --AND PP.IDVENDEDOR = 18061
                        AND PP.STPEDIDO <> 'C'
                        --AND ITEM.CD_GRUPO IN (102,105,140,132,130)                        
                        AND PP.IDEMPRESA = $cd_empresa
                    GROUP BY PP.IDEMPRESA,
                        PP.IDVENDEDOR,
                        V.NM_PESSOA
                        
                        --PC.NM_PESSOA";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getQtdColeta($data)
    {
        if (is_null($data)) {
            $cd_empresa = 0;
        } else {
            $cd_empresa = $data['cd_empresa'];
        }
        $query = "
                   SELECT
                        PP.IDEMPRESA,
                        --HOJE
                        COUNT(DISTINCT
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE THEN PP.ID
                        END) AS QTDPEDIDOS_HOJE,
                        COUNT(
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE THEN IPP.ID
                        END) AS QTDPNEUS_HOJE,

                        --ONTEM
                        COUNT(DISTINCT
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE - 1 THEN PP.ID
                        END) AS QTDPEDIDOS_ONTEM,
                        COUNT(
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE - 1 THEN IPP.ID
                        END) AS QTDPNEUS_ONTEM,

                        --ANTEONTEM
                        COUNT(DISTINCT
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE-2 THEN PP.ID
                        END) AS QTDPEDIDOS_ANTEONTEM,
                        COUNT(
                        CASE
                        WHEN PP.DTEMISSAO = CURRENT_DATE-2 THEN IPP.ID
                        END) AS QTDPNEUS_ANTEONTEM
                    FROM PEDIDOPNEU PP
                    INNER JOIN ITEMPEDIDOPNEU IPP ON IPP.IDPEDIDOPNEU = PP.ID
                    INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
                    WHERE 
                        PP.DTEMISSAO BETWEEN CURRENT_DATE - 2 AND CURRENT_DATE
                        --PP.DTEMISSAO BETWEEN '04.04.2025' AND '05.04.2025'
                        AND PP.STPEDIDO <> 'C'
                        --AND ITEM.CD_GRUPO IN (102,105,140,132,130)
                        AND PP.IDEMPRESA = $cd_empresa
                    GROUP BY PP.IDEMPRESA";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getListColetaRegiao($data)
    {
        if (is_null($data)) {
            $cd_empresa = 0;
            $inicioData = 0;
            $fimData = 0;
        } else {
            $cd_empresa = $data['cd_empresa'];
            $inicioData = $data['dt_inicial'];
            $fimData = $data['dt_final'];
        }
        $query = "
            SELECT
                PP.IDEMPRESA CD_EMPRESA,
                EP.CD_REGIAOCOMERCIAL,   
                RC.DS_REGIAOCOMERCIAL,             
                COUNT(DISTINCT
                CASE
                WHEN PP.STPEDIDO = 'B' THEN PP.ID
                ELSE NULL
                END) BLOQUEADAS,
                COUNT(DISTINCT PP.ID) QTDPEDIDOS,
                COUNT(IPP.ID) QTDPNEUS,
                CAST(SUM(IPP.VLUNITARIO) AS DECIMAL(12,2)) VALOR,
                CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO
            FROM PEDIDOPNEU PP
            INNER JOIN TIPOPEDIDOPNEU TP ON (TP.ID = PP.IDTIPOPEDIDO)
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN PESSOA PC ON (PC.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PC.CD_PESSOA
                                                        AND EP.CD_ENDERECO = PP.IDENDERECO)
            LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
            LEFT JOIN PEDIDOPNEUMOVEL PPM ON (PPM.ID = PP.ID)
            WHERE  
                --" . (($inicioData != 0) ? "PP.DTEMISSAO between '$inicioData' and '$fimData' " : "PP.DTEMISSAO BETWEEN CURRENT_DATE AND CURRENT_DATE") . "                         
                PP.DTEMISSAO BETWEEN '04.02.2025' AND '05.02.2025'
                --AND PP.IDVENDEDOR = 18061
                AND PP.STPEDIDO <> 'C'
                --AND ITEM.CD_GRUPO IN (102, 105, 140, 132, 130)
                AND PP.IDEMPRESA = $cd_empresa
            GROUP BY PP.IDEMPRESA,
                EP.CD_REGIAOCOMERCIAL,
                RC.DS_REGIAOCOMERCIAL
                ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
