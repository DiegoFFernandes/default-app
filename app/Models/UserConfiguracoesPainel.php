<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConfiguracoesPainel extends Model
{
    use HasFactory;

    protected $table = 'configuracoes_painel';

    protected $fillable = [        
        'sidebar_collapse',
        'dark_mode',
        'id_usuario',
    ];    
}
