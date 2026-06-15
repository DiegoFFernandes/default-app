<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraSolicitacaoItem extends Model
{
    use HasFactory;

    public function getBySolicitacao($idSolicitacao)
    {
        return DB::connection('firebird')->select("
            SELECT
                I.ID,
                I.ID_SOLICITACAO,
                I.CD_ITEM,
                IT.DS_ITEM,
                I.QT_ITEM,
                I.DS_UNIDADE,
                I.DS_OBSERVACAO
            FROM COMPRA_SOL_ITEM I
            INNER JOIN ITEM IT ON IT.CD_ITEM = I.CD_ITEM
            WHERE I.ID_SOLICITACAO = :id_sol
            ORDER BY I.ID
        ", ['id_sol' => $idSolicitacao]);
    }

    public function store($data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_SOL_ITEM (
                ID, ID_SOLICITACAO, CD_ITEM, QT_ITEM, DS_UNIDADE, DS_OBSERVACAO
            ) VALUES (
                :id, :id_solicitacao, :cd_item, :qt_item, :ds_unidade, :ds_observacao
            )
        ", [
            'id'             => $id,
            'id_solicitacao' => $data['id_solicitacao'],
            'cd_item'        => $data['cd_item'],
            'qt_item'        => $data['qt_item'],
            'ds_unidade'     => $data['ds_unidade'],
            'ds_observacao'  => $data['ds_observacao'] ?? null,
        ]);

        return $id;
    }

    public function deleteById($id)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_SOL_ITEM WHERE ID = :id',
            ['id' => $id]
        );
    }

    public function searchItem($term)
    {
        return DB::connection('firebird')->select("
            SELECT FIRST 20
                CD_ITEM AS id,
                CAST(CD_ITEM AS VARCHAR(20)) || ' - ' || DS_ITEM AS text
            FROM ITEM
            WHERE ST_ATIVO = 'S'
              AND (DS_ITEM CONTAINING :term OR CAST(CD_ITEM AS VARCHAR(20)) CONTAINING :term2)
            ORDER BY DS_ITEM
        ", ['term' => $term, 'term2' => $term]);
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_SOL_ITEM, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
