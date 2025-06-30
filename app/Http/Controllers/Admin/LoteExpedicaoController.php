<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\ItemLoteExpedicaoPneu;
use App\Models\LoteExpedicao;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LoteExpedicaoController extends Controller
{
    public $request, $bloqueio, $regiao, $area, $acompanha, $user, $item, $empresa, $expedicao, $producao, $itemLoteExpedicaoPneu;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Item $item,
        Empresa $empresa,
        Producao $producao,
        LoteExpedicao $expedicao,
        ItemLoteExpedicaoPneu $itemLoteExpedicaoPneu    
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->item = $item;
        $this->empresa = $empresa;
        $this->expedicao = $expedicao;
        $this->producao = $producao;
        $this->itemLoteExpedicaoPneu = $itemLoteExpedicaoPneu;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Lote de Expedição';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        $empresas       = $this->empresa->empresa();
        $ur = explode('/', $uri);
        $uri = $uri[1];

        return view('admin.expedicao.loteexpedicao', compact(
            'title_page',
            'user_auth',
            'uri',
            'empresas'
        ));
    }

    public function getLoteExpedicao()
    {
        $data = $this->expedicao->ListLoteExpedicao();
        return DataTables::of($data)
            ->addColumn('actions', function ($data) {
                $btn = '<a href="' . route('show-item-lote-expedicao', ['lote' => $data->LOTE, 'idempresa' => $data->IDEMPRESA]) . '" class="btn btn-xs btn-primary btnOpen" title="Abrir">Abrir</a>';
                $btn .= ' <button class="btn btn-xs btn-dark btnDelete" title="Excluir">Excluir</button>';
                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function createLoteExpedicao()
    {
        $validator = $this->_validateLoteExpedicao($this->request->all());
        // return $validator->validated();

        if ($validator->fails()) {
            $error = '<ul>';
            foreach ($validator->errors()->all() as $e) {
                $error .= '<li>' . $e . '</li>';
            }
            $error .= '</ul>';
            return response()->json(['errors' => $error]);
        }

        //busca o último ID do lote de expedição para a empresa selecionada
        $idValido = $this->expedicao->retornaUltimoID($validator->validated()['empresa']);

        //Inser as informações no loteExpedicaoPneu no Junsoft
        $store = $this->expedicao->storeData($idValido, $validator->validated());

        if ($store) {
            return response()->json(['success' => 'Lote de expedição criado com sucesso!']);
        } else {
            return response()->json(['errors' => 'Erro ao criar lote de expedição.']);
        }
    }

    public function _validateLoteExpedicao($data)
    {
        $rules = [
            'empresa' => 'required|integer',
            'vendedor' => 'required|integer',
            'emissao' => 'required|date_format:Y-m-d',
            'observacao' => 'nullable|string|max:255',
        ];

        $messages = [
            'empresa.required' => 'O campo Empresa é obrigatório.',
            'empresa.integer' => 'O campo Empresa deve ser um número inteiro.',
            'vendedor.required' => 'O campo Vendedor é obrigatório.',
            'vendedor.integer' => 'O campo Vendedor deve ser um número inteiro.',
            'emissao.required' => 'O campo Emissão é obrigatório.',
            'emissao.date_format' => 'O campo Emissão deve estar no formato Y-m-d.',
            'observacao.string' => 'O campo Observação deve ser uma string.',
            'observacao.max' => 'O campo Observação não pode ter mais de 255 caracteres.'
        ];

        return Validator::make($data, $rules, $messages);
    }

    public function showItemLoteExpedicao()
    {
        $lote = $this->request->lote;
        $idempresa = $this->request->idempresa;
        $title_page   = 'Itens Lote de Expedição';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        $empresas       = $this->empresa->empresa();
        $ur = explode('/', $uri);
        $uri = $uri[1];

        return view('admin.expedicao.itemloteexpedicao', compact(
            'title_page',
            'user_auth',
            'uri',
            'empresas',
            'idempresa',
            'lote'
        ));
    }
    public function searchOrdemProducao()
    {
        $validator = Validator::make($this->request->all(), [
            'nr_ordem' => 'required|integer',
            'idempresa' => 'required|integer',
        ]);

        $finalizada = $this->producao->OrdemFinalizadaExameFinal($this->request->nr_ordem);

        if (Helper::is_empty_object($finalizada)) {
            return response()->json(['errors' => 'A ordem de produção  ainda não foi finalizada no exame final.']);
        } 

        $searchOrdem = $this->producao->getOrdemProducaoById($this->request);        

        if (Helper::is_empty_object($searchOrdem)) {
            return response()->json(['errors' => 'Ordem de produção não encontrada.']);
        }        
        return response()->json(['success' => true, 'data' => $searchOrdem[0]]);             

       
    }
    public function storeItemLoteExpedicao()
    {
        // return $this->request->all();
        $validator = Validator::make($this->request->all(), [
            'lote' => 'required|integer',
            'idempresa' => 'required|integer',
            'nr_ordem' => 'required|integer'            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }      

        $exists = $this->itemLoteExpedicaoPneu->existsItemLoteExpedicao(
            $validator->validated()['lote'],
            $validator->validated()['idempresa'],
            $validator->validated()['nr_ordem']
        );

        if ($exists) {
            return response()->json(['errors' => 'Item já existe no lote de expedição.']);
        }            

        $data = $this->itemLoteExpedicaoPneu->storeData($validator->validated());

        if ($data) {
            return response()->json(['success' => true, 'message' => 'Item adicionado com sucesso!']);
        } else {
            return response()->json(['errors' => 'Erro ao adicionar item.']);
        }
    }

    public function listItemLoteExpedicao()
    {
        $lote = $this->request->lote;
        $idempresa = $this->request->idempresa;

        $data = $this->itemLoteExpedicaoPneu->getListItemLoteExpedicao($lote, $idempresa);

        return DataTables::of($data)
            ->addColumn('actions', function ($data) {
                if($data->STLOTE == 'F') {
                    $btn = '<button class="btn btn-xs btn-info" disabled>Finalizado</button>';
                }else{
                    $btn = '<button data-id="' . $data->ID . '" data-ordem="' . $data->NRORDEM . '" class="btn btn-xs btn-danger btnDelete" title="Excluir">Excluir</button>';
                }
                
                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function deleteItemLoteExpedicao()
    {

        $validator = Validator::make($this->request->all(), [
            'id' => 'required|integer',
            'nr_ordem' => 'required|integer',
            'lote' => 'required|integer',
            'idempresa' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }       

        $delete = $this->itemLoteExpedicaoPneu->deleteItemLoteExpedicao($validator->validated());

        if ($delete) {
            return response()->json(['success' => true, 'message' => 'Item excluído com sucesso!']);
        } else {
            return response()->json(['errors' => 'Erro ao excluir item.']);
        }
    }
}
