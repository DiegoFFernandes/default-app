<?php

namespace App\Services;

use App\Models\Estoque;

use Illuminate\Support\Facades\Auth;

class ServiceEstoqueNegativo
{
    protected $estoque;


    public function __construct(Estoque $estoque)
    {
        $this->estoque = $estoque;
    }

    public function EstoqueNegativo()
    {
        $estoqueNegativo = $this->estoque->getEstoqueNegativo();
        return $estoqueNegativo;
    }
}
