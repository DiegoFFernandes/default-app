<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WppParametro extends Model
{
    protected $table      = 'wpp_parametros';
    protected $primaryKey = 'chave';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    const UPDATED_AT = 'updated_at';

    protected $fillable = ['chave', 'valor'];

    public static function get(string $chave, mixed $default = null): mixed
    {
        $row = static::find($chave);
        return $row ? $row->valor : $default;
    }

    public static function set(string $chave, mixed $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }
}
