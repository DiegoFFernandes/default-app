<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BloqueioPedido extends Model
{
    use HasFactory;
    protected $connection;

    const STATUS_CLIENTE = [
        'NAO'  => '#f59898',
        'SIM'   => '#90EE90',
    ];
    const STATUS_SCPC = [
        'NAO'  => '#90EE90',
        'SIM'   => '#f59898',
    ];
    const STATUS_PEDIDO = [
        'BLOQUEADO'  => '#f59898',
        'LIBERADO '   => '#90EE90',
        'VERIFICAR' => '#FFFF99'
    ];

    public function BloqueioPedido($empresa = 0, $cd_supervisor = 0, $cd_pessoa = 0)
    {
        $query = "
            SELECT (CASE PP.STPEDIDO
                WHEN 'B' THEN 'BLOQUEADO'
                WHEN 'N' THEN 'LIBERADO'
                ELSE PP.STPEDIDO
                END)
            STPEDIDO,
            PP.IDEMPRESA CD_EMPRESA,
            PP.DTEMISSAO DATA,
            PP.ID AS PEDIDO,
            PP.IDPEDIDOMOVEL AS MOBILE,
            CAST(PP.IDPESSOA || ' - ' || PE.NM_PESSOA AS VARCHAR(200) CHARACTER SET UTF8) CLIENTE,
            --PP.TP_BLOQUEIO AS MOTIVO,
            (
            CASE PP.TP_BLOQUEIO
            WHEN 'F' THEN 'FINANCEIRO'
            WHEN 'C' THEN 'COMERCIAL'
            ELSE 'AMBOS'
            END) MOTIVO,
            EP.CD_REGIAOCOMERCIAL,
            (
            CASE PE.ST_ATIVA
            WHEN 'S' THEN 'SIM'
            WHEN 'N' THEN 'NAO'
            END) ST_ATIVA,
            (
            CASE PE.ST_SCPC
            WHEN 'S' THEN 'SIM'
            WHEN 'N' THEN 'NAO'
            END) ST_SCPC,
            CAST(PP.IDVENDEDOR || ' - ' || PV.NM_PESSOA AS VARCHAR(200) CHARACTER SET UTF8) VENDEDOR,
            SUPERVISOR.NM_PESSOA NM_SUPERVISOR,
            AC.CD_AREACOMERCIAL,
            PP.DSBLOQUEIO,
            TP.DSTIPOPEDIDO
            FROM PEDIDOPNEU PP
            INNER JOIN TIPOPEDIDOPNEU TP ON (TP.ID = PP.IDTIPOPEDIDO)
            INNER JOIN PESSOA PE ON (PE.CD_PESSOA = PP.IDPESSOA)
            LEFT JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = PE.CD_PESSOA)
            INNER JOIN VENDEDOR VE ON (VE.CD_VENDEDOR = PP.IDVENDEDOR)
            INNER JOIN PESSOA PV ON (PV.CD_PESSOA = VE.CD_VENDEDOR)
            LEFT JOIN PESSOA SUPERVISOR ON (SUPERVISOR.CD_PESSOA = VE.CD_VENDEDORGERAL)
            LEFT JOIN REGIAOCOMERCIAL RC ON (RC.CD_VENDEDOR = VE.CD_VENDEDOR
                AND EP.CD_REGIAOCOMERCIAL = RC.CD_REGIAOCOMERCIAL)
            LEFT JOIN AREACOMERCIAL AC ON (AC.CD_AREACOMERCIAL = RC.CD_AREACOMERCIAL)
            WHERE PP.DTEMISSAO BETWEEN CURRENT_DATE - 120 AND CURRENT_DATE
                AND (PP.STPEDIDO = 'B' OR (PP.STPEDIDO = 'N'
                AND PE.ST_SCPC = 'S'))
                " . (($empresa != 0) ? "AND PP.IDEMPRESA IN ($empresa) " : "") . "
                " . (($cd_supervisor != 0) ? "AND VE.CD_VENDEDORGERAL = $cd_supervisor" : "") . "
                " . (($cd_pessoa != 0) ? "AND PP.IDPESSOA IN ($cd_pessoa)" : "") . "
            ORDER BY PP.IDEMPRESA,
                PP.DTEMISSAO";

        $data = DB::connection('firebird')->select($query);
        return Helper::ConvertFormatText($data);

        // $key = "pedidos_bloqueados" . Auth::user()->id;
        // return Cache::remember($key, now()->addMinutes(2), function () use ($query) {
        //     return DB::connection('firebird_rede')->select($query);
        // });

    }
}
