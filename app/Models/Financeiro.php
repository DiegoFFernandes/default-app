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
                    CONTAS.NR_DOCUMENTO ||' - '|| CONTAS.NR_PARCELA ||' / '||RMAX.O_NR_MAIORPARCELA NR_DOCUMENTO,
                    CONTAS.NR_PARCELA,
                    RMAX.O_NR_MAIORPARCELA PARCELAS,
                    CAST(SUM(CONTAS.VL_DOCUMENTO) AS NUMERIC(12,2)) VL_DOCUMENTO,
                    CONTAS.DS_OBSERVACAO,
                    CONTAS.DS_LIBERACAO,
                    CONTAS.DT_LANCAMENTO,
                    CONTAS.DT_VENCIMENTO,
                    COALESCE(CONTAS.ST_VISTO, 'N') ST_VISTO
                FROM CONTAS
                INNER JOIN RETORNA_MAIORPARCELACONTAS(CONTAS.CD_EMPRESA, CONTAS.NR_LANCAMENTO, CONTAS.CD_PESSOA, CONTAS.CD_TIPOCONTA) RMAX ON (1 = 1)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = CONTAS.CD_PESSOA)
                INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = CONTAS.CD_TIPOCONTA)
                WHERE CONTAS.ST_BLOQUEADA = 'S'
                    AND CONTAS.ST_CONTAS NOT IN ('C', 'L', 'A')                   
                    --AND COALESCE(CONTAS.ST_VISTO, 'N') = '$status'
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
                    CONTAS.DT_VENCIMENTO,
                    CONTAS.ST_VISTO, 
                    CONTAS.NR_PARCELA";

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
    public function arquivoRemessa($filtros)
    {
        $bindings = [];

        $filtroData = '';
        if (!empty($filtros['dt_inicio']) && !empty($filtros['dt_fim'])) {
            $filtroData = 'AND C.DT_LANCAMENTO BETWEEN CAST(:dt_inicio AS DATE) AND CAST(:dt_fim AS DATE)';
            $bindings['dt_inicio'] = $filtros['dt_inicio'];
            $bindings['dt_fim'] = $filtros['dt_fim'];
        }

        $filtroEmpresa = '';
        if (!empty($filtros['cd_empresa'])) {
            $filtroEmpresa = 'AND C.CD_EMPRESA = :cd_empresa';
            $bindings['cd_empresa'] = $filtros['cd_empresa'];
        }

        $filtroPessoa = '';
        if (!empty($filtros['cd_pessoa'])) {
            $filtroPessoa = 'AND C.CD_PESSOA = :cd_pessoa';
            $bindings['cd_pessoa'] = $filtros['cd_pessoa'];
        }

        $filtroFormaPagto = '';
        if (!empty($filtros['cd_formapagto'])) {
            $filtroFormaPagto = 'AND C.CD_FORMAPAGTO = :cd_formapagto';
            $bindings['cd_formapagto'] = $filtros['cd_formapagto'];
        }

        $query = "
                SELECT 
                    COALESCE(CB.R_ST_REGISTRO, 'N') ST_REGISTRO,
                    COALESCE(CB.R_DS_INSTRUCAO, 'Sem Remessa') DS_REMESSA,
                    CB.R_DS_OCORRENCIA,
                    CB.R_NR_ARQUIVO,
                    C.NR_BOLETO,
                    C.DT_LANCAMENTO,
                    C.DT_VENCIMENTO,
                    C.CD_EMPRESA,
                    C.NR_DOCUMENTO,
                    C.NR_PARCELA,
                    C.NR_LANCAMENTO,
                    C.CD_FORMAPAGTO,
                    FP.DS_FORMAPAGTO,
                    C.CD_PESSOA,
                    P.NM_PESSOA,
                    CASE
                        WHEN NR_BOLETO IS NULL THEN 'S'
                        ELSE 'I'
                    END ST_BOLETO,
                    C.ST_CARTORIO,
                    C.ST_INCOBRAVEL,
                    C.ST_SCPC,
                    C.VL_SALDO
                FROM CONTAS C
                INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
                LEFT JOIN RETORNA_ULTIMA_INSTRUCAOBOLETO(C.CD_EMPRESA, C.CD_FORMAPAGTO, C.NR_BOLETO) CB ON (1 = 1)
                INNER JOIN BOLETO ON (BOLETO.CD_FORMAPAGTO = C.CD_FORMAPAGTO
                    AND C.CD_EMPRESA = BOLETO.CD_EMPRESA)
                LEFT JOIN FORMAPAGTO FP ON (FP.CD_FORMAPAGTO = C.CD_FORMAPAGTO)
                WHERE BOLETO.ST_ATIVO = 'S'
                      AND C.CD_TIPOCONTA IN (2)
                      AND C.ST_CONTAS IN ('P', 'T')
                      AND COALESCE(CB.R_DS_INSTRUCAO, 'Sem Remessa') NOT IN ('Registro Confirmado')
                      AND COALESCE(CB.R_ST_REGISTRO, 'N') <> 'S'
                      {$filtroData}
                      {$filtroEmpresa}
                      {$filtroPessoa}
                      {$filtroFormaPagto}
                ORDER BY C.DT_LANCAMENTO DESC, C.NR_PARCELA DESC";

        $results = DB::connection('firebird')->select($query, $bindings);
        return $results = Helper::ConvertFormatText($results);
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

    // Faz o update do ST_REGISTRO, coluna criada por nós somente para o usuario atualizar o registro para 'S',
    // assim ele da sequencia somente dos itens que ainda não validou.
    public function updateContasBoleto($cd_empresa, $nr_boleto, $cd_formapagto)
    {
        return DB::transaction(function () use ($cd_empresa, $nr_boleto, $cd_formapagto) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                UPDATE CONTASBOLETO CB SET CB.ST_REGISTRO = 'S'
                WHERE CB.CD_EMPRESA = :cd_empresa
                    AND CB.NR_BOLETO = :nr_boleto
                    AND CB.CD_FORMAPAGTO = :cd_formapagto";

            return DB::connection('firebird')->select($query, [
                'cd_empresa'    => $cd_empresa,
                'nr_boleto'     => $nr_boleto,
                'cd_formapagto' => $cd_formapagto,
            ]);
        });
    }
}
