<?php

namespace App\Console\Commands;

use App\Jobs\EnviaDisparoAutomaticoJob;
use App\Models\DisparoContexto;
use App\Models\DisparoEnvio;
use App\Services\Disparos\DisparoHandlerRegistry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExecutarDisparosWhatsApp extends Command
{
    protected $signature = 'disparo-automatico:executar-whatsapp';

    protected $description = 'Verifica os contextos de disparo automatico via WhatsApp ativos e envia, no maximo, um pendente por contexto a cada execucao (ritmo controlado)';

    public function handle(
        DisparoContexto $contextoModel,
        DisparoEnvio $envioModel,
        DisparoHandlerRegistry $registry
    ): int {
        $contextos = $contextoModel->ativosWpp();

        if (empty($contextos)) {
            $this->info('Nenhum contexto WhatsApp ativo no momento.');
            return self::SUCCESS;
        }

        foreach ($contextos as $contexto) {
            try {
                $handler = $registry->resolve($contexto->CD_HANDLER);
                $handler->gerarPendentes($contexto);
            } catch (Throwable $e) {
                Log::error("[DisparoWhatsApp] Falha ao gerar pendentes do contexto {$contexto->CD_CONTEXTO}: " . $e->getMessage());
                $this->error("Falha ao gerar pendentes: " . $e->getMessage());
                continue;
            }

            // So a marca d'agua avanca aqui - esse canal nao usa DT_PROXIMAEXECUCAO,
            // entao o watermark sempre avanca, mesmo quando nada e enviado neste tick
            // (janela fechada/limite atingido/espacamento ainda nao cumprido).
            $contextoModel->marcarUltimaExecucao($contexto->CD_CONTEXTO, $contexto->DT_AGORA);

            if (!$this->dentroDaJanela($contexto->HR_JANELAINICIO, $contexto->HR_JANELAFIM)) {
                $this->info("{$contexto->DS_CONTEXTO}: fora da janela de horário.");
                continue;
            }

            $limite = (int) ($contexto->NR_LIMITEDIARIO ?? 0);
            $enviadosHoje = $envioModel->contarEnviadosHoje($contexto->CD_CONTEXTO);

            if ($limite > 0 && $enviadosHoje >= $limite) {
                $this->info("{$contexto->DS_CONTEXTO}: limite diário atingido ({$enviadosHoje}/{$limite}).");
                continue;
            }

            $ultimoEnvio = $envioModel->ultimoEnvioEm($contexto->CD_CONTEXTO);

            // Espacamento variavel (2 a 5 min) entre envios do MESMO contexto -
            // sorteado a cada tick, nao fixado no momento do envio anterior. Serve
            // pra nao sobrecarregar a geracao de PDF/anexos e dar visibilidade
            // operacional entre um envio e outro (nao e mais por anti-ban da Meta).
            // Como o schedule roda a cada poucos minutos, isso ja produz um intervalo
            // efetivo variavel sem precisar guardar o "proximo horario permitido".
            if ($ultimoEnvio && Carbon::parse($contexto->DT_AGORA)->diffInSeconds(Carbon::parse($ultimoEnvio)) < rand(120, 300)) {
                $this->info("{$contexto->DS_CONTEXTO}: aguardando intervalo mínimo entre envios.");
                continue;
            }

            $pendente = $envioModel->proximoPendente($contexto->CD_CONTEXTO);

            if (!$pendente) {
                continue;
            }

            $this->info("{$contexto->DS_CONTEXTO}: enviando #{$pendente->CD_ENVIO} ({$enviadosHoje}/{$limite} hoje).");
            EnviaDisparoAutomaticoJob::dispatch($pendente->CD_ENVIO, $contexto->CD_HANDLER);
        }

        return self::SUCCESS;
    }

    /**
     * Janela vazia (nao configurada ainda) = nunca envia - evita disparo fora
     * de hora por contexto recem-criado sem HR_JANELA* preenchido.
     */
    private function dentroDaJanela(?string $inicio, ?string $fim): bool
    {
        if (!$inicio || !$fim) {
            return false;
        }

        $agora = now()->format('H:i');

        return $agora >= substr($inicio, 0, 5) && $agora <= substr($fim, 0, 5);
    }
}
