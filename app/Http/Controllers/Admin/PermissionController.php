<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public $request, $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Permissões de Funções';
        $uri = $this->request->route()->uri();

        return view('admin.usuarios.permission', compact('title_page', 'uri'));
    }

    public function listRolePermission()
    {
        $roles = Role::with('permissions')->whereHas('permissions')->get();

        return DataTables::of($roles)
            ->addColumn('permissions_badges', function ($role) {
                $html = '';
                foreach ($role->permissions as $perm) {
                    $html .= '<span class="badge badge-info ml-1">' . $perm->name . '</span>';
                }
                return $html;
            })
            ->addColumn('permission_names', function ($role) {
                return $role->permissions->pluck('name')->values()->toArray();
            })
            ->addColumn('actions', function ($role) {
                return '
                <button type="button" class="btn-editar btn btn-warning btn-xs" data-id="' . $role->id . '">Editar</button>
                <button type="button" class="btn-delete btn btn-danger btn-xs" data-id="' . $role->id . '">Excluir</button>
                ';
            })
            ->rawColumns(['permissions_badges', 'actions'])
            ->make(true);
    }

    public function getRoles()
    {
        $roles = Role::whereDoesntHave('permissions')->orderBy('name')->get(['id', 'name']);

        return response()->json($roles);
    }

    public function getPermissions()
    {
        return response()->json(Permission::orderBy('name')->get(['id', 'name']));
    }

    public function createAndAssign(Request $request)
    {
        $request->validate([
            'role_name'   => 'required|string|max:100|unique:roles,name',
            'permissions' => 'required|array',
        ], [
            'role_name.unique' => 'Já existe uma função com esse nome.',
        ]);

        $role = Role::create(['name' => $request->role_name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return response()->json(['success' => true, 'message' => 'Função "' . $role->name . '" criada com sucesso!']);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'role_id'     => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->syncPermissions($request->permissions);

        return response()->json(['success' => true, 'message' => 'Permissões atribuídas com sucesso!']);
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'role_id'     => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->syncPermissions($request->permissions);

        return response()->json(['success' => true, 'message' => 'Permissões atualizadas com sucesso!']);
    }

    public function removePermissions(Request $request)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);

        $role = Role::findOrFail($request->role_id);
        $role->syncPermissions([]);

        return response()->json(['success' => true, 'message' => 'Permissões removidas com sucesso!']);
    }

    public function edit()
    {
        return redirect()->route('usuario.permission');
    }

    public function update()
    {
        return redirect()->route('usuario.permission');
    }

    public function create()
    {
        return redirect()->route('usuario.permission');
    }

    public function save()
    {
        return redirect()->route('usuario.permission');
    }

    public function delete()
    {
        return redirect()->route('usuario.permission');
    }
}
