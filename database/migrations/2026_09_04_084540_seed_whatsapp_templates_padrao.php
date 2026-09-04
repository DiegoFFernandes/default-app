<?php

use App\Models\WhatsappTemplate;
use Illuminate\Database\Migrations\Migration;

/**
 * Deixa os modelos padrao do Disparo Automatico prontos como rascunho em
 * qualquer instalacao.
 *
 * Templates sao aprovados por conta (WABA), nao por app - a aprovacao nao
 * viaja entre clientes. Como o texto e o mesmo para todos, cada instalacao
 * ja nasce com as definicoes e o onboarding vira so "Enviar p/ analise",
 * sem redigitar nada no WhatsApp Manager.
 *
 * O exemplo do cabecalho (header_handle) nao entra aqui de proposito: ele
 * expira e e gerado na hora do envio, em WhatsappTemplateController::submeter().
 */
return new class extends Migration
{
    private const TEMPLATES = [
        [
            'nome'        => 'nota_cliente',
            'categoria'   => 'UTILITY',
            'idioma'      => 'pt_BR',
            'componentes' => [
                ['type' => 'HEADER', 'format' => 'DOCUMENT'],
                [
                    'type' => 'BODY',
                    'text' => "Olá, {{1}} 👋\n\nSegue a Nota Fiscal {{2}}, em anexo.\n\n"
                            . "Em caso de dúvidas, entre em contato com o nosso faturamento:\n"
                            . "📞 {{3}}\n✉️ {{4}}\n\n"
                            . "Horário de Atendimento: de segunda a sexta, das 8h às 18h.",
                    'example' => ['body_text' => [[
                        'Nome Cliente',
                        '123456',
                        '(99) 9.9999-9999',
                        'email@empresa.com.br',
                    ]]],
                ],
            ],
        ],
        [
            'nome'        => 'boleto_cliente',
            'categoria'   => 'UTILITY',
            'idioma'      => 'pt_BR',
            'componentes' => [
                ['type' => 'HEADER', 'format' => 'DOCUMENT'],
                [
                    'type'    => 'BODY',
                    'text'    => 'Segue o(s) boleto(s) referente(s) à Nota Fiscal {{1}}, em anexo.',
                    'example' => ['body_text' => [['9592']]],
                ],
            ],
        ],
    ];

    public function up(): void
    {
        foreach (self::TEMPLATES as $definicao) {
            // Nao sobrescreve o que ja existe: numa instalacao que ja usa a
            // WABA, esses modelos podem estar aprovados e sincronizados.
            if (WhatsappTemplate::where('nome', $definicao['nome'])->exists()) {
                continue;
            }

            WhatsappTemplate::create($definicao + ['status' => 'rascunho']);
        }
    }

    public function down(): void
    {
        WhatsappTemplate::whereIn('nome', array_column(self::TEMPLATES, 'nome'))
            ->where('status', 'rascunho')
            ->delete();
    }
};
