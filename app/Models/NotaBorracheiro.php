<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaBorracheiro extends Model
{
    use HasFactory;

    public function getRequisicaoBorracharia($dados = [], $filtrosExtras = [])
    {
        $query = "
            SELECT
                NOTA.CD_EMPRESA,
                --NOTA.NR_LANCAMENTO,
                --NOTA.CD_SERIE,
                --NOTA.TP_NOTA,
                NOTA.CD_PESSOA,
                NOTA.CD_PESSOA || '-' || PESSOA.NM_PESSOA NM_PESSOA,
                --ITEM.DS_ITEM,
                --ITEM.cd_grupo,
                --ITEM.CD_SUBGRUPO,
                 COUNT(DISTINCT NOTA.NR_LANCAMENTO) QTD_NOTA,
                CAST(REPLACE(SUM(
                    CASE
                    WHEN PB.CD_PESSOA IS NULL THEN I.QT_ITEMNOTA
                    WHEN PB.ST_BORRACHEIRO = 'S' THEN I.QT_ITEMNOTA
                    ELSE 0
                    END), '.00', '') AS INTEGER) QTD_ITEM,
                
                --I.VL_UNITARIO,
                --I.VL_TOTAL,

                INV.CD_VENDEDOR CD_BORRACHEIRO,
                INV.CD_VENDEDOR || '-' || PBORRACHEIRO.NM_PESSOA NM_BORRACHEIRO,
                --INV.CD_TIPO,
                --INV.PC_COMISSAO,
                --NOTA.DT_EMISSAO,                
                
                SUM(
                CASE
                    WHEN PB.CD_PESSOA IS NULL THEN INV.VL_COMISSAO 
                    WHEN PB.ST_BORRACHEIRO = 'S' THEN INV.VL_COMISSAO
                    ELSE 0
                END) VL_COMISSAO,
                PVENDEDOR.CD_PESSOA CD_VENDEDOR,
                PVENDEDOR.NM_PESSOA NM_VENDEDOR,

                PSUPERVISOR.CD_PESSOA CD_SUPERVISOR,
                PSUPERVISOR.NM_PESSOA NM_SUPERVISOR,

                CASE
                    WHEN PB.CD_PESSOA IS NULL THEN 'S'
                    ELSE PB.ST_BORRACHEIRO
                END ST_BORRACHARIA
            FROM NOTA

            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = NOTA.CD_PESSOA)

            LEFT JOIN PESSOABORRACHEIRO PB ON (PB.CD_PESSOA = NOTA.CD_PESSOA)

            INNER JOIN ITEMNOTA I ON (I.CD_EMPRESA = NOTA.CD_EMPRESA
                AND I.NR_LANCAMENTO = NOTA.NR_LANCAMENTO
                AND I.TP_NOTA = NOTA.TP_NOTA
                AND I.CD_SERIE = NOTA.CD_SERIE)

            INNER JOIN ITEM ON (ITEM.CD_ITEM = I.CD_ITEM)

            INNER JOIN ITEMNOTAVENDEDOR INV ON (INV.CD_EMPRESA = I.CD_EMPRESA
                AND INV.NR_LANCAMENTO = I.NR_LANCAMENTO
                AND INV.TP_NOTA = I.TP_NOTA
                AND INV.CD_SERIE = I.CD_SERIE
                AND INV.CD_ITEM = I.CD_ITEM)

            INNER JOIN VENDEDOR VBORRACHEIRO ON (VBORRACHEIRO.CD_VENDEDOR = INV.CD_VENDEDOR)
            INNER JOIN PESSOA PBORRACHEIRO ON (PBORRACHEIRO.CD_PESSOA = VBORRACHEIRO.CD_VENDEDOR)
            INNER JOIN RETORNA_VENDEDORNOTA(NOTA.CD_EMPRESA, NOTA.NR_LANCAMENTO, NOTA.TP_NOTA, NOTA.CD_SERIE) VENDEDORNOTA ON (1 = 1)

            INNER JOIN PESSOA PVENDEDOR ON (PVENDEDOR.CD_PESSOA = VENDEDORNOTA.R_CD_VENDEDOR)

            INNER JOIN VENDEDOR ON (VENDEDOR.CD_VENDEDOR = VENDEDORNOTA.R_CD_VENDEDOR)

            INNER JOIN PESSOA PSUPERVISOR ON (PSUPERVISOR.CD_PESSOA = VENDEDOR.CD_VENDEDORGERAL)

            WHERE NOTA.DT_EMISSAO BETWEEN '{$dados['dtInicio']}' AND '{$dados['dtFim']}'
                AND ITEM.CD_GRUPO IN (102)
                AND INV.CD_TIPO = 2
                AND NOTA.ST_NOTA IN ('V')
                " . (isset($dados['nm_vendedor']) && $dados['nm_vendedor'] != '' ? " AND PVENDEDOR.NM_PESSOA LIKE '%{$dados['nm_vendedor']}%' " : "") . "
                " . (isset($dados['nm_borracheiro']) && $dados['nm_borracheiro'] != '' ? " AND PBORRACHEIRO.NM_PESSOA LIKE '%{$dados['nm_borracheiro']}%' " : "") . "
                " . (isset($dados['nm_supervisor']) && $dados['nm_supervisor'] != '' ? " AND PSUPERVISOR.NM_PESSOA LIKE '%{$dados['nm_supervisor']}%' " : "") . "
                " . (isset($dados['nm_pessoa']) && $dados['nm_pessoa'] != '' ? " AND PESSOA.CD_PESSOA||'-'||PESSOA.NM_PESSOA LIKE '%{$dados['nm_pessoa']}%' " : "") . " 
                
                --Busca por região comercial (gerente comercial)
                " . (!empty($filtrosExtras['cd_regiao']) ? "AND VENDEDOR.CD_VENDEDORGERAL IN ({$filtrosExtras['cd_regiao']})" : "") . "

                --AND VBORRACHEIRO.CD_VENDEDOR IN (90,70127)

            GROUP BY NOTA.CD_EMPRESA, NOTA.CD_PESSOA, PESSOA.NM_PESSOA, INV.CD_VENDEDOR, PBORRACHEIRO.NM_PESSOA, PVENDEDOR.CD_PESSOA, PVENDEDOR.NM_PESSOA, PSUPERVISOR.CD_PESSOA,
                PSUPERVISOR.NM_PESSOA, PB.CD_PESSOA, PB.ST_BORRACHEIRO   
            ORDER BY CAST(REPLACE(SUM(CASE
                WHEN PB.CD_PESSOA IS NULL THEN I.QT_ITEMNOTA
                WHEN PB.ST_BORRACHEIRO = 'S' THEN I.QT_ITEMNOTA
                ELSE 0
                END), '.00', '') AS INTEGER) DESC
            ";

        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }

    public function getDetailsRequisicaoBorracharia($cd_pessoa, $cd_borracheiro, $dtInicio, $dtFim)
    {
        $query = "
            SELECT
                NOTA.CD_EMPRESA,
                NOTA.NR_LANCAMENTO,
                NOTA.nr_notafiscal,
                --NOTA.CD_SERIE,
                --NOTA.TP_NOTA,
                NOTA.CD_PESSOA,
                NOTA.CD_PESSOA || '-' || PESSOA.NM_PESSOA NM_CLIENTE,
                ITEM.DS_ITEM,
                ITEM.cd_grupo,
                ITEM.CD_SUBGRUPO,               
                REPLACE(I.QT_ITEMNOTA, '.00', '') QTD_ITEM,
                CAST(I.VL_UNITARIO AS DECIMAL(15,2)) VL_UNITARIO,
                --I.VL_TOTAL,

                INV.CD_VENDEDOR CD_BORRACHEIRO,
                INV.CD_VENDEDOR || '-' || PBORRACHEIRO.NM_PESSOA NM_BORRACHEIRO,
                --INV.CD_TIPO,
                --INV.PC_COMISSAO,
                --NOTA.DT_EMISSAO,
                INV.VL_COMISSAO,

                PVENDEDOR.CD_PESSOA CD_VENDEDOR,
                PVENDEDOR.NM_PESSOA NM_VENDEDOR,

                PSUPERVISOR.CD_PESSOA CD_SUPERVISOR,
                PSUPERVISOR.NM_PESSOA NM_SUPERVISOR
            FROM NOTA

            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = NOTA.CD_PESSOA)

            LEFT JOIN PESSOABORRACHEIRO PB ON (PB.CD_PESSOA = NOTA.CD_PESSOA)

            INNER JOIN ITEMNOTA I ON (I.CD_EMPRESA = NOTA.CD_EMPRESA
                AND I.NR_LANCAMENTO = NOTA.NR_LANCAMENTO
                AND I.TP_NOTA = NOTA.TP_NOTA
                AND I.CD_SERIE = NOTA.CD_SERIE)

            INNER JOIN ITEM ON (ITEM.CD_ITEM = I.CD_ITEM)

            INNER JOIN ITEMNOTAVENDEDOR INV ON (INV.CD_EMPRESA = I.CD_EMPRESA
                AND INV.NR_LANCAMENTO = I.NR_LANCAMENTO
                AND INV.TP_NOTA = I.TP_NOTA
                AND INV.CD_SERIE = I.CD_SERIE
                AND INV.CD_ITEM = I.CD_ITEM)

            INNER JOIN VENDEDOR VBORRACHEIRO ON (VBORRACHEIRO.CD_VENDEDOR = INV.CD_VENDEDOR)
            INNER JOIN PESSOA PBORRACHEIRO ON (PBORRACHEIRO.CD_PESSOA = VBORRACHEIRO.CD_VENDEDOR)
            INNER JOIN RETORNA_VENDEDORNOTA(NOTA.CD_EMPRESA, NOTA.NR_LANCAMENTO, NOTA.TP_NOTA, NOTA.CD_SERIE) VENDEDORNOTA ON (1 = 1)

            INNER JOIN PESSOA PVENDEDOR ON (PVENDEDOR.CD_PESSOA = VENDEDORNOTA.R_CD_VENDEDOR)

            INNER JOIN VENDEDOR ON (VENDEDOR.CD_VENDEDOR = VENDEDORNOTA.R_CD_VENDEDOR)

            INNER JOIN PESSOA PSUPERVISOR ON (PSUPERVISOR.CD_PESSOA = VENDEDOR.CD_VENDEDORGERAL)

            WHERE NOTA.DT_EMISSAO BETWEEN '$dtInicio' AND '$dtFim'
                AND ITEM.CD_GRUPO IN (102)
                AND INV.CD_TIPO = 2
                AND NOTA.ST_NOTA IN ('V')

                AND VBORRACHEIRO.CD_VENDEDOR = $cd_borracheiro
                AND NOTA.CD_PESSOA = $cd_pessoa
 
            ";

        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }

    public function desabilitaClienteBorracharia($cd_pessoa, $st_borracheiro)
    {
        $user = auth()->user();

        try {
            if ($st_borracheiro === 'S') {

                $queryInsert = "
                    UPDATE OR INSERT INTO PESSOABORRACHEIRO (CD_PESSOA, DT_REGISTRO, ST_BORRACHEIRO, NM_USUARIO)
                    VALUES ($cd_pessoa, CURRENT_DATE, 'S', '{$user->name}')
                    MATCHING (CD_PESSOA);
                ";

                DB::connection('firebird')->insert($queryInsert);

                return response()->json(
                    [
                        'success' => true,
                        'title' => 'Cliente habilitado',
                        'message' => 'Cliente habilitado para pagar borracharia!'
                    ]
                );
            } else {

                $queryUpdate = "
                    UPDATE OR INSERT INTO PESSOABORRACHEIRO (CD_PESSOA, DT_REGISTRO, ST_BORRACHEIRO, NM_USUARIO)
                    VALUES ($cd_pessoa, CURRENT_DATE, 'N', '{$user->name}')
                    MATCHING (CD_PESSOA);
                ";

                DB::connection('firebird')->update($queryUpdate);

                return response()->json(
                    [
                        'success' => true,
                        'title' => 'Cliente desabilitado',
                        'message' => 'Cliente desabilitado para pagar borracharia!'
                    ]
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'title' => 'Erro ao atualizar cliente',
                    'type' => 'error',
                    'message' => $e->getMessage()
                ]
            );
        }
    }

    public function getClienteDesabilitadoBorracharia()
    {
        $query = "
           SELECT
                PB.CD_PESSOA,
                PB.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                P.NR_CNPJCPF,
                V.CD_VENDEDOR || '-' || PV.NM_PESSOA NM_VENDEDOR,
                PB.ST_BORRACHEIRO ST_BORRACHARIA
            FROM PESSOABORRACHEIRO PB
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PB.CD_PESSOA)
            INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            INNER JOIN VENDEDOR V ON (V.CD_VENDEDOR = EP.CD_VENDEDOR)
            LEFT JOIN PESSOA PV ON (P.CD_PESSOA = PV.CD_PESSOA)
            WHERE PB.ST_BORRACHEIRO = 'N'        
        ";

        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }
}
