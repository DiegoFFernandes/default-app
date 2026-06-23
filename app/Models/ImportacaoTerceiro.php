<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportacaoTerceiro extends Model
{
    public $timestamps  = false;
    protected $table    = 'importacoes_terceiros';

    protected $fillable = [
        'hash_arquivo',
        'nm_arquivo',
        'cd_empresa',
        'cd_usuario',
        'total_registros',
        'dt_referencia_inicio',
        'dt_referencia_fim',
        'created_at',
    ];

    protected $casts = [
        'dt_referencia_inicio' => 'date',
        'dt_referencia_fim'    => 'date',
        'created_at'           => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'cd_usuario');
    }

    public static function registrar(array $data): self
    {
        return self::create(array_merge($data, ['created_at' => now()]));
    }

    public static function porHash(string $hash): ?self
    {
        return self::with('user')->where('hash_arquivo', $hash)->first();
    }
}
