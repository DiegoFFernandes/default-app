<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\ItemLoteEstoque;
use App\Models\LoteEstoque;
use App\Models\MarcaPneu;
use App\Models\MarcaLoteEstoque;
use App\Models\Subgrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LoteEstoqueController extends Controller
{
    public  $empresa, $request, $lote, $itemlote, $user, $subgrupo, $marca, $marcaLote;

    public function __construct(
        Request $request,
        Empresa $empresa,
        LoteEstoque $lote,
        ItemLoteEstoque $itemlote,
        Subgrupo $subgrupo,
        MarcaLoteEstoque $marcaLote,
        MarcaPneu $marca
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->lote = $lote;
        $this->subgrupo = $subgrupo;
        $this->marcaLote = $marcaLote;
        $this->marca = $marca;
        $this->itemlote = $itemlote;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page   = 'Criar Lote de Entrada';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();
        // $this->itemlote->countData(1);
        $subgrupo = $this->subgrupo->all();
        $marca = $this->marca->all();
        $marcaLote = $this->marcaLote->all();   
        return view('admin.estoque.contagem-estoque.index', compact(
            'title_page',
            'user_auth',
            'uri',
            'subgrupo',
            'marca',
            'marcaLote'

        ));
    }
    public function getLotes()
    {
        $lotes = $this->lote->lotesAll();
        return DataTables::of($lotes)
            ->addColumn('Actions', function ($lotes) {
                if ($lotes->status == 'F') {
                    return '<a href="' . route('item-lote-fechado', Crypt::encryptString($lotes->id)) . '" id="ver-itens" class="btn btn-default btn-xs"><i class="fas fa-eye"></i></a>';
                } else {
                    return '<a href="' . route('add-item-lote.index', Crypt::encryptString($lotes->id)) . '" id="add-itens" class="btn btn-default btn-xs"><i class="fas fa-plus"></i></a>
                    <button type="button" data-idlote="' . $lotes->id . '" class="btn btn-danger btn-xs delete"><i class="fas fa-trash"></i></button>';
                }
            })
            ->rawColumns(['Actions'])
            ->setRowClass(function ($lotes) {
                return $lotes->status == 'F' ? 'bg-success' : 'bg-warning';
            })
            ->make(true);
    }
    public function store(Request $request)
    {
        $request['cd_usuario'] = Auth::user()->id;
        $request['status'] = 'A';
        $validate = $this->_validator($request);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validate->errors()->all()
            ], 422);
        }

        $this->lote->storeData($this->request);

        return response()->json(['success' => 'Lote Criado com sucesso!']);
    }
    public function delete()
    {
        return $this->lote->deleteData($this->request->idlote);
    }
    public function finishLote()
    {
        $qtd_item = $this->itemlote->countData($this->request->id);

        return $this->lote->updateData($this->request, $qtd_item);
    }
    public function _validator(Request $request)
    {
        // tp_produto: 1 - Banda, 2 - Carcaça
        $rules = [
            'ds_lote'  => 'required|string',
            'tp_lote' => 'required|string|in:E,T,I',
            'tp_produto' => 'required|integer|in:1,2',
            'cd_marca' => 'required|integer|exists:marca_pneus,id|in:1'
        ];

        $messages = [
            'ds_lote.required' => 'Descrição deve ser preenchida',
            'tp_lote.required' => 'Tipo lote deve ser preenchido',
            'tp_lote.in' => 'Tipo lote inválido',
            'tp_produto.required' => 'Tipo de produto e obrigatório',
            'tp_produto.in' => 'Selecione um tipo de produto válido',
            'cd_marca.required' => 'Marca é obrigatória',
            'cd_marca.exists' => 'Selecione uma marca válida',
            'cd_marca.in' => 'Peso para essa marca ainda não foi parametrizado!',

        ];

        return Validator::make(
            $request->all(),
            $rules,
            $messages
        );
    }
}
