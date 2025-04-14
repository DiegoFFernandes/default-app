<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AreaComercial extends Model
{
    use HasFactory;
    protected $table = 'area_comercial';
    protected $connection;
    protected $fillable = [
        'cd_usuario',
        'cd_areacomercial',
        'ds_areacomercial',
        'cd_cadusuario'
    ];

    public function __construct()
    {
        $this->connection = 'mysql';
    }
   
    public function areaAll()
    {
        $query = "select ac.cd_areacomercial, ac.ds_areacomercial
        from areacomercial ac";
        $key = "area_comercial_" . Auth::user()->id;       

        return Cache::remember($key, now()->addMinutes(60), function () use ($query) {

            $results = DB::connection("firebird")->select($query);

            return $results = Helper::ConvertFormatText($results);

        });
    }
    public function showUserArea()
    {
        $this->connection = 'mysql';
        return AreaComercial::select('area_comercial.id', 'users.id as cd_usuario', 'users.name', 'area_comercial.cd_areacomercial', 'area_comercial.ds_areacomercial')
            ->join('users', 'users.id', 'area_comercial.cd_usuario')
            // ->whereIn('users.empresa', $cd_empresa)
            ->orderBy('users.name')
            ->get();
    }
    public function verifyIfExists($input)
    {
        $this->connection = 'mysql';
        return AreaComercial::where('cd_usuario', $input['cd_usuario'])
            ->where('cd_areacomercial', $input['cd_areacomercial'])
            ->exists();
    }
    public function verifyIfExistsArea($cd_areacomercial, $cd_empresa)
    {
        $this->connection = 'mysql';
        return AreaComercial::join('users', 'users.id', 'area_comercial.cd_usuario')
            ->where('cd_areacomercial', $cd_areacomercial)
            ->whereIn('users.empresa', $cd_empresa)
            ->exists();
    }
    public function verifyIfExistsUser($cd_usuario, $cd_empresa)
    {
        $this->connection = 'mysql';
        return AreaComercial::join('users', 'users.id', 'area_comercial.cd_usuario')
            ->where('area_comercial.cd_usuario', $cd_usuario)
            ->whereIn('users.empresa', $cd_empresa)
            ->exists();
    }
    public function storeData($input)
    {
        $this->connection = 'mysql';
        //return $input;
        return AreaComercial::insert([
            'cd_usuario' => $input['cd_usuario'],
            'cd_areacomercial' => $input['cd_areacomercial'],
            'ds_areacomercial' => $input['ds_areacomercial'],
            'cd_cadusuario' => $input['cd_cadusuario'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function updateData($input)
    {
        AreaComercial::find($input->id)
            ->update([
                'cd_usuario' => $input->cd_usuario,
                'cd_areacomercial' => $input->cd_areacomercial,
                'ds_areacomercial' => $input->ds_areacomercial,
                'updated_at' => now(),
            ]);
        return response()->json(['success' => 'Região atualizada para usúario!']);
    }
    public function destroyData($id)
    {
        return AreaComercial::find($id)->delete();
    }
    public function findAreaUser($cd_usuario)
    {
        $this->connection = 'mysql';
        return AreaComercial::select('cd_areacomercial')->where('cd_usuario', $cd_usuario)->get();
    }

    public function ImportaVendedor()
    {
        return $this->connection = 'firebird';

        $contas = ['1007', '1040', '1043', '1044', '1045', '10746', '10745', '10716', '10717', '10718', '10719', '10722', '10723', '10724', '10726', '10728', '10729', '10730', '10731', '10732', '10733', '10734', '10735', '10738', '10739', '10740', '10137', '2468', '2470', '2471', '1406', '1408', '1414', '1415', '1416', '1417', '1421', '1422', '1423', '1424', '1425', '1426', '1427', '1428', '1429', '1430', '1431', '1432', '1436', '1437', '1438', '1439', '1445', '1446', '1447', '1448', '1449', '1452', '1454', '1455', '1457', '1460', '1461', '94', '97', '98', '99', '1400', '1401', '1403', '1404', '1405'];
        $array = ['11001', '11002', '11003', '11012', '11015', '11019', '11032', '11035', '20000', '20001', '20002', '20003', '30001', '60001', '7', 'TI03', 'TI10'];

        foreach ($contas as $c) {
            foreach ($array as $a) {
                echo $query = "
                    UPDATE OR INSERT INTO USUARIOCONTASALDO (CD_EMPRESA,CD_USUARIO,CD_EMPRCONTA,CD_CONTA,DT_REGISTRO) VALUES ('208','$a','208','$c','07/09/2023 00:00');                    
            ";
                DB::connection($this->connection)->select($query);
                echo "Finalizado:" . $a . "Conta: " .  $c .  "</br>";
            }
        }
        return true;
    }
}
