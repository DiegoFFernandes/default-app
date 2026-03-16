<?php

namespace App\Console\Commands;

use App\Models\PedidosAlterados;
use Illuminate\Console\Command;

class updatePedidoAlterados extends Command
{

    protected $signature = 'pedidos:atualizar-alterados';
    protected $description = 'Atualiza pedidos alterados automaticamente';

    protected $pedidosAlterados;

    public function __construct(PedidosAlterados $pedidosAlterados)
    {
        parent::__construct();
        $this->pedidosAlterados = $pedidosAlterados;
    }

    public function handle()
    {
        $pedidos = $this->pedidosAlterados->getPedidosAlterados('N');

        foreach ($pedidos as $pedido) {

            if (empty($pedido->NR_PEDIDO)) {

                $ped = $this->pedidosAlterados->updateItemPedidoPneu($pedido);

                if ($ped) {
                    $this->info("Ordem {$pedido->NR_ORDEM} Alterado com sucesso.");
                } else {
                    $this->error("Ordem {$pedido->NR_ORDEM} Falha ao alterar valor.");
                }
            } else {

                $this->pedidosAlterados->updateItemPedido($pedido);

                $ped = $this->pedidosAlterados->updateItemPedidoPneu($pedido);

                if ($ped) {
                    $this->info("Ordem {$pedido->NR_ORDEM} Alterado com sucesso.");
                } else {
                    $this->error("Ordem {$pedido->NR_ORDEM} Falha ao alterar valor.");
                }
            }
        }

        return 0;
    }
}
