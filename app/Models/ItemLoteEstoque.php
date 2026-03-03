<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemLoteEstoque extends Model
{
    use HasFactory;

    protected $table = 'item_lote_estoque';

    protected $fillable = [
        'cd_lote',
        'cd_item',
        'peso',
        'cd_usuario',
        'created_at',
        'updated_at'
    ];

    protected function serializeDate($date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function list($id)
    {
        return ItemLoteEstoque::select(
            'item_lote_estoque.id',
            'item_lote_estoque.cd_item',
            'item.ds_item',
            'item_lote_estoque.peso',
            'item.ps_liquido',
            'users.name',
            'item_lote_estoque.created_at'
        )
            ->join('item', 'item.cd_item', 'item_lote_estoque.cd_item')
            ->join('users', 'users.id', 'item_lote_estoque.cd_usuario')
            ->where('cd_lote', $id)
            ->get();
    }
    public function listGroup($id)
    {
        return ItemLoteEstoque::select(
            'item_lote_estoque.cd_item',
            'item.ds_item',
            DB::raw('count(*) qtditem, ROUND(sum(item_lote_estoque.peso),2) peso')
        )
            ->join('item', 'item.cd_item', 'item_lote_estoque.cd_item')
            ->where('cd_lote', $id)
            ->groupBy('item_lote_estoque.cd_item', 'item.ds_item')
            ->get();
    }
    public function store($input)
    {
        try {
            ItemLoteEstoque::create([
                'cd_lote' => $input['cd_lote'],
                'cd_item' => $input['cd_item'],
                'peso' => $input['peso'],
                'cd_usuario' => $input['cd_usuario']
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return ['success' => false, 'errors' => $ex->getMessage()];
        }
        return ['success' => true];
    }
    public function destroyData($id)
    {
        ItemLoteEstoque::find($id)->delete();
        return response()->json(['success' => 'Item excluido com sucesso!']);
    }
    public function countData($cd_lote)
    {
        return ItemLoteEstoque::select(DB::raw('sum(peso) as peso, count(*) as qtd'))
            ->where('cd_lote', $cd_lote)->get();
    }
}
