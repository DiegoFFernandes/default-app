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
                COUNT(DISTINCT I.CD_ITEM) QTD_ITENS,
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

    // lista as tabelas cadastradas na tabela temporária para importação
    public function getTabprecoPreview($st_importa = 'N', $cd_regiao = '', $cd_tabela = null)
    {
        $query = "
            SELECT DISTINCT
                I.CD_TABPRECO,
                PESSOA.NM_PESSOA DS_TABPRECO,
                COUNT(I.CD_ITEM) QTD_ITENS,
                I.ST_IMPORTA,
                COALESCE(SUPERVISOR.NM_PESSOA, 'SEM SUPERVISOR') SUPERVISOR,
                CAST(I.DT_REGISTRO AS DATE) DT_REGISTRO
            FROM ITEMTABPRECO_PREVIEW I
            INNER JOIN ITEM ON (ITEM.CD_ITEM = I.CD_ITEM)
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = I.CD_TABPRECO)
            INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PESSOA.CD_PESSOA
                AND EP.CD_ENDERECO = 1)
            LEFT JOIN VENDEDOR V ON (V.CD_VENDEDOR = EP.CD_VENDEDOR)
            LEFT JOIN PESSOA SUPERVISOR ON (SUPERVISOR.CD_PESSOA = V.CD_VENDEDORGERAL)
            WHERE I.ST_IMPORTA IN ('N', 'V')                
                " . (!empty($cd_regiao) ? " AND V.CD_VENDEDORGERAL IN ({$cd_regiao}) " : "") . "
                " . (!empty($cd_tabela) ? " AND I.CD_TABPRECO = {$cd_tabela} " : "") . "
            GROUP BY I.CD_TABPRECO,
                PESSOA.NM_PESSOA,
                I.ST_IMPORTA,
                SUPERVISOR.NM_PESSOA,
                CAST(I.DT_REGISTRO AS DATE)
            ORDER BY CAST(I.DT_REGISTRO AS DATE) DESC
        ";

        $data = DB::connection('firebird')->select($query);

        return $data;
    }

    public function getItemTabPreco($cd_tabela, $tela = 'tabela_preco')
    {
        if ($tela == 'tabela_preco') {
            $table = 'ITEMTABPRECO';
        } else {
            $table = 'ITEMTABPRECO_PREVIEW';
        }
        $query = "
            SELECT
                I.CD_TABPRECO CD_TABELA,
                I.CD_ITEM ID,
                ITEM.DS_ITEM DESCRICAO,
                CAST(I.VL_PRECO AS numeric(12,2)) VALOR
            FROM $table I
            --INNER JOIN TABPRECO T ON (T.CD_TABPRECO = I.CD_TABPRECO)
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
                P.CD_PESSOA,
                CASE
                WHEN P.CD_PESSOA IS NULL THEN 'GRUPO - ' || P.CD_GRUPO || '' || GRUPO.DS_GRUPO
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

    public function getSelectTabPreco($select = null, $id_pessoa = null, $id_desenho = null, $id_medida = null, $valor = null)
    {
        // caso o usuario filtrar por desenho, trazer as medidas associadas disponiveis segundo nivel, senão trazer os desenhos disponiveis primeiro nivel
        $filtro = $select === 'desenho' ? 'SP.IDMEDIDAPNEU as ID, MP.DSMEDIDAPNEU as DESCRICAO' : 'BP.IDDESENHOPNEU as ID, DP.DSDESENHO as DESCRICAO';

        if ($select === 'previa') {
            $filtro = $id_pessoa . ' as CD_TABELA, SERVICO.CD_ITEM as ID, SP.DSSERVICO as DESCRICAO, CAST(' . ($valor ? $valor : 0) . ' as numeric(12,2)) as VALOR';
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
            ORDER BY DESCRICAO";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function getVulcanizacaoManchao($input)
    {
        $query = "
                SELECT
                    $input[pessoa] AS CD_TABELA,
                    SP.ID,
                    SP.DSSERVICO AS DESCRICAO,
                    --I.CD_GRUPO,
                    --I.CD_SUBGRUPO,
                    CASE
                --VULCANIZACAO CARGA
                    WHEN I.CD_SUBGRUPO = 10026 THEN $input[vlr_vulc_carga]                    
                -- VULCANIZACAO AGRICOLA
                    WHEN I.CD_SUBGRUPO = 122 THEN $input[vlr_vulc_agricola]                   
                    END VALOR
                FROM SERVICOPNEU SP
                INNER JOIN ITEM I ON (I.CD_ITEM = SP.ID)
                WHERE I.ST_ATIVO = 'S'
                    AND I.CD_SUBGRUPO IN (10026, 122)
                    AND CASE
                            WHEN I.CD_SUBGRUPO = 10026 THEN $input[vlr_vulc_carga] 
                            WHEN I.CD_SUBGRUPO = 122 THEN $input[vlr_vulc_agricola]                            
                    END > 0

                UNION ALL

                SELECT
                    $input[pessoa] AS CD_TABELA,
                    CP.ID,
                    CP.DSCONSERTO AS DESCRICAO,
                    --I.CD_GRUPO,
                    --I.CD_SUBGRUPO,
                    CASE
                --CONSERTO CARGA
                    WHEN I.CD_SUBGRUPO = 10037 THEN $input[vlr_manchao]                    
                -- CONSERTO AGRO
                    WHEN I.CD_SUBGRUPO = 123 THEN $input[vlr_manchao_agricola]
                    ELSE 0
                    END VALOR
                FROM CONSERTOPNEU CP
                INNER JOIN ITEM I ON (I.CD_ITEM = CP.ID)
                WHERE I.ST_ATIVO = 'S'
                    AND I.CD_SUBGRUPO IN (10037, 123)
                    --AND CASE
                            --WHEN I.CD_SUBGRUPO = 10037 THEN $input[vlr_manchao] 
                            --WHEN I.CD_SUBGRUPO = 123 THEN $input[vlr_manchao_agricola]                            
                    --END > 0";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public  function importarTabelaPreco($tabela, $itensTabela)
    {
        return DB::transaction(function () use ($tabela, $itensTabela) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "UPDATE OR INSERT INTO TABPRECO (CD_TABPRECO, DS_TABPRECO, DT_REGISTRO, ST_SINCRONIZACAO, ST_CALCULAJUROS)
                  VALUES ($tabela->CD_TABPRECO, '$tabela->DS_TABPRECO', CURRENT_TIMESTAMP, 'S', 'S')
                  MATCHING (CD_TABPRECO);";

            $resultImportTabela = DB::connection('firebird')->statement($query);

            if ($resultImportTabela) {
                foreach ($itensTabela as $item) {
                    $queryItem = "
                        UPDATE OR INSERT INTO ITEMTABPRECO (CD_TABPRECO, CD_ITEM, VL_PRECO, DT_REGISTRO, ST_CALCACRESCIMO, ST_CALCFLEX)
                        VALUES ($item->CD_TABELA, $item->ID, $item->VALOR, CURRENT_TIMESTAMP, 'S', 'S')
                        MATCHING (CD_TABPRECO, CD_ITEM)
                    ";
                    DB::connection('firebird')->statement($queryItem);
                }

                // Após importar os itens, atualizar o status de importação na tabela temporária, para 'V' (VINCULAR CLIENTE)
                $updateStatus = "UPDATE ITEMTABPRECO_PREVIEW SET ST_IMPORTA = 'V' WHERE CD_TABPRECO = $tabela->CD_TABPRECO";
                $statusDB = DB::connection('firebird')->statement($updateStatus);

                return response()->json(['success' => true, 'message' => 'Tabela e itens importados com sucesso, faça o vínculo com o(s) cliente(s) para iniciar seu uso!']);
            }
        });
    }

    public function vincularTabelaPreco($cd_tabela, $cd_pessoa)
    {

        $nr_sequencia = $this->retornaUltimoID();
        return DB::transaction(function () use ($cd_tabela, $cd_pessoa, $nr_sequencia) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            foreach ($cd_pessoa as $pessoa) {

                $query = "
                    UPDATE OR INSERT INTO PARMTABPRECO (NR_SEQUENCIA, CD_PESSOA, CD_TABPRECO, DT_REGISTRO)
                    VALUES ($nr_sequencia, $pessoa, $cd_tabela, CURRENT_TIMESTAMP)
                    MATCHING (CD_TABPRECO, CD_PESSOA)
                ";
                DB::connection('firebird')->statement($query);

                $nr_sequencia++;
            }

            $this->alterSequencia($nr_sequencia + 1);

            // Após importar os itens, atualizar o status de importação na tabela temporária, para 'S' (SUCESSO)
            $updateStatus = "UPDATE ITEMTABPRECO_PREVIEW SET ST_IMPORTA = 'S' WHERE CD_TABPRECO = $cd_tabela";
            $statusDB = DB::connection('firebird')->statement($updateStatus);

            return response()->json(['success' => true, 'message' => 'Tabela vinculada com sucesso ao(s) cliente(s)!']);
        });
    }

    public function retornaUltimoID()
    {
        $query = "
            SELECT FIRST 1
                E.NR_SEQUENCIA AS ID
            FROM PARMTABPRECO E
            ORDER BY E.NR_SEQUENCIA DESC";

        $data = DB::connection('firebird')->select($query);

        $id = Helper::ConvertFormatText($data)[0]->ID ?? null;
        //retorna o próximo ID baseado no último ID encontrado
        //se não houver ID, retorna 1
        return $id ? (int)$id + 1 : 1;
    }
    public function alterSequencia($idValido)
    {
        return DB::transaction(function () use ($idValido) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE ALTER_SEQUENCE('SEQ_PARMTABPRECO', $idValido, 'U')");
        });
    }

    public function verificaVinculoClienteTabela($cd_pessoa)
    {
        $query = "
            SELECT
                TABPRECO.DS_TABPRECO,
                COUNT(*) AS TOTAL
            FROM PARMTABPRECO
            INNER JOIN TABPRECO ON (TABPRECO.CD_TABPRECO = PARMTABPRECO.CD_TABPRECO)
            WHERE CD_PESSOA = $cd_pessoa
            GROUP BY TABPRECO.DS_TABPRECO
        ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    } 

    public function deletaRecriaVinculoClienteTabela($cd_tabela, $cd_pessoa)
    {
        return DB::transaction(function () use ($cd_tabela, $cd_pessoa) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $delete = "DELETE FROM PARMTABPRECO WHERE CD_TABPRECO = $cd_tabela AND CD_PESSOA = $cd_pessoa";
            DB::connection('firebird')->statement($delete);

            $nr_sequencia = $this->retornaUltimoID();

            $query = "
                UPDATE OR INSERT INTO PARMTABPRECO (NR_SEQUENCIA, CD_PESSOA, CD_TABPRECO, DT_REGISTRO)
                VALUES ($nr_sequencia, $cd_pessoa, $cd_tabela, CURRENT_TIMESTAMP)
                MATCHING (CD_TABPRECO, CD_PESSOA)
            ";
            DB::connection('firebird')->statement($query);

            $this->alterSequencia($nr_sequencia + 1);

            // Após importar os itens, atualizar o status de importação na tabela temporária, para 'S' (SUCESSO)
            $updateStatus = "UPDATE ITEMTABPRECO_PREVIEW SET ST_IMPORTA = 'S' WHERE CD_TABPRECO = $cd_tabela";
            DB::connection('firebird')->statement($updateStatus);

            return response()->json(['success' => true, 'message' => 'Tabela vinculada com sucesso ao(s) cliente(s)!']);
        });
    }

    public function destroyTabelaPreco($cd_tabela)
    {
        DB::transaction(function () use ($cd_tabela) {
            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");
            // Após importar os itens, atualizar o status de importação na tabela temporária, para 'D' (DELETAR)
            $delete = "DELETE FROM ITEMTABPRECO_PREVIEW WHERE CD_TABPRECO = $cd_tabela";
            return DB::connection('firebird')->statement($delete);
        });

        return response()->json(['success' => true, 'message' => 'Tabela deletada com sucesso!']);
    }
}
