<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraConfigFaixa extends Model
{
    use HasFactory;

    public function getAll()
    {
        $caseNome = Empresa::buildCaseNome('E.CD_EMPRESA');

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                F.ID_FAIXA,
                F.CD_EMPRESA,
                {$caseNome} AS NM_EMPRESA,
                F.DS_FAIXA,
                F.VL_MINIMO,
                F.VL_MAXIMO,
                F.NR_ORDEM,
                F.ST_ATIVO
            FROM COMPRA_CONFIG_FAIXA F
            INNER JOIN EMPRESA E ON E.CD_EMPRESA = F.CD_EMPRESA
            ORDER BY F.CD_EMPRESA, F.NR_ORDEM
        "));
    }

    /**
     * Resolve a faixa pelo valor considerando apenas as que possuem aprovador
     * cadastrado para o centro de resultado informado. Permite que faixas
     * sobrepostas funcionem como réguas de valor distintas por centro.
     */
    public function findFaixaByValorCentro(int $cdEmpresa, float $vlTotal, int $cdCentroCusto)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT FIRST 1
                F.ID_FAIXA,
                F.CD_EMPRESA,
                F.DS_FAIXA,
                F.VL_MINIMO,
                F.VL_MAXIMO,
                F.NR_ORDEM
            FROM COMPRA_CONFIG_FAIXA F
            WHERE F.CD_EMPRESA = :cd_empresa
              AND F.ST_ATIVO   = 'S'
              AND F.VL_MINIMO  <= :vl_total
              AND (F.VL_MAXIMO IS NULL OR F.VL_MAXIMO >= :vl_total2)
              AND EXISTS (
                    SELECT 1 FROM COMPRA_CONFIG_APROV A
                    WHERE A.ID_FAIXA       = F.ID_FAIXA
                      AND A.CD_EMPRESA     = :cd_empresa2
                      AND A.CD_CENTROCUSTO = :cd_centrocusto
              )
            ORDER BY F.NR_ORDEM
        ", [
            'cd_empresa'     => $cdEmpresa,
            'vl_total'       => $vlTotal,
            'vl_total2'      => $vlTotal,
            'cd_empresa2'    => $cdEmpresa,
            'cd_centrocusto' => $cdCentroCusto,
        ]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function getProximaOrdem(int $cdEmpresa): int
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT COALESCE(MAX(NR_ORDEM), 0) ULTIMA
            FROM COMPRA_CONFIG_FAIXA
            WHERE CD_EMPRESA = :cd_empresa
        ", ['cd_empresa' => $cdEmpresa]);

        return ((int) ($row->ULTIMA ?? 0)) + 1;
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_CONFIG_FAIXA (
                ID_FAIXA, CD_EMPRESA, DS_FAIXA,
                VL_MINIMO, VL_MAXIMO, NR_ORDEM, ST_ATIVO
            ) VALUES (
                :id, :cd_empresa, :ds_faixa,
                :vl_minimo, :vl_maximo, :nr_ordem, 'S'
            )
        ", [
            'id'         => $id,
            'cd_empresa' => $data['cd_empresa'],
            'ds_faixa'   => \Helper::ToIso($data['ds_faixa']),
            'vl_minimo'  => $data['vl_minimo'],
            'vl_maximo'  => $data['vl_maximo'] ?: null,
            'nr_ordem'   => $data['nr_ordem'],
        ]);

        return $id;
    }

    public function updateData(int $id, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_CONFIG_FAIXA SET
                DS_FAIXA  = :ds_faixa,
                VL_MINIMO = :vl_minimo,
                VL_MAXIMO = :vl_maximo,
                NR_ORDEM  = :nr_ordem,
                ST_ATIVO  = :st_ativo
            WHERE ID_FAIXA = :id
        ", [
            'ds_faixa'  => \Helper::ToIso($data['ds_faixa']),
            'vl_minimo' => $data['vl_minimo'],
            'vl_maximo' => $data['vl_maximo'] ?: null,
            'nr_ordem'  => $data['nr_ordem'],
            'st_ativo'  => $data['st_ativo'] ?? 'S',
            'id'        => $id,
        ]);
    }

    public function deleteById(int $id)
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_CONFIG_FAIXA WHERE ID_FAIXA = :id',
            ['id' => $id]
        );
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_CONFIG_FAIXA, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
