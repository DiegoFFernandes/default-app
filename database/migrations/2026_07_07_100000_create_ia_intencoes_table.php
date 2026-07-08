<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ia_intencoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50)->unique();
            $table->string('descricao', 200);
            $table->boolean('ativo')->default(false);
            $table->longText('sql_template')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        $sqlColetas = <<<'SQL'
SELECT
    P.CD_PESSOA || '-' || P.NM_PESSOA NM_PESSOA,
    SP.ID || '-' || SP.DSSERVICO DS_SERVICOPNEU,
    COUNT(*) QTD,
    CAST(SUM(IPP.VLUNITARIO) / COUNT(IPP.ID) AS DECIMAL(12,2)) VALOR_MEDIO,
    IPP.VLUNITARIO * COUNT(IPP.ID) VL_TOTAL,
    PP.IDVENDEDOR || '-' || V.NM_PESSOA NM_VENDEDOR,
    CAST(PP.DTEMISSAO AS DATE) DT_EMISSAO,
    PP.IDEMPRESA,
    DP.DSDESENHO,
    IPP.IDDESENHOPNEU,
    ITEM.CD_SUBGRUPO
FROM PEDIDOPNEU PP
INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.IDPEDIDOPNEU = PP.ID)
INNER JOIN PNEU ON (PNEU.ID = IPP.IDPNEU)
INNER JOIN PESSOA P ON (P.CD_PESSOA = PP.IDPESSOA)
INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
INNER JOIN ITEM ON (ITEM.CD_ITEM = IPP.IDSERVICOPNEU)
LEFT JOIN DESENHOPNEU DP ON (DP.ID = IPP.IDDESENHOPNEU)
INNER JOIN MEDIDAPNEU MP ON (MP.ID = PNEU.IDMEDIDAPNEU)
INNER JOIN PESSOA V ON (V.CD_PESSOA = PP.IDVENDEDOR)
WHERE
    PP.DTEMISSAO BETWEEN '{{dt_inicio}}' AND '{{dt_fim}}'
    AND PP.IDEMPRESA IN ({{cd_empresa}})
GROUP BY
    P.CD_PESSOA,
    P.NM_PESSOA,
    SP.ID,
    SP.DSSERVICO,
    CAST(PP.DTEMISSAO AS DATE),
    IPP.VLUNITARIO,
    PP.IDVENDEDOR,
    V.NM_PESSOA,
    PP.IDEMPRESA,
    DP.DSDESENHO,
    IPP.IDDESENHOPNEU,
    ITEM.CD_SUBGRUPO
ORDER BY CAST(PP.DTEMISSAO AS DATE) DESC
SQL;

        DB::table('ia_intencoes')->insert([
            [
                'nome'         => 'pedidos_coletados',
                'descricao'    => 'Coletas de pneus por período (todos os serviços)',
                'ativo'        => true,
                'sql_template' => $sqlColetas,
                'updated_at'   => now(),
            ],
            [
                'nome'         => 'pneus_coletados',
                'descricao'    => 'Pneus coletados por período (alias de pedidos_coletados)',
                'ativo'        => true,
                'sql_template' => $sqlColetas,
                'updated_at'   => now(),
            ],
            [
                'nome'         => 'faturamento_mensal',
                'descricao'    => 'Faturamento mensal por empresa',
                'ativo'        => false,
                'sql_template' => null,
                'updated_at'   => now(),
            ],
            [
                'nome'         => 'inadimplencia_mensal',
                'descricao'    => 'Inadimplência mensal (clientes em atraso)',
                'ativo'        => false,
                'sql_template' => null,
                'updated_at'   => now(),
            ],
            [
                'nome'         => 'top_clientes',
                'descricao'    => 'Top clientes por volume ou valor no período',
                'ativo'        => false,
                'sql_template' => null,
                'updated_at'   => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ia_intencoes');
    }
};
