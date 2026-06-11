<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CobrancaParametro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CobrancaParametroController extends Controller
{
    private const FORMAPAGTO_DEFAULT = 'BL,CC,CH,DB,DF,DI,TL,TC,CN';
    private const FORMAPAGTO_VALIDOS = ['BL', 'CC', 'CH', 'DB', 'DF', 'DI', 'TL', 'TC', 'CN'];

    public function index(): JsonResponse
    {
        $valor = CobrancaParametro::get('inadimplencia_formapagto', self::FORMAPAGTO_DEFAULT);

        return response()->json([
            'formapagto' => array_values(array_filter(explode(',', $valor))),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'formapagto'   => 'required|array|min:1',
            'formapagto.*' => 'in:BL,CC,CH,DB,DF,DI,TL,TC,CN',
        ]);

        CobrancaParametro::set(
            'inadimplencia_formapagto',
            implode(',', $request->formapagto),
            auth()->id()
        );

        return response()->json(['message' => 'Parâmetros salvos com sucesso.']);
    }
}
