<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class AtalhosHomeService
{
    /**
     * Catálogo de todos os atalhos exibidos na home, agrupados por seção.
     * 'visivel' recebe o usuário autenticado e decide se o atalho pode
     * aparecer para ele (mesma regra que antes estava em @haspermission/@role no blade).
     */
    protected function catalogo(): array
    {
        return [
            'Comercial' => [
                [
                    'chave' => 'coleta-geral',
                    'rota' => 'coleta-empresa-geral',
                    'icone' => 'fas fa-truck',
                    'label' => 'Coleta Geral',
                    'visivel' => fn(User $u) => $u->can('ver-coleta-empresa'),
                ],
                [
                    'chave' => 'garantia',
                    'rota' => 'analise-garantia.index',
                    'icone' => 'fas fa-certificate',
                    'label' => 'Garantia',
                    'visivel' => fn(User $u) => $u->can('ver-analise-garantia'),
                ],
                [
                    'chave' => 'prontos-sem-faturar',
                    'rota' => 'produzidos-sem-faturar',
                    'icone' => 'fa fa-exclamation-circle',
                    'label' => 'Prontos S/ Faturar',
                    'visivel' => fn(User $u) => $u->can('ver-produzidos-sem-faturar'),
                ],
                [
                    'chave' => 'acompanha-pedidos',
                    'rota' => 'bloqueio-pedidos',
                    'icone' => 'fas fa-tasks',
                    'label' => 'Acompanha Pedidos',
                    'visivel' => fn(User $u) => $u->can('ver-pedidos-coletados-acompanhamento')
                        || $u->can('ver-pedidos-coletados-acompanhamento-cliente'),
                ],
                [
                    'chave' => 'req-borracharia',
                    'rota' => 'requisicao-borracharia.index',
                    'icone' => 'fas fa-dolly',
                    'label' => 'Req. Borracharia',
                    'visivel' => fn(User $u) => $u->can('ver-requisicao-borracharia'),
                ],
                [
                    'chave' => 'estoque-carcacas',
                    'rota' => 'carcaca-casa',
                    'icone' => 'fas fa-boxes',
                    'label' => 'Estoque Carcacas',
                    'visivel' => fn(User $u) => $u->can('ver-estoque-carcacas'),
                ],
                [
                    'chave' => 'estoque-pneus-novos',
                    'rota' => 'pneus-novos.index',
                    'icone' => 'fas fa-box-open',
                    'label' => 'Pneus Novos',
                    'visivel' => fn(User $u) => $u->can('ver-estoque-carcacas'),
                ],
                [
                    'chave' => 'libera-ordem-comissao',
                    'rota' => 'libera-ordem-comissao.index',
                    'icone' => 'fas fa-check-circle',
                    'label' => 'Liberação Comercial',
                    'visivel' => fn(User $u) => $u->can('ver-libera-ordem-comercial'),
                ],
                [
                    'chave' => 'tabela-preco',
                    'rota' => 'tabela-preco.index',
                    'icone' => 'fas fa-tags',
                    'label' => 'Tabela de Preço',
                    'visivel' => fn(User $u) => $u->can('ver-tabela-preco'),
                ],
            ],
            'Compras' => [
                [
                    'chave' => 'compras-novo-item',
                    'rota' => 'compras.itens-proprios.index',
                    'icone' => 'fas fa-box-open',
                    'label' => 'Novo Item',
                    'visivel' => fn(User $u) => $u->can('compra-itens-proprios'),
                ],
                [
                    'chave' => 'compras-solicitacoes',
                    'rota' => 'compras.solicitacoes.index',
                    'icone' => 'fas fa-file-signature',
                    'label' => 'Solicitações de Compra',
                    'visivel' => fn(User $u) => $u->can('solicitacao-compra-criar')
                        || $u->can('solicitacao-compra-gerenciar'),
                ],
                [
                    'chave' => 'compras-aprovacoes',
                    'rota' => 'compras.aprovacoes.index',
                    'icone' => 'fas fa-clipboard-check',
                    'label' => 'Aprovações Pendentes',
                    'visivel' => fn(User $u) => $u->can('solicitacao-compra-aprovar'),
                ]
            ],
            'Cobrança' => [
                [
                    'chave' => 'financeiro-cliente',
                    'rota' => 'rel-cliente',
                    'icone' => 'fas fa-credit-card',
                    'label' => 'Financeiro Cliente',
                    'visivel' => fn(User $u) => $u->can('ver-rel-cobranca'),
                ],
                [
                    'chave' => 'arquivo-remessa',
                    'rota' => 'arquivo-remessa.index',
                    'icone' => 'fas fa-file-invoice-dollar',
                    'label' => 'Arquivo Remessa',
                    'visivel' => fn(User $u) => $u->can('ver-arquivo-remessa'),
                ],
            ],
            'Financeiro' => [
                [
                    'chave' => 'libera-ordem-financeiro',
                    'rota' => 'libera-ordem-financeiro.index',
                    'icone' => 'fas fa-money-check-alt',
                    'label' => 'Libera Ordem Financeiro',
                    'visivel' => fn(User $u) => $u->can('ver-libera-ordem-financeiro'),
                ],
                [
                    'chave' => 'libera-contas',
                    'rota' => 'libera-contas.index',
                    'icone' => 'fas fa-unlock-alt',
                    'label' => 'Libera Contas',
                    'visivel' => fn(User $u) => $u->can('ver-libera-contas'),
                ],
                [
                    'chave' => 'controle-despesas',
                    'rota' => 'despesa.index',
                    'icone' => 'fas fa-wallet',
                    'label' => 'Controle de Despesas',
                    'visivel' => fn(User $u) => $u->can('ver-despesas'),
                ],
                [
                    'chave' => 'fluxo-caixa',
                    'rota' => 'fluxo-caixa.index',
                    'icone' => 'fas fa-chart-line',
                    'label' => 'Fluxo de Caixa',
                    'visivel' => fn(User $u) => $u->can('ver-fluxo-caixa'),
                ],
            ],
            'Produção' => [
                [
                    'chave' => 'executor-producao',
                    'rota' => 'executor-etapas.index',
                    'icone' => 'fas fa-cogs',
                    'label' => 'Executor x Produção',
                    'visivel' => fn(User $u) => $u->can('ver-producao'),
                ],
                [
                    'chave' => 'painel-pcp',
                    'rota' => 'pneus-lote-pcp',
                    'icone' => 'fas fa-layer-group',
                    'label' => 'Painel PCP',
                    'visivel' => fn(User $u) => $u->can('ver-producao'),
                ],
                [
                    'chave' => 'pedidos-pneus',
                    'rota' => 'pedido-pneus.index',
                    'icone' => 'fas fa-list-alt',
                    'label' => 'Pedidos Pneus',
                    'visivel' => fn(User $u) => $u->can('ver-pedidos-coletados'),
                ],
            ],
            'Faturamento' => [
                [
                    'chave' => 'nota-devolucao',
                    'rota' => 'nota-devolucao.index',
                    'icone' => 'fas fa-file-alt',
                    'label' => 'Nota Devolução',
                    'visivel' => fn(User $u) => $u->can('ver-nota-devolucao'),
                ],
                [
                    'chave' => 'nota-boleto-cliente',
                    'rota' => 'list-notas-emitidas',
                    'icone' => 'fas fa-receipt',
                    'label' => 'Nota e Boleto',
                    'visivel' => fn(User $u) => $u->hasRole('cliente|admin') && $u->can('ver-nota-cliente'),
                ],
                [
                    'chave' => 'pedidos-alterados-valor',
                    'rota' => 'pedidos-alterados-valor',
                    'icone' => 'fas fa-edit',
                    'label' => 'Pedidos Alterados',
                    'visivel' => fn(User $u) => $u->can('ver-pedidos-alterados-valor'),
                ],
                [
                    'chave' => 'nota-vendedor-divergente',
                    'rota' => 'nota-vendedor-divergentes.index',
                    'icone' => 'fas fa-user-times',
                    'label' => 'Nf Vendedor Divergente',
                    'visivel' => fn(User $u) => $u->can('ver-notas-vendedor-divergente'),
                ],
                [
                    'chave' => 'analise-faturista',
                    'rota' => 'analise-faturamento.index',
                    'icone' => 'fas fa-chart-bar',
                    'label' => 'Análise Faturista',
                    'visivel' => fn(User $u) => $u->can('ver-analise-faturamento'),
                ],
            ],
            'Expedição' => [
                [
                    'chave' => 'lote-expedicao',
                    'rota' => 'lote-expedicao.index',
                    'icone' => 'fas fa-dolly-flatbed',
                    'label' => 'Lote de Expedição',
                    'visivel' => fn(User $u) => $u->can('ver-expedicao'),
                ],
            ],
            'Notificações' => [
                [
                    'chave' => 'follow-up',
                    'rota' => 'search-envio',
                    'icone' => 'fas fa-bullhorn',
                    'label' => 'Follow-Up',
                    'visivel' => fn(User $u) => $u->can('ver-follow-up'),
                ],
                [
                    'chave' => 'wppconnect',
                    'rota' => 'wppconnect.index',
                    'icone' => 'fab fa-whatsapp',
                    'label' => 'Whatsapp',
                    'visivel' => fn(User $u) => $u->can('ver-wppconnect'),
                ]
            ],
            'Estoque' => [
                [
                    'chave' => 'estoque-negativo',
                    'rota' => 'estoque-negativo',
                    'icone' => 'fas fa-exclamation-triangle',
                    'label' => 'Itens Negativos',
                    'visivel' => fn(User $u) => $u->can('ver-estoque-negativo'),
                ],
                [
                    'chave' => 'contagem-estoque',
                    'rota' => 'entrada-estoque.index',
                    'icone' => 'fas fa-list-ol',
                    'label' => 'Contagem Estoque',
                    'visivel' => fn(User $u) => $u->can('ver-contagem-estoque'),
                ],
            ],
            'Tarefas' => [
                [
                    'chave' => 'quadro-tarefas',
                    'rota' => 'area-trabalho-tarefas',
                    'icone' => 'fas fa-clipboard-list',
                    'label' => 'Quadro de tarefas',
                    'visivel' => fn(User $u) => $u->can('ver-quadro-tarefa'),
                ],
            ],
        ];
    }

    /**
     * Atalhos que o usuário tem permissão de ver, agrupados por seção,
     * já com a rota resolvida e sem o closure 'visivel'.
     */
    public function disponiveisParaUsuario(User $user): array
    {
        $disponiveis = [];

        foreach ($this->catalogo() as $secao => $itens) {
            $itensVisiveis = [];

            foreach ($itens as $item) {
                // Rota pode não existir ainda no branch/deploy atual (ex.: feature em outra branch) —
                // ignora silenciosamente em vez de quebrar a home inteira com RouteNotFoundException.
                if (! Route::has($item['rota'])) {
                    continue;
                }

                if (! ($item['visivel'])($user)) {
                    continue;
                }

                $itensVisiveis[] = [
                    'chave' => $item['chave'],
                    'url' => route($item['rota']),
                    'icone' => $item['icone'],
                    'label' => $item['label'],
                ];
            }

            if (! empty($itensVisiveis)) {
                $disponiveis[$secao] = $itensVisiveis;
            }
        }

        return $disponiveis;
    }

    /**
     * Atalhos a exibir na home: os favoritados pelo usuário, na ordem escolhida,
     * ou todos os disponíveis caso ele nunca tenha personalizado.
     */
    public function paraHome(User $user): array
    {
        $disponiveis = $this->disponiveisParaUsuario($user);

        $chavesFavoritas = $user->atalhosFavoritos()->pluck('chave_atalho')->all();

        if (empty($chavesFavoritas)) {
            return $disponiveis;
        }

        $todosItens = [];
        foreach ($disponiveis as $secao => $itens) {
            foreach ($itens as $item) {
                $todosItens[$item['chave']] = $item + ['secao' => $secao];
            }
        }

        $resultado = [];
        foreach ($chavesFavoritas as $chave) {
            if (! isset($todosItens[$chave])) {
                continue;
            }

            $item = $todosItens[$chave];
            $secao = $item['secao'];
            unset($item['secao']);

            $resultado[$secao][] = $item;
        }

        return $resultado;
    }

    /**
     * Salva a lista de atalhos favoritados pelo usuário, na ordem enviada.
     * Chaves que não existem no catálogo disponível para o usuário são ignoradas.
     */
    public function salvarFavoritos(User $user, array $chaves): void
    {
        $disponiveis = $this->disponiveisParaUsuario($user);

        $chavesValidas = [];
        foreach ($disponiveis as $itens) {
            foreach ($itens as $item) {
                $chavesValidas[$item['chave']] = true;
            }
        }

        $chaves = array_values(array_intersect($chaves, array_keys($chavesValidas)));

        $user->atalhosFavoritos()->delete();

        foreach ($chaves as $ordem => $chave) {
            $user->atalhosFavoritos()->create([
                'chave_atalho' => $chave,
                'ordem' => $ordem,
            ]);
        }
    }
}
