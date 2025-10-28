<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_coluna extends Model
{
    use HasFactory;

    public function listColunas($idProjeto)
    {
        return $this->where('projeto_id', $idProjeto)->where('st_coluna', 'P')->orderBy('posicao')->get();
    }

    public function editarColuna($input)
    {
        try {
            $coluna = $this->find($input['id']);
            if ($coluna) {
                $coluna->nome = $input['nome'];
                $coluna->color = $input['color'];
                $coluna->save();

                return response()->json(['success' => true, 'message' => 'Coluna editada com sucesso!']);
            } else {
                return response()->json(['error' => true, 'message' => 'Coluna não encontrada.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function arquivarColuna($idColuna)
    {
        try {
            $coluna = $this->find($idColuna);
            if ($coluna) {
                $coluna->st_coluna = 'A'; // Muda o status para 'A' (Arquivada)
                $coluna->save();

                return response()->json(['success' => true, 'message' => 'Coluna arquivada com sucesso!']);
            } else {
                return response()->json(['error' => true, 'message' => 'Coluna não encontrada.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function reordenarColunas($colunas)
    {
        try {
            foreach ($colunas as $index => $colunaId) {
                $coluna = $this->find($colunaId);
                if ($coluna) {
                    $coluna->posicao = $index + 1; // Atualiza a posição
                    $coluna->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Colunas reordenadas com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function criarColuna($input)
    {
        try {
            $coluna = new kanban_coluna();
            $coluna->projeto_id = $input['projeto_id'];
            $coluna->nome = $input['nome'];
            $coluna->color = $input['color'];
            $coluna->st_coluna = 'P'; // Define o status como 'P' (Pendente)
            $coluna->posicao = $input['posicao'];
            $coluna->save();

            return response()->json(['success' => true, 'message' => 'Coluna criada com sucesso!', 'coluna' => $coluna]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
