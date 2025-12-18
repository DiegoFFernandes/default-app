<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PedidoPneu;
use Illuminate\Http\Request;

class PedidoPneuController extends Controller
{
    protected Request $request;
    protected PedidoPneu $pedidoPneu;

    public function __construct(
        Request $request,
        PedidoPneu $pedidoPneu
    ) {
        $this->request = $request;
        $this->pedidoPneu = $pedidoPneu;
    }

    public function storePedidoPneu()
    {
        $data = $this->request->all();
        

        return $this->pedidoPneu->createPedidoPneu($data);
    }
}
