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

        DB::beginTransaction();
        try {
            //return $input;
            SupervisorComercial::insert([
                'cd_usuario' => $input['cd_usuario'],
                'cd_supervisorcomercial' => $input['cd_supervisorcomercial'],
                'ds_supervisorcomercial' => $input['ds_supervisorcomercial'],
                'cd_cadusuario' => $input['cd_cadusuario'],
                'pc_permitida' => $input['pc_permitida'] ?? 0,
                'libera_acima_param' => $input['libera_acima_param'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($input['subgrupos'])) {
                foreach ($input['subgrupos'] as $subgrupo) {
                    SupervisorSubgrupo::updateOrInsert(
                        [
                            'cd_user_supervisor' => $input['cd_usuario'],
                            'cd_subgrupo' => $subgrupo, // Isso será usado como critério de busca
                        ],
                        [
                            'cd_user_supervisor' => $input['cd_usuario'],
                            'cd_subgrupo' => $subgrupo,
                            'created_at' => now(),
                            'updated_at' => now(), // Atualizar a data de modificação
                        ]
                    );
                }
            }

            // Commit da transação
            DB::commit();

            return response()->json(['message' => 'Supervisor Comercial salvo com sucesso!'], 200);
        } catch (\Exception $e) {
            // Rollback caso ocorra algum erro
            DB::rollBack();

            return response()->json(['error' => 'Erro interno. Tente novamente mais tarde.'], 500);
        }
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
            'supervisor_comercial.ds_supervisorcomercial',
            'supervisor_comercial.pc_permitida',
            'supervisor_comercial.libera_acima_param',
        )
            ->join('users', 'users.id', 'supervisor_comercial.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }
    public function destroyData($supervisor)
    {
        SupervisorSubgrupo::where('cd_user_supervisor', $supervisor->cd_usuario)->delete();
        return SupervisorComercial::find($supervisor->id)->delete();
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
    public function subgrupoSupervisor($cd_supervisor)
    {
        $this->connection = 'mysql';
        return SupervisorComercial::select('cd_subgrupo as CD_SUBGRUPO')
            ->where('cd_supervisorcomercial', $cd_supervisor)
            ->get();
    }

    // No modelo SupervisorComercial:
    public function subgrupos()
    {
        return $this->hasMany(SupervisorSubgrupo::class, 'cd_user_supervisor', 'cd_usuario');
        // Ou se for uma relação de muitos para muitos:
        // return $this->belongsToMany(Subgrupo::class, 'supervisor_subgrupo', 'cd_user_supervisor', 'cd_subgrupo');
    }

    public function updateData($input)
    {
        $this->connection = 'mysql';

        DB::beginTransaction();
        try {
            //return $input;
            SupervisorComercial::where('id', $input['id'])->update([
                'cd_supervisorcomercial' => $input['cd_supervisorcomercial'],
                'ds_supervisorcomercial' => $input['ds_supervisorcomercial'],
                'pc_permitida' => $input['pc_permitida'] ?? 0,
                'libera_acima_param' => $input['libera_acima_param'],
                'updated_at' => now(),
            ]);

            if (!empty($input['subgrupos'])) {
                // Primeiro, removemos os subgrupos que não estão mais na lista
                SupervisorSubgrupo::where('cd_user_supervisor', $input['cd_usuario'])
                    ->whereNotIn('cd_subgrupo', $input['subgrupos'])
                    ->delete();

                // Agora, adicionamos ou atualizamos os subgrupos fornecidos
                foreach ($input['subgrupos'] as $subgrupo) {
                    SupervisorSubgrupo::updateOrInsert(
                        [
                            'cd_user_supervisor' => $input['cd_usuario'],
                            'cd_subgrupo' => $subgrupo, // Isso será usado como critério de busca
                        ],
                        [
                            'cd_user_supervisor' => $input['cd_usuario'],
                            'cd_subgrupo' => $subgrupo,
                            'created_at' => now(),
                            'updated_at' => now(), // Atualizar a data de modificação
                        ]
                    );
                }
            } else {
                // Se nenhum subgrupo for fornecido, removemos todos os subgrupos associados ao supervisor
                SupervisorSubgrupo::where('cd_user_supervisor', $input['cd_usuario'])->delete();
            }

            // Commit da transação
            DB::commit();

            return response()->json(['message' => 'Supervisor Comercial atualizado com sucesso!'], 200);
        } catch (\Exception $e) {
            // Rollback caso ocorra algum erro
            DB::rollBack();

            return $e->getMessage();

            return response()->json(['error' => 'Erro interno. Tente novamente mais tarde.'], 500);
        }
    }
}
