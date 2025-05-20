<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Financeiro extends Model
{
    use HasFactory;

    public function ContasBloqueadas($status)
    {
        $query = "
                SELECT
                    CONTAS.CD_EMPRESA,
                    CONTAS.NR_LANCAMENTO,
                    CONTAS.CD_PESSOA,
                    CONTAS.CD_PESSOA || ' - ' || P.NM_PESSOA NM_PESSOA,
                    CONTAS.CD_TIPOCONTA || ' ' || TC.DS_TIPOCONTA DS_TIPOCONTA,
                    CONTAS.NR_DOCUMENTO||' / '||RMAX.O_NR_MAIORPARCELA NR_DOCUMENTO,
                    RMAX.O_NR_MAIORPARCELA PARCELAS,
                    CAST(SUM(CONTAS.VL_DOCUMENTO) AS NUMERIC(12,2)) VL_DOCUMENTO,
                    CONTAS.DS_OBSERVACAO,
                    CONTAS.DS_LIBERACAO,
                    CONTAS.DT_LANCAMENTO,
                    COALESCE(CONTAS.ST_VISTO, 'N') ST_VISTO
                FROM CONTAS
                INNER JOIN RETORNA_MAIORPARCELACONTAS(CONTAS.CD_EMPRESA, CONTAS.NR_LANCAMENTO, CONTAS.CD_PESSOA, CONTAS.CD_TIPOCONTA) RMAX ON (1 = 1)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = CONTAS.CD_PESSOA)
                INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = CONTAS.CD_TIPOCONTA)
                WHERE CONTAS.ST_BLOQUEADA = 'S'
                    AND CONTAS.ST_CONTAS NOT IN ('C', 'L')                   
                    AND COALESCE(CONTAS.ST_VISTO, 'N') = '$status'
                GROUP BY
                    CONTAS.CD_EMPRESA,
                    CONTAS.NR_LANCAMENTO,
                    CONTAS.CD_PESSOA,
                    CONTAS.CD_PESSOA,
                    P.NM_PESSOA,
                    CONTAS.CD_TIPOCONTA,
                    TC.DS_TIPOCONTA,
                    CONTAS.NR_DOCUMENTO,
                    CONTAS.DS_LIBERACAO,
                    RMAX.O_NR_MAIORPARCELA,
                    CONTAS.DS_OBSERVACAO,
                    CONTAS.DT_LANCAMENTO,
                    CONTAS.ST_VISTO  ";

        $results = DB::connection('firebird')->select($query);
        return $results =  Helper::ConvertFormatText($results);
    }
    public function listHistoricoContasBloqueadas($cd_empresa, $nr_lancamento)
    {
        $query = "
                SELECT
                    CH.CD_EMPRESA,
                    CH.NR_LANCAMENTO,
                    CH.CD_PESSOA,
                    CH.CD_HISTORICO || ' - ' || HISTORICO.DS_HISTORICO DS_HISTORICO,
                    CH.VL_DOCUMENTO,
                    CH.NR_PARCELA,
                    CONTAS.DT_LANCAMENTO,
                    CONTAS.DT_VENCIMENTO
                FROM CONTASHISTORICO CH
                INNER JOIN CONTAS ON (CH.CD_EMPRESA = CONTAS.CD_EMPRESA
                    AND CH.NR_LANCAMENTO = CONTAS.NR_LANCAMENTO
                    AND CH.CD_PESSOA = CONTAS.CD_PESSOA
                    AND CH.CD_TIPOCONTA = CONTAS.CD_TIPOCONTA
                    AND CH.NR_PARCELA = CONTAS.NR_PARCELA)
                INNER JOIN HISTORICO ON (HISTORICO.CD_HISTORICO = CH.CD_HISTORICO)
                WHERE CH.NR_LANCAMENTO = $nr_lancamento
                    AND CH.CD_EMPRESA = $cd_empresa
                    ";

        $results = DB::connection('firebird')->select($query);
        return $results =  Helper::ConvertFormatText($results);
    }
    public function updateStatusContasBloqueadas($cd_empresa, $nr_lancamento, $status, $ds_liberacao)
    {
        return DB::transaction(function () use ($cd_empresa, $nr_lancamento, $status, $ds_liberacao) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                UPDATE CONTAS C
                SET C.ST_BLOQUEADA = '$status',
                    C.ST_VISTO = 'S',
                    C.DS_LIBERACAO = '$ds_liberacao'
                WHERE C.NR_LANCAMENTO = $nr_lancamento
                    AND C.CD_EMPRESA = $cd_empresa
                ";

            return DB::connection('firebird')->select($query);
        });
    }
    public function listCentroCustoContasBloqueadas($cd_empresa, $nr_lancamento)
    {
        $query = "
                    SELECT
                        C.DT_LANCAMENTO,
                        C.CD_EMPRESA,
                        COALESCE(H.CD_CENTROCUSTO, N.CD_CENTROCUSTO) CD_CENTROCUSTO,
                        COALESCE(H.VL_CENTROCUSTO, N.VL_CENTROCUSTO) VL_CENTROCUSTO,
                        CC.DS_CENTROCUSTO,
                        C.NR_DOCUMENTO,
                        C.CD_PESSOA,
                        C.NR_PARCELA,
                        C.NR_LANCAMENTO
                    FROM CONTAS C
                    LEFT JOIN CONTASHISTORICOCC H ON (C.CD_EMPRESA = H.CD_EMPRESA
                        AND C.NR_LANCAMENTO = H.NR_LANCAMENTO
                        AND C.NR_PARCELA = H.NR_PARCELA
                        AND C.CD_PESSOA = H.CD_PESSOA
                        AND C.CD_TIPOCONTA = H.CD_TIPOCONTA)

                    LEFT JOIN NOTA NT ON (NT.NR_LANCAMENTO = C.NR_LANCTONOTA
                        AND NT.CD_EMPRESA = C.CD_EMPRESA
                        AND NT.CD_SERIE = C.CD_SERIE
                        AND NT.TP_NOTA = C.TP_CONTAS)
                    LEFT JOIN ITEMNOTACC N ON (NT.CD_EMPRESA = N.CD_EMPRESA
                        AND NT.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND NT.TP_NOTA = N.TP_NOTA
                        AND NT.CD_SERIE = N.CD_SERIE)

                    LEFT JOIN CENTROCUSTO CC ON (CC.CD_EMPRESA = COALESCE(H.CD_EMPRESA, N.CD_EMPRESA)
                        AND CC.CD_CENTROCUSTO = COALESCE(H.CD_CENTROCUSTO, N.CD_CENTROCUSTO))

                    WHERE C.NR_LANCAMENTO = $nr_lancamento
                        AND C.CD_EMPRESA = $cd_empresa
                    GROUP BY C.DT_LANCAMENTO,
                        C.CD_EMPRESA,
                        H.CD_CENTROCUSTO,
                        H.VL_CENTROCUSTO,
                        N.CD_CENTROCUSTO,
                        N.VL_CENTROCUSTO,
                        C.NR_DOCUMENTO,
                        C.CD_PESSOA,
                        C.NR_PARCELA,
                        C.NR_LANCAMENTO,
                        CC.DS_CENTROCUSTO  
                    ";

        $results = DB::connection('firebird')->select($query);
        return $results =  Helper::ConvertFormatText($results);
    }
}
