<?php

namespace App\Services;

use App\Models\CompraEtapaAprov;
use App\Models\CompraParamEmpresa;
use App\Models\CompraSolicitacao;

class CompraAprovacaoService
{
    public function __construct(
        protected CompraEtapaAprov  $etapaAprov,
        protected CompraSolicitacao $solicitacao,
        protected CompraParamEmpresa $paramEmpresa,
        protected WppConnectService  $wpp
    ) {}

    public function aprovar($idEtapa, $cdUsuario, $obs): array
    {
        $etapa = $this->etapaAprov->findById($idEtapa);

        if (!$etapa) {
            return ['errors' => 'Etapa não encontrada.'];
        }

        if ($etapa->ST_ETAPA !== 'PEN') {
            return ['errors' => 'Esta etapa já foi processada.'];
        }

        if ((int)$etapa->CD_USUARIO_APROVADOR !== (int)$cdUsuario) {
            return ['errors' => 'Você não tem permissão para aprovar esta etapa.'];
        }

        if ((int)$etapa->NR_ORDEM > 1) {
            $todasEtapas = $this->etapaAprov->getBySolicitacao($etapa->CD_SOLICITACAO);
            foreach ($todasEtapas as $e) {
                if ((int)$e->NR_ORDEM < (int)$etapa->NR_ORDEM && $e->ST_ETAPA !== 'APR') {
                    return ['errors' => "Aguardando aprovação da etapa {$e->NR_ORDEM} — {$e->DS_CARGO}."];
                }
            }
        }

        $this->etapaAprov->updateEtapa($idEtapa, 'APR', $cdUsuario, $obs);
        $this->verificarConclusao($etapa->CD_SOLICITACAO);

        return ['success' => 'Etapa aprovada com sucesso!'];
    }

    public function reprovar($idEtapa, $cdUsuario, $obs): array
    {
        $etapa = $this->etapaAprov->findById($idEtapa);

        if (!$etapa) {
            return ['errors' => 'Etapa não encontrada.'];
        }

        if ($etapa->ST_ETAPA !== 'PEN') {
            return ['errors' => 'Esta etapa já foi processada.'];
        }

        if ((int)$etapa->CD_USUARIO_APROVADOR !== (int)$cdUsuario) {
            return ['errors' => 'Você não tem permissão para reprovar esta etapa.'];
        }

        if (empty(trim($obs ?? ''))) {
            return ['errors' => 'O motivo da reprovação é obrigatório.'];
        }

        $this->etapaAprov->updateEtapa($idEtapa, 'REP', $cdUsuario, $obs);
        $this->solicitacao->updateStatus($etapa->CD_SOLICITACAO, 'REP');

        try {
            $sol       = $this->solicitacao->findById((int) $etapa->CD_SOLICITACAO);
            $comprador = $this->paramEmpresa->getCompradorByEmpresa((int) $sol->CD_EMPRESA);

            if ($comprador && !empty($comprador->NR_CELULAR)) {
                $this->wpp->notificarCompradorReprovacao(
                    (int) $etapa->CD_SOLICITACAO,
                    $sol->NM_EMPRESA,
                    $obs,
                    $comprador->NR_CELULAR
                );
            }
        } catch (\Throwable) {
            // notificação falhou mas não impede o fluxo
        }

        return ['success' => 'Solicitação reprovada.'];
    }

    private function verificarConclusao($idSolicitacao): void
    {
        if (!$this->etapaAprov->allApproved($idSolicitacao)) {
            return;
        }

        $this->solicitacao->updateStatus($idSolicitacao, 'APC');

        try {
            $sol       = $this->solicitacao->findById((int) $idSolicitacao);
            $comprador = $this->paramEmpresa->getCompradorByEmpresa((int) $sol->CD_EMPRESA);

            if ($comprador && !empty($comprador->NR_CELULAR)) {
                $this->wpp->notificarCompradorAprovacao(
                    (int) $idSolicitacao,
                    $sol->NM_EMPRESA,
                    (float) ($sol->VL_TOTAL ?? 0),
                    $comprador->NR_CELULAR
                );
            }
        } catch (\Throwable) {
            // notificação falhou mas não impede o fluxo
        }
    }
}
