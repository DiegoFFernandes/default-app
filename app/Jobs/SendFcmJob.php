<?php

namespace App\Jobs;

use App\Http\Controllers\Fcm\FCMController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tipo; 

    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Chama o controler dentro do Job
       $controller = app()->make(FCMController::class);
       $controller->sendToUser($this->tipo);

       Log::info('Job de envio de FCM executado para o tipo: '.$this->tipo);
    }
}
