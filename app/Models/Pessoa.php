<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pessoa extends Model
{
    use HasFactory;

    public function FindPessoaJunsoftAll($search)
    {
        $query = "select first 10 p.cd_pessoa id, 
                    cast(p.nm_pessoa as varchar(100) character set utf8) nm_pessoa, p.nr_cnpjcpf, 
                    p.ds_email, tp.cd_tipopessoa, tp.ds_tipopessoa, ep.nr_celular
                    from pessoa p
                    inner join tipopessoa tp on (tp.cd_tipopessoa = p.cd_tipopessoa)
                    inner join enderecopessoa ep on (ep.cd_pessoa = p.cd_pessoa)
                    where p.st_ativa = 'S'
                        --and p.cd_tipopessoa in (1,3)
                        and p.nm_pessoa like '%$search%'";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
