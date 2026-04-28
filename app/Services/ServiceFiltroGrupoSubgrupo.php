<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ServiceFiltroGrupoSubgrupo
{

    public function __construct() {}

    public function obterSubgruposValidos($agrupamentoId = null)
    {
        $agrupamento = DB::connection('mysql')
            ->table('filtro_agrupamentos')
            ->whereIn('id', explode(',', $agrupamentoId))
            ->select('filtro_agrupamentos.ds_agrupamento')
            ->get();        

        $subgrupo = DB::connection('mysql')
            ->table('filtro_sugrupos')
            ->join('filtro_agrupamentos', 'filtro_sugrupos.cd_agrupamento', '=', 'filtro_agrupamentos.id')
            ->where('filtro_sugrupos.st_ativo', 'S')
            ->when($agrupamentoId, function ($query) use ($agrupamentoId) {
                $query->whereIn('filtro_agrupamentos.id', explode(',', $agrupamentoId));
            })
            ->select('filtro_sugrupos.*')
            ->get();

        if ($subgrupo->isEmpty()) {
            $ds_agrupamento = $agrupamento->pluck('ds_agrupamento')->implode(', ');
            return [
                'success' => false,
                'message' => 'Nenhum subgrupo de ' . $ds_agrupamento . ' válido encontrado, faça o vinculo do subgrupo com um agrupamento.'
            ];
        }

        $ids = $subgrupo->pluck('cd_sugrupo')->implode(',');

        return ['success' => true, 'data' => $ids];
    }

    public function obterGruposValidos($agrupamentoId = null)
    {
        $agrupamento = DB::connection('mysql')
            ->table('filtro_agrupamentos')
            ->whereIn('id', explode(',', $agrupamentoId))
            ->select('filtro_agrupamentos.ds_agrupamento')
            ->get();

        $grupo = DB::connection('mysql')
            ->table('filtro_grupos')
            ->join('filtro_agrupamentos', 'filtro_grupos.cd_agrupamento', '=', 'filtro_agrupamentos.id')
            ->where('filtro_grupos.st_ativo', 'S')
            ->when($agrupamentoId, function ($query) use ($agrupamentoId) {
                $query->whereIn('filtro_agrupamentos.id', explode(',', $agrupamentoId));
            })
            ->select('filtro_grupos.*')
            ->get();

        if ($grupo->isEmpty()) {
            $ds_agrupamento = $agrupamento->pluck('ds_agrupamento')->implode(', ');
            return [
                'success' => false,
                'message' => 'Nenhum grupo de ' . $ds_agrupamento . ' válido encontrado, faça o vinculo do grupo com um agrupamento.'
            ];
        }

        $ids = $grupo->pluck('cd_grupo')->implode(',');

        return ['success' => true, 'data' => $ids];
    }

    public function retornaExistsMsg($isValid){
        if (!$isValid['success']) {
            return response()->json(['errors' => $isValid['message']]);
        }
    }
}
