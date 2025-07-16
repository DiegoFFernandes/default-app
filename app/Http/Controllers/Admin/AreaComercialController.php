<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class AreaComercialController extends Controller
{
    public $request, $regiao, $area, $empresa, $user, $supervisorComercial;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Empresa $empresa,
        SupervisorComercial $supervisorComercial,
        User $user

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->area = $area;
        $this->supervisorComercial = $supervisorComercial;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function arrayEmpresa()
    {
        $empresa = $this->empresa->empresa();
        foreach ($empresa as $e) {
            $array[] = $e->CD_EMPRESA;
        }
        return $array;
    }
    public function index()
    {
        $title_page   = 'Vincular Supervisor/Coordenador Comercial';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();
        $area = $this->area->areaAll();
        $supervisorComercial = $this->supervisorComercial->SupervisorAll();

        $empresa = $this->arrayEmpresa();
        $user =  $this->user->getData($empresa);
        return view('admin.usuarios.area-comercial', compact(
            'title_page',
            'user_auth',
            'uri',
            'area',
            'user',
            'supervisorComercial'
        ));
    }
    public function create()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        $empresa = $this->arrayEmpresa();

        if ($this->area->verifyIfExists($input)) {
            return response()->json(['errors' => 'Area já está vinculada com esse usúario!']);
        };
        // if ($this->area->verifyIfExistsArea($input['cd_areacomercial'], $empresa)) {
        //     return response()->json(['errors' => 'Já existe essa area associada com usúario!']);
        // }
        // if ($this->area->verifyIfExistsUser($input['cd_usuario'], $empresa)) {
        //     return response()->json(['errors' => 'Já existe esse usuario associado com uma area!']);
        // }

        $store =  $this->area->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Area vinculada com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }

    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_areacomercial'     => 'required|integer',
                'ds_areacomercial' => 'string',
                'cd_cadusuario'    => 'integer',
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_areacomercial.required'    => 'Por favor informe uma região.',
            ]
        );
    }
    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach ($empresa as $e) {
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->area->showUserArea();
        return DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                return '
                <a href="#" class="btn btn-warning btn-sm btn-edit">Editar</a>
                <a href="#" data-id="' . $data->id . '" class="btn btn-danger btn-sm" id="getDeleteId">Excluir</button>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }
    public function update()
    {
        $this->request['cd_cadusuario'] = $this->user->id;        

        $input = $this->_validate($this->request);
        // $empresa = $this->arrayEmpresa();

        if ($this->area->verifyIfExists($input)) {
            return response()->json(['errors' => 'Area já está vinculada com esse usúario!']);
        };
        // if ($this->area->verifyIfExistsArea($input['cd_areacomercial'], $empresa)) {
        //     return response()->json(['errors' => 'Já existe essa area associada com usúario!']);
        // }
        // if ($this->area->verifyIfExistsUser($input['cd_usuario'], $empresa)) {
        //     return response()->json(['errors' => 'Já existe esse usuario associado com uma area!']);
        // }
        return $this->area->updateData($this->request);
    }
    public function destroy()
    {
        $this->area->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
