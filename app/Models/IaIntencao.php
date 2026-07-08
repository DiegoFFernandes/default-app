<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IaIntencao extends Model
{
    public $timestamps = false;

    protected $table = 'ia_intencoes';

    protected $fillable = ['nome', 'descricao', 'ativo', 'sql_template', 'updated_at'];

    protected $casts = [
        'ativo'      => 'boolean',
        'updated_at' => 'datetime',
    ];
}
