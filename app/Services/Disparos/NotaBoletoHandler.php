<?php

namespace App\Services\Disparos;

use App\Mail\DisparoAutomaticoMail;
use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class NotaBoletoHandler implements DisparoHandlerInterface
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

        // Carência: só notas com pelo menos N minutos de idade - da tempo
        // habil de cancelar a nota antes do envio automatico pro cliente. A
        // marca d'agua avança até esse mesmo corte (ver DisparoContexto::
        // marcarExecutado()), nunca até "agora" puro, senao uma nota mais
        // nova que a carência ficaria pra trás da marca d'água e nunca mais
        // seria reconsiderada.
        $dtMax = Carbon::parse($contexto->DT_AGORA)
            ->subMinutes((int) config('disparo-automatico.carencia_minutos'))
            ->format('Y-m-d H:i:s');

        $notas = $this->notaModel->getListNotaCliente(
            null,
            null,
            null,
            $dtMin,
            $dtMax
        );

        foreach ($notas as $nota) {
            // Sem e-mail cadastrado, criarPendente() ja registra a linha como
            // 'F'/"Não possui email cadastrado" em vez de pular - fica visivel
            // na tela em vez de sumir silenciosamente.
            $this->criarPendenteDeNota($contexto, $nota);
        }
    }

    public function criarPendenteAvulso(object $contexto, int $nrLancamento, int $cdPessoa): ?int
    {
        $data = $this->notaModel->getListNotaCliente($nrLancamento, (string) $cdPessoa);

        return $this->criarPendenteDeNota($contexto, $data[0]);
    }

    private function criarPendenteDeNota(object $contexto, object $nota): ?int
    {
        return $this->envioModel->criarPendente([
            'cd_contexto'         => $contexto->CD_CONTEXTO,
            'cd_empresa'          => $nota->CD_EMPRESA,
            'nr_lancamento'       => $nota->NR_LANCAMENTO,
            'cd_serie'            => $nota->CD_SERIE,
            'tp_nota'             => $nota->TP_NOTA,
            'cd_pessoa'           => $nota->CD_PESSOA,
            'nm_pessoa'           => $nota->NM_PESSOA,
            'ds_emaildest'        => $nota->DS_EMAIL,
            'ds_emailcopia'       => $nota->DS_EMAILCOPIA,
            'ds_emailrem'         => $nota->DS_EMAILEMPRESA,
            'ds_assunto'          => 'Nota Fiscal ' . $nota->NR_NOTA . ' - ' . $nota->NM_EMPRESA,
            'sem_destino_motivo'  => empty($nota->DS_EMAIL) ? 'Não possui email cadastrado' : null,
        ]);
    }

    /**
     * Envia de fato o e-mail (corpo + PDFs) e decide o resultado do envio -
     * so lanca excecao quando NINGUEM (destinatario nem copia) pode receber,
     * para o job aplicar o retry/release padrao.
     */
    public function enviar(object $envio): void
    {
        // Descobre quem realmente pode receber (destinatário e/ou cópias com
        // domínio válido) sem bloquear o envio por causa de UM endereço ruim -
        // só falha de vez (exceção -> retry) se NINGUÉM for conseguir receber.
        $destinatarioValido = $this->dominioValido($envio->DS_EMAILDEST);

        $copiasBrutas = $this->extrairEmailsCopia($envio->DS_EMAILCOPIA ?? null, $envio->DS_EMAILDEST);
        $copiasValidas = array_values(array_filter($copiasBrutas, fn($e) => $this->dominioValido($e)));
        $copiasInvalidas = array_values(array_diff($copiasBrutas, $copiasValidas));

        if (!$destinatarioValido && empty($copiasValidas)) {
            throw new \RuntimeException(
                'Nenhum destinatário válido (destinatário e cópia(s) com domínio inexistente): ' . $envio->DS_EMAILDEST
            );
        }

        $itens = $this->anexos->estruturaAnexos($envio);
        $corpo = $this->montarCorpo($envio, $itens);
        $anexosGerados = array_map(fn($item) => $this->anexos->gerarAnexoPdf($item), $itens);

        Mail::to($destinatarioValido ? [$envio->DS_EMAILDEST] : [])->cc($copiasValidas)->send(
            new DisparoAutomaticoMail($envio->DS_ASSUNTO, $corpo, $anexosGerados)
        );

        foreach ($anexosGerados as $anexo) {
            $this->envioModel->salvarAnexo($envio->CD_ENVIO, $anexo['titulo'], $anexo['nome']);
        }

        if (!$destinatarioValido || $copiasInvalidas) {
            $motivos = [];

            if (!$destinatarioValido) {
                $motivos[] = "Destinatário com domínio inválido, não recebeu: {$envio->DS_EMAILDEST}";
            }

            if ($copiasInvalidas) {
                $motivos[] = 'Cópia(s) com domínio inválido, ignorada(s): ' . implode(', ', $copiasInvalidas);
            }

            $this->envioModel->marcarEnviadoComFalha($envio->CD_ENVIO, implode(' | ', $motivos));
        } else {
            $this->envioModel->marcarEnviado($envio->CD_ENVIO);
        }
    }

    /**
     * Mesma estrutura de conteudo do envio real, mas sem gerar nenhum PDF - so
     * titulo/nome de cada anexo, pra listar na tela de preview sem custar
     * Chromium (o PDF de cada um so e gerado se o usuario clicar nele).
     */
    public function montarPreview(object $envio): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);

        return [
            'corpo'  => $this->montarCorpo($envio, $itens),
            'anexos' => array_map(fn($item) => [
                'titulo' => $item['titulo'],
                'nome'   => $item['nome'],
            ], $itens),
        ];
    }

    /**
     * Gera o PDF/HTML de UM anexo especifico, sob demanda (clique no botao do
     * preview) - nao toca nos outros anexos.
     */
    public function gerarAnexo(object $envio, int $indice): array
    {
        $itens = $this->anexos->estruturaAnexos($envio);

        if (!isset($itens[$indice])) {
            throw new \OutOfRangeException("Anexo {$indice} não encontrado.");
        }

        return $this->anexos->gerarAnexoPdf($itens[$indice]);
    }

    private function montarCorpo(object $envio, array $itens): string
    {
        $data = $itens[0]['dados'];

        return view('emails.disparo-automatico.nota-boleto', [
            'nmPessoa'  => $envio->NM_PESSOA,
            'nrNota'    => $data[0]->NR_NOTA,
            // count($itens) > 1: se sobrou algum boleto de verdade alem da nota
            // (item 0) - corrige um caso em que o texto dizia "e o(s) boleto(s)"
            // mesmo quando nenhum boleto tinha dado gerado.
            'temBoleto' => count($itens) > 1,
            // Contato da empresa que emitiu a nota (nao da Cambe fixo) - mesma
            // fonte usada no cabecalho da nota (NM_EMPRESA/NR_FONEEMPRESA/DS_EMAILEMPRESA).
            'nmEmpresa'    => $data[0]->NM_EMPRESA,
            'foneEmpresa'  => $data[0]->NR_FONEEMPRESA,
            'emailEmpresa' => $data[0]->DS_EMAILEMPRESA,
        ])->render();
    }

    /**
     * Confere se o domínio do e-mail tem registro DNS (MX ou A) - pega domínio
     * inexistente/digitado errado (ex.: hotmail.co) sem esperar o bounce
     * assíncrono do provedor.
     */
    private function dominioValido(string $email): bool
    {
        $dominio = substr(strrchr($email, '@'), 1);

        return $dominio && (checkdnsrr($dominio, 'MX') || checkdnsrr($dominio, 'A'));
    }

    /**
     * DS_EMAILCOPIA guarda varios enderecos separados por ';' (ex.:
     * "financeiro@x.com.br; fulano@x.com.br") - aqui viram uma lista limpa
     * para o ->cc() e para a validacao de dominio. Descarta qualquer copia
     * igual ao destinatario (sem diferenciar maiusculas/minusculas) para nao
     * mandar o mesmo e-mail duas vezes para a mesma pessoa.
     */
    private function extrairEmailsCopia(?string $copias, string $destinatario): array
    {
        if (!$copias) {
            return [];
        }

        $destinatario = mb_strtolower(trim($destinatario));

        return array_values(array_filter(
            array_map('trim', explode(';', $copias)),
            fn($email) => $email !== '' && mb_strtolower($email) !== $destinatario
        ));
    }
}
