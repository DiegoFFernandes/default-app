<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\ItemLoteEntradaEstoque;
use App\Models\LoteEntradaEstoque;
use App\Models\MarcaPneu;
use App\Models\Subgrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LoteEntradaEstoqueController extends Controller
{
    public  $empresa, $request, $lote, $itemlote, $user, $subgrupo;

    public function __construct(
        Request $request,
        Empresa $empresa,
        LoteEntradaEstoque $lote,
        ItemLoteEntradaEstoque $itemlote,
        Subgrupo $subgrupo,
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->lote = $lote;
        $this->subgrupo = $subgrupo;
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
        $subgrupo = $this->subgrupo->subgrupoAll();
        // $marca = $this->marca->all();
        return view('admin.estoque.contagem-estoque.index', compact(
            'title_page',
            'user_auth',
            'uri',
            'subgrupo',
            // 'marca'

        ));
    }
    public function getLotes()
    {
        $lotes = $this->lote->lotesAll();
        return DataTables::of($lotes)
            ->addColumn('Actions', function ($lotes) {
                if ($lotes->status == 'F') {
                    return '<a href="' . route('item-lote-fechado', Crypt::encryptString($lotes->id)) . '" id="ver-itens" class="btn btn-info btn-sm">Ver Itens</a>';
                } else {
                    return '<a href="' . route('add-item-lote.index', Crypt::encryptString($lotes->id)) . '" id="add-itens" class="btn btn-default btn-sm">Add Itens</a>
                    <button type="button" data-idlote="' . $lotes->id . '" class="btn btn-danger btn-sm delete">Excluir</button>';
                }
            })
            ->rawColumns(['Actions'])
            ->setRowClass(function ($lotes) {
                return $lotes->status == 'F' ? 'alert-success' : 'alert-warning';
            })
            ->make(true);
    }
    public function store(Request $request)
    {
        $request['cd_usuario'] = Auth::user()->id;
        $request['status'] = 'A';
        $validator = $this->_validator($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
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
        return Validator::make(
            $request->all(),
            ['ds_lote'  => 'required'],
            ['ds_lote.required' => 'Descrição deve ser preenchida'],
            ['tp_lote' => 'required'],
            ['tp_lote.required' => 'Tipo lote deve ser preenchido'],
            ['cd_subgrupo' => 'required|integer'],
            ['cd_subgrupo.required' => 'Tipo de produto e obrigatório']
        );
    }
}
