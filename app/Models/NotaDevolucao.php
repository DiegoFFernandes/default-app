<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;

class NotaDevolucao extends Model
{
    use HasFactory;

    public function getNotaDevolucao($input = 0)
    {
        $query = "
               SELECT DISTINCT
                    ORIGEM.CD_EMPRESA,
                    CASE 
                        WHEN ORIGEM.CD_EMPRESA = 1 THEN 'Cambe'
                        WHEN ORIGEM.CD_EMPRESA = 2 THEN '2'
                        WHEN ORIGEM.CD_EMPRESA = 3 THEN 'Osvaldo Cruz'
                        WHEN ORIGEM.CD_EMPRESA = 4 THEN '4'
                        WHEN ORIGEM.CD_EMPRESA = 5 THEN 'Ponta Grossa'
                        WHEN ORIGEM.CD_EMPRESA = 6 THEN 'Catanduva'
                        ELSE 'OUTROS'
                    END NM_EMPRESA,
                    P.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                    --D.NR_LANCTOORIG,
                    ORIGEM.DT_EMISSAO,
                    ORIGEM.DT_FORNECEDOR,
                    COALESCE(ORIGEM.NR_NOTAFISCAL, ORIGEM.NR_NOTAFOR) NOTA_ENTRADA,
                    COALESCE(DESTINO.NR_NOTAFISCAL, DESTINO.NR_NOTAFOR) NOTA_SAIDA,
                    --D.TP_NOTAORIG,
                    --D.CD_SERIEORIG,
                    ITEMORIGEM.CD_ITEM || '-' || ITEM.DS_ITEM DS_ITEM,
                    ITEMORIGEM.CD_MOVIMENTACAO || '-' || M.DS_MOVIMENTACAO DS_MOVIMENTACAO,
                    --D.CD_EMPRDEST,
                    --D.NR_LANCTODEST,
                    --D.TP_NOTADEST,
                    --D.CD_SERIEDEST,
                    --D.CD_ITEMDEST,
                    --D.PS_DEVOLUCAO,
                    ITEMORIGEM.QT_ITEMNOTA QT_ENTRADA,
                    COALESCE(D.QT_DEVOLUCAO, 0) QT_SAIDA,
                    (ITEMORIGEM.QT_ITEMNOTA - COALESCE(D.QT_DEVOLUCAO, 0)) SALDO
                    --D.VL_DEVOLUCAO
                    --D.DT_REGISTRO
                FROM NOTA ORIGEM

                LEFT JOIN ITEMNOTA ITEMORIGEM ON (ORIGEM.CD_EMPRESA = ITEMORIGEM.CD_EMPRESA
                    AND ORIGEM.NR_LANCAMENTO = ITEMORIGEM.NR_LANCAMENTO
                    AND ORIGEM.CD_SERIE = ITEMORIGEM.CD_SERIE
                    AND ORIGEM.TP_NOTA = ITEMORIGEM.TP_NOTA

                    )

                LEFT JOIN DEVOLUCAONOTA D ON (D.CD_EMPRORIG = ORIGEM.CD_EMPRESA
                    AND D.NR_LANCTOORIG = ORIGEM.NR_LANCAMENTO
                    AND D.TP_NOTAORIG = ORIGEM.TP_NOTA
                    AND D.CD_SERIEORIG = ORIGEM.CD_SERIE
                    AND ITEMORIGEM.CD_ITEM = D.CD_ITEMDEST)

                LEFT JOIN NOTA DESTINO ON (D.CD_EMPRDEST = DESTINO.CD_EMPRESA
                    AND D.NR_LANCTODEST = DESTINO.NR_LANCAMENTO
                    AND D.TP_NOTADEST = DESTINO.TP_NOTA
                    AND D.CD_SERIEDEST = DESTINO.CD_SERIE

                    )

                LEFT JOIN ITEM ON (ITEM.CD_ITEM = ITEMORIGEM.CD_ITEM)
                LEFT JOIN PESSOA P ON (P.CD_PESSOA = ORIGEM.CD_PESSOA)
                LEFT JOIN MOVIMENTACAO M ON (M.CD_MOVIMENTACAO = ITEMORIGEM.CD_MOVIMENTACAO)
                WHERE ITEMORIGEM.CD_MOVIMENTACAO = 57
                    --AND DESTINO.ST_NOTA NOT IN ('C')
                    --AND ORIGEM.NR_LANCAMENTO = 2405
                    --AND P.CD_PESSOA = 83924
                    AND ORIGEM.ST_NOTA NOT IN ('C')
                    AND ORIGEM.DT_EMISSAO >= '01.03.2025'
                    --AND ORIGEM.CD_EMPRESA = 1
                    -- AND ORIGEM.nr_notafor = 115943
                    AND ITEMORIGEM.QT_ITEMNOTA - COALESCE(D.QT_DEVOLUCAO, 0) > 0
                ORDER BY ORIGEM.DT_EMISSAO";

          $data = DB::connection('firebird')->select($query);   
          
          return Helper::ConvertFormatText($data);
    }
}

