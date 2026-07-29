<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisparoAutomaticoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{titulo: string, nome: string, conteudo: string}> $anexos
     */
    public function __construct(
        public string $assuntoEmail,
        public string $corpoHtml,
        public array $anexos = [],
    ) {
    }

    public function build()
    {
        $mail = $this->subject($this->assuntoEmail)->html($this->corpoHtml);

        foreach ($this->anexos as $anexo) {
            $mail->attachData($anexo['conteudo'], $anexo['nome'], [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
