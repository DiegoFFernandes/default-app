<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Helper;

class PedidoPneu extends Model
{
    use HasFactory;
    protected $table = 'PEDIDOPNEU';

    public function verifyIfExists($pedido)
    {
        $query = "select first 1 pp.id from pedidopneu pp where pp.id = $pedido";
        $data = DB::connection('firebird')->select($query);

        return empty($data) ? 0 : 1;
    }
    public function updateData($data, $stpedido, $tpbloqueio)
    {
        return DB::transaction(function () use ($data, $stpedido, $tpbloqueio) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "update pedidopneu pp
            set pp.dsliberacao = '$data->DSLIBERACAO'
            " . (($stpedido == "N") ? ", pp.stpedido = 'N' " : "") . " 
            " . (($tpbloqueio == "F") ? ", pp.tp_bloqueio = 'F' " : "") . " 
            " . (($tpbloqueio == "C") ? ", pp.tp_bloqueio = 'C' " : "") . " 
            where pp.id = $data->PEDIDO";

            return DB::connection('firebird')->statement($query);
        });
    }

    static function updateDesconto($pedido, $tp_cargo)
    {
        if (empty($pedido)) {
            return;
        }

        return DB::transaction(function () use ($pedido, $tp_cargo) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                UPDATE PEDIDOPNEU PP
                    SET PP.ST_COMERCIAL = '$tp_cargo'
                WHERE PP.ID in ($pedido)";

            return DB::connection('firebird')->statement($query);
        });
    }

    static function getPedidoPneu($dt_inicio = null, $dt_fim = null, $cd_empresa = null)
    {
        $query = "
            SELECT
                MP.DSMEDIDAPNEU,
                COUNT(*) QTD,
                CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO
            FROM PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = PP.ID)
            INNER JOIN PNEU ON (PNEU.ID = IPP.IDPNEU)
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
            INNER JOIN MEDIDAPNEU MP ON (MP.ID = PNEU.IDMEDIDAPNEU)
            WHERE
                PP.DTEMISSAO BETWEEN '$dt_inicio' AND '$dt_fim'
                AND PP.IDEMPRESA = $cd_empresa
            GROUP BY MP.DSMEDIDAPNEU";
        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }

    public function createPedidoPneu($pessoa, $input)
    {
        // return $input['cd_empresa'];
        return DB::transaction(function () use ($pessoa, $input) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                INSERT INTO 
                PEDIDOPNEU (
                    ID,                    
                    DTEMISSAO,
                    DTENTREGA,
                    IDVENDEDOR,
                    STPEDIDO,
                    IDCONDPAGTO,
                    CDFORMAPAGTO,
                    DSOBSERVACAO,
                    DTREGISTRO,
                    IDPESSOA,
                    IDEMPRESA,
                    DSBLOQUEIO,
                    DTBLOQUEIO,
                    IDTIPOPEDIDO,
                    STGERAPEDIDO,
                    IDENDERECO,
                    DSOBSFATURAMENTO,
                    STFATURAAUTO,
                    IDEMPRESAFATURAMENTO,
                    HREMISSAO,
                    TP_BLOQUEIO)
                VALUES ( 
                        NEXT VALUE FOR SEQ_IDPEDIDOPNEU,                     
                        CURRENT_DATE,                       
                        CURRENT_DATE+1,                        
                        :idvendedor,              
                        'B',                                   
                        :cond_pagto,                 
                        :form_pagto,                 
                        NULL,                               
                        CURRENT_TIMESTAMP,                  
                        :idpessoa,                 
                        :cd_empresa, 
                        'PEDIDO GERADO PELO PORTAL',
                        CURRENT_DATE,   
                        1,                                  
                        'S',                                
                        1,                                  
                        NULL,                               
                        'S',                                
                        :cd_empresa_fat,                 
                        CAST(CURRENT_TIME AS TIME),             
                        'A') RETURNING ID
            ";


            $result = DB::connection('firebird')->select($query, [
                'idvendedor'    => (int) $pessoa->CD_VENDEDOR,
                'idpessoa'      => (int) $pessoa->CD_PESSOA,
                'cond_pagto'    => (int) $input['cond_pagto'],
                'form_pagto'    => (string) $input['form_pagto'],
                'cd_empresa'    => (int) $input['cd_empresa'],
                'cd_empresa_fat' => (int) $input['cd_empresa'],
            ]);

            if (empty($result)) {
                throw new \Exception('Falha ao gerar o Código do pedido.');
            }
            return $result[0]->ID;
        });
    }

    public function createItemPedidoPneu($idPedido, $idPneu, $pessoa, $servicoPneu, $valor, $seqItemPedidoPneu)
    {

        return DB::transaction(function () use ($idPedido, $idPneu, $pessoa, $servicoPneu, $valor, $seqItemPedidoPneu) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                INSERT INTO ITEMPEDIDOPNEU (
                    ID,
                    IDPEDIDOPNEU,
                    IDPNEU,
                    IDSERVICOPNEU,
                    IDDESENHOPNEU,
                    DTREGISTRO,
                    VLUNITARIO,
                    NRSEQUENCIA,
                    STGARANTIA,
                    STURGENTE,
                    STPEDIDOENT,
                    STCOLETATESTE,
                    IDTABPRECO,
                    STCANCELADO,
                    NRSEQCRIACAO,
                    STITEMPEDIDOPNEU,
                    STPNEUDACASA,
                    IDCARCACAPEDIDO,
                    STJAFATURADOAUTO,
                    STFATURAAUTOSAIDA,
                    STUTILIZAESTUFA,
                    STPNEUPARREPROVADO)
                VALUES ( 
                        NEXT VALUE FOR SEQ_IDITEMPEDIDOPNEU,
                        :idpedidopneu,
                        :idpneu,
                        :idservicopneu,
                        :iddesenhopneu,
                        CURRENT_TIMESTAMP,
                        :vlunitario,
                        :nrsequencia,
                        'N',
                        'N',
                        'S',
                        'N',
                        :idtabpreco,
                        'N',
                        :nrseqcriacao,
                        'N',
                        'N',
                        :idcarcacapedido,
                        'N',
                        'N',
                        'N',
                        'N') RETURNING ID
                 ";

            $result = DB::connection('firebird')->select($query, [
                'idpedidopneu'     => (int) $idPedido,
                'idpneu'           => (int) $idPneu,
                'idservicopneu'    => (int) $servicoPneu->ID,
                'iddesenhopneu'    => (int) $servicoPneu->IDDESENHOPNEU,
                'vlunitario'       => (float) $valor,
                'nrsequencia'      => (int) $seqItemPedidoPneu,
                'idtabpreco'       => (int) $pessoa->CD_TABPRECO,
                'nrseqcriacao'     => (int) $seqItemPedidoPneu,
                'idcarcacapedido'  => (int) $servicoPneu->IDITEMCARCACA,

            ]);

            if (empty($result)) {
                throw new \Exception('Falha ao gerar o Código do item do pedido.');
            }

            return $result[0]->ID;
        });
    }

    public function createItemPedidoPneuBorracheiro($iditemPedidoPneu, $pessoa, $calculaComissao)
    {
        return DB::transaction(function () use ($iditemPedidoPneu, $pessoa, $calculaComissao) {
            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                INSERT INTO ITEMPEDIDOPNEUBORRACHEIRO (
                    ID,
                    IDITEMPEDIDOPNEU,
                    IDBORRACHEIRO,
                    PC_COMISSAO,
                    VL_COMISSAO,
                    DT_REGISTRO,
                    CD_TIPO)
                VALUES (
                    NEXT VALUE FOR SEQ_IDITEMPEDIDOPNEUBORRACHEIRO,
                    :iditempedidopneu,
                    :idborracheiro,
                    :pc_comissao,
                    :vl_comissao,
                    CURRENT_TIMESTAMP,
                    1
                );
            ";

            $result = DB::connection('firebird')->insert($query, [
                'iditempedidopneu'  => (int) $iditemPedidoPneu,
                'idborracheiro'        => (int) $pessoa->CD_VENDEDOR,
                'pc_comissao' => (float) $calculaComissao->PC_COMISSAO,
                'vl_comissao'    => (float) $calculaComissao->VL_COMISSAO,
            ]);

            if (empty($result)) {
                throw new \Exception('Falha ao gerar o Código do item do pedido do borracheiro.');
            }

            return $result;
        });
    }

    public function searchPedidoPneu($data)
    {
        $where = [];
        $params = [];

        if (!empty($data['pedido'])) {
            $where[] = 'PP.ID = :idPedido';
            $params['idPedido'] = $data['pedido'];
        }

        if (!empty($data['ordem'])) {
            $where[] = 'OPR.ID = :nrOrdem';
            $params['nrOrdem'] = $data['ordem'];
        }

        $query = "
            SELECT
                PP.IDEMPRESA CD_EMPRESA,
                PP.ID ID_PEDIDO,
                PP.IDPESSOA || '-' || PESSOA.NM_PESSOA NM_PESSOA,
                IPP.IDSERVICOPNEU,
                ITEM.DS_ITEM,
                OPR.id NR_ORDEM,
                --DADOS DOS PNEUS
                PNEU.ID ID_PNEU,
                PNEU.NRSERIE,
                PNEU.NRFOGO,
                PNEU.NRDOT
            FROM PEDIDOPNEU PP
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
            INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
            INNER JOIN PNEU ON (PNEU.ID = IPP.IDPNEU)
            LEFT JOIN ORDEMPRODUCAORECAP OPR ON (OPR.IDITEMPEDIDOPNEU = IPP.ID)            
        ";

        $query .= ' WHERE ' . implode(' AND ', $where);

        $data = DB::connection('firebird')->select(
            $query,
            $params
        );

        return Helper::ConvertFormatText($data);
    }
}
