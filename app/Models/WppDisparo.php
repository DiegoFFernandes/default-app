<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WppDisparo extends Model
{
    public $timestamps = false;

    protected $table = 'wpp_disparos';

    protected $fillable = [
        'user_id',
        'phone',
        'mensagem',
        'status',
        'erro',
        'token',
        'referencia_tipo',
        'referencia_id',
        'dt_envio',
        'dt_registro',
    ];

    protected $casts = [
        'dt_envio'    => 'datetime',
        'dt_registro' => 'datetime',
    ];

    const STATUS_ENVIADO = 'E';
    const STATUS_FALHA   = 'F';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
