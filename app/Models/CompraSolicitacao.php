<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraSolicitacao extends Model
{
    use HasFactory;

    public function getAll($cdUsuario = null)
    {
        $caseNome = Empresa::buildCaseNome('E.CD_EMPRESA');

        $sql = "
            SELECT
                S.CD_SOLICITACAO,
                S.CD_EMPRESA,
                {$caseNome} AS NM_EMPRESA,
                S.CD_USUARIO_SOLICITANTE,
                S.DT_SOLICITACAO,
                S.DS_JUSTIFICATIVA,
                S.ST_SOLICITACAO,
                S.VL_TOTAL,
                CC.DS_CENTROCUSTO,
                S.DT_REGISTRO
            FROM COMPRA_SOLICITACAO S
            INNER JOIN COMPRA_CENTROCUSTO CC ON (CC.CD_EMPRESA = S.CD_EMPRESA AND CC.CD_CENTROCUSTO = S.CD_CENTROCUSTO)
            INNER JOIN EMPRESA E ON E.CD_EMPRESA = S.CD_EMPRESA
            ";
        $params = [];

        if ($cdUsuario) {
            $sql    .= ' WHERE S.CD_USUARIO_SOLICITANTE = :cd_usuario';
            $params  = ['cd_usuario' => $cdUsuario];
        }

        $sql .= ' ORDER BY S.DT_REGISTRO DESC';

        return \Helper::ConvertFormatText(DB::connection('firebird')->select($sql, $params));
    }

    public function getCounts($cdUsuario = null)
    {
        $where  = $cdUsuario ? 'WHERE CD_USUARIO_SOLICITANTE = :cd_usuario' : '';
        $params = $cdUsuario ? ['cd_usuario' => $cdUsuario] : [];

        $rows = DB::connection('firebird')->select("
            SELECT ST_SOLICITACAO, COUNT(*) QT
            FROM COMPRA_SOLICITACAO
            $where
            GROUP BY ST_SOLICITACAO
        ", $params);

        return collect($rows)->pluck('QT', 'ST_SOLICITACAO');
    }

    public function findById(int $id)
    {
        $caseNome = Empresa::buildCaseNome('E.CD_EMPRESA');

        $row = DB::connection('firebird')->selectOne("
            SELECT
                S.CD_SOLICITACAO,
                S.CD_EMPRESA,
                {$caseNome} AS NM_EMPRESA,
                S.CD_USUARIO_SOLICITANTE,
                S.DT_SOLICITACAO,
                S.DS_JUSTIFICATIVA,
                S.DS_OBSERVACAO,
                S.ST_SOLICITACAO,
                S.CD_FORNECEDOR,
                S.VL_TOTAL,
                S.DT_REGISTRO,
                S.DT_ATUALIZADO,
                S.CD_CENTROCUSTO,
                CC.DS_CENTROCUSTO
            FROM COMPRA_SOLICITACAO S
            INNER JOIN EMPRESA E ON E.CD_EMPRESA = S.CD_EMPRESA
            LEFT JOIN COMPRA_CENTROCUSTO CC ON CC.CD_CENTROCUSTO = S.CD_CENTROCUSTO
            WHERE S.CD_SOLICITACAO = :id
        ", ['id' => $id]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_SOLICITACAO (
                CD_SOLICITACAO, CD_EMPRESA, CD_USUARIO_SOLICITANTE,
                DT_SOLICITACAO, DS_JUSTIFICATIVA, DS_OBSERVACAO,
                ST_SOLICITACAO, CD_CENTROCUSTO, DT_REGISTRO, DT_ATUALIZADO
            ) VALUES (
                :id, :cd_empresa, :cd_usuario,
                :dt_solicitacao, :ds_justificativa, :ds_observacao,
                'RAS', :cd_centrocusto, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ", [
            'id'               => $id,
            'cd_empresa'       => $data['cd_empresa'],
            'cd_usuario'       => $data['cd_usuario_solicitante'],
            'dt_solicitacao'   => $data['dt_solicitacao'],
            'ds_justificativa' => \Helper::ToIso($data['ds_justificativa']),
            'ds_observacao'    => \Helper::ToIso($data['ds_observacao'] ?? null),
            'cd_centrocusto'   => $data['cd_centrocusto'] ?? null,
        ]);

        return $id;
    }

    public function updateData(int $id, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOLICITACAO SET
                CD_EMPRESA       = :cd_empresa,
                DT_SOLICITACAO   = :dt_solicitacao,
                DS_JUSTIFICATIVA = :ds_justificativa,
                DS_OBSERVACAO    = :ds_observacao,
                CD_CENTROCUSTO   = :cd_centrocusto,
                DT_ATUALIZADO    = CURRENT_TIMESTAMP
            WHERE CD_SOLICITACAO = :id
              AND ST_SOLICITACAO  = 'RAS'
        ", [
            'cd_empresa'       => $data['cd_empresa'],
            'dt_solicitacao'   => $data['dt_solicitacao'],
            'ds_justificativa' => \Helper::ToIso($data['ds_justificativa']),
            'ds_observacao'    => \Helper::ToIso($data['ds_observacao'] ?? null),
            'cd_centrocusto'   => $data['cd_centrocusto'] ?? null,
            'id'               => $id,
        ]);
    }

    public function updateStatus(int $id, string $status)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOLICITACAO SET
                ST_SOLICITACAO = :status,
                DT_ATUALIZADO  = CURRENT_TIMESTAMP
            WHERE CD_SOLICITACAO = :id
        ", ['status' => $status, 'id' => $id]);
    }

    public function updateFornecedorTotal(int $id, int $cdFornecedor, float $vlTotal)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOLICITACAO SET
                CD_FORNECEDOR = :cd_fornecedor,
                VL_TOTAL      = :vl_total,
                DT_ATUALIZADO = CURRENT_TIMESTAMP
            WHERE CD_SOLICITACAO = :id
        ", ['cd_fornecedor' => $cdFornecedor, 'vl_total' => $vlTotal, 'id' => $id]);
    }

    public function deleteById(int $id)
    {
        DB::connection('firebird')->statement("
            DELETE FROM COMPRA_SOLICITACAO
            WHERE CD_SOLICITACAO = :id AND ST_SOLICITACAO = 'RAS'
        ", ['id' => $id]);
    }

    public function enviarAnalise(int $id)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOLICITACAO
            SET ST_SOLICITACAO = 'ANA'
            WHERE CD_SOLICITACAO = :id
              AND ST_SOLICITACAO = 'RAS'
        ", ['id' => $id]);
    }

    public function cancelar(int $id)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_SOLICITACAO
            SET ST_SOLICITACAO = 'CAN'
            WHERE CD_SOLICITACAO = :id
              AND ST_SOLICITACAO NOT IN ('RAS', 'CAN')
        ", ['id' => $id]);
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_SOLICITACAO, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
