<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\kanban_cartao;
use App\Models\kanban_coluna;
use App\Models\kanban_projeto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetosTarefasController extends Controller
{
    protected $request, $projeto, $coluna, $cartao, $user;

    public function __construct(
        User $user,
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


        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function listarProjeto()
    {
        $idUser = $this->user->hasRole('admin') ? [] : [auth()->user()->id];       


        $projeto = $this->projeto->listProjetos($idUser)->makeHidden(['id']);


        return view('admin.tarefas.projetos-tarefas', compact('projeto'));
    }
}
