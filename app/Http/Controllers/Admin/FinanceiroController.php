<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Financeiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class FinanceiroController extends Controller
{
    public $request, $financeiro, $user, $empresa;
    public function __construct(
        Request $request,
        Financeiro $financeiro,
        Empresa $empresa,
    ) {
        $this->empresa = $empresa;
        $this->request = $request;
        $this->financeiro = $financeiro;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function liberaContas()
    {
        $title_page   = 'Liberação de Contas a Pagar';
        $user_auth    = $this->user;
        $uri          = $this->request->route()->uri();
        $uri = explode('/', $uri);
        $uri = $uri[1];

        return view('admin.financeiro.libera-contas', compact(
            'title_page',
            'user_auth',
            'uri'

        ));
    }
    public function listContasBloqueadas()
    {
        $status = $this->request->st_visto;
        $data = $this->financeiro->ContasBloqueadas($status);

        $datatables = DataTables::of($data)
            ->addColumn('actions', function ($d) {
                return '<button class="btn-detalhes details-control fas fa-plus-circle btn-table" aria-hidden="true"></button>
                        <button class="btn-detalhes details-centrocusto fas fa-align-justify btn-open btn-table" aria-hidden="true"></button>
                        <button class="btn-detalhes details-motivo far fa-comment-alt btn-open btn-table" aria-hidden="true"></button>
                                                
                        ';
            })
            ->rawColumns(['actions'])
            ->make(true)
            ->getData();

        $qtd_bloqueadas = count($data);
        $vlr_bloqueadas = array_sum(array_map(function ($item) {
            return $item->VL_DOCUMENTO;
        }, $data));

        $qtd_aguardando_analise = count(array_filter($data, function ($item) {
            return $item->ST_VISTO === 'N';
        }));
        $qtd_pendentes_bloqueadas = count(array_filter($data, function ($item) {
            return $item->ST_VISTO === 'S';
        }));


        $vlr_aguardando_analise = array_sum(array_map(function ($item) {
            return $item->ST_VISTO === 'N' ? $item->VL_DOCUMENTO : 0;
        }, $data));

        $vlr_pendentes_bloqueadas = array_sum(array_map(function ($item) {
            return $item->ST_VISTO === 'S' ? $item->VL_DOCUMENTO : 0;
        }, $data));

        return response()->json(
            [
                'datatables' => $datatables,
                'qtd_bloqueadas' => number_format($qtd_bloqueadas),
                'vlr_bloqueadas' => number_format($vlr_bloqueadas, 2, ',', '.'),

                'qtd_aguardando_analise' => number_format($qtd_aguardando_analise),
                'qtd_pendentes_bloqueadas' => number_format($qtd_pendentes_bloqueadas),

                'vlr_aguardando_analise' => number_format($vlr_aguardando_analise, 2, ',', '.'),
                'vlr_pendentes_bloqueadas' => number_format($vlr_pendentes_bloqueadas, 2, ',', '.')
            ]
        );
    }
    public function listHistoricoContasBloqueadas()
    {
        $cd_empresa = $this->request->cd_empresa;
        $nr_lancamento = $this->request->nr_lancamento;

        $data = $this->financeiro->listHistoricoContasBloqueadas($cd_empresa, $nr_lancamento);

        return DataTables::of($data)->make(true);
    }
    public function updateStatusContasBloqueadas()
    {
        $data = $this->request->all();

        foreach ($data['contas'] as $c) {
            $this->financeiro->updateStatusContasBloqueadas(
                $c['cd_empresa'],
                $c['nr_lancamento'],
                $c['status'],
                mb_convert_encoding($c['ds_liberacao'] . ' / ' . $data['ds_liberacao'], 'ISO-8859-1', 'UTF-8')


            );
            $status = $c['status'];
        }
        if ($status == 'S') {
            return response()->json(['warning' => 'Contas ainda esta bloqueada, movidas para bloqueadas pendentes!']);
        } else {
            return response()->json(['success' => 'Contas liberadas com sucesso!']);
        }
    }
    public function listCentroCustoContasBloqueadas()
    {
        $cd_empresa = $this->request->cd_empresa;
        $nr_lancamento = $this->request->nr_lancamento;

        $data = $this->financeiro->listCentroCustoContasBloqueadas($cd_empresa, $nr_lancamento);

        return Datatables::of($data)->make(true);
    }
    public function arquivoRemessa()
    {
        $title_page = 'Arquivo Remessa';
        $user_auth  = $this->user;

        return view('admin.financeiro.arquivo-remessa', compact(
            'title_page',
            'user_auth'
        ));
    }
    public function listArquivoRemessa()
    {
        $dtInicio = $this->request->dt_inicio;
        $dtFim    = $this->request->dt_fim;

        $filtros = [
            'dt_inicio'     => $dtInicio ? \Carbon\Carbon::createFromFormat('d.m.Y', $dtInicio)->format('Y-m-d') : null,
            'dt_fim'        => $dtFim ? \Carbon\Carbon::createFromFormat('d.m.Y', $dtFim)->format('Y-m-d') : null,
            'cd_pessoa'     => $this->request->cd_pessoa,
            'cd_formapagto' => $this->request->cd_formapagto,
        ];

        $data = $this->financeiro->arquivoRemessa($filtros);

        $datatables = DataTables::of($data)
            ->addColumn('status', function ($d) {
                $boleto = [
                    'I' => ['label' => 'Boleto Impresso', 'cor' => 'badge-info'],
                    'S' => ['label' => 'Sem Boleto', 'cor' => 'badge-danger'],
                ][$d->ST_BOLETO] ?? null;

                $badges = $boleto
                    ? '<span class="badge ' . $boleto['cor'] . ' mr-1">' . $boleto['label'] . '</span>'
                    : ($d->ST_BOLETO ? '<span class="badge badge-secondary mr-1">' . $d->ST_BOLETO . '</span>' : '');

                if ($d->ST_CARTORIO && $d->ST_CARTORIO !== 'N') {
                    $badges .= '<span class="badge badge-danger mr-1">Cartório</span>';
                }
                if ($d->ST_INCOBRAVEL && $d->ST_INCOBRAVEL !== 'N') {
                    $badges .= '<span class="badge badge-dark mr-1">Incobrável</span>';
                }
                if ($d->ST_SCPC && $d->ST_SCPC !== 'N') {
                    $badges .= '<span class="badge badge-warning mr-1">SCPC</span>';
                }

                return $badges;
            })
            ->addColumn('remessa', function ($d) {
                $remessa = [
                    'Registrar Remessa' => ['label' => 'Registrar Remessa no Banco', 'cor' => 'badge-warning'],
                    'Sem Remessa' => ['label' => 'Sem Arquivo Remessa', 'cor' => 'badge-danger'],
                    'Registro Recusado' => ['label' => 'Registro Recusado', 'cor' => 'badge-info'],
                ][$d->DS_REMESSA] ?? null;

                $badges = $remessa
                    ? '<span class="badge ' . $remessa['cor'] . ' mr-1">' . $remessa['label'] . '</span>'
                    : ($d->DS_REMESSA ? '<span class="badge badge-secondary mr-1">' . $d->DS_REMESSA . '</span>' : '');

                return $badges;
            })
            ->rawColumns(['status', 'remessa'])
            ->make(true)
            ->getData();

        $qtd_titulos = count($data);
        $vlr_titulos = array_sum(array_map(function ($item) {
            return $item->VL_SALDO;
        }, $data));

        $qtd_boleto_impresso = count(array_filter($data, function ($item) {
            return $item->ST_BOLETO === 'I';
        }));
        $qtd_sem_boleto = count(array_filter($data, function ($item) {
            return $item->ST_BOLETO === 'S';
        }));

        $qtd_sem_remessa = count(array_filter($data, function ($item) {
            return $item->DS_REMESSA === 'Sem Remessa';
        }));
        $qtd_registro_recusado = count(array_filter($data, function ($item) {
            return $item->DS_REMESSA === 'Registro Recusado';
        }));
        $qtd_registrar_remessa = $qtd_titulos - $qtd_sem_remessa - $qtd_registro_recusado;

        return response()->json([
            'datatables'  => $datatables,
            'qtd_titulos' => number_format($qtd_titulos),
            'vlr_titulos' => number_format($vlr_titulos, 2, ',', '.'),

            'qtd_boleto_impresso' => number_format($qtd_boleto_impresso),
            'qtd_sem_boleto'      => number_format($qtd_sem_boleto),

            'qtd_sem_remessa'       => number_format($qtd_sem_remessa),
            'qtd_registro_recusado' => number_format($qtd_registro_recusado),
            'qtd_registrar_remessa' => number_format($qtd_registrar_remessa),
        ]);
    }
}
