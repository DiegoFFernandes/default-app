<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraCotacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CotacaoComprasController extends Controller
{
    public $user;

    public function __construct(
        protected Request       $request,
        protected CompraCotacao $cotacao
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function list($idSolicitacao)
    {
        $data = $this->cotacao->getBySolicitacao($idSolicitacao);

        return DataTables::of($data)
            ->addColumn('vl_total_fmt', fn($row) =>
                'R$ ' . number_format($row->VL_TOTAL, 2, ',', '.'))
            ->addColumn('selecionada_badge', fn($row) =>
                $row->ST_SELECIONADA === 'S'
                    ? '<span class="badge badge-success">Selecionado</span>'
                    : '')
            ->addColumn('Actions', function ($row) {
                if ($row->ST_SELECIONADA === 'S') return '';
                return '
                    <button data-id="' . $row->ID_COTACAO . '" data-sol="' . $row->ID_SOLICITACAO . '"
                        data-fornecedor="' . $row->CD_FORNECEDOR . '" data-nm="' . e($row->NM_FORNECEDOR) . '"
                        data-prazo="' . $row->NR_PRAZO_ENTREGA . '" data-cond="' . e($row->DS_CONDICAO_PAGAMENTO) . '"
                        data-vl="' . $row->VL_TOTAL . '" data-obs="' . e($row->DS_OBSERVACAO) . '"
                        class="btn btn-warning btn-xs btn-edit-cot mr-1" title="Editar">
                        <i class="fas fa-edit"></i></button>
                    <button data-id="' . $row->ID_COTACAO . '" data-sol="' . $row->ID_SOLICITACAO . '"
                        class="btn btn-danger btn-xs btn-delete-cot" title="Remover">
                        <i class="fas fa-trash"></i></button>
                ';
            })
            ->rawColumns(['selecionada_badge', 'Actions'])
            ->make(true);
    }

    public function store()
    {
        $input = $this->_validate($this->request);

        try {
            $id = $this->cotacao->store($input);
            return response()->json(['success' => 'Cotação adicionada com sucesso!', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao adicionar cotação: ' . $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $input = $this->_validate($this->request);

        try {
            $this->cotacao->updateData($id, $input);
            return response()->json(['success' => 'Cotação atualizada!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar cotação.']);
        }
    }

    public function destroy($id)
    {
        $idSolicitacao = $this->request->id_solicitacao;

        try {
            $this->cotacao->deleteById($id, $idSolicitacao);
            return response()->json(['success' => 'Cotação removida!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao remover cotação.']);
        }
    }

    public function selecionarFornecedor()
    {
        $input = $this->request->validate([
            'id_solicitacao' => 'required|integer',
            'id_cotacao'     => 'required|integer',
            'ds_motivo'      => 'required|string|max:500',
        ], [
            'id_cotacao.required' => 'Selecione a cotação vencedora.',
            'ds_motivo.required'  => 'O motivo da escolha é obrigatório.',
            'ds_motivo.max'       => 'O motivo deve ter no máximo 500 caracteres.',
        ]);

        try {
            $this->cotacao->selecionarFornecedor(
                $input['id_solicitacao'],
                $input['id_cotacao'],
                $input['ds_motivo']
            );
            return response()->json(['success' => 'Fornecedor selecionado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao selecionar fornecedor.']);
        }
    }

    private function _validate($request)
    {
        return $request->validate([
            'id_solicitacao'        => 'required|integer',
            'cd_fornecedor'         => 'required|integer',
            'nr_prazo_entrega'      => 'required|integer|min:1',
            'ds_condicao_pagamento' => 'required|string|max:200',
            'vl_total'              => 'required|numeric|min:0.01',
            'ds_observacao'         => 'nullable|string|max:500',
        ], [
            'cd_fornecedor.required'         => 'Selecione o fornecedor.',
            'nr_prazo_entrega.required'      => 'Informe o prazo de entrega.',
            'nr_prazo_entrega.min'           => 'O prazo deve ser de pelo menos 1 dia.',
            'ds_condicao_pagamento.required' => 'Informe a condição de pagamento.',
            'vl_total.required'              => 'Informe o valor total.',
            'vl_total.numeric'               => 'O valor total deve ser numérico.',
            'vl_total.min'                   => 'O valor total deve ser maior que zero.',
        ]);
    }
}
