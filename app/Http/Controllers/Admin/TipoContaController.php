<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoConta;

class TipoContaController extends Controller
{
    public function index()
    {
        return response()->json(TipoConta::all());
    }
}
