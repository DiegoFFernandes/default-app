<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAtalhoFavorito extends Model
{
    protected $table = 'users_atalhos_favoritos';

    protected $fillable = [
        'id_usuario',
        'chave_atalho',
        'ordem',
    ];
}
