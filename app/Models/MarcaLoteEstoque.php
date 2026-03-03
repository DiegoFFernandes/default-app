<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarcaLoteEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'ds_marca_lote',
        'created_at',
        'updated_at'
    ];

    protected $table = 'marca_lote_estoque';
}
