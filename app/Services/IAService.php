<?php

namespace App\Services;

use App\Models\IaIntencao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
     * Usa apenas as intenções ativas cadastradas no banco.
     * Retorna ['intent' => '...', 'parametros' => [...]] ou [] em caso de falha.
     */
    public function classificarIntent(string $pergunta): array
    {
        $hoje = now()->format('d.m.Y');

        $intentsAtivos = IaIntencao::where('ativo', true)
            ->orderBy('id')
            ->get(['nome', 'descricao']);

        if ($intentsAtivos->isEmpty()) {
            return [];
        }

        $listaIntents = $intentsAtivos
            ->map(fn($i) => "- {$i->nome} → {$i->descricao}")
            ->join("\n");

        $apelidos      = config('empresas.apelidos');
        $adminIds      = config('empresas.admin_ids');
        $listaEmpresas = collect($adminIds)
            ->map(fn($id) => "- \"{$apelidos[$id]}\" ou \"empresa {$id}\" → cd_empresa: {$id}")
            ->join("\n");

        $prompt = <<<PROMPT
                    Você é um classificador de intenções.

                    A data de HOJE é: {$hoje}

                    Responda APENAS com JSON válido no formato:
                    {"intent": "...", "parametros": {}}

                    Possíveis intents:
                    {$listaIntents}

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

                    7. Se o usuário mencionar uma empresa específica, adicione cd_empresa nos parametros:
                    {$listaEmpresas}
                    Se não mencionar empresa, omita cd_empresa (serão usadas todas).

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
     * Executa o sql_template de uma intenção e retorna o texto formatado.
     * Usado para pré-visualização no painel admin.
     */
    public function previa(IaIntencao $intencao, string $dtInicio, string $dtFim, ?int $cdEmpresa = null): string
    {
        return $this->coletasTexto($dtInicio, $dtFim, $intencao, $cdEmpresa);
    }

    /**
     * Recebe a pergunta do usuário via WhatsApp, resolve o intent
     * e retorna uma resposta formatada como texto simples.
     * Retorna null se o intent não for suportado ou sem SQL configurado.
     */
    public function responderParaWhatsapp(string $pergunta): ?string
    {
        $intent = $this->classificarIntent($pergunta);

        if (empty($intent)) {
            return null;
        }

        $intencao = IaIntencao::where('nome', $intent['intent'])
            ->where('ativo', true)
            ->first();

        if (! $intencao || ! $intencao->sql_template) {
            Log::info('IAService: intent sem SQL ou inativo', ['intent' => $intent['intent'] ?? null]);
            return null;
        }

        $parametros = $intent['parametros'] ?? [];
        $periodo    = $this->resolverPeriodo($parametros);
        $cdEmpresa  = isset($parametros['cd_empresa']) ? (int) $parametros['cd_empresa'] : null;

        return $this->coletasTexto($periodo['inicio'], $periodo['fim'], $intencao, $cdEmpresa);
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

    private function coletasTexto(string $dtInicio, string $dtFim, IaIntencao $intencao, ?int $cdEmpresa = null): string
    {
        $adminIds = config('empresas.admin_ids');
        $empresas = $cdEmpresa && in_array($cdEmpresa, $adminIds)
            ? (string) $cdEmpresa
            : implode(',', $adminIds);

        $sql = str_replace(
            ['{{dt_inicio}}', '{{dt_fim}}', '{{cd_empresa}}'],
            [$dtInicio,       $dtFim,       $empresas],
            $intencao->sql_template
        );

        try {
            $dados = DB::connection('firebird')->select($sql);
        } catch (\Throwable $e) {
            Log::error('IAService: erro ao executar SQL da intenção', [
                'intencao' => $intencao->nome,
                'erro'     => $e->getMessage(),
            ]);
            return "Ocorreu um erro ao consultar os dados. Tente novamente.";
        }

        if (empty($dados)) {
            $periodo = $dtInicio === $dtFim ? "em *{$dtInicio}*" : "de *{$dtInicio}* a *{$dtFim}*";
            return "📦 Nenhuma coleta encontrada {$periodo}.";
        }

        $totalPneus  = array_sum(array_column($dados, 'QTD'));
        $valorTotal  = array_sum(array_column($dados, 'VL_TOTAL'));
        $qtdClientes = count(array_unique(array_column($dados, 'NM_PESSOA')));

        // Agrupa por vendedor
        $vendedores = [];
        foreach ($dados as $item) {
            $v = $item->NM_VENDEDOR ?? 'Sem Vendedor';
            $vendedores[$v]['qtd']   = ($vendedores[$v]['qtd']   ?? 0) + (int)   $item->QTD;
            $vendedores[$v]['valor'] = ($vendedores[$v]['valor'] ?? 0) + (float) $item->VL_TOTAL;
        }
        arsort($vendedores);

        // Agrupa por empresa
        $apelidos   = config('empresas.apelidos');
        $apelidoPad = config('empresas.apelido_padrao', 'OUTROS');
        $porEmpresa = [];
        foreach ($dados as $item) {
            $idEmp   = (int) ($item->IDEMPRESA ?? 0);
            $nmEmp   = $apelidos[$idEmp] ?? $apelidoPad;
            $porEmpresa[$nmEmp]['qtd']   = ($porEmpresa[$nmEmp]['qtd']   ?? 0) + (int)   $item->QTD;
            $porEmpresa[$nmEmp]['valor'] = ($porEmpresa[$nmEmp]['valor'] ?? 0) + (float) $item->VL_TOTAL;
        }
        uasort($porEmpresa, fn($a, $b) => $b['valor'] <=> $a['valor']);

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
        $linhas[] = "🏢 *Por Empresa:*";

        foreach ($porEmpresa as $nmEmp => $e) {
            $linhas[] = "• {$nmEmp} → {$e['qtd']} pneus · {$fmt($e['valor'])}";
        }

        $linhas[] = "";
        $linhas[] = "🏆 *Vendedores:*";

        foreach (array_slice($vendedores, 0, 5, true) as $nome => $v) {
            $nomeSimples = preg_replace('/^\d+-/', '', $nome);
            $linhas[]    = "• {$nomeSimples} → {$v['qtd']} pneus · {$fmt($v['valor'])}";
        }

        return implode("\n", $linhas);
    }
}
