<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WppSessao extends Model
{
    use HasFactory;

    protected $table = 'wpp_sessoes';

    protected $fillable = ['setor', 'session_name'];

    /**
     * Setores que podem ter uma conexão WhatsApp própria.
     * A chave é o slug (usado como nome da sessão no wppconnect-server),
     * o valor é o rótulo exibido na tela.
     */
    public const SETORES = [
        'geral'          => 'Geral',
        'comercial'      => 'Comercial',
        'compras'        => 'Compras',
        'cobranca'       => 'Cobrança',
        'faturamento'    => 'Faturamento',
        'administrativo' => 'Administrativo',
    ];

    public function getLabelAttribute(): string
    {
        return self::SETORES[$this->setor] ?? ucfirst($this->setor);
    }

    /**
     * Setores ainda sem conexão cadastrada — alimenta o select do modal.
     */
    public static function setoresDisponiveis(): array
    {
        $usados = static::pluck('setor')->all();

        return collect(self::SETORES)
            ->reject(fn($label, $setor) => in_array($setor, $usados, true))
            ->map(fn($label, $setor) => ['setor' => $setor, 'label' => $label])
            ->values()
            ->all();
    }

    /**
     * Sessões cadastradas no formato consumido pela view e pelo JS.
     */
    public static function paraView()
    {
        return static::orderBy('setor')
            ->get()
            ->map(fn($s) => [
                'setor' => $s->setor,
                'name'  => $s->session_name,
                'label' => $s->label,
            ]);
    }
}
