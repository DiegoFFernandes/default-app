<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\MedidaPneu;
use App\Models\ModeloPneu;
use App\Models\User;
use App\Services\ServiceEstoqueNegativo;
use Illuminate\Support\Facades\Validator;
use Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class EstoqueController extends Controller
{
    protected Empresa $empresa;
    protected Request $request;
    protected User $user;
    protected Estoque $estoque;
    protected MedidaPneu $medidapneu;
    protected ModeloPneu $modelopneu;
    protected ServiceEstoqueNegativo $serviceEstoqueNegativo;

    public function __construct(
        Empresa $empresa,
        Request $request,
        User $user,
        Estoque $estoque,
        MedidaPneu $medidapneu,
        ModeloPneu $modelopneu,
        ServiceEstoqueNegativo $serviceEstoqueNegativo
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->user = $user;
        $this->estoque = $estoque;
        $this->medidapneu = $medidapneu;
        $this->modelopneu = $modelopneu;
        $this->serviceEstoqueNegativo = $serviceEstoqueNegativo;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function estoqueNegativo()
    {
        $title_page  = 'Estoque Negativo';
        $user_auth   = $this->user;
        $uri         = $this->request->route()->uri();

        return view("admin.estoque.estoque-negativo", compact('uri', 'title_page', 'user_auth'));
    }

    public function getEstoqueNegativo()
    {
        $estoqueNegativo = $this->serviceEstoqueNegativo->EstoqueNegativo();

        return datatables()->of($estoqueNegativo)->toJson();
    }

    public function carcacaCasa()
    {
        $title_page  = 'Carcaças da Casa';
        $user_auth   = $this->user;
        $uri         = $this->request->route()->uri();

        // Verifica se o usuário tem permissão de edição e enviar a view para bloquear ou liberar as ações
        $canEdit = $this->user->hasRole('vendedor|supervisor|gerente comercial');

        return view(
            'admin.estoque.carcaca-casa.carcaca-casa',
            compact(
                'uri',
                'title_page',
                'user_auth',
                'canEdit'
            )
        );
    }

    public function getCarcacaCasa()
    {
        $data = $this->estoque->getCarcacasDaCasa();

        $canEdit = $this->user->hasRole('vendedor|supervisor|gerente comercial');


        $datatable = Datatables()
            ->of($data)
            ->addColumn('action', function ($row) use ($canEdit) {
                if ($canEdit) {
                    return '';
                }

                $btn = '<button class="btn btn-xs btn-editar btn-secondary mr-1 btn-sm-phone" data-id="' . $row->ID . '" title="Editar"><i class="fas fa-edit"></i></button>';
                $btn .= '<button class="btn btn-xs btn-baixar btn-secondary mr-1 btn-sm-phone" data-id="' . $row->ID . '" title="Baixar"><i class="fas fa-sign-out-alt"></i></button>';
                $btn .= '<button class="btn btn-xs btn-deletar btn-secondary mr-1 btn-sm-phone" data-id="' . $row->ID . '" title="Deletar"><i class="fas fa-trash-alt"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true)
            ->getData();


        $arrayCarcacaLocal =  $this->agruparArrayCarcacaLocal($data, 'LOCAL_ESTOQUE', 'DSMEDIDAPNEU', 'DSMARCA', 'DSMODELO1');            

        return response()->json([
            'datatable' => $datatable, // <-- agora é apenas o array,
            'accordion_data_local_marca'   => array_values($arrayCarcacaLocal),
            // <-- estrutura para o acordeão local > medida > marca > modelo            
            'extra' => [
                'total_carcacas'   => count($data),
                'local_agrupado'   => $this->agruparCarcacaLocalQtd($data),
                'marca_agrupado'   => $this->agruparCarcacaMarcaQtd($data),
                // 'accordion_data'   => $this->agruparArrayCarcaca($data, $nivel1 = 'DSMARCA', $nivel2 = 'DSMEDIDAPNEU', $nivel3 = 'DSMODELO'),
                // 'accordion_data_local'   => $this->agruparArrayCarcaca($data, $nivel1 = 'LOCAL_ESTOQUE', $nivel2 = 'DSMEDIDAPNEU', $nivel3 = 'DSMODELO'),
                ]
        ]);
    }

    public function getCarcacaCasaBaixas()
    {
        $data = $this->estoque->getCarcacasDaCasa(null, 'B');

        $datatable = Datatables()
            ->of($data)
            ->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<button class="btn btn-xs btn-cancelar-baixa btn-secondary mr-1" data-id="' . $row->ID . '" title="Cancelar Baixar"><i class="fas fa-undo"></i></button>';
                return $btn;
            })
            ->make(true)
            ->getData();

        return response()->json([
            'datatable' => $datatable // <-- agora é apenas o array            
        ]);
    }

    public function agruparCarcacaLocalQtd($data)
    {
        $result = [];

        foreach ($data as $item) {
            $local = $item->LOCAL_ESTOQUE;

            if (!isset($result[$local])) {
                $result[$local] = 0;
            }
            $result[$local]++;
        }
        return $result;
    }
    public function agruparCarcacaMarcaQtd($data)
    {
        $result = [];

        foreach ($data as $item) {
            $marca = $item->DSMARCA;

            if (!isset($result[$marca])) {
                $result[$marca] = 0;
            }
            $result[$marca]++;
        }
        return $result;
    }

    public function agruparArrayCarcaca($data, $nivel1 = 'DSMARCA', $nivel2 = 'DSMEDIDAPNEU', $nivel3 = 'DSMODELO')
    {
        $result = [];

        foreach ($data as $item) {
            $nivel1Key = $item->{$nivel1};

            // Marca
            if (!isset($result[$nivel1Key])) {
                $result[$nivel1Key] = [
                    'qtd'    => 0,
                    'medida' => [],
                ];
            }

            $result[$nivel1Key]['qtd']++;

            // Medida Pneu
            $nivel2Key = $item->{$nivel2};
            if (!isset($result[$nivel1Key]['medida'][$nivel2Key])) {
                $result[$nivel1Key]['medida'][$nivel2Key] = [
                    'modelo' => [],
                    'qtd'    => 0,
                ];
            }
            $result[$nivel1Key]['medida'][$nivel2Key]['qtd']++;

            // Modelo Pneu
            $modelo = $item->{$nivel3};
            if (!isset($result[$nivel1Key]['medida'][$nivel2Key]['modelo'][$modelo . ' - ' . $item->DS_TIPO])) {
                $result[$nivel1Key]['medida'][$nivel2Key]['modelo'][$modelo . ' - ' . $item->DS_TIPO] = [
                    'qtd'    => 0,
                ];
            }
            $result[$nivel1Key]['medida'][$nivel2Key]['modelo'][$modelo . ' - ' . $item->DS_TIPO]['qtd']++;
        }

        return $result;
    }

    public function agruparArrayCarcacaLocal($data, $nivel1 = 'LOCAL_ESTOQUE',  $nivel2 = 'DSMEDIDAPNEU', $nivel3 = 'DSMARCA', $nivel4 = 'DSMODELO1')
    {
        $result = [];

        foreach ($data as $item) {
            $nivel1Key = $item->{$nivel1};

            // local
            $local = $item->{$nivel1} ?? 'Sem Local';
            if (!isset($result[$nivel1Key])) {
                $result[$nivel1Key] = [
                    'local'  => $local,
                    'qtd'    => 0,
                    'medida' => []
                ];
            }
            $result[$nivel1Key]['qtd']++;

            // Medida
            $nivel2Key = $item->{$nivel2} ?? 'Sem Medida';
             if (!isset($result[$nivel1Key]['medida'][$nivel2Key])) {
                $result[$nivel1Key]['medida'][$nivel2Key] = [
                    'medida' => $nivel2Key,
                    'qtd'    => 0,
                    'marca' => []                    
                ];
            }

            $result[$nivel1Key]['medida'][$nivel2Key]['qtd']++;

            // Marca
            $marca = $item->{$nivel3} ?? 'Sem Marca';
            if (!isset($result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca])) {
                $result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca] = [
                    'marca' => $marca,
                    'qtd'    => 0,
                    'modelo' => [],
                ];
            }
            $result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca]['qtd']++;


            // Modelo
            $modelo = $item->{$nivel4} ?? 'Sem Modelo';
            if (!isset($result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca]['modelo'][$modelo . ' - ' . $item->DS_TIPO])) {
                $result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca]['modelo'][$modelo . ' - ' . $item->DS_TIPO] = [
                    'modelo' => $modelo . ' - ' . $item->DS_TIPO,
                    'qtd'    => 0,
                ];
            }
            $result[$nivel1Key]['medida'][$nivel2Key]['marca'][$marca]['modelo'][$modelo . ' - ' . $item->DS_TIPO]['qtd']++;
        }

        // return $result;

        //Normaliza a estrutura para o formato esperado pelo frontend       
        foreach ($result as &$local) {
            $local['medida'] = array_values($local['medida']);
            foreach ($local['medida'] as &$medida) {
                $medida['marca'] = array_values($medida['marca']);
                foreach ($medida['marca'] as &$marca) {
                    $marca['modelo'] = array_values($marca['modelo']);
                }
                unset($marca);                
            }
            unset($medida);
        }
        unset($local);
        

        return $result;
    }

    public function storeCarcaca()
    {
        $input = $this->_validate('create');

        //    return $input->errors();
        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }

        $data = $this->estoque->storeCarcaca($input->validated());

        return response()->json($data);
    }

    public function editCarcaca()
    {
        $idExists = $this->estoque->verifyCarcacaExists($this->request->id);

        if (!$idExists) {
            return response()->json(['error' => true, 'message' => 'Carcaça não encontrada.']);
        }

        $input = $this->_validate('edit');

        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }

        $data = $this->estoque->editCarcaca($input->validated());

        return response()->json($data);
    }

    public function _validate($editOrCreate)
    {
        $rules = [
            'medida' => 'integer|required',
            'modelo' => 'integer|required',
            'fogo'      => 'integer|nullable',
            'serie'     => 'string|required',
            'dot'       => 'string|required',
            'valor'     => 'numeric|required',
            'tipo'      => 'integer|required|in:1,2,3',
            'local'     => 'integer|required|in:1,3,5,6',
        ];

        if ($editOrCreate === 'edit') {
            $rules['id'] = 'required|integer';
        }

        $messages = [
            'medida.integer' => 'Medida inválida.',
            'medida.required' => 'Por favor, informe a medida do pneu.',
            'modelo.integer' => 'Modelo inválido.',
            'modelo.required' => 'Por favor, informe o modelo do pneu.',
            'fogo.integer'      => 'Número de fogo inválido.',
            'serie.string'     => 'Número de série inválido.',
            'serie.required'     => 'Por favor, informe o número de série.',
            'dot.string'       => 'Número DOT inválido.',
            'dot.required'     => 'Por favor, informe o número DOT.',
            'valor.numeric'     => 'Valor inválido.',
            'valor.required'     => 'Por favor, informe o valor.',
            'tipo.integer'      => 'Tipo inválido.',
            'tipo.required'     => 'Por favor, informe o tipo da carcaça.',
            'local.integer'      => 'Local inválido.',
            'local.required'     => 'Por favor, informe o local da carcaça.',
        ];

        return Validator::make($this->request->all(), $rules, $messages);
    }

    public function deleteCarcaca()
    {
        $input = Validator::make($this->request->all(), [
            'id' => 'array|required',
            'status' => 'string|required|in:B,D,A',
        ], [
            'id.integer' => 'ID inválido.',
            'id.required' => 'ID é obrigatório.',
            'id.exists' => 'Carcaça não encontrada.',
            'status.string' => 'Status inválido.',
            'status.required' => 'Status é obrigatório.',
            'status.in' => 'Status inválido.',
        ]);

        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }

        $ids = $input->validated()['id'];

        $data = $this->estoque->deleteCarcaca($ids, $this->request->status);

        return response()->json($data);
    }

    public function transferCarcaca()
    {
        $input = Validator::make($this->request->all(), [
            'ids' => 'array|required',
            'local' => 'integer|required|in:1,3,5,6',
        ], [
            'ids.integer' => 'ID inválido.',
            'ids.required' => 'ID é obrigatório.',
            'local.integer' => 'Local inválido.',
            'local.required' => 'Local é obrigatório.',
            'local.in' => 'Local inválido.',
        ]);

        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }

        $ids = $input->validated()['ids'];
        $local = $input->validated()['local'];

        $data = $this->estoque->transferCarcaca($ids, $local);

        return response()->json($data);
    }

    public function getCarcacaCasaProntas(){
        $data = $this->estoque->getCarcacaCasaProntas();

        $arrayCarcacaProntasLocal =  $this->agruparArrayCarcacaLocal($data, 'LOCAL_ESTOQUE', 'DSMEDIDAPNEU', 'DESENHOPNEU', 'DSMODELO');

        $datatable = Datatables()
            ->of($data)
            ->addColumn('action', function ($row) {
                $btn = '<button class="btn btn-xs btn-reservar btn-success" data-id="' . $row->NR_ORDEM . '" title="Reservar Pneu"><i class="fas fa-circle fa-xs"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true)
            ->getData();

        return response()->json([
            'datatable' => $datatable,
            'total_carcacas_prontas' => count($data),
            'accordion_data_local_marca'   => array_values($arrayCarcacaProntasLocal),         
        ]);
    }

    //Ajustar esses dois métodos abaixo para um service específico de pneus
    public function searchMedidasPneu()
    {
        // Helper::searchCliente($this->user_auth->conexao)
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->medidapneu->searchMedidasPneusCasa($search);
        }
        return response()->json($data);
    }

    public function searchModeloPneu()
    {
        // Helper::searchCliente($this->user_auth->conexao)
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->modelopneu->searchModeloPneu($search);
        }
        return response()->json($data);
    }
}
