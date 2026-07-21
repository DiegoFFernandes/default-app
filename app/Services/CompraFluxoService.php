<?php

namespace App\Services;

use App\Models\CompraSolicitacao;
use App\Models\CompraSolicitacaoItem;
use App\Models\CompraCotacao;
use App\Models\CompraConfigFaixa;
use App\Models\CompraConfigAprov;
use App\Models\CompraEtapaAprov;
use App\Models\CompraParamEmpresa;
use App\Models\User;
use App\Services\WppConnectService;

class CompraFluxoService
{
    public function __construct(
        protected CompraSolicitacao     $solicitacao,
        protected CompraSolicitacaoItem $solicitacaoItem,
        protected CompraCotacao         $cotacao,
        protected CompraConfigFaixa     $configFaixa,
        protected CompraConfigAprov     $configAprov,
        protected CompraEtapaAprov      $etapaAprov,
        protected CompraParamEmpresa    $paramEmpresa
    ) {}

    public function validarParaSubmissao($id): array
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao) {
            return ['errors' => 'Solicitação não encontrada.'];
        }

        if (!in_array($solicitacao->ST_SOLICITACAO, ['RAS', 'ANA'])) {
            return ['errors' => 'Apenas solicitações em Rascunho ou Análise de Compra podem ser enviadas para aprovação.'];
        }

        $itens = $this->solicitacaoItem->getBySolicitacao($id);
        if (empty($itens)) {
            return ['errors' => 'Adicione pelo menos 1 item antes de enviar para aprovação.'];
        }

        $qtCotacoes = $this->cotacao->countBySolicitacao($id);
        $qtMinCot   = $this->paramEmpresa->getQtdFornecCot($solicitacao->CD_EMPRESA);
        if ($qtCotacoes < $qtMinCot) {
            return ['errors' => "São necessárias no mínimo {$qtMinCot} cotações. Cadastradas: {$qtCotacoes}."];
        }

        $cotacaoSel = $this->cotacao->getCotacaoSelecionada($id);
        if (!$cotacaoSel) {
            return ['errors' => 'Selecione um fornecedor (Ganhador) antes de enviar para aprovação.'];
        }

        if (empty(trim($cotacaoSel->DS_MOTIVO_ESCOLHA ?? ''))) {
            return ['errors' => 'Informe o motivo da escolha do fornecedor.'];
        }

        if (empty($solicitacao->CD_CENTROCUSTO)) {
            return ['errors' => 'Informe o centro de resultado da solicitação antes de enviar para aprovação.'];
        }

        $faixa = $this->configFaixa->findFaixaByValorCentro(
            (int) $solicitacao->CD_EMPRESA,
            (float) $cotacaoSel->VL_TOTAL,
            (int) $solicitacao->CD_CENTROCUSTO
        );

        if (!$faixa) {
            $vlFmt = number_format((float) $cotacaoSel->VL_TOTAL, 2, ',', '.');
            return ['errors' => "Não há faixa de aprovação com aprovadores cadastrados para o centro de resultado \"{$solicitacao->DS_CENTROCUSTO}\" no valor de R$ {$vlFmt}. Contate o administrador."];
        }

        $aprovadores = $this->configAprov->getByFaixaCentro(
            (int) $faixa->ID_FAIXA,
            (int) $solicitacao->CD_EMPRESA,
            (int) $solicitacao->CD_CENTROCUSTO
        );

        if (empty($aprovadores)) {
            return ['errors' => "Não há aprovadores configurados para o centro de resultado \"{$solicitacao->DS_CENTROCUSTO}\" nesta faixa de valor. Contate o administrador."];
        }

        return ['success' => true, 'faixa' => $faixa, 'cotacao' => $cotacaoSel, 'aprovadores' => $aprovadores];
    }

    public function submeter($id, $cdUsuario): array
    {
        $validacao = $this->validarParaSubmissao($id);

        if (isset($validacao['errors'])) {
            return $validacao;
        }

        $cotacao     = $validacao['cotacao'];
        $aprovadores = $validacao['aprovadores'];

        $this->solicitacao->updateFornecedorTotal($id, $cotacao->CD_FORNECEDOR, $cotacao->VL_TOTAL);

        $userIds = collect($aprovadores)->pluck('CD_USUARIO')->unique();
        $users   = User::whereIn('id', $userIds)->pluck('name', 'id');

        $etapasCriadas = [];
        foreach ($aprovadores as $aprov) {
            $idEtapa = $this->etapaAprov->store([
                'cd_solicitacao'       => $id,
                'nr_ordem'             => $aprov->NR_ORDEM,
                'ds_cargo'             => $aprov->DS_CARGO,
                'cd_usuario_aprovador' => $aprov->CD_USUARIO,
                'nm_aprovador'         => $users[$aprov->CD_USUARIO] ?? null,
            ]);
            $etapasCriadas[] = [
                'id_etapa'   => $idEtapa,
                'cd_usuario' => $aprov->CD_USUARIO,
                'ds_cargo'   => $aprov->DS_CARGO,
            ];
        }

        $this->solicitacao->updateStatus($id, 'APR');

        $this->dispararNotificacoesAprovadores($id, $etapasCriadas, $cotacao->VL_TOTAL);

        return ['success' => 'Solicitação enviada para aprovação com sucesso!'];
    }

    private function dispararNotificacoesAprovadores(int $idSolicitacao, array $etapas, float $vlTotal): void
    {
        try {
            $solicitacao = $this->solicitacao->findById($idSolicitacao);
            $solicitante = User::find($solicitacao->CD_USUARIO_SOLICITANTE);
            $itens       = $this->solicitacaoItem->getBySolicitacao($idSolicitacao);

            (new WppConnectService())->notificarAprovadores(
                idSolicitacao: $idSolicitacao,
                nmSolicitante: $solicitante?->name ?? 'Solicitante',
                nmEmpresa:     $solicitacao->NM_EMPRESA ?? '',
                vlTotal:       (float) $vlTotal,
                itens:         $itens,
                aprovadores:   $etapas,
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WppConnect: falha ao notificar aprovadores', [
                'id_solicitacao' => $idSolicitacao,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
