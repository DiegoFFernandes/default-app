<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use HasFactory;

    public static function buildCaseNome(string $campo): string
    {
        $apelidos = config('empresas.apelidos');
        $padrao   = config('empresas.apelido_padrao', 'OUTROS');
        $case = 'CASE';
        foreach ($apelidos as $id => $nome) {
            $case .= " WHEN {$campo} = {$id} THEN '{$nome}'";
        }
        $case .= " ELSE '{$padrao}' END";
        return $case;
    }

    public function empresa($empresa = 0)
    {
        if ($empresa == 0) {
            $empresa = implode(',', config('empresas.admin_ids'));
        }

        $caseNome = self::buildCaseNome('EMPRESA.CD_EMPRESA');

        $query = "SELECT
                    EMPRESA.CD_EMPRESA,
                    {$caseNome} NM_EMPRESA,
                    EMPRESA.CD_PESSOA
                FROM EMPRESA
                WHERE EMPRESA.CD_EMPRESA IN ($empresa)";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
