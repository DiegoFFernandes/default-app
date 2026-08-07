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

    public function FindPessoaJunsoftAll($search, $cd_tipopessoa = null)
    {
        $query = "
            SELECT FIRST 10
                P.CD_PESSOA ID,
                P.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                P.NR_CNPJCPF,
                P.DS_EMAIL,
                TP.CD_TIPOPESSOA,
                TP.DS_TIPOPESSOA,
                EP.NR_CELULAR,
                EP.CD_VENDEDOR
            FROM PESSOA P
            INNER JOIN TIPOPESSOA TP ON (TP.CD_TIPOPESSOA = P.CD_TIPOPESSOA)
            INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            WHERE
                P.ST_ATIVA = 'S'   
        ";

        if (isset($cd_tipopessoa)) {
            $query .= " and p.cd_tipopessoa in ($cd_tipopessoa) ";
        }

        $query .= " and p.nm_pessoa||'-'||p.cd_pessoa like '%$search%'";


        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    public function FindPessoaJunsoftId($cd_pessoa, $cd_tabpreco = 1, $nr_cnpjcpf = null)
    {
        // Quando $nr_cnpjcpf é informado, busca pelo CNPJ/CPF (usado para checar se já
        // existe antes de cadastrar). Caso contrário, busca pelo CD_PESSOA.
        $filtro = $nr_cnpjcpf !== null
            ? "P.NR_CNPJCPF = :cnpj"
            : "P.CD_PESSOA = :cd_pessoa";

        $bind = $nr_cnpjcpf !== null
            ? ['cnpj' => $nr_cnpjcpf]
            : ['cd_pessoa' => $cd_pessoa];

        $query = "
            SELECT
                P.CD_PESSOA CD_PESSOA,
                P.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
                P.NM_PESSOA PESSOA,
                P.NR_CNPJCPF,
                P.DS_EMAIL,
                TP.CD_TIPOPESSOA,
                TP.DS_TIPOPESSOA,
                EP.NR_CELULAR,
                EP.CD_VENDEDOR,
                COALESCE(PT.CD_TABPRECO, $cd_tabpreco) CD_TABPRECO
            FROM PESSOA P
            INNER JOIN TIPOPESSOA TP ON (TP.CD_TIPOPESSOA = P.CD_TIPOPESSOA)
            INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA)
            LEFT JOIN PARMTABPRECO PT ON (PT.CD_PESSOA = P.CD_PESSOA)
            WHERE
                P.ST_ATIVA = 'S'
                AND $filtro";
        $data = DB::connection('firebird')->select($query, $bind);

        $data  = Helper::ConvertFormatText($data);

        return $data[0] ?? null;
    }

    /**
     * Insere um fornecedor/cliente na PESSOA do ERP (Firebird) e devolve o novo CD_PESSOA.
     * Campos fixos: ST_ATIVA='S', ST_REVENDAIPI='N', datas = CURRENT_TIMESTAMP.
     */
    public function insertPessoa(array $data): int
    {
        $db = DB::connection('firebird');

        $cdPessoa = (int) $db->selectOne("SELECT NEXT VALUE FOR SEQ_PESSOA AS N FROM RDB\$DATABASE")->N;

        $db->statement("
            INSERT INTO PESSOA (
                CD_PESSOA, NM_PESSOA, NR_CNPJCPF, CD_TIPOPESSOA,
                ST_ATIVA, ST_REVENDAIPI, DT_REGISTRO, DT_LIBERACAO
            ) VALUES (
                :cd, :nm, :cnpj, :tipo,
                'S', 'N', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ", [
            'cd'    => $cdPessoa,
            'nm'    => Helper::ToIso(mb_substr(mb_strtoupper(trim($data['nm_pessoa']), 'UTF-8'), 0, 60, 'UTF-8')),
            'cnpj'  => $data['nr_cnpjcpf'],
            'tipo'  => $data['cd_tipopessoa'],
        ]);

        return $cdPessoa;
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
