<?php

namespace App\Services\Disparos;

use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
use App\Services\WhatsappCloudService;
use Illuminate\Support\Carbon;

/**
 * Mesmo fluxo do NotaBoletoWppHandler, mas via API oficial (WhatsApp Cloud
 * API / WABA) em vez do WppConnect. Templates aprovados na Meta: nota_cliente
 * (corpo + documento da nota) e boleto_cliente (corpo + documento dos boletos
 * mesclados) - nomes reaproveitados em todos os apps/clientes que usarem WABA.
 */
class NotaBoletoWabaHandler implements DisparoHandlerInterface
{
    private const IDIOMA = 'pt_BR';
    private const TEMPLATE_NOTA = 'nota_cliente';
    private const TEMPLATE_BOLETO = 'boleto_cliente';

    public function __construct(
        private NotaCliente $notaModel,
        private DisparoEnvio $envioModel,
        private NotaBoletoAnexos $anexos,
        private WhatsappCloudService $waba,
    ) {}

    public function gerarPendentes(object $contexto): void
    {
        // Marca d'água: só notas registradas depois da última execução.
        // Na primeira execução (sem última) usa a data de início do contexto.
        $dtMin = Carbon::parse($contexto->DT_ULTIMAEXECUCAO ?: $contexto->DT_INICIOENVIO)
            ->format('Y-m-d H:i:s');

        $notas = $this->notaModel->getListNotaCliente(
            null,
            null,
            null,
            $dtMin
        );

        // NR_LIMITEDIARIO aqui e o tamanho maximo da fila ativa ('A'), nao um
        // contador por dia-calendario: acima disso, os pendentes nascem 'L'
        // (Limite Atingido) em vez de 'A' - mesma regra do canal WppConnect
        // (ver NotaBoletoWppHandler::gerarPendentes).
        $limite = (int) ($contexto->NR_LIMITEDIARIO ?? 0);
        $naFila = $limite > 0 ? $this->envioModel->contarNaFila($contexto->CD_CONTEXTO) : 0;

        foreach ($notas as $nota) {
            $filaCheia = $limite > 0 && $naFila >= $limite;

            $id = $this->criarPendenteDeNota($contexto, $nota, $filaCheia);

            if ($id && !$filaCheia) {
                $naFila++;
            }
        }
    }

    public function criarPendenteAvulso(object $contexto, int $nrLancamento, int $cdPessoa): ?int
    {
        $data = $this->notaModel->getListNotaCliente($nrLancamento, (string) $cdPessoa);

        return $this->criarPendenteDeNota($contexto, $data[0], false);
    }

    private function criarPendenteDeNota(object $contexto, object $nota, bool $filaCheia = false): ?int
    {
        // Celular tem prioridade - telefone fixo normalmente nao tem WhatsApp.
        $telefone = $this->somenteDigitos($nota->NR_CELULAR ?: $nota->NR_FONE);

        return $this->envioModel->criarPendente([
            'cd_contexto'        => $contexto->CD_CONTEXTO,
            'cd_empresa'         => $nota->CD_EMPRESA,
            'nr_lancamento'      => $nota->NR_LANCAMENTO,
            'cd_serie'           => $nota->CD_SERIE,
            'tp_nota'            => $nota->TP_NOTA,
            'cd_pessoa'          => $nota->CD_PESSOA,
            'nm_pessoa'          => $nota->NM_PESSOA,
            'ds_telefone'        => $telefone ?: null,
            'ds_assunto'         => 'Nota Fiscal ' . $nota->NR_NOTA . ' - ' . $nota->NM_EMPRESA,
            'sem_destino_motivo' => $telefone ? null : 'Não possui celular/telefone cadastrado',
            'limite_atingido'    => $filaCheia,
        ]);
    }

    /**
     * Envia o template da nota e, se houver boleto(s) em aberto, o template
     * do(s) boleto(s) em seguida - templates nao dependem da janela de 24h,
     * entao os dois podem ser mandados um atras do outro sem espera. So lanca
     * excecao (-> retry/release do job) quando NADA foi entregue.
     */
    public function enviar(object $envio): void
    {
        $telefone = $this->somenteDigitos($envio->DS_TELEFONE);

        if (strlen($telefone) < 10) {
            // Nao se autocorrige numa proxima tentativa - falha direto, sem
            // consumir NR_TENTATIVAS. Usuario corrige o telefone e reenvia.
            $this->envioModel->marcarFalhaDefinitiva($envio->CD_ENVIO, "Telefone inválido: {$envio->DS_TELEFONE}");
            return;
        }

        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->anexos->agruparParaEnvio($itens);
        $dados = $itens[0]['dados'][0];

        $falhas = [];
        $anexosEnviados = [];

        foreach ($grupos as $grupo) {
            $anexo = $this->anexos->gerarAnexoDoGrupo($grupo);
            $resposta = $this->enviarGrupo($telefone, $grupo, $anexo, $dados);

            if ($this->sucesso($resposta)) {
                $anexosEnviados[] = $anexo;
            } else {
                $falhas[] = $anexo['titulo'] . ': ' . $this->mensagemErro($resposta);
            }
        }

        foreach ($anexosEnviados as $anexo) {
            $this->envioModel->salvarAnexo($envio->CD_ENVIO, $anexo['titulo'], $anexo['nome']);
        }

        if (empty($anexosEnviados)) {
            throw new \RuntimeException("Falha ao enviar templates via WhatsApp (WABA) para {$telefone}.");
        }

        if ($falhas) {
            $this->envioModel->marcarEnviadoComFalha($envio->CD_ENVIO, 'Anexo(s) não entregue(s): ' . implode(' | ', $falhas));
        } else {
            $this->envioModel->marcarEnviado($envio->CD_ENVIO);
        }
    }

    private function enviarGrupo(string $telefone, array $grupo, array $anexo, object $dados): array
    {
        $mediaId = $this->waba->enviarMidia($anexo['conteudo'], $anexo['nome']);

        if (!$mediaId) {
            return ['error' => ['message' => 'Falha ao subir o PDF na API de mídia da Meta.']];
        }

        $documento = ['type' => 'document', 'document' => ['id' => $mediaId, 'filename' => $anexo['nome']]];

        if ($grupo['tipo'] === 'nota') {
            $fone = trim($dados->NR_CELULAREMPRESA ?: $dados->NR_FONEEMPRESA);
            $componentes = [
                ['type' => 'header', 'parameters' => [$documento]],
                ['type' => 'body', 'parameters' => [
                    ['type' => 'text', 'text' => $dados->NM_PESSOA],
                    ['type' => 'text', 'text' => (string) $dados->NR_NOTA],
                    ['type' => 'text', 'text' => $fone],
                    ['type' => 'text', 'text' => strtolower($dados->DS_EMAILEMPRESA)],
                ]],
            ];

            return $this->waba->enviarTemplate($telefone, self::TEMPLATE_NOTA, self::IDIOMA, $componentes);
        }

        $componentes = [
            ['type' => 'header', 'parameters' => [$documento]],
            ['type' => 'body', 'parameters' => [
                ['type' => 'text', 'text' => (string) $dados->NR_NOTA],
            ]],
        ];

        return $this->waba->enviarTemplate($telefone, self::TEMPLATE_BOLETO, self::IDIOMA, $componentes);
    }

    public function montarPreview(object $envio): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->anexos->agruparParaEnvio($itens);
        $dados = $itens[0]['dados'][0];

        $corpo = "[Template " . self::TEMPLATE_NOTA . "] Olá, {$dados->NM_PESSOA}! Segue a Nota Fiscal {$dados->NR_NOTA} e o(s) boleto(s) para pagamento, em anexo.";

        if (count($grupos) > 1) {
            $corpo .= "\n\n[Template " . self::TEMPLATE_BOLETO . "] Segue o(s) boleto(s) referente(s) à Nota Fiscal {$dados->NR_NOTA}, em anexo.";
        }

        return [
            'corpo'  => $corpo,
            'anexos' => array_map(fn($grupo) => [
                'titulo' => $grupo['titulo'],
                'nome'   => $grupo['nome'],
            ], $grupos),
        ];
    }

    public function gerarAnexo(object $envio, int $indice): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->anexos->agruparParaEnvio($itens);

        if (!isset($grupos[$indice])) {
            throw new \OutOfRangeException("Anexo {$indice} não encontrado.");
        }

        return $this->anexos->gerarAnexoDoGrupo($grupos[$indice]);
    }

    private function sucesso(array $resposta): bool
    {
        return !isset($resposta['error']) && isset($resposta['messages']);
    }

    private function mensagemErro(array $resposta): string
    {
        return $resposta['error']['error_data']['details'] ?? $resposta['error']['message'] ?? 'erro desconhecido';
    }

    private function somenteDigitos(?string $valor): string
    {
        return preg_replace('/\D/', '', (string) $valor) ?? '';
    }
}
