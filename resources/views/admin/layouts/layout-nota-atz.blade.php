@extends('layouts.master-simple')

{{--
    Layout 1 — NFS-e (fiel ao PDF original "Nota 11926")
    Estrutura montada com tabelas HTML para máxima fidelidade de bordas/alinhamentos
    e compatibilidade com SnappyPDF / wkhtmltopdf.
--}}

{{-- O master-simple usa 'Boleto' como título padrão; o Chromium grava esse
     <title> como metadado do PDF, que aparece no visualizador. --}}
@section('title', 'Nota Fiscal ' . $nota->numero)

@section('content')

    <div class="nfse">
        @php
            // Paginação MANUAL (mesma lógica do layout-nota-atz-emp-3): o Chromium
            // não repete cabeçalho alto entre páginas, então quebramos os itens em
            // blocos e desenhamos o cabeçalho completo em cada um (idêntico ao PDF
            // oficial). Quebra por LINHA física (pneu e conserto ocupam linha).
            // Dois limites, medidos na nossa fonte:
            //   $linhasPorPagina -> páginas normais (só cabeçalho + itens)
            //   $linhasUltima    -> última página, que ainda leva o rodapé (cabe menos)
            $linhasPorPagina = $linhasPorPagina ?? 40;
            $linhasUltima    = $linhasUltima ?? 24;

            // Agrupa cada pneu (linha com SEQ) com seus consertos (linhas de SEQ
            // vazio logo abaixo); o grupo nunca é partido entre páginas.
            $grupos = [];
            foreach ($itens as $item) {
                if (filled($item->seq) || empty($grupos)) {
                    $grupos[] = [];
                }
                $grupos[count($grupos) - 1][] = $item;
            }

            // Enche as páginas normais até $linhasPorPagina (sem partir grupo).
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

            // A última página leva o rodapé. Se os itens dela passarem de
            // $linhasUltima, o rodapé não cabe ao lado — em vez de redistribuir
            // os itens, a página de itens fica cheia como está e o rodapé vai
            // sozinho para uma página extra (só cabeçalho + rodapé, sem itens).
            $ult = count($paginasItens) - 1;
            $linhasNaUlt = array_sum(array_map('count', $paginasItens[$ult]));
            if ($linhasNaUlt > $linhasUltima) {
                $paginasItens[] = [];
            }
            $paginasItens = $paginasItens ?: [[]];
        @endphp

        @foreach ($paginasItens as $itensPagina)
            {{-- Uma página física: cabeçalho completo + os itens deste bloco.
                 A quebra entre blocos é forçada pela classe .pagina no CSS. --}}
            <div class="pagina">

                @include('admin.layouts.partials.nota-atz-cabecalho')

                {{-- DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS (itens deste bloco). Quando a
                     página não leva itens (rodapé empurrado para folha própria por
                     não caber junto da página anterior), pula a faixa e a tabela. --}}
                @if (count($itensPagina))
                    <div class="faixa">DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS</div>
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

                {{-- Rodapé (totais/retenções/outras info) só ao fim da última página --}}
                @if ($loop->last)
                    @include('admin.layouts.partials.nota-atz-rodape')
                @endif
            </div>{{-- .pagina --}}
        @endforeach

    </div>

@stop

@section('css')
    <style>
        /* ── Página A4 ─────────────────────────────────────────────
           Respeitado pelo Chromium headless (usado na nota do disparo).
           O wkhtmltopdf IGNORA @page{margin} — nos pontos que ainda usam */
        @page {
            size: A4;
            /* topo/base 5mm — laterais maiores para dar respiro ao conteúdo */
            margin: 5mm 10mm;
        }

        /* O Chromium descarta cores de fundo ao imprimir; sem isto as faixas
           cinzas de seção (PRESTADOR / TOMADOR / DISCRIMINAÇÃO) saem brancas. */
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* O master-simple envolve o conteúdo em .container-fluid, que tem
           padding lateral de 15px — some com a margem da página e "afina"
           a nota. Zerado aqui para a folha ocupar toda a área imprimível. */
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .nfse {
            width: 100%;
            /* Respiro lateral de 1px: sem ele, a borda direita das tabelas
               (que ocupam 100%) encosta na viewport e o zoom do navegador
               corta o último pixel por arredondamento sub-pixel. */
            padding: 0 1px;
            margin: 0 auto;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
        }

         /* border-box: inclui a borda de 1px DENTRO dos 100% da tabela, senão
           ela soma por fora e estoura a largura (recortando no zoom). */
        .nfse,
        .nfse *,
        .nfse *::before,
        .nfse *::after {
            box-sizing: border-box;
        }

        /* ── Tabelas base ──────────────────────────────────────── */
        .nfse table.tb {
            width: 100%;           
        }

        .nfse table.tb td,
        .nfse table.tb th {
            padding: 1px 5px;
            vertical-align: top;
        }

        /* ── Bordas utilitárias ────────────────────────────────── */
        .nfse .bt { border-top: 1px solid #000; }
        .nfse .bb { border-bottom: 1px solid #000; }
        .nfse .bl { border-left: 1px solid #000; }
        .nfse .br { border-right: 1px solid #000; }

        .nfse .quadro {
            border: 1px solid #000;
        }

        .nfse .quadro-info-nota{
            height: 70px;
        }

        /* ── Canhoto ───────────────────────────────────────────── */
        .nfse .canhoto {
            border: 1px solid #000;
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .nfse .canhoto .p-0 {
            padding: 0;
        }

        .nfse .tracejado {
            border-top: 1px dashed #000;
            margin: 8px 0 10px 0;
        }

        /* ── Logos / imagens ───────────────────────────────────── */
        .nfse .logo-municipio {
            max-width: 70px;
            max-height: 110px;
        }

        .nfse .logo-nfse {
            max-width: 200px;
            max-height: 134px;
        }

        .nfse .logo-empresa {
            max-width: 130px;
            max-height: 100px;
        }

        .nfse .codigo-barras {
            height: 40px;
            max-width: 95%;
        }

        /* Código de barras CODE-128C (SVG vetorial) da chave de acesso. */
        .nfse .barcode-chave {
            line-height: 0;
            margin: 2px 0 1px 0;
        }

        /* Largura da célula com folga nas laterais. O preserveAspectRatio="none"
           aplicado no SVG pelo builder é o que permite esticar até essa largura.
           Sendo display:block, o text-align do <td> não centraliza — daí o
           margin auto. */
        .nfse .barcode-chave svg {
            display: block;
            width: 80%;
            height: 35px;
            margin: 0 auto;
        }

        /* ── Faixas de seção (barra cinza) ─────────────────────── */
        .nfse .faixa {
            background: #bdbcbc;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            padding: 1px 0;
            margin-top: 3px;
        }

        .nfse .secao {
            margin-top: 2px;
            margin-bottom: 2px;
        }

        /* ── Paginação manual ──────────────────────────────────── */
        /* Cada .pagina é uma folha física: força a quebra depois dela, menos na
           última. O cabeçalho completo (partial) é redesenhado em cada uma. */
        .nfse .pagina {
            break-after: page;
            page-break-after: always;
        }

        .nfse .pagina:last-child {
            break-after: auto;
            page-break-after: auto;
        }

        /* ── Tabela de itens (sem bordas, como o original) ─────── */
        .nfse .itens th {
            text-align: left;
            font-weight: bold;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        .nfse .itens td {
            padding-top: 1px;
            padding-bottom: 1px;
        }

        .nfse .itens td,
        .nfse .itens th {
            white-space: nowrap;
        }

        /* Um item nunca é cortado ao meio entre páginas. */
        .nfse .itens tbody tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* ── Retenções / linhas ────────────────────────────────── */
        .nfse .linha-solida {
            border-top: 1px solid #000;
        }

        .nfse .retencoes {
            margin: 3px 0;
        }

        .nfse .valor-total {
            font-size: 15px;
            padding: 4px 0;
        }

        /* ── Quadro de impostos ────────────────────────────────── */
        .nfse .impostos td {
            padding: 3px 6px;
        }

        .nfse .valor-imposto {
            margin-top: 12px;
        }

        /* ── Outras informações ────────────────────────────────── */
        .nfse .outras-info {
            padding: 4px 8px;
            margin-top: 2px;
        }

        .nfse .outras-info p {
            line-height: 14px;
        }

        /* ── Utilitários ───────────────────────────────────────── */
        .nfse .text-center { text-align: center; }
        .nfse .text-right { text-align: right; }
        .nfse .nowrap { white-space: nowrap; }
        .nfse .m-0 { margin: 0; }
        .nfse .mt-2 { margin-top: 8px; }
        .nfse .mb-2 { margin-bottom: 4px; }
        .nfse .pt-1 { padding-top: 3px; }
        .nfse .pt-2 { padding-top: 4px; }
        .nfse .pb-2 { padding-bottom: 4px; }
        /* Nomes mantidos por compatibilidade com o HTML; os valores é que definem o tamanho. */
        .nfse .fs-9 { font-size: 11px; }
        .nfse .fs-11 { font-size: 13px; }
        .nfse .fs-12 { font-size: 15px; }

        /* ── Impressão ─────────────────────────────────────────── */
        @media print {
            .page-break {
                page-break-before: always;
            }

            .no-break {
                page-break-inside: avoid;
            }

            .nfse table {
                width: 100%;
            }
        }
    </style>
@stop
