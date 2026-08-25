<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'nome',
        'categoria',
        'idioma',
        'componentes',
        'header_documento_path',
        'status',
        'meta_template_id',
        'motivo_rejeicao',
    ];

    protected $casts = [
        'componentes' => 'array',
    ];

    // Acessores usados pra popular o formulário de edição - evitam repetir a
    // busca em $componentes na view/JS toda vez.
    public function getHeaderAttribute(): ?array
    {
        return collect($this->componentes)->firstWhere('type', 'HEADER');
    }

    public function getCorpoAttribute(): string
    {
        return collect($this->componentes)->firstWhere('type', 'BODY')['text'] ?? '';
    }

    public function getRodapeAttribute(): string
    {
        return collect($this->componentes)->firstWhere('type', 'FOOTER')['text'] ?? '';
    }

    public function getExemplosAttribute(): string
    {
        $body = collect($this->componentes)->firstWhere('type', 'BODY');

        return implode(', ', $body['example']['body_text'][0] ?? []);
    }
}
