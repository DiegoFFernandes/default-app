@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-3 col-xs-3">
                            <p class="cresol1">{{ $boleto['DS_BANCO'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <p class="codigol1">| {{ $boleto['DS_CODIGOBANCO'] }} |</p>
                        </div>
                        <div class="col-md-3 col-xs-3"></div>
                        <div class="col-md-4 col-xs-4">
                            <p class="textol1">Comprovante de Entrega</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-6 col-xs-6">
                            <p class="texto_sup">Beneficiário</p>
                            <p class="nomes">{{ $boleto['NM_CEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">CPF/CNPJ do Beneficiário</p>
                            <p class="numeros_center">{{ $boleto['NR_CNPJCPFCEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Vencimento</p>
                            <p class="numeros_right_b">{{ $boleto['DT_VENC'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Pagador</p>
                            <p class="nomes">{{ $boleto['NM_PESSOA'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Agência / Conta</p>
                            <p class="numeros_right">{{ $boleto['DS_AGENCIACODIGOCEDENTE'] }}</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Data Docto</p>
                            <p class="numeros_center">{{ $boleto['DT_DOCUMENTO'] }}</p>
                        </div>

                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Nr Docto</p>
                            <p class="numeros_center">{{ $boleto['NR_DOC'] }}</p>
                        </div>

                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Espécie</p>
                            <p class="nomes_center">{{ $boleto['DS_ESPECIE'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Aceite</p>
                            <p class="nomes_center">{{ $boleto['TP_ACEITE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column" style="margin-right: 4px">
                            <p class="texto_sup">Data do Processamento</p>
                            <p class="numeros_center">{{ $boleto['DT_PROCESSAMENTO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Nosso Número</p>
                            <p class="numeros_right">{{ $boleto['NR_NOSSONUMERO'] }}</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p style="font-size: 10px; margin-top: 9px;"> Recebi(emos) o bloqueto / título com as
                                características acima.</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">(=) Valor do Documento</p>
                            <p class="numeros_right_b">{{ number_format($boleto['VL_DOCUMENTO'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line_auto">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Data</p>
                        </div>
                        <div class="col-md-4> col-xs-4 column">
                            <p class="texto_sup">Assinatura</p>
                        </div>
                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Data</p>
                        </div>
                        <div class="col-md-4 col-xs-4 column">
                            <p class="texto_sup">Entregador</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <hr class="tracejado">
                    </div>
                    <div class="col-md-12 col-xs-12 line2">
                        <div class="col-md-3 col-xs-3">
                            <p class="cresol1">{{ $boleto['DS_BANCO'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <p class="codigol1">| {{ $boleto['DS_CODIGOBANCO'] }} |</p>
                        </div>
                        <div class="col-md-4 col-xs-4"></div>
                        <div class="col-md-3 col-xs-3">
                            <p class="textol1">Recibo do Pagador</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Local do Pagamento</p>
                            <p class="nomes">PAGÁVEL EM QUALQUER SISTEMA DE COMPENSAÇÃO</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Vencimento</p>
                            <p class="numeros_right_b">{{ $boleto['DT_VENC'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-6 col-xs-6">
                            <p class="texto_sup">Beneficiário</p>
                            <p class="nomes">{{ $boleto['NM_CEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">CPF / CNPJ do Beneficiário</p>
                            <p class="numeros_right">{{ $boleto['NR_CNPJCPFCEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Agência / Conta</p>
                            <p class="numeros_right">{{ $boleto['DS_AGENCIACODIGOCEDENTE'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Endereço do Beneficiário</p>
                            <p class="nomes">{{ $boleto['DS_ENDERECOCEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Nosso Número</p>
                            <p class="numeros_right">{{ $boleto['NR_NOSSONUMERO'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Data Docto</p>
                            <p class="numeros_center">{{ $boleto['DT_DOCUMENTO'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Nr Docto</p>
                            <p class="numeros_center">{{ $boleto['NR_DOC'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Espécie</p>
                            <p class="nomes_center">{{ $boleto['DS_ESPECIE'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Aceite</p>
                            <p class="nomes_center">{{ $boleto['TP_ACEITE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column" style="margin-right: 4px">
                            <p class="texto_sup">Data do Processamento</p>
                            <p class="numeros_center">{{ $boleto['DT_PROCESSAMENTO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">(=) Valor do Documento</p>
                            <p class="numeros_right_b">{{ number_format($boleto['VL_DOCUMENTO'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Uso do Banco</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Carteira</p>
                            <p class="numeros_center">{{ $boleto['NR_CARTEIRA'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Espécie</p>
                            <p class="numeros_center">{{ $boleto['DS_MOEDA'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Quantidade</p>
                            <p class="ajuste_center">P</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column" style="margin-right: 4px">
                            <p class="texto_sup">Valor</p>
                            <p class="ajuste_center">P</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup_v">(-) Desconto / Abatimento</p>
                            <p class="ajuste_center">P</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Intruções (Todas as informações deste bloqueto são de exclusiva
                                responsabilidade do cedente.)</p>
                            <p class="nomes" style="padding-top: 4px">{{ $boleto['DS_INSTRUCAO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column no-padding">

                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(-) Outras Deduções</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(+) Mora / Multa (Juros)</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(+) Outros Acréscicomos</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p class="texto_sup">(=) Valor Cobrado</p>
                                {{-- <p class="ajuste_center">>P</p> --}}
                                <p class="ajuste_center">P</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Pagador</p>
                            <p class="nomes">{{ $boleto['NM_SACADO'] }}</p>
                            <p class="nomes">{{ $boleto['DS_ENDERECOSACADO'] }}</p>
                            <p class="nomes">{{ $boleto['DS_CEPCIDADESACADO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">CPF / CNPJ do Pagador</p>
                            <p class="numeros_right">{{ $boleto['NR_CNPJCPFSACADO'] }}</p>
                            <p class="texto_sup">Código de Baixa</p>
                            {{-- <p class="ajuste_sup">P</p> --}}
                            <p class="numeros_right">{{ $boleto['NR_NOSSONUMERO'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line_inv">
                        <div class="col-md-9 col-xs-9"></div>
                        <div class="col-md-3 col-xs-3">
                            <p class="texto_autenticação">Autenticação mecânica</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <hr class="tracejado">
                    </div>
                    <div class="col-md-12 col-xs-12 line2">
                        <div class="col-md-3 col-xs-3">
                            <p class="cresol1">{{ $boleto['DS_BANCO'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <p class="codigol1">| {{ $boleto['DS_CODIGOBANCO'] }} |</p>
                        </div>
                        <div class="col-md-7 col-xs-7">
                            <p class="textol1">{{ $boleto['DS_LINHADIGITAVEL'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Local do Pagamento</p>
                            <p class="nomes">PAGÁVEL EM QUALQUER SISTEMA DE COMPENSAÇÃO</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Vencimento</p>
                            <p class="numeros_right_b">{{ $boleto['DT_VENC'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-6 col-xs-6">
                            <p class="texto_sup">Beneficiário</p>
                            <p class="nomes">{{ $boleto['NM_CEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">CPF / CNPJ do Beneficiário</p>
                            <p class="numeros_right">{{ $boleto['NR_CNPJCPFCEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Agência / Conta</p>
                            <p class="numeros_right">{{ $boleto['DS_AGENCIACODIGOCEDENTE'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Endereço do Beneficiário</p>
                            <p class="nomes">{{ $boleto['DS_ENDERECOCEDENTE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">Nosso Número</p>
                            <p class="numeros_right">{{ $boleto['NR_NOSSONUMERO'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Data Docto</p>
                            <p class="numeros_center">{{ $boleto['DT_DOCUMENTO'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Nr Docto</p>
                            <p class="numeros_center">{{ $boleto['NR_DOC'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Espécie</p>
                            <p class="nomes_center">{{ $boleto['DS_ESPECIE'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Aceite</p>
                            <p class="nomes_center">{{ $boleto['TP_ACEITE'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column" style="margin-right: 4px">
                            <p class="texto_sup">Data do Processamento</p>
                            <p class="numeros_center">{{ $boleto['DT_PROCESSAMENTO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">(=) Valor do Documento</p>
                            <p class="numeros_right_b">{{ number_format($boleto['VL_DOCUMENTO'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-2 col-xs-2">
                            <p class="texto_sup">Uso do Banco</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Carteira</p>
                            <p class="numeros_center">{{ $boleto['NR_CARTEIRA'] }}</p>
                        </div>
                        <div class="col-md-1 col-xs-1 column">
                            <p class="texto_sup">Espécie</p>
                            <p class="numeros_center">{{ $boleto['DS_MOEDA'] }}</p>
                        </div>
                        <div class="col-md-2 col-xs-2 column">
                            <p class="texto_sup">Quantidade</p>
                            <p class="ajuste_center">P</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column" style="margin-right: 4px">
                            <p class="texto_sup">Valor</p>
                            <p class="ajuste_center">P</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup_v">(-) Desconto / Abatimento</p>
                            <p class="ajuste_center">P</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Intruções (Todas as informações deste bloqueto são de exclusiva
                                responsabilidade do cedente.)</p>
                            <p class="nomes" style="padding-top: 4px">{{ $boleto['DS_INSTRUCAO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column no-padding">

                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(-) Outras Deduções</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(+) Mora / Multa (Juros)</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12 line">
                                <p class="texto_sup">(+) Outros Acréscicomos</p>
                                <p class="ajuste_center">P</p>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p class="texto_sup">(=) Valor Cobrado</p>
                                <p class="ajuste_center">>P</p>
                                <p class="ajuste_center">P</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 line">
                        <div class="col-md-9 col-xs-9">
                            <p class="texto_sup">Pagador</p>
                            <p class="nomes">{{ $boleto['NM_SACADO'] }}</p>
                            <p class="nomes">{{ $boleto['DS_ENDERECOSACADO'] }}</p>
                            <p class="nomes">{{ $boleto['DS_CEPCIDADESACADO'] }}</p>
                        </div>
                        <div class="col-md-3 col-xs-3 column">
                            <p class="texto_sup">CPF / CNPJ do Pagador</p>
                            <p class="numeros_right">{{ $boleto['NR_CNPJCPFSACADO'] }}</p>
                            <p class="texto_sup">Código de Baixa</p>
                            {{-- <p class="ajuste_sup">P</p> --}}
                            <p class="numeros_right">{{ $boleto['NR_NOSSONUMERO'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-md-8 col-xs-8" style="padding-top: 15px">
                            {!! $codigo_barras !!}
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <p class="texto_autenticação">Autenticação mecânica - Ficha de Compensação</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- /.row -->
    </section>
@stop
