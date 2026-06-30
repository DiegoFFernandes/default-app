<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcompanhamentoPneu;
use App\Models\AreaComercial;
use App\Models\BloqueioPedido;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\Item;
use App\Models\Pessoa;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\Vendedor;
use App\Services\SupervisorAuthService;
use App\Services\UserRoleFilterService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class BloqueioPedidosController extends Controller
{
    public $request, $bloqueio, $regiao, $area, $acompanha, $user, $item, $empresa, $supervisor, $supervisorComercial, $gerenteUnidade, $pessoa, $vendedorComercial;

    public function __construct(
        Request $request,
        BloqueioPedido $bloqueio,
        RegiaoComercial $regiao,
        AreaComercial $area,
        AcompanhamentoPneu $acompanha,
        SupervisorComercial $supervisor,
        Vendedor $vendedorComercial,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        Pessoa $pessoa,
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
        $this->vendedorComercial = $vendedorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->item = $item;
        $this->empresa = $empresa;
        $this->pessoa = $pessoa;

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
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
            $empresa = $this->empresa->empresa($cd_empresa);
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
        $empresa = 0;
        $cd_pessoa = 0;

        if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else if ($this->user->hasRole('cliente')) {
            $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
                ->pluck('cd_pessoa')
                ->implode(',');
        } else if ($this->user->hasRole('vendedor')) {
            $cd_vendedor = $this->vendedorComercial->findVendedorUser($this->user->id)
                ->pluck('cd_vendedorcomercial')
                ->implode(',');
        }
        $bloqueio = $this->bloqueio->BloqueioPedido($empresa, $supervisor, $cd_pessoa, $cd_vendedor ?? 0);

        $fone_cadastro = config('app.contact_numbers.cadastro');
        $fone_financeiro = config('app.contact_numbers.financeiro');

        return DataTables::of($bloqueio)
            ->addColumn('action', function ($b) use ($fone_cadastro, $fone_financeiro) {
                $button = "";
                if ($b->MOTIVO === 'CADASTRO  ') {
                    $button .= '<a href="https://api.whatsapp.com/send?phone=' . $fone_cadastro . '
                    &text=Olá,%20meu%20pedido%20' . $b->PEDIDO . ',%20cliente%20' . $b->CLIENTE . '%20está%20bloqueado%20com%20motivo%20'
                        . $b->MOTIVO . '%20poderiam%20verificar?" id="ver-itens" class="btn btn-success btn-xs">
                <i class="fab fa-whatsapp"></i></a>';
                } else if ($b->MOTIVO === 'FINANCEIRO' || $b->MOTIVO === 'AMBOS     ') {
                    $button .= '<a href="https://api.whatsapp.com/send?phone=' . $fone_financeiro . '
                    &text=Olá,%20meu%20pedido%20' . $b->PEDIDO . ',%20cliente%20' . $b->CLIENTE . '%20está%20bloqueado%20com%20motivo%20'
                        . $b->MOTIVO . '%20poderiam%20verificar?" id="ver-itens" class="btn btn-success btn-xs">
                <i class="fab fa-whatsapp"></i></a>';
                }

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
    public function getPedidoCliente()
    {
        $dados = $this->request->input('data', []);

        $empresa    = 0;
        $cd_regiao  = '';
        $cd_pessoa  = 0;
        $cd_vendedor = 0;

        if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')->implode(',');
        } elseif ($this->user->hasRole('cliente')) {
            $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
                ->pluck('cd_pessoa')->implode(',');
        } elseif ($this->user->hasRole('vendedor')) {
            $cd_vendedor = $this->vendedorComercial->findVendedorUser($this->user->id)
                ->pluck('cd_vendedorcomercial')->implode(',');
        }

        $supervisor = $this->supervisorComercial->getCdSupervisor();

        if ($supervisor == null) {
            $pedidos = $this->acompanha->ListPedidoPneu($empresa, $cd_regiao, 0, $dados, $cd_pessoa, $cd_vendedor);
        } else {
            $pedidos = $this->acompanha->ListPedidoPneu(0, $cd_regiao, $supervisor, $dados, $cd_pessoa, $cd_vendedor);
        }

        $data = array_map(function ($d) use ($dados) {
            $dataAttrs = [
                'empresa'               => $d->NM_EMPRESA,
                'pedido'                => $d->ID,
                'pedido_palm'           => $d->IDPEDIDOMOVEL,
                'cd_empresa'            => $d->CD_EMPRESA,
                'nm_pessoa'             => $d->PESSOA,
                'nm_vendedor'           => $d->NM_VENDEDOR,
                'forma_pagamento'       => $d->DS_FORMAPAGTO,
                'cond_pagamento'        => $d->DS_CONDPAGTO,
                'observacao'            => $d->DSOBSERVACAO,
                'status'                => $d->STPEDIDO,
                'dt_emissao'            => $d->DTEMISSAO,
                'dt_entrega'            => $d->DTENTREGAPED,
                'dt_sincronizacao'      => $d->DHSINCRONIZACAO,
                'dt_registro_palm'      => $d->DTREGISTROPALM,
                'ds_motivo'             => $d->MOTIVO,
                'ds_bloqueio'           => $d->DSBLOQUEIO,
                'ds_liberacao_anterior' => $d->DSLIBERACAOANTERIOR,
            ];

            $dataString = collect($dataAttrs)
                ->map(fn($v, $k) => 'data-' . $k . '="' . htmlspecialchars($v ?? '', ENT_QUOTES) . '"')
                ->implode(' ');

            $d->actions = '<span class="btn-detalhes btn-show-modal right mr-1" ' . $dataString . '><i class="fas fa-eye"></i></span> '
                        . '<span class="btn-detalhes details-control mr-1"><i class="fas fa-plus-circle"></i></span> ' . $d->CD_EMPRESA;

            $d->QTD_FINALIZADAS = '<span class="badge badge-secondary">'
                                . $d->QTDPNEUS . ' / ' . $d->QTD_FINALIZADAS . '</span>';

            $stpedido = trim($d->STPEDIDO ?? '');
            if ($stpedido === 'ATENDIDO') {
                $d->DT_RowClass = 'bg-green';
            } elseif ($stpedido === 'EM PRODUCAO') {
                $d->DT_RowClass = 'bg-yellow';
            } elseif (in_array($stpedido, ['BLOQUEADO', 'SCPC'])) {
                $d->DT_RowClass = 'bg-red';
            } else {
                $d->DT_RowClass = '';
            }

            return $d;
        }, $pedidos);

        return response()->json(['data' => $data]);
    }

    public function getPedidoAcompanhar()
    {
        // ── DataTables server-side params ────────────────────────────────────
        $draw   = intval($this->request->input('draw', 1));
        $start  = intval($this->request->input('start', 0));
        $length = intval($this->request->input('length', 50));
        $search = trim($this->request->input('search.value', ''));
        $colIdx = intval($this->request->input('order.0.column', 7));
        $dir    = strtoupper($this->request->input('order.0.dir', 'desc')) === 'ASC' ? 'ASC' : 'DESC';

        $columnMap = [
            1  => 'PP.IDEMPRESA',
            2  => 'PP.ID',
            3  => 'PPM.IDPEDIDOMOVEL',
            4  => 'PC.NM_PESSOA',
            7  => 'PP.DTEMISSAO',
            8  => 'PP.DTENTREGA',
            9  => 'PP.STPEDIDO',
            10 => 'TP.DSTIPOPEDIDO',
        ];
        $orderBy = $columnMap[$colIdx] ?? 'PP.DTEMISSAO';

        // ── Filtros por perfil do usuário ─────────────────────────────────────
        $empresa    = 0;
        $cd_regiao  = '';
        $cd_pessoa  = 0;
        $cd_vendedor = 0;

        if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')->implode(',');
        } elseif ($this->user->hasRole('cliente')) {
            $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
                ->pluck('cd_pessoa')->implode(',');
        } elseif ($this->user->hasRole('vendedor')) {
            $cd_vendedor = $this->vendedorComercial->findVendedorUser($this->user->id)
                ->pluck('cd_vendedorcomercial')->implode(',');
        }

        $supervisor = $this->supervisorComercial->getCdSupervisor();

        $regiao = $this->request->input('regiao', []);
        if (!empty($regiao)) {
            $cd_regiao = is_array($regiao) ? implode(',', $regiao) : $regiao;
        }

        // ── Filtros do formulário (enviados pelo data: function(d) do DT) ────
        $dados = [
            'cd_empresa'  => $this->request->input('cd_empresa', 0),
            'nm_cliente'  => $this->request->input('nm_cliente', ''),
            'nm_vendedor' => $this->request->input('nm_vendedor', ''),
            'pedido_palm' => $this->request->input('pedido_palm', ''),
            'pedido'      => $this->request->input('pedido', ''),
            'grupo_item'  => (array) $this->request->input('grupo_item', [0]),
            'dt_inicial'  => $this->request->input('dt_inicial', 0),
            'dt_final'    => $this->request->input('dt_final', 0),
            'idvendedor'  => '',
            'nr_fogo'     => $this->request->input('nr_fogo', ''),
            'nr_serie'    => $this->request->input('nr_serie', ''),
            'nr_dot'      => $this->request->input('nr_dot', ''),
        ];

        // Supervisor: força empresa = 0 (vê todas)
        $empresaFinal    = $supervisor ? 0      : $empresa;
        $supervisorFinal = $supervisor ?? 0;

        $result = $this->acompanha->ListPedidoPneuPaginated(
            $empresaFinal, $cd_regiao, $supervisorFinal, $dados,
            $cd_pessoa, $cd_vendedor,
            $start, $length, $orderBy, $dir, $search
        );

        // ── Transformação das linhas (substitui Yajra addColumn / setRowClass)
        $isCatanduvaAgro = ($dados['cd_empresa'] == '7');

        $data = array_map(function ($d) use ($isCatanduvaAgro) {
            if ($isCatanduvaAgro) {
                $d->CD_EMPRESA = '7';
            }

            $dataAttrs = [
                'empresa'               => $d->NM_EMPRESA,
                'pedido'                => $d->ID,
                'pedido_palm'           => $d->IDPEDIDOMOVEL,
                'cd_empresa'            => $d->CD_EMPRESA,
                'nm_pessoa'             => $d->PESSOA,
                'nm_vendedor'           => $d->NM_VENDEDOR,
                'forma_pagamento'       => $d->DS_FORMAPAGTO,
                'cond_pagamento'        => $d->DS_CONDPAGTO,
                'observacao'            => $d->DSOBSERVACAO,
                'status'                => $d->STPEDIDO,
                'dt_emissao'            => $d->DTEMISSAO,
                'dt_entrega'            => $d->DTENTREGAPED,
                'dt_sincronizacao'      => $d->DHSINCRONIZACAO,
                'dt_registro_palm'      => $d->DTREGISTROPALM,
                'ds_motivo'             => $d->MOTIVO,
                'ds_bloqueio'           => $d->DSBLOQUEIO,
                'ds_liberacao_anterior' => $d->DSLIBERACAOANTERIOR,
            ];

            $dataString = collect($dataAttrs)
                ->map(fn($v, $k) => 'data-' . $k . '="' . htmlspecialchars($v ?? '', ENT_QUOTES) . '"')
                ->implode(' ');

            $d->actions = '<span class="btn-detalhes btn-show-modal right mr-1" ' . $dataString . '><i class="fas fa-eye"></i></span> '
                        . '<span class="btn-detalhes details-control mr-1"><i class="fas fa-plus-circle"></i></span> ' . $d->CD_EMPRESA;

            $d->QTD_FINALIZADAS = '<span class="badge badge-secondary">'
                                . $d->QTDPNEUS . ' / ' . $d->QTD_FINALIZADAS . '</span>';

            $stpedido = trim($d->STPEDIDO ?? '');
            if ($stpedido === 'ATENDIDO') {
                $d->DT_RowClass = 'bg-green';
            } elseif ($stpedido === 'EM PRODUCAO') {
                $d->DT_RowClass = 'bg-yellow';
            } elseif (in_array($stpedido, ['BLOQUEADO', 'SCPC'])) {
                $d->DT_RowClass = 'bg-red';
            } else {
                $d->DT_RowClass = '';
            }

            return $d;
        }, $result['data']);

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => intval($result['total']),
            'recordsFiltered' => intval($result['filtered']),
            'data'            => $data,
            'totais'          => $result['totais'],
        ]);
    }
    public function getItemPedidoAcompanhar()
    {
        //dados dos pneus
        $dadosPneus = [
            'nr_fogo' => $this->request->nr_fogo ?? '',
            'nr_serie' => $this->request->nr_serie ?? '',
            'nr_dot' => $this->request->nr_dot ?? '',
        ];

        $itempedidos = $this->acompanha->ItemPedidoPneu($this->request->id, $dadosPneus);

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

        //faz a inclusão da empresa catanduva - agro numero 7 e ficticio não existente no banco de dados
        $empresa[] = (object)[
            'CD_EMPRESA' => '7',
            'NM_EMPRESA' => 'Catanduva - Agro'
        ];


        //Caso for gerente de unidade, busca a empresa vinculada ao gerente, e mostra somente os dados dessa empresa
        if ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
            $empresa = $this->empresa->empresa($cd_empresa);
        }

        if ($this->user->roles->isEmpty()) {
            return redirect()->route('home')->with('warning', 'Usuário sem função associada. Contate o administrador.');
        }

        return view('admin.comercial.coleta-empresa.coleta-empresa', compact(
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
                return '<span class="btn-detalhes details-control-vendedor mr-2"><i class="fas fa-plus-circle"></i></span> ';
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
                return '<span class="btn-detalhes details-control-pedido mr-2"><i class="fas fa-plus-circle"></i></span>';
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
