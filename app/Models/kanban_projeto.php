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
            ->select('id', 'nome', 'descricao')
            ->when(!empty($idUser), function ($query) use ($idUser) {
                $query->whereIn('cd_usuario', $idUser);
            })
            ->get();
    }

    public function getProjetoById($projeto_id)
    {
        return $this->where('id', $projeto_id)->first();
    }

    public function salvarProjeto($data)
    {
        try {
            $projeto = $this->create(
                [
                    'nome' => $data['nome'],
                    'descricao' => $data['descricao'] ?? null,
                    'cd_usuario' => auth()->user()->id,
                ]
            );

            return response()->json(['success' => 'Projeto salvo com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar o projeto: ' . $e->getMessage()], 500);
        }
        return $projeto;
    }
}
