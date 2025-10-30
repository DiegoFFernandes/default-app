<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\ItemTabPreco;
use App\Models\Pessoa;
use App\Models\SupervisorComercial;
use App\Models\TabPreco;
use App\Models\User;
use App\Services\UserRoleFilterService;
use Dflydev\DotAccessData\Data;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\Help;
use Yajra\DataTables\Facades\DataTables;

class TabelaPrecoController extends Controller
{
    public $request, $user_auth, $tipo, $pessoa, $empresa, $user, $tabela, $itemTabPreco, $area, $supervisorComercial, $gerenteUnidade;

    public function __construct(
        Empresa $empresa,
        Request $request,
        Pessoa $pessoa,
        TabPreco $tabela,
        ItemTabPreco $itemTabPreco,
        AreaComercial $area,
        SupervisorComercial $supervisorComercial,
        GerenteUnidade $gerenteUnidade
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->pessoa = $pessoa;
        $this->tabela = $tabela;
        $this->itemTabPreco = $itemTabPreco;

        $this->area = $area;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page = 'Tabela de Preço';
        $uri  = $this->request->route()->uri();
        $user_auth = $this->user_auth;
        $empresas  = $this->empresa->empresa();
        $desenho = $this->tabela->getSelectTabPreco();

        return view('admin.comercial.tabela-preco', compact(
            'title_page',
            'user_auth',
            'empresas',
            'uri',
            'desenho'
        ));
    }

    public function getTabPreco()
    {
        $data = $this->tabela->getTabpreco();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-xs btn-secondary details-control mr-2" data-cd_tabela="' . $row->CD_TABPRECO . '">Clientes</button>                
                    ';
            })
            ->addColumn('clientes_associados', function ($row) {
                $btn = '
                    <button class="btn btn-xs btn-secondary btn-block btn-ver-itens mb-1" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . $row->CD_TABPRECO . '">Itens</button>                    
                    <button class="btn btn-xs btn-secondary btn-block details-control mr-2 mb-1" data-cd_tabela="' . $row->CD_TABPRECO . '">Clientes</button>
                    <button class="btn btn-xs btn-warning btn-block btn-vincular-tabela mb-1" data-cd_tabela="' . $row->CD_TABPRECO . '">Vincular</button>';
                if (!in_array($row->CD_TABPRECO, [1, 2, 3, 4, 5, 6, 7, 8]) && $row->ASSOCIADOS == 0) {
                    $btn .= '
                    <button class="btn btn-xs btn-danger btn-block btn-delete-tabela mb-1" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . encrypt($row->CD_TABPRECO) . '">Excluir</button>
                    ';
                }
                return $btn;
            })
            ->setRowClass(function ($row) {
                return $row->ASSOCIADOS > 0 ? 'bg-green' : '';
            })
            ->rawColumns(['action', 'clientes_associados'])
            ->make(true);
    }

    public function getTabPrecoPreview()
    {
        $service = new UserRoleFilterService(
            $this->user,
            $this->area,
            $this->supervisorComercial,
            $this->gerenteUnidade,
            null
        );

        $filtros = $service->getFiltros();

        $data = $this->tabela->getTabprecoPreview('', $filtros['cd_regiao']);

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '
                    <button class="btn mb-1 btn-xs btn-secondary btn-ver-itens" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . $row->CD_TABPRECO . '">Ver Itens</button> 
                  ';
                if ($this->user->hasRole('admin|gerente comercial')) {
                    if ($row->ST_IMPORTA === 'N') {
                        $btn .= '<button class="btn mb-1 btn-xs btn-secondary btn-importar" data-cd_tabela="' . $row->CD_TABPRECO . '">Importar</button>';
                        $btn .= '<button class="btn mb-1 ml-1 btn-xs btn-danger btn-delete-tabela" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . encrypt($row->CD_TABPRECO) . '">Excluir</button>';
                    } else if ($row->ST_IMPORTA === 'V') {
                        $btn .= '<button class="btn mb-1 btn-xs btn-warning btn-vincular-tabela" data-cd_tabela="' . $row->CD_TABPRECO . '">Vincular</button>';
                    }
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getItemTabPreco()
    {
        $cd_tabela = $this->request->get('cd_tabela');
        $tela = $this->request->get('tela');
        $data = $this->tabela->getItemTabPreco($cd_tabela, $tela);

        return DataTables::of($data)
            ->make(true);
    }
    public function getTabClientePreco()
    {
        $cd_tabela = $this->request->cd_tabela;
        $data = $this->tabela->getTabClientePreco($cd_tabela);

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-xs btn-secondary btn-cancelar-vinculo" data-cd_pessoa="' . $row->CD_PESSOA . '" data-cd_tabela="' . $row->CD_TABPRECO . '">Cancelar Vinculo</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getSearchMedida()
    {
        $idDesenho = $this->request->input('desenho', []);
        $select = $this->request->select;
        $idDesenho = is_array($idDesenho) ? implode(',', $idDesenho) : $idDesenho;

        $data = $this->tabela->getSelectTabPreco($select, $idDesenho);

        return response()->json($data);
    }
    public function getPreviaTabelaPreco()
    {
        $validator = $this->_validate($this->request->all());

        if ($validator->fails()) {
            return Helper::formatErrorsAsHtml($validator);
        }

        $idDesenho = $this->request->input('desenho', []);
        $idMedida = $this->request->input('medida', []);
        $valor = $this->request->input('valor', 0);
        $IdPessoa = $this->request->input('pessoa');
        $select = $this->request->select;

        $idDesenho = is_array($idDesenho) ? implode(',', $idDesenho) : $idDesenho;
        $idMedida = is_array($idMedida) ? implode(',', $idMedida) : $idMedida;

        $data = $this->tabela->getSelectTabPreco($select, $IdPessoa, $idDesenho, $idMedida, $valor);

        // Número total de registros após filtros
        $totalFiltered = count($data);

        // Resposta formatada para o DataTables
        return response()->json([
            'draw' => (int) $this->request->draw,
            'recordsTotal' => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function getSearchAdicional()
    {
        $arr = [
            'pessoa' => $this->request->input('pessoa'),
            'vlr_vulc_carga' => $this->request->input('vulc_carga_valor', 0),
            'vlr_vulc_agricola' => $this->request->input('vulc_agricola_valor', 0),
            'vlr_manchao' => $this->request->input('manchao_valor', null),
            'vlr_manchao_agricola' => $this->request->input('manchao_agricola_valor', 0),
            'vlr_enchimento' => $this->request->input('enchimento_valor', 0),
            'vlr_enchimento_ombro_1' => $this->request->input('enchimento_ombro_1_valor', 0),
            'vlr_enchimento_ombro_2' => $this->request->input('enchimento_ombro_2_valor', 0),
        ];

        $rules = [
            'pessoa' => 'required|integer',
            'vulc_carga_valor' => 'required|numeric|min:0',
            'vulc_agricola_valor' => 'required|numeric|min:0',
            'manchao_valor' => 'nullable|numeric|min:0',
            'manchao_agricola_valor' => 'required|numeric|min:0',
            'enchimento_valor' => 'required|numeric|min:0',
            'enchimento_ombro_1_valor' => 'required|numeric|min:0',
            'enchimento_ombro_2_valor' => 'required|numeric|min:0',
        ];

        $messages = [
            'pessoa.required' => 'O campo Nome Tabela é obrigatório.',
            'pessoa.integer' => 'O Id Pessoa deve ser um número inteiro.',
            'vulc_carga_valor.required' => 'O campo Valor Vulcanização Carga é obrigatório.',
            'vulc_carga_valor.numeric' => 'O campo Valor Vulcanização Carga deve ser um número.',
            'vulc_carga_valor.min' => 'O campo Valor Vulcanização Carga deve ser maior ou igual a zero.',
            'vulc_agricola_valor.required' => 'O campo Valor Vulcanização Agrícola é obrigatório.',
            'vulc_agricola_valor.numeric' => 'O campo Valor Vulcanização Agrícola deve ser um número.',
            'vulc_agricola_valor.min' => 'O campo Valor Vulcanização Agrícola deve ser maior ou igual a zero.',
            'manchao_valor.numeric' => 'O campo Valor Manchão Carga deve ser um número.',
            'manchao_valor.min' => 'O campo Valor Manchão Carga deve ser maior ou igual a zero.',
            'manchao_agricola_valor.required' => 'O campo Valor Manchão Agrícola é obrigatório.',
            'manchao_agricola_valor.numeric' => 'O campo Valor Manchão Agrícola deve ser um número.',
            'manchao_agricola_valor.min' => 'O campo Valor Manchão Agrícola deve ser maior ou igual a zero.',
            'enchimento_valor.required' => 'O campo Valor Enchimento é obrigatório.',
            'enchimento_valor.numeric' => 'O campo Valor Enchimento deve ser um número.',
            'enchimento_valor.min' => 'O campo Valor Enchimento deve ser maior ou igual a zero.',
            'enchimento_ombro_1_valor.required' => 'O campo Valor Enchimento Ombro 1 é obrigatório.',
            'enchimento_ombro_1_valor.numeric' => 'O campo Valor Enchimento Ombro 1 deve ser um número.',
            'enchimento_ombro_1_valor.min' => 'O campo Valor Enchimento Ombro 1 deve ser maior ou igual a zero.',
            'enchimento_ombro_2_valor.required' => 'O campo Valor Enchimento Ombro 2 é obrigatório.',
            'enchimento_ombro_2_valor.numeric' => 'O campo Valor Enchimento Ombro 2 deve ser um número.',
            'enchimento_ombro_2_valor.min' => 'O campo Valor Enchimento Ombro 2 deve ser maior ou igual a zero.',
        ];
        $validator = Validator::make($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Helper::formatErrorsAsHtml($validator);
        }

        $data = $this->tabela->getVulcanizacaoManchao($arr);

        return response()->json(['data' => $data]);
    }

    public function _validate($data)
    {
        $rules = [
            'pessoa' => 'required|integer',
            'desenho' => 'required',
            'medida' => 'required',
            'valor' => 'required|numeric|min:0'
        ];
        $messages = [
            'pessoa.required' => 'O campo Pessoa é obrigatório.',
            'pessoa.integer' => 'O Id Pessoa deve ser um número inteiro.',
            'desenho.required' => 'O campo Desenho é obrigatório.',
            'medida.required' => 'O campo Medida é obrigatório.',
            'valor.required' => 'O campo Valor é obrigatório.',
            'valor.numeric' => 'O campo Valor deve ser um número.',
            'valor.min' => 'O campo Valor deve ser maior ou igual a zero.'
        ];

        return Validator::make($data, $rules, $messages);
    }

    //verifica se já existe tabela cadastrada para o cliente
    public function getVerificaExistsTabelaCadastrada()
    {
        $idTabela = $this->request->input('idTabela');

        $itensTabela = $this->tabela->getItemTabPreco($idTabela, 'tabela_preco');

        if (Helper::is_empty_object($itensTabela)) {
            $itensTabela = $this->tabela->getItemTabPreco($idTabela, 'tabela_preco_preview');
            return response()->json([
                'data' => $itensTabela,
                'success' => true,
                'message' => 'Cliente sem tabela de preço cadastrada.',
            ]);
        }

        return response()->json([
            'data' => $itensTabela,
            'success' => true,
            'message' => 'Já existe uma tabela de preço cadastrada para este cliente, deseja atualiza-la?',
        ]);
    }

    //Salva os itens na tabela temporária para importação
    public function salvaItemTabelaPreco()
    {
        $status = $this->itemTabPreco->saveItemTabPreco($this->request->input('dadosTabela'));

        return $status;
    }

    //Importa a tabela temporária para a tabela definitiva
    public function importarTabelaPreco()
    {
        $cd_tabela = $this->request->input('cd_tabela');

        // Verifica se existe tabela para importar, pega o nome da tabela
        $tabela = $this->tabela->getTabprecoPreview('N', '', $cd_tabela);

        if (Helper::is_empty_object($tabela)) {
            return response()->json([
                'success' => false,
                'message' => 'Não existe tabela para importar ou já foi importada!'
            ]);
        }

        // Pega os itens da tabela temporária, e o nome da tabela 
        $itensTabela = $this->tabela->getItemTabPreco($cd_tabela, 'tabela_preco_preview');

        // Realiza a importação dos itens
        $status = $this->tabela->importarTabelaPreco($tabela[0], $itensTabela);

        return $status;
    }

    public function vincularTabelaPreco()
    {
        $cd_tabela = $this->request->input('cd_tabela');
        $cd_pessoa = $this->request->input('cd_pessoa');

        foreach ($cd_pessoa as $key => $value) {
            $verificaVinculoClienteTabela = $this->tabela->verificaVinculoClienteTabela($cd_tabela, $value);

            if (!Helper::is_empty_object($verificaVinculoClienteTabela)) {
                return  $this->tabela->deletaRecriaVinculoClienteTabela($cd_tabela, $value);
            }
        }
        return  $this->tabela->vincularTabelaPreco($cd_tabela, $cd_pessoa);
    }

    public function deletarTabelaPreco()
    {
        $cd_tabela = decrypt($this->request->input('cd_tabela'));
        $tipo_tabela = $this->request->input('tipo_tabela');

        if ($tipo_tabela === 'tabela_preco') {
            return $this->tabela->destroyTabelaPreco($cd_tabela, $tipo_tabela);
        } else {

            $tabela = $this->tabela->getTabprecoPreview('N', '', $cd_tabela);

            if (Helper::is_empty_object($tabela)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não existe tabela para excluir ou já foi importada!'
                ]);
            }

            return $this->tabela->destroyTabelaPreco($cd_tabela, $tipo_tabela);
        }
    }
    public function cancelarVinculo()
    {
        $cd_tabela = $this->request->input('cd_tabela');
        $cd_pessoa = $this->request->input('cd_pessoa');

        return $this->tabela->cancelarVinculo($cd_tabela, $cd_pessoa);
    }

    public function divergenciaTabelaPreco()
    {
        //Lista as tabelas de preço com divergência de associação
        $data = $this->tabela->divergenciaVinculoTabelaPreco();

        return DataTables::of($data)
            ->make(true);
    }
}
