<?php

namespace App\Jobs;

use App\Models\DisparoContexto;
use App\Models\DisparoEnvio;
use App\Services\Disparos\DisparoHandlerRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnviaDisparoAutomaticoJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // O controle de repeticao "de negocio" e manual (NR_TENTATIVAS/ST_ENVIO no
    // banco), nao pela fila - mas tries=1 deixava qualquer soluco de
    // infraestrutura (worker reiniciado/job reciclado por timeout) matar o job
    // direto no MaxAttemptsExceededException, sem nunca chamar handle() pra
    // registrar o motivo. 2 da uma margem só pra isso, sem virar retry de negocio.
    public int $tries = 2;

    public function __construct(
        public int $cdEnvio,
        public string $cdHandler,
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->cdEnvio;
    }

    /**
     * Generico por canal: quem sabe como enviar (e-mail, WhatsApp, ...) e
     * decidir sucesso/falha parcial e o proprio handler - aqui so sobra o
     * retry/release padrao quando NADA foi entregue.
     */
    public function handle(
        DisparoEnvio $envioModel,
        DisparoContexto $contextoModel,
        DisparoHandlerRegistry $registry
    ): void {
        $envio = $envioModel->find($this->cdEnvio);

        if (!$envio || $envio->ST_ENVIO !== 'A') {
            return;
        }

        $contexto = $contextoModel->find($envio->CD_CONTEXTO);

        try {
            $handler = $registry->resolve($this->cdHandler);
            $handler->enviar($envio);
        } catch (Throwable $e) {
            Log::error("[DisparoAutomatico] Falha ao enviar envio {$this->cdEnvio}: " . $e->getMessage());

            $status = $envioModel->registrarFalha($this->cdEnvio, $e->getMessage(), $contexto->NR_TENTATIVAS);

            if ($status === 'A') {
                $this->release(now()->addMinutes(5));
            }
        }
    }
}
