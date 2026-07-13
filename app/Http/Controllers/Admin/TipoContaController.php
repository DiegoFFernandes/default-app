<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoConta;
use Illuminate\Http\Request;

class TipoContaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(TipoConta::all($request->query('tipo')));
    }
}
