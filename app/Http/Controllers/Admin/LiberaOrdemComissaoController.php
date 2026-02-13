<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\LiberaOrdemComercial;
use App\Models\PedidoPneu;
use App\Models\PercentualDescontoComissao;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\SupervisorSubgrupo;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class LiberaOrdemComissaoController extends Controller
{

    public User $user;
    public LiberaOrdemComercial $libera;
    public Request $request;
    public AreaComercial $area;
    public RegiaoComercial $regiao;
    public SupervisorAuthService $supervisorComercial;
    public SupervisorComercial $supervisorComercialModel;
    public SupervisorSubgrupo $supervisorSubgrupo;
    public PedidoPneu $pedido;

    public function __construct(
        User $user,
        LiberaOrdemComercial $libera,
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        SupervisorAuthService $supervisorComercial,
        SupervisorComercial $supervisorComercialModel,
        SupervisorSubgrupo $supervisorSubgrupo,
        PedidoPneu $pedido,
    ) {
        $this->libera = $libera;
        $this->request = $request;
        $this->pedido = $pedido;
        $this->area = $area;
        $this->supervisorComercialModel = $supervisorComercialModel;
        $this->supervisorComercial = $supervisorComercial;
        $this->supervisorSubgrupo = $supervisorSubgrupo;
        $this->regiao = $regiao;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title   = 'Pedidos Bloqueados Comercial';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();
            if (is_null($supervisor)) {
                return Redirect::back()->with('warning', 'Usuário sem vinculo com Supervisor Comercial, fale com o Administrador do sistema!');
            }
        }

        $percentual = PercentualDescontoComissao::listAllPercDesconto();

        if (Helper::is_empty_object($percentual)) {
            return Redirect::back()->with('warning', 'Nenhum percentual de desconto comercial cadastrado!');
        }

        return view('admin.comercial.libera-ordem.index', compact(
            'title',
            'user_auth',
            'uri',
            'percentual'
        ));
    }

    public function getListOrdemBloqueadas()
    {
        $supervisor = null;
        $subgruposLiberados = null;

        if ($this->user->hasRole('supervisor')) {

            $subgruposLiberados = $this->supervisorSubgrupo->subgrupoSupervisor($this->user->id)->pluck('CD_SUBGRUPO');

            if (!Helper::is_empty_object($subgruposLiberados)) {
                //Anula o vinculo de vendedores pega todos os pedidos que tenham o subgrupo liberado
                $supervisor = null;
                //verifico se o supervisor tem subgrupo liberado, se tiver, passo os subgrupos para filtrar os pedidos
                $subgruposLiberados = $subgruposLiberados->implode(',');

                $dataPedidoSubgrupo = $this->libera->listPedidosBloqueadas(0, 0, 0, $supervisor, null, $subgruposLiberados);
            }

            $subgruposLiberados = null;
            $supervisor = $this->supervisorComercial->getCdSupervisor();

            if (is_null($supervisor)) {
                return response()->json(['warning' => 'Usuário com função de supervisor mais sem vinculo com supervisor comercial, fale com o Administrador do sistema!']);
            }
        }
        // Atualiza o status do pedido para filtrar por Gerente, supervisor ou liberação Automatica
        $this->libera->updateStatusPedidos();

        $dataPedidoSupervisor = $this->libera->listPedidosBloqueadas(0, 0, 0, $supervisor, null, null);

        // $data = array_merge($dataPedidoSupervisor, $dataPedidoSubgrupo ?? []);

        $data = collect($dataPedidoSupervisor)
            ->merge($dataPedidoSubgrupo ?? [])
            ->unique('PEDIDO')
            ->values()
            ->toArray();

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
        return DataTables::of($data)
            ->setRowClass(function ($d) {
                return $d->ST_CALCULO == 'M' ? 'bg-purple text-white' : '';
            })
            ->make(true);
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

    public function saveLiberaPedido($pedido = null)
    {
        if ($this->request->has('pedido')) {
            $pedidoId = $this->request->pedido;
            $observacao = $this->request->observacao ?? '';
            foreach ($this->request->pneus as $pneu) {
                $this->libera->updateValueItempedidoPneu($pneu);
            }
        } else {
            $pedidoId = $pedido->PEDIDO;
            $observacao = 'Automatico';
        }
        $pedido = $this->pedido->verifyIfExists($pedidoId);

        $data = $this->libera->listPedidosBloqueadas(0, $pedidoId);
        $data[0]->DSLIBERACAO = $data[0]->DSLIBERACAO . ' / (Dash - ' . $this->user->name . ') Obs: ' . $observacao;

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
                return response()->json(['success' => 'Pedido ' . $data[0]->PEDIDO . ' liberado com sucesso!']);
            } else {
                return response()->json(['danger' => 'Houve algum erro favor contactar TI!']);
            }
        } elseif ($data[0]->TP_BLOQUEIO == "A") {
            $update = $this->pedido->updateData($data[0], $stpedido = "B", $tpbloqueio = "F");
            if ($update) {
                return response()->json(['warning' => 'Pedido ' . $data[0]->PEDIDO . ' liberado com sucesso, mas ainda falta liberação de credito!']);
            } else {
                return response()->json(['danger' => 'Houve algum erro favor contactar TI!']);
            }
        }
    }

    public function liberaAbaixoDesconto()
    {
        $supervisor = null;

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();
        }

        $pedidos = $this->libera->listPedidosBloqueadas(0, 0, 0, $supervisor, 'A');

        if (Helper::is_empty_object($pedidos)) {
            return response()->json([
                'message' => 'Nenhum pedido abaixo do desconto encontrado para liberação automática!'
            ]);
        }

        $resultados = [];

        foreach ($pedidos as $pedido) {
            $resultados[] = $this->saveLiberaPedido($pedido);
        }

        $mensagens = [];

        foreach ($resultados as $r) {
            // transforma JsonResponse em array associativo
            $data = $r->getData(true);

            // pega a primeira mensagem (success ou warning)
            $mensagens[] = reset($data);
        }

        return response()->json([
            'message' => implode("</br>", $mensagens)
        ]);
    }
}
