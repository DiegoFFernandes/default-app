<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WppLidPendente extends Model
{
    protected $table      = 'wpp_lid_pendentes';
    protected $primaryKey = 'lid';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = ['lid', 'pushname', 'ultimo_texto', 'updated_at'];

    protected $casts = ['updated_at' => 'datetime'];
}
