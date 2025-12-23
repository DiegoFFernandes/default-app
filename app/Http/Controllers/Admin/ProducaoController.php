<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\Pessoa;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProducaoController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $pessoa, $area;

    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        Empresa $empresa,
        User $user,
        Producao $producao,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        Pessoa $pessoa,
        AreaComercial $area
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->producao = $producao;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->pessoa = $pessoa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Produzidos - Sem Faturar';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();

        $user =  $this->user->getData();
        $regiao = $this->regiao->regiaoAll();

        if ($this->user->hasRole('admin|gerente comercial')) {
            $regiao = $this->regiao->regiaoAll();
        } elseif ($this->user->hasRole('supervisor')) {
            $regiao = $this->regiao->findRegiaoUser($this->user->id);
        } elseif ($this->user->hasRole('gerente unidade')) {
            $regiao = $this->regiao->regiaoAll();
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
            $empresa = $this->empresa->empresa($cd_empresa);
        }

        $list_regiao = $this->regiao->showUserRegiao();

        return view('admin.producao.produzidos-sem-faturar', compact(
            'title_page',
            'user_auth',
            'uri',
            'regiao',
            'user',
            'list_regiao',
            'empresa'
        ));
    }
    public function getListPneusProduzidosFaturar()
    {
        $cd_regiao = "";
        $supervisor = 0;
        $cd_empresa = 0;

        if ($this->user->hasRole('admin|gerente comercial')) {
            $cd_regiao = "";
            $supervisor = 0;
            $cd_empresa = 0;
        } elseif ($this->user->hasRole('supervisor')) {
            $cd_regiao = "";
            $cd_empresa = 0;
            $supervisor = $this->supervisorComercial->getCdSupervisor();
        } elseif ($this->user->hasRole('gerente unidade')) {
            $cd_regiao = "";
            $supervisor = 0;
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');
        } else if ($this->user->hasRole('cliente')) {
            $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
                ->pluck('cd_pessoa')
                ->implode(',');
        }

        if (!empty($this->request->data['regiao'])) {
            $cd_regiao = implode(',', $this->request->data['regiao']);
        }

        $data = $this->producao->getPneusProduzidosFaturar($cd_empresa, $cd_regiao, $supervisor, $this->request->data, $cd_pessoa ?? 0);

        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');
        $hierarquia = [];

        foreach ($data as $r) {
            foreach ($regioes_mysql as $regiao) {
                if ($r->CD_VENDEDORGERAL == $regiao->cd_areacomercial) {

                    //Adiciona o nome do geerente no objeto data
                    $r->NM_GERENTE = $regiao->name ?? 'Sem gerente';

                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'cargo' => 'Gerente',
                            'supervisores' => [],
                            'qtd' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['qtd'] += $r->PNEUS;
                }
            }
        }


        $datatables = DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                $btn = '';
                $btn .= '<span class="right m-0 p-0 btn-detalhes"><i class="fas fa-plus-circle"></i></span> ';
                $btn .= '<span class="btn-observacao-embarque p-0 m-0"><i class="fas fa-comment-dots"></i></span>';
                return $btn;
            })
            ->addColumn('NM_PESSOA', function ($row) {
                return $row->NM_PESSOA;
            })
            ->addColumn('VALOR', function ($row) {
                return number_format($row->VALOR, 2, ',', '.');
            })
            ->addColumn('PNEUS', function ($row) {
                return $row->PNEUS;
            })
            ->addColumn('EXPEDICIONADO', function ($row) {
                return $row->EXPEDICIONADO;
            })
            ->addColumn('DTENTREGA', function ($row) {
                return date('d/m/Y', strtotime($row->DTENTREGA));
            })
            ->rawColumns(['actions', 'NM_PESSOA', 'VALOR', 'PNEUS', 'EXPEDICIONADO', 'DTENTREGA'])
            ->make(true)
            ->getData();


        return response()->json([
            'datatables' => $datatables,
            'hierarquia' => $hierarquia
        ]);
    }
    public function getListPneusProduzidosFaturarDetails()
    {
        $nr_embarque = $this->request->get('nr_embarque');
        $pedido = $this->request->get('pedido');
        $expedicionado = $this->request->get('expedicionado');

        if ($nr_embarque == 'SEM EMBARQUE') {
            $nr_embarque = 0;
        }

        $data = $this->producao->getPneusProduzidosFaturarDetails($pedido, $nr_embarque, $expedicionado);

        return DataTables::of($data)->make(true);
    }
}
