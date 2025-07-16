<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupervisorComercialController extends Controller
{

    public $request, $regiao, $empresa, $user, $vendedor, $supervisor;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Vendedor $vendedor,
        SupervisorComercial $supervisor,
        Empresa $empresa,
        User $user

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->vendedor = $vendedor;
        $this->supervisor = $supervisor;
        $this->user = $user;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Supervisor Comercial';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();
        $supervisor = $this->supervisor->SupervisorAll();
        $user =  $this->user->getData();

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.usuarios.supervisor-comercial', compact(
            'title_page',
            'user_auth',
            'uri',
            'supervisor',
            'user',
            'list_regiao'
        ));
    }
    public function create()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        if ($this->supervisor->verifyIfExists($input)) {
            return response()->json(['errors' => 'Supervisor já está vinculada com esse usúario!']);
        };
        $store =  $this->supervisor->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Supervisor vinculado com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }
    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_supervisorcomercial'     => 'required|integer',
                'ds_supervisorcomercial' => 'string',
                'cd_cadusuario'    => 'integer',
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_supervisorcomercial.required'    => 'Por favor informe um supervisor.',
            ]
        );
    }
    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach($empresa as $e){
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->supervisor->showUserSupervisor();
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
        if ($this->regiao->verifyIfExists($input)) {
            return response()->json(['errors' => 'Região já está vinculada com esse usúario!']);
        };
        return $this->regiao->updateData($this->request);
    }
    public function destroy()
    {
        $this->supervisor->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
