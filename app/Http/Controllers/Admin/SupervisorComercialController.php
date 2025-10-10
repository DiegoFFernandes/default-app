<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\Subgrupo;
use App\Models\SupervisorComercial;
use App\Models\SupervisorSubgrupo;
use App\Models\User;
use App\Models\Vendedor;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupervisorComercialController extends Controller
{
    public $request, $regiao, $empresa, $user, $vendedor, $supervisor, $subgrupo, $supervisorSubgrupo;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Vendedor $vendedor,
        SupervisorComercial $supervisor,
        SupervisorSubgrupo $supervisorSubgrupo,
        Subgrupo $subgrupo,
        Empresa $empresa,
        User $user

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->vendedor = $vendedor;
        $this->supervisor = $supervisor;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->subgrupo = $subgrupo;
        $this->supervisorSubgrupo = $supervisorSubgrupo;

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
        $subgrupo = $this->subgrupo->subgrupoAll();

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.usuarios.supervisor-comercial', compact(
            'title_page',
            'user_auth',
            'uri',
            'supervisor',
            'user',
            'list_regiao',
            'subgrupo'
        ));
    }
    public function create()
    {
        if ($this->request->libera_acima_param == 1 && $this->request->pc_permitida == '') {
            return response()->json(['error' => 'Parâmetro esta marcado como sim, o campo % permitido deve ser preenchido']);
        }
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request->all());

        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }

        if ($this->supervisor->verifyIfExists($input->validated())) {
            return response()->json(['error' => 'Supervisor já está vinculada com esse usúario!']);
        };
        return $this->supervisor->storeData($input->validated());
    }
    public function _validate($data)
    {
        $rules = [
            'cd_usuario'       => 'required|integer',
            'cd_supervisorcomercial'     => 'required|integer',
            'ds_supervisorcomercial' => 'string',
            'cd_cadusuario'    => 'integer',
            'libera_acima_param' => 'integer',
            'subgrupos'    => 'array|nullable',
        ];
        $messages = [
            'cd_usuario.required'    => 'Por favor informe um nome.',
            'cd_supervisorcomercial.required'    => 'Por favor informe um supervisor.',
        ];

        return Validator::make($data, $rules, $messages);
    }
    public function list()
    {
        $data = $this->supervisor->showUserSupervisor()
            ->load('subgrupos');

        return DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                $btn = '
                <a href="#" class="btn btn-warning btn-xs btn-edit">Editar</a>
                <a href="#" data-id="' . $data->id . '" class="btn btn-danger btn-xs" id="getDeleteId">Excluir</a>';
                return $btn;
            })
            ->addColumn('subgrupos', function ($data) {
                // Acessar diretamente os subgrupos carregados
                $btn = '';
                foreach ($data->subgrupos as $sg) {
                    $btn .= '<span class="badge badge-info mr-1">' . $sg->cd_subgrupo . '</span>';
                }
                return $btn;
            })
            ->addColumn('cd_subgrupos', function ($data) {
                return implode(',', $data->subgrupos->pluck('cd_subgrupo')->toArray());
            })
            ->addColumn('ds_libera_acima', function ($data) {
                return $data->libera_acima_param ? 'Sim' : 'Não';
            })
            ->rawColumns(['Actions', 'subgrupos'])
            ->make(true);
    }
    public function update()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request->all());
        if ($input->fails()) {
            return Helper::formatErrorsAsHtml($input);
        }
        return $this->supervisor->updateData($this->request);
    }
    public function destroy()
    {
        $supervisor = $this->supervisor->find($this->request->id);
        $this->supervisor->destroyData($supervisor);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
