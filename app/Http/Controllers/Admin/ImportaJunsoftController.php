<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\MarcaPneu;
use App\Services\ServiceFiltroGrupoSubgrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportaJunsoftController extends Controller
{
    public $empresa;
    public $request;
    public $item;
    public $marca; 
    public $user; 
    public $serviceFiltroGrupoSubgrupo;

    public function __construct(
        Request $request,
        Empresa $empresa,
        MarcaPneu $marca,
        Item $item,
        ServiceFiltroGrupoSubgrupo $serviceFiltroGrupoSubgrupo
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->item = $item;
        $this->marca = $marca;
        $this->serviceFiltroGrupoSubgrupo = $serviceFiltroGrupoSubgrupo;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }


    public function index()
    {
        $marcas        = $this->marca->MarcaAll();

        return view('admin.importa_junsoft.index', compact('marcas'));
    }

    public function AjaxImportaItem(){
        $cd_marca = $this->request->cd_marca;            
        
        // 6 - BANDAS
        $isValidSubgrupoReformaCarga = $this->serviceFiltroGrupoSubgrupo->obterSubgruposValidos(6);

        $import = $this->item->ImportaItemJunsoft($cd_marca, $isValidSubgrupoReformaCarga);
        
        if($import == 1){
            return response()->json(['success' => "Importação de produto realizada com sucesso!"]);
        }
        return response()->json(['error' => $import]);
    }
}
