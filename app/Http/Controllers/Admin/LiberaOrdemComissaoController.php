<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\LiberaOrdemComercial;
use App\Models\PedidoPneu;
use App\Models\RegiaoComercial;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class LiberaOrdemComissaoController extends Controller
{
    public $user, $request, $libera, $pedido, $area, $regiao;

    public function __construct(
        User $user,
        LiberaOrdemComercial $libera,
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        PedidoPneu $pedido,
    ) {
        $this->libera = $libera;
        $this->request = $request;
        $this->pedido = $pedido;
        $this->area = $area;
        $this->regiao = $regiao;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Pedidos Bloqueados Comercial';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        // if ($this->user->hasRole('gerente comercial')) {
        //     $find = $this->regiao->findRegiaoUser($this->user->id);

        //     $area = Helper::is_empty_object($find);
        //     if ($area) {
        //         session()->flash('warning', 'Usuário com função de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
        //         return redirect()->back();
        //     }
        // }

        return view('admin.comercial.libera-ordem', compact(
            'title_page',
            'user_auth',
            'uri'
        ));
    }

    public function getListOrdemBloqueadas()
    {        
        $data = $this->libera->listOrdensBloqueadas();

        return DataTables::of($data)
            ->addColumn('actions', function ($d) {
                return '<span class="right details-control mr-2"><i class="fa fa-plus-circle"></i></span> ' . $d->EMP;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getListPneusOrdemBloqueadas($id)
    {

        $data = $this->libera->listPneusOrdensBloqueadas($id);

        return DataTables::of($data)->make(true);
    }
    public function saveLiberaPedido()
    {
        $pedido = $this->pedido->verifyIfExists($this->request->pedido);
        $pedido ? "True" : "False";

        $data = $this->libera->listOrdensBloqueadas("", $this->request->pedido);

        $data[0]->DSLIBERACAO = $data[0]->DSLIBERACAO . ' / (Dash - ' . $this->user->name . ') Obs: ' . $this->request->liberacao;

        foreach ($this->request->pneus as $pneu) {
            $this->libera->updateValueItempedidoPneu($pneu);
        }

        if ($data[0]->TP_BLOQUEIO == "C") //Se bloqueio for igual a Comercial
        {
            $update = $this->pedido->updateData($data[0], $stpedido = 'N', $tpbloqueio = '');

            if ($update) {
                return response()->json(['success' => 'Ordem Liberada com sucesso!']);
            } else {
                return response()->json(['danger' => 'Houve algum erro favor contactar TI!']);
            }
        } elseif ($data[0]->TP_BLOQUEIO == "A") {
            $update = $this->pedido->updateData($data[0], $stpedido = "B", $tpbloqueio = "F");
            if ($update) {
                return response()->json(['warning' => 'Ordem Liberada com sucesso, mas ainda falta liberação de credito!']);
            } else {
                return response()->json(['danger' => 'Houve algum erro favor contactar TI!']);
            }
        }
    }
}
