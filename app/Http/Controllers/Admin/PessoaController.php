<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PessoaController extends Controller
{
    public $request, $regiao, $user, $pessoa;
    public function __construct(
        Request $request,
        User $user,
        Pessoa $pessoa

    ) {
        $this->request = $request;
        $this->user = $user;
        $this->pessoa = $pessoa;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Pessoa';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();       
        $user =  $this->user->getData();
        
        return view('admin.usuarios.pessoa', compact(
            'title_page',
            'user_auth',
            'uri',
            'user'
        ));
    }
    public function create()
    {        
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);

        if ($this->pessoa->verifyIfExists($input)) {
            return response()->json(['errors' => 'Pessoa já está vinculada com esse usúario!']);
        };
        $store =  $this->pessoa->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Pessoa vinculada com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }
    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_pessoa'     => 'required|integer',
                'nm_pessoa' => 'string',
                'cd_cadusuario'    => 'integer'                               
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_pessoa.required'    => 'Por favor informe uma pessoa.',
            ]
        );
    }
    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach($empresa as $e){
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->pessoa->showUserPessoa();
        return DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                return '
                <a href="#" class="btn btn-warning btn-xs btn-edit">Editar</a>
                <a href="#" data-id="' . $data->id . '" class="btn btn-danger btn-xs" id="getDeleteId">Excluir</a>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }
    public function update()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        
        return $this->pessoa->updateData($this->request);
    }
    public function destroy()
    {
        $this->pessoa->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
