<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PedidoPneu;
use App\Services\ServiceFiltroGrupoSubgrupo;
use App\Services\WppConnectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IAController extends Controller
{
    protected $pedidoPneu, $serviceFiltroGrupoSubgrupo;

    public function __construct(
        PedidoPneu $pedidoPneu,
        ServiceFiltroGrupoSubgrupo $serviceFiltroGrupoSubgrupo
    ) {
        $this->pedidoPneu = $pedidoPneu;
        $this->serviceFiltroGrupoSubgrupo = $serviceFiltroGrupoSubgrupo;
    }

    public function index()
    {
        return view('admin.ia.index');
    }

    public function perguntar(Request $request)
    {
        $pergunta = $request->input('pergunta');
        $hoje = now()->format('d.m.Y');

        $prompt = "
                Você é um classificador de intenções.

                A data de HOJE é: $hoje

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
                \"hoje\" → data_inicio = data_fim = $hoje

                3. Para intervalos:
                - use data_inicio e data_fim

                4. Determine também:
                - tipo_periodo: dia, mes, ano, intervalo

                5. Nunca invente datas erradas. Use sempre a data de hoje informada acima.

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

    public function resumo(Request $request)
    {
        $dados  = $request->input('dados', []);
        $intent = $request->input('intent', 'pneus_coletados');

        if (empty($dados)) {
            return response()->json(['resumo' => 'Nenhum dado disponível para resumir.']);
        }

        return response()->json(['resumo' => $this->resumoIA($dados, $intent)]);
    }

    public function resumoWhatsapp(Request $request)
    {
        $telefone = $request->input('telefone', '');
        $mensagem = $request->input('mensagem', '');

        if (!$telefone || !$mensagem) {
            return response()->json(['success' => false, 'message' => 'Telefone e mensagem são obrigatórios.'], 422);
        }

        try {
            $wpp    = app(WppConnectService::class);
            $result = $wpp->sendText($telefone, $mensagem, 'resumo_ia');
            $ok     = ($result['status'] ?? '') === 'success';

            return response()->json([
                'success' => $ok,
                'message' => $ok ? 'Mensagem enviada com sucesso!' : ($result['message'] ?? 'Falha ao enviar.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resumoIA(array $dados, string $intent = 'pneus_coletados'): string
    {
        $dados = array_map(fn($item) => (array) $item, $dados);

        [$instrucoes, $contexto, $secoes] = match(true) {
            in_array($intent, ['pneus_coletados', 'pedidos_coletados'])
                => $this->configPneusColetados($dados),
            $intent === 'inadimplencia_mensal'
                => $this->configInadimplenciaMensal($dados),
            $intent === 'relatorio_vencidos'
                => $this->configRelatorioVencidos($dados),
            default
                => $this->configGenerico($dados),
        };

        $prompt = "
            $instrucoes

            Analise os dados abaixo e gere um resumo executivo em português
            seguindo EXATAMENTE esta estrutura (use os emojis dos títulos):

            $secoes

            REGRAS:
            - Máximo 2 linhas por seção.
            - Não invente dados fora do JSON abaixo.
            - Não adicione seções extras.
            - Responda somente com as seções acima.

            Dados:
            " . json_encode($contexto, JSON_UNESCAPED_UNICODE);

        $cacheKey = 'resumo_ia_' . md5($intent . json_encode($contexto));

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($prompt) {
            $response = $this->apiChat($prompt);
            $data     = $response->json();
            return $data['output'][0]['content'][0]['text'] ?? '';
        });
    }

    private function configPneusColetados(array $dados): array
    {
        $totalPneus  = array_sum(array_column($dados, 'QTD'));
        $valorTotal  = array_sum(array_column($dados, 'VL_TOTAL'));
        $ticketMedio = $totalPneus > 0 ? $valorTotal / $totalPneus : 0;

        $datas = array_filter(array_column($dados, 'DT_EMISSAO'));
        $dtMin = !empty($datas) ? min($datas) : '';
        $dtMax = !empty($datas) ? max($datas) : '';

        $vendedores = [];
        $clientes   = [];
        $servicos   = [];
        $bandas     = [];

        foreach ($dados as $item) {
            $qtd   = (int)   ($item['QTD']      ?? 0);
            $valor = (float) ($item['VL_TOTAL'] ?? 0);
            $v = $item['NM_VENDEDOR']    ?? 'Sem Vendedor';
            $c = $item['NM_PESSOA']      ?? 'Sem Cliente';
            $s = $item['DS_SERVICOPNEU'] ?? 'Sem Serviço';
            $b = $item['DSDESENHO']      ?? 'Sem Desenho';

            $vendedores[$v]['qtd']   = ($vendedores[$v]['qtd']   ?? 0) + $qtd;
            $vendedores[$v]['valor'] = ($vendedores[$v]['valor'] ?? 0) + $valor;
            $clientes[$c]['qtd']     = ($clientes[$c]['qtd']     ?? 0) + $qtd;
            $clientes[$c]['valor']   = ($clientes[$c]['valor']   ?? 0) + $valor;
            $servicos[$s]            = ($servicos[$s] ?? 0) + $qtd;
            $bandas[$b]              = ($bandas[$b]   ?? 0) + $qtd;
        }

        arsort($vendedores);
        arsort($clientes);
        arsort($servicos);
        arsort($bandas);

        $fmt   = fn(float $v) => 'R$ ' . number_format($v, 2, ',', '.');
        $toTop = fn(array $map, int $n, string $label, bool $comValor = false) =>
            array_map(
                fn($k, $v) => $comValor
                    ? [$label => $k, 'qtd' => $v['qtd'], 'valor' => $fmt($v['valor'])]
                    : [$label => $k, 'qtd' => $v],
                array_keys(array_slice($map, 0, $n, true)),
                array_slice($map, 0, $n, true)
            );

        $nVend = min(3, count($vendedores));
        $nCli  = min(5, count($clientes));

        $contexto = [
            'periodo'        => ['inicio' => $dtMin, 'fim' => $dtMax],
            'totais'         => [
                'pneus'          => $totalPneus,
                'valor_total'    => $fmt($valorTotal),
                'ticket_medio'   => $fmt($ticketMedio),
                'qtd_clientes'   => count($clientes),
                'qtd_vendedores' => count($vendedores),
            ],
            'top_vendedores' => $toTop($vendedores, $nVend, 'vendedor', true),
            'top_clientes'   => $toTop($clientes,   $nCli,  'cliente',  true),
            'top_servicos'   => $toTop($servicos,   3,      'servico'),
            'top_bandas'     => $toTop($bandas,     3,      'banda'),
        ];

        $instrucoes = "Você é um analista de negócios especializado em recauchutagem e reforma de pneus.";

        $secoes = "
            📊 VISÃO GERAL
            - 1 frase: panorama do período (total de pneus, valor total e ticket médio).

            🏆 TOP VENDEDORES
            - Liste os $nVend melhores: Nome → Qtd pneus · Valor total

            🏢 TOP CLIENTES
            - Liste os $nCli maiores: Nome → Qtd pneus · Valor total

            🔧 SERVIÇO MAIS FREQUENTE
            - Nome do serviço e quantidade.

            🛞 BANDA MAIS COLETADA
            - Nome da banda e quantidade.

            💡 DESTAQUE
            - 1 insight relevante (concentração de clientes, vendedor dominante, oportunidade, alerta).
        ";

        return [$instrucoes, $contexto, $secoes];
    }

    private function configInadimplenciaMensal(array $dados): array
    {
        $totalVencido  = array_sum(array_column($dados, 'VL_SALDO'));
        $totalCarteira = array_sum(array_column($dados, 'VL_DOCUMENTO'));
        $qtdMeses      = count($dados);
        $mediaPerc     = $qtdMeses > 0
            ? array_sum(array_column($dados, 'PC_INADIMPLENCIA')) / $qtdMeses
            : 0;

        usort($dados, fn($a, $b) => $b['PC_INADIMPLENCIA'] <=> $a['PC_INADIMPLENCIA']);

        $fmt = fn(float $v) => 'R$ ' . number_format($v, 2, ',', '.');

        $contexto = [
            'totais' => [
                'total_carteira'  => $fmt($totalCarteira),
                'total_vencido'   => $fmt($totalVencido),
                'media_inadimpl'  => number_format($mediaPerc, 2, ',', '.') . '%',
                'qtd_meses'       => $qtdMeses,
            ],
            'top_meses_criticos' => array_map(fn($i) => [
                'mes'           => $i['MES_ANO'],
                'vencido'       => $fmt($i['VL_SALDO']),
                'inadimplencia' => number_format($i['PC_INADIMPLENCIA'], 2, ',', '.') . '%',
            ], array_slice($dados, 0, 3)),
            'evolucao' => array_reverse(array_map(fn($i) => [
                'mes'           => $i['MES_ANO'],
                'inadimplencia' => $i['PC_INADIMPLENCIA'],
            ], $dados)),
        ];

        $instrucoes = "Você é um analista financeiro especializado em crédito e cobrança.";

        $secoes = "
            📊 VISÃO GERAL
            - 1 frase: panorama da inadimplência (total vencido, média % no período e número de meses analisados).

            📅 MESES CRÍTICOS
            - Liste os 3 meses com maior índice de inadimplência: Mês → % · Valor vencido.

            📉 TENDÊNCIA
            - A inadimplência está melhorando, piorando ou estável? Justifique com base na evolução.

            💡 ALERTA
            - 1 ponto de atenção importante para o gestor financeiro.
        ";

        return [$instrucoes, $contexto, $secoes];
    }

    private function configRelatorioVencidos(array $dados): array
    {
        $item        = $dados[0] ?? [];
        $gerentes    = $item['gerentes']     ?? [];
        $topClientes = $item['top_clientes'] ?? [];
        $totais      = $item['totais']       ?? [];

        $fmt = fn(float $v) => 'R$ ' . number_format($v, 2, ',', '.');

        // Flat lists for cross-hierarchy ranking
        $todasSupv = [];
        $todosVend = [];

        foreach ($gerentes as $g) {
            foreach ($g['supervisores'] ?? [] as $s) {
                $todasSupv[] = [
                    'gerente'       => $g['nome']          ?? '',
                    'supervisor'    => $s['nome']          ?? '',
                    'inadimplencia' => (float)($s['inadimplencia'] ?? 0),
                    'cartorio'      => (float)($s['cartorio']      ?? 0),
                    'saldo'         => (float)($s['saldo']         ?? 0),
                ];
                foreach ($s['top_vendedores'] ?? [] as $v) {
                    $todosVend[] = [
                        'supervisor'  => $s['nome']            ?? '',
                        'vendedor'    => $v['nome']            ?? '',
                        'saldo'       => (float)($v['saldo']   ?? 0),
                        'qtd_clientes'=> (int)($v['qtd_clientes'] ?? 0),
                        'em_cartorio' => (int)($v['em_cartorio']  ?? 0),
                    ];
                }
            }
        }

        usort($gerentes,  fn($a, $b) => ((float)($b['inadimplencia'] ?? 0)) <=> ((float)($a['inadimplencia'] ?? 0)));
        usort($todasSupv, fn($a, $b) => $b['inadimplencia'] <=> $a['inadimplencia']);
        usort($todosVend, fn($a, $b) => $b['saldo'] <=> $a['saldo']);

        $contexto = [
            'totais' => [
                'total_atrasados'     => $fmt((float)($totais['atrasados']           ?? 0)),
                'total_inadimplencia' => $fmt((float)($totais['inadimplencia']       ?? 0)),
                'total_cartorio'      => $fmt((float)($totais['cartorio']            ?? 0)),
                'carteira_ate_60dias' => $fmt((float)($totais['carteira60dias']      ?? 0)),
                'carteira_maior_60'   => $fmt((float)($totais['carteiraMaior60']     ?? 0)),
                'titulos_cartorio'    => (int)($totais['qtd_titulos_cartorio']       ?? 0),
            ],
            'top_gerentes' => array_map(fn($g) => [
                'nome'          => $g['nome']          ?? '',
                'saldo'         => $fmt((float)($g['saldo']         ?? 0)),
                'inadimplencia' => $fmt((float)($g['inadimplencia'] ?? 0)),
                'cartorio'      => $fmt((float)($g['cartorio']      ?? 0)),
            ], array_slice($gerentes, 0, 5)),
            'top_supervisores' => array_map(fn($s) => [
                'gerente'       => $s['gerente'],
                'supervisor'    => $s['supervisor'],
                'inadimplencia' => $fmt($s['inadimplencia']),
                'cartorio'      => $fmt($s['cartorio']),
                'saldo'         => $fmt($s['saldo']),
            ], array_slice($todasSupv, 0, 5)),
            'top_vendedores' => array_map(fn($v) => [
                'supervisor'  => $v['supervisor'],
                'vendedor'    => $v['vendedor'],
                'saldo'       => $fmt($v['saldo']),
                'qtd_clientes'=> $v['qtd_clientes'],
                'em_cartorio' => $v['em_cartorio'],
            ], array_slice($todosVend, 0, 5)),
            'top_clientes' => array_map(fn($c) => [
                'cliente'  => $c['cliente']  ?? '',
                'vendedor' => $c['vendedor'] ?? '',
                'saldo'    => $fmt((float)($c['saldo']   ?? 0)),
                'cartorio' => (float)($c['cartorio'] ?? 0) > 0 ? $fmt((float)$c['cartorio']) : null,
            ], array_slice($topClientes, 0, 10)),
        ];

        $instrucoes = "Você é um analista financeiro especializado em gestão de carteira de crédito e cobrança.";

        $secoes = "
            📊 VISÃO GERAL
            - 1 frase: panorama da carteira vencida (total atrasado, inadimplência e cartório).

            🏆 HIERARQUIA CRÍTICA
            - Top 3 gerentes e top 3 supervisores com maior inadimplência: Nome → Valor.

            💼 VENDEDORES EM DESTAQUE
            - Top 3 vendedores por saldo vencido, indicando quantos clientes em cartório.

            🏢 TOP CLIENTES
            - Liste os 5 maiores clientes inadimplentes: Nome → Saldo · Cartório (se houver).

            💡 AÇÃO PRIORITÁRIA
            - 1 medida concreta e imediata que o gestor financeiro deve tomar.
        ";

        return [$instrucoes, $contexto, $secoes];
    }

    private function configGenerico(array $dados): array
    {
        $instrucoes = "Você é um analista de negócios.";

        $contexto = ['dados' => array_slice($dados, 0, 50)];

        $secoes = "
            📊 VISÃO GERAL
            - Resumo geral dos dados apresentados.

            💡 DESTAQUE
            - 1 insight relevante sobre os dados.
        ";

        return [$instrucoes, $contexto, $secoes];
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
            case 'pneus_coletados':
                return $this->pneusColetados($dtInicio, $dtFim);
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

    public function pneusColetados(string $dtInicio, string $dtFim)
    {
        $dados = $this->pedidoPneu->getColetaPedidoPneu($dtInicio, $dtFim, 1);

        // 9 - RECUSADAS
        $subgrupoRcs = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(9);

        $qtdPneus = array_sum(array_column($dados, 'QTD'));
        $valorTotal = array_sum(array_column($dados, 'VL_TOTAL'));
        $qtdVendedores = count(array_unique(array_column($dados, 'NM_VENDEDOR')));
        $qtdClientes = count(array_unique(array_column($dados, 'NM_PESSOA')));
        $qtdRecusados =
            array_sum(
                array_column(array_filter($dados, function ($item) use ($subgrupoRcs) {
                    return in_array($item->CD_SUBGRUPO, explode(',', $subgrupoRcs['data']));
                }), 'QTD')
            );


        $vendedor = [];
        $cliente = [];
        $desenhoBanda = [];

        foreach ($dados as $item) {
            $nm_vendedor = $item->NM_VENDEDOR ?? 'Sem Vendedor';
            if (!isset($vendedor[$nm_vendedor])) {
                $vendedor[$nm_vendedor] = [
                    'qtd' => 0,
                    'valor' => 0,
                ];
            }
            $vendedor[$nm_vendedor]['qtd'] += $item->QTD;
            $vendedor[$nm_vendedor]['valor'] += $item->VL_TOTAL;


            $nm_cliente = $item->NM_PESSOA ?? 'Sem Cliente';
            if (!isset($cliente[$nm_cliente])) {
                $cliente[$nm_cliente] = [
                    'qtd' => 0,
                    'valor' => 0,
                ];
            }
            $cliente[$nm_cliente]['qtd'] += $item->QTD;
            $cliente[$nm_cliente]['valor'] += $item->VL_TOTAL;

            $desenho = $item->DSDESENHO ?? 'Sem Desenho';
            if (!isset($desenhoBanda[$desenho])) {
                $desenhoBanda[$desenho] = [
                    'qtd' => 0,
                    'valor' => 0,
                ];
            }
            $desenhoBanda[$desenho]['qtd'] += $item->QTD;
            $desenhoBanda[$desenho]['valor'] += $item->VL_TOTAL;
        }

        arsort($vendedor);
        arsort($desenhoBanda);
        arsort($cliente);

        return response()->json([
            'intent' => 'pneus_coletados',
            'tabela' => [
                'titulo' => "Pneus coletados de $dtInicio a $dtFim",
                'dados' => $dados
            ],
            'resumo_ia' => null,
            'componentes' => [
                [
                    'tipo' => 'info_box',
                    'titulo' => 'Total Pneus',
                    'valor' => $qtdPneus,
                    'icone' => 'fas fa-truck',
                    'cor' => 'info'
                ],
                [
                    'tipo' => 'info_box',
                    'titulo' => 'Valor Total',
                    'valor' =>  number_format($valorTotal, 2, ',', '.'),
                    'icone' => 'fas fa-dollar-sign',
                    'cor' => 'success'
                ],
                [
                    'tipo' => 'info_box',
                    'titulo' => 'Vendedores',
                    'valor' => $qtdVendedores,
                    'icone' => 'fas fa-user',
                    'cor' => 'warning'
                ],
                [
                    'tipo' => 'info_box',
                    'titulo' => 'Clientes',
                    'valor' => $qtdClientes,
                    'icone' => 'fas fa-users',
                    'cor' => 'primary'
                ],
                [
                    'tipo' => 'info_box',
                    'titulo' => 'Recusados',
                    'valor' => $qtdRecusados,
                    'icone' => 'fas fa-times',
                    'cor' => 'danger'
                ]
            ],
            'progress_vendedores' => [
                'titulo' => 'Coletas por Vendedor',
                'progress' => array_map(function ($vendedor, $qtd, $valor) use ($qtdPneus, $valorTotal) {
                    return [
                        'nome' => $vendedor,
                        'qtdColetado' => $qtd,
                        'totalPneus' => $qtdPneus,
                        'percQtd' => round(($qtd / $qtdPneus) * 100, 2),
                        'percValor' => round(($valor / $valorTotal) * 100, 2),
                        'valor' => "R$ " . number_format($valor, 2, ',', '.')
                    ];
                }, array_keys($vendedor), array_column($vendedor, 'qtd'), array_column($vendedor, 'valor'))
            ],
            'progress_clientes' => [
                'titulo' => 'Coletas por Cliente',
                'progress' => array_map(function ($cliente, $qtd, $valor) use ($qtdPneus, $valorTotal) {
                    return [
                        'nome' => $cliente,
                        'qtdColetado' => $qtd,
                        'totalPneus' => $qtdPneus,
                        'percQtd' => round(($qtd / $qtdPneus) * 100, 2),
                        'percValor' => round(($valor / $valorTotal) * 100, 2),
                        'valor' => "R$ " . number_format($valor, 2, ',', '.')
                    ];
                }, array_keys($cliente), array_column($cliente, 'qtd'), array_column($cliente, 'valor'))
            ],
            'progress_desenho_banda' => [
                'titulo' => 'Coletas por Desenho/Banda',
                'progress' => array_map(function ($desenho, $qtd, $valor) use ($qtdPneus, $valorTotal) {
                    return [
                        'nome' => $desenho,
                        'qtdColetado' => $qtd,
                        'totalPneus' => $qtdPneus,
                        'percQtd' => round(($qtd / $qtdPneus) * 100, 2),
                        'percValor' => round(($valor / $valorTotal) * 100, 2),
                        'valor' => "R$ " . number_format($valor, 2, ',', '.')
                    ];
                }, array_keys($desenhoBanda), array_column($desenhoBanda, 'qtd'), array_column($desenhoBanda, 'valor'))
            ]

        ]);
    }
}
