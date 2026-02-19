<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estoque;
use App\Models\Item;
use App\Models\LiberaOrdemComercial;
use App\Models\PedidoPneu;
use App\Models\Pessoa;
use App\Models\Pneu;
use Dflydev\DotAccessData\Data;
use Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PedidoPneuController extends Controller
{
    protected Request $request;
    protected PedidoPneu $pedidoPneu;
    protected Pessoa $pessoa;
    protected Item $item;
    protected Pneu $pneu;
    protected Estoque $estoque;
    protected LiberaOrdemComercial $liberaOrdemComercial;

    public function __construct(
        Request $request,
        PedidoPneu $pedidoPneu,
        Pessoa $pessoa,
        Item $item,
        Pneu $pneu,
        Estoque $estoque,
        LiberaOrdemComercial $liberaOrdemComercial
    ) {
        $this->request = $request;
        $this->pedidoPneu = $pedidoPneu;
        $this->pessoa = $pessoa;
        $this->item = $item;
        $this->pneu = $pneu;
        $this->estoque = $estoque;
        $this->liberaOrdemComercial = $liberaOrdemComercial;
    }

    public function index()
    {
        return view('admin.pedido.index');
    }

    public function searchPedidoPneu()
    {
        $validate = Validator::make($this->request->all(), [
            'pedido' => 'nullable|integer|required_without:ordem',
            'ordem' => 'nullable|integer|required_without:pedido',
        ], [
            'pedido.integer' => 'Pedido inválido.',
            'pedido.required_without' => 'Por favor, informe o número do <b>pedido</b> ou da ordem.',
            'ordem.integer' => 'Número da ordem inválido.',
            'ordem.required_without' => 'Por favor, informe o número da <b>ordem</b> ou do pedido.',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validate->errors()->all()
            ], 422);
        }

        if (empty($validate->validated()['pedido']) && empty($validate->validated()['ordem'])) {
            return response()->json([
                'success' => false,
                'errors'  => ['Por favor, informe ao menos um critério de busca, pedido ou ordem.']
            ], 422);
        }

        $pedidos = $this->pedidoPneu->searchPedidoPneu($validate->validated());


        return DataTables::of($pedidos)
            ->addColumn('action', function ($pedido) {
                return '<button class="btn btn-xs btn-secondary btn-editar-pneu" data-id="' . $pedido->ID_PNEU . '"><i class="fas fa-edit"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storePedidoPneu()
    {
        $validate = $this->__validate();

        if ($validate->fails()) {
            return Helper::formatErrorsAsHtml($validate);
        }

        DB::beginTransaction();

        $pessoa = $this->pessoa->FindPessoaJunsoftId($validate->validated()['pessoa']);

        if (empty($pessoa)) {
            return response()->json(['success' => false, 'message' => 'Pessoa não encontrada no sistema.'], 400);
        }

        try {
            $idPedido = $this->pedidoPneu->createPedidoPneu($pessoa, $validate->validated());

            // $idPedido = 222790; //provisorio

            $seqItemPedidoPneu = 1;

            foreach ($validate->validated()['itens'] as $item) {

                //Pesquisa os dados na tabela PNEUCARCACA para poder inserir o pneu
                $pneuCarcaca = $this->estoque->getCarcacasDaCasa($item['itemId']);

                //Faz a inserção do pneu e aguarda o IDPNEU retornado
                $idPneu = $this->pneu->createPneu($pneuCarcaca[0], $pessoa);

                // $idPneu = 854583; //provisorio

                //Busca os dados do serviço do pneu para inserir no item do pedido
                $servicoPneu = $this->item->servicoPneu(null, $item['servico']);

                // faz a inserção do item do pedido pneu
                $iditemPedidoPneu = $this->pedidoPneu->createItemPedidoPneu(
                    $idPedido,
                    $idPneu,
                    $pessoa,
                    $servicoPneu[0],
                    $item['valor'],
                    $seqItemPedidoPneu
                );

                $seqItemPedidoPneu++;

                // Prepara os dados para a  pegar a informação para insertir no ItempedidopneuBorracheiro
                $input[0] = new \stdClass();
                $input[0]->EMP = $validate->validated()['cd_empresa'];
                $input[0]->IDPESSOA = $pessoa->CD_PESSOA;
                $input[0]->CD_ITEM = $servicoPneu[0]->ID;
                $input[0]->CD_VENDEDOR = $pessoa->CD_VENDEDOR;
                $input[0]->CD_MOVIMENTACAO = 20;
                $input[0]->DTEMISSAO = date('d.m.Y');
                $input[0]->IDCONDPAGTO = $validate->validated()['cond_pagto'];
                $input[0]->CD_TABPRECO = $pessoa->CD_TABPRECO;

                $calculaComissao =  $this->liberaOrdemComercial->calculaComissao($input, $item['valor']);

                // //faz a inserção do item do pedido borracheiro
                $this->pedidoPneu->createItemPedidoPneuBorracheiro(
                    $iditemPedidoPneu,
                    $pessoa,
                    $calculaComissao[0]
                );

                //Atualiza o status do pneu carcaca para vinculado ao pedido
                $this->estoque->updateStatusPneuCarcaca(
                    $pneuCarcaca[0]->ID,
                    $iditemPedidoPneu
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $idPedido
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function __validate()
    {
        $rules = [
            'cd_empresa' => 'required|integer',
            'pessoa' => 'required|integer',
            'cond_pagto' => 'required|integer',
            'form_pagto' => 'required|string',
            'itens' => 'required|array|min:1',

            'itens.*.itemId' => 'required|integer',
            'itens.*.servico' => 'required|integer',
            'itens.*.valor' => 'required|numeric',
        ];

        $messages = [
            'cd_empresa.required' => 'Por favor, informe a empresa.',
            'cd_empresa.integer' => 'Empresa inválida.',
            'pessoa.required' => 'Por favor, informe a pessoa.',
            'pessoa.integer' => 'Pessoa inválida.',
            'cond_pagto.required' => 'Por favor, informe a condição de pagamento.',
            'cond_pagto.integer' => 'Condição de pagamento inválida.',
            'form_pagto.required' => 'Por favor, informe a forma de pagamento.',
            'form_pagto.string' => 'Forma de pagamento inválida.',
            'itens.required' => 'Por favor, informe ao menos um item para o pedido.',
            'itens.array' => 'Itens inválidos.',
            'itens.min' => 'Por favor, informe ao menos um item para o pedido.',

            'itens.*.itemId.required' => 'Por favor, informe o item do pedido.',
            'itens.*.itemId.integer' => 'Item do pedido inválido.',
            'itens.*.servico.required' => 'Por favor, informe o serviço do pneu.',
            'itens.*.servico.integer' => 'Serviço do pneu inválido.',
            'itens.*.valor.required' => 'Por favor, informe o valor do item.',
            'itens.*.valor.numeric' => 'Valor do item inválido.',
        ];

        return Validator::make($this->request->all(), $rules, $messages);
    }
}
