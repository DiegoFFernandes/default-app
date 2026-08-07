<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EnderecoPessoa extends Model
{
    use HasFactory;

    /**
     * Insere o primeiro endereço (CD_ENDERECO = 1) de uma pessoa.
     * Campos fixos: TP_ENDERECO='C', CD_CONDFISCAL=1, ST_CONTRIBUINTE='9', ST_CONSUMIDOR=1.
     */
    public function store(array $data): void
    {
        DB::connection('firebird')->statement("
            INSERT INTO ENDERECOPESSOA (
                CD_PESSOA, CD_ENDERECO, DS_ENDERECO, NR_ENDERECO, CD_MUNICIPIO,
                NR_CEP, DS_BAIRRO, TP_ENDERECO, NR_FONE, NR_CELULAR,
                CD_CONDFISCAL, DT_REGISTRO, ST_CONTRIBUINTE, ST_CONSUMIDOR
            ) VALUES (
                :cd_pessoa, 1, :ds_endereco, :nr_endereco, :cd_municipio,
                :nr_cep, :ds_bairro, 'C', :nr_fone, :nr_celular,
                1, CURRENT_TIMESTAMP, '9', 1
            )
        ", [
            'cd_pessoa'    => $data['cd_pessoa'],
            'ds_endereco'  => \Helper::ToIso(mb_substr((string) ($data['ds_endereco'] ?? ''), 0, 60, 'UTF-8')),
            'nr_endereco'  => is_numeric($data['nr_endereco'] ?? null) ? (int) $data['nr_endereco'] : null,
            'cd_municipio' => $data['cd_municipio'] ?? null,
            'nr_cep'       => $data['nr_cep'] ?? null,
            'ds_bairro'    => \Helper::ToIso(mb_substr((string) ($data['ds_bairro'] ?? ''), 0, 60, 'UTF-8')),
            'nr_fone'      => $data['nr_fone'] ?: null,
            'nr_celular'   => $data['nr_celular'] ?: null,
        ]);
    }
}
