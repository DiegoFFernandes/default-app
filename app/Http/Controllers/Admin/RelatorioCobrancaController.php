<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Cobranca;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use Yajra\DataTables\Facades\DataTables;

class RelatorioCobrancaController extends Controller
{
    public $cobranca, $empresa, $request, $area, $regiao, $user, $supervisorComercial, $gerenteUnidade;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        SupervisorComercial $supervisorComercial,
        Cobranca $cobranca,
        Empresa $empresa,
        GerenteUnidade $gerenteUnidade
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->cobranca = $cobranca;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->empresa = $empresa;

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
    public function getListCobranca()
    {
        if ($this->user->hasRole('admin|diretoria')) {
            $cd_area = "";
            $cd_regiao = "";
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $cd_regiao = $this->regiao->findRegiaoUser($this->user->id)
                ->pluck('CD_REGIAOCOMERCIAL')
                ->implode(',');
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
        }
        // return $cd_regiao;
        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao);
        //return $this->calc($clientesInadimplentes, "N");


        $regioes_mysql = $this->regiao->RegiaoUsuarioAll()->keyBy('cd_regiaocomercial');

        $valor_geral = 0;
        foreach ($data as $item) {
            $valor_geral += (float)$item->VL_SALDO; // ou $item->VL_SALDO se for objeto
        }


        return DataTables::of($data)
            ->addColumn('percentual', function ($data) use ($valor_geral) {
                return number_format(((float)$data->VL_SALDO / $valor_geral) * 100, 2) . '%';
            })
            ->addColumn('responsavel', function ($data) use ($regioes_mysql) {
                $regiao = $regioes_mysql[$data->CD_REGIAOCOMERCIAL] ?? null;

                if ($regiao) {
                    return '<span class="right badge badge-success details-control mr-2"><i class="fa fa-plus-circle"></i></span> ' . $regiao->name;
                } else {
                    return '<span class="right badge badge-success details-control mr-2"><i class="fa fa-plus-circle"></i></span>' . 'SEM RESPONSAVEL';
                }
            })
            ->rawColumns(['responsavel', 'total'])
            ->make(true);
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
            foreach ($regiaoUsuario as $r) {
                $cd_regiao[] = $r->cd_regiaocomercial;
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

        $receber_liquidada = self::getRecebimentoLiquidado();

        $indexado = [
            'vendedor' => [],
            'supervisor' => [],
            'gerente_comercial' => []
        ];
        //Indexa os valores de recebimento e liquidação por vendedor e supervisor
        foreach ($receber_liquidada as $r) {
            $codigoSupervisor = $r->CD_VENDEDORGERAL;
            $codigoVendedor = $r->CD_VENDEDOR;

            $valorRecebidoMaior61dias = floatval($r->RECEBERMAIOR61DIAS ?? 0);
            $liquidadaoMaior61dias = floatval($r->LIQUIDADOMAIOR61DIAS ?? 0);
            $receberMenor60dias = floatval($r->RECEBERMENOR60DIAS ?? 0);
            $liquidadoMenor60dias = floatval($r->LIQUIDADOMENOR60DIAS ?? 0);

            if (!isset($indexado['vendedor'][$codigoVendedor])) {
                $indexado['vendedor'][$codigoVendedor] = [
                    'valor_receber_maior_61_dias' => 0,
                    'liquidado_maior_61_dias' => 0,

                    'valor_receber_menor_60_dias' => 0,
                    'liquidado_menor_60_dias' => 0
                ];
            }
            $indexado['vendedor'][$codigoVendedor]['valor_receber_maior_61_dias'] += $valorRecebidoMaior61dias;
            $indexado['vendedor'][$codigoVendedor]['liquidado_maior_61_dias'] += $liquidadaoMaior61dias;
            $indexado['vendedor'][$codigoVendedor]['valor_receber_menor_60_dias'] += $receberMenor60dias;
            $indexado['vendedor'][$codigoVendedor]['liquidado_menor_60_dias'] += $liquidadoMenor60dias;

            if (!isset($indexado['supervisor'][$codigoSupervisor])) {
                $indexado['supervisor'][$codigoSupervisor] = [
                    'valor_receber_maior_61_dias' => 0,
                    'liquidado_maior_61_dias' => 0,

                    'valor_receber_menor_60_dias' => 0,
                    'liquidado_menor_60_dias' => 0
                ];
            }
            $indexado['supervisor'][$codigoSupervisor]['valor_receber_maior_61_dias'] += $valorRecebidoMaior61dias;
            $indexado['supervisor'][$codigoSupervisor]['liquidado_maior_61_dias'] += $liquidadaoMaior61dias;
            $indexado['supervisor'][$codigoSupervisor]['valor_receber_menor_60_dias'] += $receberMenor60dias;
            $indexado['supervisor'][$codigoSupervisor]['liquidado_menor_60_dias'] += $liquidadoMenor60dias;
        }


        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

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

        //Faz a indexação das regiões codigo do supervisor e nome do gerente comercial
        $regioesIndexadas = [];
        foreach ($regioes_mysql as $regiao) {
            $regioesIndexadas[$regiao->cd_areacomercial] = $regiao->name;
        }


        //Busca os dados de cobrança com as informações de vencimento, valor, etc.
        //e adiciona os valores de recebimento maior que 61 dias e o nome da
        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao, $cd_empresa);

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

        return response()->json($data);
    }

    public function getRecebimentoLiquidado()
    {
        return $this->cobranca->getRecebimentoLiquidado();
    }

    public function getRecebimentoLiquidadoProvisorio()
    {

        $data = $this->cobranca->getRecebimentoLiquidado();

        return Datatables::of($data)
            ->addColumn('RECEBERMAIOR61DIAS', function ($data) {
                return number_format($data->RECEBERMAIOR61DIAS, 2, ',', '.');
            })
            ->addColumn('LIQUIDADOMAIOR61DIAS', function ($data) {
                return number_format($data->LIQUIDADOMAIOR61DIAS, 2, ',', '.');
            })
            ->addColumn('RECEBERMENOR60DIAS', function ($data) {
                return number_format($data->RECEBERMENOR60DIAS, 2, ',', '.');
            })
            ->addColumn('LIQUIDADOMENOR60DIAS', function ($data) {
                return number_format($data->LIQUIDADOMENOR60DIAS, 2, ',', '.');
            })
            ->make(true);
    }
}
