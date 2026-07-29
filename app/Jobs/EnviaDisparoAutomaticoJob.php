<?php

namespace App\Jobs;

use App\Mail\DisparoAutomaticoMail;
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
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviaDisparoAutomaticoJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // O controle de repeticao e manual (NR_TENTATIVAS/ST_ENVIO no banco), nao pela fila.
    public int $tries = 1;

    public function __construct(
        public int $cdEnvio,
        public string $cdHandler,
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->cdEnvio;
    }

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

            $handler = $registry->resolve($this->cdHandler);
            $email = $handler->montarEmail($envio);

            Mail::to($destinatarioValido ? [$envio->DS_EMAILDEST] : [])->cc($copiasValidas)->send(
                new DisparoAutomaticoMail($envio->DS_ASSUNTO, $email['corpo'], $email['anexos'])
            );

            foreach ($email['anexos'] as $anexo) {
                $envioModel->salvarAnexo($this->cdEnvio, $anexo['titulo'], $anexo['nome']);
            }

            if (!$destinatarioValido || $copiasInvalidas) {
                $motivos = [];

                if (!$destinatarioValido) {
                    $motivos[] = "Destinatário com domínio inválido, não recebeu: {$envio->DS_EMAILDEST}";
                }

                if ($copiasInvalidas) {
                    $motivos[] = 'Cópia(s) com domínio inválido, ignorada(s): ' . implode(', ', $copiasInvalidas);
                }

                $envioModel->marcarEnviadoComFalha($this->cdEnvio, implode(' | ', $motivos));
            } else {
                $envioModel->marcarEnviado($this->cdEnvio);
            }
        } catch (Throwable $e) {
            Log::error("[DisparoAutomatico] Falha ao enviar envio {$this->cdEnvio}: " . $e->getMessage());

            $status = $envioModel->registrarFalha($this->cdEnvio, $e->getMessage(), $contexto->NR_TENTATIVAS);

            if ($status === 'A') {
                $this->release(now()->addMinutes(5));
            }
        }
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
