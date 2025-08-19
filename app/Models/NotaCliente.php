<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaCliente extends Model
{
    use HasFactory;

    public function getListNotaCliente($nr_lancamento = null)
    {
        $query = "
            SELECT DISTINCT
        N.CD_EMPRESA,
        N.NR_LANCAMENTO,
        N.TP_NOTA,
        N.CD_SERIE,
        FORMATA_DATA(N.DT_EMISSAO, '%D/%M/%Y') DS_DTEMISSAO,
        N.HR_NOTA,                        

        -- EMPRESA --
        PE.NM_PESSOA NM_EMPRESA,
        PE.NM_FANTASIA,
        PE.NR_CNPJCPF NR_CNPJEMPRESA,
        EE.NR_INSCEST NR_INSCESTEMPRESA,
        EE.NR_INSCMUN NR_INSCMUNEMPRESA,
        --PE.DS_SITE DS_SITEEMPRESA,
        PE.DS_EMAIL DS_EMAILEMPRESA,
        EE.DS_ENDERECO DS_ENDEMPRESA,
        EE.NR_ENDERECO NR_ENDEMPRESA,
        EE.NR_CEP NR_CEPEMPRESA,
        EE.DS_BAIRRO DS_BAIRROEMPRESA,
        EE.DS_COMPLEMENTO DS_COMPEMPRESA,
        ME.DS_MUNICIPIO DS_MUNICIPIOEMP,
        EE.NR_FONE NR_FONEEMPRESA,
        EE.NR_FAX NR_FAXEMPRESA,
        --COALESCE(CF.DS_LOGOTIPO, PA.DS_LOGOTIPO) DS_LOGOTIPO,
        COALESCE(PU.NM_PESSOA, U.NM_USUARIO) NM_USUARIO,
        --EE.DS_ENDERECO || COALESCE(', Nº ' || EE.NR_ENDERECO, ' ') || COALESCE(', BAIRRO: ' || EE.DS_BAIRRO, ' ') || COALESCE(', ' || EE.DS_COMPLEMENTO, ' ') DS_ENDERECOEMP,
        --EE.DS_ENDERECO || COALESCE(' ' || EE.NR_ENDERECO, '') || COALESCE(' ' || EE.DS_BAIRRO, '') || COALESCE('' || ME.DS_MUNICIPIO, '') || COALESCE(' - ' || ESE.SG_ESTADO, '') DS_ENDERECOEMPRESA,
        ME.DS_MUNICIPIO || ' - ' || ESE.SG_ESTADO DS_MUNEMPRESA,
        'CNPJ: ' || PE.NR_CNPJCPF || COALESCE(' | IE: ' || EE.NR_INSCEST, '') NR_CNPJINSCEST,
        'TELEFONE : ' || COALESCE(EE.NR_FONE, EE.NR_CELULAR, EE.NR_FAX) NR_TELEFONEEMPRESA,

        --NOTA--
        COALESCE(NFSE.NR_RPS, NFSE.NR_NOTASERVICO, N.NR_NOTAFISCAL) NR_NOTA,
        N.NR_NOTAFISCAL NR_NOTAPREFA,
        N.DT_EMISSAO DT_EMISSAONOTA,
        NFSE.NR_NOTASERVICO,
        NFSE.CD_AUTENTICACAO,
        N.DT_EMISSAO DT_EMISSAORPS,
        N.NR_LANCAMENTO || '/' || N.CD_SERIE || '  ' || EXTRACT(DAY FROM N.DT_EMISSAO) || '/' || EXTRACT(MONTH FROM N.DT_EMISSAO) || '/' || EXTRACT(YEAR FROM N.DT_EMISSAO) DS_NOTASERIEDATA,
        NFSE.NR_LOTE NR_LOTERPS,
        NFSE.NR_RPS,
        N.NR_NOTAFISCAL NR_DOCUMENTO,
        N.DT_REGISTRO HR_DOCUMENTO,
        RC.O_DS_CONDPAGTO,
        --TRIM(OBS.V_DS_OBSNOTA) DS_OBSNOTA,
        --TRIM(CAST(OBS.V_DS_OBSFISCAL AS DOM_VARCHAR1000)) DS_OBSFISCAL,
        N.DS_OBSNOTA,
        N.DS_OBSFISCAL,
        V.CD_PESSOA || '-' || V.NM_PESSOA NM_VENDEDOR,
        C.CD_CONDPAGTO,
        C.DS_CONDPAGTO,
        F.CD_FORMAPAGTO,
        F.DS_FORMAPAGTO,

        --CLIENTE--
        P.CD_PESSOA,
        P.NM_PESSOA,
        P.NR_CNPJCPF,
        P.DS_EMAIL,
        P.NM_FANTASIA NM_FANTASIAPESSOA,
        EP.NR_ENDERECO NR_ENDPESSOA,
        EP.NR_INSCMUN,
        EP.NR_CEP NR_CEPPESSOA,
        EP.DS_BAIRRO DS_BAIRROPESSOA,
        EP.DS_COMPLEMENTO DS_COMPPESSOA,
        MP.DS_MUNICIPIO,
        MP.DS_MUNICIPIO || ' - ' || MP.SG_ESTADO DS_MUNPESSOA,
        MP.SG_ESTADO,
        EP.NR_FONE,
        EP.NR_FAX,
        EP.DS_CONTATO,
        EP.NR_CELULAR,
        EP.DS_ENDERECO DS_ENDERECOPESSOA,
        --EP.DS_ENDERECO || COALESCE(', Nº ' || EP.NR_ENDERECO, ' ') || COALESCE(', BAIRRO: ' || EP.DS_BAIRRO, ' ') || COALESCE(' - ' || MP.DS_MUNICIPIO || ' , ' || ESP.DS_ESTADO, ' ') DS_ENDPESSOA,
        EP.NR_INSCEST NR_INSCESTPESSOA,
        --EP.DS_ENDERECO || COALESCE(', Nº ' || EP.NR_ENDERECO, ' ') || COALESCE(', CEP: ' || EP.NR_CEP, ' ') || COALESCE(', BAIRRO: ' || EP.DS_BAIRRO, ' ') || COALESCE(', ' || EP.DS_COMPLEMENTO, ' ') || COALESCE(' - ' || MP.DS_MUNICIPIO || ' , ' || ESP.DS_ESTADO, ' ') DS_ENDCOMPLETOPESSOA,

        
        (SELECT
            V_VL_IMPOSTO
        FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, NULL, 'Q', NULL, 'VI')) VL_ISSQN,

        (SELECT
            V_VL_IMPOSTO
        FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, NULL, 'Q', 'S', 'VI')) VL_ISSQN_RETIDO,
        N.VL_CONTABIL";

        if (!empty($nr_lancamento)) {
            $query .= "
            ,R.O_CD_ITEM,
            R.O_DS_ITEM,
            R.O_QTDE,
            R.O_VL_UNITARIO,
            R.O_VL_TOTAL,
            R.O_QT_DESCONTADA,
            R.O_NR_SERIE,
            R.O_NR_DOT,
            R.O_NR_FOGO,
            R.O_DS_MODELO,
            R.O_DS_MARCA,
            R.O_DS_MEDIDAPNEU,
            R.O_DS_DESENHO,
            R.O_IDORDEMPRODUCAORECAP,
            R.O_ORDEM,
            R.O_VL_TOTAL TOT_VL_ITENS,
            R.O_QTDE TOT_QT_ITENS,
            IIF(R.O_ORDEM = 1, R.O_ORDEM, NULL) TOT_QT_PNEUS,
            IIF(R.O_DSSITUACAO = 'A', 1, NULL) TOT_QT_PRODUZIDOS,
            IIF(R.O_DSSITUACAO = 'R', 1, NULL) TOT_QT_RECUSADOS,
            CAST(I.DS_OBSERVACAO AS VARCHAR(32000)) DS_OBSERVACAOTPO001";
        }

        $query .= "                   

                FROM NOTA N
                INNER JOIN PESSOA P ON (P.CD_PESSOA = N.CD_PESSOA)
                INNER JOIN ENDERECOPESSOA EP ON (EP.CD_PESSOA = N.CD_PESSOA
                    AND EP.CD_ENDERECO = N.CD_ENDERECO)
                INNER JOIN MUNICIPIO MP ON (MP.CD_MUNICIPIO = EP.CD_MUNICIPIO)
                INNER JOIN ESTADO ESP ON (ESP.SG_ESTADO = MP.SG_ESTADO)

                INNER JOIN EMPRESA E ON (E.CD_EMPRESA = N.CD_EMPRESA)
                INNER JOIN PESSOA PE ON (PE.CD_PESSOA = E.CD_PESSOA)
                INNER JOIN ENDERECOPESSOA EE ON (EE.CD_PESSOA = PE.CD_PESSOA
                    AND EE.CD_ENDERECO = (SELECT
                                                MIN(CD_ENDERECO)
                                            FROM ENDERECOPESSOA ENDER
                                            WHERE ENDER.CD_PESSOA = EE.CD_PESSOA))
                INNER JOIN MUNICIPIO ME ON (EE.CD_MUNICIPIO = ME.CD_MUNICIPIO)
                INNER JOIN ESTADO ESE ON (ESE.SG_ESTADO = ME.SG_ESTADO)

                INNER JOIN CONDPAGTO C ON (C.CD_CONDPAGTO = N.CD_CONDPAGTO)
                INNER JOIN PARMFATUR PA ON (PA.CD_EMPRESA = N.CD_EMPRESA)
                INNER JOIN USUARIO U ON (U.CD_USUARIO = N.CD_USUARIO)

                INNER JOIN NFSE ON (NFSE.CD_EMPRESA = N.CD_EMPRESA
                    AND NFSE.NR_LANCAMENTO = N.NR_LANCAMENTO
                    AND NFSE.CD_SERIE = N.CD_SERIE
                    AND NFSE.TP_NOTA = N.TP_NOTA)

                LEFT JOIN PESSOA PU ON (PU.CD_PESSOA = U.CD_PESSOA)
                LEFT JOIN PESSOA V ON (V.CD_PESSOA = N.CD_VENDEDOR)
                LEFT JOIN CONFIGNFSE CF ON (CF.CD_EMPRESA = N.CD_EMPRESA)
                LEFT JOIN FORMAPAGTO F ON (F.CD_FORMAPAGTO = N.CD_FORMAPAGTO)
                LEFT JOIN RETORNA_CONDPAGTONOTALNF230(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA) RC ON (1 = 1)
                " . ($nr_lancamento != null ? " LEFT JOIN RETORNA_SERVICONOTALNF230(N.CD_EMPRESA, N.NR_LANCAMENTO, N.TP_NOTA, N.CD_SERIE) R ON (1 = 1)
                    INNER JOIN ITEM I ON (I.CD_ITEM = R.O_CD_ITEM) " : "") . "
                WHERE N.TP_NOTA = 'S'
                    AND N.CD_SERIE = 'F3'
                    AND N.ST_NOTA = 'V'
                    --AND N.DT_EMISSAO = CURRENT_DATE -1
                    --AND P.DS_EMAIL IS NOT NULL
                    AND N.CD_PESSOA = 24585
                    " . ($nr_lancamento != null ? " AND N.NR_LANCAMENTO = " . $nr_lancamento : "") . "
                    
                ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
