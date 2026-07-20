<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_projeto extends Model
{
    use HasFactory;

    protected $table = 'kanban_projetos';
    protected $fillable = [
        'nome',
        'descricao',
        'color',
        'posicao',
        'cd_usuario',
    ];
    protected $appends = ['encrypted_id'];

    public function getEncryptedIdAttribute()
    {
        return encrypt($this->id);
    }

    public function listProjetos($idUser)
    {
        return $this
            ->select('id', 'nome', 'descricao', 'color', 'posicao')
            ->when(!empty($idUser), function ($query) use ($idUser) {
                $query->whereIn('cd_usuario', $idUser);
            })
            ->orderBy('posicao')
            ->orderBy('id')
            ->get();
    }

    public function getProjetoById($projeto_id)
    {
        return $this->where('id', $projeto_id)->first();
    }

    // Projetos que foram compartilhados COM o usuario informado
    public function listProjetosCompartilhados($idUser)
    {
        return $this
            ->select(
                'kanban_projetos.id',
                'kanban_projetos.nome',
                'kanban_projetos.descricao',
                'kanban_projetos.color',
                'users.name as proprietario'
            )
            ->join('kanban_projetos_compartilhados as kpc', 'kpc.id_projeto', '=', 'kanban_projetos.id')
            ->join('users', 'users.id', '=', 'kanban_projetos.cd_usuario')
            ->where('kpc.id_user', $idUser)
            ->orderBy('kanban_projetos.nome')
            ->get();
    }

    public function salvarProjeto($data)
    {
        try {
            $this->create(
                [
                    'nome' => $data['nome'],
                    'descricao' => $data['descricao'] ?? null,
                    'color' => $data['color'],
                    'posicao' => $this->max('posicao') + 1,
                    'cd_usuario' => auth()->user()->id,
                ]
            );

            return response()->json(['success' => 'Projeto salvo com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar o projeto: ' . $e->getMessage()], 500);
        }
    }

    public function editarProjeto($data)
    {
        $projeto = $this->find($data['id']);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado.'], 404);
        }

        try {
            $projeto->nome = $data['nome'];
            $projeto->descricao = $data['descricao'] ?? null;
            $projeto->color = $data['color'];
            $projeto->save();

            return response()->json(['success' => 'Projeto editado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao editar o projeto: ' . $e->getMessage()], 500);
        }
    }

    public function reordenarProjetos($projetoIds)
    {
        try {
            foreach ($projetoIds as $index => $projetoId) {
                $projeto = $this->find($projetoId);
                if ($projeto) {
                    $projeto->posicao = $index + 1;
                    $projeto->save();
                }
            }

            return response()->json(['success' => 'Projetos reordenados com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao reordenar os projetos: ' . $e->getMessage()], 500);
        }
    }
}
