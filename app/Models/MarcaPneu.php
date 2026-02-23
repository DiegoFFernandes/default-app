<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarcaPneu extends Model
{
    use HasFactory;

    protected $table = 'marca_pneus';
    
    public function MarcaAll(){

        $query = "select cd_marca, ds_marca from marca";

        return DB::connection('firebird')->select($query);
        
    }
}
