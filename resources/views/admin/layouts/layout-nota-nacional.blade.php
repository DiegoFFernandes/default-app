@extends('layouts.master-simple')

{{--
    Layout Nacional — DANFSe v2.0 (padrão nacional pós-Reforma Tributária, IBS/CBS)
    Estrutura em tabelas HTML (mesmo motivo do layout-nota-atz: fidelidade de
    bordas/alinhamento e compatibilidade com Chromium headless).
    Dados vêm de NotaCliente::getListNotaClienteNacional() + NotaLayoutDataNacional.
--}}

@section('title', 'Nota Fiscal ' . $nota->numero)

@section('content')

    <div class="danfse">
        @php
            // Paginação MANUAL - idêntica à do layout-nota-atz (mesma lógica de
            // agrupamento pneu+conserto e os mesmos dois limites de linha).
            $linhasPorPagina = $linhasPorPagina ?? 40;
            $linhasUltima = $linhasUltima ?? 24;

            $grupos = [];
            foreach ($itens as $item) {
                if (filled($item->seq) || empty($grupos)) {
                    $grupos[] = [];
                }
                $grupos[count($grupos) - 1][] = $item;
            }

            $paginasItens = [];
            $paginaAtual = [];
            $linhasAtual = 0;
            foreach ($grupos as $grupo) {
                $linhasGrupo = count($grupo);
                if ($paginaAtual && $linhasAtual + $linhasGrupo > $linhasPorPagina) {
                    $paginasItens[] = $paginaAtual;
                    $paginaAtual = [];
                    $linhasAtual = 0;
                }
                $paginaAtual[] = $grupo;
                $linhasAtual += $linhasGrupo;
            }
            if ($paginaAtual) {
                $paginasItens[] = $paginaAtual;
            }

            $ult = count($paginasItens) - 1;
            $linhasNaUlt = array_sum(array_map('count', $paginasItens[$ult]));
            if ($linhasNaUlt > $linhasUltima) {
                $paginasItens[] = [];
            }
            $paginasItens = $paginasItens ?: [[]];
        @endphp

        @foreach ($paginasItens as $itensPagina)
            {{-- Uma página física: cabeçalho completo + os itens deste bloco.
                 A quebra entre blocos é forçada pela classe .pagina no CSS.

                 A assinatura (só na última página) fica no fluxo normal, logo
                 após o rodapé - SEM tentar ancorá-la no rodapé físico da
                 folha. Três técnicas diferentes pra isso (position:absolute,
                 flex+margin-top:auto, altura de linha de tabela) funcionaram
                 no preview local (Chrome --screenshot, modo tela) mas NENHUMA
                 se comportou igual no PDF real do ChromePdfService
                 (--print-to-pdf): min-height/height em mm num container
                 simplesmente não "esticou" na paginação de impressão real do
                 Chromium, então a página sempre saiu do tamanho do conteúdo,
                 não dos 287mm. Decisão 28/08/2026: aceitar a posição natural
                 (sem sobreposição, sem bug) em vez de insistir numa técnica
                 CSS que não se comprova nesse pipeline - reabrir só se topar
                 medir a altura renderizada via protocolo de depuração do
                 Chrome (renderização em 2 passadas) no futuro. --}}
            <div class="pagina">
                @include('admin.layouts.partials.nota-nacional-cabecalho')

                {{-- DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS (itens deste bloco). Quando a
                     página não leva itens (rodapé empurrado para folha própria por
                     não caber junto da página anterior), pula a faixa e a tabela. --}}
                @if (count($itensPagina))
                    <div class="rotulo">Descrição do Serviço</div>
                    <table class="tb itens">
                        <thead>
                            <tr>
                                <th style="width: 4%">Seq</th>
                                <th style="width: 33%">Item</th>
                                <th style="width: 12%">Marca</th>
                                <th style="width: 10%">Modelo</th>
                                <th style="width: 10%">Série</th>
                                <th style="width: 6%" class="text-right">Fogo</th>
                                <th style="width: 6%" class="text-right">DOT</th>
                                <th style="width: 6%" class="text-right">Qtde</th>
                                <th style="width: 6%" class="text-right">VL Unit</th>
                                <th style="width: 7%" class="text-right">VL Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itensPagina as $grupo)
                                @foreach ($grupo as $item)
                                    <tr>
                                        <td>{{ $item->seq }}</td>
                                        <td>{{ $item->item }}</td>
                                        <td>{{ $item->marca }}</td>
                                        <td>{{ $item->modelo }}</td>
                                        <td>{{ $item->serie }}</td>
                                        <td class="text-right">{{ $item->fogo }}</td>
                                        <td class="text-right">{{ $item->dot }}</td>
                                        <td class="text-right">{{ number_format((float) $item->qtde, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format((float) $item->vlUnit, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format((float) $item->vlTotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Rodapé (tributação/totais/informações complementares) e a
                     assinatura só ao fim da última página --}}
                @if ($loop->last)
                    @include('admin.layouts.partials.nota-nacional-rodape')
                    @include('admin.layouts.partials.nota-nacional-assinatura')
                @endif
            </div>{{-- .pagina --}}
        @endforeach

    </div>

@stop

@section('css')
    <style>
        /* ── Página A4 ─────────────────────────────────────────────── */
        @page {
            size: A4;
            margin: 5mm 5mm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* 1px em vez de 0: dá respiro pra borda do .danfse (readicionada
           abaixo) não ficar encostada na margem física da página - sem isso,
           a impressão corta ~1px dela por arredondamento sub-pixel (mesmo
           problema já documentado no layout-nota-atz). O padding do próprio
           .danfse NÃO resolve isso: com box-sizing:border-box ele fica
           DENTRO da borda (dá respiro pro conteúdo, não pra borda em si) -
           o respiro precisa estar no pai. */
        .container-fluid {
            padding-left: 1px !important;
            padding-right: 1px !important;
        }

        .danfse {
            width: 100%;
            padding: 0 1px;
            margin: 0 auto;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            border: 1px solid #000;
        }

        .danfse,
        .danfse *,
        .danfse *::before,
        .danfse *::after {
            box-sizing: border-box;
        }

        /* ── Tabelas base ──────────────────────────────────────────── */
        .danfse table.tb {
            width: 100%;
        }

        .danfse table.tb td,
        .danfse table.tb th {
            padding: 2px 6px;
            vertical-align: top;
        }

        /* ── Bordas utilitárias ────────────────────────────────────── */
        .danfse .bt {
            border-top: 1px solid #000;
        }

        .danfse .bb {
            border-bottom: 1px solid #000;
        }

        .danfse .bl {
            border-left: 1px solid #000;
        }

        .danfse .br {
            border-right: 1px solid #000;
        }

        /* Grade: caixa com TODAS as células com borda - visual do DANFSe
                   oficial (cada bloco de campo é uma célula fechada, com o rótulo
                   pequeno em cima e o valor em negrito embaixo). */
        .danfse .grade {
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .danfse .grade td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: top;
        }

        /* Seção no padrão do DANFSe oficial: SEM borda nas células - só uma
                   linha horizontal de largura total marcando o início da seção. */
        .danfse .secao-nacional {
            border-collapse: collapse;
            border-top: 1px solid #000;
        }

        .danfse .secao-nacional td {
            padding: 3px 6px;
            vertical-align: top;
        }

        /* Título de seção do oficial: caixinha cinza que abraça só o texto,
                   na primeira coluna da própria grade (não uma faixa de largura total). */
        .danfse .titulo-secao {
            display: inline-block;
            background: #e8e8e8;
            /* padding: 1px 4px; */
        }


        /* Célula destacada em cinza (ex.: VALOR LÍQUIDO DA NFS-e + IBS/CBS). */
        .danfse .celula-destaque {
            background: #e8e8e8;
        }

        .danfse .rotulo {
            font-size: 11px;
            display: block;
            font-weight: bold;            
        }

        .danfse .valor {
            font-size: 12px;
            display: block;           
        }

        /* Cinza reutilizável (mesmo tom da faixa) - usado em células isoladas
                   que precisam do mesmo destaque, sem ser uma faixa de seção inteira. */
        .danfse .bg-cinza {
            background: #e8e8e8;
        }

        /* ── Faixas de seção (barra cinza) ──────────────────────────── */
        .danfse .faixa {
            background: #bdbcbc;
            font-weight: bold;
            font-size: 12px;
            padding: 2px 6px;
            margin-top: 4px;
            /* border: 1px solid #000; */
            border-bottom: none;
        }

        /* Uma faixa "solta" (sem grade logo abaixo) fecha a própria borda. */
        .danfse .faixa.solta {
            border-bottom: 1px solid #000;
        }

        .danfse .secao {
            margin-top: 0;
            margin-bottom: 4px;
        }

        /* ── Cabeçalho DANFSe ────────────────────────────────────────── */
        .danfse .titulo-danfse {
            background: #e8e8e8;
            border-bottom: 1px solid #000;
            margin-top: 1px;
        }

        /* O respiro vertical do título vai nas CÉLULAS, não na tabela: o
               AdminLTE aplica table{border-collapse:collapse} (com !important no
               print) e, nesse modo, o CSS manda ignorar padding no <table>.
               O .tb no seletor é para vencer a especificidade de
               ".danfse table.tb td", que fixa padding: 2px 6px. */
        .danfse table.tb.titulo-danfse td {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        /* Linha compacta: reduz a altura das linhas via padding do <td> (tr
           não aceita padding). Aplicada em quase toda a nota - só o bloco
           TÍTULO DANFSe + AMBIENTE fica de fora (usa o padding maior acima).
           ".tb" no seletor garante especificidade maior que
           ".danfse table.tb td", senão a regra de 2px 6px venceria. */
        .danfse table.tb.linha-compacta td {
            padding-top: 1px;
            padding-bottom: 1px;
        }

        .danfse .logo-nfse-nacional {
            max-height: 40px;
        }

        .danfse .topo-titulo {
            font-size: 15px;
            font-weight: bold;
        }

        .danfse .topo-subtitulo {
            font-size: 15px;
            font-weight: bold;
        }

        .danfse .qrcode {
            text-align: center;
            padding-top: 10px !important;
        }

        .danfse .qrcode svg {
            width: 90px;
            height: 90px;
        }

        /* Banners centrais (DESTINATÁRIO/INTERMEDIÁRIO): no oficial são só
                   linhas de texto centralizadas separadas por linhas horizontais. */
        .danfse .banner-nao-identificado {
            border-top: 1px solid #000;
            text-align: center;
            padding: 1px 0;
            font-size: 11px;
        }

        /* Nome do serviço - texto livre abaixo da grade SERVIÇO PRESTADO. */
        .danfse .servico-nome {
            padding: 2px 6px 4px 6px;
        }

        /* ── Paginação manual ────────────────────────────────────────── */
        .danfse .pagina {
            break-after: page;
            page-break-after: always;
        }

        .danfse .pagina:last-child {
            break-after: auto;
            page-break-after: auto;
        }

        /* Assinatura no fluxo normal, logo após o rodapé (sem tentar ancorar
           no rodapé físico da folha - ver comentário no loop de páginas acima
           sobre as 3 técnicas que não se comprovaram no --print-to-pdf real).
           Só uma margem simples pra separar de "Informações Complementares". */
        /* Margem soma por fora do width:100% da .tb (não é box-sizing:content
           ali dentro, é o box do PRÓPRIO elemento que cresce) - sem reduzir a
           largura, 3px de cada lado ultrapassariam a borda do .danfse em vez
           de respeitá-la. table.tb.assinatura pra vencer a especificidade de
           ".danfse table.tb" (que fixa width:100%). */
        .danfse table.tb.assinatura {
            width: calc(100% - 6px);
            margin-top: 6px;
            margin-bottom: 6px;
            margin-left: 3px;
            margin-right: 3px;
        }

        /* ── Tabela de itens (mesmo visual do layout tradicional) ──────── */
        .danfse .itens th {
            text-align: left;
            font-weight: bold;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        .danfse .itens td {
            padding-top: 1px;
            padding-bottom: 1px;
        }

        .danfse .itens td,
        .danfse .itens th {
            white-space: nowrap;
        }

        .danfse .itens tbody tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* ── Informações complementares (texto livre, sem caixa) ─────── */
        .danfse .info-complementar {
            padding: 2px 6px;
            white-space: pre-line;
            font-size: 11px;
            line-height: 14px;
        }

        /* ── Utilitários ───────────────────────────────────────────── */
        .danfse .text-center {
            text-align: center;
        }

        .danfse .text-right {
            text-align: right;
        }

        .danfse .nowrap {
            white-space: nowrap;
        }

        .danfse .m-0 {
            margin: 0;
        }

        .danfse .mt-1 {
            margin-top: 4px;
        }

        .danfse .mt-2 {
            margin-top: 8px;
        }

        .danfse .mb-2 {
            margin-bottom: 4px;
        }
        .danfse .fs-10 {
            font-size: 10px;
            text-align: justify
        }

        .danfse .fs-9 {
            font-size: 9px;            
        }

        .danfse .fs-11 {
            font-size: 12px;
        }

        .danfse .fs-12 {
            font-size: 12px;
        }

        @media print {
            .danfse table {
                width: 100%;
            }
        }
    </style>
@stop
