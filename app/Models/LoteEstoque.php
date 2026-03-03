<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoteEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'cd_empresa',
        'descricao',
        'cd_usuario',
        'status',
        'tp_lote',
        'tp_produto',
        'id_marca_lote',
        'created_at',
        'updated_at'
    ];


    public $table = 'lote_estoque';

    protected $connection;

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function getCreatedAtFormatadoAttribute()
    {
        return $this->created_at?->format('d/m/Y H:i:s');
    }

    public function lotesAll()
    {
        return LoteEstoque::select(
            'lote_estoque.id',
            'lote_estoque.cd_empresa',
            'lote_estoque.descricao',
            DB::raw('count(i.id) as qtd_itens'),
            DB::raw('coalesce(sum(i.peso),0) as ps_liquido_total'),
            'lote_estoque.status',
            DB::raw('case lote_estoque.tp_lote when "I" then "Inventario" when "E" then "Entrada" when "T" then "Transferencia" end as tp_lote'),
            DB::raw('case lote_estoque.tp_produto when 1 then "Banda" when 2 then "Carcaça" end as tp_produto'),
            'lote_estoque.id_marca_lote',
            'm.ds_marca_lote as ds_marca',
            'lote_estoque.cd_usuario',
            'lote_estoque.created_at',
            'lote_estoque.updated_at'
        )

            ->leftJoin('item_lote_estoque as i', 'i.cd_lote', 'lote_estoque.id')
            ->leftJoin('marca_lote_estoque as m', 'm.id', 'lote_estoque.id_marca_lote')
            // ->leftJoin('sub_grupos as s', 's.id', 'lote_estoque.id_subgrupo')
            ->where('cd_empresa', Auth::user()->empresa)
            ->groupBy(
                'lote_estoque.id',
                'lote_estoque.cd_empresa',
                'lote_estoque.descricao',
                'lote_estoque.status',
                'lote_estoque.tp_lote',
                'lote_estoque.tp_produto',
                'lote_estoque.id_marca_lote',
                'lote_estoque.cd_usuario',
                'lote_estoque.created_at',
                'lote_estoque.updated_at'
            )
            ->get();
    }
    public function storeData($input)
    {
        LoteEstoque::create([
            'cd_empresa' => Auth::user()->empresa,
            'descricao' => $input['ds_lote'],
            'cd_usuario' => $input['cd_usuario'],
            'status' => $input['status'],
            'tp_lote' => $input['tp_lote'],
            'tp_produto' => $input['tp_produto'],
            'id_marca_lote' => $input['cd_marca']
        ]);
    }
    public function findLote($id)
    {
        return LoteEstoque::select(
            'lote_estoque.id',
            'lote_estoque.cd_empresa',
            'lote_estoque.descricao',
            'lote_estoque.id_marca_lote as id_marca',
            'lote_estoque.cd_usuario',
            'lote_estoque.tp_produto',
            'users.name as nm_usuario',
            'lote_estoque.created_at',  
            'lote_estoque.updated_at',
            DB::raw('case lote_estoque.tp_lote when "I" then "Inventario" when "E" then "Entrada" when "T" then "Transferencia" end as tp_lote')
        )
            ->leftJoin('users', 'users.id', 'lote_estoque.cd_usuario')
            ->findOrFail($id);
    }
    public function updateData($data, $qtd_item)
    {
        LoteEstoque::where('id', $data->id)
            ->update(['status' => 'F', 'ps_liquido_total' => $qtd_item[0]['peso'], 'qtd_itens' => $qtd_item[0]['qtd']]);

        return response()->json(['success' => 'Lote finalizado com sucesso!']);
    }
    public function deleteData($id)
    {
        try {
            LoteEstoque::find($id)->delete();
        } catch (\Throwable $th) {
            return response()->json(['error' => "Esse lote não pode ser excluido a item nele!"]);
        }
        return response()->json(['success' => 'Lote excluido com sucesso!']);
    }
}
