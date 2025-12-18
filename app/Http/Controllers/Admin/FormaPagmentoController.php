<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\FormaPagamento;
use App\Models\User;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Http\Request;

class FormaPagmentoController extends Controller
{
    protected Empresa $empresa;
    protected Request $request;
    protected FormaPagamento $formaPagamento;

    public function __construct(
        Empresa $empresa,
        Request $request,
        FormaPagamento $formaPagamento
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->formaPagamento = $formaPagamento;
    }

    public function condicaoPagamento()
    {
        $condicoes = $this->formaPagamento->getCondicaoPagamento();

        return response()->json($condicoes);
    }

    public function formaPagamento()
    {
        $formas = $this->formaPagamento->getFormaPagamento();

        return response()->json($formas);
    }
}
