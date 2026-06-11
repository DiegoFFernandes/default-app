<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionUserController extends Controller
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
        $title_page = 'Permissões de Usuários';
        $uri = $this->request->route()->uri();

        return view('admin.usuarios.permission-user', compact('title_page', 'uri'));
    }

    public function listUserPermission()
    {
        $users = User::with('permissions')
            ->whereHas('permissions')
            ->where('id', '!=', 1)
            ->get();

        return DataTables::of($users)
            ->addColumn('permissions_badges', function ($user) {
                $html = '';
                foreach ($user->permissions as $perm) {
                    $html .= '<span class="badge badge-info ml-1">' . $perm->name . '</span>';
                }
                return $html;
            })
            ->addColumn('permission_names', function ($user) {
                return $user->permissions->pluck('name')->values()->toArray();
            })
            ->addColumn('actions', function ($user) {
                return '
                <button type="button" class="btn-editar btn btn-warning btn-xs" data-id="' . $user->id . '">Editar</button>
                <button type="button" class="btn-delete btn btn-danger btn-xs" data-id="' . $user->id . '">Excluir</button>
                ';
            })
            ->rawColumns(['permissions_badges', 'actions'])
            ->make(true);
    }

    public function getUsers()
    {
        $users = User::whereDoesntHave('permissions')
            ->where('id', '!=', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function getPermissions()
    {
        return response()->json(Permission::orderBy('name')->get(['id', 'name']));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'permissions' => 'required|array',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncPermissions($request->permissions);

        return response()->json(['success' => true, 'message' => 'Permissões atribuídas com sucesso!']);
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'permissions' => 'required|array',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncPermissions($request->permissions);

        return response()->json(['success' => true, 'message' => 'Permissões atualizadas com sucesso!']);
    }

    public function removePermissions(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::findOrFail($request->user_id);
        $user->syncPermissions([]);

        return response()->json(['success' => true, 'message' => 'Permissões removidas com sucesso!']);
    }
}
