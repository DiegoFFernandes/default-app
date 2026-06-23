<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index()
    {
        $rows = (new Empresa())->empresa();
        return response()->json(array_map(fn ($r) => [
            'id'   => $r->CD_EMPRESA ?? $r->cd_empresa ?? null,
            'text' => $r->NM_EMPRESA ?? $r->nm_empresa ?? '',
        ], (array) $rows));
    }
}
