<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Helper;

class PedidoPneu extends Model
{
    use HasFactory;
    protected $table = 'PEDIDOPNEU';

    public function verifyIfExists($pedido)
    {
        $query = "select first 1 pp.id from pedidopneu pp where pp.id = $pedido";
        $data = DB::connection('firebird')->select($query);

        return empty($data) ? 0 : 1;
    }
    public function updateData($data, $stpedido, $tpbloqueio)
    {

        return DB::transaction(function () use ($data, $stpedido, $tpbloqueio) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "update pedidopneu pp
            set pp.dsliberacao = '$data->DSLIBERACAO'
            " . (($stpedido == "N") ? ", pp.stpedido = 'N' " : "") . " 
            " . (($tpbloqueio == "F") ? ", pp.tp_bloqueio = 'F' " : "") . " 
            " . (($tpbloqueio == "C") ? ", pp.tp_bloqueio = 'C' " : "") . " 
            where pp.id = $data->PEDIDO";

            return DB::connection('firebird')->statement($query);
        });
    }

    static function updateDescontoMaior20($pedido)
    {
        if (empty($pedido)) {
            return;
        }

        return DB::transaction(function () use ($pedido) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "
                UPDATE PEDIDOPNEU PP
                    SET PP.ST_COMERCIAL = 'G'
                WHERE PP.ID in ($pedido)";

            return DB::connection('firebird')->statement($query);
        });
    }

    static function getPedidoPneu($dt_inicio = null, $dt_fim = null, $cd_empresa = null)
    {

        $query = "
            SELECT
                MP.DSMEDIDAPNEU,
                COUNT(*) QTD,
                CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO
            FROM PEDIDOPNEU PP
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = PP.ID)
            INNER JOIN PNEU ON (PNEU.ID = IPP.IDPNEU)
            INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
            INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
            INNER JOIN MEDIDAPNEU MP ON (MP.ID = PNEU.IDMEDIDAPNEU)
            WHERE
                PP.DTEMISSAO BETWEEN '$dt_inicio' AND '$dt_fim'
                AND PP.IDEMPRESA = $cd_empresa
            GROUP BY MP.DSMEDIDAPNEU";
        $dados = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($dados);
    }
}
