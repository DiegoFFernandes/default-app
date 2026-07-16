<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FluxoCaixaSaldoConta extends Model
{
    protected $table = 'fluxo_caixa_saldo_conta';

    protected $fillable = [
        'cd_conta',
        'ds_conta',
        'updated_by',
    ];
}
