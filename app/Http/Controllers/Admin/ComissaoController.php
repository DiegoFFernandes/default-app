<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaComercial;
use App\Models\Comissao;
use App\Models\Empresa;
use App\Models\GerenteUnidade;
use App\Models\RegiaoComercial;
use App\Models\SupervisorComercial;
use App\Services\SupervisorAuthService;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use Yajra\DataTables\Facades\DataTables;

class ComissaoController extends Controller
{
    public $request, $comissao, $regiao, $area, $supervisor, $supervisorComercial, $gerenteUnidade, $empresa, $user;

    public function __construct(
        Request $request,

        RegiaoComercial $regiao,
        AreaComercial $area,
        SupervisorComercial $supervisor,
        SupervisorAuthService $supervisorComercial,
        GerenteUnidade $gerenteUnidade,
        Empresa $empresa,
        Comissao $comissao
    ) {
        $this->request = $request;
        $this->regiao = $regiao;
        $this->area = $area;
        $this->supervisor = $supervisor;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
        $this->empresa = $empresa;
        $this->comissao = $comissao;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function comissaoVendedorFaturamento()
    {
        $title_page   = 'Comissão Vendedor';
        $exploder = explode('/', $this->request->route()->uri());
        $uri = ucfirst($exploder[1]);
        $empresa = $this->empresa->empresa();

        // Implement the logic for comissaoVendedorFaturamento
        return view('admin.comercial.comissao-vendedor', compact(
            'title_page',
            'uri',
            'empresa'
        ));
    }

    public function getComissaoVendedorFaturamento()
    {
        $data = $this->comissao->getComissaoFaturamento();

        return DataTables::of($data)
            
            ->make(true);
    }

    // Other methods related to ComissaoController can be added here
}
