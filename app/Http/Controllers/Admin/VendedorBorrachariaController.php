<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\GerenteUnidade;
use App\Models\LiberaOrdemComercial;
use App\Models\NotaBorracheiro;
use App\Models\PedidoPneu;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Models\SupervisorSubgrupo;
use App\Models\User;
use App\Models\Vendedor;
use App\Services\SupervisorAuthService;
use App\Services\UserRoleFilterService;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Js;
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
    public GerenteUnidade $gerenteUnidade;
    public Vendedor $vendedorComercial;

    public function __construct(
        Request $request,
        AreaComercial $area,
        RegiaoComercial $regiao,
        SupervisorAuthService $supervisorComercial,
        SupervisorComercial $supervisorComercialModel,
        SupervisorSubgrupo $supervisorSubgrupo,
        GerenteUnidade $gerenteUnidade,
        Vendedor $vendedor,
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
        $this->gerenteUnidade = $gerenteUnidade;
        $this->vendedorComercial = $vendedor;

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

        if ($this->user->hasRole('admin|cobranca')) {
            $gerentes = $this->area->GerenteAll();
        } elseif ($this->user->hasRole('gerente comercial')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $gerentes = $this->area->GerenteAll($this->user->id);
        } elseif ($this->user->hasRole('supervisor')) {
            $gerentes = $this->area->GerenteAll();
        } elseif ($this->user->hasRole('gerente unidade')) {
            $gerentes = $this->area->GerenteAll();
        } elseif ($this->user->hasRole('vendedor')) {
            $gerentes = $this->area->GerenteAll();
        }


        return view('admin.comercial.requisicao-borracharia', compact(
            'title_page',
            'user_auth',
            'uri',
            'gerentes'
        ));
    }


    public function getRequisicaoBorracharia()
    {
        $service = new UserRoleFilterService(
            $this->user,
            $this->area,
            $this->supervisorComercial,
            $this->gerenteUnidade,
            $this->vendedorComercial,
            null
        );

        $filtrosExtras = $service->getFiltros();

        // return $filtrosExtras;

        $datas = $this->request->validate([
            'filtro.nm_vendedor' => 'nullable|string',
            'filtro.nm_borracheiro' => 'nullable|string',
            'filtro.nm_pessoa' => 'nullable|string',
            'filtro.nm_supervisor' => 'nullable|string',
            'filtro.dtInicio' => 'required|date_format:d.m.Y',
            'filtro.dtFim' => 'required|date_format:d.m.Y',
            'filtro.cd_gerente' => 'nullable|integer',
        ]);

        // Caso o usuario tenha filtrado por gerente comercial ele busca os supervisores vinculados a esse gerente
        if ($this->request['filtro.cd_gerente'] ?? 0 <> 0) {
            $filtrosExtras['cd_regiao'] = $this->area->findGerenteSupervisor($this->request['filtro.cd_gerente'])->pluck('CD_AREACOMERCIAL')
                ->implode(',');
        }

        $dados = $this->notaBorracheiro->getRequisicaoBorracharia($datas['filtro'], $filtrosExtras);

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
                            'vendedores' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['qtd_item'] += $item->QTD_ITEM;


                    // --Vendedor
                    $nomeVendedor = $item->NM_VENDEDOR ?? 'Sem Vendedor';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor] = [
                            'nome' => $nomeVendedor,
                            'cargo' => 'Vendedor',
                            'vl_comissao' => 0,
                            'qtd_item' => 0,
                            'borracheiros' => []
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['qtd_item'] += $item->QTD_ITEM;



                    // --- Borracheiro ---
                    $nomeBorracheiro = $item->NM_BORRACHEIRO ?? 'Sem Borracheiro';
                    if (!isset($hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['borracheiros'][$nomeBorracheiro])) {
                        $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['borracheiros'][$nomeBorracheiro] = [
                            'nome' => $nomeBorracheiro,
                            'cargo' => 'Borracheiro',
                            'vl_comissao' => 0,
                            'qtd_item' => 0
                        ];
                    }
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['borracheiros'][$nomeBorracheiro]['vl_comissao'] += $item->VL_COMISSAO;
                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['borracheiros'][$nomeBorracheiro]['qtd_item'] += $item->QTD_ITEM;


                    // --- CLIENTE ---
                    $nomeCliente = $item->NM_PESSOA ?? 'Sem cliente';
                    $btn = '';

                    if ($item->ST_BORRACHARIA === 'S') {
                        $btn .= '<button type="button" class="btn btn-success btn-xs btn-desabilita-cliente" style="width: 25px" data-cd-pessoa="' . $item->CD_PESSOA . '" title="Desabilitar Cliente"><i class="fas fa-check"></i></button>';
                    } else {
                        $btn .= '<button type="button" class="btn btn-danger btn-xs btn-habilita-cliente" style="width: 25px" data-cd-pessoa="' . $item->CD_PESSOA . '" title="Habilitar Cliente"><i class="fas fa-times"></i></button>';
                    }
                    $btn .= '<button type="button" class="btn btn-info btn-xs btn-view-requisicao-borracharia ml-1" style="width: 25px;" 
                                data-cd-borracheiro="' . $item->CD_BORRACHEIRO . '" 
                                data-nm-borracheiro="' . $item->NM_BORRACHEIRO . '" 
                                data-nm-pessoa="' . $item->NM_PESSOA . '"
                                data-cd-pessoa="' . $item->CD_PESSOA . '"                                
                                data-cd-borracheiro="' . $item->CD_BORRACHEIRO . '"
                                title="Ver Detalhes"><i class="fas fa-eye"></i>
                            </button>';

                    $hierarquia[$nomeGerente]['supervisores'][$nomeSupervisor]['vendedores'][$nomeVendedor]['borracheiros'][$nomeBorracheiro]['clientes'][] = [
                        'PESSOA' => $nomeCliente,
                        'QTD_ITEM'  => $item->QTD_ITEM,
                        'VL_COMISSAO'  => intval($item->VL_COMISSAO),
                        'ST_BORRACHARIA'  => $item->ST_BORRACHARIA,
                        'actions'  => $btn,
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
                    // borracheiros
                    $vendedor['borracheiros'] = array_values($vendedor['borracheiros']);
                }
                unset($vendedor);
            }
        }
        unset($gerente);
        unset($supervisor);
        unset($vendedor);


        $datatables = DataTables::of($dados)           
            ->addColumn('gerente_comercial', function ($data) use ($regioes_mysql) {
                foreach ($regioes_mysql as $regiao) {
                    if ($data->CD_SUPERVISOR == $regiao->cd_areacomercial) {
                        return $regiao->name ?? 'SEM GERENTE';
                    }
                }
                return 'SEM GERENTE';
            })            
            
            ->make(true)
            ->getData();

        session(
            [
                'datas' => $datas,
                'hierarquia' => array_values($hierarquia)
            ]
        );


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
            $datas['filtro']['dtInicio'],
            $datas['filtro']['dtFim']
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

    public function printPdfRequisicaoBorracharia()
    {
        if (session()->has('hierarquia')) {
            $hierarquia = session('hierarquia');
            $datas = session('datas');
        }

        $view = view('admin.comercial.layout-requisicao-borracharia', compact('hierarquia', 'datas'));

        $html = $view->render();

        // Configurando o Snappy
        $options = [
            'page-size' => 'A4',
            'no-stop-slow-scripts' => true,
            'enable-javascript' => true,
            'lowquality' => true,
            'encoding' => 'UTF-8',

        ];

        $pdf = SnappyPdf::loadHTML($html)->setOptions($options);

        // return $pdf->inline('requisicao.pdf'); //Exibe o pdf sem fazer o download.

        // return $pdf->download('requisicao.pdf'); //Faz o download do arquivo.

        $fileName = storage_path('app/temp/requisicao_' . time() . '.pdf');

        $pdf->save($fileName);

        return response()->json([
            'url' => route('download-pdf-temp', ['file' => basename($fileName)])
        ]);
    }

    public function downloadPdfTemp($file)
    {
        $filePath = storage_path('app/temp/' . $file);

        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }
    }

    public function getClienteDesabilitadoBorracharia()
    {
        $dados = $this->notaBorracheiro->getClienteDesabilitadoBorracharia();

        return DataTables::of($dados)
            ->addColumn('actions', function ($item) {
                $btn = '';

                if ($item->ST_BORRACHARIA === 'S') {
                    $btn .= '
                        <button 
                            type="button" 
                            class="btn btn-success btn-sm btn-action btn-desabilita-cliente"
                            data-cd-pessoa="' . $item->CD_PESSOA . '"
                            title="Desabilitar cliente">
                            <i class="fas fa-check"></i>
                        </button>';
                } else {
                    $btn .= '
                                <button 
                                    type="button" 
                                    class="btn btn-warning btn-sm btn-action btn-habilita-cliente"
                                    data-cd-pessoa="' . $item->CD_PESSOA . '"
                                    title="Habilitar cliente">
                                    <i class="fas fa-times"></i>
                                </button>';
                }
                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
