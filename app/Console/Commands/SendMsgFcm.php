<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  App\Http\Controllers\Fcm\FCMController;
use App\Jobs\SendFcmJob;

class SendMsgFcm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:msg_fcm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar mensagem via FCM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       //despacha o job para envio de notificação FCM
       dispatch(new SendFcmJob('Estoque'));

       $this->info('Job de envio enviado com sucesso!');
    }
}
