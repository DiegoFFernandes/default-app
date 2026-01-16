<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\LiberaOrdemComercial;
use App\Models\NotaBorracheiro;
use App\Models\PedidoPneu;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\SupervisorSubgrupo;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\DataTables;

class VendedorBorrachariaController extends Controller
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
    public NotaBorracheiro $notaBorracheiro;

    public function __construct(
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        SupervisorAuthService $supervisorComercial,
        SupervisorComercial $supervisorComercialModel,
        SupervisorSubgrupo $supervisorSubgrupo,
        PedidoPneu $pedido,
        NotaBorracheiro $notaBorracheiro
    ) {
        $this->request = $request;
        $this->pedido = $pedido;
        $this->notaBorracheiro = $notaBorracheiro;
        $this->area = $area;
        $this->supervisorComercialModel = $supervisorComercialModel;
        $this->supervisorComercial = $supervisorComercial;
        $this->supervisorSubgrupo = $supervisorSubgrupo;
        $this->regiao = $regiao;
        $this->notaBorracheiro = $notaBorracheiro;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {

        $title_page   = 'Requisão Borracharia';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();

        if ($this->user->hasRole('supervisor')) {
            $supervisor = $this->supervisorComercial->getCdSupervisor();
            if (is_null($supervisor)) {
                return Redirect::back()->with('warning', 'Usuário sem vinculo com Supervisor Comercial, fale com o Administrador do sistema!');
            }
        }

        return view('admin.comercial.requisicao-borracharia', compact(
            'title_page',
            'user_auth',
            'uri',
        ));
    }


    public function getRequisicaoBorracharia()
    {

        $datas = $this->request->validate([
            'dt_inicio' => 'required|date_format:d.m.Y',
            'dt_fim' => 'required|date_format:d.m.Y',
        ]);

        session(['datas' => $datas]);

        $dados = $this->notaBorracheiro->getRequisicaoBorracharia($datas['dt_inicio'], $datas['dt_fim']);

        // Busca no mysql as regiões de gerente comercial vinculadas as Gerente Comercial
        $regioes_mysql = $this->area->GerenteSupervisorAll()->keyBy('cd_areacomercial');

        //faz a indexação dos valores por gerente comercial
        $hierarquia = [];

        foreach ($dados as $item) {
            foreach ($regioes_mysql as $regiao) {
                if ($item->CD_SUPERVISOR == $regiao->cd_areacomercial) {

                    // --- GERENTE ---
                    $nomeGerente = $regiao->name ?? 'Sem gerente';
                    if (!isset($hierarquia[$nomeGerente])) {
                        $hierarquia[$nomeGerente] = [
                            'nome' => $nomeGerente,
                            'cargo' => 'Gerente',
                            'vl_comissao' => 0,
                            'qtd_item' => 0,
                            'supervisores' => [],
                        ];
                    }
                    $hierarquia[$nomeGerente]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['qtd_item'] += $item->QTD_ITEM;
                    
                    // --- SUPERVISOR ---
                    $nomeSupervisor = $item->NM_SUPERVISOR ?? 'Sem supervisor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor] = [
                            'nome' => $nomeSupervisor,
                            'cargo' => 'Supervisor',
                            'vl_comissao' => 0,
                            'qtd_item' => 0,
                            'borracheiros' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['qtd_item'] += $item->QTD_ITEM;

                    // --- VENDEDOR ---
                    $nomeBorracheiro = $item->NM_BORRACHEIRO ?? 'Sem Borracheiro';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['borracheiros'][$nomeBorracheiro])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['borracheiros'][$nomeBorracheiro] = [
                            'nome' => $nomeBorracheiro,
                            'cargo' => 'Borracheiro',
                            'vl_comissao' => 0,
                            'qtd_item' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['borracheiros'][$nomeBorracheiro]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['borracheiros'][$nomeBorracheiro]['qtd_item'] += $item->QTD_ITEM;


                    // --- CLIENTE ---
                    $nomeCliente = $item->NM_PESSOA ?? 'Sem cliente';
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['borracheiros'][$nomeBorracheiro]['clientes'][] = [
                        'PESSOA' => $nomeCliente,
                        'QTD_ITEM'  => $item->QTD_ITEM,
                        'VL_COMISSAO'  => $item->VL_COMISSAO,
                        'ST_BORRACHARIA'  => $item->ST_BORRACHARIA
                    ];
                }
            }
        }

        // --- Normaliza a hierarquia em arrays 
        foreach ($hierarquia as &$gerente) {
            // supervisores
            $gerente['supervisores'] = array_values($gerente['supervisores']);
            foreach ($gerente['supervisores'] as &$supervisor) {
                // borracheiros
                $supervisor['borracheiros'] = array_values($supervisor['borracheiros']);
            }
        }
        unset($gerente);
        unset($supervisor);


        $datatables = DataTables::of($dados)
            ->addColumn('actions', function ($data) {
                $btn = '';

                if ($data->ST_BORRACHARIA === 'S') {
                    $btn .= '<button type="button" class="btn btn-warning btn-xs btn-desabilita-cliente mr-1" style="width: 30px;" data-cd-pessoa="' . $data->CD_PESSOA . '" title="Desabilitar Cliente"><i class="fas fa-times" ></i></button>';
                } else {
                    $btn .= '<button type="button" class="btn btn-success btn-xs btn-habilita-cliente mr-1" style="width: 30px;" data-cd-pessoa="' . $data->CD_PESSOA . '" title="Habilitar Cliente"><i class="fas fa-check"></i></button>';
                }

                $btn .= '<button type="button" class="btn btn-info btn-xs btn-view-requisicao-borracharia" style="width: 30px;" data-cd-borracheiro="' . $data->CD_BORRACHEIRO . '"  title="Ver Detalhes"><i class="fas fa-eye"></i></button>';

                return $btn;
            })
            ->addColumn('gerente_comercial', function ($data) use ($regioes_mysql) {
                foreach ($regioes_mysql as $regiao) {
                    if ($data->CD_SUPERVISOR == $regiao->cd_areacomercial) {
                        return $regiao->name ?? 'SEM GERENTE';
                    }
                }
                return 'SEM GERENTE';
            })
            ->setRowClass(function ($d) {
                return $d->ST_BORRACHARIA === 'N' ? 'bg-secondary disabled' : '';
            })
            ->rawColumns(['actions'])
            ->make(true)
            ->getData();


        return response()->json([
            'datatables' => $datatables,
            'accordionResumoGerente' => array_values($hierarquia),

        ]);
    }


    public function getDetailsRequisicaoBorracharia()
    {

        $validate = $this->request->validate([
            'cd_pessoa' => 'required|integer',
            'cd_borracheiro' => 'required|integer',
        ]);

        if (session()->has('datas')) {
            $datas = session('datas');
        }

        $dados = $this->notaBorracheiro->getDetailsRequisicaoBorracharia(
            $validate['cd_pessoa'],
            $validate['cd_borracheiro'],
            $datas['dt_inicio'],
            $datas['dt_fim']
        );

        return DataTables::of($dados)

            ->make(true);
    }

    public function desabilitaClienteBorracharia()
    {
        $validate = $this->request->validate([
            'cd_pessoa' => 'required|integer',
            'st_borracheiro' => 'required|in:S,N',
        ]);

        $data = $this->notaBorracheiro->desabilitaClienteBorracharia(
            $validate['cd_pessoa'],
            $validate['st_borracheiro']
        );

        return $data;
    }
}
