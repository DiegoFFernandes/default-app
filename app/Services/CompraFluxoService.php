<?php

namespace App\Services;

use App\Models\CompraSolicitacao;
use App\Models\CompraSolicitacaoItem;
use App\Models\CompraCotacao;
use App\Models\CompraConfigFaixa;
use App\Models\CompraConfigAprov;
use App\Models\CompraEtapaAprov;

class CompraFluxoService
{
    public function __construct(
        protected CompraSolicitacao     $solicitacao,
        protected CompraSolicitacaoItem $solicitacaoItem,
        protected CompraCotacao         $cotacao,
        protected CompraConfigFaixa     $configFaixa,
        protected CompraConfigAprov     $configAprov,
        protected CompraEtapaAprov      $etapaAprov
    ) {}

    public function validarParaSubmissao($id): array
    {
        $solicitacao = $this->solicitacao->findById($id);

        if (!$solicitacao) {
            return ['errors' => 'Solicitação não encontrada.'];
        }

        if ($solicitacao->ST_SOLICITACAO !== 'RAS') {
            return ['errors' => 'Apenas solicitações em Rascunho podem ser enviadas.'];
        }

        $itens = $this->solicitacaoItem->getBySolicitacao($id);
        if (empty($itens)) {
            return ['errors' => 'Adicione pelo menos 1 item antes de enviar para aprovação.'];
        }

        $qtCotacoes = $this->cotacao->countBySolicitacao($id);
        if ($qtCotacoes < 3) {
            return ['errors' => "São necessárias no mínimo 3 cotações. Cadastradas: {$qtCotacoes}."];
        }

        $cotacaoSel = $this->cotacao->getCotacaoSelecionada($id);
        if (!$cotacaoSel) {
            return ['errors' => 'Selecione um fornecedor (Ganhador) antes de enviar para aprovação.'];
        }

        if (empty(trim($cotacaoSel->DS_MOTIVO_ESCOLHA ?? ''))) {
            return ['errors' => 'Informe o motivo da escolha do fornecedor.'];
        }

        $faixa = $this->configFaixa->findFaixaByValor($solicitacao->CD_EMPRESA, $cotacaoSel->VL_TOTAL);
        if (!$faixa) {
            return ['errors' => 'Não há configuração de aprovação para este valor. Contate o administrador.'];
        }

        $aprovadores = $this->configAprov->getByFaixa($faixa->ID_FAIXA);
        if (empty($aprovadores)) {
            return ['errors' => 'A faixa de aprovação não possui aprovadores configurados. Contate o administrador.'];
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

        foreach ($aprovadores as $aprov) {
            $this->etapaAprov->store([
                'cd_solicitacao'       => $id,
                'nr_ordem'             => $aprov->NR_ORDEM,
                'ds_cargo'             => $aprov->DS_CARGO,
                'cd_usuario_aprovador' => $aprov->CD_USUARIO,
            ]);
        }

        $this->solicitacao->updateStatus($id, 'APR');

        return ['success' => 'Solicitação enviada para aprovação com sucesso!'];
    }
}
