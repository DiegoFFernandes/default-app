<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraEtapaAprov;
use App\Models\CompraSolicitacao;
use App\Models\CompraSolicitacaoItem;
use App\Models\WppDisparo;
use App\Services\CompraAprovacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WppAcaoController extends Controller
{
    public function __construct(
        private CompraAprovacaoService $aprovacaoService,
        private CompraEtapaAprov       $etapaAprov,
        private CompraSolicitacao      $solicitacao,
        private CompraSolicitacaoItem  $solicitacaoItem,
    ) {}

    public function show(Request $request)
    {
        $token = $request->query('token');
        $acao  = $request->query('acao'); // aprovar | reprovar | null (página inicial)

        ['disparo' => $disparo, 'erro' => $erro] = $this->buscarDisparo($token);

        if ($erro) {
            return view('admin.wppconnect.acao', compact('erro'));
        }

        $etapa       = $this->etapaAprov->findById($disparo->referencia_id);
        $solicitacao = $this->solicitacao->findById($etapa->CD_SOLICITACAO);
        $itens       = $this->solicitacaoItem->getBySolicitacao($etapa->CD_SOLICITACAO);

        return view('admin.wppconnect.acao', compact(
            'disparo', 'etapa', 'solicitacao', 'itens', 'acao', 'token'
        ));
    }

    public function processar(Request $request)
    {
        $token  = $request->input('token');
        $acao   = $request->input('acao');
        $motivo = $request->input('motivo', '');

        if (!in_array($acao, ['aprovar', 'reprovar'])) {
            return view('admin.wppconnect.acao', ['erro' => 'Ação inválida.']);
        }

        ['disparo' => $disparo, 'erro' => $erro] = $this->buscarDisparo($token);

        if ($erro) {
            return view('admin.wppconnect.acao', compact('erro'));
        }

        $result = $acao === 'aprovar'
            ? $this->aprovacaoService->aprovar($disparo->referencia_id, $disparo->user_id, $motivo ?: null)
            : $this->aprovacaoService->reprovar($disparo->referencia_id, $disparo->user_id, $motivo);

        if (isset($result['errors'])) {
            $erro = $result['errors'];
            return view('admin.wppconnect.acao', compact('erro'));
        }

        $disparo->update(['token' => null]);

        $sucesso = $acao === 'aprovar' ? 'Solicitação aprovada com sucesso!' : 'Solicitação reprovada.';

        return view('admin.wppconnect.acao', compact('sucesso', 'acao'));
    }

    private function buscarDisparo(?string $token): array
    {
        if (!$token) {
            return ['disparo' => null, 'erro' => 'Link inválido — token não informado.'];
        }

        $disparo = WppDisparo::where('token', $token)
            ->whereNotNull('token')
            ->where('referencia_tipo', 'compra_etapa')
            ->first();

        if (!$disparo) {
            return ['disparo' => null, 'erro' => 'Este link já foi utilizado ou não existe.'];
        }

        if ((int) $disparo->user_id !== (int) Auth::id()) {
            return [
                'disparo' => null,
                'erro'    => 'Este link de aprovação pertence a outro usuário. Faça login com o usuário correto.',
            ];
        }

        return ['disparo' => $disparo, 'erro' => null];
    }
}
