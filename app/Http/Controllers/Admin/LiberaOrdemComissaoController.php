<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\LiberaOrdemComercial;
use App\Models\PedidoPneu;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class LiberaOrdemComissaoController extends Controller
{
    public $user, $request, $libera, $pedido, $area, $regiao, $supervisorComercial, $supervisorComercialModel;

    public function __construct(
        User $user,
        LiberaOrdemComercial $libera,
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        SupervisorAuthService $supervisorComercial,
        SupervisorComercial $supervisorComercialModel,
        PedidoPneu $pedido,
    ) {
        $this->libera = $libera;
        $this->request = $request;
        $this->pedido = $pedido;
        $this->area = $area;
        $this->supervisorComercialModel = $supervisorComercialModel;
        $this->supervisorComercial = $supervisorComercial;
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

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();
            if (is_null($supervisor)) {
                return Redirect::back()->with('warning', 'Usuário sem vinculo com Supervisor Comercial, fale com o Administrador do sistema!');
            }
        }

        return view('admin.comercial.libera-ordem', compact(
            'title_page',
            'user_auth',
            'uri'
        ));
    }

    public function getListOrdemBloqueadas()
    {
        $supervisor = null;

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();

            if (is_null($supervisor)) {
                return response()->json(['warning' => 'Usuário com função de supervisor mais sem vinculo com supervisor comercial, fale com o Administrador do sistema!']);
            }
        }

        // Atualiza o desconto maior que 20% para Gerente fazer a liberação
        $this->libera->updateDescontoMaior20();

        $data = $this->libera->listOrdensBloqueadas(0, 0, 0, $supervisor);

        return DataTables::of($data)
            ->addColumn('actions', function ($d) {
                return '<span class="right btn-detalhes details-control mr-2"><i class="fa fa-plus-circle"></i></span> ' . $d->EMP;
            })
            ->rawColumns(['actions'])
            ->setRowClass(function ($d) {
                return $d->ST_COMERCIAL == 'G' ? 'bg-warning' : '';
            })
            ->make(true);
    }

    public function getListPneusOrdemBloqueadas($id)
    {
        $data = $this->libera->listPneusOrdensBloqueadas($id);
        return DataTables::of($data)->make(true);
    }

    public function getCalculaComissao()
    {
        $item_pedido = $this->libera->listPneusOrdensBloqueadas(0, $this->request->item_pedido);

        //verifica se teve algum retorno do calculo de comissão, 
        //alguns pedidos não tem tipo vendedor 1, então retorna 0
        if (Helper::is_empty_object($item_pedido)) {
            $data = 0;
            return response()->json($data);
        }

        $data = $this->libera->calculaComissao($item_pedido, $this->request->venda);

        return response()->json($data);
    }

    public function saveLiberaPedido()
    {
        $pedido = $this->pedido->verifyIfExists($this->request->pedido);

        $data = $this->libera->listOrdensBloqueadas(0, $this->request->pedido);

        $data[0]->DSLIBERACAO = $data[0]->DSLIBERACAO . ' / (Dash - ' . $this->user->name . ') Obs: ' . $this->request->liberacao;

        foreach ($this->request->pneus as $pneu) {
            $this->libera->updateValueItempedidoPneu($pneu);
        }

        $supervisor = $this->supervisorComercialModel->findSupervisorUser($this->user->id);
        //Verifica se o usuario e supervisor e se o status comercial é G (Gerente)
        //Caso verdadeiro, ajusta os valores mas mantem a ordem bloqueada até liberação do gerente
        $registro = $data[0] ?? null;

        if ($this->user->hasRole('supervisor') && $supervisor[0]->ST_PARAM == 1 && $supervisor[0]->PC_PERMITIDA <= 35) {
            // Se verdade continua, ação criada para permitir supervisor como se fosse gerente!
        } else if (
            $this->user->hasRole('supervisor') &&
            $registro->ST_COMERCIAL === 'G' &&
            in_array($registro->TP_BLOQUEIO, ['C', 'A'])
        ) {
            return response()->json([
                'warning' => 'Feita atualização de comissão, favor aguardar liberação do Gerente!'
            ]);
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
