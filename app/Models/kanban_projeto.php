<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kanban_projeto extends Model
{
    use HasFactory;

    public function getEncryptedIdAttribute()
    {
        return encrypt($this->id);
    }

    public function listProjetos($idUser)
    {
        return $this
            ->when(!empty($idUser), function ($query) use ($idUser) {
                $query->whereIn('cd_usuario', $idUser);
            })             
            ->get();
    }

    public function getProjetoById($projeto_id)
    {
        return $this->where('id', $projeto_id)->first();
    }
}
