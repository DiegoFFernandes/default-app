<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMensagem extends Model
{
    protected $table = 'whatsapp_mensagens';

    protected $fillable = [
        'direcao',
        'telefone',
        'mensagem',
        'wamid',
        'status',
    ];
}
