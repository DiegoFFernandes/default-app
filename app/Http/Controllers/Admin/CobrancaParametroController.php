<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CobrancaParametro;
use App\Models\FormaPagamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CobrancaParametroController extends Controller
{
    public function __construct(private FormaPagamento $formaPagamento) {}

    private function codigosValidos(): array
    {
        return collect($this->formaPagamento->getFormaPagamento())
            ->pluck('CD_FORMAPAGTO')
            ->filter()
            ->values()
            ->all();
    }

    public function index(): JsonResponse
    {
        $codigos = $this->codigosValidos();
        $default = implode(',', $codigos);
        $valor   = CobrancaParametro::get('inadimplencia_formapagto', $default);

        return response()->json([
            'formapagto' => array_values(array_filter(explode(',', $valor))),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $codigos = $this->codigosValidos();

        $request->validate([
            'formapagto'   => 'required|array|min:1',
            'formapagto.*' => Rule::in($codigos),
        ]);

        CobrancaParametro::set(
            'inadimplencia_formapagto',
            implode(',', $request->formapagto),
            auth()->id()
        );

        return response()->json(['message' => 'Parâmetros salvos com sucesso.']);
    }
}
