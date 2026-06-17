<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompraCentroCusto extends Model
{
    use HasFactory;

    public function getAll()
    {
        $caseNome = Empresa::buildCaseNome('C.CD_EMPRESA');

        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                C.CD_CENTROCUSTO,
                C.CD_EMPRESA,
                {$caseNome} AS NM_EMPRESA,
                C.DS_CENTROCUSTO,
                C.VL_ORCADO_MES,
                C.CD_USUARIO_RESP,
                C.DIA_INICIO_CICLO,
                C.DT_REGISTRO
            FROM COMPRA_CENTROCUSTO C
            ORDER BY C.CD_EMPRESA, C.CD_CENTROCUSTO
        "));
    }

    public function findById(int $id)
    {
        $caseNome = Empresa::buildCaseNome('C.CD_EMPRESA');

        $row = DB::connection('firebird')->selectOne("
            SELECT
                C.CD_CENTROCUSTO,
                C.CD_EMPRESA,
                {$caseNome} AS NM_EMPRESA,
                C.DS_CENTROCUSTO,
                C.VL_ORCADO_MES,
                C.CD_USUARIO_RESP,
                C.DIA_INICIO_CICLO,
                C.DT_REGISTRO
            FROM COMPRA_CENTROCUSTO C
            WHERE C.CD_CENTROCUSTO = :id
        ", ['id' => $id]);

        return $row ? \Helper::ConvertFormatText([$row])[0] : null;
    }

    public function store(array $data)
    {
        DB::connection('firebird')->statement("
            INSERT INTO COMPRA_CENTROCUSTO (
                CD_CENTROCUSTO, CD_EMPRESA, DS_CENTROCUSTO, VL_ORCADO_MES,
                CD_USUARIO_RESP, DIA_INICIO_CICLO, DT_REGISTRO
            ) VALUES (
                :cd, :cd_empresa, :ds, :vl_orcado,
                :cd_usuario, :dia_inicio, CURRENT_TIMESTAMP
            )
        ", [
            'cd'          => $data['cd_centrocusto'],
            'cd_empresa'  => $data['cd_empresa'],
            'ds'          => \Helper::ToIso($data['ds_centrocusto']),
            'vl_orcado'   => $data['vl_orcado_mes'] ?: null,
            'cd_usuario'  => $data['cd_usuario_resp'] ?: null,
            'dia_inicio'  => $data['dia_inicio_ciclo'] ?: null,
        ]);
    }

    public function updateData(int $id, array $data)
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_CENTROCUSTO SET
                DS_CENTROCUSTO   = :ds,
                VL_ORCADO_MES    = :vl_orcado,
                CD_USUARIO_RESP  = :cd_usuario,
                DIA_INICIO_CICLO = :dia_inicio
            WHERE CD_CENTROCUSTO = :id
        ", [
            'ds'          => $data['ds_centrocusto'],
            'vl_orcado'   => $data['vl_orcado_mes'] ?: null,
            'cd_usuario'  => $data['cd_usuario_resp'] ?: null,
            'dia_inicio'  => $data['dia_inicio_ciclo'] ?: null,
            'id'          => $id,
        ]);
    }

    public function updateResponsavel(int $cdEmpresa, int $cd, array $data): void
    {
        DB::connection('firebird')->statement("
            UPDATE COMPRA_CENTROCUSTO SET
                VL_ORCADO_MES    = :vl_orcado,
                CD_USUARIO_RESP  = :cd_usuario,
                DIA_INICIO_CICLO = :dia_inicio
            WHERE CD_EMPRESA     = :cd_empresa
              AND CD_CENTROCUSTO = :cd
        ", [
            'vl_orcado'  => $data['vl_orcado_mes'] ?: null,
            'cd_usuario' => $data['cd_usuario_resp'] ?: null,
            'dia_inicio' => $data['dia_inicio_ciclo'] ?: null,
            'cd_empresa' => $cdEmpresa,
            'cd'         => $cd,
        ]);
    }

    public function deleteById(int $cdEmpresa, int $cd): void
    {
        DB::connection('firebird')->statement(
            'DELETE FROM COMPRA_CENTROCUSTO WHERE CD_EMPRESA = :cd_empresa AND CD_CENTROCUSTO = :cd',
            ['cd_empresa' => $cdEmpresa, 'cd' => $cd]
        );
    }

    public function getSaldoCiclo(int $cdEmpresa, int $cdCentrocusto): ?object
    {
        $centro = DB::connection('firebird')->selectOne("
            SELECT CD_CENTROCUSTO, DS_CENTROCUSTO, VL_ORCADO_MES, DIA_INICIO_CICLO
            FROM COMPRA_CENTROCUSTO
            WHERE CD_EMPRESA = :cd_empresa AND CD_CENTROCUSTO = :cd
        ", ['cd_empresa' => $cdEmpresa, 'cd' => $cdCentrocusto]);

        if (!$centro || !$centro->VL_ORCADO_MES) {
            return null;
        }

        [$dtInicio, $dtFim] = $this->calcCycleDates((int) ($centro->DIA_INICIO_CICLO ?? 1));

        $row = DB::connection('firebird')->selectOne("
            SELECT COALESCE(SUM(VL_TOTAL), 0) AS VL_UTILIZADO
            FROM COMPRA_SOLICITACAO
            WHERE CD_EMPRESA     = :cd_empresa
              AND CD_CENTROCUSTO = :cd
              AND DT_SOLICITACAO >= :dt_inicio
              AND DT_SOLICITACAO <= :dt_fim
              AND ST_SOLICITACAO NOT IN ('REP', 'CAN')
        ", [
            'cd_empresa' => $cdEmpresa,
            'cd'         => $cdCentrocusto,
            'dt_inicio'  => $dtInicio->format('Y-m-d'),
            'dt_fim'     => $dtFim->format('Y-m-d'),
        ]);

        $vlOrcado    = (float) $centro->VL_ORCADO_MES;
        $vlUtilizado = (float) ($row->VL_UTILIZADO ?? 0);

        return (object) [
            'ds_centrocusto'   => \Helper::ConvertFormatText([$centro])[0]->DS_CENTROCUSTO,
            'vl_orcado'        => $vlOrcado,
            'vl_utilizado'     => $vlUtilizado,
            'vl_saldo'         => $vlOrcado - $vlUtilizado,
            'vl_orcado_fmt'    => number_format($vlOrcado, 2, ',', '.'),
            'vl_utilizado_fmt' => number_format($vlUtilizado, 2, ',', '.'),
            'vl_saldo_fmt'     => number_format(abs($vlOrcado - $vlUtilizado), 2, ',', '.'),
            'dt_inicio'        => $dtInicio->format('d/m/Y'),
            'dt_fim'           => $dtFim->format('d/m/Y'),
        ];
    }

    private function calcCycleDates(int $diaInicio): array
    {
        $hoje = \Carbon\Carbon::today();

        if ($hoje->day >= $diaInicio) {
            $maxDay = \Carbon\Carbon::create($hoje->year, $hoje->month, 1)->daysInMonth;
            $inicio = \Carbon\Carbon::create($hoje->year, $hoje->month, min($diaInicio, $maxDay));
        } else {
            $prev   = $hoje->copy()->subMonthNoOverflow();
            $maxDay = $prev->daysInMonth;
            $inicio = \Carbon\Carbon::create($prev->year, $prev->month, min($diaInicio, $maxDay));
        }

        $fim = $inicio->copy()->addMonthNoOverflow()->subDay();

        return [$inicio, $fim];
    }

    public function getAvailableTypes(int $cdEmpresa)
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT DISTINCT CD_CENTROCUSTO, DS_CENTROCUSTO
            FROM COMPRA_CENTROCUSTO
            WHERE CD_CENTROCUSTO NOT IN (
                SELECT CD_CENTROCUSTO FROM COMPRA_CENTROCUSTO WHERE CD_EMPRESA = :cd_empresa
            )
            ORDER BY CD_CENTROCUSTO
        ", ['cd_empresa' => $cdEmpresa]));
    }

    public function getForSelect(int $cdEmpresa)
    {
        return \Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT CD_CENTROCUSTO, DS_CENTROCUSTO
            FROM COMPRA_CENTROCUSTO
            WHERE CD_EMPRESA = :cd_empresa
            ORDER BY CD_CENTROCUSTO
        ", ['cd_empresa' => $cdEmpresa]));
    }
}
