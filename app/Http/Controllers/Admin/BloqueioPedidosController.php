<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcompanhamentoPneu;
use App\Models\AreaComercial;
use App\Models\BloqueioPedido;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Services\SupervisorAuthService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class BloqueioPedidosController extends Controller
{
    public $request, $bloqueio, $regiao, $area, $acompanha, $user, $item, $empresa, $supervisor, $supervisorComercial;

    public function __construct(
        Request $request,
        BloqueioPedido $bloqueio,
        RegiaoComercial $regiao,
        AreaComercial $area,
        AcompanhamentoPneu $acompanha,
        SupervisorComercial $supervisor,
        SupervisorAuthService $supervisorComercial,
        Item $item,
        Empresa $empresa
    ) {
        $this->request = $request;
        $this->bloqueio = $bloqueio;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->acompanha = $acompanha;
        $this->supervisor = $supervisor;
        $this->supervisorComercial = $supervisorComercial;
        $this->item = $item;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Acompanhamento de Pedidos';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $regiao = $this->regiao->regiaoAll();
        $uri = ucfirst($exploder[1]);
        $grupo = $this->item->getGroupItem();
        $empresa = $this->empresa->empresa();

        if ($this->user->hasRole('supervisor')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $find = $this->supervisor->findSupervisorUser($this->user->id);
            $array = json_decode($find, true);

            if (empty($array)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif (!$this->user->hasRole('admin|gerente comercial')) {
            $regiaoUsuario = $this->regiao->regiaoPorUsuario($this->user->id);
            foreach ($regiaoUsuario as $r) {
                $cd_regiao[] = $r->cd_regiaocomercial;
            }
            //verifica se o usuario tem permissão mais ainda nao foi associado região para ele e retorna com mensagem!
            if (empty($cd_regiao)) {
                return redirect()->back()->with('warning', 'Usuario com permissão mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        }


        return view('admin.comercial.bloqueio-pedidos', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'grupo',
            'empresa'
        ));
    }
    public function getBloqueioPedido()
    {
        $supervisor = $this->supervisorComercial->getCdSupervisor();

        $bloqueio = $this->bloqueio->BloqueioPedido($supervisor);

        return DataTables::of($bloqueio)
            ->addColumn('action', function ($b) {
                $button = '<a href="https://api.whatsapp.com/send?phone=+5541985227055&text=
                Olá,%20meu%20pedido%20' . $b->PEDIDO . ',%20cliente%20' . $b->CLIENTE . '%20está%20bloqueado%20com%20motivo%20'
                    . $b->MOTIVO . '%20poderiam%20verificar?" id="ver-itens" class="btn btn-success btn-xs">
                <i class="fab fa-whatsapp"></i></a>';
                $button .= '<button type="button" class="btn btn-info ml-1 btn-xs" data-toggle="popover" title="Detalhes" 
                data-content="' . $b->DSBLOQUEIO . '"><i class="fas fa-comments"></i></button>';

                $button .= " " . $b->CD_EMPRESA;

                return $button;
            })
            ->addColumn('status_cliente', ' ')
            ->addColumn('status_scpc', ' ')
            ->addColumn('status_pedido', ' ')
            ->editColumn('status_cliente', function ($row) {
                return $row->ST_ATIVA && BloqueioPedido::STATUS_CLIENTE[$row->ST_ATIVA] ? BloqueioPedido::STATUS_CLIENTE[$row->ST_ATIVA] : 'none';
            })
            ->editColumn('status_scpc', function ($row) {
                return $row->ST_SCPC && BloqueioPedido::STATUS_SCPC[$row->ST_SCPC] ? BloqueioPedido::STATUS_SCPC[$row->ST_SCPC] : 'none';
            })
            ->editColumn('status_pedido', function ($row) {
                return $row->STPEDIDO && BloqueioPedido::STATUS_PEDIDO[$row->STPEDIDO] ? BloqueioPedido::STATUS_PEDIDO[$row->STPEDIDO] : 'none';
            })
            ->rawColumns(['action'])
            ->make();
    }
    public function getPedidoAcompanhar()
    {
        $dados = $this->request->data;
        $cd_regiao = "";

        if ($this->user->hasRole('admin|gerente comercial')) {
            $cd_regiao = "";
        }
        // verifica se o usuario logado é supervisor, se sim ele filtra somente os dados dele
        $supervisor = $this->supervisorComercial->getCdSupervisor();

        if (!empty($this->request->data['regiao'])) {
            $cd_regiao = implode(',', $this->request->data['regiao']);
        }

        if ($supervisor == null) {
            $pedidos = $this->acompanha->ListPedidoPneu($cd_regiao,  0, $dados);
        } else {
            $pedidos = $this->acompanha->ListPedidoPneu($cd_regiao,  $supervisor, $dados);
        }



        // verifica se cd_empresa e nullo ou e igual a 7
        if ($this->request->filled('cd_empresa') && $dados['cd_empresa'] == '7') {
            foreach ($pedidos as $pedido) {
                $pedido->CD_EMPRESA = '7';
            }
        }

        return DataTables::of($pedidos)
            ->addColumn('actions', function ($d) {
                $dataAttrs = [
                    'empresa' => $d->NM_EMPRESA,
                    'pedido' => $d->ID,
                    'pedido_palm' => $d->IDPEDIDOMOVEL,
                    'cd_empresa' => $d->CD_EMPRESA,
                    'nm_pessoa' => $d->PESSOA,
                    'nm_vendedor' => $d->NM_VENDEDOR,
                    'forma_pagamento' => $d->DS_FORMAPAGTO,
                    'cond_pagamento' => $d->DS_CONDPAGTO,
                    'observacao' => $d->DSOBSERVACAO,
                    'status' => $d->STPEDIDO,
                    'dt_emissao' => $d->DTEMISSAO,
                    'dt_entrega' => $d->DTENTREGAPED,
                    'dt_sincronizacao' => $d->DTREGISTROPALM,
                    'ds_motivo' => $d->MOTIVO,
                    'ds_bloqueio' => $d->DSBLOQUEIO,
                ];

                $dataString = collect($dataAttrs)
                    ->map(function ($value, $key) {
                        return 'data-' . $key . '="' . $value . '"';
                    })->implode(' ');

                $btn = '<span class="btn-detalhes btn-show-modal right mr-2" ' . $dataString . '><i class="fas fa-eye"></i></span> ';
                $btn .=  '<span class="btn-detalhes details-control mr-2"><i class="fas fa-plus-circle"></i></span> ' . $d->CD_EMPRESA;
                return $btn;
            })
            ->rawColumns(['actions'])
            ->setRowClass(function ($p) {
                if ($p->STPEDIDO == "ATENDIDO        ") {
                    return 'bg-green';
                } elseif ($p->STPEDIDO == "EM PRODUCAO     ") {
                    return 'bg-yellow';
                } elseif ($p->STPEDIDO == "BLOQUEADO       ") {
                    return 'bg-red';
                } elseif ($p->STPEDIDO == "SCPC            ") {
                    return 'bg-red';
                }
            })
            ->make();
    }
    public function getItemPedidoAcompanhar()
    {
        $itempedidos = $this->acompanha->ItemPedidoPneu($this->request->id);
        return DataTables::of($itempedidos)
            ->addColumn('details_item_pedido_url', function ($i) {
                return route('get-detalhe-item-pedido', $i->ID);
            })
            ->make();
    }
    public function getDetalheItemPedidoAcompanhar($nrordem)
    {
        // return $nrordem;
        $detalhe_ordem = $this->acompanha->BuscaSetores($nrordem);
        return DataTables::of($detalhe_ordem)
            ->addColumn('entrada', function ($d) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $d->O_DT_ENTRADA)
                    ->format('d/m/Y') . ' ' . $d->O_HR_ENTRADA;
            })
            ->addColumn('saida', function ($d) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $d->O_DT_SAIDA)
                    ->format('d/m/Y') . ' ' . $d->O_HR_SAIDA;
            })
            ->make();
    }
    public function coletaGeral()
    {
        $title_page   = 'Pedidos Geral';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $regiao = $this->regiao->regiaoAll();
        $uri = ucfirst($exploder[1]);
        $grupo = $this->item->getGroupItem();
        $empresa = $this->empresa->empresa();

        if ($this->user->hasRole('supervisor')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $find = $this->supervisor->findSupervisorUser($this->user->id);
            $array = json_decode($find, true);

            if (empty($array)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        }

        if ($this->user->roles->isEmpty()) {
            return redirect()->route('home')->with('warning', 'Usuário sem função associada. Contate o administrador.');
        }



        $empresa[] = (object)[
            'CD_EMPRESA' => '7',
            'NM_EMPRESA' => 'Catanduva - Agro'
        ];

        return view('admin.comercial.coleta-empresa', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'grupo',
            'empresa'
        ));
    }
    public function getColetaGeralRegiao()
    {
        $supervisor = $this->supervisorComercial->getCdSupervisor();

        $pedidos = $this->acompanha->getListColetaRegiao($this->request->data, $supervisor ?? null);

        if ($this->request->data['cd_empresa'] == '7') {
            foreach ($pedidos as $pedido) {
                $pedido->CD_EMPRESA = '7';
            }
        }
        return DataTables::of($pedidos)
            ->addColumn('actions', function ($d) {
                return '<span class="details-control-vendedor mr-2"><i class="fas fa-plus-circle"></i></span> ';
            })
            ->rawColumns(['actions'])
            ->make();
    }
    public function getColetaGeral()
    {
        $dados = $this->request->data;

        // $supervisor = $this->supervisorComercial->getCdSupervisor();

        $pedidos = $this->acompanha->getColetaEmpresa($dados);

        if ($this->request->data['cd_empresa'] == '7') {
            foreach ($pedidos as $pedido) {
                $pedido->CD_EMPRESA = '7';
            }
        }
        return DataTables::of($pedidos)
            ->addColumn('actions', function ($d) {
                return '<span class="details-control-pedido mr-2"><i class="fas fa-plus-circle"></i></span>';
            })
            ->rawColumns(['actions'])
            ->make();
    }
    public function getQtdColeta()
    {
        $supervisor = $this->supervisorComercial->getCdSupervisor();

        if ($supervisor == null) {
            $pedidos = $this->acompanha->getQtdColeta($this->request, 0);
        } else {
            $pedidos = $this->acompanha->getQtdColeta($this->request, $supervisor);
        }

        if (Helper::is_empty_object($pedidos)) {

            $pedidos[0] = [
                'IDEMPRESA' => $this->request->cd_empresa,
                "QTDPNEUS_HOJE" => "0",
                "QTDPEDIDOS_ONTEM" => "0",
                "QTDPNEUS_ONTEM" => "0",
                "QTDPEDIDOS_ANTEONTEM" => "0",
                "QTDPNEUS_ANTEONTEM" => "0"
            ];

            return response()->json($pedidos);
        }

        return response()->json(
            $pedidos
        );
    }
    public function verifyIfSupervisor()
    {
        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisor->seachSupervisor($this->user->id);
        }
        return $supervisor->cd_supervisorcomercial ?? null;
    }
}
