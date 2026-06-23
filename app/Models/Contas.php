<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Contas
{
    /**
     * Importa um lote de registros ConnectCar em CONTAS + CONTASHISTORICO numa transação.
     *
     * @param  array $rows          Linhas de dadosMescla vindas do front (placa, dataFormatada, valor)
     * @param  array $opcoes        cd_empresa, cd_pessoa, cd_tipoconta, cd_historico, cd_formapagto
     * @return array{importados: int, erros: string[]}
     */
    public static function importarLote(array $rows, array $opcoes): array
    {
        $importados    = 0;
        $erros         = [];
        $idsImportados = [];

        DB::connection('firebird')->transaction(function () use ($rows, $opcoes, &$importados, &$erros, &$idsImportados) {
            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            foreach ($rows as $row) {
                try {
                    $dtLancamento = Carbon::createFromFormat('d/m/Y', $row['dataFormatada'])->format('Y-m-d');
                    $dtVencimento = Carbon::createFromFormat('d/m/Y', $row['dataFormatada'])->addDay()->format('Y-m-d');

                    $nr = self::inserir([
                        'cd_empresa'    => $opcoes['cd_empresa'],
                        'cd_pessoa'     => $opcoes['cd_pessoa'],
                        'cd_tipoconta'  => $opcoes['cd_tipoconta'],
                        'cd_formapagto' => $opcoes['cd_formapagto'],
                        'dt_lancamento' => $dtLancamento,
                        'dt_vencimento' => $dtVencimento,
                        'vl_documento'  => (float) ($row['valor'] ?? 0),
                        'ds_observacao' => 'PLACA: ' . ($row['placa'] ?? '') . ' - CONNECTCAR',
                    ]);

                    ContasHistorico::inserir([
                        'cd_empresa'    => $opcoes['cd_empresa'],
                        'nr_lancamento' => $nr,
                        'cd_pessoa'     => $opcoes['cd_pessoa'],
                        'cd_tipoconta'  => $opcoes['cd_tipoconta'],
                        'cd_historico'  => $opcoes['cd_historico'],
                        'vl_documento'  => (float) ($row['valor'] ?? 0),
                    ]);

                    if (!empty($row['comprovante_id'])) {
                        $idsImportados[] = (int) $row['comprovante_id'];
                    }

                    $importados++;
                } catch (\Exception $e) {
                    $erros[] = ($row['placa'] ?? '?') . ': ' . $e->getMessage();
                }
            }
        });

        return compact('importados', 'erros', 'idsImportados');
    }

    /**
     * Insere um lançamento em CONTAS e retorna o NR_LANCAMENTO gerado.
     *
     * @param array{
     *   cd_empresa:     int,
     *   cd_pessoa:      int,
     *   cd_tipoconta:   int,
     *   cd_formapagto:  string,
     *   dt_lancamento:  string,  // 'YYYY-MM-DD'
     *   dt_vencimento:  string,  // 'YYYY-MM-DD'
     *   vl_documento:   float,
     *   ds_observacao:  string,
     *   cd_usuario:     string,
     * } $data
     */
    public static function inserir(array $data): int
    {
        $db = DB::connection('firebird');

        // Obtém o próximo valor do sequence — será usado em NR_LANCAMENTO e NR_DOCUMENTO
        $row = $db->selectOne('SELECT NEXT VALUE FOR SEQ0001_CONTAS AS NR FROM RDB$DATABASE');
        $nr  = (int) ($row->NR ?? $row->nr);

        $db->insert("
            INSERT INTO CONTAS (
                CD_EMPRESA,
                NR_LANCAMENTO,
                CD_PESSOA,
                CD_TIPOCONTA,
                NR_PARCELA,
                CD_FORMAPAGTO,
                TP_CONTAS,
                TP_DOCUMENTO,
                NR_DOCUMENTO,
                DT_LANCAMENTO,
                DT_VENCIMENTO,
                VL_DOCUMENTO,
                VL_SALDO,
                ST_CONTAS,
                DS_OBSERVACAO,
                DT_REGISTRO,
                CD_EMPRCONTROLE,
                CD_USUARIO
            ) VALUES (
                :cd_empresa,
                :nr_lancamento,
                :cd_pessoa,
                :cd_tipoconta,
                1,
                :cd_formapagto,
                'E',
                'DP',
                :nr_documento,
                CAST(:dt_lancamento AS DATE),
                CAST(:dt_vencimento AS DATE),
                :vl_documento,
                :vl_saldo,
                'T',
                :ds_observacao,
                CURRENT_TIMESTAMP,
                :cd_emprcontrole,
                'admin'
            )
        ", [
            'cd_empresa'     => (int)    $data['cd_empresa'],
            'nr_lancamento'  =>          $nr,
            'cd_pessoa'      => (int)    $data['cd_pessoa'],
            'cd_tipoconta'   => (int)    $data['cd_tipoconta'],
            'cd_formapagto'  => (string) $data['cd_formapagto'],
            'nr_documento'   =>          $nr,
            'dt_lancamento'  => (string) $data['dt_lancamento'],
            'dt_vencimento'  => (string) $data['dt_vencimento'],
            'vl_documento'   => (float)  $data['vl_documento'],
            'vl_saldo'       => (float)  $data['vl_documento'],
            'ds_observacao'  => (string) $data['ds_observacao'],
            'cd_emprcontrole' => (int)    ($data['cd_emprcontrole'] ?? $data['cd_empresa'])
        ]);

        return $nr;
    }
}
