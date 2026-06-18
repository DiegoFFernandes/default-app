<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprovante extends Model
{
    use HasFactory;

    protected $table = 'comprovante';

    protected $fillable = [
        'cd_user',
        'tp_despesa',
        'vl_consumido',
        'ds_observacao',
        'st_visto',
        'dt_despesa',
    ];

    public static function tiposDespesa(): array
    {
        return [
            'ALI' => 'Alimentação',
            'COM' => 'Combustível',
            'HOS' => 'Hospedagem',
            'PED' => 'Pedágio',
        ];
    }

    public function fotos()
    {
        return $this->hasMany(ComprovanteFoto::class, 'id_comprovante');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cd_user');
    }
}
