<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use App\Models\BoletoCliente;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\NotaCliente;
use App\Models\Pessoa;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\Nota\NotaLayoutData;
use App\Services\Pdf\ChromePdfService;
use App\Services\SupervisorAuthService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AcessoClienteController extends Controller
{
    public $request, $pessoa, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $nota, $boleto;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,
        NotaCliente $nota,
        BoletoCliente $boleto,
        Pessoa $pessoa

    ) {
        $this->request = $request;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->nota = $nota;
        $this->boleto = $boleto;
        $this->pessoa = $pessoa;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    // Notas fiscais
    public function listNotasEmitidasCliente()
    {
        $title_page   = 'Notas Emitidas';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();

        $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id);

        if (Helper::is_empty_object($cd_pessoa)) {
            abort(403, 'Cliente sem vínculo com usuário no sistema.');
        }

        return view('admin.cliente.notas', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa'
        ));
    }
    public function getListNotasEmitidasCliente()
    {
        $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
            ->pluck('cd_pessoa')
            ->implode(',');
        $data = $this->nota->getListNotaCliente(null, $cd_pessoa);

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-danger btn-xs">Nota</a>';
                // $btn .= '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-secondary btn-xs ml-1">Boleto</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function layoutNotaEmitidaCliente($id, ChromePdfService $chromePdf)
    {
        $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
            ->pluck('cd_pessoa')
            ->implode(',');

        $data = $this->nota->getListNotaCliente($id, $cd_pessoa);
        $layout = (new NotaLayoutData())->build($data);

        // Chromium headless (mesmo motor do disparo): renderização idêntica ao
        // preview HTML e quebra de página confiável. As margens vêm do
        // @page{margin} do CSS do layout, que o Chromium respeita.
        $html = view(NotaLayoutData::viewName($data[0]->CD_EMPRESA), $layout)->render();
        $pdf  = $chromePdf->fromHtml($html);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="nota_fiscal.pdf"',
        ]);
    }

    // Boletos
    public function getListBoletosEmitidosCliente()
    {
        $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
            ->pluck('cd_pessoa')
            ->implode(',');

        $data = $this->boleto->BoletoResumo($cd_pessoa);

        return DataTables::of($data)
            ->addColumn('action', function ($d) {
                $dataAttrs = [

                    'nr_lancamento' => $d->NR_LANCAMENTO,
                    'cd_empresa' => $d->CD_EMPRESA,
                    'nr_parcela' => $d->NR_PARC,
                ];

                $dataString = collect($dataAttrs)
                    ->map(function ($value, $key) {
                        return 'data-' . $key . '="' . $value . '"';
                    })->implode(' ');
                $btn = '<a href="' . route('get-layout-boleto-emitida', $dataAttrs) . '" class="btn btn-danger btn-xs">Boleto</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function layoutBoletoEmitidoCliente(ChromePdfService $chromePdf)
    {
        $cd_pessoa = $this->pessoa->findPessoaUser($this->user->id)
            ->pluck('cd_pessoa')
            ->implode(',');
        $nr_lancamento = $this->request->nr_lancamento;
        $cd_empresa = $this->request->cd_empresa;
        $nr_parcela = $this->request->nr_parcela;

        $boleto = $this->boleto->Boleto($nr_lancamento, $cd_empresa, $nr_parcela, $cd_pessoa);
        $boleto = $boleto[0];

        $codigo_barras = $this->getImagemCodigoDeBarras($boleto->DS_CODIGOBARRA);

        // Chromium headless (mesmo motor da nota e do disparo): margens vêm do
        // @page do layout e o barcode usa o boleto.css (width, sub-pixel).
        $html = view('admin.layouts.layout-boleto-atz', compact('codigo_barras', 'boleto'))->render();
        $pdf  = $chromePdf->fromHtml($html);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="boleto-' . $nr_lancamento . '.pdf"',
        ]);
    }
    public function getImagemCodigoDeBarras($codigo_barras)
    {
        return Helper::codigoBarrasHtml($codigo_barras);
    }
}
