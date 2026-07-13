<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FluxoCaixaParametro extends Model
{
    protected $table = 'fluxo_caixa_parametros';

    protected $fillable = [
        'tipo',
        'cd_tipoconta',
        'ds_tipoconta',
        'cd_formapagto',
        'updated_by',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Agrupa os parâmetros de um tipo ('receber' ou 'pagar') por CD_TIPOCONTA, juntando as
     * formas de pagamento associadas (quando houver) num array — pronto pra passar direto pra
     * Contas::contasReceber()/contasPagar().
     *
     * @return array<int, string[]> ex: [2 => ['BL', 'DB'], 12 => [], 5 => []]
     */
    public static function tipocontasPorTipo(string $tipo): array
    {
        return self::where('tipo', $tipo)
            ->orderBy('cd_tipoconta')
            ->get()
            ->groupBy('cd_tipoconta')
            ->map(fn ($linhas) => $linhas->pluck('cd_formapagto')->filter()->values()->all())
            ->all();
    }
}
