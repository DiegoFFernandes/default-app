<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        foreach (['08:00', '12:00', '16:00'] as $hora) {
            $schedule->command('send:msg_fcm')->dailyAt($hora);
        }

        $schedule->command('pedidos:atualizar-alterados')->everyTenMinutes();

        // Disparos automaticos: gera pendentes (marca d'agua) e enfileira o envio.
        // A decisao de "esta na hora" e por contexto (DT_PROXIMAEXECUCAO, que
        // respeita o NR_INTERVALOHORAS/HR_EXECUCAO de cada um) - o tick daqui so
        // precisa ser frequente o bastante pra nao atrasar essa checagem.
        // withoutOverlapping evita duas execucoes se uma atrasar.
        $schedule->command('disparo-automatico:executar')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
