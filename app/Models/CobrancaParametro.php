<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobrancaParametro extends Model
{
    protected $table    = 'cobranca_parametros';
    protected $fillable = ['chave', 'valor', 'updated_by'];

    public static function get(string $chave, ?string $default = null): ?string
    {
        return static::where('chave', $chave)->value('valor') ?? $default;
    }

    public static function set(string $chave, string $valor, ?int $userId = null): void
    {
        static::updateOrCreate(
            ['chave'      => $chave],
            ['valor'      => $valor, 'updated_by' => $userId]
        );
    }
}
