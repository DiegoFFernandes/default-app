<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GerenteUnidadeController extends Controller
{
    public $request, $regiao, $area, $empresa, $user, $supervisorComercial, $gerenteUnidade;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Empresa $empresa,
        SupervisorComercial $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        User $user

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->area = $area;
        $this->supervisorComercial = $supervisorComercial;
        $this->empresa = $empresa;
        $this->gerenteUnidade = $gerenteUnidade;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Gerente Unidade';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();
        $area = $this->area->areaAll();
        $supervisor = $this->supervisorComercial->SupervisorAll();
        $user =  $this->user->getData();
        $empresa  = $this->empresa->empresa();

        return view('admin.usuarios.gerente-unidade', compact(
            'title_page',
            'user_auth',
            'uri',
            'empresa',
            'user'
        ));
    }
    public function create()
    {
        $this->request['cd_cadusuario'] = $this->user->id;

        
        $validator = $this->_validate($this->request->all());
        
        if ($validator->fails()) {
            return Helper::formatErrorsAsHtml($validator);
        }

        if ($this->gerenteUnidade->verifyIfExists($validator->validated())) {
            return response()->json(['errors' => 'Gerente já está vinculada com esse usúario!']);
        }
        $store =  $this->gerenteUnidade->storeData($validator->validated());
        if ($store) {
            return response()->json(['success' => 'Gerente vinculado com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }
    
    public function list()
    {
        $data = $this->gerenteUnidade->showUserGerente();
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

        $input = $this->_validate($this->request->all());

        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input->errors());
        }

        if ($this->gerenteUnidade->verifyIfExists($input->validated())) {
            return response()->json(['errors' => 'Gerente já está vinculado com esse usúario e empresa!']);
        };

        return $this->gerenteUnidade->updateData($this->request);
    }
    public function destroy()
    {
        $this->gerenteUnidade->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }

    public function _validate($data)
    {
        $rules =
            [
                'cd_usuario'       => 'required|integer',
                'cd_gerenteunidade'     => 'required|integer',
                'ds_gerenteunidade' => 'required|string',
                'cd_cadusuario'    => 'integer',
                'cd_empresa'       => 'required|integer',
            ];

        $messages = [
            'cd_usuario.required'    => 'Por favor informe um usuario.',
            'cd_usuario.integer' => 'O usuário deve ser valido.',
            'cd_gerenteunidade.required'    => 'O gerente deve ser valido.',
            'cd_gerenteunidade.integer' => 'O gerente deve ser valido.',
            'ds_gerenteunidade.string' => 'A descrição do gerente deve ser uma string.',
            'ds_gerenteunidade.required' => 'Por favor informe um Gerente.',
            'cd_cadusuario.integer' => 'O usuário deve ser valido.',
            'cd_empresa.required' => 'Por favor informe uma empresa.',
            'cd_empresa.integer' => 'A empresa deve ser valido.'
        ];

        return Validator::make($data, $rules, $messages);       
    }
}
