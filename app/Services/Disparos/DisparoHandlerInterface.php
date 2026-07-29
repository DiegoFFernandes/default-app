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
     * Monta o conteudo do e-mail (corpo + anexos em PDF) para um envio pendente.
     * Retorna ['corpo' => string, 'anexos' => [['titulo' => string, 'path' => string, 'nome' => string], ...]]
     */
    public function montarEmail(object $envio): array;

    /**
     * Cria a linha pendente para UMA nota especifica, sob demanda (fora da marca
     * d'agua de gerarPendentes()) - usado quando o usuario aciona manualmente o
     * envio de uma nota que ainda nao tem nenhuma linha em DISPARO_ENVIO.
     * Retorna null se ja existir (mesma protecao de criarPendente()).
     */
    public function criarPendenteAvulso(object $contexto, int $nrLancamento, int $cdPessoa): ?int;
}
