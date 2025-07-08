<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegiaoComercial extends Model
{
    use HasFactory;

    protected $fillable = [
        'cd_usuario',
        'cd_regiaocomercial',
        'cd_cadusuario',
        'ds_regiaocomercial'
    ];
    protected $table = 'regiao_comercial';
    protected $connection;
    public $timestamps = true;

    public function __construct()
    {
        $this->connection = 'mysql';
    }
    
    public function regiaoAll()
    {
        $query = "select 
                    rc.cd_regiaocomercial, 
                    rc.ds_regiaocomercial, 
                    ac.cd_areacomercial, 
                    ac.ds_areacomercial                   
                from regiaocomercial rc
                left join areacomercial ac on (ac.cd_areacomercial = rc.cd_areacomercial)
                order by ds_regiaocomercial";

        $results = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($results);        
    }
    public function regiaoArea($cd_areacomercial)
    {

        $query = "select rc.cd_regiaocomercial, 
        cast(rc.ds_regiaocomercial as varchar(40) character set utf8) ds_regiaocomercial, 
        ac.cd_areacomercial, cast(ac.ds_areacomercial as varchar(40) character set utf8) ds_areacomercial
        from regiaocomercial rc
        inner join areacomercial ac on (ac.cd_areacomercial = rc.cd_areacomercial)
        where ac.cd_areacomercial in ($cd_areacomercial)
        order by cd_regiaocomercial";

        return DB::connection('firebird')->select($query);

        // $key = "regiao_comercial__" . Auth::user()->id;
        // return Cache::remember($key, now()->addMinutes(60), function () use ($query) {
        //     return DB::connection($this->setConnet())->select($query);
        // });
    }
    public function storeData($input)
    {
        $this->connection = 'mysql';
        //return $input;
        return RegiaoComercial::insert([
            'cd_usuario' => $input['cd_usuario'],
            'cd_regiaocomercial' => $input['cd_regiaocomercial'],
            'ds_regiaocomercial' => $input['ds_regiaocomercial'],
            'cd_cadusuario' => $input['cd_cadusuario'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function verifyIfExists($input)
    {
        $this->connection = 'mysql';
        return RegiaoComercial::where('cd_usuario', $input['cd_usuario'])->where('cd_regiaocomercial', $input['cd_regiaocomercial'])->exists();
    }
    public function showUserRegiao()
    {
        $this->connection = 'mysql';
        return RegiaoComercial::select('regiao_comercial.id', 'users.id as cd_usuario', 'users.name', 'regiao_comercial.cd_regiaocomercial', 'regiao_comercial.ds_regiaocomercial')
            ->join('users', 'users.id', 'regiao_comercial.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }
    public function regiaoPorUsuario($cd_usuario)
    {
        $this->connection = 'mysql';
        return RegiaoComercial::select('regiao_comercial.cd_regiaocomercial')
            ->join('users', 'users.id', 'regiao_comercial.cd_usuario')
            ->where('users.id', $cd_usuario)
            ->get();
    }
    public function updateData($input)
    {
        RegiaoComercial::find($input->id)
            ->update([
                'cd_usuario' => $input->cd_usuario,
                'cd_regiaocomercial' => $input->cd_regiaocomercial,
                'ds_regiaocomercial' => $input->ds_regiaocomercial,
                'updated_at' => now(),
            ]);
        return response()->json(['success' => 'Região atualizada para usúario!']);
    }
    public function destroyData($id)
    {
        return RegiaoComercial::find($id)->delete();
    }

    public function findRegiaoUser($cd_usuario)
    {
        $this->connection = 'mysql';
        return RegiaoComercial::select('cd_regiaocomercial as CD_REGIAOCOMERCIAL', 'ds_regiaocomercial as DS_REGIAOCOMERCIAL')->where('cd_usuario', $cd_usuario)->get();
    }
    public function RegiaoUsuarioAll(){
        $this->connection = 'mysql';
        return RegiaoComercial::select('regiao_comercial.cd_regiaocomercial', DB::raw("substring_index(UPPER(users.name), ' ', 1) as name"))
            ->join('users', 'users.id', 'regiao_comercial.cd_usuario')           
            ->get();
    }

    

}
