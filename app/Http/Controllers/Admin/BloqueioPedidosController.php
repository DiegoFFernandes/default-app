<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcompanhamentoPneu;
use App\Models\AreaComercial;
use App\Models\BloqueioPedido;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\RegiaoComercial;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class BloqueioPedidosController extends Controller
{
    public $request, $bloqueio, $regiao, $area, $acompanha, $user, $item, $empresa;

    public function __construct(
        Request $request,
        BloqueioPedido $bloqueio,
        RegiaoComercial $regiao,
        AreaComercial $area,
        AcompanhamentoPneu $acompanha,
        Item $item,
        Empresa $empresa
    ) {
        $this->request = $request;
        $this->bloqueio = $bloqueio;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->acompanha = $acompanha;
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

        if ($this->user->hasRole('gerencia')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $find = $this->regiao->findRegiaoUser($this->user->id);
            $array = json_decode($find, true);

            if (empty($array)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif (!$this->user->hasRole('admin|diretoria')) {
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
        if ($this->user->hasRole('admin|diretoria')) {
            $cd_regiao = "";
        } elseif ($this->user->hasRole('gerencia')) {
            $cd_regiao = $this->regiao->findRegiaoUser($this->user->id)
                ->pluck('CD_REGIAOCOMERCIAL')
                ->implode(',');
        }

        $bloqueio = $this->bloqueio->BloqueioPedido($cd_regiao);

        return DataTables::of($bloqueio)
            ->addColumn('action', function ($b) {
                $button = '<a href="https://api.whatsapp.com/send?phone=+5541985227055&text=
                Olá,%20meu%20pedido%20' . $b->PEDIDO . ',%20cliente%20' . $b->CLIENTE . '%20está%20bloqueado%20com%20motivo%20'
                    . $b->MOTIVO . '%20poderiam%20verificar?" id="ver-itens" class="btn btn-success">
                <i class="fab fa-whatsapp"></i></a>';
                $button .= '<button type="button" class="btn btn-info ml-1 popover-lg" data-toggle="popover" title="Detalhes" 
                data-content="' . $b->DSBLOQUEIO . '"><i class="fas fa-comments"></i></button>';
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

        $cd_regiao = "";

        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
        } elseif ($this->user->hasRole('gerencia')) {
            $cd_regiao = $this->regiao->findRegiaoUser($this->user->id)
                ->pluck('CD_REGIAOCOMERCIAL')
                ->implode(',');
        }
        if (!empty($this->request->data['regiao'])) {
            $cd_regiao = implode(',', $this->request->data['regiao']);
        }

        $pedidos = $this->acompanha->ListPedidoPneu($cd_regiao, $this->request->data);
        return DataTables::of($pedidos)

            ->addColumn('actions', function ($d) {

                $dataAttrs = [
                    'pedido' => $d->ID,
                    'pedido_palm' => $d->IDPEDIDOMOVEL,
                    'cd_empresa' => $d->CD_EMPRESA,
                    'nm_pessoa' => $d->PESSOA,
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
        $pedidos = $this->acompanha->getListColetaRegiao($this->request->data);
        return DataTables::of($pedidos)
            ->addColumn('actions', function ($d) {
                return '<span class="details-control-vendedor mr-2"><i class="fas fa-plus-circle"></i></span> ';
            })
            ->rawColumns(['actions'])
            ->make();
    }
    public function getColetaGeral()
    {
        $pedidos = $this->acompanha->getColetaEmpresa($this->request->data);
        return DataTables::of($pedidos)
            ->addColumn('actions', function ($d) {
                return '<span class="details-control-pedido mr-2"><i class="fas fa-plus-circle"></i></span> ';
            })
            ->rawColumns(['actions'])
            ->make();
    }
    public function getQtdColeta()
    {
        $pedidos = $this->acompanha->getQtdColeta($this->request);

        return response()->json(
            $pedidos
        );
    }
}
