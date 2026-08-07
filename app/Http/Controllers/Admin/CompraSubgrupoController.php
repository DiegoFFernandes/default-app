<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraSubgrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CompraSubgrupoController extends Controller
{
    public $user;

    public function __construct(
        protected Request        $request,
        protected CompraSubgrupo $subgrupo
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function list()
    {
        $data = $this->subgrupo->getAll();

        return DataTables::of($data)
            ->addColumn('Actions', function ($row) {
                return '<button '
                    . 'data-cd="' . $row->CD_SUBGRUPO . '" '
                    . 'data-ds="' . e($row->DS_SUBGRUPO) . '" '
                    . 'class="btn btn-warning btn-xs btn-edit-subgrupo mr-1" title="Editar">'
                    . '<i class="fas fa-edit"></i></button>'
                    . '<button data-cd="' . $row->CD_SUBGRUPO . '" '
                    . 'class="btn btn-danger btn-xs btn-delete-subgrupo" title="Remover">'
                    . '<i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function search()
    {
        $term = $this->request->get('q', '');

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        return response()->json($this->subgrupo->search($term));
    }

    public function store()
    {
        $input = $this->_validate();

        try {
            $cd = $this->subgrupo->store($input);
            return response()->json([
                'success' => 'Subgrupo cadastrado!',
                'id'      => $cd,
                'text'    => mb_strtoupper($input['ds_subgrupo'], 'UTF-8'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao cadastrar subgrupo: ' . $e->getMessage()]);
        }
    }

    public function update($cd)
    {
        $input = $this->_validate();

        try {
            $this->subgrupo->updateData((int) $cd, $input);
            return response()->json(['success' => 'Subgrupo atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar subgrupo.']);
        }
    }

    public function destroy($cd)
    {
        try {
            $this->subgrupo->deleteById((int) $cd);
            return response()->json(['success' => 'Subgrupo removido!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Não foi possível remover. O subgrupo pode estar vinculado a itens.']);
        }
    }

    private function _validate(): array
    {
        return $this->request->validate([
            'ds_subgrupo' => 'required|string|max:100',
        ], [
            'ds_subgrupo.required' => 'Informe a descrição do subgrupo.',
            'ds_subgrupo.max'      => 'A descrição deve ter no máximo 100 caracteres.',
        ]);
    }
}
