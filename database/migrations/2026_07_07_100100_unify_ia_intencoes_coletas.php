<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ia_intencoes')->where('nome', 'pneus_coletados')->delete();

        DB::table('ia_intencoes')
            ->where('nome', 'pedidos_coletados')
            ->update([
                'descricao'  => 'Coletas de pneus por período (pedidos e pneus coletados)',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Restaura pneus_coletados com o mesmo SQL de pedidos_coletados
        $pedidos = DB::table('ia_intencoes')->where('nome', 'pedidos_coletados')->first();

        DB::table('ia_intencoes')->insert([
            'nome'         => 'pneus_coletados',
            'descricao'    => 'Pneus coletados por período (alias de pedidos_coletados)',
            'ativo'        => $pedidos->ativo ?? true,
            'sql_template' => $pedidos->sql_template ?? null,
            'updated_at'   => now(),
        ]);

        DB::table('ia_intencoes')
            ->where('nome', 'pedidos_coletados')
            ->update(['descricao' => 'Coletas de pneus por período (todos os serviços)']);
    }
};
