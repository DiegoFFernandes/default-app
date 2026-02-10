<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Estoque extends Model
{
    use HasFactory;

    public function getEstoqueNegativo()
    {
        $query = "
            SELECT
                1 CD_EMPRESA,
                ITEM.CD_ITEM,
                ITEM.DS_ITEM,
                CAST(R.O_QT_SALDO AS NUMERIC(18)) QTD_SALDO,
                R.O_VL_CUSTO
            FROM ITEM
            INNER JOIN RETORNA_SALDOESTOQUE(1, ITEM.CD_ITEM, 1, 1, CURRENT_DATE, NULL) R ON (1 = 1)
            WHERE ITEM.CD_GRUPO = 113
                AND ITEM.ST_ATIVO = 'S'
                AND R.O_QT_SALDO < 0

            UNION ALL

            SELECT
                3 CD_EMPRESA,
                ITEM.CD_ITEM,
                ITEM.DS_ITEM,
                CAST(R.O_QT_SALDO AS NUMERIC(18)) QTD_SALDO,
                R.O_VL_CUSTO
            FROM ITEM
            INNER JOIN RETORNA_SALDOESTOQUE(3, ITEM.CD_ITEM, 1, 1, CURRENT_DATE, NULL) R ON (1 = 1)
            WHERE ITEM.CD_GRUPO = 113
                AND ITEM.ST_ATIVO = 'S'
                AND R.O_QT_SALDO < 0

            UNION ALL

            SELECT
                5 CD_EMPRESA,
                ITEM.CD_ITEM,
                ITEM.DS_ITEM,
                CAST(R.O_QT_SALDO AS NUMERIC(18)) QTD_SALDO,
                R.O_VL_CUSTO
            FROM ITEM
            INNER JOIN RETORNA_SALDOESTOQUE(5, ITEM.CD_ITEM, 1, 1, CURRENT_DATE, NULL) R ON (1 = 1)
            WHERE ITEM.CD_GRUPO = 113
                AND ITEM.ST_ATIVO = 'S'
                AND R.O_QT_SALDO < 0

            UNION ALL

            SELECT
                6 CD_EMPRESA,
                ITEM.CD_ITEM,
                ITEM.DS_ITEM,
                CAST(R.O_QT_SALDO AS NUMERIC(18)) QTD_SALDO,
                R.O_VL_CUSTO
            FROM ITEM
            INNER JOIN RETORNA_SALDOESTOQUE(6, ITEM.CD_ITEM, 1, 1, CURRENT_DATE, NULL) R ON (1 = 1)
            WHERE ITEM.CD_GRUPO = 113
                AND ITEM.ST_ATIVO = 'S'
                AND R.O_QT_SALDO < 0 
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getCarcacasDaCasa($idPneuCarcaca = null, $stCarcaca = 'A')
    {
        $query = "
                SELECT
                    PC.ID,
                    PC.IDMEDIDAPNEU,
                    MP.DSMEDIDAPNEU,
                    PC.IDMODELOPNEU,
                    MARCA.DSMARCA,
                    MODELO.DSMODELO || ' - ' || MARCA.DSMARCA DSMODELO,
                    MODELO.DSMODELO DSMODELO1,
                    PC.NR_FOGO,
                    PC.NR_SERIE,
                    PC.NR_DOT,
                    PC.CD_TIPO,
                    PC.VL_CARCACA,
                    CASE PC.CD_TIPO
                    WHEN 1 THEN 'PRIMEIRA'
                    WHEN 2 THEN 'SEGUNDA'
                    WHEN 3 THEN 'TERCEIRA'
                    END DS_TIPO,
                    PC.CD_LOCAL,
                    CASE PC.CD_LOCAL
                    WHEN 1 THEN 'CAMBE'
                    WHEN 3 THEN 'OSVALDO'
                    WHEN 5 THEN 'PONTA GROSSA'
                    WHEN 6 THEN 'CATANDUVA'
                    END LOCAL_ESTOQUE,
                    PC.DT_REGISTRO,
                    PC.ST_CARCACA,
                    PP.ID PEDIDO,
                    CASE PC.ST_BAIXA
                        WHEN 'A' THEN 'AUTOMATICA'
                        WHEN 'M' THEN 'MANUAL'
                    END ST_BAIXA,                    
                    CASE PP.IDEMPRESA
                        WHEN 1 THEN 'CAMBE'
                        WHEN 3 THEN 'OSVALDO'
                        WHEN 5 THEN 'PONTA GROSSA'
                        WHEN 6 THEN 'CATANDUVA'
                    END EMPRESA_BAIXA,
                    PC.DT_ATUALIZACAO DT_BAIXA
                FROM PNEUCARCACA PC
                INNER JOIN MEDIDAPNEU MP ON (MP.ID = PC.IDMEDIDAPNEU)
                INNER JOIN MODELOPNEU MODELO ON (MODELO.ID = PC.IDMODELOPNEU)
                INNER JOIN MARCAPNEU MARCA ON (MARCA.ID = MODELO.IDMARCAPNEU)
                LEFT JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = PC.IDITEMPEDIDOPNEU)
                LEFT JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
                WHERE
                      PC.ST_CARCACA = :stCarcaca 
                    " . (!empty($idPneuCarcaca) ? " AND PC.ID = $idPneuCarcaca " : "AND 1=1") . "                    
        ";

        $data = DB::connection('firebird')->select($query, ['stCarcaca' => $stCarcaca]);

        return Helper::ConvertFormatText($data);
    }

    public function storeCarcaca($data)
    {
        try {
            $query = "
            INSERT INTO PNEUCARCACA (
                IDMEDIDAPNEU,
                IDMODELOPNEU,
                NR_DOT,
                VL_CARCACA,
                NR_FOGO,
                NR_SERIE,
                CD_TIPO,
                CD_LOCAL,
                DT_REGISTRO,
                DT_ATUALIZACAO,
                ST_CARCACA
            ) VALUES (
                :idmedidapneu,
                :idmodelopneu,
                :nr_dot,
                :vl_carcaca,
                :nr_fogo,
                :nr_serie,
                :cd_tipo,
                :cd_local,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP,
                'A'
            )
        ";

            DB::connection('firebird')->insert($query, [
                'idmedidapneu' => $data['medida'],
                'idmodelopneu' => $data['modelo'],
                'nr_fogo'      => $data['fogo'],
                'nr_serie'     => $data['serie'],
                'nr_dot'       => $data['dot'],
                'vl_carcaca'   => $data['valor'],
                'cd_tipo'      => $data['tipo'],
                'cd_local'     => $data['local'],
            ]);

            return ['success' => true, 'message' => 'Carcaça registrada com sucesso.'];
        } catch (\Exception $e) {

            return ['error' => true, 'message' => 'Erro ao registrar carcaça: ' . $e->getMessage()];
        }
    }

    public function editCarcaca($data)
    {
        try {
            $query = "
            UPDATE PNEUCARCACA
            SET
                IDMEDIDAPNEU = :idmedidapneu,
                IDMODELOPNEU = :idmodelopneu,
                NR_DOT = :nr_dot,
                VL_CARCACA = :vl_carcaca,
                NR_FOGO = :nr_fogo,
                NR_SERIE = :nr_serie,                
                CD_TIPO = :cd_tipo,
                CD_LOCAL = :cd_local,
                DT_ATUALIZACAO = CURRENT_TIMESTAMP
            WHERE ID = :id
        ";

            DB::connection('firebird')->update($query, [
                'idmedidapneu' => $data['medida'],
                'idmodelopneu' => $data['modelo'],
                'nr_dot'       => $data['dot'],
                'vl_carcaca'   => $data['valor'],
                'nr_fogo'      => $data['fogo'],
                'nr_serie'     => $data['serie'],
                'cd_tipo'      => $data['tipo'],
                'cd_local'     => $data['local'],
                'id'           => $data['id'],
            ]);

            return ['success' => true, 'message' => 'Carcaça atualizada com sucesso.'];
        } catch (\Exception $e) {

            return ['error' => true, 'message' => 'Erro ao atualizar carcaça: ' . $e->getMessage()];
        }
    }

    public function deleteCarcaca($ids, $status)
    {
        try {
            // Cria placeholders :id0, :id1 ...
            $placeholders = implode(',', array_map(fn($i) => ":id$i", array_keys($ids)));

            $query = "
            UPDATE PNEUCARCACA
                SET ST_CARCACA = :status, DT_ATUALIZACAO = CURRENT_TIMESTAMP, ST_BAIXA = 'M'
            WHERE ID IN ($placeholders)
            ";

            // Monta o array de parâmetros
            $params = ['status' => $status];

            foreach ($ids as $i => $value) {
                $params["id$i"] = $value;
            }

            DB::connection('firebird')->update($query, $params);

            return ['success' => true, 'message' => 'Carcaça' . ($status === 'B' ? ' baixada' : ' deletada') . ' com sucesso.'];
        } catch (\Exception $e) {

            return ['error' => true, 'message' => 'Erro ao ' . ($status === 'B' ? 'baixar' : 'deletar') . ' carcaça: ' . $e->getMessage()];
        }
    }

    public function verifyCarcacaExists($id)
    {
        $query = "
            SELECT COUNT(*) AS QTD
            FROM PNEUCARCACA
            WHERE ID = :id
                AND ST_CARCACA = 'A'
        ";

        $result = DB::connection('firebird')->select($query, [
            'id' => $id,
        ]);

        return $result[0]->QTD > 0;
    }

    public function transferCarcaca($ids, $local)
    {
        try {
            // Cria placeholders :id0, :id1 ...
            $placeholders = implode(',', array_map(fn($i) => ":id$i", array_keys($ids)));

            $query = "
            UPDATE PNEUCARCACA
            SET CD_LOCAL = :local
            WHERE ID IN ($placeholders)
            ";

            // Monta o array de parâmetros
            $params = ['local' => $local];
            foreach ($ids as $i => $value) {
                $params["id$i"] = $value;
            }

            DB::connection('firebird')->update($query, $params);

            return ['success' => true, 'message' => 'Carcaça(s) transferida(s) com sucesso.'];
        } catch (\Exception $e) {

            return ['error' => true, 'message' => 'Erro ao transferir carcaça(s): ' . $e->getMessage()];
        }
    }

    public function updateStatusPneuCarcaca($idPneuCarcaca, $iditemPedidoPneu)
    {
        $query = "
           UPDATE PNEUCARCACA SET ST_CARCACA = 'B', IDITEMPEDIDOPNEU = :idItemPedidoPneu, ST_BAIXA = 'A'
            WHERE ID = :idPneuCarcaca
        ";

        DB::connection('firebird')->update($query, [            
            'idPneuCarcaca' => $idPneuCarcaca,
            'idItemPedidoPneu' => $iditemPedidoPneu
        ]);
    }
}
