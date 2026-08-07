<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CnpjService
{
    private string $baseUrl = 'https://brasilapi.com.br/api/cnpj/v1';

    /**
     * Consulta um CNPJ na BrasilAPI e devolve os campos já mapeados para o
     * cadastro de fornecedor. Retorna null quando não encontra ou falha.
     *
     * @return array{nr_cnpjcpf:string, nm_pessoa:string, ds_endereco:string,
     *   nr_endereco:string, ds_bairro:string, nr_cep:string, cd_ibge:?int,
     *   municipio:string, uf:string, nr_fone:string, nr_celular:string}|null
     */
    public function consultar(string $cnpj): ?array
    {
        $digits = preg_replace('/\D/', '', $cnpj);

        if (strlen($digits) !== 14) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/{$digits}");

            if (! $response->successful()) {
                return null;
            }

            $d = $response->json();

            if (empty($d) || empty($d['cnpj'])) {
                return null;
            }

            return [
                'nr_cnpjcpf'  => $this->formatCnpj($digits),
                'nm_pessoa'   => (string) ($d['razao_social'] ?? ''),
                'ds_endereco' => trim(($d['descricao_tipo_de_logradouro'] ?? '') . ' ' . ($d['logradouro'] ?? '')),
                'nr_endereco' => (string) ($d['numero'] ?? ''),
                'ds_bairro'   => (string) ($d['bairro'] ?? ''),
                'nr_cep'      => $this->formatCep((string) ($d['cep'] ?? '')),
                'cd_ibge'     => isset($d['codigo_municipio_ibge']) ? (int) $d['codigo_municipio_ibge'] : null,
                'municipio'   => (string) ($d['municipio'] ?? ''),
                'uf'          => (string) ($d['uf'] ?? ''),
                'nr_fone'     => (string) ($d['ddd_telefone_1'] ?? ''),
                'nr_celular'  => (string) ($d['ddd_telefone_2'] ?? ''),
            ];
        } catch (\Throwable $e) {
            Log::warning('CnpjService: falha ao consultar CNPJ', ['cnpj' => $digits, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function formatCnpj(string $digits): string
    {
        return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $digits);
    }

    private function formatCep(string $cep): string
    {
        $d = preg_replace('/\D/', '', $cep);
        return strlen($d) === 8 ? preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $d) : $cep;
    }
}
