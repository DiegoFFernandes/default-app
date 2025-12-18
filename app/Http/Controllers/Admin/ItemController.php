<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected Request $request;
    protected Item $item;

    public function __construct(
        Item $item,
        Request $request
    )
    {
        $this->item = $item;
        $this->request = $request;
    }

    public function servicoPneuMedida()
    {
        $idMedidaPneu = $this->request->get('idMedidaPneu');
        
        $servicos = $this->item->servicoPneuMedida($idMedidaPneu);

        return response()->json($servicos);
    }
}
