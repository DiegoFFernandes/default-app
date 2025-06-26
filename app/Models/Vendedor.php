<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vendedor extends Model
{
    use HasFactory;

    public function FindVendedorJunsoftAll($search)
    {
        $query = "select first 10 p.cd_pessoa id, 
                    cast(p.nm_pessoa as varchar(100) character set utf8) nm_vendedor
                    from vendedor v
                    inner join pessoa p on (v.cd_vendedor = p.cd_pessoa)                    
                    where p.st_ativa = 'S'
                        --and p.cd_tipopessoa in (1,3)
                        and p.nm_pessoa like '%$search%'";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
