<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUsers extends Model
{
    use HasFactory;

    protected $table = 'notificacao_usuario';

    protected $fillable = [
        'user_id',
        'tipo_notificacao',
        'ds_notificacao',
        'created_at',
        'updated_at'
    ];

    public function allListTypeNotificationUsers($TipoNotificacao)
    {
        return $this->where('tipo_notificacao', $TipoNotificacao)
            ->join('users', 'notificacao_usuario.user_id', '=', 'users.id')
            ->where('users.notifications', 'S')
            ->select('users.id', 'users.name', 'users.email', 'notificacao_usuario.ds_notificacao', 'notificacao_usuario.tipo_notificacao')
            ->get();
    }
}
