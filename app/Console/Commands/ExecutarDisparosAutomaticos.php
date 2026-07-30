<?php

namespace App\Console\Commands;

use App\Jobs\EnviaDisparoAutomaticoJob;
use App\Models\DisparoContexto;
use App\Models\DisparoEnvio;
use App\Services\Disparos\DisparoHandlerRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExecutarDisparosAutomaticos extends Command
{
    protected $signature = 'disparo-automatico:executar
        {--dry-run : Gera os envios pendentes mas nao enfileira o disparo de e-mail}
        {--force : Ignora o DT_PROXIMAEXECUCAO (intervalo/horario configurado) e verifica os contextos ativos agora}';

    protected $description = 'Verifica os contextos de disparo automatico ativos e dispara os e-mails pendentes';

    public function handle(
        DisparoContexto $contextoModel,
        DisparoEnvio $envioModel,
        DisparoHandlerRegistry $registry
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $contextos = $contextoModel->ativosParaExecucao($force);

        if (empty($contextos)) {
            $this->info('Nenhum contexto ativo pendente de execucao no momento.');
            return self::SUCCESS;
        }

        foreach ($contextos as $contexto) {
            $this->info("Processando contexto: {$contexto->DS_CONTEXTO}");

            try {
                $handler = $registry->resolve($contexto->CD_HANDLER);
                $handler->gerarPendentes($contexto);
            } catch (Throwable $e) {
                Log::error("[DisparoAutomatico] Falha ao gerar pendentes do contexto {$contexto->CD_CONTEXTO}: " . $e->getMessage());
                $this->error("Falha ao gerar pendentes: " . $e->getMessage());
                continue;
            }

            $pendentes = $envioModel->pendentes($contexto->CD_CONTEXTO);
            $this->info(count($pendentes) . ' envio(s) pendente(s) para este contexto.');

            // Em dry-run NÃO avança a marca d'água nem enfileira: assim a próxima
            // execução real reprocessa a mesma janela (a constraint UNIQUE evita
            // duplicar os pendentes já criados) e só então marca como executado.
            if ($dryRun) {
                continue;
            }

            foreach ($pendentes as $pendente) {
                EnviaDisparoAutomaticoJob::dispatch($pendente->CD_ENVIO, $contexto->CD_HANDLER);
            }

            // --force processa fora do ciclo normal sem mexer no agendamento -
            // nao avança DT_ULTIMAEXECUCAO/DT_PROXIMAEXECUCAO, entao a proxima
            // execucao automatica continua no horario que ja estava calculado.
            if ($force) {
                continue;
            }

            $contextoModel->marcarExecutado(
                $contexto->CD_CONTEXTO,
                $contexto->DT_AGORA,
                (int) $contexto->NR_INTERVALOHORAS,
                $contexto->HR_EXECUCAO
            );
        }

        return self::SUCCESS;
    }
}
