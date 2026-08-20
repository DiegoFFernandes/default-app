<?php

namespace App\Services\Disparos;

use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
use App\Services\WppConnectService;
use Illuminate\Support\Carbon;

class NotaBoletoWppHandler implements DisparoHandlerInterface
{
    public function __construct(
        private NotaCliente $notaModel,
        private DisparoEnvio $envioModel,
        private NotaBoletoAnexos $anexos,
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
        // (Limite Atingido) em vez de 'A' - ficam visiveis na tela, com motivo
        // claro, e so voltam a fila se o usuario reenviar manualmente (ver
        // DisparoAutomaticoController::reenviarEnvio, que despacha na hora
        // pra esses casos, sem esperar o comando automatico).
        $limite = (int) ($contexto->NR_LIMITEDIARIO ?? 0);
        $naFila = $limite > 0 ? $this->envioModel->contarNaFila($contexto->CD_CONTEXTO) : 0;

        foreach ($notas as $nota) {
            $filaCheia = $limite > 0 && $naFila >= $limite;

            // Sem celular cadastrado, criarPendente() ja registra a linha como
            // 'F'/"Não possui celular..." em vez de pular - fica visivel na
            // tela em vez de sumir silenciosamente.
            $id = $this->criarPendenteDeNota($contexto, $nota, $filaCheia);

            // So conta contra a fila quando de fato nasceu 'A' (nao 'F' nem 'L').
            if ($id && !$filaCheia) {
                $naFila++;
            }
        }
    }

    /**
     * Criacao manual (avulsa) de um pendente - acao explicita e pontual do
     * usuario, nao a marca d'agua automatica. Sempre nasce 'A' (bypass do
     * limite diario), igual o reenvio manual tambem bypassa.
     */
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
     * Envia a mensagem de texto + os PDFs (nota/boletos) como arquivo via
     * WhatsApp. So lanca excecao (-> retry/release do job) quando NADA foi
     * entregue - se pelo menos o texto ou um anexo chegou, fica como "enviado
     * com ressalva" (mesmo padrao do e-mail com copia invalida).
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

        $wpp = WppConnectService::forModulo('cobranca');

        if (!$wpp->isConnected()) {
            // Isso sim pode se resolver sozinho (sessao volta) - continua
            // lancando excecao, entra no retry/release padrao do job.
            throw new \RuntimeException('Sessão do WhatsApp (Cobrança) está desconectada.');
        }

        if (!$wpp->numberExists($telefone)) {
            // Mesmo raciocinio do telefone invalido - numero sem WhatsApp nao
            // vai passar a ter numa proxima tentativa automatica.
            $this->envioModel->marcarFalhaDefinitiva($envio->CD_ENVIO, "Número não possui WhatsApp ativo: {$telefone}");
            return;
        }

        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->agruparParaEnvio($itens);
        $mensagem = $this->montarMensagem($envio, $itens);

        $textoEnviado = $this->sucesso($wpp->sendText($telefone, $mensagem));

        $falhas = [];
        $anexosEnviados = [];

        foreach ($grupos as $grupo) {
            $anexo = $this->gerarAnexoDoGrupo($grupo);

            $resposta = $wpp->sendFile(
                $telefone,
                'data:application/pdf;base64,' . base64_encode($anexo['conteudo']),
                $anexo['nome'],
                $anexo['titulo']
            );

            if ($this->sucesso($resposta)) {
                $anexosEnviados[] = $anexo;
            } else {
                $falhas[] = $anexo['titulo'];
            }
        }

        foreach ($anexosEnviados as $anexo) {
            $this->envioModel->salvarAnexo($envio->CD_ENVIO, $anexo['titulo'], $anexo['nome']);
        }

        if (!$textoEnviado && empty($anexosEnviados)) {
            throw new \RuntimeException("Falha ao enviar mensagem e anexos via WhatsApp para {$telefone}.");
        }

        if (!$textoEnviado || $falhas) {
            $motivos = [];

            if (!$textoEnviado) {
                $motivos[] = 'Mensagem de texto não entregue.';
            }

            if ($falhas) {
                $motivos[] = 'Anexo(s) não entregue(s): ' . implode(', ', $falhas);
            }

            $this->envioModel->marcarEnviadoComFalha($envio->CD_ENVIO, implode(' | ', $motivos));
        } else {
            $this->envioModel->marcarEnviado($envio->CD_ENVIO);
        }
    }

    public function montarPreview(object $envio): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->agruparParaEnvio($itens);

        return [
            'corpo'  => $this->montarMensagem($envio, $itens),
            'anexos' => array_map(fn($grupo) => [
                'titulo' => $grupo['titulo'],
                'nome'   => $grupo['nome'],
            ], $grupos),
        ];
    }

    public function gerarAnexo(object $envio, int $indice): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);
        $grupos = $this->agruparParaEnvio($itens);

        if (!isset($grupos[$indice])) {
            throw new \OutOfRangeException("Anexo {$indice} não encontrado.");
        }

        return $this->gerarAnexoDoGrupo($grupos[$indice]);
    }

    /**
     * Agrupa nota + boletos em no maximo 2 anexos: a API oficial do WhatsApp
     * (WABA) so aceita 1 documento por template, entao todos os boletos em
     * aberto viram um unico PDF (paginas separadas por quebra de pagina) -
     * ver NotaBoletoAnexos::gerarAnexoPdfBoletos(). E-mail nao usa este
     * metodo, continua recebendo cada boleto como anexo avulso.
     */
    private function agruparParaEnvio(array $itens): array
    {
        $nrNota = $itens[0]['dados'][0]->NR_NOTA;

        $grupos = [
            ['tipo' => 'nota', 'titulo' => $itens[0]['titulo'], 'nome' => $itens[0]['nome'], 'item' => $itens[0]],
        ];

        $itensBoletos = array_values(array_filter($itens, fn($item) => $item['tipo'] === 'boleto'));

        if ($itensBoletos) {
            $grupos[] = [
                'tipo'         => 'boletos',
                'titulo'       => 'Boleto(s) ' . $nrNota,
                'nome'         => "Boletos_{$nrNota}.pdf",
                'itensBoletos' => $itensBoletos,
                'nrNota'       => $nrNota,
            ];
        }

        return $grupos;
    }

    private function gerarAnexoDoGrupo(array $grupo): array
    {
        return $grupo['tipo'] === 'nota'
            ? $this->anexos->gerarAnexoPdf($grupo['item'])
            : $this->anexos->gerarAnexoPdfBoletos($grupo['itensBoletos'], $grupo['nrNota']);
    }

    private function montarMensagem(object $envio, array $itens): string
    {
        $data = $itens[0]['dados'];

        return trim(view('whatsapp.disparo-automatico.nota-boleto', [
            'nmPessoa' => $envio->NM_PESSOA,
            'nrNota'   => $data[0]->NR_NOTA,
            // Calculado aqui (em vez de @if/@endif na view) porque Blade nao
            // reconhece @endif colado direto numa palavra sem espaço antes -
            // texto puro (sem HTML) sofre mais com isso do que o template de e-mail.
            'complementoNota' => count($itens) > 1 ? ' e o(s) boleto(s) para pagamento' : '',
            'foneEmpresa'     => $data[0]->NR_CELULAREMPRESA ?: $data[0]->NR_FONEEMPRESA,
            'emailEmpresa'    => $data[0]->DS_EMAILEMPRESA,
        ])->render());
    }

    private function sucesso(?array $resposta): bool
    {
        return ($resposta['status'] ?? null) === 'success';
    }

    private function somenteDigitos(?string $valor): string
    {
        return preg_replace('/\D/', '', (string) $valor) ?? '';
    }
}
