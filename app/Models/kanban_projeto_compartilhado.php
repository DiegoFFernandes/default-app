<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_projeto_compartilhado extends Model
{
    use HasFactory;

    protected $table = 'kanban_projetos_compartilhados';
    protected $fillable = [
        'id_projeto',
        'id_user',
        'id_user_proprietario',
    ];

    // Ids de usuarios que ja possuem o projeto compartilhado
    public function usuariosDoProjeto($idProjeto)
    {
        return $this->where('id_projeto', $idProjeto)->pluck('id_user')->toArray();
    }

    // Usuarios (id + nome) que possuem acesso compartilhado ao projeto
    public function usuariosComAcesso($idProjeto)
    {
        return $this
            ->select('users.id', 'users.name')
            ->join('users', 'users.id', '=', 'kanban_projetos_compartilhados.id_user')
            ->where('id_projeto', $idProjeto)
            ->orderBy('users.name')
            ->get();
    }

    // Compartilha o projeto com uma lista de usuarios (ignora quem ja tem)
    public function compartilhar($idProjeto, $idProprietario, $idsUsuarios)
    {
        try {
            $jaCompartilhado = $this->usuariosDoProjeto($idProjeto);

            foreach ($idsUsuarios as $idUser) {
                if (in_array($idUser, $jaCompartilhado)) {
                    continue;
                }

                $this->create([
                    'id_projeto' => $idProjeto,
                    'id_user' => $idUser,
                    'id_user_proprietario' => $idProprietario,
                ]);
            }

            return response()->json(['success' => 'Projeto compartilhado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao compartilhar o projeto: ' . $e->getMessage()], 500);
        }
    }

    // Remove o compartilhamento de um projeto com um usuario
    public function revogar($idProjeto, $idUser)
    {
        try {
            $this->where('id_projeto', $idProjeto)->where('id_user', $idUser)->delete();

            return response()->json(['success' => 'Compartilhamento removido com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao remover o compartilhamento: ' . $e->getMessage()], 500);
        }
    }
}
