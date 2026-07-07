<?php

namespace App\Services;

use App\Models\PedidoPneu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IAService
{
    /**
     * Envia um prompt para o modelo e retorna o texto da resposta.
     */
    public function chat(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => [
                [
                    'role'    => 'user',
                    'content' => [
                        ['type' => 'input_text', 'text' => $prompt],
                    ],
                ],
            ],
        ]);

        if (! $response->successful()) {
            Log::error('IAService: falha na API', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return '';
        }

        return $response->json('output.0.content.0.text') ?? '';
    }

    /**
     * Classifica a pergunta em uma intent com parâmetros.
     * Retorna ['intent' => '...', 'parametros' => [...]] ou [] em caso de falha.
     */
    public function classificarIntent(string $pergunta): array
    {
        $hoje = now()->format('d.m.Y');

        $prompt = <<<PROMPT
                    Você é um classificador de intenções.

                    A data de HOJE é: {$hoje}

                    Responda APENAS com JSON válido no formato:
                    {"intent": "...", "parametros": {}}

                    Possíveis intents:
                    - faturamento_mensal
                    - inadimplencia_mensal
                    - top_clientes
                    - pedidos_coletados
                    - pneus_coletados

                    REGRAS IMPORTANTES:

                    1. Para períodos por mês:
                    - SEMPRE use "mes" e "ano"
                    - NÃO use data_inicio e data_fim
                    Exemplo: "março" → { "mes": 3, "ano": 2026 }

                    2. Para período de um dia:
                    - use data_inicio e data_fim iguais
                    Exemplo: "hoje" → data_inicio = data_fim = {$hoje}

                    3. Para intervalos:
                    - use data_inicio e data_fim

                    4. Determine também:
                    - tipo_periodo: dia, mes, ano, intervalo

                    5. Nunca invente datas erradas. Use sempre a data de hoje informada acima.

                    6. Para todas as datas responda no formato: dd.mm.aaaa

                    Pergunta: {$pergunta}
                    PROMPT;

        $texto = $this->chat($prompt);
        $texto = trim(str_replace(['```json', '```'], '', $texto));
        $json  = json_decode($texto, true);

        if (! $json || ! isset($json['intent'])) {
            Log::warning('IAService: intent não reconhecida', ['pergunta' => $pergunta, 'resposta' => $texto]);
            return [];
        }

        return $json;
    }

    /**
     * Recebe a pergunta do usuário via WhatsApp, resolve o intent
     * e retorna uma resposta formatada como texto simples.
     * Retorna null se o intent não for suportado.
     */
    public function responderParaWhatsapp(string $pergunta): ?string
    {
        $intent = $this->classificarIntent($pergunta);

        if (empty($intent)) {
            return null;
        }

        $periodo = $this->resolverPeriodo($intent['parametros'] ?? []);

        return match ($intent['intent']) {
            'pedidos_coletados',
            'pneus_coletados' => $this->coletasTexto($periodo['inicio'], $periodo['fim']),

            default => null,
        };
    }

    // -------------------------------------------------------
    // Resolução de período
    // -------------------------------------------------------

    private function resolverPeriodo(array $params): array
    {
        $tipo = $params['tipo_periodo'] ?? 'dia';

        switch ($tipo) {
            case 'mes':
                $inicio = Carbon::create($params['ano'] ?? now()->year, $params['mes'] ?? now()->month, 1);
                $fim    = $inicio->copy()->endOfMonth();
                break;

            case 'ano':
                $inicio = Carbon::create($params['ano'] ?? now()->year, 1, 1);
                $fim    = Carbon::create($params['ano'] ?? now()->year, 12, 31);
                break;

            case 'intervalo':
                $inicio = Carbon::createFromFormat('d.m.Y', $params['data_inicio']);
                $fim    = Carbon::createFromFormat('d.m.Y', $params['data_fim']);
                break;

            default: // dia
                $inicio = Carbon::createFromFormat('d.m.Y', $params['data_inicio'] ?? now()->format('d.m.Y'));
                $fim    = $inicio->copy();
        }

        return [
            'inicio' => $inicio->format('d.m.Y'),
            'fim'    => $fim->format('d.m.Y'),
        ];
    }

    // -------------------------------------------------------
    // Formatadores de resposta para WhatsApp
    // -------------------------------------------------------

    private function coletasTexto(string $dtInicio, string $dtFim): string
    {
        $dados = PedidoPneu::getColetaPedidoPneu($dtInicio, $dtFim, 1);

        if (empty($dados)) {
            $periodo = $dtInicio === $dtFim ? "em *{$dtInicio}*" : "de *{$dtInicio}* a *{$dtFim}*";
            return "📦 Nenhuma coleta encontrada {$periodo}.";
        }

        $totalPneus  = array_sum(array_column($dados, 'QTD'));
        $valorTotal  = array_sum(array_column($dados, 'VL_TOTAL'));
        $qtdClientes = count(array_unique(array_column($dados, 'NM_PESSOA')));

        // Agrupa e ordena por vendedor
        $vendedores = [];
        foreach ($dados as $item) {
            $v = $item->NM_VENDEDOR ?? 'Sem Vendedor';
            $vendedores[$v]['qtd']   = ($vendedores[$v]['qtd']   ?? 0) + (int)   $item->QTD;
            $vendedores[$v]['valor'] = ($vendedores[$v]['valor'] ?? 0) + (float) $item->VL_TOTAL;
        }
        arsort($vendedores);

        $periodo = $dtInicio === $dtFim
            ? "em *{$dtInicio}*"
            : "de *{$dtInicio}* a *{$dtFim}*";

        $fmt = fn(float $v) => 'R$ ' . number_format($v, 2, ',', '.');

        $linhas   = [];
        $linhas[] = "📦 *Coletas {$periodo}*";
        $linhas[] = "";
        $linhas[] = "📊 *Resumo:*";
        $linhas[] = "• Pneus: *{$totalPneus}*";
        $linhas[] = "• Valor: *{$fmt($valorTotal)}*";
        $linhas[] = "• Clientes: *{$qtdClientes}*";
        $linhas[] = "";
        $linhas[] = "🏆 *Vendedores:*";

        foreach (array_slice($vendedores, 0, 5, true) as $nome => $v) {
            // Remove prefixo "ID-" que vem do Firebird (ex: "12-João Silva")
            $nomeSimples = preg_replace('/^\d+-/', '', $nome);
            $linhas[]    = "• {$nomeSimples} → {$v['qtd']} pneus · {$fmt($v['valor'])}";
        }

        return implode("\n", $linhas);
    }
}
