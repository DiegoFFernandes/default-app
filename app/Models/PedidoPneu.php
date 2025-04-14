<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            where pp.id = $data->PEDIDO";

            return DB::connection('firebird')->statement($query);
        });
    }
    

   
}
