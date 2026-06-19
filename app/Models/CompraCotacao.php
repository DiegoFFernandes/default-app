<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraCotacao extends Model
{
    use HasFactory;

    public function getBySolicitacao(int $idSolicitacao)
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                C.ID_COTACAO,
                C.ID_SOLICITACAO,
                C.CD_FORNECEDOR,
                P.NM_PESSOA AS NM_FORNECEDOR,
                C.NR_PRAZO_ENTREGA,
                C.DS_CONDICAO_PAGAMENTO,
                C.CD_FORMAPAGTO,
                C.VL_TOTAL,
                C.DS_OBSERVACAO,
                C.ST_SELECIONADA,
                C.DS_MOTIVO_ESCOLHA,
                C.DT_COTACAO
            FROM COMPRA_COTACAO C
            INNER JOIN PESSOA P ON P.CD_PESSOA = C.CD_FORNECEDOR
            WHERE C.ID_SOLICITACAO = :id_sol
            ORDER BY C.ID_COTACAO
        ", ['id_sol' => $idSolicitacao]));
    }

    public function findById(int $id)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT
                C.ID_COTACAO,
                C.ID_SOLICITACAO,
                C.CD_FORNECEDOR,
                P.NM_PESSOA AS NM_FORNECEDOR,
                C.NR_PRAZO_ENTREGA,
                C.DS_CONDICAO_PAGAMENTO,
                C.CD_FORMAPAGTO,
                C.VL_TOTAL,
                C.DS_OBSERVACAO,
                C.ST_SELECIONADA,
                C.DS_MOTIVO_ESCOLHA
            FROM COMPRA_COTACAO C
            INNER JOIN PESSOA P ON P.CD_PESSOA = C.CD_FORNECEDOR
            WHERE C.ID_COTACAO = :id
        ", ['id' => $id]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function getCotacaoSelecionada(int $idSolicitacao)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT FIRST 1
                C.ID_COTACAO,
                C.CD_FORNECEDOR,
                P.NM_PESSOA AS NM_FORNECEDOR,
                C.VL_TOTAL,
                C.DS_MOTIVO_ESCOLHA
            FROM COMPRA_COTACAO C
            INNER JOIN PESSOA P ON P.CD_PESSOA = C.CD_FORNECEDOR
            WHERE C.ID_SOLICITACAO = :id_sol
              AND C.ST_SELECIONADA = 'S'
        ", ['id_sol' => $idSolicitacao]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function store(array $data)
    {
        $id = $this->nextId();

        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_COTACAO (
                ID_COTACAO, ID_SOLICITACAO, CD_FORNECEDOR,
                NR_PRAZO_ENTREGA, DS_CONDICAO_PAGAMENTO, CD_FORMAPAGTO,
                VL_TOTAL, DS_OBSERVACAO, ST_SELECIONADA, DT_COTACAO
            ) VALUES (
                :id, :id_solicitacao, :cd_fornecedor,
                :nr_prazo, :ds_condicao, :cd_formapagto,
                :vl_total, :ds_observacao, 'N', CURRENT_TIMESTAMP
            )
        ", [
            'id'             => $id,
            'id_solicitacao' => $data['id_solicitacao'],
            'cd_fornecedor'  => $data['cd_fornecedor'],
            'nr_prazo'       => $data['nr_prazo_entrega'],
            'ds_condicao'    => \Helper::ToIso($data['ds_condicao_pagamento']),
            'cd_formapagto'  => $data['cd_formapagto'],
            'vl_total'       => $data['vl_total'],
            'ds_observacao'  => \Helper::ToIso($data['ds_observacao'] ?? null),
        ]);

        return $id;
    }

    public function updateData(int $id, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_COTACAO SET
                CD_FORNECEDOR         = :cd_fornecedor,
                NR_PRAZO_ENTREGA      = :nr_prazo,
                DS_CONDICAO_PAGAMENTO = :ds_condicao,
                CD_FORMAPAGTO         = :cd_formapagto,
                VL_TOTAL              = :vl_total,
                DS_OBSERVACAO         = :ds_observacao
            WHERE ID_COTACAO = :id
        ", [
            'cd_fornecedor' => $data['cd_fornecedor'],
            'nr_prazo'      => $data['nr_prazo_entrega'],
            'ds_condicao'   => \Helper::ToIso($data['ds_condicao_pagamento']),
            'cd_formapagto' => $data['cd_formapagto'],
            'vl_total'      => $data['vl_total'],
            'ds_observacao' => \Helper::ToIso($data['ds_observacao'] ?? null),
            'id'            => $id,
        ]);
    }

    public function selecionarFornecedor(int $idSolicitacao, int $idCotacao, ?string $motivo)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_COTACAO SET ST_SELECIONADA = 'N', DS_MOTIVO_ESCOLHA = NULL
            WHERE ID_SOLICITACAO = :id_sol
        ", ['id_sol' => $idSolicitacao]);

        DB::connection('firebird')->statement("
            UPDATE COMPRA_COTACAO SET
                ST_SELECIONADA    = 'S',
                DS_MOTIVO_ESCOLHA = :motivo
            WHERE ID_COTACAO = :id
        ", ['motivo' => \Helper::ToIso($motivo), 'id' => $idCotacao]);
    }

    public function deleteById(int $id, int $idSolicitacao)
    {
        DB::connection('firebird')->statement("
            DELETE FROM COMPRA_COTACAO
            WHERE ID_COTACAO = :id AND ID_SOLICITACAO = :id_sol
        ", ['id' => $id, 'id_sol' => $idSolicitacao]);
    }

    public function countBySolicitacao(int $idSolicitacao): int
    {
        return DB::connection('firebird')
            ->selectOne("SELECT COUNT(*) QT FROM COMPRA_COTACAO WHERE ID_SOLICITACAO = :id_sol", ['id_sol' => $idSolicitacao])
            ->QT ?? 0;
    }

    public function searchFornecedor(string $term)
    {
        return DB::connection('firebird')->select("
            SELECT FIRST 20
                PESSOA.CD_PESSOA AS id,
                CAST(PESSOA.CD_PESSOA AS VARCHAR(20)) || ' - ' || PESSOA.NM_PESSOA AS text
            FROM PESSOA
            WHERE PESSOA.ST_ATIVA = 'S'
              AND PESSOA.CD_TIPOPESSOA IN (2, 3)
              AND (PESSOA.NM_PESSOA CONTAINING :term
                   OR CAST(PESSOA.CD_PESSOA AS VARCHAR(20)) CONTAINING :term2)
            ORDER BY PESSOA.NM_PESSOA
        ", ['term' => $term, 'term2' => $term]);
    }

    private function nextId()
    {
        return DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_COMPRA_COTACAO, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;
    }
}
