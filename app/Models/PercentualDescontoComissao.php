<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentualDescontoComissao extends Model
{
    use HasFactory;

    protected $table = 'percentual_desconto_comercial';

    static function listAllPercDesconto()
    {
        return PercentualDescontoComissao::all();
    }

    public function verifyPercDesconto($input){

        $percentual = $this->where('percentual', $input['percentual'])
            ->where('cd_areacomercial', $input['cd_areacomercial'])
            ->where('cd_regiaocomercial', $input['cd_regiaocomercial'])
            ->first();

        return $percentual;
    }
}
