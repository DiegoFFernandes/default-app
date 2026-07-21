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
                A.NM_APROVADOR,
                A.CD_EMPRESA,
                A.CD_CENTROCUSTO,
                CC.DS_CENTROCUSTO
            FROM COMPRA_CONFIG_APROV A
            LEFT JOIN COMPRA_CENTROCUSTO CC ON (CC.CD_CENTROCUSTO = A.CD_CENTROCUSTO
                                            AND CC.CD_EMPRESA     = A.CD_EMPRESA)
            WHERE A.ID_FAIXA = :id_faixa
            ORDER BY A.NR_ORDEM
        ", ['id_faixa' => $idFaixa]));
    }

    public function getByFaixaCentro(int $idFaixa, int $cdEmpresa, int $cdCentroCusto)
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                A.ID_CONFIG_APROV,
                A.ID_FAIXA,
                A.NR_ORDEM,
                A.DS_CARGO,
                A.CD_USUARIO,
                A.NM_APROVADOR,
                A.CD_EMPRESA,
                A.CD_CENTROCUSTO
            FROM COMPRA_CONFIG_APROV A
            WHERE A.ID_FAIXA      = :id_faixa
              AND A.CD_EMPRESA    = :cd_empresa
              AND A.CD_CENTROCUSTO = :cd_centrocusto
            ORDER BY A.NR_ORDEM
        ", [
            'id_faixa'       => $idFaixa,
            'cd_empresa'     => $cdEmpresa,
            'cd_centrocusto' => $cdCentroCusto,
        ]));
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_CONFIG_APROV (
                ID_CONFIG_APROV, ID_FAIXA, NR_ORDEM, DS_CARGO, CD_USUARIO, NM_APROVADOR,
                CD_EMPRESA, CD_CENTROCUSTO
            ) VALUES (
                :id, :id_faixa, :nr_ordem, :ds_cargo, :cd_usuario, :nm_aprovador,
                :cd_empresa, :cd_centrocusto
            )
        ", [
            'id'             => $id,
            'id_faixa'       => $data['id_faixa'],
            'nr_ordem'       => $data['nr_ordem'],
            'ds_cargo'       => \Helper::ToIso($data['ds_cargo']),
            'cd_usuario'     => $data['cd_usuario'],
            'nm_aprovador'   => \Helper::ToIso($data['nm_aprovador']),
            'cd_empresa'     => $data['cd_empresa'],
            'cd_centrocusto' => $data['cd_centrocusto'],
        ]);

        return $id;
    }

    /**
     * Copia os aprovadores de uma faixa para outra, reapontando para a empresa
     * de destino. Centros de resultado inexistentes na empresa destino são
     * ignorados — copiá-los geraria configuração que nunca seria encontrada,
     * já que CD_CENTROCUSTO só é único dentro de uma empresa.
     *
     * @return array{copiados:int, ignorados:int}
     */
    public function copiarParaFaixa(int $idFaixaOrigem, int $idFaixaDestino, int $cdEmpresaDestino): array
    {
        $origem = DB::connection('firebird')->select("
            SELECT NR_ORDEM, DS_CARGO, CD_USUARIO, NM_APROVADOR, CD_CENTROCUSTO
            FROM COMPRA_CONFIG_APROV
            WHERE ID_FAIXA = :id_faixa
            ORDER BY NR_ORDEM
        ", ['id_faixa' => $idFaixaOrigem]);

        $copiados = 0;
        $ignorados = 0;

        foreach ($origem as $a) {
            $existe = DB::connection('firebird')->selectOne("
                SELECT 1 ACHOU FROM COMPRA_CENTROCUSTO
                WHERE CD_EMPRESA = :cd_empresa AND CD_CENTROCUSTO = :cd_centrocusto
            ", ['cd_empresa' => $cdEmpresaDestino, 'cd_centrocusto' => $a->CD_CENTROCUSTO]);

            if (!$existe) {
                $ignorados++;
                continue;
            }

            DB::connection('firebird')->statement("
                INSERT INTO COMPRA_CONFIG_APROV (
                    ID_CONFIG_APROV, ID_FAIXA, NR_ORDEM, DS_CARGO, CD_USUARIO, NM_APROVADOR,
                    CD_EMPRESA, CD_CENTROCUSTO
                ) VALUES (
                    :id, :id_faixa, :nr_ordem, :ds_cargo, :cd_usuario, :nm_aprovador,
                    :cd_empresa, :cd_centrocusto
                )
            ", [
                'id'             => $this->nextId(),
                'id_faixa'       => $idFaixaDestino,
                'nr_ordem'       => $a->NR_ORDEM,
                'ds_cargo'       => $a->DS_CARGO,
                'cd_usuario'     => $a->CD_USUARIO,
                'nm_aprovador'   => $a->NM_APROVADOR,
                'cd_empresa'     => $cdEmpresaDestino,
                'cd_centrocusto' => $a->CD_CENTROCUSTO,
            ]);

            $copiados++;
        }

        return ['copiados' => $copiados, 'ignorados' => $ignorados];
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
