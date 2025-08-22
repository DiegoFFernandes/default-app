<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use App\Models\BoletoCliente;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\NotaCliente;
use App\Models\Producao;
use App\Models\RegiaoComercial;
use App\Models\User;
use App\Services\SupervisorAuthService;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AcessoClienteController extends Controller
{
    public $request, $regiao, $empresa, $user, $producao, $supervisorComercial, $gerenteUnidade, $nota, $boleto;

    public function __construct(
        Request $request,
        Empresa $empresa,
        User $user,
        NotaCliente $nota,
        BoletoCliente $boleto

    ) {
        $this->request = $request;
        $this->user = $user;
        $this->empresa = $empresa;
        $this->nota = $nota;
        $this->boleto = $boleto;

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
        $data = $this->nota->getListNotaCliente();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-danger btn-xs">Nota</a>';
                $btn .= '<a href="' . route('get-layout-nota-emitida', ['id' => $row->NR_LANCAMENTO]) . '" class="btn btn-secondary btn-xs ml-1">Boleto</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function layoutNotaEmitidaCliente($id)
    {

        $data = $this->nota->getListNotaCliente($id);
        $title_page   = 'Notas Emitidas';
        $user_auth    = $this->user;
        $exploder     = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();
        $user =  $this->user->getData();


        return view('admin.cliente.layout-nota', compact(
            'title_page',
            'user_auth',
            'uri',
            'user',
            'empresa',
            'data'
        ));
    }

    // Boletos

    public function getListBoletosEmitidosCliente()
    {
        $data = $this->boleto->BoletoResumo();

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
    public function layoutBoletoEmitidoCliente()
    {
        $nr_lancamento = $this->request->nr_lancamento;
        $cd_empresa = $this->request->cd_empresa;
        $nr_parcela = $this->request->nr_parcela;

        $boleto = $this->boleto->Boleto($nr_lancamento, $cd_empresa, $nr_parcela);
        $boleto = $boleto[0];

        $codigo_barras = $this->getImagemCodigoDeBarras($boleto->DS_CODIGOBARRA);

        $view = view('admin.cliente.layout-boleto', compact('codigo_barras', 'boleto'));

        $html = $view->render();

        // Configurando o Snappy
        $options = [
            // 'page-size' => 'A4',
            'no-stop-slow-scripts' => true,
            'enable-javascript' => true,
            'lowquality' => true,
            'encoding' => 'UTF-8'
        ];

        $pdf = SnappyPdf::loadHTML($html)->setOptions($options);

        // $pdf->inline('nota_fiscal.pdf'); //Exibe o pdf sem fazer o downlaod.
        return $pdf->download('boleto-' . $nr_lancamento . '.pdf'); //Faz o download do arquivo.
    }
    public function getImagemCodigoDeBarras($codigo_barras)
    {
        $codigo_barras = (strlen($codigo_barras) % 2 != 0 ? '0' : '') . $codigo_barras;
        $barcodes = ['00110', '10001', '01001', '11000', '00101', '10100', '01100', '00011', '10010', '01010'];
        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1 * 10) + $f2;
                $texto = "";
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }

        // Guarda inicial
        $retorno = '<div class="barcode">' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>';

        // Draw dos dados
        while (strlen($codigo_barras) > 0) {
            $i = round(substr($codigo_barras, 0, 2));
            $codigo_barras = substr($codigo_barras, strlen($codigo_barras) - (strlen($codigo_barras) - 2), strlen($codigo_barras) - 2);
            $f = $barcodes[$i];
            for ($i = 1; $i < 11; $i += 2) {
                if (substr($f, ($i - 1), 1) == "0") {
                    $f1 = 'thin';
                } else {
                    $f1 = 'large';
                }
                $retorno .= "<div class='black {$f1}'></div>";
                if (substr($f, $i, 1) == "0") {
                    $f2 = 'thin';
                } else {
                    $f2 = 'large';
                }
                $retorno .= "<div class='white {$f2}'></div>";
            }
        }

        // Final
        return $retorno . '<div class="black large"></div>' .
            '<div class="white thin"></div>' .
            '<div class="black thin"></div>' .
            '</div>';
    }
}
