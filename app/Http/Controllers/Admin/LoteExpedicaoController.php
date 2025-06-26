<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\LoteExpedicao;
use App\Models\RegiaoComercial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LoteExpedicaoController extends Controller
{
    public $request, $bloqueio, $regiao, $area, $acompanha, $user, $item, $empresa, $expedicao;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Item $item,
        Empresa $empresa,
        LoteExpedicao $expedicao
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->item = $item;
        $this->empresa = $empresa;
        $this->expedicao = $expedicao;

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
                $btn = '<a href="#" class="btn btn-xs btn-primary" title="Abrir">Abrir</a>';
                $btn .= ' <button class="btn btn-xs btn-dark" title="Excluir">Excluir</button>';
                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function createLoteExpedicao()
    {
        // return $data = $this->request->all();

        $validator = $this->_validateLoteExpedicao($this->request->all());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return $validator->validate();

        // if ($lote) {
        //     return response()->json(['status' => 'success', 'message' => 'Lote de expedição criado com sucesso!']);
        // } else {
        //     return response()->json(['status' => 'error', 'message' => 'Erro ao criar lote de expedição.']);
        // }
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
}
