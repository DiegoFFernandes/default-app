<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Historico;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
    public function index(Request $request)
    {
        $cdTipoConta = (int) $request->get('cd_tipoconta', 0);

        if (!$cdTipoConta) {
            return response()->json([]);
        }

        return response()->json(Historico::byTipoConta($cdTipoConta));
    }
}
