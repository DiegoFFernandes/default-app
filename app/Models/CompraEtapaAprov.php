<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraEtapaAprov extends Model
{
    use HasFactory;

    public function getBySolicitacao($idSolicitacao)
    {
        return DB::connection('firebird')->select("
            SELECT
                E.ID_ETAPA,
                E.CD_SOLICITACAO,
                E.NR_ORDEM,
                E.DS_CARGO,
                E.CD_USUARIO_APROVADOR,
                E.ST_ETAPA,
                E.CD_USUARIO_ACAO,
                E.DT_ACAO,
                E.DS_OBSERVACAO
            FROM COMPRA_ETAPA_APROV E
            WHERE E.CD_SOLICITACAO = :id_sol
            ORDER BY E.NR_ORDEM
        ", ['id_sol' => $idSolicitacao]);
    }

    public function findById($id)
    {
        return DB::connection('firebird')->selectOne("
            SELECT
                E.ID_ETAPA,
                E.CD_SOLICITACAO,
                E.NR_ORDEM,
                E.DS_CARGO,
                E.CD_USUARIO_APROVADOR,
                E.ST_ETAPA,
                E.CD_USUARIO_ACAO,
                E.DT_ACAO,
                E.DS_OBSERVACAO
            FROM COMPRA_ETAPA_APROV E
            WHERE E.ID_ETAPA = :id
        ", ['id' => $id]);
    }

    public function getPendentesParaUsuario($cdUsuario)
    {
        $caseNome = Empresa::buildCaseNome('EN.CD_EMPRESA');

        return DB::connection('firebird')->select("
            SELECT
                E.ID_ETAPA,
                E.CD_SOLICITACAO,
                S.CD_EMPRESA,
                {$caseNome} as NM_EMPRESA,
                E.NR_ORDEM,
                E.DS_CARGO,
                S.DS_JUSTIFICATIVA,
                S.VL_TOTAL,
                S.DT_SOLICITACAO
            FROM COMPRA_ETAPA_APROV E
            INNER JOIN COMPRA_SOLICITACAO S  ON S.CD_SOLICITACAO = E.CD_SOLICITACAO
            INNER JOIN EMPRESA EN            ON EN.CD_EMPRESA    = S.CD_EMPRESA
            WHERE E.CD_USUARIO_APROVADOR = :cd_usuario
              AND E.ST_ETAPA             = 'PEN'
              AND S.ST_SOLICITACAO       = 'APR'
            ORDER BY S.DT_REGISTRO
        ", ['cd_usuario' => $cdUsuario]);
    }

    public function store($data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_ETAPA_APROV (
                ID_ETAPA, CD_SOLICITACAO, NR_ORDEM,
                DS_CARGO, CD_USUARIO_APROVADOR, ST_ETAPA
            ) VALUES (
                :id, :cd_sol, :nr_ordem,
                :ds_cargo, :cd_usuario, 'PEN'
            )
        ", [
            'id'         => $id,
            'cd_sol'     => $data['cd_solicitacao'],
            'nr_ordem'   => $data['nr_ordem'],
            'ds_cargo'   => $data['ds_cargo'],
            'cd_usuario' => $data['cd_usuario_aprovador'],
        ]);

        return $id;
    }

    public function updateEtapa($id, $status, $cdUsuario, $obs)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_ETAPA_APROV SET
                ST_ETAPA        = :status,
                CD_USUARIO_ACAO = :cd_usuario,
                DT_ACAO         = CURRENT_TIMESTAMP,
                DS_OBSERVACAO   = :obs
            WHERE ID_ETAPA = :id
        ", ['status' => $status, 'cd_usuario' => $cdUsuario, 'obs' => $obs, 'id' => $id]);
    }

    public function allApproved($idSolicitacao)
    {
        $result = DB::connection('firebird')->selectOne("
            SELECT COUNT(*) QT FROM COMPRA_ETAPA_APROV
            WHERE CD_SOLICITACAO = :id_sol AND ST_ETAPA <> 'APR'
        ", ['id_sol' => $idSolicitacao]);

        return ($result->QT ?? 1) == 0;
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_ETAPA_APROV, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
