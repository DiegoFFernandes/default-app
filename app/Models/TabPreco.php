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

    public function getSelectTabPreco($select = null, $id_desenho = null, $id_medida = null, $valor = null)
    {
        // SELECT DISTINCT
        //     BP.IDDESENHOPNEU,
        // --REPLACE(DP.DSDESENHO || ' ' || BP.NRLARGURA, '.00', '') DSBANDA,
        //     DP.DSDESENHO
        // --BP.IDITEM,
        // --ITEM.DS_ITEM PRODUTO,
        // --SP.IDMEDIDAPNEU,
        // --MP.DSMEDIDAPNEU,
        // --SERVICO.CD_ITEM CD_ITEM,
        // --SP.DSSERVICO
        // --SERVICO.CD_GRUPO,
        // --SERVICO.CD_SUBGRUPO

        // caso o usuario filtrar por desenho, trazer as medidas associadas disponiveis segundo nivel, senão trazer os desenhos disponiveis primeiro nivel
        $filtro = $select === 'desenho' ? 'SP.IDMEDIDAPNEU as ID, MP.DSMEDIDAPNEU as DESCRICAO' : 'BP.IDDESENHOPNEU as ID, DP.DSDESENHO as DESCRICAO';
        if ($select === 'previa') {
            $filtro = 'SERVICO.CD_ITEM as ID, SP.DSSERVICO as DESCRICAO, CAST(' . ($valor ? $valor : 0) . ' AS numeric(12,2)) as VALOR';
        }

        $query = "
            SELECT DISTINCT
            $filtro
            FROM BANDAPNEU BP
            INNER JOIN DESENHOPNEU DP ON (DP.ID = BP.IDDESENHOPNEU)
            INNER JOIN ITEM ON (ITEM.CD_ITEM = BP.IDITEM)
            INNER JOIN SERVICOPNEU SP ON (SP.IDBANDAPNEU = BP.ID)
            INNER JOIN MEDIDAPNEU MP ON (MP.ID = SP.IDMEDIDAPNEU)
            INNER JOIN ITEM SERVICO ON (SERVICO.CD_ITEM = SP.ID)
            WHERE BP.STATIVO = 'S'
                AND SERVICO.ST_ATIVO = 'S'
                " . ($id_desenho ? " AND BP.IDDESENHOPNEU IN ($id_desenho) " : "") . "
                " . ($id_medida ? " AND SP.IDMEDIDAPNEU IN ($id_medida) " : "") . "
                AND SERVICO.CD_SUBGRUPO IN (1021, 1022, 1023, 1024, 1026, 1027, 1029)
            ORDER BY DESCRICAO
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
