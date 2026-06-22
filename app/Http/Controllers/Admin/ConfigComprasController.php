<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraConfigAprov;
use App\Models\CompraConfigFaixa;
use App\Models\CompraCentroCusto;
use App\Models\CompraParamEmpresa;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ConfigComprasController extends Controller
{
    public $user;

    public function __construct(
        protected Request            $request,
        protected CompraConfigFaixa  $configFaixa,
        protected CompraConfigAprov  $configAprov,
        protected CompraCentroCusto  $centroCusto,
        protected CompraParamEmpresa $paramEmpresa,
        protected Empresa            $empresa,
        protected User               $userModel
    ) {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page    = 'Configuração de Aprovações';
        $user_auth     = $this->user;
        $uri           = $this->request->route()->uri();
        $empresas      = $this->empresa->empresa();
        $usuarios      = $this->userModel->getData();
        $paramEmpresas = $this->paramEmpresa->getAll();
        $paramMap      = collect($paramEmpresas)->keyBy('CD_EMPRESA');

        return view('admin.compras.configuracao.index', compact(
            'title_page',
            'user_auth',
            'uri',
            'empresas',
            'usuarios',
            'paramMap'
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

    public function reordenarAprovadores()
    {
        $ids = $this->request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer',
        ])['ids'];

        try {
            $this->configAprov->reordenar($ids);
            return response()->json(['success' => 'Ordem atualizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e . 'Erro ao reordenar.']);
        }
    }

    public function listCentros()
    {
        $centros = $this->centroCusto->getAll();
        $userIds = collect($centros)->pluck('CD_USUARIO_RESP')->unique()->filter();
        $users   = User::whereIn('id', $userIds)->pluck('name', 'id');

        return DataTables::of($centros)
            ->addColumn('nm_responsavel', fn($row) =>
                $row->CD_USUARIO_RESP ? ($users[$row->CD_USUARIO_RESP] ?? '— não definido —') : '— não definido —'
            )
            ->addColumn('vl_orcado_fmt', fn($row) =>
                $row->VL_ORCADO_MES ? 'R$ ' . number_format($row->VL_ORCADO_MES, 2, ',', '.') : '—'
            )
            ->addColumn('Actions', function ($row) {
                return '<button class="btn btn-warning btn-xs btn-edit-centro"
                            data-cd="' . $row->CD_CENTROCUSTO . '"
                            data-empresa="' . $row->CD_EMPRESA . '"
                            data-ds="' . e($row->DS_CENTROCUSTO) . '"
                            data-usuario="' . ($row->CD_USUARIO_RESP ?? '') . '"
                            data-orcado="' . ($row->VL_ORCADO_MES ?? '') . '"
                            data-dia="' . ($row->DIA_INICIO_CICLO ?? '') . '"
                            title="Configurar"><i class="fas fa-edit"></i></button>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function getSaldoCiclo()
    {
        $cdEmpresa = (int) $this->request->get('cd_empresa');
        $cdCentro  = (int) $this->request->get('cd_centrocusto');

        $saldo = $this->centroCusto->getSaldoCiclo($cdEmpresa, $cdCentro);

        return response()->json($saldo);
    }

    public function getCentrosByEmpresa()
    {
        $cdEmpresa = (int) $this->request->get('cd_empresa');
        return response()->json($this->centroCusto->getForSelect($cdEmpresa));
    }

    public function updateCentroCusto($cd)
    {
        $input = $this->request->validate([
            'cd_empresa'      => 'required|integer',
            'cd_usuario_resp' => 'nullable|integer',
            'vl_orcado_mes'   => 'nullable|numeric|min:0',
            'dia_inicio_ciclo'=> 'nullable|integer|min:1|max:31',
        ]);

        try {
            $this->centroCusto->updateResponsavel($input['cd_empresa'], $cd, [
                'cd_usuario_resp'  => $input['cd_usuario_resp'] ?: null,
                'vl_orcado_mes'    => $input['vl_orcado_mes'] ?: null,
                'dia_inicio_ciclo' => $input['dia_inicio_ciclo'] ?: null,
            ]);
            return response()->json(['success' => 'Centro de resultado atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar centro de resultado.']);
        }
    }

    public function getCentrosTipos()
    {
        $cdEmpresa = (int) $this->request->get('cd_empresa');
        return response()->json($this->centroCusto->getAvailableTypes($cdEmpresa));
    }

    public function storeCentro()
    {
        $input = $this->request->validate([
            'cd_empresa'       => 'required|integer',
            'cd_centrocusto'   => 'required|integer',
            'ds_centrocusto'   => 'required|string|max:100',
            'cd_usuario_resp'  => 'nullable|integer',
            'vl_orcado_mes'    => 'nullable|numeric|min:0',
            'dia_inicio_ciclo' => 'nullable|integer|min:1|max:31',
        ], [
            'cd_empresa.required'     => 'Selecione a empresa.',
            'cd_centrocusto.required' => 'Selecione o tipo de centro.',
            'ds_centrocusto.required' => 'Descrição não identificada.',
        ]);

        try {
            $this->centroCusto->store($input);
            return response()->json(['success' => 'Centro criado com sucesso!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'violation of PRIMARY') || str_contains($msg, 'duplicate')) {
                return response()->json(['errors' => 'Este centro já existe para a empresa selecionada.']);
            }
            return response()->json(['errors' => 'Erro ao criar centro: ' . $msg]);
        }
    }

    public function toggleCentroCusto()
    {
        $input = $this->request->validate([
            'cd_empresa' => 'required|integer',
            'st_usa'     => 'required|in:S,N',
        ]);

        try {
            $this->paramEmpresa->upsert($input['cd_empresa'], $input['st_usa']);
            return response()->json(['success' => 'Parâmetro atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar parâmetro.']);
        }
    }

    public function updateQtdFornecCot()
    {
        $input = $this->request->validate([
            'cd_empresa'    => 'required|integer',
            'qtd_fornec_cot' => 'required|integer|min:1|max:99',
        ], [
            'qtd_fornec_cot.required' => 'Informe a quantidade mínima.',
            'qtd_fornec_cot.integer'  => 'A quantidade deve ser um número inteiro.',
            'qtd_fornec_cot.min'      => 'O mínimo é 1 cotação.',
            'qtd_fornec_cot.max'      => 'O máximo é 99 cotações.',
        ]);

        try {
            $this->paramEmpresa->updateQtdFornecCot($input['cd_empresa'], $input['qtd_fornec_cot']);
            return response()->json(['success' => 'Parâmetro atualizado!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao atualizar parâmetro.']);
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
