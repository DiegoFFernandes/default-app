<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraConfigAprov;
use App\Models\CompraConfigFaixa;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ConfigComprasController extends Controller
{
    public $user;

    public function __construct(
        protected Request          $request,
        protected CompraConfigFaixa $configFaixa,
        protected CompraConfigAprov $configAprov,
        protected Empresa           $empresa,
        protected User              $userModel
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Configuração de Aprovações';
        $user_auth  = $this->user;
        $uri        = $this->request->route()->uri();
        $empresas   = $this->empresa->empresa();
        $usuarios   = $this->userModel->getData();

        return view('admin.compras.configuracao.index', compact(
            'title_page', 'user_auth', 'uri', 'empresas', 'usuarios'
        ));
    }

    public function listFaixas()
    {
        $data = $this->configFaixa->getAll();

        return DataTables::of($data)
            ->addColumn('vl_minimo_fmt', fn($row) =>
                'R$ ' . number_format($row->VL_MINIMO, 2, ',', '.'))
            ->addColumn('vl_maximo_fmt', fn($row) =>
                $row->VL_MAXIMO ? 'R$ ' . number_format($row->VL_MAXIMO, 2, ',', '.') : 'Ilimitado')
            ->addColumn('ativo_badge', fn($row) =>
                $row->ST_ATIVO === 'S'
                    ? '<span class="badge badge-success">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>')
            ->addColumn('Actions', function ($row) {
                return '
                    <button data-id="' . $row->ID_FAIXA . '"
                        data-empresa="' . $row->CD_EMPRESA . '" data-ds="' . e($row->DS_FAIXA) . '"
                        data-min="' . $row->VL_MINIMO . '" data-max="' . $row->VL_MAXIMO . '"
                        data-ordem="' . $row->NR_ORDEM . '" data-ativo="' . $row->ST_ATIVO . '"
                        class="btn btn-warning btn-xs btn-edit-faixa mr-1" title="Editar">
                        <i class="fas fa-edit"></i></button>
                    <button data-id="' . $row->ID_FAIXA . '" data-ds="' . e($row->DS_FAIXA) . '"
                        class="btn btn-info btn-xs btn-aprovadores mr-1" title="Aprovadores">
                        <i class="fas fa-users"></i></button>
                    <button data-id="' . $row->ID_FAIXA . '"
                        class="btn btn-danger btn-xs btn-delete-faixa" title="Excluir">
                        <i class="fas fa-trash"></i></button>
                ';
            })
            ->rawColumns(['ativo_badge', 'Actions'])
            ->make(true);
    }

    public function storeFaixa()
    {
        $input = $this->_validateFaixa($this->request);

        try {
            $id = $this->configFaixa->store($input);
            return response()->json(['success' => 'Faixa criada com sucesso!', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao criar faixa: ' . $e->getMessage()]);
        }
    }

    public function updateFaixa($id)
    {
        $input = $this->_validateFaixa($this->request);

        try {
            $this->configFaixa->updateData($id, $input);
            return response()->json(['success' => 'Faixa atualizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar faixa.']);
        }
    }

    public function destroyFaixa($id)
    {
        try {
            $this->configFaixa->deleteById($id);
            return response()->json(['success' => 'Faixa excluída com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao excluir. Verifique aprovadores vinculados.']);
        }
    }

    public function listAprovadores($idFaixa)
    {
        return response()->json($this->configAprov->getByFaixa($idFaixa));
    }

    public function storeAprovador()
    {
        $input = $this->request->validate([
            'id_faixa'     => 'required|integer',
            'nr_ordem'     => 'required|integer|min:1',
            'ds_cargo'     => 'required|string|max:100',
            'cd_usuario'   => 'required|integer',
            'nm_aprovador' => 'required|string|max:200',
        ], [
            'ds_cargo.required'     => 'Informe o cargo.',
            'cd_usuario.required'   => 'Selecione o usuário aprovador.',
            'nm_aprovador.required' => 'Nome do aprovador não identificado.',
        ]);

        try {
            $id = $this->configAprov->store($input);
            return response()->json(['success' => 'Aprovador adicionado!', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao adicionar. Verifique se a ordem já existe nesta faixa.']);
        }
    }

    public function destroyAprovador($id)
    {
        try {
            $this->configAprov->deleteById($id);
            return response()->json(['success' => 'Aprovador removido!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao remover aprovador.']);
        }
    }

    private function _validateFaixa($request)
    {
        return $request->validate([
            'cd_empresa' => 'required|integer',
            'ds_faixa'   => 'required|string|max:100',
            'vl_minimo'  => 'required|numeric|min:0',
            'vl_maximo'  => 'nullable|numeric|gt:vl_minimo',
            'nr_ordem'   => 'required|integer|min:1',
        ], [
            'cd_empresa.required' => 'Selecione a empresa.',
            'ds_faixa.required'   => 'Informe a descrição da faixa.',
            'vl_minimo.required'  => 'Informe o valor mínimo.',
            'vl_maximo.gt'        => 'O valor máximo deve ser maior que o mínimo.',
        ]);
    }
}
