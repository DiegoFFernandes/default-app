<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraItem extends Model
{
    use HasFactory;

    public function getAll()
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                I.CD_ITEM,
                I.DS_ITEM,
                I.SG_UNIDMED,
                I.CD_SUBGRUPO_COMPRA,
                SG.DS_SUBGRUPO,
                I.ST_ATIVO,
                I.DT_REGISTRO
            FROM COMPRA_ITEM I
            LEFT JOIN COMPRA_SUBGRUPO SG ON SG.CD_SUBGRUPO = I.CD_SUBGRUPO_COMPRA
            ORDER BY I.DS_ITEM
        "));
    }

    /** Busca para o Select2 (mesmo formato do searchItem do Junsoft). */
    public function search(string $term)
    {
        // DS_ITEM é gravado em ISO-8859-1; converte o termo para casar acentos.
        $termIso = \Helper::ToIso($term);

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT FIRST 20
                CD_ITEM AS id,
                CAST(CD_ITEM AS VARCHAR(20)) || ' - ' || DS_ITEM AS text,
                SG_UNIDMED AS SG_UNIDMED
            FROM COMPRA_ITEM
            WHERE ST_ATIVO = 'S'
              AND (DS_ITEM CONTAINING :term OR CAST(CD_ITEM AS VARCHAR(20)) CONTAINING :term2)
            ORDER BY DS_ITEM
        ", ['term' => $termIso, 'term2' => $term]));
    }

    public function findById(int $cdItem)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT CD_ITEM, DS_ITEM, SG_UNIDMED, ST_ATIVO
            FROM COMPRA_ITEM WHERE CD_ITEM = :cd
        ", ['cd' => $cdItem]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    /** Descrição do item próprio, para denormalizar em COMPRA_SOL_ITEM. */
    public function getDescricao(int $cdItem): ?string
    {
        $row = DB::connection('firebird')->selectOne(
            "SELECT DS_ITEM FROM COMPRA_ITEM WHERE CD_ITEM = :cd",
            ['cd' => $cdItem]
        );

        return $row ? \Helper::ConvertFormatText([$row])[0]->DS_ITEM : null;
    }

    /** Verifica se já existe item com a mesma descrição (ignora o próprio no update). */
    public function existsByDescricao(string $dsItem, ?int $exceptCd = null): bool
    {
        // DS_ITEM é gravado em MAIÚSCULO + ISO; compara na mesma forma.
        $dsIso = \Helper::ToIso(mb_strtoupper(trim($dsItem), 'UTF-8'));

        $sql    = "SELECT 1 FROM COMPRA_ITEM WHERE DS_ITEM = :ds";
        $params = ['ds' => $dsIso];

        if ($exceptCd !== null) {
            $sql .= " AND CD_ITEM <> :cd";
            $params['cd'] = $exceptCd;
        }

        return (bool) DB::connection('firebird')->selectOne($sql, $params);
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_ITEM (CD_ITEM, DS_ITEM, SG_UNIDMED, CD_SUBGRUPO_COMPRA, ST_ATIVO, CD_USUARIO, DT_REGISTRO)
            VALUES (:id, :ds_item, :sg_unidmed, :cd_subgrupo, :st_ativo, :cd_usuario, CURRENT_TIMESTAMP)
        ", [
            'id'          => $id,
            'ds_item'     => \Helper::ToIso(mb_strtoupper($data['ds_item'], 'UTF-8')),
            'sg_unidmed'  => \Helper::ToIso($this->upper($data['sg_unidmed'] ?? null)),
            'cd_subgrupo' => $data['cd_subgrupo_compra'] ?? null,
            'st_ativo'    => $data['st_ativo'] ?? 'S',
            'cd_usuario'  => $data['cd_usuario'] ?? null,
        ]);

        return $id;
    }

    public function updateData(int $cdItem, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_ITEM SET
                DS_ITEM            = :ds_item,
                SG_UNIDMED         = :sg_unidmed,
                CD_SUBGRUPO_COMPRA = :cd_subgrupo,
                ST_ATIVO           = :st_ativo
            WHERE CD_ITEM = :cd
        ", [
            'ds_item'     => \Helper::ToIso(mb_strtoupper($data['ds_item'], 'UTF-8')),
            'sg_unidmed'  => \Helper::ToIso($this->upper($data['sg_unidmed'] ?? null)),
            'cd_subgrupo' => $data['cd_subgrupo_compra'] ?? null,
            'st_ativo'    => $data['st_ativo'] ?? 'S',
            'cd'          => $cdItem,
        ]);
    }

    public function deleteById(int $cdItem)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_ITEM WHERE CD_ITEM = :cd',
            ['cd' => $cdItem]
        );
    }

    /** Uppercase com suporte a acentos; devolve null se vazio. */
    private function upper(?string $value): ?string
    {
        return ($value === null || $value === '') ? null : mb_strtoupper($value, 'UTF-8');
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_ITEM, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
