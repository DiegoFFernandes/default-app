<?php

namespace App\Services\Disparos;

interface DisparoHandlerInterface
{
    /**
     * Consulta a fonte de dados do contexto e cria as linhas pendentes em DISPARO_ENVIO
     * (sem enviar nada - o envio de fato é feito por EnviaDisparoAutomaticoJob).
     */
    public function gerarPendentes(object $contexto): void;

    /**
     * Cria a linha pendente para UMA nota especifica, sob demanda (fora da marca
     * d'agua de gerarPendentes()) - usado quando o usuario aciona manualmente o
     * envio de uma nota que ainda nao tem nenhuma linha em DISPARO_ENVIO.
     * Retorna null se ja existir (mesma protecao de criarPendente()).
     */
    public function criarPendenteAvulso(object $contexto, int $nrLancamento, int $cdPessoa): ?int;

    /**
     * Envia de fato um envio pendente ('A'), pelo canal do handler (e-mail,
     * WhatsApp, etc). O proprio handler decide o resultado e atualiza
     * DISPARO_ENVIO (marcarEnviado/marcarEnviadoComFalha) - so lanca excecao
     * quando NADA foi entregue, para o job aplicar o retry/release padrao.
     */
    public function enviar(object $envio): void;

    /**
     * Monta o conteudo (corpo/mensagem + titulos/nomes dos anexos) para a tela
     * de preview, sem gerar nenhum PDF - o PDF de cada anexo so e gerado se o
     * usuario clicar nele (gerarAnexo).
     * Retorna ['corpo' => string, 'anexos' => [['titulo' => string, 'nome' => string], ...]]
     */
    public function montarPreview(object $envio): array;

    /**
     * Gera o PDF/HTML de um unico anexo, pelo indice retornado em montarPreview().
     * Retorna ['titulo' => string, 'nome' => string, 'conteudo' => string, 'html' => string]
     * Lanca \OutOfRangeException se o indice nao existir.
     */
    public function gerarAnexo(object $envio, int $indice): array;
}
