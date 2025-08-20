<?php

namespace App\Models;

use Carbon\Carbon;
use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class Boleto extends Model
{
    use HasFactory;

    protected $table = 'boleto';

    protected $fillable = [
        'CD_EMPRESA',
        'NR_LANCAMENTO',
        'NR_LANCNOTA',
        'NR_DOCUMENTO',
        'DT_EMISSAO',
        'NM_PESSOA',
        'NR_CNPJCPF',
        'CD_FORMAPAGTO',
        'STATUS'
    ];

    public function BoletoResumo()
    {
        $query = "
                    SELECT DISTINCT
                    BI.CD_EMPRESA,
                    BI.NR_LANCAMENTO,
                    C.NR_DOCUMENTO,
                    --BI.NR_DOCUMENTO,
                    C.NR_LANCTONOTA,
                    FORMATA_DATA(C.DT_LANCAMENTO, '%Y-%M-%D') DT_EMISSAO,
                    --C.dt_registro,
                    BI.NM_SACADO NM_PESSOA,
                    BI.nr_cnpjcpfsacado NR_CNPJCPF,
                    --BI.NR_PARCELA,
                    --BI.VL_DOCUMENTO,
                    C.CD_FORMAPAGTO
                FROM CONTAS C
                INNER JOIN BOLETOIMPRESSO BI ON (BI.CD_EMPRESA = C.CD_EMPRESA
                    AND BI.NR_LANCAMENTO = C.NR_LANCAMENTO
                    AND BI.NR_PARCELA = C.NR_PARCELA
                    AND BI.NR_SEQUENCIA = (SELECT FIRST 1
                                                BI2.NR_SEQUENCIA
                                            FROM BOLETOIMPRESSO BI2
                                            WHERE BI2.CD_EMPRESA = C.CD_EMPRESA
                                                AND BI2.NR_LANCAMENTO = C.NR_LANCAMENTO
                                                AND BI2.NR_PARCELA = C.NR_PARCELA
                                            ORDER BY BI2.NR_SEQUENCIA DESC))
                INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
                WHERE C.ST_CONTAS NOT IN ('C', 'L', 'A')
                    --AND C.DT_LANCAMENTO BETWEEN CURRENT_DATE AND CURRENT_DATE
                    --AND C.CD_FORMAPAGTO NOT IN ( 'V2', 'VB', 'V1', 'VD', 'V3')
                    --AND C.NR_LANCAMENTO = 
                    --AND C.CD_EMPRESA = 
                    AND C.CD_PESSOA = 25715

                GROUP BY BI.CD_EMPRESA,
                    BI.NR_LANCAMENTO,
                    C.NR_DOCUMENTO,
                    C.NR_LANCTONOTA,
                    BI.DT_DOCUMENTO,
                    --C.dt_registro,
                    C.DT_LANCAMENTO,
                    --BI.NR_DOCUMENTO,
                    BI.NM_SACADO,
                    BI.NR_CNPJCPFSACADO ,
                    --BI.NR_PARCELA,
                    --BI.VL_DOCUMENTO,
                    C.CD_FORMAPAGTO  
        ";

        $results = DB::connection('firebird_rede')->select($query);

        return Helper::ConvertFormatText($results);
    }

    public function storeData($input)
    {
        foreach ($input as $i) {

            Boleto::firstOrCreate(
                [
                    'CD_EMPRESA' => $i['CD_EMPRESA'],
                    'NR_LANCAMENTO' => $i['NR_LANCAMENTO'],
                ],
                [
                    'CD_EMPRESA' => $i['CD_EMPRESA'],
                    'NR_LANCNOTA' => $i['NR_LANCTONOTA'] == '' ? null : $i['NR_LANCTONOTA'],
                    'NR_LANCAMENTO' => $i['NR_LANCAMENTO'],
                    'NR_DOCUMENTO' => $i['NR_DOCUMENTO'],
                    'DT_EMISSAO' => $i['DT_EMISSAO'],
                    'NM_PESSOA' => $i['NM_PESSOA'],
                    'NR_CNPJCPF' => $i['NR_CNPJCPF'],
                    'CD_FORMAPAGTO' => $i['CD_FORMAPAGTO'],
                    'STATUS' => 'A'
                ]
            );
        }
    }
    public function Boleto($nr_lancamento, $empresa)
    {
        $query = "
                   SELECT DISTINCT
                    CPP.NRTELEFONE NR_CELULAR,
                    P.CD_PESSOA,
                    P.NM_PESSOA,
                    C.ST_CONTAS,
                    FORMATA_DATA(C.DT_VENCIMENTO, '%D/%M/%Y') DT_VENC,
                    C.NR_DOCUMENTO || '/' || C.NR_PARCELA NR_DOC,
                    'R$ ' || C.VL_SALDO VL_TOTAL,
                    C.NR_PARCELA || '/' || MAX((SELECT
                                                    MAX(CM.NR_PARCELA)
                                                FROM CONTAS CM
                                                WHERE CM.CD_EMPRESA = C.CD_EMPRESA
                                                    AND CM.NR_LANCAMENTO = C.NR_LANCAMENTO
                                                    AND CM.CD_PESSOA = C.CD_PESSOA
                                                    AND CM.CD_TIPOCONTA = C.CD_TIPOCONTA)) DS_PARCELA,
                    LIST(DISTINCT 'Nº ' || N.NR_NOTAFISCAL || '-' || N.CD_SERIE, ', ') DS_NOTAS,
                    LIST(DISTINCT FORMATA_DATA(N.DT_EMISSAO, '%D/%M/%Y'), ', ') DS_EMISSAONOTAS,
                    BI.NR_SEQUENCIA,
                    BI.CD_EMPRESA,
                    BI.NR_LANCAMENTO,
                    BI.NR_PARCELA,
                    BI.CD_BANCO,
                    BI.DS_CODIGOBANCO,
                    BI.DS_BANCO,
                    BI.DS_LOCALPAGAMENTO,
                    BI.DT_VENCIMENTO,
                    BI.NM_CEDENTE,
                    BI.DS_ENDERECOCEDENTE,
                    BI.NR_CNPJCPFCEDENTE,
                    BI.DS_AGENCIACODIGOCEDENTE,
                    BI.DT_DOCUMENTO,
                    FORMATA_DATA(BI.DT_DOCUMENTO, '%D/%M/%Y') DT_DOCUMENTO,
                    C.NR_DOCUMENTO,
                    BI.DS_ESPECIE,
                    BI.TP_ACEITE,
                    FORMATA_DATA(BI.DT_PROCESSAMENTO, '%D/%M/%Y') DT_PROCESSAMENTO,
                    BI.NR_NOSSONUMERO,
                    BI.DS_USOBANCO,
                    BI.NR_CARTEIRA,
                    BI.DS_MOEDA,
                    BI.QT_MOEDA,
                    BI.VL_MOEDA,
                    BI.VL_DOCUMENTO,
                    BI.DS_INSTRUCAO,
                    BI.VL_DESCONTOABATIMENTO,
                    BI.VL_DEDUCAO,
                    BI.VL_MULTAJURO,
                    BI.VL_ACRESCIMO,
                    BI.VL_COBRADO,
                    BI.NM_SACADO,
                    BI.NR_CNPJCPFSACADO,
                    BI.DS_ENDERECOSACADO,
                    BI.DS_CEPCIDADESACADO,
                    BI.DS_LINHADIGITAVEL,
                    BI.DS_CODIGOBARRA,
                    --BI.BI_CODIGOBARRA,
                    BI.CD_VENDEDOR,
                    BI.NM_VENDEDOR,
                    BI.DS_PARCELAS,
                    BI.NR_DOCUMENTOPARC,
                    BI.NR_DOCSERIEPARC,
                    BI.CD_SACADO,
                    BI.NM_SACADOCODIGO
                FROM CONTAS C
                INNER JOIN BOLETOIMPRESSO BI ON (BI.CD_EMPRESA = C.CD_EMPRESA
                    AND BI.NR_LANCAMENTO = C.NR_LANCAMENTO
                    AND BI.NR_PARCELA = C.NR_PARCELA
                    AND BI.NR_SEQUENCIA = (SELECT FIRST 1
                                                BI2.NR_SEQUENCIA
                                            FROM BOLETOIMPRESSO BI2
                                            WHERE BI2.CD_EMPRESA = C.CD_EMPRESA
                                                AND BI2.NR_LANCAMENTO = C.NR_LANCAMENTO
                                                AND BI2.NR_PARCELA = C.NR_PARCELA
                                            ORDER BY BI2.NR_SEQUENCIA DESC))
                INNER JOIN PESSOA P ON (P.CD_PESSOA = C.CD_PESSOA)
                INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = P.CD_PESSOA
                    AND EP.CD_ENDERECO = 1)
                LEFT JOIN(SELECT
                            MAX(CP.ID) ID,
                            CP.CD_PESSOA,
                            CP.IDTIPOCONTATO
                        FROM CONTATOPESSOA CP
                        WHERE CP.IDTIPOCONTATO IN (2)
                        GROUP BY CP.CD_PESSOA,
                            CP.IDTIPOCONTATO) CP ON (CP.CD_PESSOA = EP.CD_PESSOA)

                LEFT JOIN CONTATOPESSOA CPP ON (CPP.ID = CP.ID
                    AND CPP.CD_PESSOA = CP.CD_PESSOA)
                LEFT JOIN DESTINOREPARCELAMENTO D ON (D.CD_EMPRCONTAS = C.CD_EMPRESA
                    AND D.NR_LANCAMENTO = C.NR_LANCAMENTO
                    AND D.CD_PESSOA = C.CD_PESSOA
                    AND D.CD_TIPOCONTA = C.CD_TIPOCONTA
                    AND D.NR_PARCELA = C.NR_PARCELA)
                LEFT JOIN ORIGEMREPARCELAMENTO O ON (O.CD_EMPRESA = D.CD_EMPRESA
                    AND O.NR_REPARCELAMENTO = D.NR_REPARCELAMENTO)
                LEFT JOIN CONTAS CO ON (CO.CD_EMPRESA = O.CD_EMPRESA
                    AND CO.NR_LANCAMENTO = O.NR_LANCAMENTO
                    AND CO.CD_PESSOA = O.CD_PESSOA
                    AND CO.CD_TIPOCONTA = O.CD_TIPOCONTA
                    AND CO.NR_PARCELA = O.NR_PARCELA)
                LEFT JOIN NOTA N ON (N.CD_EMPRESA = COALESCE(CO.CD_EMPRESA, C.CD_EMPRESA)
                    AND N.CD_PESSOA = COALESCE(CO.CD_PESSOA, C.CD_PESSOA)
                    AND N.NR_LANCAMENTO = COALESCE(CO.NR_LANCTONOTA, C.NR_LANCTONOTA)
                    AND N.TP_NOTA = COALESCE(CO.TP_CONTAS, C.TP_CONTAS))
                WHERE C.ST_CONTAS NOT IN ('L', 'A')
                    --AND N.DT_EMISSAO >= CURRENT_DATE-1
                    --AND C.CD_FORMAPAGTO NOT IN ('V2', 'VB', 'V1', 'VD', 'V3')
                    AND C.NR_LANCAMENTO = $nr_lancamento
                    AND C.CD_EMPRESA = $empresa

                GROUP BY CPP.NRTELEFONE,
                    P.CD_PESSOA,
                    P.NM_PESSOA,
                    C.ST_CONTAS,
                    DT_VENC,
                    NR_DOC,
                    C.NR_PARCELA,
                    VL_TOTAL,
                    BI.NR_SEQUENCIA,
                    BI.CD_EMPRESA,
                    BI.NR_LANCAMENTO,
                    BI.NR_PARCELA,
                    BI.CD_BANCO,
                    BI.DS_CODIGOBANCO,
                    BI.DS_BANCO,
                    BI.DS_LOCALPAGAMENTO,
                    BI.DT_VENCIMENTO,
                    BI.NM_CEDENTE,
                    BI.DS_ENDERECOCEDENTE,
                    BI.NR_CNPJCPFCEDENTE,
                    BI.DS_AGENCIACODIGOCEDENTE,
                    BI.DT_DOCUMENTO,
                    C.NR_DOCUMENTO,
                    BI.DS_ESPECIE,
                    BI.TP_ACEITE,
                    BI.DT_PROCESSAMENTO,
                    BI.NR_NOSSONUMERO,
                    BI.DS_USOBANCO,
                    BI.NR_CARTEIRA,
                    BI.DS_MOEDA,
                    BI.QT_MOEDA,
                    BI.VL_MOEDA,
                    BI.VL_DOCUMENTO,
                    BI.DS_INSTRUCAO,
                    BI.VL_DESCONTOABATIMENTO,
                    BI.VL_DEDUCAO,
                    BI.VL_MULTAJURO,
                    BI.VL_ACRESCIMO,
                    BI.VL_COBRADO,
                    BI.NM_SACADO,
                    BI.NR_CNPJCPFSACADO,
                    BI.DS_ENDERECOSACADO,
                    BI.DS_CEPCIDADESACADO,
                    BI.DS_LINHADIGITAVEL,
                    BI.DS_CODIGOBARRA,
                    --BI.BI_CODIGOBARRA,
                    BI.CD_VENDEDOR,
                    BI.NM_VENDEDOR,
                    BI.DS_PARCELAS,
                    BI.NR_DOCUMENTOPARC,
                    BI.NR_DOCSERIEPARC,
                    BI.CD_SACADO,
                    BI.NM_SACADOCODIGO,
                    BI.NM_AVALISTA,
                    BI.NR_CNPJCPFAVALISTA
                ORDER BY P.CD_PESSOA,
                    P.NM_PESSOA,
                    NR_DOC,
                    C.NR_PARCELA                
                ";

        $results = DB::connection('firebird_rede')->select($query);

        return Helper::ConvertFormatText($results);

        // $key = "Boleto_" . $nr_doc . '_' . Auth::user()->id;
        // return Cache::remember($key, now()->addMinutes(15), function () use ($query, $empresa) {
        //     return DB::connection($this->setConnet($empresa->regiao))->select($query);
        // });
    }

    public function listBoletoSend($nota)
    {
        if ($nota === null) {
            return Boleto::where('STATUS', 'A')
                // ->where('NR_DOCUMENTO', 1103666)
                // ->where('CD_EMPRESA', 102)
                ->where('created_at', '<', Carbon::now()->subHour(2))
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        } else {

            return Boleto::where('STATUS', 'A')
                ->whereIn('NR_DOCUMENTO', [$nota['NR_DOCUMENTO']])
                ->where('CD_EMPRESA', $nota['CD_EMPRESA'])
                ->get();
        }
    }

    public function UpdateBoletoSend($input, $status)
    {
        return Boleto::where('NR_LANCAMENTO', $input['NR_LANCAMENTO'])
            ->where('CD_EMPRESA', $input['CD_EMPRESA'])
            ->update([
                'STATUS' => $status,
                'updated_at' => \Carbon\Carbon::now()
            ]);
    }
}
