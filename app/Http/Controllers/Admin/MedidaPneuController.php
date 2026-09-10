<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedidaPneu;
use Illuminate\Http\Request;

class MedidaPneuController extends Controller
{
    protected Request $request;
    protected MedidaPneu $medidapneu;

    public function __construct(
        Request $request,
        MedidaPneu $medidapneu
    ) {
        $this->request = $request;
        $this->medidapneu = $medidapneu;
    }

    public function searchMedidasPneu()
    {
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->medidapneu->searchMedidasPneusCasa($search);
        }

        return response()->json($data);
    }

    public function searchMedidas()
    {
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->medidapneu->searchMedidas($search);
        }

        return response()->json($data);
    }
}
