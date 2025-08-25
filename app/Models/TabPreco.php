<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TabPreco extends Model
{
    use HasFactory;

    public function getTabpreco()
    {
        $query = "
            SELECT
                T.CD_TABPRECO,
                T.DS_TABPRECO,
                COUNT(I.CD_ITEM) QTD_ITENS,
                COUNT(DISTINCT P.NR_SEQUENCIA) ASSOCIADOS
            FROM TABPRECO T
            INNER JOIN ITEMTABPRECO I ON (I.CD_TABPRECO = T.CD_TABPRECO)
            LEFT JOIN PARMTABPRECO P ON P.CD_TABPRECO = T.CD_TABPRECO
            --WHERE P.CD_TABPRECO = 68
            GROUP BY T.CD_TABPRECO,
                T.DS_TABPRECO
            ORDER BY T.CD_TABPRECO
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getItemTabPreco($cd_tabela)
    {
        $query = "
            SELECT
                I.CD_TABPRECO,
                I.CD_ITEM||' - '||ITEM.DS_ITEM DS_ITEM,
                CAST(I.VL_PRECO AS numeric(12,2)) VL_PRECO
            FROM ITEMTABPRECO I
            INNER JOIN ITEM  ON (ITEM.CD_ITEM = I.CD_ITEM)
            WHERE I.cd_tabpreco = $cd_tabela
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function getTabClientePreco($cd_tabela)
    {
        $query = "
            SELECT
                P.CD_TABPRECO,
                CASE
                WHEN P.CD_PESSOA IS NULL THEN 'GRUPO -' || P.CD_GRUPO || '' || GRUPO.DS_GRUPO
                ELSE P.CD_PESSOA || '-' || PESSOA.NM_PESSOA
                END NM_PESSOA,
                COALESCE(P.CD_VENDEDOR || ' - ' || VP.NM_PESSOA, EPV.CD_PESSOA || ' - ' || EPV.NM_PESSOA) VENDEDOR,
                PV.NM_PESSOA SUPERVISOR
            FROM PARMTABPRECO P
            LEFT JOIN PESSOA ON (PESSOA.CD_PESSOA = P.CD_PESSOA)
            LEFT JOIN ENDERECOPESSOA E ON (E.CD_PESSOA = P.CD_PESSOA
                AND E.CD_ENDERECO = 1)
            LEFT JOIN VENDEDOR V ON (V.CD_VENDEDOR = E.CD_VENDEDOR)
            LEFT JOIN PESSOA PV ON (PV.CD_PESSOA = V.CD_VENDEDORGERAL)
            LEFT JOIN GRUPO ON (GRUPO.CD_GRUPO = P.CD_GRUPO)
            LEFT JOIN PESSOA VP ON (VP.CD_PESSOA = P.CD_VENDEDOR)
            LEFT JOIN PESSOA EPV ON (EPV.CD_PESSOA = E.CD_VENDEDOR)
            WHERE P.CD_TABPRECO = $cd_tabela
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
