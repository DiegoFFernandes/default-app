<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_projeto extends Model
{
    use HasFactory;

    public function listProjetos($idUser)
    {
        return $this->where('cd_usuario', $idUser)->get();
    }
}
