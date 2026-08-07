<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompraParam extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'compra_param_fonte_item';

    /**
     * Fonte de itens em uso: 'J' = Junsoft (ITEM), 'P' = próprios (COMPRA_ITEM).
     * Cacheado — o parâmetro muda raramente e é lido a cada request (menu/gate).
     */
    public function fonteItem(): string
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(6), function () {
            // Resiliente: se COMPRA_PARAM ainda não existir (código no ar antes do
            // DDL), assume Junsoft. O Gate roda no menu de todo usuário logado —
            // uma exceção aqui derrubaria o sistema inteiro.
            try {
                $row = DB::connection('firebird')->selectOne(
                    "SELECT ST_FONTE_ITEM FROM COMPRA_PARAM WHERE ID = 1"
                );
                return $row->ST_FONTE_ITEM ?? 'J';
            } catch (\Throwable) {
                return 'J';
            }
        });
    }

    public function usaItensProprios(): bool
    {
        return $this->fonteItem() === 'P';
    }

    public function setFonteItem(string $fonte): void
    {
        $fonte = in_array($fonte, ['J', 'P'], true) ? $fonte : 'J';

        DB::connection('firebird')->statement(
            "UPDATE OR INSERT INTO COMPRA_PARAM (ID, ST_FONTE_ITEM) VALUES (1, :st) MATCHING (ID)",
            ['st' => $fonte]
        );

        Cache::forget(self::CACHE_KEY);
    }
}
