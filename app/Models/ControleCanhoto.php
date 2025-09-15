<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ControleCanhoto extends Model
{
    use HasFactory;

    public function canhotoNaoRecebidos()
    {
        $query = "
            SELECT
                NT.NR_NOTAFISCAL,
                NT.CD_SERIE,
                NS.NR_RPS,
                NT.DT_EMISSAO DT_RECEBIMENTO,
                P.CD_PESSOA,
                P.NM_PESSOA,
                COALESCE(VEND.NM_PESSOA, 'SEM VENDEDOR') NM_VENDEDOR,
                SUPERVISOR.NM_PESSOA NM_SUPERVISOR,
                'Não Recebidos' TP_RECEBIDO
            FROM NOTA NT
            LEFT JOIN CANHOTONOTAFISCAL CN ON (CN.CD_EMPRESA = NT.CD_EMPRESA
                AND CN.NR_LANCAMENTO = NT.NR_LANCAMENTO
                AND CN.CD_SERIE = NT.CD_SERIE
                AND CN.TP_NOTA = NT.TP_NOTA)
            LEFT JOIN NFSE NS ON (NS.CD_EMPRESA = NT.CD_EMPRESA
                AND NS.NR_LANCAMENTO = NT.NR_LANCAMENTO
                AND NS.CD_SERIE = NT.CD_SERIE
                AND NS.TP_NOTA = NT.TP_NOTA)
            LEFT JOIN CANHOTONOTAFISCAL CNF ON (CNF.CD_EMPRESA = NS.CD_EMPRESA
                AND CNF.NR_LANCAMENTO = NS.NR_LANCAMENTO
                AND CNF.CD_SERIE = NS.CD_SERIE
                AND CNF.TP_NOTA = NS.TP_NOTA)
            LEFT JOIN FORMAPAGTO FP ON (FP.CD_FORMAPAGTO = NT.CD_FORMAPAGTO)
            LEFT JOIN PESSOA P ON (P.CD_PESSOA = NT.CD_PESSOA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = NT.CD_PESSOA
                AND EP.CD_ENDERECO = 1)
            LEFT JOIN RETORNA_VENDEDORNOTA(NT.CD_EMPRESA, NT.NR_LANCAMENTO, NT.TP_NOTA, NT.CD_SERIE) ITNV ON (1 = 1)
            LEFT JOIN VENDEDOR V ON (V.CD_VENDEDOR = COALESCE(ITNV.R_CD_VENDEDOR, EP.CD_VENDEDOR))
            LEFT JOIN PESSOA VEND ON (VEND.CD_PESSOA = V.CD_VENDEDOR)
            LEFT JOIN PESSOA SUPERVISOR ON (SUPERVISOR.CD_PESSOA = V.CD_VENDEDORGERAL)
            WHERE NT.CD_EMPRESA = COALESCE(1, NT.CD_EMPRESA)
                AND NT.NR_NOTAFISCAL IS NOT NULL
                AND CN.DT_RECEBIMENTO IS NULL
                AND CNF.DT_RECEBIMENTO IS NULL
                AND NT.TP_NOTA <> 'E'
                AND NT.ST_NOTA = 'V'
                AND NT.CD_SERIE IN ('F3')
                AND NT.DT_EMISSAO BETWEEN CURRENT_DATE - 240 AND CURRENT_DATE - 5
            GROUP BY NT.NR_NOTAFISCAL,
                NT.CD_SERIE,
                NS.NR_RPS,
                NT.DT_EMISSAO,
                P.CD_PESSOA,
                P.NM_PESSOA,
                VEND.NM_PESSOA,
                SUPERVISOR.NM_PESSOA,
                TP_RECEBIDO
            ORDER BY VEND.NM_PESSOA,
                P.NM_PESSOA,
                NT.NR_NOTAFISCAL
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
