<?php

namespace App\Repositories;

use App\Models\SupervisorComercial;
use Illuminate\Support\Facades\DB;

class SupervisorRepository
{
    public function seachSupervisor($id)
    {

        return DB::connection('mysql')
            ->table('supervisor_comercial')
            ->select(
                'supervisor_comercial.id',
                'users.id as cd_usuario',
                'users.name',
                'supervisor_comercial.cd_supervisorcomercial',
                'supervisor_comercial.ds_supervisorcomercial'
            )
            ->join('users', 'users.id', 'supervisor_comercial.cd_usuario')
            ->where('supervisor_comercial.cd_usuario', $id)
            ->first();
    }
}
