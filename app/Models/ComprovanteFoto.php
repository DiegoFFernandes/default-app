<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprovanteFoto extends Model
{
    use HasFactory;

    protected $table = 'comprovante_foto';

    protected $fillable = ['id_comprovante', 'path'];

    public function comprovante()
    {
        return $this->belongsTo(Comprovante::class, 'id_comprovante');
    }
}
