<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{

    public $request, $user;


    public function __construct(
        Request $request,
    ) {
        $this->middleware('auth');
        $this->request = $request;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_auth = $this->user;
        $uri       = $this->request->route()->uri();
        $title_page = "Portal";

        // return Auth::user()->settings->sidebar_collapse == 'S'? 'sidebar-collapse' : '';

        return view('admin.home', compact(
            'user_auth',
            'uri', 'title_page'
        ));
    }
}
