<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ConfigurationUsersController extends Controller
{
    public $request;
    public $user;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page = 'Configurações de Usuários';
        $user_auth = auth()->user();
        $users = User::where('id', '!=', 1)->get();

        return view('admin.usuarios.configuration-users', compact(
            'title_page',
            'user_auth',
            'users'
        ));
    }
}
