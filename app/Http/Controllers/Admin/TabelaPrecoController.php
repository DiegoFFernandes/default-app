<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\TabPreco;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TabelaPrecoController extends Controller
{
    public $request, $user_auth, $tipo, $pessoa, $empresa, $user, $tabela;

    public function __construct(
        Empresa $empresa,
        Request $request,
        Pessoa $pessoa,
        User $user,
        TabPreco $tabela
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->pessoa = $pessoa;
        $this->user = $user;
        $this->tabela = $tabela;
        $this->middleware(function ($request, $next) {
            $this->user_auth = Auth::user();
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
                    <button class="btn btn-xs btn-danger btn-ver-itens" data-nm_tabela="' . $row->DS_TABPRECO . '" data-cd_tabela="' . $row->CD_TABPRECO . '">Itens</button>                    
                ';
            })
            ->addColumn('clientes_associados', function ($row) {
                $btn = '<span class="btn-detalhes details-control mr-2" data-cd_tabela="' . $row->CD_TABPRECO . '"><i class="fas fa-plus-circle"></i></span> ' . $row->CD_TABPRECO;
                return $btn;
            })
            ->setRowClass(function ($row) {
                return $row->ASSOCIADOS > 0 ? 'bg-green' : '';
            })
            ->rawColumns(['action', 'clientes_associados'])
            ->make(true);
    }
    public function getItemTabPreco()
    {
        $cd_tabela = $this->request->get('cd_tabela');
        $data = $this->tabela->getItemTabPreco($cd_tabela);

        return DataTables::of($data)
            ->make(true);
    }
    public function getTabClientePreco()
    {
        $cd_tabela = $this->request->cd_tabela;
        $data = $this->tabela->getTabClientePreco($cd_tabela);

        return DataTables::of($data)
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
        $select = $this->request->select;

        $idDesenho = is_array($idDesenho) ? implode(',', $idDesenho) : $idDesenho;
        $idMedida = is_array($idMedida) ? implode(',', $idMedida) : $idMedida;

        $data = $this->tabela->getSelectTabPreco($select, $idDesenho, $idMedida, $valor);

        return response()->json(['data' => $data]);
    }

    public function _validate($data)
    {
        $rules =[
                'desenho' => 'required',
                'medida' => 'required',
                'valor' => 'required|numeric|min:0'
            ];
        $messages = [
            'desenho.required' => 'O campo Desenho é obrigatório.',
            'medida.required' => 'O campo Medida é obrigatório.',
            'valor.required' => 'O campo Valor é obrigatório.',
            'valor.numeric' => 'O campo Valor deve ser um número.',
            'valor.min' => 'O campo Valor deve ser maior ou igual a zero.'
        ];

        return Validator::make($data, $rules, $messages);
    }
}
