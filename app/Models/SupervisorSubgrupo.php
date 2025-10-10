<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorSubgrupo extends Model
{
    use HasFactory;

    protected $table = 'supervisor_subgrupo';

    public function subgrupoSupervisor($cd_user)
    {
        $this->connection = 'mysql';
        return SupervisorSubgrupo::select('cd_subgrupo as CD_SUBGRUPO')
            ->where('cd_user_supervisor', $cd_user)
            ->get();
    }
    
}


