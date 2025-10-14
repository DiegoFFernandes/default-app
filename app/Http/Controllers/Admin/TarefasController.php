<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\kanban_coluna;
use App\Models\kanban_projeto;
use Illuminate\Http\Request;

class TarefasController extends Controller
{
    protected $projeto, $coluna;

    public function __construct(
        kanban_projeto $kanban_projeto,
        kanban_coluna $kanban_coluna,
    ) {
        $this->middleware('auth');
        $this->projeto = $kanban_projeto;
        $this->coluna = $kanban_coluna;
    }

    public function tarefas()
    {
        return view('admin.tarefas.quadro-tarefas');
    }

    public function listarTarefas()
    {
        $idUser = auth()->user()->id;
        $tarefas = $this->coluna->listColunas(1);
        return response()->json($tarefas);
    }
}
