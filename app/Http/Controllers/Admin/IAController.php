<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PedidoPneu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IAController extends Controller
{
    protected $pedidoPneu;

    public function __construct(PedidoPneu $pedidoPneu)
    {
        $this->pedidoPneu = $pedidoPneu;
    }

    public function index()
    {
        return view('admin.ia.index');
    }

    public function perguntar(Request $request)
    {
        $pergunta = $request->input('pergunta');

        $prompt = "
                Você é um classificador de intenções.

                Responda APENAS com JSON válido no formato:

                {
                \"intent\": \"...\",
                \"parametros\": {}
                }

                Possíveis intents:
                - faturamento_mensal
                - inadimplencia_mensal
                - top_clientes
                - pedidos_coletados
                - pneus_coletados

                REGRAS IMPORTANTES:

                1. Para períodos por mês:
                - SEMPRE use \"mes\" e \"ano\"
                - NÃO use data_inicio e data_fim

                Exemplo:
                \"março\" → { \"mes\": 3, \"ano\": 2026 }

                2. Para período de um dia:
                - use data_inicio e data_fim iguais

                Exemplo:
                \"hoje\" → data_inicio = data_fim = hoje

                3. Para intervalos:
                - use data_inicio e data_fim

                4. Determine também:
                - tipo_periodo: dia, mes, ano, intervalo

                5. Nunca invente datas erradas.

                6. Para todas as datas responda no formato: dd.mm.aaaa

                Pergunta: $pergunta
        ";

        $response = $this->apiChat($prompt);

        $data = $response->json();

        $texto = $data['output'][0]['content'][0]['text'] ?? '{}';

        // limpa markdown
        $texto = str_replace(['```json', '```'], '', $texto);

        $json = json_decode($texto, true);

        // validação
        if (!$json || !isset($json['intent'])) {
            return response()->json([
                'tipo' => 'texto',
                'titulo' => 'Resposta',
                'dados' => 'Não consegui entender a pergunta.',
                'resumo_ia' => null
            ]);
        }

        return $this->executarAcao($json);
    }

    public function resumoIA($dados)
    {
        $prompt = "
            1. Resuma os seguintes dados em um parágrafo curto e objetivo, destacando os pontos mais importantes.
            2. Exiba quem são os maiores compradores e as suas compras 'NM_PESSOA', 'DS_SERVICOPNEU', 'VALOR_MEDIO'.
            3. Verifique quais foi o DS_SERVICOPNEU mais frequente e destaque isso no resumo.    
            4. Não e necessario mencionar do que os dados se tratam, apenas resuma as informações seguindo as regras acima.    
            5. Responda apenas com o resumo, sem introdução ou conclusão.    
            " .

            "Dados" . json_encode($dados);

        $response = $this->apiChat($prompt);

        $data = $response->json();

        return $data['output'][0]['content'][0]['text'] ?? '';
    }

    public function executarAcao($dados)
    {
        $parms = $dados['parametros'] ?? [];

        $dataFormatadas = $this->validarPeriodoData($parms);

        $dtInicio = $dataFormatadas['data_inicio'];
        $dtFim = $dataFormatadas['data_fim'];

        switch ($dados['intent'] ?? '') {
            case 'inadimplencia_mensal':
                // Aqui você pode implementar a lógica para consultar a inadimplência mensal
                return response()->json([
                    'tipo' => 'texto',
                    'titulo' => 'Resposta',
                    'dados' => 'A inadimplência mensal é de 5%.',
                    'resumo_ia' => null
                ]);
            case 'pedidos_coletados':
                $dados = $this->pedidoPneu->getColetaPedidoPneu($dtInicio, $dtFim, 1);

                return response()->json([
                    'tipo' => 'tabela',
                    'titulo' => "Pneus coletados de $dtInicio a $dtFim",
                    'dados' => $dados,
                    'resumo_ia' => $this->resumoIA($dados)
                ]);
            case 'pneus_coletados':
                $dados = $this->pedidoPneu->getColetaPedidoPneu($dtInicio, $dtFim, 1);

                return response()->json([
                    'tipo' => 'tabela',
                    'titulo' => "Pneus coletados de $dtInicio a $dtFim",
                    'dados' => $dados,
                    'resumo_ia' => $this->resumoIA($dados)
                ]);
            default:
                return response()->json([
                    'tipo' => 'texto',
                    'titulo' => 'Resposta',
                    'dados' => 'Não entendi sua solicitação.',
                    'resumo_ia' => null
                ]);
        }
    }

    public function validarPeriodoData($params)
    {
        $tipoPeriodo = $params['tipo_periodo'] ?? 'dia';

        switch ($tipoPeriodo) {
            case 'dia':
                $inicio = Carbon::createFromFormat('d.m.Y', $params['data_inicio']);
                $fim = $inicio->copy();
                break;

            case 'mes':
                $mes = $params['mes'] ?? now()->month;
                $ano = $params['ano'] ?? now()->year;

                $inicio = Carbon::create($ano, $mes, 1);
                $fim = $inicio->copy()->endOfMonth();
                break;

            case 'ano':
                $ano = $params['ano'] ?? now()->year;

                $inicio = Carbon::create($ano, 1, 1);
                $fim = Carbon::create($ano, 12, 31);
                break;

            case 'intervalo':
                $inicio = Carbon::createFromFormat('d.m.Y', $params['data_inicio']);
                $fim = Carbon::createFromFormat('d.m.Y', $params['data_fim']);
                break;

            default:
                $inicio = now();
                $fim = now();
        }

        return [
            'data_inicio' => $inicio->format('d.m.Y'),
            'data_fim' => $fim->format('d.m.Y'),
            'tipo_periodo' => $tipoPeriodo
        ];
    }

    public function apiChat($prompt)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "input_text",
                            "text" => $prompt
                        ]
                    ]
                ]
            ]
        ]);
    }
}
