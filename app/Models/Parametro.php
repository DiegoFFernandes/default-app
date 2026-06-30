<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table    = 'parametros';
    protected $fillable = ['view', 'chave', 'valor', 'updated_by'];

    public static function get(string $view, string $chave, ?string $default = null): ?string
    {
        return static::where('view', $view)->where('chave', $chave)->value('valor') ?? $default;
    }

    public static function set(string $view, string $chave, string $valor, ?int $userId = null): void
    {
        static::updateOrCreate(
            ['view'  => $view, 'chave' => $chave],
            ['valor' => $valor, 'updated_by' => $userId]
        );
    }
}
