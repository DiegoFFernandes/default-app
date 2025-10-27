<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\kanban_cartao;
use App\Models\kanban_coluna;
use App\Models\kanban_projeto;
use Illuminate\Http\Request;

class TarefasController extends Controller
{
    protected $request, $projeto, $coluna, $cartao;

    public function __construct(
        Request $request,
        kanban_projeto $kanban_projeto,
        kanban_coluna $kanban_coluna,
        kanban_cartao $kanban_cartao
    ) {
        $this->middleware('auth');
        $this->request = $request;
        $this->projeto = $kanban_projeto;
        $this->coluna = $kanban_coluna;
        $this->cartao = $kanban_cartao;
    }

    public function tarefas()
    {
        return view('admin.tarefas.quadro-tarefas');
    }

    public function listarColunas()
    {
        $idUser = auth()->user()->id;
        $tarefas = $this->coluna->listColunas(1);
        return response()->json($tarefas);
    }
    public function salvarTarefas()
    {
        $dados = $this->request->input('dados');
        return $this->cartao->salvarTarefas($dados);
    }

    public function editarCartoes()
    {
        $dados = $this->request->input('dados');
        return $this->cartao->editarCartoes($dados);
    }

    public function listarCartoes()
    {
        $cartoes = $this->request->input('id_coluna')
            ? $this->cartao->where('coluna_id', $this->request->input('id_coluna'))->orderBy('posicao')->get()
            : [];
        return response()->json($cartoes);
    }
    public function deletarCartao()
    {
        $idCard = $this->request->input('id_card');
        return $this->cartao->deleteCartao($idCard);
    }

    public function reordenarCartao()
    {
        $tarefas = $this->request->input('tarefas');
        return $this->cartao->reordenarTarefas($tarefas);
    }

    public function reordenarColunas()
    {
        return $colunas = $this->request->input('colunas');
        return $this->coluna->reordenarColunas($colunas);
    }

    public function editarColuna()
    {
        // return $this->request->dados['id'];

        $validateData = $this->request->validate([
            'dados.id' => 'required|integer|exists:kanban_colunas,id',
            'dados.nome' => 'required|string|max:255',
            'dados.color' => 'required|string|size:6',
        ]);           

        return $this->coluna->editarColuna($validateData['dados']);
    }

    public function arquivarColuna()
    {
        $idColuna = $this->request->input('dados.id');
        return $this->coluna->arquivarColuna($idColuna);
    }
}
