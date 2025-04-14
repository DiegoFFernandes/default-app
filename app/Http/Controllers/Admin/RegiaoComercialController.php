<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RegiaoComercialController extends Controller
{
    public $request, $regiao, $empresa, $user;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Região Comercial';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();
        $regiao = $this->regiao->regiaoAll();
        $user =  $this->user->getData();

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.usuarios.regiao-comercial', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'user',
            'list_regiao'
        ));
    }
    public function create()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        if ($this->regiao->verifyIfExists($input)) {
            return response()->json(['errors' => 'Região já está vinculada com esse usúario!']);
        };
        $store =  $this->regiao->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Região vinculada com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }

    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_regiaocomercial'     => 'required|integer',
                'ds_regiaocomercial' => 'string',
                'cd_cadusuario'    => 'integer',
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_regiaocomercial.required'    => 'Por favor informe uma região.',
            ]
        );
    }
    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach($empresa as $e){
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->regiao->showUserRegiao();
        return DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                return '
                <a href="#" class="btn btn-warning btn-sm btn-edit">Editar</a>
                <a href="#" data-id="' . $data->id . '" class="btn btn-danger btn-sm" id="getDeleteId">Excluir</button>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }
    public function update(){
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        if ($this->regiao->verifyIfExists($input)) {
            return response()->json(['errors' => 'Região já está vinculada com esse usúario!']);
        };
        return $this->regiao->updateData($this->request);
    }
    public function destroy(){
        $this->regiao->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
