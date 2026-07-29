@extends('layouts.master-simple')

{{--
    Layout de Boleto — ATZ (fiel ao PDF original "BOLETO_1.pdf" / Santander)

    Estrutura montada com tabelas HTML para máxima fidelidade de bordas/alinhamentos
    e compatibilidade com SnappyPDF / wkhtmltopdf.

    Três blocos destacáveis, na ordem do documento original:
      1. Recibo do Pagador
      2. Ficha de Compensação (com linha digitável e código de barras)
      3. Comprovante de Entrega

    Contrato de dados (o mesmo já usado por admin.cliente.layout-boleto):
      $boleto         → objeto com os campos do título
      $codigo_barras  → HTML do código de barras (Helper::codigoBarrasHtml)
--}}

@section('content')

    <div class="boleto">

        {{-- ============================================================
             BLOCO 1 — RECIBO DO PAGADOR
        ============================================================ --}}
        <div class="">

            {{-- Cabeçalho: logo do banco | código | título --}}
            <table class="w100 cabecalho">
                <tr>
                    <td class="br nome-banco" style="width: 18%">
                        @isset($logo_banco)
                            <img src="{{ $logo_banco }}" alt="{{ $boleto->DS_BANCO }}" class="logo-banco">
                        @else
                            {{ $boleto->DS_BANCO }}
                        @endisset
                    </td>
                    <td class="br text-center cod-banco" style="width: 12%">{{ $boleto->DS_CODIGOBANCO }}</td>
                    <td class="text-right titulo-bloco">Recibo do Pagador</td>
                </tr>
            </table>

            {{-- Corpo --}}
            <table class="w100 corpo">
                {{-- Local de Pagamento / Vencimento --}}
                <tr>
                    <td class="br bb" style="width: 70%">
                        <span class="rot">Local de Pagamento</span>
                        <span
                            class="val">{{ $boleto->DS_LOCALPAGAMENTO ?? 'PAGÁVEL PREFERENCIALMENTE NAS AGÊNCIAS SANTANDER' }}</span>
                    </td>
                    <td class="bb" style="width: 25%">
                        <span class="rot">Vencimento</span>
                        <span class="val val-b text-right">{{ $boleto->DT_VENC }}</span>
                    </td>
                </tr>

                {{-- Beneficiário / CPF-CNPJ / Agência-Código --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br" style="width: 66%">
                                    <span class="rot">Beneficiário</span>
                                    <span class="val">{{ $boleto->NM_CEDENTE }}</span>
                                </td>
                                <td>
                                    <span class="rot">CPF/CNPJ do Beneficiário</span>
                                    <span class="val text-center">{{ $boleto->NR_CNPJCPFCEDENTE }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">Agência / Código Beneficiário</span>
                        <span class="val text-right">{{ $boleto->DS_AGENCIACODIGOCEDENTE }}</span>
                    </td>
                </tr>

                {{-- Endereço do Beneficiário / Nosso Número --}}
                <tr>
                    <td class="br bb">
                        <span class="rot">Endereço do Beneficiário</span>
                        <span class="val">{{ $boleto->DS_ENDERECOCEDENTE }}</span>
                    </td>
                    <td class="bb">
                        <span class="rot">Nosso Número</span>
                        <span class="val text-right">{{ $boleto->NR_NOSSONUMERO }}</span>
                    </td>
                </tr>

                {{-- Dados do documento / Valor do Documento --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br text-center" style="width: 20%">
                                    <span class="rot text-left">Data do Documento</span>
                                    <span class="val">{{ $boleto->DT_DOCUMENTO }}</span>
                                </td>
                                <td class="br text-center" style="width: 24%">
                                    <span class="rot text-left">Número do Documento</span>
                                    <span class="val">{{ $boleto->NR_DOC }}</span>
                                </td>
                                <td class="br text-center" style="width: 15%">
                                    <span class="rot text-left">Espécie Doc.</span>
                                    <span class="val">{{ $boleto->DS_ESPECIE }}</span>
                                </td>
                                <td class="br text-center" style="width: 13%">
                                    <span class="rot text-left">Aceite</span>
                                    <span class="val">{{ $boleto->TP_ACEITE }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="rot text-left">Data do Processamento</span>
                                    <span class="val">{{ $boleto->DT_PROCESSAMENTO }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">(=) Valor do Documento</span>
                        <span class="val val-b text-right">{{ number_format($boleto->VL_DOCUMENTO, 2, ',', '.') }}</span>
                    </td>
                </tr>

                {{-- Uso do banco / Desconto --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br" style="width: 20%">
                                    <span class="rot">Uso do Banco</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                                <td class="br text-center" style="width: 12%">
                                    <span class="rot text-left">Carteira</span>
                                    <span class="val">{{ $boleto->NR_CARTEIRA }}</span>
                                </td>
                                <td class="br text-center" style="width: 12%">
                                    <span class="rot text-left">Espécie</span>
                                    <span class="val">{{ $boleto->DS_MOEDA }}</span>
                                </td>
                                <td class="br" style="width: 28%">
                                    <span class="rot">Quantidade</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                                <td>
                                    <span class="rot">Valor</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">(-) Desconto / Abatimento</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>

                {{-- Instruções (ocupa 4 linhas à esquerda) + caixas de valores à direita --}}
                <tr>
                    <td class="br bb instrucoes" rowspan="4">
                        <span class="rot">Instruções (Todas as informações deste bloqueto são de exclusiva
                            responsabilidade do cedente.)</span>
                        <div class="val-instrucao">{!! nl2br(e($boleto->DS_INSTRUCAO)) !!}</div>
                    </td>
                    <td class="bb">
                        <span class="rot">(-) Outras Deduções</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(+) Mora / Multa (Juros)</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(+) Outros Acréscimos</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb vl-cobrado">
                        <span class="rot">(=) Valor Cobrado</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>

                {{-- Pagador / CPF-CNPJ + Código de Baixa --}}
                <tr>
                    <td class="br bb pagador">
                        <span class="rot">Pagador</span>
                        <span class="val">{{ $boleto->NM_SACADO }}</span>
                        <span class="val">{{ $boleto->DS_ENDERECOSACADO }}</span>
                        <span class="val">{{ $boleto->DS_CEPCIDADESACADO }}</span>
                    </td>
                    <td class="bb p0 cel-baixa-wrap">
                        <div class="cel-baixa">
                            <span class="rot">CPF/CNPJ do Pagador</span>
                            <span class="val">{{ $boleto->NR_CNPJCPFSACADO }}</span>
                        </div>
                        <div class="cel-baixa">
                            <span class="rot">Código de Baixa</span>
                            <span class="val">{{ $boleto->NR_NOSSONUMERO }}</span>
                        </div>
                    </td>
                </tr>

                {{-- Rodapé: cheque / autenticação --}}
                <tr class="bb rodape">
                    <td class="rodape-cheque">
                        <span class="rot">Recebimento através do cheque núm. _______________ do banco
                            _______________ sacado.</span>
                        <span class="rot">Esta quitação só terá validade após o pagamento do cheque</span>
                    </td>
                    <td>
                        <span class="rot">Autenticação mecânica</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Linha destacável --}}
        <div class="tracejado"></div>

        {{-- ============================================================
             BLOCO 2 — FICHA DE COMPENSAÇÃO
        ============================================================ --}}
        <div class="">

            {{-- Cabeçalho: logo do banco | código | linha digitável --}}
            <table class="w100 cabecalho">
                <tr>
                    <td class="br nome-banco" style="width: 18%">
                        @isset($logo_banco)
                            <img src="{{ $logo_banco }}" alt="{{ $boleto->DS_BANCO }}" class="logo-banco">
                        @else
                            {{ $boleto->DS_BANCO }}
                        @endisset
                    </td>
                    <td class="br text-center cod-banco" style="width: 12%">{{ $boleto->DS_CODIGOBANCO }}</td>
                    <td class="text-center linha-digitavel">{{ $boleto->DS_LINHADIGITAVEL }}</td>
                </tr>
            </table>

            {{-- Corpo --}}
            <table class="w100 corpo">
                {{-- Local de Pagamento / Vencimento --}}
                <tr>
                    <td class="br bb" style="width: 70%">
                        <span class="rot">Local de Pagamento</span>
                        <span
                            class="val">{{ $boleto->DS_LOCALPAGAMENTO ?? 'PAGÁVEL PREFERENCIALMENTE NAS AGÊNCIAS SANTANDER' }}</span>
                    </td>
                    <td class="bb" style="width: 25%">
                        <span class="rot">Vencimento</span>
                        <span class="val val-b text-right">{{ $boleto->DT_VENC }}</span>
                    </td>
                </tr>

                {{-- Beneficiário / CPF-CNPJ / Agência-Código --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br" style="width: 66%">
                                    <span class="rot">Beneficiário</span>
                                    <span class="val">{{ $boleto->NM_CEDENTE }}</span>
                                </td>
                                <td>
                                    <span class="rot">CPF/CNPJ do Beneficiário</span>
                                    <span class="val text-center">{{ $boleto->NR_CNPJCPFCEDENTE }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">Agência / Código Beneficiário</span>
                        <span class="val text-right">{{ $boleto->DS_AGENCIACODIGOCEDENTE }}</span>
                    </td>
                </tr>

                {{-- Dados do documento / Nosso Número --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br text-center" style="width: 20%">
                                    <span class="rot text-left">Data do Documento</span>
                                    <span class="val">{{ $boleto->DT_DOCUMENTO }}</span>
                                </td>
                                <td class="br text-center" style="width: 24%">
                                    <span class="rot text-left">Número do Documento</span>
                                    <span class="val">{{ $boleto->NR_DOC }}</span>
                                </td>
                                <td class="br text-center" style="width: 15%">
                                    <span class="rot text-left">Espécie Doc.</span>
                                    <span class="val">{{ $boleto->DS_ESPECIE }}</span>
                                </td>
                                <td class="br text-center" style="width: 13%">
                                    <span class="rot text-left">Aceite</span>
                                    <span class="val">{{ $boleto->TP_ACEITE }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="rot text-left">Data do Processamento</span>
                                    <span class="val">{{ $boleto->DT_PROCESSAMENTO }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">Nosso Número</span>
                        <span class="val text-right">{{ $boleto->NR_NOSSONUMERO }}</span>
                    </td>
                </tr>

                {{-- Uso do banco / Valor do Documento --}}
                <tr>
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br" style="width: 20%">
                                    <span class="rot">Uso do Banco</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                                <td class="br text-center" style="width: 12%">
                                    <span class="rot text-left">Carteira</span>
                                    <span class="val">{{ $boleto->NR_CARTEIRA }}</span>
                                </td>
                                <td class="br text-center" style="width: 12%">
                                    <span class="rot text-left">Espécie</span>
                                    <span class="val">{{ $boleto->DS_MOEDA }}</span>
                                </td>
                                <td class="br" style="width: 28%">
                                    <span class="rot">Quantidade</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                                <td>
                                    <span class="rot">Valor</span>
                                    <span class="val">&nbsp;</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">(=) Valor do Documento</span>
                        <span class="val val-b text-right">{{ number_format($boleto->VL_DOCUMENTO, 2, ',', '.') }}</span>
                    </td>
                </tr>

                {{-- Instruções (ocupa 5 linhas à esquerda) + caixas de valores à direita --}}
                <tr>
                    <td class="br bb instrucoes" rowspan="5">
                        <span class="rot">Instruções (Todas as informações deste bloqueto são de exclusiva
                            responsabilidade do cedente.)</span>
                        <div class="val-instrucao">{!! nl2br(e($boleto->DS_INSTRUCAO)) !!}</div>
                    </td>
                    <td class="bb">
                        <span class="rot">(-) Desconto / Abatimento</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(-) Outras Deduções</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(+) Mora / Multa (Juros)</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(+) Outros Acréscimos</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <td class="bb">
                        <span class="rot">(=) Valor Cobrado</span>
                        <span class="val">&nbsp;</span>
                    </td>
                </tr>

                {{-- Pagador / CPF-CNPJ + Código de Baixa --}}
                <tr>
                    <td class="br bb pagador">
                        <span class="rot">Pagador</span>
                        <span class="val">{{ $boleto->NM_SACADO }}</span>
                        <span class="val">{{ $boleto->DS_ENDERECOSACADO }}</span>
                        <span class="val">{{ $boleto->DS_CEPCIDADESACADO }}</span>
                    </td>
                    <td class="bb p0 cel-baixa-wrap">
                        <div class="cel-baixa">
                            <span class="rot">CPF/CNPJ do Pagador</span>
                            <span class="val text-center">{{ $boleto->NR_CNPJCPFSACADO }}</span>
                        </div>
                        <div class="cel-baixa">
                            <span class="rot">Código de Baixa</span>
                            <span class="val text-center">{{ $boleto->NR_NOSSONUMERO }}</span>
                        </div>
                    </td>
                </tr>

                {{-- Código de barras / autenticação --}}
                <tr>
                    <td class="cel-barras">
                        {!! $codigo_barras !!}
                    </td>
                    <td class="autenticacao">
                        <span class="rot">Autenticação mecânica - Ficha de Compensação</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Linha destacável --}}
        <div class="tracejado"></div>

        {{-- ============================================================
             BLOCO 3 — COMPROVANTE DE ENTREGA
        ============================================================ --}}
        <div class="">

            {{-- Cabeçalho: logo do banco | código | título --}}
            <table class="w100 cabecalho">
                <tr>
                    <td class="br nome-banco" style="width: 18%">
                        @isset($logo_banco)
                            <img src="{{ $logo_banco }}" alt="{{ $boleto->DS_BANCO }}" class="logo-banco">
                        @else
                            {{ $boleto->DS_BANCO }}
                        @endisset
                    </td>
                    <td class="br text-center cod-banco" style="width: 12%">{{ $boleto->DS_CODIGOBANCO }}</td>
                    <td class="text-right titulo-bloco">Comprovante de Entrega</td>
                </tr>
            </table>

            {{-- Corpo --}}
            <table class="w100 corpo">
                {{-- Beneficiário / CPF-CNPJ / Vencimento --}}
                <tr>
                    <td class="br bb p0" style="width: 70%">
                        <table class="w100">
                            <tr>
                                <td class="br" style="width: 66%">
                                    <span class="rot">Beneficiário</span>
                                    <span class="val">{{ $boleto->NM_CEDENTE }}</span>
                                </td>
                                <td>
                                    <span class="rot">CPF/CNPJ do Beneficiário</span>
                                    <span class="val text-center">{{ $boleto->NR_CNPJCPFCEDENTE }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb" style="width: 25%">
                        <span class="rot">Vencimento</span>
                        <span class="val val-b text-right">{{ $boleto->DT_VENC }}</span>
                    </td>
                </tr>

                {{-- Pagador / Agência-Código --}}
                <tr>
                    <td class="br bb">
                        <span class="rot">Pagador</span>
                        <span class="val">{{ $boleto->NM_SACADO }}</span>
                    </td>
                    <td class="bb">
                        <span class="rot">Agência / Código Beneficiário</span>
                        <span class="val text-right">{{ $boleto->DS_AGENCIACODIGOCEDENTE }}</span>
                    </td>
                </tr>

                {{-- Dados do documento / Nosso Número --}}
                <tr class="bb">
                    <td class="br bb p0">
                        <table class="w100">
                            <tr>
                                <td class="br text-center" style="width: 20%">
                                    <span class="rot text-left">Data do Documento</span>
                                    <span class="val">{{ $boleto->DT_DOCUMENTO }}</span>
                                </td>
                                <td class="br text-center" style="width: 24%">
                                    <span class="rot text-left">Número do Documento</span>
                                    <span class="val">{{ $boleto->NR_DOC }}</span>
                                </td>
                                <td class="br text-center" style="width: 15%">
                                    <span class="rot text-left">Espécie</span>
                                    <span class="val">{{ $boleto->DS_ESPECIE }}</span>
                                </td>
                                <td class="br text-center" style="width: 13%">
                                    <span class="rot text-left">Aceite</span>
                                    <span class="val">{{ $boleto->TP_ACEITE }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="rot text-left">Data do Processamento</span>
                                    <span class="val">{{ $boleto->DT_PROCESSAMENTO }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="bb">
                        <span class="rot">Nosso Número</span>
                        <span class="val text-right">{{ $boleto->NR_NOSSONUMERO }}</span>
                    </td>
                </tr>

                {{-- Recebimento / Valor do Documento --}}
                <tr>
                    <td class="br bb">
                        <span class="rot">Recebi(emos) o bloqueto / título com as características acima.</span>
                    </td>
                    <td class="bb">
                        <span class="rot">(=) Valor do Documento</span>
                        <span class="val val-b text-right">{{ number_format($boleto->VL_DOCUMENTO, 2, ',', '.') }}</span>
                    </td>
                </tr>

                {{-- Assinaturas --}}
                <tr class="bb">
                    <td class="p0" colspan="2">
                        <table class="w100">
                            <tr>
                                <td class="br cel-assinatura" style="width: 22%">
                                    <span class="rot">Data</span>
                                </td>
                                <td class="br cel-assinatura" style="width: 28%">
                                    <span class="rot">Assinatura</span>
                                </td>
                                <td class="br cel-assinatura" style="width: 22%">
                                    <span class="rot">Data</span>
                                </td>
                                <td class="cel-assinatura">
                                    <span class="rot">Entregador</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    </div>

@stop

@section('css')
    <style>
        /* ── Página A4 ─────────────────────────────────────────── */
        @page {
            size: A4;
            margin: 5mm 10mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        /* O master-simple aninha o conteúdo em .wrapper > .content >
               .container-fluid, e o AdminLTE dá padding lateral a esses containers.
               Somados à margem da página, empurram o boleto para dentro e sobram
               faixas brancas nas laterais. Zerados para a folha ocupar toda a
               área imprimível. */
        .wrapper,
        .content,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            max-width: none !important;
        }

        /* Largura relativa (não fixa em mm): assim o boleto acompanha
               exatamente a área imprimível definida pela margem do @page,
               sem sobrar nem estourar se a margem mudar. */
        .boleto {
            width: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        /* ── Blocos destacáveis ────────────────────────────────── */
        .boleto .bloco {
            border: 1px solid #000;
            page-break-inside: avoid;
        }

        .boleto .tracejado {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        /* ── Tabelas base ──────────────────────────────────────── */
        .boleto table.w100 {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .boleto table.w100 td {
            padding: 1px 4px;
        }

        .boleto td.p0 {
            padding: 0;
        }

        /* ── Bordas utilitárias ────────────────────────────────── */
        .boleto .br {
            border-right: 1px solid #000;
        }

        .boleto .bb {
            border-bottom: 1px solid #000;
        }

        /* ── Cabeçalho do bloco (logo / código / título) ───────── */
        .boleto .cabecalho {
            border-bottom: 1.2px solid #000;
        }

        .boleto .cabecalho td {
            vertical-align: middle;
            padding: 2px 6px;
        }

        .boleto .nome-banco {
            font-size: 16px;
            font-weight: bold;
            padding: 0;
        }

        .boleto .logo-banco {
            max-height: 22px;
            max-width: 100%;
            padding: 0;
        }

        .boleto .cod-banco {
            font-size: 30px;
            padding: 0px !important;
            vertical-align: bottom !important;
            line-height: 1;
        }

        .boleto .titulo-bloco {
            font-size: 22px;
        }

        .boleto .linha-digitavel {
            font-size: 22px;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        /* ── Campos: rótulo pequeno em cima, valor embaixo ─────── */
        .boleto .rot {
            display: block;
            font-size: 11px;
            line-height: 12px;
            padding-bottom: 4px
        }

        .boleto .val {
            display: block;
            font-size: 16px;
            line-height: 14px;
            padding-left: 6px;
        }

        .boleto .val-b {
            font-weight: bold;
        }

        .boleto .vl-cobrado {
            height: 70px !important;
            vertical-align: top !important;
        }

        /* Alinhamentos precisam vencer o alinhamento herdado da célula */
        .boleto .text-left {
            text-align: left;
        }

        .boleto .text-center {
            text-align: center;
        }

        .boleto .text-right {
            text-align: right;
            padding-right: 30px;
        }

        /* ── Alturas fixas (mantêm a proporção do boleto) ──────── */
        .boleto .corpo>tbody>tr>td {
            height: 30px;
        }

        .boleto .instrucoes {
            height: 130px;
            vertical-align: top;
        }

        .boleto .val-instrucao {
            font-size: 12px;
            line-height: 13px;
            padding-left: 6px;
        }

        .boleto .pagador {
            height: 58px;
        }

        .boleto .pagador .val {
            line-height: 17px;
            padding-bottom: 10px;
        }        

        .boleto .cel-baixa .val {
            padding-bottom: 10px;
        }

        /* vertical-align só funciona em table-cell (a <td>), não na <div>
           .cel-baixa (que é block) - por isso a regra vai aqui, na célula que
           envolve as duas divs, e não nelas mesmas. */
        .boleto .cel-baixa-wrap {
            vertical-align: top;
        }

        .boleto .rodape {
            height: 60px;
            vertical-align: top;
        }

        .boleto .rodape-cheque {
            height: 26px;
        }

        .boleto .cel-assinatura {
            height: 30px;
        }

        .boleto .autenticacao{
            vertical-align: top !important;
        }

        /* ── Código de barras ──────────────────────────────────── */
        /* As regras do .barcode agora vivem em boleto.css (fonte única,
           compartilhada com o boleto do portal). Aqui fica só a célula. */
        .boleto .cel-barras {
            height: 50px !important;
            vertical-align: middle;
            padding: 6px !important;
        }

        /* ── Impressão ─────────────────────────────────────────── */
        @media print {
            .page-break {
                page-break-before: always;
            }

            .no-break {
                page-break-inside: avoid;
            }

            .boleto table {
                width: 100%;
            }
        }
    </style>
@stop
