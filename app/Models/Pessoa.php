<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pessoa extends Model
{
    use HasFactory;

    protected $table = 'pessoas';
    protected $fillable = [
        'cd_usuario',
        'cd_pessoa',
        'nm_pessoa',
        'cd_cadusuario',
        'created_at',
        'updated_at',
    ];

    public function FindPessoaJunsoftAll($search)
    {
        $query = "select first 10 p.cd_pessoa id, 
                    p.cd_pessoa||'-'||p.nm_pessoa nm_pessoa, p.nr_cnpjcpf, 
                    p.ds_email, tp.cd_tipopessoa, tp.ds_tipopessoa, ep.nr_celular
                    from pessoa p
                    inner join tipopessoa tp on (tp.cd_tipopessoa = p.cd_tipopessoa)
                    inner join enderecopessoa ep on (ep.cd_pessoa = p.cd_pessoa)
                    where p.st_ativa = 'S'
                        --and p.cd_tipopessoa in (1,3)
                        and p.nm_pessoa||'-'||p.cd_pessoa like '%$search%'";
        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function storeData($input)
    {
        $this->connection = 'mysql';
        //return $input;
        return Pessoa::insert([
            'cd_usuario' => $input['cd_usuario'],
            'cd_pessoa' => $input['cd_pessoa'],
            'nm_pessoa' => $input['nm_pessoa'],
            'cd_cadusuario' => $input['cd_cadusuario'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function verifyIfExists($input)
    {
        $this->connection = 'mysql';
        return Pessoa::where('cd_usuario', $input['cd_usuario'])->where('cd_pessoa', $input['cd_pessoa'])->exists();
    }
    public function showUserPessoa()
    {
        $this->connection = 'mysql';
        return Pessoa::select(
            'pessoas.id',
            'users.id as cd_usuario',
            'users.name',
            'pessoas.cd_pessoa',
            'pessoas.nm_pessoa',
        )
            ->join('users', 'users.id', 'pessoas.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }
    public function destroyData($id)
    {
        return Pessoa::find($id)->delete();
    }
    public function seachSupervisor($id)
    {
        $this->connection = 'mysql';

        return Pessoa::select(
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
    public function findPessoaUser($cd_usuario)
    {
        $this->connection = 'mysql';
        return Pessoa::select(
            'cd_pessoa',
            'nm_pessoa'
        )
        ->where('cd_usuario', $cd_usuario)->get();
    }
    public function updateData($input)
    {
        // return $input;
        try {
            $this->connection = 'mysql';
            Pessoa::find($input->id)
                ->update([
                    'cd_usuario' => $input->cd_usuario,
                    'cd_pessoa' => $input->cd_pessoa,
                    'nm_pessoa' => $input->nm_pessoa,
                    'updated_at' => now(),
                ]);
            return response()->json(['success' => 'Pessoa atualizada para usúario!']);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao conectar ao banco de dados.']);
        }
    }
}
