<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CompraItemController extends Controller
{
    public $user;

    public function __construct(
        protected Request     $request,
        protected CompraItem  $compraItem
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Itens de Compra';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();

        return view('admin.compras.itens.index', compact('title_page', 'user_auth', 'uri'));
    }

    public function list()
    {
        $data = $this->compraItem->getAll();

        return DataTables::of($data)
            ->addColumn('ativo_badge', fn($row) =>
                $row->ST_ATIVO === 'S'
                    ? '<span class="badge badge-success">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>')
            ->addColumn('Actions', function ($row) {
                return '<button '
                    . 'data-cd="' . $row->CD_ITEM . '" '
                    . 'data-ds="' . e($row->DS_ITEM) . '" '
                    . 'data-un="' . e($row->SG_UNIDMED ?? '') . '" '
                    . 'data-subgrupo-cd="' . ($row->CD_SUBGRUPO_COMPRA ?? '') . '" '
                    . 'data-subgrupo-ds="' . e($row->DS_SUBGRUPO ?? '') . '" '
                    . 'data-ativo="' . $row->ST_ATIVO . '" '
                    . 'class="btn btn-warning btn-xs btn-edit-compra-item mr-1" title="Editar">'
                    . '<i class="fas fa-edit"></i></button>'
                    . '<button data-cd="' . $row->CD_ITEM . '" '
                    . 'class="btn btn-danger btn-xs btn-delete-compra-item" title="Remover">'
                    . '<i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['ativo_badge', 'Actions'])
            ->make(true);
    }

    public function store()
    {
        $input = $this->_validate();
        $input['cd_usuario'] = $this->user->id;

        if ($this->compraItem->existsByDescricao($input['ds_item'])) {
            return response()->json(['errors' => 'Já existe um item com essa descrição.']);
        }

        try {
            $cd = $this->compraItem->store($input);
            return response()->json([
                'success' => 'Item cadastrado!',
                'id'      => $cd,
                'text'    => $cd . ' - ' . $input['ds_item'],
                'un'      => $input['sg_unidmed'] ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao cadastrar item: ' . $e->getMessage()]);
        }
    }

    public function update($cd)
    {
        $input = $this->_validate();

        if ($this->compraItem->existsByDescricao($input['ds_item'], (int) $cd)) {
            return response()->json(['errors' => 'Já existe um item com essa descrição.']);
        }

        try {
            $this->compraItem->updateData((int) $cd, $input);
            return response()->json(['success' => 'Item atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar item.']);
        }
    }

    public function destroy($cd)
    {
        try {
            $this->compraItem->deleteById((int) $cd);
            return response()->json(['success' => 'Item removido!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Não foi possível remover. O item pode estar em uso.']);
        }
    }

    private function _validate(): array
    {
        return $this->request->validate([
            'ds_item'            => 'required|string|max:200',
            'sg_unidmed'         => 'required|string|max:10',
            'cd_subgrupo_compra' => 'nullable|integer',
            'st_ativo'           => 'nullable|in:S,N',
        ], [
            'ds_item.required'    => 'Informe a descrição do item.',
            'ds_item.max'         => 'A descrição deve ter no máximo 200 caracteres.',
            'sg_unidmed.required' => 'Selecione a unidade.',
            'sg_unidmed.max'      => 'A unidade deve ter no máximo 10 caracteres.',
        ]);
    }
}
