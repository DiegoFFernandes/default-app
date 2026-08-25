<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AtalhosHomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{

    public $request, $user;
    protected $atalhosHomeService;


    public function __construct(
        Request $request,
        AtalhosHomeService $atalhosHomeService
    ) {
        $this->middleware('auth');
        $this->request = $request;
        $this->atalhosHomeService = $atalhosHomeService;

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

        $atalhos = $this->atalhosHomeService->paraHome($user_auth);
        $atalhosDisponiveis = $this->atalhosHomeService->disponiveisParaUsuario($user_auth);
        $chavesFavoritas = $user_auth->atalhosFavoritos()->pluck('chave_atalho')->all();

        return view('admin.home', compact(
            'user_auth',
            'uri', 'title_page',
            'atalhos', 'atalhosDisponiveis', 'chavesFavoritas'
        ));
    }

    /**
     * Salva os atalhos favoritados pelo usuário autenticado, na ordem enviada.
     */
    public function salvarAtalhosFavoritos(Request $request)
    {
        $request->validate([
            'atalhos' => ['array'],
            'atalhos.*' => ['string'],
        ]);

        $this->atalhosHomeService->salvarFavoritos($this->user, $request->input('atalhos', []));

        return response()->json(['success' => true]);
    }
}
