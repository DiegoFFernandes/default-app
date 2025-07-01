<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoteExpedicao extends Model
{
    use HasFactory;

    public function ListLoteExpedicao($lote = 0)
    {

        $query = "
            SELECT
                E.ID LOTE,
                E.IDEMPRESA,
                E.IDVENDEDOR,
                V.NM_PESSOA NM_VENDEDOR,
                E.DSOBSERVACAO,
                E.DTLOTE EMISSAO,
                CASE
                    WHEN E.STLOTE = 'A' THEN 'ABERTO'
                    WHEN E.STLOTE = 'F' THEN 'FINALIZADO'
                    ELSE E.STLOTE
                END SITUACAO,
                E.STLOTE,
                E.DTREGISTRO
            FROM EXPEDICAOLOTEPNEU E
            INNER JOIN PESSOA V ON (V.CD_PESSOA = E.IDVENDEDOR)
            WHERE
                E.DTREGISTRO >= '04.04.2025'
                AND E.IDEMPRESA = 1
                AND E.STLOTE NOT IN ('C')
                " . ($lote == 0 ? '' : " AND E.ID = $lote") . "
            ORDER BY E.DTREGISTRO DESC";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }

    public function retornaUltimoID($empresa)
    {
        $query = "
            SELECT FIRST 1
                E.ID
            FROM EXPEDICAOLOTEPNEU E
            WHERE
                E.IDEMPRESA = $empresa
            ORDER BY E.ID DESC";

        $data = DB::connection('firebird')->select($query);

        $id = Helper::ConvertFormatText($data)[0]->ID ?? null;

        //retorna o próximo ID baseado no último ID encontrado
        //se não houver ID, retorna 1
        return $id ? (int)$id + 1 : 1;
    }

    public function storeData($idValido, $input)
    {
        return DB::transaction(function () use ($idValido, $input) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                    INSERT INTO EXPEDICAOLOTEPNEU (ID, IDEMPRESA, IDVENDEDOR, DSOBSERVACAO, DTLOTE, STLOTE, DTREGISTRO) 
                    VALUES (
                        $idValido,
                        {$input['empresa']},
                        {$input['vendedor']},
                        '{$input['observacao']}',
                        '{$input['emissao']}',
                        'A',
                        CURRENT_TIMESTAMP
                    )";

            return DB::connection('firebird')->statement($query);
        });
    }    

    public function existsLoteExpedicao($idLote, $idEmpresa)
    {
        $exists = DB::connection('firebird')
            ->table('EXPEDICAOLOTEPNEU')
            ->where('ID', $idLote)
            ->where('IDEMPRESA', $idEmpresa)
            ->exists();

        return $exists;
    }
}
