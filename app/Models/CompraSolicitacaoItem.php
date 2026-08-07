<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraSolicitacaoItem extends Model
{
    use HasFactory;

    public function getBySolicitacao(int $idSolicitacao)
    {
        // DS_ITEM é denormalizado na própria linha: o item pode vir do Junsoft
        // (ITEM) ou do catálogo próprio (COMPRA_ITEM), sem JOIN a uma tabela fixa.
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                I.ID,
                I.ID_SOLICITACAO,
                I.CD_ITEM,
                I.DS_ITEM,
                I.QT_ITEM,
                I.DS_UNIDADE,
                I.DS_OBSERVACAO
            FROM COMPRA_SOL_ITEM I
            WHERE I.ID_SOLICITACAO = :id_sol
            ORDER BY I.ID
        ", ['id_sol' => $idSolicitacao]));
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_SOL_ITEM (
                ID, ID_SOLICITACAO, CD_ITEM, DS_ITEM, QT_ITEM, DS_UNIDADE, DS_OBSERVACAO
            ) VALUES (
                :id, :id_solicitacao, :cd_item, :ds_item, :qt_item, :ds_unidade, :ds_observacao
            )
        ", [
            'id'             => $id,
            'id_solicitacao' => $data['id_solicitacao'],
            'cd_item'        => $data['cd_item'],
            'ds_item'        => \Helper::ToIso($data['ds_item'] ?? null),
            'qt_item'        => $data['qt_item'],
            'ds_unidade'     => \Helper::ToIso($data['ds_unidade']),
            'ds_observacao'  => \Helper::ToIso($data['ds_observacao'] ?? null),
        ]);

        return $id;
    }

    public function updateData(int $id, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOL_ITEM SET
                CD_ITEM       = :cd_item,
                DS_ITEM       = :ds_item,
                QT_ITEM       = :qt_item,
                DS_UNIDADE    = :ds_unidade,
                DS_OBSERVACAO = :ds_observacao
            WHERE ID = :id
        ", [
            'id'           => $id,
            'cd_item'      => $data['cd_item'],
            'ds_item'      => \Helper::ToIso($data['ds_item'] ?? null),
            'qt_item'      => $data['qt_item'],
            'ds_unidade'   => \Helper::ToIso($data['ds_unidade']),
            'ds_observacao'=> \Helper::ToIso($data['ds_observacao'] ?? null),
        ]);
    }

    public function deleteById(int $id)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_SOL_ITEM WHERE ID = :id',
            ['id' => $id]
        );
    }

    /** Descrição de um item do Junsoft (tabela ITEM), para denormalizar. */
    public function descricaoJunsoft(int $cdItem): ?string
    {
        $row = DB::connection('firebird')->selectOne(
            "SELECT DS_ITEM FROM ITEM WHERE CD_ITEM = :cd",
            ['cd' => $cdItem]
        );

        return $row ? \Helper::ConvertFormatText([$row])[0]->DS_ITEM : null;
    }

    public function searchItem(string $term)
    {
        return DB::connection('firebird')->select("
            SELECT FIRST 20
                CD_ITEM AS id,
                CAST(CD_ITEM AS VARCHAR(20)) || ' - ' || DS_ITEM AS text,
                SG_UNIDMED AS SG_UNIDMED 
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
