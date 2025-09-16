<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LimiteCredito extends Model
{
    use HasFactory;

    public function getLimiteCredito($cd_empresa, $cd_regiao)
    {
        $query = "
            SELECT
                N.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                SUM(CONTAS.VL_DOCUMENTO) VL_NOTA,
                SUM(CONTAS.VL_SALDO) VL_USADO,
                COALESCE(SUM(DISTINCT CREDITO.VL_CREDITOACUM),0) VL_CREDITO,
                COALESCE(SUM(DISTINCT CREDITO.VL_CREDITOACUM),0) - SUM(CONTAS.VL_SALDO) DISPONIVEL
            FROM NOTA N
            LEFT JOIN PESSOA P ON (P.CD_PESSOA = N.CD_PESSOA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = N.CD_PESSOA
                AND EP.CD_ENDERECO = 1)
            LEFT JOIN VENDEDOR V ON (V.CD_VENDEDOR = EP.CD_VENDEDOR)    
            LEFT JOIN CONTAS ON (N.CD_EMPRESA = CONTAS.CD_EMPRESA
                AND N.NR_LANCAMENTO = CONTAS.NR_LANCTONOTA
                AND N.TP_NOTA = CONTAS.TP_CONTAS
                AND N.CD_SERIE = CONTAS.CD_SERIE)
            LEFT JOIN CREDITO ON (CREDITO.CD_PESSOA = N.CD_PESSOA
                AND CREDITO.CD_EMPRESA = N.CD_EMPRESA)
            WHERE N.CD_SERIE = 'F3'
                AND N.ST_NOTA = 'V'                
                AND N.DT_EMISSAO >= CURRENT_DATE - 180
                " . (!empty($cd_regiao) ? "AND V.CD_VENDEDORGERAL IN ($cd_regiao)" : "") . "
            GROUP BY N.CD_PESSOA, P.NM_PESSOA;
        ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
    public function getPrazoMedio($cd_empresa, $cd_regiao){

        $query = "
            SELECT
                N.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                --CONTAS.DT_LANCAMENTO,
                --CONTAS.DT_VENCIMENTO,
                AVG(CAST((CONTAS.DT_VENCIMENTO - CONTAS.DT_LANCAMENTO) AS INTEGER)) AS PRAZO_MEDIO,
                --N.CD_CONDPAGTO,
                --N.NR_LANCAMENTO,
                --N.NR_NOTAFISCAL,
                SUPERVISOR.NM_PESSOA NM_SUPERVISOR,
                VEND.NM_PESSOA NM_VENDEDOR,
                V.CD_VENDEDORGERAL
            FROM NOTA N
            LEFT JOIN PESSOA P ON (P.CD_PESSOA = N.CD_PESSOA)
            LEFT JOIN CONTAS ON (N.CD_EMPRESA = CONTAS.CD_EMPRESA
                AND N.NR_LANCAMENTO = CONTAS.NR_LANCTONOTA
                AND N.TP_NOTA = CONTAS.TP_CONTAS
                AND N.CD_SERIE = CONTAS.CD_SERIE)
            LEFT JOIN RETORNA_VENDEDORNOTA(N.CD_EMPRESA, N.NR_LANCAMENTO, N.TP_NOTA, N.CD_SERIE) ITNV ON (1 = 1)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = CONTAS.CD_PESSOA
                AND EP.CD_ENDERECO = 1)
            LEFT JOIN VENDEDOR V ON (V.CD_VENDEDOR = COALESCE(COALESCE(ITNV.R_CD_VENDEDOR, CONTAS.CD_VENDEDOR), EP.CD_VENDEDOR))
            LEFT JOIN PESSOA VEND ON (VEND.CD_PESSOA = V.CD_VENDEDOR)
            LEFT JOIN PESSOA SUPERVISOR ON (SUPERVISOR.CD_PESSOA = V.CD_VENDEDORGERAL)
            WHERE N.CD_SERIE = 'F3'
                AND N.ST_NOTA = 'V'                
                AND N.DT_EMISSAO >= CURRENT_DATE - 180
                " . (!empty($cd_regiao) ? "AND V.CD_VENDEDORGERAL IN ($cd_regiao)" : "") . "
            GROUP BY N.CD_PESSOA,
                P.NM_PESSOA,
                SUPERVISOR.NM_PESSOA,
                VEND.NM_PESSOA,
                V.CD_VENDEDORGERAL
            ORDER BY PRAZO_MEDIO DESC
        ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
