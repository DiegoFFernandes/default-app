<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SupervisorComercial extends Model
{
    use HasFactory;

    protected $table = 'supervisor_comercial';

    public function SupervisorAll()
    {
        $query = "
                SELECT
                    CD_VENDEDORGERAL,
                    V.CD_VENDEDORGERAL || ' - ' || P.NM_PESSOA NM_SUPERVISOR
                FROM VENDEDOR V
                INNER JOIN PESSOA P ON (P.CD_PESSOA = V.CD_VENDEDORGERAL)
                GROUP BY CD_VENDEDORGERAL, P.NM_PESSOA";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
    public function storeData($input)
    {
        $this->connection = 'mysql';
        //return $input;
        return SupervisorComercial::insert([
            'cd_usuario' => $input['cd_usuario'],
            'cd_supervisorcomercial' => $input['cd_supervisorcomercial'],
            'ds_supervisorcomercial' => $input['ds_supervisorcomercial'],
            'cd_cadusuario' => $input['cd_cadusuario'],
            'pc_permitida' => $input['pc_permitida'] ?? 0,
            'libera_acima_param' => $input['libera_acima_param'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function verifyIfExists($input)
    {
        $this->connection = 'mysql';
        return SupervisorComercial::where('cd_usuario', $input['cd_usuario'])->where('cd_supervisorcomercial', $input['cd_supervisorcomercial'])->exists();
    }
    public function showUserSupervisor()
    {
        $this->connection = 'mysql';
        return SupervisorComercial::select(
            'supervisor_comercial.id',
            'users.id as cd_usuario',
            'users.name',
            'supervisor_comercial.cd_supervisorcomercial',
            'supervisor_comercial.ds_supervisorcomercial'
        )
            ->join('users', 'users.id', 'supervisor_comercial.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }
    public function destroyData($id)
    {
        return SupervisorComercial::find($id)->delete();
    }
    public function seachSupervisor($id)
    {
        $this->connection = 'mysql';

        return SupervisorComercial::select(
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
    public function findSupervisorUser($cd_usuario)
    {
        $this->connection = 'mysql';
        return SupervisorComercial::select(
            'cd_supervisorcomercial as CD_SUPERVISORCOMERCIAL',
            'ds_supervisorcomercial as DS_SUPERVISORCOMERCIAL',
            'pc_permitida as PC_PERMITIDA',
            'libera_acima_param as ST_PARAM'
        )
            ->where('cd_usuario', $cd_usuario)->get();
    }
}
