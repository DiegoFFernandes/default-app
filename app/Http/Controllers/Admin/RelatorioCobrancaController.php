<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Cobranca;
use App\Models\ControleCanhoto;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\LimiteCredito;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Services\UserRoleFilterService;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Termwind\Components\Li;
use Yajra\DataTables\Facades\DataTables;

class RelatorioCobrancaController extends Controller
{
    public $cobranca, $empresa, $request, $area, $regiao, $user, $supervisorComercial, $gerenteUnidade, $limite, $controleCanhoto;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        SupervisorComercial $supervisorComercial,
        Cobranca $cobranca,
        Empresa $empresa,
        GerenteUnidade $gerenteUnidade,
        LimiteCredito $limite,
        ControleCanhoto $controleCanhoto
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->cobranca = $cobranca;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->empresa = $empresa;
        $this->limite = $limite;
        $this->controleCanhoto = $controleCanhoto;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page   = 'Relatório de Cobrança';
        // $user_auth    = $this->user;        
        $exploder = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        // $cd_empresa = $this->setEmpresa($this->user->empresa);
        $cd_area = "";


        return view('admin.cobranca.rel-cobranca', compact(
            'empresa',
            'title_page',
            'uri',
        ));
        // return view('admin.cobranca.teste', compact(
        //     'empresa',
        //     'title_page',
        //     'uri',
        // ));
    }
    public function getListCobrancaGerente()
    {
        $this->request->validate([
            'filtro.nm_pessoa' => 'string|nullable',
            'filtro.nm_vendedor' => 'string|nullable',
            'filtro.nm_supervisor' => 'string|nullable',
            'filtro.cnpj' => 'string|nullable',
            'filtro.filtro_gerente' => 'integer|nullable',
        ]);
        $tab = $this->request->input('tab');

        $filtro = $this->request->input('filtro');

        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }

        if (!$this->user->hasRole('supervisor')) {
            if ($filtro['filtro_gerente'] ?? 0 <> 0) {
                $cd_regiao = $this->area->findGerenteSupervisor($filtro['filtro_gerente'])->pluck('CD_AREACOMERCIAL')
                    ->implode(',');
            }
        }
        // return $cd_regiao;
        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao, $cd_empresa, $tab, 0, 0, $filtro);

        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

        //faz a indexação dos valores por gerente comercial
        $hierarquia = [];
        $dataAtrasoHojeAte60 = Carbon::parse(now()->subDays(60))->format('Y-m-d');


        foreach ($data as $item) {
            foreach ($regioes_mysql as $regiao) {
                if ($item->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {
                    $nomeGerente = $regiao->name;
                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'cargo' => 'Gerente',
                            'saldo' => 0,
                            'supervisores' => [],
                            'atrasados' => 0,
                            'inadimplencia' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['saldo'] += $item->VL_SALDO;
                    $vencimento = \Carbon\Carbon::parse($item->DT_VENCIMENTO)->format('Y-m-d');

                    if ($vencimento >= $dataAtrasoHojeAte60) {
                        $hierarquia[$nomeGerente]['atrasados'] += floatval($item->VL_SALDO);
                    } else {
                        $hierarquia[$nomeGerente]['inadimplencia'] += floatval($item->VL_SALDO);
                    }

                    // --- SUPERVISOR ---
                    $nomeSupervisor = $item->NM_SUPERVISOR ?? 'Sem supervisor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor] = [
                            'nome' => $nomeSupervisor,
                            'cargo' => 'Supervisor',
                            'saldo' => 0,
                            'vendedores' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['saldo'] += $item->VL_SALDO;

                    // --- VENDEDOR ---
                    $nomeVendedor = $item->NM_VENDEDOR ?? 'Sem vendedor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor] = [
                            'nome' => $nomeVendedor,
                            'cargo' => 'Vendedor',
                            'saldo' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['saldo'] += $item->VL_SALDO;


                    // --- CLIENTE ---
                    $nomeCliente = $item->NM_PESSOA ?? 'Sem cliente';
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][] = [
                        'PESSOA' => $nomeCliente,
                        'NR_DOCUMENTO' => $item->NR_DOCUMENTO,
                        'CD_FORMAPAGTO' => $item->CD_FORMAPAGTO,
                        'NR_PARCELA'   => $item->NR_PARCELA,
                        'DT_VENCIMENTO' => $item->DT_VENCIMENTO,
                        'DT_LANCAMENTO' => $item->DT_LANCAMENTO,
                        'VL_SALDO'  => $item->VL_SALDO,
                        'VL_JUROS'  => $item->VL_JUROS,
                        'TIPOCONTA' => $item->TIPOCONTA,
                        'VL_TOTAL'  => $item->VL_TOTAL
                    ];
                }
            }
        }

        // --- Normaliza a hierarquia em arrays 
        foreach ($hierarquia as &$gerente) {
            // supervisores
            $gerente['supervisores'] = array_values($gerente['supervisores']);
            foreach ($gerente['supervisores'] as &$supervisor) {
                // vendedores
                $supervisor['vendedores'] = array_values($supervisor['vendedores']);
            }
        }
        unset($gerente);
        unset($supervisor);


        // return $hierarquia;
        return response()->json(array_values($hierarquia));
    }
    public function getListCobrancaPessoa()
    {
        $data = $this->cobranca->clientesInadiplentes($this->request->id);
        return DataTables::of($data)
            ->addColumn('details', function ($d) {
                return '<button class="btn btn-success btn-xs mr-4 detalhar" data-cd_pessoa="' . $d->CD_PESSOA . '">Detalhar</button> ';
            })
            ->rawColumns(['details'])
            ->make(true);
    }
    public function getListCobrancaPessoaDetails()
    {
        $data = $this->cobranca->clientesInadiplentesDetails($this->request->cd_pessoa);
        return Datatables::of($data)->make(true);
    }
    public function getListCobrancaFiltro()
    {
        $cd_empresa = intval($this->request->cd_empresa);
        $cd_empresa = $this->setEmpresa($cd_empresa);
        $cd_regiao = $this->request->cd_regiao;

        if ($this->user->hasRole('gerente comercial|coordenador|admin')) {
            if ($this->request->cd_area != "") {
                $cd_area = implode(",", $this->request->cd_area);
            } else {
                $cd_area = "";
            }
            if ($this->request->cd_regiao != "") {
                $cd_regiao = implode(",", $this->request->cd_regiao);
            } else {
                $cd_regiao = "";
            }
        } else {
            $regiaoUsuario = $this->regiao->regiaoPorUsuario($this->user->id);
            foreach ($regiaoUsuario as $item) {
                $cd_regiao[] = $item->cd_regiaocomercial;
            }
            $cd_regiao = implode(",", $cd_regiao);
            $cd_area = "";
        }

        $clientesInadimplentes = $this->cobranca->clientesInadiplentes($cd_empresa, $cd_regiao, $cd_area);
        $html = '<table id="table-rel-cobranca" class="table table-striped " style="width:100%">
                    <thead style="font-size: 12px">
                        <tr>
                            <th>Emitente</th>
                            <th>Cnpj/Cpf</th>
                            <th>Cliente</th>
                            <th>Area</th>
                            <th>Região</th>
                            <th>Valor Total</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 11px">';
        foreach ($clientesInadimplentes as $c) {
            $html .= '
                    <tr>
                        <td>' . $c->CD_EMPRESA . ' - ' . $c->NM_PESSOAEMP . '</td>
                        <td>' . $c->NR_CNPJCPF . '</td>                        
                        <td>' . $c->NM_PESSOA . '</td>
                        <td>' . $c->AREA . '</td>
                        <td>' . $c->DS_REGIAOQ . '</td>                        
                        <td>' . $c->VL_TOTAL . '</td>   
                        <td>
                            <button class="btn btn-xs btn-info btn-detalhar"
                            data-empresa=' . $c->CD_EMPRESA . '
                            data-pessoa=' . $c->NR_CNPJCPF . '>Detalhar
                            </button>
                        </td>                     
                    </tr>';
        }
        $html .= '</tbody>';
        if (empty($clientesInadimplentes)) {
            $array = [0, ''];
        } else {
            $array = [
                number_format($clientesInadimplentes[0]->VL_TOTAL,  "2", ",", "."),
                $clientesInadimplentes[0]->NM_PESSOA
            ];
        }
        $total = $this->calc($clientesInadimplentes);

        return response()->json([
            'html' => $html,
            'divida' => $array,
            'total' => $total
        ]);
    }
    public function getListCobrancaFiltroCnpj()
    {
        $cd_empresa = $this->setEmpresa($this->request->cd_empresa);
        $cobranca = $this->cobranca->clientesInadiplentesCnpj($this->request->cpfcnpj, $cd_empresa);

        $html = '<table id="table-cobranca-cnpj" class="nowrap display" style="width:100%">
                    <thead style="font-size: 12px">
                        <tr>
                            <th>Emp</th>
                            <th>Cnpj/Cpf</th>
                            <th>Cliente</th>
                            <th>Atraso</th>
                            <th>Documento</th>
                            <th>Fr Pgto</th>
                            <th>Lançamento</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Juros</th>
                            <th>Valor Total</th>                            
                        </tr>
                    </thead>
                    <tbody style="font-size: 11px">';
        foreach ($cobranca as $c) {
            $html .= '
                    <tr>
                        <td>' . $c->CD_EMPRESA . '</td>
                        <td>' . $c->NR_CNPJCPF . '</td>                        
                        <td>' . $c->NM_PESSOA . '</td>
                        <td>' . $c->NR_DIAS . '</td>
                        <td>' . $c->NR_DOCUMENTO . ' - ' . $c->NR_MAXPARCELA . '</td>                        
                        <td>' . $c->CD_FORMAPAGTO . '</td> 
                        <td>' . $c->DT_LANCAMENTO . '</td>
                        <td>' . $c->DT_VENCIMENTO . '</td> 
                        <td>' . $c->VL_DOCUMENTO . '</td> 
                        <td>' . $c->VL_JUROS . '</td> 
                        <td>' . $c->VL_TOTAL . '</td>
                    </tr>';
        }
        return $html;
    }
    public function setEmpresa($cd_empresa)
    {
        if ($cd_empresa == 1 || $cd_empresa == 12) {
            $cd_empresa = [1, 12];
        } elseif ($cd_empresa == 3 || $cd_empresa == 2) {
            $cd_empresa = [3, 2];
        } elseif ($cd_empresa == 21 || $cd_empresa == 22) {
            $cd_empresa = [21, 22];
        } elseif ($cd_empresa == 4 || $cd_empresa == 42) {
            $cd_empresa = [4, 42];
        } elseif ($cd_empresa == 101 || $cd_empresa == 102) {
            $cd_empresa = [101, 102];
        }
        // verificar empresas irmão com Paranavai
        $cd_empresa = implode(",", $cd_empresa);
        return $cd_empresa;
    }
    public function calc($clientes)
    {
        $total = 0;
        foreach ($clientes as $c) {
            $total += $c->VL_TOTAL;
        }
        $total_ = number_format($total, 2, ',', '.');
        return "R$ " . $total_;
    }
    public function getRelatorioCobranca()
    {
        $tela = $this->request->tela;

        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }

        // Busca os dados de cobrança de carteiro que deveria ter recebido e liquidado
        $receber_liquidada = self::RecebimentoLiquidado($tela);

        // Indexa os valores de recebimento e liquidação por vendedor e supervisor
        $indexado = self::indexarVendedorSupervisorRecebimentoLiquidado($receber_liquidada);

        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

        // Faz a indexação dos valores por gerente comercial
        $gerente = self::indexarGerenteComercial($receber_liquidada, $regioes_mysql);

        //Faz a indexação das regiões codigo do supervisor e nome do gerente comercial
        $regioesIndexadas = self::regioesIndexada($regioes_mysql);


        //Busca os dados de cobrança com as informações de vencimento, valor, etc.
        //e adiciona os valores de recebimento maior que 61 dias e o nome da
        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao, $cd_empresa, $tela);

        // Recompila os dados com as informações de região, vendedor e gerente para enviar ao Front
        $data = self::reCompilaDados($data, $regioesIndexadas, $indexado, $gerente);

        return response()->json($data);
    }
    public static function indexarVendedorSupervisorRecebimentoLiquidado($receber_liquidada)
    {

        $indexado = [
            'vendedor' => [],
            'supervisor' => []
        ];
        //Indexa os valores de recebimento e liquidação por vendedor e supervisor
        foreach ($receber_liquidada as $r) {
            $codigoSupervisor = $r->CD_VENDEDORGERAL;
            $codigoVendedor = $r->CD_VENDEDOR;

            $valores = [
                'valor_receber_maior_61_dias' => floatval($r->RECEBERMAIOR61DIAS ?? 0),
                'liquidado_maior_61_dias' => floatval($r->LIQUIDADOMAIOR61DIAS ?? 0),
                'valor_receber_menor_60_dias' => floatval($r->RECEBERMENOR60DIAS ?? 0),
                'liquidado_menor_60_dias' => floatval($r->LIQUIDADOMENOR60DIAS ?? 0),
            ];

            // Vendedor
            if (!isset($indexado['vendedor'][$codigoVendedor])) {
                $indexado['vendedor'][$codigoVendedor] = array_fill_keys(array_keys($valores), 0);
            }

            foreach ($valores as $campo => $valor) {
                $indexado['vendedor'][$codigoVendedor][$campo] += $valor;
            }

            // Supervisor
            if (!isset($indexado['supervisor'][$codigoSupervisor])) {
                $indexado['supervisor'][$codigoSupervisor] = array_fill_keys(array_keys($valores), 0);
            }

            foreach ($valores as $campo => $valor) {
                $indexado['supervisor'][$codigoSupervisor][$campo] += $valor;
            }
        }

        return $indexado;
    }
    public static function indexarGerenteComercial($receber_liquidada, $regioes_mysql)
    {
        //faz a indexação dos valores por gerente comercial
        $gerente = [];
        foreach ($receber_liquidada as $r) {
            foreach ($regioes_mysql as $regiao) {
                if ($r->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {
                    $nome = $regiao->name;
                    $valorRecebidoMaior61dias = floatval($r->RECEBERMAIOR61DIAS ?? 0);
                    $liquidadaoMaior61dias = floatval($r->LIQUIDADOMAIOR61DIAS ?? 0);
                    $valorRecebidoMenor60dias = floatval($r->RECEBERMENOR60DIAS ?? 0);
                    $liquidadaoMenor60dias = floatval($r->LIQUIDADOMENOR60DIAS ?? 0);

                    if (!isset($gerente[$nome])) {
                        $gerente[$nome] = [
                            'valor_receber_maior_61_dias' => 0,
                            'liquidado_maior_61_dias' => 0,
                            'valor_receber_menor_60_dias' => 0,
                            'liquidado_menor_60_dias' => 0
                        ];
                    }
                    $gerente[$nome]['valor_receber_maior_61_dias'] += $valorRecebidoMaior61dias;
                    $gerente[$nome]['liquidado_maior_61_dias'] += $liquidadaoMaior61dias;
                    $gerente[$nome]['valor_receber_menor_60_dias'] += $valorRecebidoMenor60dias;
                    $gerente[$nome]['liquidado_menor_60_dias'] += $liquidadaoMenor60dias;
                }
            }
        }
        return $gerente;
    }
    public static function reCompilaDados($data, $regioesIndexadas, $indexado, $gerente)
    {
        foreach ($data as $item) {
            $codigoSupervisor = $item->CD_VENDEDORGERAL;
            $nomeRegiao = $regioesIndexadas[$codigoSupervisor] ?? null;
            $item->DS_AREACOMERCIAL = $nomeRegiao;

            $item->RECEBERMAIOR61DIAS_VENDEDOR = $indexado['vendedor'][$item->CD_VENDEDOR]['valor_receber_maior_61_dias'] ?? 0;
            $item->LIQUIDADOMAIOR61DIAS_VENDEDOR = $indexado['vendedor'][$item->CD_VENDEDOR]['liquidado_maior_61_dias'] ?? 0;
            $item->RECEBERMENOR60DIAS_VENDEDOR = $indexado['vendedor'][$item->CD_VENDEDOR]['valor_receber_menor_60_dias'] ?? 0;
            $item->LIQUIDADOMENOR60DIAS_VENDEDOR = $indexado['vendedor'][$item->CD_VENDEDOR]['liquidado_menor_60_dias'] ?? 0;

            $item->RECEBERMAIOR61DIAS_SUPERVISOR = $indexado['supervisor'][$codigoSupervisor]['valor_receber_maior_61_dias'] ?? 0;
            $item->LIQUIDADOMAIOR61DIAS_SUPERVISOR = $indexado['supervisor'][$codigoSupervisor]['liquidado_maior_61_dias'] ?? 0;
            $item->RECEBERMENOR60DIAS_SUPERVISOR = $indexado['supervisor'][$codigoSupervisor]['valor_receber_menor_60_dias'] ?? 0;
            $item->LIQUIDADOMENOR60DIAS_SUPERVISOR = $indexado['supervisor'][$codigoSupervisor]['liquidado_menor_60_dias'] ?? 0;

            $item->RECEBERMAIOR61DIASGERENTECOMERCIAL = $gerente[$nomeRegiao]['valor_receber_maior_61_dias'] ?? 0;
            $item->LIQUIDADOMAIOR61DIASGERENTECOMERCIAL = $gerente[$nomeRegiao]['liquidado_maior_61_dias'] ?? 0;
            $item->RECEBERMENOR60DIASGERENTECOMERCIAL = $gerente[$nomeRegiao]['valor_receber_menor_60_dias'] ?? 0;
            $item->LIQUIDADOMENOR60DIASGERENTECOMERCIAL = $gerente[$nomeRegiao]['liquidado_menor_60_dias'] ?? 0;
        }
        return $data;
    }
    public function RecebimentoLiquidado($tela, $cd_empresa = 0, $cd_regiao = "")
    {
        return $this->cobranca->getRecebimentoLiquidado($tela, $cd_empresa, $cd_regiao);
    }
    public function getRecebimentoLiquidado()
    {
        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }


        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');
        //Faz a indexação das regiões codigo do supervisor e nome do gerente comercial
        $regioesIndexadas = [];
        foreach ($regioes_mysql as $regiao) {
            $regioesIndexadas[$regiao->cd_areacomercial] = $regiao->name;
        }

        $data = self::RecebimentoLiquidado(1, $cd_empresa, $cd_regiao);


        foreach ($data as $item) {
            $codigoSupervisor = $item->CD_VENDEDORGERAL;
            $nomeRegiao = $regioesIndexadas[$codigoSupervisor] ?? null;
            $item->DS_AREACOMERCIAL = $nomeRegiao;
        }

        return response()->json($data);
    }
    public function regioesIndexada($regioes_mysql)
    {
        //Faz a indexação das regiões codigo do supervisor e nome do gerente comercial
        $regioesIndexadas = [];
        foreach ($regioes_mysql as $regiao) {
            $regioesIndexadas[$regiao->cd_areacomercial] = $regiao->name;
        }
        return $regioesIndexadas;
    }
    public function getInadimplencia()
    {
        $this->request->validate([
            'filtro.nm_pessoa' => 'string|nullable',
            'filtro.nm_vendedor' => 'string|nullable',
            'filtro.nm_supervisor' => 'string|nullable',
            'filtro.cnpj' => 'string|nullable',
            'filtro.filtro_gerente' => 'integer|nullable',
        ]);
        $tab = $this->request->input('tab');
        $filtro = $this->request->input('filtro');

        session(['filtro' => $filtro]);

        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }
        //Caso o usuario buscar por gerente comercial
        if (!$this->user->hasRole('supervisor')) {
            if ($filtro['filtro_gerente'] ?? 0 <> 0) {
                $cd_regiao = $this->area->findGerenteSupervisor($filtro['filtro_gerente'])->pluck('CD_AREACOMERCIAL')
                    ->implode(',');
            }
        }

        $data = $this->cobranca->getInadimplencia($filtro, $tab,  $cd_empresa, $cd_regiao);
        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

        $resultados = self::formataArrayMeses($data, $tab, $regioes_mysql);

        // Dados formatados
        $data = $resultados['mesesAgrupados'];
        $atrasados = $resultados['atrasados'];
        $inadimplencia = $resultados['inadimplencia'];
        $hierarquia = $resultados['hierarquia'];

        // Retorna os dados para o DataTables, incluindo as variáveis extras
        return response()->json([
            'data' => $data,  // Tabela dos meses
            'atrasados' => $atrasados,
            'inadimplencia' => $inadimplencia,
            'hierarquia' => $hierarquia
        ]);
    }
    static function formataArrayMeses($data, $tab, $regioes_mysql)
    {
        // Inicializa um array vazio para armazenar os objetos
        $meses = [];
        $hierarquia = [];
        $atrasados = 0;
        $inadimplencia = 0;
        $dataAtrasoHojeAte60 = Carbon::parse(now()->subDays(60))->format('Y-m-d');

        if ($tab == 1) {
            $string = 'DT_VENCIMENTO';
        } else {
            $string = 'DT_LANCAMENTO';
        }

        foreach ($data as $item) {
            $vencimento = \Carbon\Carbon::parse($item->{$string})->format('Y-m-d');
            // Verifica se já existe um mês no array
            if (!isset($meses[$item->MES_ANO])) {
                // Cria um novo objeto para MES_ANO
                $meses[$item->MES_ANO] = (object)[
                    'MES' => $item->MES,
                    'ANO' => $item->ANO,
                    'MES_ANO' => $item->MES_ANO,
                    'VL_DOCUMENTO' => 0,
                    'VL_SALDO' => 0,
                    'PC_INADIMPLENCIA' => 0
                ];
            }
            // Acumula os valores de VL_DOCUMENTO e VL_SALDO
            $meses[$item->MES_ANO]->VL_DOCUMENTO += floatval($item->VL_DOCUMENTO);
            $meses[$item->MES_ANO]->VL_SALDO += floatval($item->VL_SALDO);
            // Calcula a porcentagem de inadimplência
            if ($meses[$item->MES_ANO]->VL_DOCUMENTO > 0) {
                $meses[$item->MES_ANO]->PC_INADIMPLENCIA = ($meses[$item->MES_ANO]->VL_SALDO / $meses[$item->MES_ANO]->VL_DOCUMENTO) * 100;
            } else {
                $meses[$item->MES_ANO]->PC_INADIMPLENCIA = 0;
            }

            if ($vencimento >= $dataAtrasoHojeAte60) {
                $atrasados += floatval($item->VL_SALDO);
            } else {
                $inadimplencia += floatval($item->VL_SALDO);
            }

            foreach ($regioes_mysql as $regiao) {
                if ($item->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {
                    $nomeGerente = $regiao->name;
                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'vl_documento' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['vl_documento'] += $item->VL_DOCUMENTO;
                }
            }
        }

        return [
            'mesesAgrupados' => array_values((array)$meses),
            'atrasados' => $atrasados,
            'inadimplencia' => $inadimplencia,
            'hierarquia' => $hierarquia
        ];
    }

    public function getInadimplenciaDetalhes()
    {
        $filtro = null;
        if (session()->has('filtro')) {
            $filtro = session()->get('filtro');
            if ($filtro['session'] === false) {
                $filtro = null;
            }
        }

        $mes = $this->request->mes;
        $ano = $this->request->ano;
        $tab = $this->request->input('tab');

        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }
        //Caso o usuario buscar por gerente comercial
        if (!$this->user->hasRole('supervisor')) {
            if ($filtro['filtro_gerente'] ?? 0 <> 0) {
                $cd_regiao = $this->area->findGerenteSupervisor($filtro['filtro_gerente'])->pluck('CD_AREACOMERCIAL')
                    ->implode(',');
            }
        }

        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao, $cd_empresa, $tab, $mes, $ano, $filtro);

        $pessoa = [];

        foreach ($data as $item) {
            if (!isset($pessoa[$item->CD_PESSOA])) {
                $pessoa[$item->CD_PESSOA] = [
                    'CD_PESSOA' => $item->CD_PESSOA,
                    'NM_PESSOA' => $item->NM_PESSOA,
                    'VL_SALDO_AGRUPADO'  => 0,
                    'DETALHES' => []
                ];
            }

            $pessoa[$item->CD_PESSOA]['VL_SALDO_AGRUPADO'] += $item->VL_SALDO;

            $pessoa[$item->CD_PESSOA]['DETALHES'][] = [
                'NR_DOCUMENTO' => $item->NR_DOCUMENTO,
                'CD_FORMAPAGTO' => $item->CD_FORMAPAGTO,
                'NR_PARCELA'   => $item->NR_PARCELA,
                'DT_VENCIMENTO' => $item->DT_VENCIMENTO,
                'DT_LANCAMENTO' => $item->DT_LANCAMENTO,
                'VL_SALDO'  => $item->VL_SALDO,
                'VL_JUROS'  => $item->VL_JUROS,
                'TIPOCONTA' => $item->TIPOCONTA,
                'VL_TOTAL'  => $item->VL_TOTAL
            ];
        }

        return $pessoa;
    }
    public function relatorioFinanceiroCliente()
    {
        if ($this->user->hasRole('admin')) {
            $gerentes = $this->area->GerenteAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $gerentes = $this->area->GerenteAll($this->user->id);
        } elseif ($this->user->hasRole('supervisor')) {
            $gerentes = $this->area->GerenteAll();
        } elseif ($this->user->hasRole('gerente unidade')) {
            $gerentes = $this->area->GerenteAll();
        }

        return view('admin.cobranca.rel-cobranca-novo', compact('gerentes'));
    }

    public function getLimiteCredito()
    {
        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }

        $data = $this->limite->getLimiteCredito($cd_empresa, $cd_regiao);
        return Datatables::of($data)
            ->make(true);
    }
    public function getPrazoMedio()
    {
        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');
        if ($this->user->hasRole('admin')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_empresa = 0;
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_empresa = 0;
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de supervisor mais sem vinculo com vendedor, fale com o Administrador do sistema!');
            }
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        }

        //faz a indexação dos valores por gerente comercial
        $hierarquia = [];
        $data = $this->limite->getPrazoMedio($cd_empresa, $cd_regiao);

        foreach ($data as $r) {
            foreach ($regioes_mysql as $regiao) {
                if ($r->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {
                    $nomeGerente = $regiao->name;
                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'cargo' => 'Gerente',
                            'dias' => 0,
                            'supervisores' => [],
                            'qtd' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['dias'] += $r->PRAZO_MEDIO;
                    $hierarquia[$nomeGerente]['qtd']++;


                    // --- SUPERVISOR ---
                    $nomeSupervisor = $r->NM_SUPERVISOR ?? 'Sem supervisor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor] = [
                            'nome' => $nomeSupervisor,
                            'cargo' => 'Supervisor',
                            'dias' => 0,
                            'vendedores' => [],
                            'qtd' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['dias'] += $r->PRAZO_MEDIO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['qtd']++;

                    // --- VENDEDOR ---
                    $nomeVendedor = $r->NM_VENDEDOR ?? 'Sem vendedor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor] = [
                            'nome' => $nomeVendedor,
                            'cargo' => 'Vendedor',
                            'dias' => 0,
                            'qtd' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['dias'] += $r->PRAZO_MEDIO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['qtd']++;

                    // --- CLIENTE ---
                    $nomeCliente = $r->NM_PESSOA ?? 'Sem cliente';

                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomeCliente])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomeCliente] = [
                            'nome' => $nomeCliente,
                            'dias' => 0,
                            'qtd' => 0
                        ];
                    }

                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomeCliente]['dias'] += $r->PRAZO_MEDIO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomeCliente]['qtd']++;
                }
            }
        }
        // --- Normaliza a hierarquia em arrays 
        foreach ($hierarquia as &$gerente) {
            // supervisores
            $gerente['supervisores'] = array_values($gerente['supervisores']);
            foreach ($gerente['supervisores'] as &$supervisor) {
                // vendedores
                $supervisor['vendedores'] = array_values($supervisor['vendedores']);
            }
        }
        unset($gerente);
        unset($supervisor);

        return response()->json(array_values($hierarquia));
    }

    public function getCanhoto()
    {
        $service = new UserRoleFilterService($this->user, $this->area, $this->supervisorComercial, $this->gerenteUnidade);

        $filtros = $service->getFiltros();

        $data = $this->controleCanhoto->canhotoNaoRecebidos(
            $filtros['cd_empresa'],
            $filtros['cd_regiao']
        );

        $resultados = self::formataArrayMesesCanhoto($data);

        // Dados formatados
        $data = $resultados['mesesAgrupados'];

        // Retorna os dados para o DataTables
        return response()->json([
            'data' => $data,  // Tabela dos meses
        ]);
    }
    public function getCanhotoDetails()
    {
        $filtro = null;
        if (session()->has('filtro')) {
            $filtro = session()->get('filtro');
            if ($filtro['session'] === false) {
                $filtro = null;
            }
        }

        $mes = $this->request->mes;
        $ano = $this->request->ano;

        $service = new UserRoleFilterService($this->user, $this->area, $this->supervisorComercial, $this->gerenteUnidade);

        $filtros = $service->getFiltros();

        $data = $this->controleCanhoto->canhotoNaoRecebidos(
            $filtros['cd_empresa'],
            $filtros['cd_regiao'],
            $mes,
            $ano
        );

        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

        $hierarquia = [];

        foreach ($data as $item) {
            foreach ($regioes_mysql as $regiao) {
                if ($item->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {
                    $nomeGerente = $regiao->name;
                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'cargo' => 'Gerente',
                            'qtd_notas' => 0,
                            'supervisores' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['qtd_notas']++;

                    // --- SUPERVISOR ---
                    $nomeSupervisor = $item->NM_SUPERVISOR ?? 'Sem supervisor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor] = [
                            'nome' => $nomeSupervisor,
                            'cargo' => 'Supervisor',
                            'qtd_notas' => 0,
                            'vendedores' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['qtd_notas']++;

                    // --- VENDEDOR ---
                    $nomeVendedor = $item->NM_VENDEDOR ?? 'Sem vendedor';   
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor] = [
                            'nome' => $nomeVendedor,
                            'cargo' => 'Vendedor',
                            'qtd_notas' => 0,
                            'clientes' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['qtd_notas']++;

                    // --- PESSOA ---
                    $nomePessoa = $item->NM_PESSOA ?? 'Sem pessoa';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomePessoa])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomePessoa] = [
                            'nome' => $nomePessoa,
                            'qtd_notas' => 0,
                            'detalhes' => []
                        ];
                    }

                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomePessoa]['qtd_notas']++;

                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['clientes'][$nomePessoa]['detalhes'][] = [
                        'nr_documento' => $item->NR_DOCUMENTO,
                        'cd_serie' => $item->CD_SERIE,
                        'dt_lancamento' => $item->DT_LANCAMENTO,
                    ];
                }
            }            
        }

         // --- Normaliza a hierarquia em arrays 
        foreach ($hierarquia as &$gerente) {
            // supervisores
            $gerente['supervisores'] = array_values($gerente['supervisores']);
            foreach ($gerente['supervisores'] as &$supervisor) {
                // vendedores
                $supervisor['vendedores'] = array_values($supervisor['vendedores']);
                foreach ($supervisor['vendedores'] as &$vendedor) {
                    // pessoas
                    $vendedor['clientes'] = array_values($vendedor['clientes']);
                }
            }
        }
        unset($gerente);
        unset($supervisor);
        unset($vendedor);


        // return $hierarquia;
        return response()->json(array_values($hierarquia));
    }


    static function formataArrayMesesCanhoto($data)
    {
        // Inicializa um array vazio para armazenar os objetos
        $meses = [];

        foreach ($data as $item) {

            // Verifica se já existe um mês no array
            if (!isset($meses[$item->MES_ANO])) {
                // Cria um novo objeto para MES_ANO
                $meses[$item->MES_ANO] = (object)[
                    'MES' => $item->MES,
                    'ANO' => $item->ANO,
                    'MES_ANO' => $item->MES_ANO,
                    'QTD_NOTA' => 0
                ];
            }
            // Acumula os valores de QTD_NOTA            
            $meses[$item->MES_ANO]->QTD_NOTA++;
        }

        return [
            'mesesAgrupados' => array_values((array)$meses),
        ];
    }
}
