<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public $request, $user, $role;

    public function __construct(Request $request, Role $role)
    {
        $this->request = $request;
        $this->role = $role;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $title_page = 'Funções Usuários';
        $uri  = $this->request->route()->uri();

        return view('admin.usuarios.roles', compact('title_page', 'uri'));
    }
    public function listUserRole()
    {
        $data = $this->user->listUserRole();

        return DataTables::of($data)

            ->addColumn('role', function ($data) {

                $role = '';
                foreach ($data->getRoleNames() as $v) {
                    $role .= '<span class="badge badge-info ml-1">' . $v  . '</span>';
                }
                return $role;
            })
            ->addColumn('actions', function ($data) {
                return '
                <button type="button" class="btn-editar btn btn-warning btn-xs" data-id="' . $data->id . '">Editar</button> 
                <button type="button" class="btn-delete btn btn-danger btn-xs" data-id="' . $data->id . '">Excluir</button>                     
                ';
            })
            ->rawColumns(['role', 'actions'])
            ->make(true);
    }
    public function edit(Request $request)
    {
        $title     = 'Edição de função de usuários';
        $exploder  = explode('/', $this->request->route()->uri());
        $uri       = ucfirst($exploder[1]);
        $user_auth      = $this->user;
        $userId    = User::findOrFail($request->id);
        $roles     = Role::all()->pluck('name');
        $userRoles = $userId->getRoleNames();
        $array1    = json_decode(json_encode($roles), true);
        $array2    = json_decode(json_encode($userRoles), true);
        $all_roles = array_diff($array1, $array2);

        return view('admin.usuarios.user-role', compact(
            'uri',
            'user_auth',
            'userId',
            'roles',
            'title',
            'userRoles',
            'all_roles'
        ));
    }
    public function update(Request $request)
    {
        $user     = User::findOrFail($request->id);
        $userRole = $request->roles;
        //$user->assignRole($userRole);
        $user->syncRoles($userRole);

        return redirect()->route('admin.usuarios.role')->with('status', 'Nova função Usuário atualizado com sucesso!');
    }
    public function create()
    {
        $all_roles = Role::all()->pluck('name');
        $title     = 'Nova função do usuario';
        $user_auth = $this->user;

        $users = User::select('users.id', 'users.name', 'users.email', 'users.empresa', 'users.created_at')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->whereNull('model_has_roles.model_id')
            ->where('users.id', '<>', '1')
            ->groupBy('users.id', 'users.name')
            ->orderBy('id')->get();

        $exploder = explode('/', $this->request->route()->uri());
        $uri      = ucfirst($exploder[1]);

        return view('admin.usuarios.user-role', compact(
            'uri',
            'users',
            'all_roles',
            'title',
            'user_auth'
        ));
    }
    public function save(Request $request)
    {
        $user = User::findOrFail($request->id);
        $userRole = $request->roles;
        $user->syncRoles($userRole);
        return redirect()->route('admin.usuarios.role')->with('status', 'Nova função usuário criada  com sucesso!');
    }

    public function delete($id)
    {
        DB::table("model_has_roles")->where('model_id', $id)->delete();
        return redirect()->route('admin.usuarios.role')
            ->with('status', 'Função usuario deletado com successo');
    }
}
