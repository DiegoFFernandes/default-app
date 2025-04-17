<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pessoa;
use App\Models\TipoPessoa;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public $request, $user_auth, $tipo, $pessoa, $empresa, $user;

    public function __construct(
        Empresa $empresa,
        Request $request,
        Pessoa $pessoa,
        TipoPessoa $tipo,
        User $user
        // EmpresasGrupoPessoa $grupo
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->pessoa = $pessoa;
        $this->tipo = $tipo;
        $this->user = $user;
        // $this->grupo = $grupo;
        $this->middleware(function ($request, $next) {
            $this->user_auth = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Usúarios';
        $uri  = $this->request->route()->uri();
        $user_auth = $this->user_auth;
        // $users     = User::where('id', '<>', 1)->get();
        $empresas  = $this->empresa->empresa();
        // $empresas  = $this->empresa->EmpresaAll();
        $tipopessoa = $this->tipo->TipoPessoa();
        // $cargos = Cargo::all();

        return view('admin.usuarios.index', compact(
            'title_page',
            // 'users',
            'user_auth',
            'empresas',
            // 'uri',
            'tipopessoa',
            // 'cargos'
        ));
    }
    public function listUser()
    {
        $data = User::all();

        return DataTables::of($data)
            ->addColumn('status', function ($data) {
                if (Cache::has('user-is-online-' . $data->id)) {
                    return '<span class="badge badge-success">Online</span>';
                } else {
                    return '<span class="badge bg-gray">Offline</span>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '
                <button type="button" class="btn-editar btn btn-warning btn-xs" data-id="' . $data->id . '">Editar</button> 
                <button type="button" class="btn-delete btn btn-danger btn-xs" data-id="' . $data->id . '">Excluir</button>                     
                ';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }
    public function searchPessoa()
    {
        // Helper::searchCliente($this->user_auth->conexao)
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->pessoa->FindPessoaJunsoftAll($search);
        }
        return response()->json($data);
    }

    public function create()
    {
        $create = User::where('email', $this->request->email)->exists();
        if ($create == 1) {
            return  response()->json(["warning" => "Email já existe, favor cadastrar outro!"]);
        } else {
            $this->request['password'] = Hash::make($this->request['password']);
            $this->request['email'] = strtolower($this->request->email);
            $this->request['name'] = mb_convert_case($this->request->name, MB_CASE_TITLE, 'UTF-8');
            $this->request['phone'] = Helper::RemoveSpecialChar($this->request->phone);
            $this->request['id'] = 0; // zero somente para passar no validate

            $user = $this->__validate();
            if ($user->fails()) {
                $error = self::returnFails($user);
                return response()->json(['error' => $error]);
            }
            $user = $this->user->storeData($user->validated());

            return response()->json([
                'success' => 'Usuário criado com sucesso!'
            ]);
        }
    }
    public function update()
    {
        // verifica se o usuario existe no banco de dados
        // $exist = $this->user->userExists($this->request->id);
        $exist = User::find($this->request->id);        
        if($exist){
            if($this->request['password'] == null){
                $this->request['password'] = $exist->password;
            }else{
                $this->request['password'] = Hash::make($this->request['password']);
            }
            
            $this->request['email'] = strtolower($this->request->email);
            $this->request['name'] = mb_convert_case($this->request->name, MB_CASE_TITLE, 'UTF-8');
            $this->request['phone'] = Helper::RemoveSpecialChar($this->request->phone);
        }
        // return $this->request;
        $user = $this->__validate();
        if ($user->fails()) {
            $error = self::returnFails($user);
            return response()->json(['error' => $error]);
        }
        $user = $this->user->updateData($user->validated());

        return response()->json([
            'success' => 'Usuário atualizado com sucesso!'
        ]);
    }
    public function returnFails($fails)
    {
        $error = '<ul>';

        foreach ($fails->errors()->all() as $e) {
            $error .= '<li>' . $e . '</li>';
        }
        $error .= '</ul>';
    }

    public function destroy()
    {
        $user = $this->user->find($this->request->id)->delete();
        return response()->json(['success' => 'Usuario excluido com sucesso!']);
    }

    public function __validate()
    {
        // return $this->request;
        return Validator::make(
            $this->request->all(),
            [
                'id'       => 'integer',
                'name'     => 'required|max:255',
                'email'    => 'required|email',
                'password' => 'required',
                'empresa'  => ['required', 'integer:1,2,3,4,5,6'],
                'phone' => ['required', 'numeric', 'min:10'],
                'tipopessoa' => 'required'
            ],
            [
                'name.required'    => 'Por favor informe um nome.',
                'email.required'    => 'Por favor informe um email.',
                'password.required' => 'Por favor informe uma senha.',
                'empresa.required'  => 'Por favor informe uma empresa valida.',
                'phone.required' => 'Por favor informe um numero de contato.',
                'phone.numeric' => 'Celular deve numerico',
            ]
        );
    }
}
