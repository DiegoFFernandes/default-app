<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Cobranca extends Model
{
    use HasFactory;

    public function AreaRegiaoInadimplentes($cd_regiao)
    {
        $query = "          
                SELECT DISTINCT
                    CONTAS.CD_EMPRESA,
                    CONTAS.NR_LANCAMENTO,
                    P.NR_CNPJCPF,
                    CONTAS.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                    --CONTAS.DS_OBSERVACAO,
                    CONTAS.CD_TIPOCONTA || ' ' || TIPOCONTA.DS_TIPOCONTA TIPOCONTA,
                    CONTAS.CD_SERIE,
                    CONTAS.ST_CONTAS,
                    CASE CONTAS.ST_CONTAS
                    WHEN 'L' THEN 0
                    ELSE CASE
                            WHEN (CURRENT_DATE - CONTAS.DT_VENCIMENTO) < 0 THEN 0
                            ELSE (CURRENT_DATE - CONTAS.DT_VENCIMENTO)
                        END
                    END NR_DIAS,
                    --CONTAS.TP_DOCUMENTO,
                    CONTAS.NR_DOCUMENTO || ' - ' || CONTAS.NR_PARCELA || '/' || RMAX.O_NR_MAIORPARCELA NR_DOCUMENTO,
                    CONTAS.DT_LANCAMENTO,
                    CONTAS.DT_VENCIMENTO,
                    ME.O_DS_ABREVIACAOMES || '/' || EXTRACT(YEAR FROM CONTAS.DT_VENCIMENTO) MES,
                    CONTAS.VL_DOCUMENTO,
                    CONTAS.VL_SALDO,
                    CJ.O_VL_JURO VL_JUROS,

                    (COALESCE(CONTAS.VL_SALDO, 0) + COALESCE(CJ.O_VL_JURO, 0)) VL_TOTAL,
                    (COALESCE(CONTAS.VL_DOCUMENTO, 0) - COALESCE(CONTAS.VL_SALDO, 0) + COALESCE(MP.O_VL_JURO, 0) - COALESCE(MP.O_VL_DESCONTO, 0)) VL_LIQUIDO,

                    IIF(CONTAS.ST_CARTORIO = 'J' AND CONTAS.ST_INCOBRAVEL = 'N', CONTAS.VL_SALDO, 0) VL_JURIDICO,
                    IIF(CONTAS.ST_CARTORIO = 'C' AND CONTAS.ST_INCOBRAVEL = 'N', CONTAS.VL_SALDO, 0) VL_CARTORIO,
                    IIF(CONTAS.ST_CARTORIO = 'S' AND CONTAS.ST_INCOBRAVEL = 'N', CONTAS.VL_SALDO, 0) VL_PROTESTADO,
                    IIF(CONTAS.ST_CARTORIO = 'N' AND CONTAS.DT_VENCIMENTO <= CURRENT_DATE AND CONTAS.ST_INCOBRAVEL <> 'S', CONTAS.VL_SALDO, 0) VL_VENCIDO,
                    IIF(CONTAS.ST_CARTORIO = 'N' AND CONTAS.DT_VENCIMENTO > CURRENT_DATE AND CONTAS.ST_INCOBRAVEL <> 'S', CONTAS.VL_SALDO, 0) VL_AVENCER,
                    PV.NM_PESSOA NM_VENDEDOR,
                    (COALESCE(CONTAS.VL_SALDO, 0) + COALESCE(CJ.O_VL_JURO, 0)) VL_TOTALSOMA,
                    (COALESCE(CONTAS.VL_DOCUMENTO, 0) - COALESCE(CONTAS.VL_SALDO, 0) + COALESCE(MP.O_VL_JURO, 0) - COALESCE(MP.O_VL_DESCONTO, 0)) VL_LIQUIDOSOMA,

                    COALESCE(RGC.CD_REGIAOCOMERCIAL, 99) CD_REGIAOCOMERCIAL,
                    COALESCE(RGC.DS_REGIAOCOMERCIAL, 'SEM REGIAO') DS_REGIAOCOMERCIAL,

                    COALESCE(AC.CD_AREACOMERCIAL, 99) CD_AREACOMERCIAL,
                    COALESCE(AC.DS_AREACOMERCIAL, 'SEM AREA') DS_AREACOMERCIAL
                FROM CONTAS
                INNER JOIN MES_EXTENSO(CONTAS.DT_VENCIMENTO) ME ON (1 = 1)
                INNER JOIN RETORNA_MAIORPARCELACONTAS(CONTAS.CD_EMPRESA, CONTAS.NR_LANCAMENTO, CONTAS.CD_PESSOA, CONTAS.CD_TIPOCONTA) RMAX ON (1 = 1)
                INNER JOIN PESSOA P ON (P.CD_PESSOA = CONTAS.CD_PESSOA)
                INNER JOIN TIPOCONTA ON (TIPOCONTA.CD_TIPOCONTA = CONTAS.CD_TIPOCONTA)
                LEFT JOIN RETORNA_JURODESCONTOPAGO(CONTAS.CD_EMPRESA, CONTAS.NR_LANCAMENTO, CONTAS.CD_PESSOA, CONTAS.CD_TIPOCONTA, CONTAS.NR_PARCELA) MP ON (1 = 1)
                LEFT JOIN PESSOA PV ON (PV.CD_PESSOA = CONTAS.CD_VENDEDOR)
                LEFT JOIN NOTA NT ON (NT.CD_EMPRESA = CONTAS.CD_EMPRESA AND
                    NT.NR_LANCAMENTO = CONTAS.NR_LANCTONOTA AND
                    NT.TP_NOTA = CONTAS.TP_CONTAS AND
                    NT.CD_SERIE = CONTAS.CD_SERIE)
                LEFT JOIN ITEMNOTA ITN ON (ITN.CD_EMPRESA = NT.CD_EMPRESA AND
                    ITN.NR_LANCAMENTO = NT.NR_LANCAMENTO AND
                    ITN.TP_NOTA = NT.TP_NOTA AND
                    ITN.CD_SERIE = NT.CD_SERIE)
                LEFT JOIN ITEMNOTAVENDEDOR ITNV ON (ITNV.CD_EMPRESA = ITN.CD_EMPRESA AND
                    ITNV.NR_LANCAMENTO = ITN.NR_LANCAMENTO AND
                    ITNV.TP_NOTA = ITN.TP_NOTA AND
                    ITNV.CD_SERIE = ITN.CD_SERIE AND
                    ITNV.CD_ITEM = ITN.CD_ITEM)
                LEFT JOIN PESSOA VEND ON (VEND.CD_PESSOA = COALESCE(ITNV.CD_VENDEDOR, CONTAS.CD_VENDEDOR))

                LEFT JOIN TIPOVENDEDOR TVEN ON (TVEN.CD_TIPO = ITNV.CD_TIPO)
                LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = CONTAS.CD_PESSOA AND
                    EP.CD_ENDERECO = 1)

                LEFT JOIN CALCULA_JUROMORA(CONTAS.CD_EMPRESA, CONTAS.NR_LANCAMENTO, CONTAS.CD_PESSOA, CONTAS.CD_TIPOCONTA, CONTAS.NR_PARCELA, CURRENT_DATE, NULL) CJ ON (0 = 0)
                LEFT JOIN REGIAOCOMERCIAL RGC ON (RGC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
                LEFT JOIN AREACOMERCIAL AC ON (AC.CD_AREACOMERCIAL = RGC.CD_AREACOMERCIAL)

                WHERE CONTAS.CD_TIPOCONTA IN (2, 10)
                    AND CONTAS.ST_CONTAS IN ('T', 'P')
                    " . (!empty($cd_regiao) ? "AND RCG.CD_REGIAOCOMERCIAL IN ($cd_regiao)" : "") . "
                    --AND COALESCE(ITNV.CD_VENDEDOR, CONTAS.CD_VENDEDOR) IN (16007, 18404)
                    AND CONTAS.CD_FORMAPAGTO IN ('BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL')   
                ORDER BY CONTAS.DT_VENCIMENTO;          
             ";

        // $data = DB::connection('firebird')->select($query);
        // return Helper::ConvertFormatText($data);

        $key = "contas-1" . Auth::user()->id;

        return Cache::remember($key, now()->addMinutes(2), function () use ($query) {
            $data = DB::connection('firebird')->select($query);
            return Helper::ConvertFormatText($data);
        });
    }

    public function clientesInadiplentes($cd_regiao)
    {
        $query =  "
                SELECT
                    C.CD_PESSOA,
                    C.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                    P.NR_CNPJCPF,
                    C.CD_TIPOCONTA || ' ' || TC.DS_TIPOCONTA TIPOCONTA,
                    C.NR_DOCUMENTO || '-' || C.NR_PARCELA || '/' || M.O_NR_MAIORPARCELA NR_DOCUMENTO,
                    CAST(C.VL_SALDO AS NUMERIC(15,2)) AS VL_SALDO,
                    COUNT(C.NR_DOCUMENTO) TITULOS,
                    C.DT_VENCIMENTO,
                    --C.ST_CONTAS,
                    COALESCE(RC.CD_REGIAOCOMERCIAL, 99) CD_REGIAOCOMERCIAL,
                    COALESCE(RC.DS_REGIAOCOMERCIAL, 'SEM REGIAO') DS_REGIAOCOMERCIAL,
                    COALESCE(AC.CD_AREACOMERCIAL, 99) CD_AREACOMERCIAL,
                    COALESCE(AC.DS_AREACOMERCIAL, 'SEM AREA') DS_AREACOMERCIAL
                FROM CONTAS C
                INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
                INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = C.CD_TIPOCONTA)
                INNER JOIN RETORNA_MAIORPARCELACONTAS(C.CD_EMPRESA, C.NR_LANCAMENTO, C.CD_PESSOA, C.CD_TIPOCONTA) M ON (1 = 1)
                LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA  
                AND EP.CD_ENDERECO = 1)
                LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
                LEFT JOIN AREACOMERCIAL AC ON (AC.CD_AREACOMERCIAL = RC.CD_AREACOMERCIAL)
                WHERE
                    C.ST_CONTAS IN ('P', 'T')
                    AND C.DT_VENCIMENTO <= CURRENT_DATE-2
                    AND C.CD_TIPOCONTA IN (2, 10)
                    " . (!empty($cd_regiao) ? "AND RC.CD_REGIAOCOMERCIAL IN ($cd_regiao)" : "") . "
                    AND C.CD_FORMAPAGTO IN ('BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL')
                GROUP BY C.CD_PESSOA,
                    P.NM_PESSOA,
                    P.NR_CNPJCPF,
                    C.CD_TIPOCONTA,
                    TC.DS_TIPOCONTA,
                    VL_SALDO,
                    C.DT_VENCIMENTO,
                    C.NR_DOCUMENTO,
                    C.NR_PARCELA,
                    M.O_NR_MAIORPARCELA,
                    RC.CD_REGIAOCOMERCIAL,
                    RC.DS_REGIAOCOMERCIAL,
                    AC.CD_AREACOMERCIAL,
                    AC.DS_AREACOMERCIAL
                ORDER BY VL_SALDO DESC";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }

    public function clientesInadiplentesDetails($cd_pessoa)
    {
        $query =  "
            SELECT
                C.CD_EMPRESA,
                C.CD_PESSOA,
                C.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                P.NR_CNPJCPF,
                C.DT_VENCIMENTO,
                C.CD_TIPOCONTA || ' ' || TC.DS_TIPOCONTA TIPOCONTA,
                C.NR_DOCUMENTO || '-' || C.NR_PARCELA || '/' || M.O_NR_MAIORPARCELA NR_DOCUMENTO,
                CAST(C.VL_SALDO AS NUMERIC(15,2)) VL_SALDO,
                C.ST_CONTAS,
                   C.DS_OBSERVACAO
            FROM CONTAS C
            INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
            INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = C.CD_TIPOCONTA)
            INNER JOIN RETORNA_MAIORPARCELACONTAS(C.CD_EMPRESA, C.NR_LANCAMENTO, C.CD_PESSOA, C.CD_TIPOCONTA) M ON (1 = 1)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            WHERE
                C.ST_CONTAS IN ('P', 'T')
                AND C.DT_VENCIMENTO <= CURRENT_DATE-2
                AND C.CD_TIPOCONTA IN (2, 10)
                AND C.CD_PESSOA = $cd_pessoa
                AND C.CD_FORMAPAGTO IN ('BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL')
            ORDER BY C.VL_SALDO DESC     
                ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
