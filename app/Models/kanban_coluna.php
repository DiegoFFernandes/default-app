<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_coluna extends Model
{
    use HasFactory;

    public function listColunas($idProjeto)
    {
        return $this->where('projeto_id', $idProjeto)->orderBy('posicao')->get();
    }
}
