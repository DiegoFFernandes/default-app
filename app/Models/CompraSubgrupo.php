<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraSubgrupo extends Model
{
    use HasFactory;

    public function getAll()
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT CD_SUBGRUPO, DS_SUBGRUPO, DT_REGISTRO
            FROM COMPRA_SUBGRUPO
            ORDER BY DS_SUBGRUPO
        "));
    }

    /** Busca para o Select2 (usado no modal de item). */
    public function search(string $term)
    {
        $termIso = \Helper::ToIso($term);

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT FIRST 20
                CD_SUBGRUPO AS id,
                DS_SUBGRUPO AS text
            FROM COMPRA_SUBGRUPO
            WHERE DS_SUBGRUPO CONTAINING :term
               OR CAST(CD_SUBGRUPO AS VARCHAR(20)) CONTAINING :term2
            ORDER BY DS_SUBGRUPO
        ", ['term' => $termIso, 'term2' => $term]));
    }

    public function findById(int $cdSubgrupo)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT CD_SUBGRUPO, DS_SUBGRUPO FROM COMPRA_SUBGRUPO WHERE CD_SUBGRUPO = :cd
        ", ['cd' => $cdSubgrupo]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_SUBGRUPO (CD_SUBGRUPO, DS_SUBGRUPO, DT_REGISTRO)
            VALUES (:id, :ds_subgrupo, CURRENT_TIMESTAMP)
        ", [
            'id'          => $id,
            'ds_subgrupo' => \Helper::ToIso(mb_strtoupper($data['ds_subgrupo'], 'UTF-8')),
        ]);

        return $id;
    }

    public function updateData(int $cdSubgrupo, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SUBGRUPO SET DS_SUBGRUPO = :ds_subgrupo WHERE CD_SUBGRUPO = :cd
        ", [
            'ds_subgrupo' => \Helper::ToIso(mb_strtoupper($data['ds_subgrupo'], 'UTF-8')),
            'cd'          => $cdSubgrupo,
        ]);
    }

    public function deleteById(int $cdSubgrupo)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_SUBGRUPO WHERE CD_SUBGRUPO = :cd',
            ['cd' => $cdSubgrupo]
        );
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_SUBGRUPO, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
