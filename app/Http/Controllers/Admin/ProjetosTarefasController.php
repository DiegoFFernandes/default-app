<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\kanban_cartao;
use App\Models\kanban_coluna;
use App\Models\kanban_projeto;
use App\Models\kanban_projeto_compartilhado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetosTarefasController extends Controller
{
    protected $request, $projeto, $coluna, $cartao, $compartilhado, $user;

    public function __construct(
        User $user,
        Request $request,
        kanban_projeto $kanban_projeto,
        kanban_coluna $kanban_coluna,
        kanban_cartao $kanban_cartao,
        kanban_projeto_compartilhado $kanban_projeto_compartilhado
    ) {
        $this->middleware('auth');
        $this->request = $request;
        $this->projeto = $kanban_projeto;
        $this->coluna = $kanban_coluna;
        $this->cartao = $kanban_cartao;
        $this->compartilhado = $kanban_projeto_compartilhado;


        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $usuarios = (new User)->getData();

        return view('admin.tarefas.projetos-tarefas', compact('usuarios'));
    }

    public function listarProjeto()
    {
        $idUser = [auth()->user()->id];

        $projeto = $this->projeto->listProjetos($idUser)->makeHidden(['id']);

        return response()->json($projeto);
    }

    public function listarProjetoCompartilhado()
    {
        $projetos = $this->projeto->listProjetosCompartilhados(auth()->user()->id)->makeHidden(['id']);

        return response()->json($projetos);
    }

    public function salvarProjeto()
    {
        $data = $this->request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string',
            'color' => 'required|string|size:6',
        ]);

        return $this->projeto->salvarProjeto($data);
    }

    public function editarProjeto()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string',
            'color' => 'required|string|size:6',
        ]);

        $data['id'] = decrypt($data['id']);

        return $this->projeto->editarProjeto($data);
    }

    public function reordenarProjetos()
    {
        $data = $this->request->validate([
            'projetos' => 'required|array',
            'projetos.*' => 'required|string',
        ]);

        $projetoIds = array_map(fn ($id) => decrypt($id), $data['projetos']);

        return $this->projeto->reordenarProjetos($projetoIds);
    }

    public function editarTituloProjeto()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
            'nome' => 'required|string|max:150',
        ]);

        $projeto = $this->projeto->getProjetoById($data['id']);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado.'], 404);
        }

        try {
            $projeto->nome = $data['nome'];
            $projeto->save();

            return response()->json(['success' => 'Título do projeto atualizado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar o título do projeto: ' . $e->getMessage()], 500);
        }
    }

    public function removerProjeto()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
        ]);

        $projeto = $this->projeto->getProjetoById(decrypt($data['id']));

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado.'], 404);
        }

        $colunaIds = $this->coluna->where('projeto_id', $projeto->id)->pluck('id');

        if ($colunaIds->isNotEmpty() && !$this->request->boolean('forcar')) {
            $temCartoes = $this->cartao->whereIn('coluna_id', $colunaIds)->exists();

            return response()->json([
                'confirmar_exclusao' => true,
                'message' => $temCartoes
                    ? 'Este projeto possui colunas e cartões cadastrados. Deseja excluir tudo?'
                    : 'Este projeto possui colunas cadastradas. Deseja excluir tudo?',
            ]);
        }

        try {
            $this->cartao->whereIn('coluna_id', $colunaIds)->delete();
            $this->coluna->where('projeto_id', $projeto->id)->delete();
            $this->compartilhado->where('id_projeto', $projeto->id)->delete();
            $projeto->delete();

            return response()->json(['success' => 'Projeto removido com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao remover o projeto: ' . $e->getMessage()], 500);
        }
    }

    // Retorna o projeto apenas se o usuario autenticado for o dono; senao null
    private function projetoDoDono($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
        } catch (\Exception $e) {
            return null;
        }

        $projeto = $this->projeto->getProjetoById($id);

        if (!$projeto || $projeto->cd_usuario != auth()->user()->id) {
            return null;
        }

        return $projeto;
    }

    public function compartilharProjeto()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
            'usuarios' => 'required|array',
            'usuarios.*' => 'required|integer|exists:users,id',
        ]);

        $projeto = $this->projetoDoDono($data['id']);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado ou você não tem permissão.'], 403);
        }

        // ignora o proprio dono, caso venha na lista
        $usuarios = array_filter($data['usuarios'], fn ($id) => $id != $projeto->cd_usuario);

        return $this->compartilhado->compartilhar($projeto->id, $projeto->cd_usuario, $usuarios);
    }

    public function listarCompartilhamentos()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
        ]);

        $projeto = $this->projetoDoDono($data['id']);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado ou você não tem permissão.'], 403);
        }

        return response()->json($this->compartilhado->usuariosComAcesso($projeto->id));
    }

    public function revogarCompartilhamento()
    {
        $data = $this->request->validate([
            'id' => 'required|string',
            'id_user' => 'required|integer',
        ]);

        $projeto = $this->projetoDoDono($data['id']);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado ou você não tem permissão.'], 403);
        }

        return $this->compartilhado->revogar($projeto->id, $data['id_user']);
    }
}
