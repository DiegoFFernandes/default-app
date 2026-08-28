<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaCliente extends Model
{
    use HasFactory;

    public function getListNotaCliente($nr_lancamento = null, $cd_pessoa = null, $nr_nota = null, $dt_registro_min = null)
    {
        if (empty($cd_pessoa) && empty($nr_nota) && empty($dt_registro_min)) {
            throw new \InvalidArgumentException('Informe cd_pessoa, nr_nota ou dt_registro_min para consultar notas.');
        }

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
        ME.CD_IBGE CD_IBGEEMP,
        EE.DS_BAIRRO DS_BAIRROEMPRESA,
        EE.DS_COMPLEMENTO DS_COMPEMPRESA,
        ME.DS_MUNICIPIO DS_MUNICIPIOEMP,
        EE.NR_FONE NR_FONEEMPRESA,
        EE.NR_CELULAR NR_CELULAREMPRESA,
        EE.NR_FAX NR_FAXEMPRESA,
        --COALESCE(CF.DS_LOGOTIPO, PA.DS_LOGOTIPO) DS_LOGOTIPO,
        COALESCE(PU.NM_PESSOA, U.NM_USUARIO) NM_USUARIO,
        --EE.DS_ENDERECO || COALESCE(', Nº ' || EE.NR_ENDERECO, ' ') || COALESCE(', BAIRRO: ' || EE.DS_BAIRRO, ' ') || COALESCE(', ' || EE.DS_COMPLEMENTO, ' ') DS_ENDERECOEMP,
        --EE.DS_ENDERECO || COALESCE(' ' || EE.NR_ENDERECO, '') || COALESCE(' ' || EE.DS_BAIRRO, '') || COALESCE('' || ME.DS_MUNICIPIO, '') || COALESCE(' - ' || ESE.SG_ESTADO, '') DS_ENDERECOEMPRESA,
        ME.DS_MUNICIPIO || ' - ' || ESE.SG_ESTADO DS_MUNEMPRESA,
        'CNPJ: ' || PE.NR_CNPJCPF || COALESCE(' | IE: ' || EE.NR_INSCEST, '') NR_CNPJINSCEST,
        'TELEFONE : ' || COALESCE(EE.NR_FONE, EE.NR_CELULAR, EE.NR_FAX) NR_TELEFONEEMPRESA,

        --NOTA--
        COALESCE(NFSE.NR_NOTASERVICO, N.NR_NOTAFISCAL, NFSE.NR_RPS) NR_NOTA,
        FORMATA_DATA(N.DT_EMISSAO, '%D/%M/%Y') DS_DTEMISSAO,
        N.DT_EMISSAO DT_EMISSAONOTA,
        IIF(NFSE.CD_AUTENTICACAO IS NOT NULL, COALESCE(NFSE.NR_NOTASERVICO, N.NR_NOTAFISCAL), '') NR_NOTASERVICO,
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
        RPE.O_DS_EMAIL DS_EMAILCOPIA,
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

        --IMPOSTOS VALOR
        (SELECT
            V_VL_IMPOSTO
        FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, NULL, 'Q', NULL, 'VI')) VL_ISSQN,

        (SELECT
            V_VL_IMPOSTO
        FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, NULL, 'Q', 'S', 'VI')) VL_ISSQN_RETIDO,

        N.VL_CONTABIL,
        
        (SELECT V_VL_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'S', NULL, 'VI')) VL_PIS,
        (SELECT V_VL_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'F', NULL, 'VI')) VL_COFINS,
        (SELECT V_VL_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'N', NULL, 'VI')) VL_INSS,
        (SELECT V_VL_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'J', NULL, 'VI')) VL_IR,
        (SELECT V_VL_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'L', NULL, 'VI')) VL_CSLL,

        N.VL_DESCONTO VL_TOTDESCONTO, LDN.O_VL_LAUDO VL_GARANTIA,

        --IMPOSTOS PORCENTAGENS
        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, 
                            N.TP_NOTA, NULL, 'Q', NULL, 'VI')) PC_ISSQN,

        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'S', NULL, 'VI')) PC_PIS,
        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'F', NULL, 'VI')) PC_COFINS,
        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'N', NULL, 'VI')) PC_INSS,
        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'J', NULL, 'VI')) PC_IR,
        (SELECT V_PC_IMPOSTO
            FROM VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE,
                            N.TP_NOTA, NULL, 'L', NULL, 'VI')) PC_CSLL,
        N.VL_NOTAFISCAL               
        
        ";

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
            CASE WHEN R.O_ORDEM = 1 THEN ROW_NUMBER() OVER (PARTITION BY CASE WHEN R.O_ORDEM = 1 THEN 0 ELSE 1 END ORDER BY R.O_IDORDEMPRODUCAORECAP, R.O_ORDEM) ELSE NULL END AS SEQ,
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
                LEFT JOIN RETORNA_VLLAUDONOTA(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA) LDN ON (1=1)
                LEFT JOIN RETORNA_PESSOAEMAIL(P.CD_PESSOA, 1, NULL) RPE ON (1 = 1)
                " . ($nr_lancamento != null ? " LEFT JOIN RETORNA_SERVICONOTALNF230(N.CD_EMPRESA, N.NR_LANCAMENTO, N.TP_NOTA, N.CD_SERIE) R ON (1 = 1)
                    INNER JOIN ITEM I ON (I.CD_ITEM = R.O_CD_ITEM) " : "") . "
                WHERE N.TP_NOTA = 'S'
                    AND N.CD_SERIE = 'F3'                    
                    AND N.ST_NOTA = 'V'
                    AND NFSE.CD_AUTENTICACAO IS NOT NULL
                    --AND N.DT_EMISSAO = CURRENT_DATE -1
                    --AND P.DS_EMAIL IS NOT NULL
                    " . ($cd_pessoa != null ? " AND N.CD_PESSOA in ($cd_pessoa)" : "") . "
                    " . ($nr_nota != null ? " AND N.NR_NOTAFISCAL in ($nr_nota)" : "") . "
                    " . ($nr_lancamento != null ? " AND N.NR_LANCAMENTO = " . $nr_lancamento : "") . "
                    " . ($dt_registro_min != null ? " AND N.DT_REGISTRO > '" . $dt_registro_min . "'" : "") . "
                ";

        if (!empty($nr_lancamento)) {
            $query .= "
            ORDER BY R.O_IDORDEMPRODUCAORECAP, R.O_ORDEM";
        }

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }

    /**
     * Lista notas emitidas (NFS-e válida) com o status do disparo automático,
     * via LEFT JOIN em DISPARO_ENVIO - nota que ainda não foi processada pelo
     * gerarPendentes() aparece do mesmo jeito, com ST_ENVIO nulo (COALESCE
     * para 'P' = Pendente de Envio). Separado de getListNotaCliente() porque
     * essa e uma listagem resumida (grid), sem os joins de imposto/itens que
     * só o layout de PDF de uma nota especifica usa.
     */
    public function listarNotasEmitidas(array $filtros): array
    {
        $where = ['CAST(N.DT_EMISSAO AS DATE) BETWEEN :inicio AND :fim'];
        $bindings = [
            'inicio' => $filtros['inicio_data'],
            'fim'    => $filtros['fim_data'],
        ];

        if (!empty($filtros['nm_pessoa'])) {
            $where[] = 'P.NM_PESSOA CONTAINING :nm_pessoa';
            $bindings['nm_pessoa'] = Helper::ToIso($filtros['nm_pessoa']);
        }

        if (!empty($filtros['cd_empresa'])) {
            $where[] = 'N.CD_EMPRESA = :cd_empresa';
            $bindings['cd_empresa'] = $filtros['cd_empresa'];
        }

        if (!empty($filtros['cd_contexto'])) {
            $where[] = 'DE.CD_CONTEXTO = :cd_contexto';
            $bindings['cd_contexto'] = $filtros['cd_contexto'];
        }

        if (!empty($filtros['st_envio'])) {
            $where[] = "COALESCE(DE.ST_ENVIO, 'P') = :st_envio";
            $bindings['st_envio'] = $filtros['st_envio'];
        }

        $query = "
            SELECT
                N.CD_EMPRESA,
                N.NR_LANCAMENTO,
                N.CD_SERIE,
                N.TP_NOTA,
                COALESCE(NFSE.NR_NOTASERVICO, N.NR_NOTAFISCAL, NFSE.NR_RPS) NR_NOTA,
                N.DT_EMISSAO,  
                N.DT_REGISTRO,

                P.CD_PESSOA,
                P.CD_PESSOA||'-'||P.NM_PESSOA NM_PESSOA,
                COALESCE(DE.DS_EMAILDEST, P.DS_EMAIL) DS_EMAIL,
                COALESCE(DE.DS_EMAILCOPIA, RPE.O_DS_EMAIL) DS_EMAILCOPIA,

                DE.CD_ENVIO,
                DE.CD_CONTEXTO,
                DC.DS_CONTEXTO,
                DC.TP_CANAL,
                DC.CD_HANDLER,

                COALESCE(DE.DS_EMAILDEST, P.DS_EMAIL) DS_EMAILDEST,
                DE.DS_TELEFONE,
                DE.NR_TENTATIVAS,
                DE.DT_ENVIO,
                DE.DS_MOTIVO,
                COALESCE(DE.ST_ENVIO, 'P') ST_ENVIO

            FROM NOTA N
            INNER JOIN PESSOA P ON (P.CD_PESSOA = N.CD_PESSOA)
            INNER JOIN NFSE ON (NFSE.CD_EMPRESA = N.CD_EMPRESA
                AND NFSE.NR_LANCAMENTO = N.NR_LANCAMENTO
                AND NFSE.CD_SERIE = N.CD_SERIE
                AND NFSE.TP_NOTA = N.TP_NOTA)
            LEFT JOIN RETORNA_PESSOAEMAIL(P.CD_PESSOA, 1, NULL) RPE ON (1 = 1)
            LEFT JOIN DISPARO_ENVIO DE ON (DE.CD_EMPRESA = N.CD_EMPRESA
                AND DE.NR_LANCAMENTO = N.NR_LANCAMENTO
                AND DE.CD_SERIE = N.CD_SERIE
                AND DE.TP_NOTA = N.TP_NOTA)
            LEFT JOIN DISPARO_CONTEXTO DC ON (DC.CD_CONTEXTO = DE.CD_CONTEXTO)

            WHERE N.TP_NOTA = 'S'
                AND N.CD_SERIE = 'F3'
                AND N.ST_NOTA = 'V'
                AND NFSE.CD_AUTENTICACAO IS NOT NULL
                AND " . implode(' AND ', $where) . '

            ORDER BY N.DT_REGISTRO DESC
        ';

        return Helper::ConvertFormatText(DB::connection('firebird')->select($query, $bindings));
    }

    public function getListNotaClienteNacional($nr_lancamento = null, $cd_pessoa = null, $nr_nota = null, $dt_registro_min = null)
    {
        $query = "
            SELECT
                X.CD_EMPRESA,
                X.NR_LANCAMENTO,
                X.TP_NOTA,
                X.CD_SERIE,
                X.NM_EMPRESA,
                X.NM_FANTASIA,
                X.NR_CNPJEMPRESA,
                X.NR_INSCESTEMPRESA,
                X.NR_INSCMUNEMPRESA,
                X.DS_SITEEMPRESA,
                X.DS_EMAILEMPRESA,
                X.DS_ENDEMPRESA,
                X.NR_ENDEMPRESA,
                X.NR_CEPEMPRESA,
                X.DS_BAIRROEMPRESA,
                X.DS_COMPEMPRESA,
                X.DS_MUNICIPIOEMP,
                X.NR_FONEEMPRESA,
                X.NR_CELULAREMPRESA,
                X.NR_FAXEMPRESA,
                X.DS_LOGOTIPO,
                X.NM_USUARIO,
                X.DS_ENDERECOEMP,
                X.DS_MUNEMPRESA,
                X.ST_OPTANTESIMPLES,
                X.NR_NOTA,
                X.DT_EMISSAONOTA,
                X.CD_AUTENTICACAO,
                X.DT_EMISSAORPS,
                X.NR_LOTERPS,
                X.NR_RPS,
                X.NR_DOCUMENTO,
                X.HR_DOCUMENTO,
                X.ST_NOTA,
                X.NR_PROTOCOLOLOTE,
                X.NR_DPS,
                X.CD_SERIEPREFEITURA,
                X.DS_LINK,
                X.CD_PESSOA,
                X.NM_PESSOA,
                X.NR_CNPJCPF,
                X.DS_EMAIL,
                X.NM_FANTASIAPESSOA,
                X.NR_ENDPESSOA,
                X.NR_INSCMUN,
                X.NR_CEPPESSOA,
                X.DS_BAIRROPESSOA,
                X.DS_COMPPESSOA,
                X.DS_MUNPESSOA,
                X.NR_FONE,
                X.NR_FAX,
                X.DS_CONTATO,
                X.NR_CELULAR,
                X.DS_ENDPESSOA,
                X.NR_INSCESTPESSOA,
                X.DS_ENDCOMPLETOPESSOA,
                X.DS_CNAE,
                X.DS_OBSNOTA,
                X.DS_OBSFISCAL,
                X.NM_VENDEDOR,
                X.CD_CONDPAGTO,
                X.DS_CONDPAGTO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_DESCONTO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_DESCONTO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_LIQUIDO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_LIQUIDO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BRUTO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BRUTO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BRUTONOTA AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BRUTONOTA,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BASEISS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BASEISS,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BASEISSRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BASEISSRET,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BASESUBISS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BASESUBISS,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BASESUBISSRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BASESUBISSRET,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_ISS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_ISS,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_ISSRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_ISSRET,
                X.DS_SERVICO,
                X.DS_FORMAPAGTO,
                X.PC_ALIQUOTAISS,
                X.DS_ST_RETIDO,
                X.NR_ORDEMCARREG,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_LIQUIDONOTA AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_LIQUIDONOTA,
                X.NR_NOTASERVICO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_IRRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_IRRET,
                X.VL_IBSMUN,
                X.VL_IBSUF,
                X.VL_CBS,
                X.DS_CSTCLASSTRIB,
                X.PC_IBSUF,
                X.PC_IBSMUN,
                X.PC_ALIQEFETIBSUF,
                X.PC_ALIQEFETIBSMUN,
                X.PC_REDALIQIBSCBS,
                X.PC_CBS,
                X.PC_ALIQEFETCBS,
                X.VL_TOTIBS,
                X.VL_TOTIBSCBS,
                X.VL_LIQUIDOMAISIBSCBS,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_BRUTOSERVICO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BRUTOSERVICO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_DESCONTOCOND AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_DESCONTOCOND,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_TOTALRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_TOTALRET,
                X.VL_BASEIBSCBS,
                X.VL_EXCLUIBSCBS,
                X.CD_IBGEINCID,
                X.DS_MUNINCID,
                X.DS_OBSERVACAONF,
                X.DS_OBSFISCALNF,
                X.DS_CODBARRASCANHOTO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_PISCOFINSCSLLRET AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_PISCOFINSCSLLRET,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_PISDEVIDO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_PISDEVIDO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_COFINSDEVIDO AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_COFINSDEVIDO,
                COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(X.VL_TOTTRIBFED AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_TOTTRIBFED,
                X.DS_OBSNOTA_CAMPO,
                X.DS_PARCELAS_CAMPO,
                X.DS_OBSEPARCELAS_CAMPO,
                X.DS_INFOCOMPLEMENTAR,
                --INFORMAÇÕES DOS PNEUS
                R.O_CD_ITEM,
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
                CASE
                WHEN R.O_ORDEM = 1 THEN ROW_NUMBER() OVER(PARTITION BY CASE
                                                                        WHEN R.O_ORDEM = 1 THEN 0
                                                                        ELSE 1
                                                                        END ORDER BY R.O_IDORDEMPRODUCAORECAP, R.O_ORDEM)
                ELSE NULL
                END AS SEQ,
                R.O_VL_TOTAL TOT_VL_ITENS,
                R.O_QTDE TOT_QT_ITENS,
                IIF(R.O_ORDEM = 1, R.O_ORDEM, NULL) TOT_QT_PNEUS,
                IIF(R.O_DSSITUACAO = 'A', 1, NULL) TOT_QT_PRODUZIDOS,
                IIF(R.O_DSSITUACAO = 'R', 1, NULL) TOT_QT_RECUSADOS

            FROM (SELECT DISTINCT
                    N.CD_EMPRESA,
                    N.NR_LANCAMENTO,
                    N.TP_NOTA,
                    N.CD_SERIE,
                    -- EMPRESA --
                    PE.NM_PESSOA NM_EMPRESA,
                    PE.NM_FANTASIA,
                    PE.NR_CNPJCPF NR_CNPJEMPRESA,
                    EE.NR_INSCEST NR_INSCESTEMPRESA,
                    EE.NR_INSCMUN NR_INSCMUNEMPRESA,
                    PE.DS_SITE DS_SITEEMPRESA,
                    PE.DS_EMAIL DS_EMAILEMPRESA,
                    EE.DS_ENDERECO DS_ENDEMPRESA,
                    EE.NR_ENDERECO NR_ENDEMPRESA,
                    COALESCE(CAST(ME.CD_IBGE AS VARCHAR(7)), '-') || ' / ' || COALESCE(SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EE.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 1 FOR 2) || '.' || SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EE.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 3 FOR 3) || '-' || SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EE.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 6 FOR 3), '-') NR_CEPEMPRESA,
                    EE.DS_BAIRRO DS_BAIRROEMPRESA,
                    EE.DS_COMPLEMENTO DS_COMPEMPRESA,
                    ME.DS_MUNICIPIO DS_MUNICIPIOEMP,
                    EE.NR_FONE NR_FONEEMPRESA,
                    EE.NR_CELULAR NR_CELULAREMPRESA,
                    EE.NR_FAX NR_FAXEMPRESA,
                    COALESCE(CF.DS_LOGOTIPO, PA.DS_LOGOTIPO) DS_LOGOTIPO,
                    COALESCE(PU.NM_PESSOA, U.NM_USUARIO) NM_USUARIO,
                    EE.DS_ENDERECO || COALESCE(', Nº ' || EE.NR_ENDERECO, ', S/N') || COALESCE(', ' || EE.DS_BAIRRO, ' ') || COALESCE(', ' || EE.DS_COMPLEMENTO, ' ') DS_ENDERECOEMP,
                    ME.DS_MUNICIPIO || ' / ' || ESE.SG_ESTADO || ' / ' || IIF(UPPER(PBE.NMPAIS) = 'BRASIL', 'BR', COALESCE(PBE.NMPAIS, '-')) DS_MUNEMPRESA,
                    IIF(E.ST_FEDERAL = 'E' OR E.ST_FEDERAL = 'M', _UTF8'Optante', _UTF8'Não Optante') ST_OPTANTESIMPLES,

                    --NOTA--
                    COALESCE(NFSE.NR_NOTASERVICO, N.NR_NOTAFISCAL) NR_NOTA,
                    DATEADD(SECOND, EXTRACT(HOUR FROM COALESCE(NFSE.DT_REGISTRO, N.DT_REGISTRO)) * 3600 + EXTRACT(MINUTE FROM COALESCE(NFSE.DT_REGISTRO, N.DT_REGISTRO)) * 60 + CAST(EXTRACT(SECOND FROM COALESCE(NFSE.DT_REGISTRO, N.DT_REGISTRO)) AS INTEGER), CAST(N.DT_EMISSAO AS TIMESTAMP)) DT_EMISSAONOTA,
                    NFSE.CD_AUTENTICACAO,
                    DATEADD(SECOND, EXTRACT(HOUR FROM N.DT_REGISTRO) * 3600 + EXTRACT(MINUTE FROM N.DT_REGISTRO) * 60 + CAST(EXTRACT(SECOND FROM N.DT_REGISTRO) AS INTEGER), CAST(N.DT_EMISSAO AS TIMESTAMP)) DT_EMISSAORPS,
                    NFSE.NR_LOTE NR_LOTERPS,
                    NFSE.NR_RPS,
                    N.NR_NOTAFISCAL NR_DOCUMENTO,

                    N.DT_REGISTRO HR_DOCUMENTO,
                    N.ST_NOTA,
                    LR.NR_PROTOCOLO NR_PROTOCOLOLOTE,
                    COALESCE(NFSE.NR_DPS, NFSE.NR_RPS) NR_DPS,
                    COALESCE(PRE.CD_SERIEPREFEITURA, NFSE.CD_SERIE) CD_SERIEPREFEITURA,
                    ('https://www.nfse.gov.br/ConsultaPublica/?tpc=1&&chave=' || NFSE.CD_AUTENTICACAO) DS_LINK,
                    --CLIENTE--
                    P.CD_PESSOA,
                    P.NM_PESSOA,
                    P.NR_CNPJCPF,
                    P.DS_EMAIL,
                    P.NM_FANTASIA NM_FANTASIAPESSOA,
                    EP.NR_ENDERECO NR_ENDPESSOA,
                    EP.NR_INSCMUN,
                    COALESCE(CAST(MP.CD_IBGE AS VARCHAR(7)), '-') || ' / ' || COALESCE(SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EP.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 1 FOR 2) || '.' || SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EP.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 3 FOR 3) || '-' || SUBSTRING(LPAD(REPLACE(REPLACE(TRIM(CAST(EP.NR_CEP AS VARCHAR(20))), '.', ''), '-', ''), 8, '0') FROM 6 FOR 3), '-') NR_CEPPESSOA,
                    EP.DS_BAIRRO DS_BAIRROPESSOA,
                    EP.DS_COMPLEMENTO DS_COMPPESSOA,
                    MP.DS_MUNICIPIO || ' / ' || MP.SG_ESTADO DS_MUNPESSOA,
                    EP.NR_FONE,
                    EP.NR_FAX,
                    EP.DS_CONTATO,
                    EP.NR_CELULAR,
                    EP.DS_ENDERECO || COALESCE(', Nº ' || EP.NR_ENDERECO, ', S/N') || COALESCE(', ' || EP.DS_BAIRRO, ' ') DS_ENDPESSOA,

                    EP.NR_INSCEST NR_INSCESTPESSOA,

                    EP.DS_ENDERECO || COALESCE(', Nº ' || EP.NR_ENDERECO, ', S/N') || COALESCE(', Cep: ' || EP.NR_CEP, ' ') || COALESCE(', Bairro: ' || EP.DS_BAIRRO, ' ') || COALESCE(', ' || EP.DS_COMPLEMENTO, ' ') || COALESCE(' - ' || MP.DS_MUNICIPIO || ' , ' || ESP.DS_ESTADO, ' ') DS_ENDCOMPLETOPESSOA,

                    CNAE.DS_CNAE,
                    TRIM(OBS.V_DS_OBSNOTA) DS_OBSNOTA,
                    TRIM(CAST(OBS.V_DS_OBSFISCAL AS VARCHAR(1000))) DS_OBSFISCAL,
                    V.CD_PESSOA || '-' || V.NM_PESSOA NM_VENDEDOR,
                    C.CD_CONDPAGTO,
                    C.DS_CONDPAGTO,

                    -- TOTAIS --
                    N.VL_DESCONTO,
                    N.VL_CONTABIL VL_LIQUIDO,
                    N.VL_CONTABIL + COALESCE(N.VL_DESCONTO, 0) - COALESCE(N.VL_DESPESA, 0) - COALESCE(N.VL_FRETE, 0) VL_BRUTO,
                    N.VL_NOTAFISCAL + COALESCE(N.VL_DESCONTO, 0) - COALESCE(N.VL_DESPESA, 0) - COALESCE(N.VL_FRETE, 0) VL_BRUTONOTA,

                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(ITN.CD_EMPRESA, ITN.NR_LANCAMENTO, ITN.CD_SERIE, ITN.TP_NOTA, ITN.CD_ITEM, 'Q', 'N', 'BI') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_BASEISS,
                    /******************************************************************************/
                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'S', 'BI') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_BASEISSRET,

                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'N', 'BR') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_BASESUBISS,

                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'S', 'BR') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_BASESUBISSRET,
                    /******************************************************************************/
                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'N', 'VI') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_ISS,

                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'S', 'VI') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_ISSRET,

                    (SELECT
                        MIN(ITNF.DS_SERVICO)
                    FROM ITEMNOTA ITN
                    INNER JOIN ITEM I ON (I.CD_ITEM = ITN.CD_ITEM)
                    INNER JOIN SERVICONFSE ITNF ON (ITNF.CD_SERVICO = I.CD_SERVICO)
                    INNER JOIN NFSE ON (NFSE.CD_EMPRESA = ITN.CD_EMPRESA
                            AND NFSE.NR_LANCAMENTO = ITN.NR_LANCAMENTO
                            AND NFSE.CD_SERIE = ITN.CD_SERIE
                            AND NFSE.TP_NOTA = ITN.TP_NOTA)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) DS_SERVICO,
                    F.DS_FORMAPAGTO,

                    (SELECT
                        MAX(IMP.PC_IMPOSTO)
                    FROM IMPOSTONOTA IMP
                    INNER JOIN IMPOSTO P ON (P.CD_IMPOSTO = IMP.CD_IMPOSTO)
                    WHERE
                        IMP.CD_EMPRESA = N.CD_EMPRESA
                        AND IMP.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND IMP.TP_NOTA = N.TP_NOTA
                        AND IMP.CD_SERIE = N.CD_SERIE
                        AND P.TP_IMPOSTO = 'Q') PC_ALIQUOTAISS,

                    IIF((SELECT
                            COUNT(IMP.CD_EMPRESA)
                        FROM IMPOSTONOTA IMP
                        INNER JOIN IMPOSTO P ON (P.CD_IMPOSTO = IMP.CD_IMPOSTO)
                        WHERE
                            IMP.CD_EMPRESA = N.CD_EMPRESA
                            AND IMP.NR_LANCAMENTO = N.NR_LANCAMENTO
                            AND IMP.TP_NOTA = N.TP_NOTA
                            AND IMP.CD_SERIE = N.CD_SERIE
                            AND P.TP_IMPOSTO = 'Q'
                            AND P.ST_RETIDO = 'S') = 0, _UTF8'Não Retido', 'Retido') DS_ST_RETIDO,
                    ROC.O_NR_EMBARQUE NR_ORDEMCARREG,
                    N.VL_NOTAFISCAL VL_LIQUIDONOTA,
                    NFSE.NR_NOTASERVICO,

                    (SELECT
                        SUM(V_VL_IMPOSTO)
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'J', NULL, 'VI') ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_IRRET,

                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST((SELECT
                                                                    SUM(IMP.VL_IBSMUN)
                                                                FROM ITEMNOTA ITN
                                                                LEFT JOIN IMPOSTONOTA IMP ON (ITN.CD_EMPRESA = IMP.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = IMP.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = IMP.TP_NOTA
                                                                    AND ITN.CD_SERIE = IMP.CD_SERIE
                                                                    AND ITN.CD_ITEM = IMP.CD_ITEM)
                                                                LEFT JOIN IMPOSTO ON (IMPOSTO.CD_IMPOSTO = IMP.CD_IMPOSTO)
                                                                WHERE
                                                                    ITN.CD_EMPRESA = N.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = N.TP_NOTA
                                                                    AND ITN.CD_SERIE = N.CD_SERIE
                                                                    AND IMPOSTO.TP_IMPOSTO = 'B') AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_IBSMUN,

                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST((SELECT
                                                                    SUM(IMP.VL_IBSUF)
                                                                FROM ITEMNOTA ITN
                                                                LEFT JOIN IMPOSTONOTA IMP ON (ITN.CD_EMPRESA = IMP.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = IMP.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = IMP.TP_NOTA
                                                                    AND ITN.CD_SERIE = IMP.CD_SERIE
                                                                    AND ITN.CD_ITEM = IMP.CD_ITEM)
                                                                LEFT JOIN IMPOSTO ON (IMPOSTO.CD_IMPOSTO = IMP.CD_IMPOSTO)
                                                                WHERE
                                                                    ITN.CD_EMPRESA = N.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = N.TP_NOTA
                                                                    AND ITN.CD_SERIE = N.CD_SERIE
                                                                    AND IMPOSTO.TP_IMPOSTO = 'B') AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_IBSUF,

                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST((SELECT
                                                                    SUM(IMP.VL_CBSUNIAO)
                                                                FROM ITEMNOTA ITN
                                                                LEFT JOIN IMPOSTONOTA IMP ON (ITN.CD_EMPRESA = IMP.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = IMP.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = IMP.TP_NOTA
                                                                    AND ITN.CD_SERIE = IMP.CD_SERIE
                                                                    AND ITN.CD_ITEM = IMP.CD_ITEM)
                                                                LEFT JOIN IMPOSTO ON (IMPOSTO.CD_IMPOSTO = IMP.CD_IMPOSTO)
                                                                WHERE
                                                                    ITN.CD_EMPRESA = N.CD_EMPRESA
                                                                    AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                                                                    AND ITN.TP_NOTA = N.TP_NOTA
                                                                    AND ITN.CD_SERIE = N.CD_SERIE
                                                                    AND IMPOSTO.TP_IMPOSTO = 'Y') AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_CBS,

                    -- IBS/CBS (NT008 - Ticket 15466) --
                    COALESCE(IBSCBS.DS_CSTIBSCBS, '-') || ' / ' || COALESCE(CAST(IBSCBS.CD_CLASSTRIBIBSCBS AS VARCHAR(10)), '-') DS_CSTCLASSTRIB,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_IBSUF AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_IBSUF,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_IBSMUN AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_IBSMUN,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_ALIQEFETIBSUF AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_ALIQEFETIBSUF,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_ALIQEFETIBSMUN AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_ALIQEFETIBSMUN,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_REDALIQIBSCBS AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_REDALIQIBSCBS,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_CBS AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_CBS,
                    COALESCE(REPLACE(TRIM(CAST(CAST(IBSCBS.PC_ALIQEFETCBS AS NUMERIC(9,4)) AS VARCHAR(20))), '.', ',') || ' %', '-') PC_ALIQEFETCBS,
                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(IBSCBS.VL_TOTIBS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_TOTIBS,
                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(IBSCBS.VL_TOTIBSCBS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_TOTIBSCBS,
                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(N.VL_CONTABIL + COALESCE(IBSCBS.VL_TOTIBSCBS, 0) AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_LIQUIDOMAISIBSCBS,
                    -- VALOR TOTAL DA NFS-e (NT008) --
                    N.VL_NOTAFISCAL + COALESCE(N.VL_DESCONTO, 0) - COALESCE(N.VL_DESPESA, 0) - COALESCE(N.VL_FRETE, 0) VL_BRUTOSERVICO,
                    CAST(0.00 AS DOM_NUMERIC15_2) VL_DESCONTOCOND,
                    (SELECT
                        SUM(COALESCE(VISS.V_VL_IMPOSTO, 0) + COALESCE(VIR.V_VL_IMPOSTO, 0) + COALESCE(VP.V_VL_IMPOSTO, 0) + COALESCE(VC.V_VL_IMPOSTO, 0) + COALESCE(VL2.V_VL_IMPOSTO, 0))
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'Q', 'S', 'VI') VISS ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'J', NULL, 'VI') VIR ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'S', 'S', 'VI') VP ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'F', 'S', 'VI') VC ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'L', 'S', 'VI') VL2 ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_TOTALRET,
                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(IBSCBS.VL_BASEIBSCBS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_BASEIBSCBS,
                    COALESCE('R$ ' || REPLACE(TRIM(CAST(CAST(IBSCBS.VL_EXCLUIBSCBS AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ','), '-') VL_EXCLUIBSCBS,
                    MI.CD_IBGE CD_IBGEINCID,
                    '- / ' || COALESCE(CAST(MI.CD_IBGE AS VARCHAR(7)), '-') || ' / ' || COALESCE(MI.DS_MUNICIPIO, '-') || ' / ' || COALESCE(MI.SG_ESTADO, '-') DS_MUNINCID,

                    N.DS_OBSNOTA DS_OBSERVACAONF,
                    N.DS_OBSFISCAL DS_OBSFISCALNF,
                    LPAD(N.CD_EMPRESA, 9, '0') || LPAD(N.CD_PESSOA, 9, '0') || LPAD(N.CD_SERIE, 5, '-') || LPAD(COALESCE(N.NR_NOTAFOR, N.NR_NOTAFISCAL), 9, '0') DS_CODBARRASCANHOTO,

                    (SELECT (SUM(COALESCE(VI_PIS.V_VL_IMPOSTO, 0)) + SUM(COALESCE(VI_COFINS.V_VL_IMPOSTO, 0)) + SUM(COALESCE(VI_CSLL.V_VL_IMPOSTO, 0)))
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'S', 'S', 'VI') VI_PIS ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'F', 'S', 'VI') VI_COFINS ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'L', 'S', 'VI') VI_CSLL ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_PISCOFINSCSLLRET,

                    (SELECT
                        SUM(COALESCE(VI_PIS.V_VL_IMPOSTO, 0))
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'S', 'N', 'VI') VI_PIS ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_PISDEVIDO,

                    (SELECT
                        SUM(COALESCE(VI_COFINS.V_VL_IMPOSTO, 0))
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'F', 'N', 'VI') VI_COFINS ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_COFINSDEVIDO,

                    (SELECT
                        SUM(COALESCE(VI_PIS.V_VL_IMPOSTO, 0)) + SUM(COALESCE(VI_COFINS.V_VL_IMPOSTO, 0))
                    FROM ITEMNOTA ITN
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'S', 'N', 'VI') VI_PIS ON (1 = 1)
                    LEFT JOIN VALOR_IMPOSTO(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, ITN.CD_ITEM, 'F', 'N', 'VI') VI_COFINS ON (1 = 1)
                    WHERE
                        ITN.CD_EMPRESA = N.CD_EMPRESA
                        AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND ITN.TP_NOTA = N.TP_NOTA
                        AND ITN.CD_SERIE = N.CD_SERIE) VL_TOTTRIBFED,

                    N.DS_OBSNOTA DS_OBSNOTA_CAMPO,
                    (SELECT
                        LIST('Parcela ' || TRIM(CAST(RC.V_NR_PARCELA AS VARCHAR(10))) || ': R$ ' || TRIM(CAST(RC.V_VL_DOCUMENTO AS VARCHAR(20))) || ' Venc.: ' || FORMATA_DATA(COALESCE(RC.V_DT_VENCIMENTO, N.DT_EMISSAO), '%D/%M/%Y'), ASCII_CHAR(13) || ASCII_CHAR(10))
                    FROM RETORNA_CONTAS(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, N.CD_PESSOA, 6, 'S') RC) DS_PARCELAS_CAMPO,
                    NULLIF(TRIM(COALESCE(N.DS_OBSNOTA || ASCII_CHAR(13) || ASCII_CHAR(10), '') || COALESCE((SELECT
                                                                                                                LIST('Parcela ' || TRIM(CAST(RC.V_NR_PARCELA AS VARCHAR(10))) || ': R$ ' || TRIM(CAST(RC.V_VL_DOCUMENTO AS VARCHAR(20))) || ' Venc.: ' || FORMATA_DATA(COALESCE(RC.V_DT_VENCIMENTO, N.DT_EMISSAO), '%D/%M/%Y'), ASCII_CHAR(13) || ASCII_CHAR(10))
                                                                                                            FROM RETORNA_CONTAS(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, N.CD_PESSOA, 6, 'S') RC), '')), '') DS_OBSEPARCELAS_CAMPO,

                    -- INFORMACOES COMPLEMENTARES (NT008 2.1.12): Inf.Cont pipes, trunc 1997, Lei 12.741 --
                    (SELECT
                        CASE
                            WHEN CHAR_LENGTH(INF.P) > 1997 THEN SUBSTRING(INF.P FROM 1 FOR 1994) || '...'
                            ELSE INF.P
                        END
                    FROM (SELECT
                                REPLACE(REPLACE('Inf. Cont.: ' || REPLACE(REPLACE(TRIM(COALESCE(TRIM(OBS.V_DS_OBSNOTA), '') || COALESCE(' | ' || NULLIF(TRIM(CAST(OBS.V_DS_OBSFISCAL AS VARCHAR(1000))), ''), '') || COALESCE(' | ' ||(SELECT
                                                                                                                                                                                                                                            LIST('Parc. ' || TRIM(CAST(RC.V_NR_PARCELA AS VARCHAR(10))) || ': R$ ' || TRIM(CAST(RC.V_VL_DOCUMENTO AS VARCHAR(20))) || ' Venc.: ' || FORMATA_DATA(COALESCE(RC.V_DT_VENCIMENTO, N.DT_EMISSAO), '%D/%M/%Y'), ' | ')
                                                                                                                                                                                                                                        FROM RETORNA_CONTAS(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, N.CD_PESSOA, 6, 'S') RC), '')), ASCII_CHAR(13), ''), ASCII_CHAR(10), ' | '), ' |  | ', ' | '), 'Inf. Cont.:  | ', 'Inf. Cont.: ') AS P
                            FROM RDB\$DATABASE) INF) || ASCII_CHAR(13) || ASCII_CHAR(10) ||(SELECT
                                                                                                'Totais aproximados dos Tributos cfe. Lei n. 12.741/2012: Federais: R$ ' || REPLACE(TRIM(CAST(CAST(COALESCE(SUM(ITN.VL_TOTAL * COALESCE(IB.PC_NACIONALFED, 0) / 100), 0) AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ',') || '; Estaduais: R$ ' || REPLACE(TRIM(CAST(CAST(COALESCE(SUM(ITN.VL_TOTAL * COALESCE(IB.PC_ESTADUAL, 0) / 100), 0) AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ',') || '; Municipais: R$ ' || REPLACE(TRIM(CAST(CAST(COALESCE(SUM(ITN.VL_TOTAL * COALESCE(IB.PC_MUNICIPAL, 0) / 100), 0) AS NUMERIC(15,2)) AS VARCHAR(20))), '.', ',') || ';'
                                                                                            FROM ITEMNOTA ITN
                                                                                            INNER JOIN ITEM I ON I.CD_ITEM = ITN.CD_ITEM
                                                                                            LEFT JOIN IBPTALIQUOTA IB ON (IB.NR_NCM = I.NR_NCM
                                                                                                AND IB.SG_ESTADO = ME.SG_ESTADO)
                                                                                            WHERE
                                                                                                ITN.CD_EMPRESA = N.CD_EMPRESA
                                                                                                AND ITN.NR_LANCAMENTO = N.NR_LANCAMENTO
                                                                                                AND ITN.TP_NOTA = N.TP_NOTA
                                                                                                AND ITN.CD_SERIE = N.CD_SERIE) DS_INFOCOMPLEMENTAR

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
                                            WHERE
                                                ENDER.CD_PESSOA = EE.CD_PESSOA))
                INNER JOIN MUNICIPIO ME ON (EE.CD_MUNICIPIO = ME.CD_MUNICIPIO)
                INNER JOIN ESTADO ESE ON (ESE.SG_ESTADO = ME.SG_ESTADO)
                LEFT JOIN PAISBACEN PBE ON (PBE.ID = ESE.IDPAIS)
                LEFT JOIN CNAE ON (CNAE.CD_CNAE = E.CD_CNAEPRIN)

                INNER JOIN CONDPAGTO C ON (C.CD_CONDPAGTO = N.CD_CONDPAGTO)
                INNER JOIN PARMFATUR PA ON (PA.CD_EMPRESA = N.CD_EMPRESA)
                INNER JOIN USUARIO U ON (U.CD_USUARIO = N.CD_USUARIO)
                LEFT JOIN PESSOA PU ON (PU.CD_PESSOA = U.CD_PESSOA)
                LEFT JOIN PESSOA V ON (V.CD_PESSOA = N.CD_VENDEDOR)
                LEFT JOIN CONFIGNFSE CF ON (CF.CD_EMPRESA = N.CD_EMPRESA)
                LEFT JOIN NFSE ON (NFSE.CD_EMPRESA = N.CD_EMPRESA
                        AND NFSE.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND NFSE.CD_SERIE = N.CD_SERIE
                        AND NFSE.TP_NOTA = N.TP_NOTA)
                LEFT JOIN PROVEDOREMPRESA PRE ON (PRE.CD_EMPRESA = CF.CD_EMPRESA)
                LEFT JOIN LOTERPS LR ON (LR.CD_EMPRESA = NFSE.CD_EMPRESA
                        AND LR.NR_LOTE = NFSE.NR_LOTE)
                LEFT JOIN FORMAPAGTO F ON (F.CD_FORMAPAGTO = N.CD_FORMAPAGTO)
                LEFT JOIN RETORNA_OBSNOTA(N.CD_EMPRESA, N.NR_LANCAMENTO, N.CD_SERIE, N.TP_NOTA, N.DS_OBSERVACAO, N.CD_VENDEDOR, N.CD_USUARIO, '4', 80) OBS ON (1 = 1)
                LEFT JOIN RETORNA_ORDEMCARREG(N.CD_EMPRESA, N.NR_LANCAMENTO, N.TP_NOTA, N.CD_SERIE) ROC ON (1 = 1)
                        -- IBS/CBS agregado por nota (NT008 - Ticket 15466) --
                LEFT JOIN(SELECT
                                IMP.CD_EMPRESA,
                                IMP.NR_LANCAMENTO,
                                IMP.TP_NOTA,
                                IMP.CD_SERIE,
                                MAX(CST.DS_CST) DS_CSTIBSCBS,
                                MAX(IMP.CD_CLASSTRIB) CD_CLASSTRIBIBSCBS,
                                MAX(IIF(P.TP_IMPOSTO = 'B', IMP.PC_IBSUF, NULL)) PC_IBSUF,
                                MAX(IIF(P.TP_IMPOSTO = 'B', IMP.PC_IBSMUN, NULL)) PC_IBSMUN,
                                MAX(IIF(P.TP_IMPOSTO = 'B', IMP.PC_ALIQEFETIBSUF, NULL)) PC_ALIQEFETIBSUF,
                                MAX(IIF(P.TP_IMPOSTO = 'B', IMP.PC_ALIQEFETIBSMUN, NULL)) PC_ALIQEFETIBSMUN,
                                MAX(IMP.PC_REDALIQIBSCBS) PC_REDALIQIBSCBS,
                                MAX(IIF(P.TP_IMPOSTO = 'Y', IMP.PC_CBSUNIAO, NULL)) PC_CBS,
                                MAX(IIF(P.TP_IMPOSTO = 'Y', IMP.PC_ALIQEFETIBSCBS, NULL)) PC_ALIQEFETCBS,
                                SUM(IIF(P.TP_IMPOSTO = 'B', COALESCE(IMP.VL_BASE, 0), 0)) VL_BASEIBSCBS,
                                SUM(IIF(P.TP_IMPOSTO = 'B', COALESCE(IMP.VL_ISENTO, 0) + COALESCE(IMP.VL_OUTRAS, 0), 0)) VL_EXCLUIBSCBS,
                                SUM(IIF(P.TP_IMPOSTO = 'B', COALESCE(IMP.VL_IBSMUN, 0) + COALESCE(IMP.VL_IBSUF, 0), 0)) VL_TOTIBS,
                                SUM(
                                CASE
                                WHEN P.TP_IMPOSTO = 'B' THEN COALESCE(IMP.VL_IBSMUN, 0) + COALESCE(IMP.VL_IBSUF, 0)
                                WHEN P.TP_IMPOSTO = 'Y' THEN COALESCE(IMP.VL_CBSUNIAO, 0)
                                ELSE 0
                                END) VL_TOTIBSCBS
                            FROM IMPOSTONOTA IMP
                            JOIN IMPOSTO P ON (P.CD_IMPOSTO = IMP.CD_IMPOSTO)
                            LEFT JOIN CST_IBSCBS CST ON (CST.CD_CST = IMP.CD_CSTIBSCBS)
                            WHERE
                                P.TP_IMPOSTO IN ('B', 'Y')
                            GROUP BY IMP.CD_EMPRESA,
                                IMP.NR_LANCAMENTO,
                                IMP.TP_NOTA,
                                IMP.CD_SERIE) IBSCBS ON (IBSCBS.CD_EMPRESA = N.CD_EMPRESA
                        AND IBSCBS.NR_LANCAMENTO = N.NR_LANCAMENTO
                        AND IBSCBS.TP_NOTA = N.TP_NOTA
                        AND IBSCBS.CD_SERIE = N.CD_SERIE)
                LEFT JOIN MUNICIPIO MI ON (MI.CD_MUNICIPIO = N.CD_MUNPRESTACAO)
                WHERE N.TP_NOTA = 'S'
                    AND N.CD_SERIE = 'F3'
                    AND N.ST_NOTA = 'V'
                    " . ($cd_pessoa != null ? " AND N.CD_PESSOA in ($cd_pessoa)" : "") . "
                    " . ($nr_nota != null ? " AND N.NR_NOTAFISCAL in ($nr_nota)" : "") . "
                    " . ($nr_lancamento != null ? " AND N.NR_LANCAMENTO = " . $nr_lancamento : "") . "
                    " . ($dt_registro_min != null ? " AND N.DT_REGISTRO > '" . $dt_registro_min . "'" : "") . "
                ) X
            LEFT JOIN RETORNA_SERVICONOTALNF230(X.CD_EMPRESA, X.NR_LANCAMENTO, X.TP_NOTA, X.CD_SERIE) R ON (1 = 1)
            ";

        $data = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($data);
    }
}
