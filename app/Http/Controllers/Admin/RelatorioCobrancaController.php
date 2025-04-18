<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Cobranca;
use App\Models\Empresa;
use App\Models\RegiaoComercial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class RelatorioCobrancaController extends Controller
{
    public $cobranca, $empresa, $request, $area, $regiao, $user;
    public function __construct(
        Request $request,
        RegiaoComercial $regiao,
        AreaComercial $area,
        Cobranca $cobranca,
        Empresa $empresa

    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->cobranca = $cobranca;
        $this->empresa = $empresa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function index()
    {
        $title_page   = 'Titulos em Atraso';
        // $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();
        $empresa = $this->empresa->empresa();
        // $cd_empresa = $this->setEmpresa($this->user->empresa);
        $cd_area = "";


        return view('admin.cobranca.rel-cobranca', compact(
            'empresa',
            'title_page',
            'uri',
        ));
    }
    public function getListCobranca()
    {
        if ($this->user->hasRole('admin|diretoria')) {
            $cd_area = "";
            $cd_regiao = "";
            // $regiao = $this->regiao->regiaoAll();
            // $area = $this->area->areaAll();
        } elseif ($this->user->hasRole('gerente')) {
            //Criar condição caso o usuario for gerente mais não estiver associado no painel
            $find = $this->area->findAreaUser($this->user->id);
            $array = json_decode($find, true);
            if (empty($array)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão  de gerente mais sem vinculo com região, fale com o Administrador do sistema!');
            }
            $regiao = $this->regiao->regiaoArea($find[0]->cd_areacomercial);
            $area = "";
            $cd_area = $find[0]->cd_areacomercial;
            $cd_regiao = "";
        } else {
            $regiaoUsuario = $this->regiao->regiaoPorUsuario($this->user->id);
            $regiao = "";
            $area = "";
            foreach ($regiaoUsuario as $r) {
                $cd_regiao[] = $r->cd_regiaocomercial;
            }
            //verifica se o usuario tem permissão mais ainda nao foi associado região para ele e retorna com mensagem!
            if (empty($cd_regiao)) {
                return Redirect::route('home')->with('warning', 'Usuario com permissão mais sem vinculo com região, fale com o Administrador do sistema!');
            }
            //serialize a informação vinda do banco e faz o implode dos valores separados por (;)
            $cd_regiao = implode(",", $cd_regiao);
        }
        $data = $this->cobranca->AreaRegiaoInadimplentes($cd_regiao, $cd_area);
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
                return '<button class="btn btn-success btn-xs mr-4">Detalhar</button> ' . $d->CD_EMPRESA;
            })
            ->rawColumns(['details'])
            ->make(true);
    }

    public function getListCobrancaFiltro()
    {
        $cd_empresa = intval($this->request->cd_empresa);
        $cd_empresa = $this->setEmpresa($cd_empresa);
        $cd_regiao = $this->request->cd_regiao;

        if ($this->user->hasRole('gerencia|coordenador|admin')) {
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
}
