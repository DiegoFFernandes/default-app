<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cobranca extends Model
{
    use HasFactory;

    public function AreaRegiaoInadimplentes($cd_regiao, $cd_area)
    {
        $query = "
            SELECT
                COALESCE(RC.CD_REGIAOCOMERCIAL, 99) CD_REGIAOCOMERCIAL,
                COALESCE(RC.DS_REGIAOCOMERCIAL, 'SEM REGIAO') DS_REGIAOCOMERCIAL,
                COALESCE(AC.CD_AREACOMERCIAL, 99) CD_AREACOMERCIAL,
                COALESCE(AC.DS_AREACOMERCIAL, 'SEM AREA') DS_AREACOMERCIAL,
                CAST(SUM(C.VL_SALDO) AS NUMERIC(15,2)) AS VL_SALDO
            FROM CONTAS C
            INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
            INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = C.CD_TIPOCONTA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
            LEFT JOIN AREACOMERCIAL AC ON (AC.CD_AREACOMERCIAL = RC.CD_AREACOMERCIAL)
            WHERE
                C.ST_CONTAS IN ('P', 'T')
                AND C.CD_TIPOCONTA IN (2, 10)
                --and c.cd_pessoa = 4
                AND C.CD_FORMAPAGTO IN ('BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL')
            GROUP BY RC.CD_REGIAOCOMERCIAL,
                RC.DS_REGIAOCOMERCIAL,
                AC.CD_AREACOMERCIAL,
                AC.DS_AREACOMERCIAL
            ORDER BY RC.DS_REGIAOCOMERCIAL DESC";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }

    public function clientesInadiplentes($cd_regiao)
    {
        $query =  "
            SELECT
                C.CD_EMPRESA,
                C.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                P.NR_CNPJCPF,
                C.CD_TIPOCONTA || ' ' || TC.DS_TIPOCONTA TIPOCONTA,
                --C.NR_DOCUMENTO || '-' || C.NR_PARCELA || '/' || M.O_NR_MAIORPARCELA NR_DOCUMENTO,
                CAST(SUM(C.VL_SALDO) AS NUMERIC(15,2)) AS VL_SALDO,
                --C.ST_CONTAS,
                COALESCE(RC.CD_REGIAOCOMERCIAL, 99) CD_REGIAOCOMERCIAL,
                COALESCE(RC.DS_REGIAOCOMERCIAL, 'SEM REGIAO') DS_REGIAOCOMERCIAL,
                COALESCE(AC.CD_AREACOMERCIAL, 99) CD_AREACOMERCIAL,
                COALESCE(AC.DS_AREACOMERCIAL, 'SEM AREA') DS_AREACOMERCIAL
            FROM CONTAS C
            INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
            INNER JOIN TIPOCONTA TC ON (TC.CD_TIPOCONTA = C.CD_TIPOCONTA)
                --INNER JOIN RETORNA_MAIORPARCELACONTAS(C.CD_EMPRESA, C.NR_LANCAMENTO, C.CD_PESSOA, C.CD_TIPOCONTA) M ON (1 = 1)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_REGIAOCOMERCIAL = EP.CD_REGIAOCOMERCIAL)
            LEFT JOIN AREACOMERCIAL AC ON (AC.CD_AREACOMERCIAL = RC.CD_AREACOMERCIAL)
            WHERE
                 C.ST_CONTAS IN ('P', 'T')
                AND C.CD_TIPOCONTA IN (2, 10)
                and RC.CD_REGIAOCOMERCIAL = $cd_regiao
                AND C.CD_FORMAPAGTO IN ('BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL')
            GROUP BY C.CD_EMPRESA, C.CD_PESSOA, P.NM_PESSOA, P.NR_CNPJCPF, C.CD_TIPOCONTA, TC.DS_TIPOCONTA, RC.CD_REGIAOCOMERCIAL, RC.DS_REGIAOCOMERCIAL, AC.CD_AREACOMERCIAL,
                AC.DS_AREACOMERCIAL
            ORDER BY RC.DS_REGIAOCOMERCIAL DESC    
                ";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);
    }
}
