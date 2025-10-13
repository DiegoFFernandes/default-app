<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class VendedorController extends Controller
{
    public $request, $regiao, $area, $empresa, $user, $vendedor;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Empresa $empresa,
        Vendedor $vendedor
    ) {
        $this->request = $request;
        $this->vendedor = $vendedor;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function searchVendedor()
    {
        // Helper::searchCliente($this->user_auth->conexao)
        $data = [];

        if ($this->request->has('q')) {
            $search = $this->request->q;
            $data = $this->vendedor->FindVendedorJunsoftAll($search);
        }
        return response()->json($data);
    }

    public function index()
    {
        $title_page   = 'Vendedor Comercial';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();
        $user =  $this->user->getData();

        return view('admin.usuarios.vendedor-comercial', compact(
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

        if ($this->vendedor->verifyIfExists($input)) {
            return response()->json(['errors' => 'Vendedor já está vinculado com esse usúario!']);
        };
        $store =  $this->vendedor->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Vendedor vinculado com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }
    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_vendedor'     => 'required|integer',
                'ds_vendedor' => 'string',
                'cd_cadusuario'    => 'integer'
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_vendedor.required'    => 'Por favor informe um vendedor.',
            ]
        );
    }
    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach($empresa as $e){
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->vendedor->showUserVendedor();
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

        return $this->vendedor->updateData($this->request);
    }
    public function destroy()
    {
        $this->vendedor->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }
}
