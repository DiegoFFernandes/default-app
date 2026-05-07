<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pneu extends Model
{
    use HasFactory;


    public function createPneu($input, $pessoa)
    {
        return DB::transaction(function () use ($pessoa, $input) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                INSERT INTO PNEU (
                    ID,
                    IDMODELOPNEU,
                    IDMEDIDAPNEU,
                    NRSERIE,
                    NRFOGO,
                    NRDOT,
                    DTREGISTRO,
                    IDPESSOA,
                    DTANOPNEU,
                    STMONTAGEM)
                VALUES (
                    NEXT VALUE FOR SEQ_IDPNEU,
                    :idModeloPneu,
                    :idMedidaPneu,
                    :nrSerie,
                    :nrFogo,
                    :nrDot,
                    CURRENT_TIMESTAMP,
                    :idPessoa,
                    :dtAnoPneu,
                    'N')
                RETURNING ID  
           ";

            $result = DB::connection('firebird')->select($query, [
                'idModeloPneu' => (int) $input->IDMODELOPNEU,
                'idMedidaPneu' => (int) $input->IDMEDIDAPNEU,
                'nrSerie'      => (string) $input->NR_SERIE,
                'nrFogo'       => (string) $input->NR_FOGO,
                'nrDot'        => (int) $input->NR_DOT,
                'idPessoa'     => (int) $pessoa->CD_PESSOA,
                'dtAnoPneu'    => (int) $input->NR_DOT
            ]);

            if (empty($result)) {
                throw new \Exception('Falha ao gerar o Código do pedido.');
            }
            return $result[0]->ID;
        });
    }

    public function updatePneu(array $data)
    {
        return DB::transaction(function () use ($data) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                    UPDATE PNEU
                    SET
                        NRSERIE = :serie,
                        NRFOGO = :fogo,
                        NRDOT = :dot
                    WHERE (ID = :idPneu);
                ";

            DB::connection('firebird')->statement($query, [
                'serie' => $data['nrSerie'],
                'fogo' => $data['fogo'],
                'dot' => $data['dot'],
                'idPneu' => $data['idPneu']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pneu atualizado com sucesso.'
            ], 200);
        });
    }
}
