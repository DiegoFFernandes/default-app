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
            ->where('id', $agrupamentoId)
            ->select('filtro_agrupamentos.ds_agrupamento')
            ->first();

        $subgrupo = DB::connection('mysql')
            ->table('filtro_sugrupos')
            ->join('filtro_agrupamentos', 'filtro_sugrupos.cd_agrupamento', '=', 'filtro_agrupamentos.id')
            ->where('filtro_sugrupos.st_ativo', 'S')
            ->when($agrupamentoId, function ($query) use ($agrupamentoId) {
                $query->where('filtro_agrupamentos.id', $agrupamentoId);
            })
            ->select('filtro_sugrupos.*')
            ->get();

        if ($subgrupo->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Nenhum subgrupo de ' . $agrupamento->ds_agrupamento . ' válido encontrado, faça o vinculo do subgrupo com um agrupamento.'
            ];
        }

        $ids = $subgrupo->pluck('cd_sugrupo')->implode(',');

        return ['success' => true, 'data' => $ids];
    }

    public function obterGruposValidos($agrupamentoId = null)
    {
        $agrupamento = DB::connection('mysql')
            ->table('filtro_agrupamentos')
            ->where('id', $agrupamentoId)
            ->select('filtro_agrupamentos.ds_agrupamento')
            ->first();

        $grupo = DB::connection('mysql')
            ->table('filtro_grupos')
            ->join('filtro_agrupamentos', 'filtro_grupos.cd_agrupamento', '=', 'filtro_agrupamentos.id')
            ->where('filtro_grupos.st_ativo', 'S')
            ->when($agrupamentoId, function ($query) use ($agrupamentoId) {
                $query->where('filtro_agrupamentos.id', $agrupamentoId);
            })
            ->select('filtro_grupos.*')
            ->get();

        if ($grupo->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Nenhum grupo de ' . $agrupamento->ds_agrupamento . ' válido encontrado, faça o vinculo do grupo com um agrupamento.'
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
