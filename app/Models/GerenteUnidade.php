<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GerenteUnidade extends Model
{
    use HasFactory;
    protected $table = 'gerente_unidade';

    protected $fillable = [
        'id',
        'cd_empresa',
        'cd_usuario',
        'cd_gerenteunidade',    
        'ds_gerenteunidade',
        'cd_cadusuario',
        'created_at',
        'updated_at'
    ];

    public function showUserGerente()
    {
        $this->connection = 'mysql';
        return GerenteUnidade::select(
            'gerente_unidade.cd_empresa',
            'gerente_unidade.id',
            'users.id as cd_usuario',
            'users.name',
            'gerente_unidade.cd_gerenteunidade',
            'gerente_unidade.ds_gerenteunidade'
        )
            ->join('users', 'users.id', 'gerente_unidade.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }

    public function verifyIfExists($input)
    {
        $this->connection = 'mysql';
        return GerenteUnidade::where('cd_usuario', $input['cd_usuario'])
            ->where('cd_gerenteunidade', $input['cd_gerenteunidade'])
            ->where('cd_empresa', $input['cd_empresa'])
            ->exists();
    }

    public function storeData($input)
    {
        $this->connection = 'mysql';
        //return $input;
        return GerenteUnidade::insert([
            'cd_usuario' => $input['cd_usuario'],
            'cd_empresa' => $input['cd_empresa'],
            'cd_gerenteunidade' => $input['cd_gerenteunidade'],
            'ds_gerenteunidade' => $input['ds_gerenteunidade'],
            'cd_cadusuario' => $input['cd_cadusuario'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function destroyData($id)
    {
        return GerenteUnidade::find($id)->delete();
    }

    public function updateData($input)
    {
        // return $input->cd_usuario;
        try {
            $this->connection = 'mysql';
            GerenteUnidade::find($input->id)
                ->update([
                    'cd_empresa' => $input->cd_empresa,
                    'cd_usuario' => $input->cd_usuario,
                    'cd_gerenteunidade' => $input->cd_gerenteunidade,
                    'ds_gerenteunidade' => $input->ds_gerenteunidade,
                    'updated_at' => now(),
                ]);
            return response()->json(['success' => 'Gerente atualizado para usúario!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao conectar ao banco de dados.']);
        }
    }
    public function findEmpresaGerenteUnidade($id)
    {
        $this->connection = 'mysql';
        return GerenteUnidade::select('cd_empresa')
            ->where('cd_usuario', $id)
            ->get();
    }
}
