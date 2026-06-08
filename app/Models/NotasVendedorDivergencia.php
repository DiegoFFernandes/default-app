<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotasVendedorDivergencia extends Model
{
    use HasFactory;

    public function getNotasDivergentes()
    {
        $query = "
        SELECT
            NOTA.DT_EMISSAO,    
            NOTA.CD_EMPRESA,            
            NOTA.NR_LANCAMENTO,
            NOTA.NR_NOTAFISCAL,
            --NOTA.NR_NOTAFOR,
            NOTA.CD_PESSOA,
            P.NM_PESSOA,
            ITEM.CD_ITEM,
            ITEM.DS_ITEM,

            --VENDEDOR VEND_NOTA
            COALESCE(NOTA.CD_VENDEDOR, EP.CD_VENDEDOR) CD_VENDEDOR,
            COALESCE(VEND_NOTA.NM_PESSOA, EP.CD_VENDEDOR) NM_VEND_NOTA,

            --VENDEDOR INFORMADO
            INV.CD_VENDEDOR CD_VEND_INV,
            VEND_INV.NM_PESSOA NM_VENDEDOR_INV,

            IIF(INV.CD_VENDEDOR = COALESCE(NOTA.CD_VENDEDOR, EP.CD_VENDEDOR), 'TRUE', 'FALSE') VALID
        FROM ITEMNOTAVENDEDOR INV

        LEFT JOIN PESSOA VEND_INV ON (VEND_INV.CD_PESSOA = INV.CD_VENDEDOR)

        INNER JOIN NOTA ON (NOTA.NR_LANCAMENTO = INV.NR_LANCAMENTO
            AND NOTA.CD_SERIE = INV.CD_SERIE
            AND NOTA.TP_NOTA = INV.TP_NOTA
            AND NOTA.CD_EMPRESA = INV.CD_EMPRESA)

        LEFT JOIN PESSOA VEND_NOTA ON (VEND_NOTA.CD_PESSOA = NOTA.CD_VENDEDOR)

        INNER JOIN ITEMNOTA I ON (I.NR_LANCAMENTO = INV.NR_LANCAMENTO
            AND I.CD_SERIE = INV.CD_SERIE
            AND I.TP_NOTA = INV.TP_NOTA
            AND I.CD_EMPRESA = INV.CD_EMPRESA
            AND I.CD_ITEM = INV.CD_ITEM)

        INNER JOIN ITEM ON (ITEM.CD_ITEM = INV.CD_ITEM)

        INNER JOIN PESSOA P ON (P.CD_PESSOA = NOTA.CD_PESSOA)
        INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
            AND NOTA.CD_ENDERECO = EP.CD_ENDERECO)

        WHERE NOTA.ST_NOTA NOT IN ('C', 'E', 'B')
            AND NOTA.TP_NOTA = 'S'
            AND NOTA.DT_EMISSAO >= '01.02.2026'
            AND IIF(INV.CD_VENDEDOR = COALESCE(NOTA.CD_VENDEDOR, EP.CD_VENDEDOR), 'TRUE', 'FALSE') = 'FALSE'
            AND I.CD_MOVIMENTACAO NOT IN (75)
            --AND I.CD_EMPRESA = 1 
            ORDER BY NOTA.CD_EMPRESA,            
            NOTA.NR_LANCAMENTO 
            ";

        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }

    public function updateItemVendedorNota(array $notas)
    {

        return DB::transaction(function () use ($notas) {

            $retorno = [];

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            foreach ($notas as $nota) {
                $query = "
                    UPDATE ITEMNOTAVENDEDOR
                    SET CD_VENDEDOR = :cd_vendedor
                    WHERE NR_LANCAMENTO = :nr_lancamento           
                        AND CD_EMPRESA = :cd_empresa
                        
                    RETURNING
                    CD_EMPRESA,
                    NR_LANCAMENTO,                    
                    CD_VENDEDOR    
                ";

                $resultado = DB::connection('firebird')->select($query, [
                    'cd_vendedor' => $nota['CD_VENDEDOR'],
                    'nr_lancamento' => $nota['NR_LANCAMENTO'],
                    'cd_empresa' => $nota['CD_EMPRESA']
                ]);

                if (!empty($resultado)) {
                    $retorno[] = $resultado[0];
                }
            }

            return $retorno;
        });
    }

    public function updateVendedorNota(array $data)
    {
        return DB::transaction(function () use ($data) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                UPDATE NOTA
                SET CD_VENDEDOR = :cd_vendedor
                WHERE NR_LANCAMENTO = :nr_lancamento           
                    AND CD_EMPRESA = :cd_empresa
                RETURNING
                    CD_EMPRESA,
                    NR_LANCAMENTO,
                    CD_VENDEDOR
             ";

            $resultado = DB::connection('firebird')->select($query, [
                'cd_vendedor' => $data['cd_vendedor_novo'],
                'nr_lancamento' => $data['nr_lancamento'],
                'cd_empresa' => $data['cd_empresa']
            ]);

            return !empty($resultado) ? $resultado[0] : null;
        });
    }
}
