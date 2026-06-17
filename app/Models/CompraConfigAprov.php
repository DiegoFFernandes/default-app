<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraConfigAprov extends Model
{
    use HasFactory;

    public function getByFaixa(int $idFaixa)
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                A.ID_CONFIG_APROV,
                A.ID_FAIXA,
                A.NR_ORDEM,
                A.DS_CARGO,
                A.CD_USUARIO,
                A.NM_APROVADOR
            FROM COMPRA_CONFIG_APROV A
            WHERE A.ID_FAIXA = :id_faixa
            ORDER BY A.NR_ORDEM
        ", ['id_faixa' => $idFaixa]));
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_CONFIG_APROV (
                ID_CONFIG_APROV, ID_FAIXA, NR_ORDEM, DS_CARGO, CD_USUARIO, NM_APROVADOR
            ) VALUES (
                :id, :id_faixa, :nr_ordem, :ds_cargo, :cd_usuario, :nm_aprovador
            )
        ", [
            'id'           => $id,
            'id_faixa'     => $data['id_faixa'],
            'nr_ordem'     => $data['nr_ordem'],
            'ds_cargo'     => \Helper::ToIso($data['ds_cargo']),
            'cd_usuario'   => $data['cd_usuario'],
            'nm_aprovador' => \Helper::ToIso($data['nm_aprovador']),
        ]);

        return $id;
    }

    public function deleteById(int $id)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_CONFIG_APROV WHERE ID_CONFIG_APROV = :id',
            ['id' => $id]
        );
    }

    public function reordenar(array $ids): void
    {
        $offset = 10000;
        foreach ($ids as $index => $id) {
            DB::connection('firebird')->statement(
                'UPDATE COMPRA_CONFIG_APROV SET NR_ORDEM = :nr_ordem WHERE ID_CONFIG_APROV = :id',
                ['nr_ordem' => $offset + $index + 1, 'id' => $id]
            );
        }
        foreach ($ids as $index => $id) {
            DB::connection('firebird')->statement(
                'UPDATE COMPRA_CONFIG_APROV SET NR_ORDEM = :nr_ordem WHERE ID_CONFIG_APROV = :id',
                ['nr_ordem' => $index + 1, 'id' => $id]
            );
        }
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_CONFIG_APROV, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
