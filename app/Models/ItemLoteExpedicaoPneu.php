<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemLoteExpedicaoPneu extends Model
{
    use HasFactory;

    public function retornaUltimoID()
    {
        $query = "
            SELECT FIRST 1
                E.ID
            FROM ORDEMPRODEXPEDICAOLOTEPNEU E           
            ORDER BY E.ID DESC";

        $data = DB::connection('firebird')->select($query);

        $id = Helper::ConvertFormatText($data)[0]->ID ?? null;
        //retorna o próximo ID baseado no último ID encontrado
        //se não houver ID, retorna 1
        return $id ? (int)$id + 1 : 1;
    }
    public function StoreData($input)
    {
        $idValido = $this->retornaUltimoID();

        return DB::transaction(function () use ($idValido, $input) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                    INSERT INTO ORDEMPRODEXPEDICAOLOTEPNEU (
                        ID,
                        IDORDEMPRODUCAORECAP,
                        IDEXPEDICAOLOTEPNEU,
                        IDEMPRESAEXPEDICAOLOTE,
                        DTREGISTRO,
                        STVALIDADO                
                    ) VALUES (
                        $idValido,
                        {$input['nr_ordem']},
                        {$input['lote']},
                        '{$input['idempresa']}',
                        CURRENT_TIMESTAMP,
                        'A'
                    );";

            $insert = DB::connection('firebird')->statement($query);

            if ($insert) {
                $this->alterSequencia($idValido + 1);
            }
            return $insert;
        });
    }
    public function alterSequencia($idValido)
    {

        return DB::transaction(function () use ($idValido) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE ALTER_SEQUENCE('GEN_ORDEMPRODEXPEDLOTEPNEU_ID', $idValido, 'U')");
        });
    }
    public function existsItemLoteExpedicao($idLote, $idEmpresa, $nrOrdem)
    {
        $exists = DB::connection('firebird')
            ->table('ORDEMPRODEXPEDICAOLOTEPNEU')
            ->where('IDEXPEDICAOLOTEPNEU', $idLote)
            ->where('IDEMPRESAEXPEDICAOLOTE', $idEmpresa)
            ->where('IDORDEMPRODUCAORECAP', $nrOrdem)
            ->exists();

        return $exists;
    }
    public function getListItemLoteExpedicao($lote, $idempresa)
    {
        $query = "
            SELECT
                O.ID,
                SP.ID || '-' || SP.DSSERVICO DSSERVICO,
                O.IDORDEMPRODUCAORECAP NRORDEM,
                O.IDEXPEDICAOLOTEPNEU,
                O.IDEMPRESAEXPEDICAOLOTE,
                O.DTREGISTRO,
                O.STVALIDADO,
                EXPEDICAOLOTEPNEU.STLOTE
            FROM ORDEMPRODEXPEDICAOLOTEPNEU O
            INNER JOIN EXPEDICAOLOTEPNEU ON (EXPEDICAOLOTEPNEU.IDEMPRESA = O.IDEMPRESAEXPEDICAOLOTE)
                AND (EXPEDICAOLOTEPNEU.ID = O.IDEXPEDICAOLOTEPNEU)
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = O.IDORDEMPRODUCAORECAP)
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
            INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
            WHERE
                IDEXPEDICAOLOTEPNEU = $lote
                AND IDEMPRESAEXPEDICAOLOTE = $idempresa";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function deleteItemLoteExpedicao($input)
    {
        return DB::transaction(function () use ($input) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");
            $query = "
                DELETE FROM ORDEMPRODEXPEDICAOLOTEPNEU
                WHERE ID = {$input['id']}
                AND IDORDEMPRODUCAORECAP = {$input['nr_ordem']}
                AND IDEXPEDICAOLOTEPNEU = {$input['lote']}
                AND IDEMPRESAEXPEDICAOLOTE = {$input['idempresa']}";

            return DB::connection('firebird')->statement($query);
        });
    }
}
