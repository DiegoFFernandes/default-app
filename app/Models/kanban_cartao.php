<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_cartao extends Model
{
    use HasFactory;

    protected $table = 'kanban_cartoes';

    protected $fillable = [
        'titulo',
        'descricao',
        'coluna_id',
        'posicao',
    ];


    public function salvarTarefas($dados)
    {
        $posicao = $this->where('coluna_id', $dados['coluna'])->count() + 1;

        try {
            $this::updateOrCreate(
                [
                    'coluna_id' => $dados['coluna'],
                    'posicao' => $posicao,
                ],
                [
                    'titulo' => $dados['titulo'],
                    'descricao' => $dados['descricao']
                ]
            );

            return response()->json(['success' => true, 'message' => 'Tarefa salva com sucesso!']);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteCartao($idCard)
    {
        try {
            $card = $this->find($idCard);
            if ($card) {
                $card->delete();
                return response()->json(['success' => true, 'message' => 'Tarefa deletada com sucesso.']);
            } else {
                return response()->json(['error' => true, 'message' => 'Tarefa não encontrada.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function reordenarTarefas($tarefas)
    {
        // return $tarefas;
        try {
            foreach ($tarefas as $tarefa) {
                $card = $this->find($tarefa['id']);
                if ($card) {
                    $card->coluna_id = $tarefa['coluna'];
                    $card->posicao = $tarefa['posicao'];
                    $card->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Tarefas reordenadas com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function editarCartoes($dados)
    {
        try {
            $card = $this->find($dados['id']);
            if ($card) {
                $card->titulo = $dados['titulo'];
                $card->descricao = $dados['descricao'];
                $card->save();

                return response()->json(['success' => true, 'message' => 'Cartão editado com sucesso!']);
            } else {
                return response()->json(['error' => true, 'message' => 'Cartão não encontrado.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
