<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TabPreco;
use Illuminate\Http\Request;

class TabelaPrecoIgnorarController extends Controller
{
    public
        $request,
        $tabela;

    public function __construct(
        Request $request,
        TabPreco $tabela
    ) {
        $this->request = $request;
        $this->tabela = $tabela;
    }

    public function ignorarItemTabPreco()
    {
        $cd_tabpreco = (int) $this->request->input('cd_tabpreco');
        $cd_item     = (int) $this->request->input('cd_item');
        $cd_pessoa   = (int) $this->request->input('cd_pessoa');

        try {
            $this->tabela->ignorarItemTabPreco($cd_tabpreco, $cd_item, $cd_pessoa);

            return response()->json(['success' => true, 'message' => 'Item marcado para não incluir.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao ignorar item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ignorarItensItemFaltante()
    {
        $itens = $this->request->input('itens', []);

        if (empty($itens)) {
            return response()->json(['success' => false, 'message' => 'Nenhum item selecionado.']);
        }

        try {
            foreach ($itens as $item) {
                $this->tabela->ignorarItemTabPreco((int) $item['cd_tabpreco'], (int) $item['cd_item'], (int) $item['cd_pessoa']);
            }

            return response()->json([
                'success' => true,
                'message' => count($itens) . ' item(ns) marcado(s) para não incluir.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao ignorar itens: ' . $e->getMessage(),
            ], 500);
        }
    }
}
