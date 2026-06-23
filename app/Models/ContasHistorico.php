<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ContasHistorico
{
    /**
     * Insere o histórico de um lançamento em CONTASHISTORICO.
     *
     * @param array{
     *   cd_empresa:    int,
     *   nr_lancamento: int,
     *   cd_pessoa:     int,
     *   cd_tipoconta:  int,
     *   cd_historico:  int,
     *   vl_documento:  float,
     * } $data
     */
    public static function inserir(array $data): void
    {
        DB::connection('firebird')->insert("
            INSERT INTO CONTASHISTORICO (
                CD_EMPRESA,
                NR_LANCAMENTO,
                CD_PESSOA,
                CD_TIPOCONTA,
                NR_PARCELA,
                CD_HISTORICO,
                VL_DOCUMENTO,
                DT_REGISTRO,
                ST_HISTORICOREPARC
            ) VALUES (
                :cd_empresa,
                :nr_lancamento,
                :cd_pessoa,
                :cd_tipoconta,
                1,
                :cd_historico,
                :vl_documento,
                CURRENT_TIMESTAMP,
                'N'
            )
        ", [
            'cd_empresa'    => (int)   $data['cd_empresa'],
            'nr_lancamento' => (int)   $data['nr_lancamento'],
            'cd_pessoa'     => (int)   $data['cd_pessoa'],
            'cd_tipoconta'  => (int)   $data['cd_tipoconta'],
            'cd_historico'  => (int)   $data['cd_historico'],
            'vl_documento'  => (float) $data['vl_documento'],
        ]);
    }
}
