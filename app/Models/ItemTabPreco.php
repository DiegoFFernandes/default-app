<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemTabPreco extends Model
{
    use HasFactory;

    protected $table = 'ITEMTABPRECO';

    public function saveItemTabPreco($input)
    {
        try {
            return DB::transaction(function () use ($input) {
                // Executando o procedimento no Firebird
                DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

                // Processando os itens
                foreach ($input as $item) {
                    $query = "
                        UPDATE OR INSERT INTO ITEMTABPRECO_PREVIEW (CD_TABPRECO, CD_ITEM, VL_PRECO, DT_REGISTRO)
                        VALUES ($item[CD_TABELA], $item[ID], $item[VALOR], CURRENT_TIMESTAMP)
                        MATCHING (CD_TABPRECO, CD_ITEM)
            ";
                    DB::connection('firebird')->statement($query);
                }

                // Retorna um sucesso caso todos os itens sejam processados sem erros
                return response()->json(['success' => true, 'message' => 'Itens salvos com sucesso, e enviados para importação!']);
            });
        } catch (\Exception $e) {
            // Em caso de erro, reverte a transação e retorna a mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar os itens: ' . $e->getMessage()
            ], 500); // 500 - Internal Server Error
        }
    }

   
}