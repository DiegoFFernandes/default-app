<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\LotePcpRecap;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\ServiceFiltroGrupoSubgrupo;
use App\Services\SupervisorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

class PcpProducaoController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $serviceFiltroGrupoSubgrupo, $lotePcpRecap;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user,
        Producao $producao,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        ServiceFiltroGrupoSubgrupo $serviceFiltroGrupoSubgrupo,
        LotePcpRecap $lotePcpRecap

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->producao = $producao;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->serviceFiltroGrupoSubgrupo = $serviceFiltroGrupoSubgrupo;
        $this->lotePcpRecap = $lotePcpRecap;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function pneusLotePCP()
    {
        $title_page   = 'Painel de PCP';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);

        $canEditPCP = $this->user->hasPermissionTo('editar-pneus-lote-pcp');

        if ($this->user->hasRole('admin|diretoria')) {
            $empresa = $this->empresa->empresa();
        } else if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else {
            $empresa = $this->empresa->empresa($this->user->empresa);
        }
        return view('admin.producao.pcp.pcp-producao', compact(
            'title_page',
            'uri',
            'empresa',
            'canEditPCP'
        ));
    }

    //tras as informaçõs dos pneus do lote de PCP em atraso
    public function getPneusAtrasoLotePCP()
    {
        $cd_empresa = $this->request->validate([
            'cd_empresa' => 'required|integer',
        ])['cd_empresa'];

        $lote = $this->getLotePCP()->getData(true)['data'];

        $data = $this->producao->getPneusAtrasoLotePCP($cd_empresa);

        $datatables = DataTables::of($data)
            ->addColumn('actions', function ($row) {

                if ($this->user->hasPermissionTo('editar-pneus-lote-pcp')) {

                    return "<button class='btn btn-xs btn-danger btn-remover-pneus-lote' 
                            data-empresa='" . $row->IDEMPRESA . "' 
                            data-lote='" . $row->NR_LOTE . "'
                            data-ordem='" . $row->NR_ORDEM . "'
                            data-etapa='" . $row->CD_ETAPA . "'
                            
                            >
                             <i class='fa fa-trash'></i>
                        </button>
                        <button class='btn btn-xs btn-primary btn-transferir-pneus-lote' 
                            data-empresa='" . $row->IDEMPRESA . "' 
                            data-lote='" . $row->NR_LOTE . "'
                            data-ordem='" . $row->NR_ORDEM . "'
                            >
                             <i class='fa fa-exchange-alt'></i>
                        </button>                        
                        ";
                } else {
                    return "<button class='btn btn-xs btn-secondary' disabled>
                                <i class='fa fa-truck'></i>
                            </button>                                                
                         ";
                }
            })
            ->editColumn('DS_ETAPA', function ($row) {
                $badgeClass = 'secondary';
                switch ($row->CD_ETAPA) {
                    case 0:
                        $badgeClass = 'danger';
                        break;
                    case 1:
                        $badgeClass = 'warning';
                        break;
                    case 3:
                        $badgeClass = 'info';
                        break;
                    case 4:
                        $badgeClass = 'primary';
                        break;
                    case 5:
                        $badgeClass = 'dark';
                        break;
                    default:
                        $badgeClass = 'secondary';
                }
                return "<span class='badge badge-{$badgeClass} w-100'>{$row->DS_ETAPA}</span>";
            })
            ->rawColumns(['actions', 'DS_ETAPA'])
            ->make(true)
            ->getData();


        return response()->json([
            'lote' => $lote,
            'datatables' => $datatables,
        ]);
    }

    public function getLotePCP()
    {
        if ($this->user->hasRole('admin')) {
            $empresa = $this->empresa->empresa();
            $empresa = collect($empresa)->pluck('CD_EMPRESA')->implode(',');
        } else if ($this->user->hasRole('gerente unidade')) {
            $empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else {
            $empresa = $this->user->empresa;
        }
        $data = $this->producao->getLotePCP($empresa);
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return '<button class="btn btn-xs btn-secondary btn-pneus-lote" 
                            data-empresa="' . $row->CD_EMPRESA . '" 
                            data-lote="' . $row->NR_LOTE . '">
                        <i class="fa fa-eye" style="color: white;"></i></button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detalhesPneusLotePCP()
    {
        $validated = $this->request->validate([
            'empresa' => 'required|integer',
            'lote' => 'required|integer',
        ]);

        $cd_empresa = $validated['empresa'];
        $lote = $validated['lote'];

        $data = $this->producao->getDetalhesPneusLotePCP($cd_empresa, $lote);

        return DataTables::of($data)
            ->editColumn('STORDEM', function ($row) {
                return "<span class='badge 
                    badge-" . ($row->STORDEM === 'A' ? 'warning' : ($row->STORDEM === 'F' ? 'success' : 'danger')) . "'>" . $row->STATUS .

                    "</span>";
            })
            ->rawColumns(['STORDEM'])
            ->make(true);
    }

    public function consumoEstoqueLoteMateriaPrima()
    {
        //esses subgrupo não serão utilizados na consulta.
        // 2 - REFORMA AGRICOLA
        // 5 - ENCHIMENTO
        // 6 - VULCANIZAÇÃO
        // 7 - VULCANIZACAO OTR
        // 10 - DUPLAGEM
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos('2,5,6,7,10')['data'];

        $localestoque = 1;
        $tipolocalestoque = 1;

        $data = $this->producao->consumoEstoqueLoteMateriaPrima($subgrupo, $localestoque, $tipolocalestoque);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {

                if ($row->ST_BANDA === 'NAO') {
                    return '<button class="btn btn-xs btn-outline-warning btn-banda-sem-associacao">
                        <i class="fa fa-exclamation-circle"></i> VER</button>';
                } else {
                    return '<button class="btn btn-xs btn-outline-success btn-banda-com-associacao">
                        <i class="fa fa-check"></i></button>';
                }
            })
            ->editColumn('QT_CONSUMO', function ($row) {
                if (($row->QT_CONSUMO >= $row->QT_ESTOQUE)) {
                    return '<span class="badge badge-danger text-xs">' . $row->QT_CONSUMO . '</span>';
                } else {
                    return '<span class="badge badge-success text-xs">' . $row->QT_CONSUMO . '</span>';
                }
            })
            ->editColumn('QT_ESTOQUE', function ($row) {
                if (($row->QT_CONSUMO >= $row->QT_ESTOQUE)) {
                    return '<span class="badge badge-danger text-xs">' . $row->QT_ESTOQUE . '</span>';
                } else {
                    return '<span class="badge badge-success text-xs">' . $row->QT_ESTOQUE . '</span>';
                }
            })
            ->rawColumns(['QT_CONSUMO', 'QT_ESTOQUE', 'actions'])
            ->make(true);
    }

    public function bandasSemAssociacao()
    {
        $subgrupo = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos('5,6,7')['data'];

        $data = $this->producao->bandasSemAssociacao($subgrupo);

        return DataTables::of($data)
            ->make(true);
    }

    public function removerOrdemProducaoLotePCP()
    {
        $validated = $this->request->validate([
            'cd_empresa' => 'required|integer',
            'nr_lote' => 'required|integer',
            'ordem_producao' => 'required|integer',
        ]);

        $cdEmpresa = $validated['cd_empresa'];
        $nrLote = $validated['nr_lote'];
        $nrOrdemProducao = $validated['ordem_producao'];

        //verifica se a ordem de produção já passou no exame inicial
        $exists = $this->producao->existExameInicial($nrOrdemProducao);

        if (!$exists) {
            try {
                //remove a ordem de produção do lote de PCP caso ainda não passou no exame inicial
                $this->producao->removerOrdemProducaoLotePCP($nrOrdemProducao);

                // 1 = EXISTE PNEUS EM ABERTO
                // 0 = NÃO EXISTE PNEUS EM ABERTO
                $verificaPneusLotePcpRecapAberto = $this->lotePcpRecap->verificaPneusLotePcpRecapAberto($nrLote);

                if ($verificaPneusLotePcpRecapAberto === 0) {
                    //muda o status do lote de PCP para FECHADO, pois não tem pneus em aberto
                    $this->lotePcpRecap->fecharLotePcpRecap($nrLote, $cdEmpresa);
                }

                return response()->json(['success' => true, 'message' => 'Ordem de produção ' . $nrOrdemProducao . ' removida do lote de PCP.']);
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'message' => 'Erro ao remover a ordem de produção do lote de PCP: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Não é possível remover a ordem de produção, Já passou no exame inicial.']);
        }
    }
}
