@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-12 border border-dark rounded p-0">
                                    <div class="row m-0">
                                        <div class="col-6 border-bottom border-dark">
                                            <p class="title-nota"><strong>Prezado:</strong> {{ $data[0]->NM_PESSOA }}</p>
                                            <p class="title-nota">RECEBEMOS OS ITENS CONSTANTES NO DOCUMENTO INDICADO ABAIXO:</p>
                                        </div>
                                        <div class="col-3 border-bottom border-dark vr">
                                            <p class="title-nota"><strong>Cód:</strong> 0000</p>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">NOTA:<strong> 00000</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3  border-end border-dark vr">
                                            <p class="title-nota"><strong>Data de Recebimento </p></strong>
                                        </div>
                                        <div class="col-6 border-end border-dark vr">
                                            <p class="title-nota"><strong>Identificação e Assinatura do Recebedor</p></strong>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">RPS:<strong> 00000</strong></p>
                                            <p class="title-nota">SÉRIE:<strong> F3</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="pontilhado" style="border-top: 2px dashed black;">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <div class="row align-items-center">
                                        <div class="col-3 d-flex justify-content-center">
                                            <img src="{{ asset('img/municipio.png') }}" alt="Logo do Município"
                                                class="logo-municipio img-fluid">
                                        </div>
                                        <div class="col-9 text-center">
                                            <h5><strong>MUNICÍPIO DE CAMBÉ</strong></h5>
                                            <h5>SECRETARIA MUNICIPAL DE FAZENDA</h5>
                                            <h5><strong>Nota Fiscal de Serviços Eletrônica - NFS-e</strong></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 border border-dark p-0">
                                    <div class="row m-0">
                                        <div class="col-6 border-bottom border-dark text-center vr">
                                            <p class="title-nota">Nº da Nota:</p>
                                            <p class="title-nota"><strong>00000</strong></p>
                                        </div>
                                        <div class="col-6 border-bottom border-dark text-center">
                                            <p class="title-nota">Data de emissão</p>
                                            <p class="title-nota"><strong>13/08/2025</strong></p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 text-center">
                                            <p class="title-nota"><strong>Código de Verificação</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>PRESTADOR DE SERVIÇOS</strong></h5>
                                </div>
                            </div>
                            <div class="row align-items-center mt-1">
                                <div class="col-3 d-flex justify-content-center">
                                    <img src="{{ asset('img/atz-logo.png') }}" alt="Logo ATZ" class="logo-atz  img-fluid">
                                </div>
                                <div class="col-9 align-items-center">
                                    <div class="row">
                                        <div class="col-8">
                                            <p class="title-nota">Nome/Razão Social: LDB - VITTA COMERCIAL LTDA ME</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Inscrição Estadual: 000000000</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <p class="title-nota">CNPJ: 00.000.000/0000-00</p>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">Fone: (00) 0000-0000</p>
                                        </div>
                                        <div class="col-5">
                                            <p class="title-nota">Email: email@gmail.com </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="title-nota">Endereço: ROD.MELLO PEIXOTO BR 369 KM 166, Bairro: JARDIM SANTA ADELAIDE </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-4">
                                            <p class="title-nota">CEP: 00000-000</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Município: Cambe</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Inscrição Municipal: 00000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-0">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>TOMADOR DE SERVIÇOS</strong></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Nome/Razão Social: TESTE</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p class="title-nota">CNPJ/CPF: 000.000.000-00</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Inscrição Municipal:</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Inscrição Estadual:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Endereço: R SETE DE SETEMBRO, Bairro: SAO JOSE - Maracai , SAO PAULO</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Complemento: SAO JOSE DAS LARANJEIRAS</p>
                                </div>
                            </div>
                            <div class="row mt-0">
                                <div class="col-4">
                                    <p class="title-nota">Cep: 00000-000</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Email: email@gmail.com</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Fone: 00-00000-0000</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="title-nota">Forma Pagamento: Boleto</p>
                                </div>
                                <div class="col-6">
                                    <p class="title-nota">Condição Pagamento: 7/28/56/84 dd</p>
                                </div>
                            </div>
                            <div class="row mt-0">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS</strong></h5>
                                </div>
                            </div>
                            <table class=" table-responsive table table compact table-sm text-center align-middle"
                                style="font-size: 13px">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%">Seq</th>
                                        <th style="width: 30%">Item</th>
                                        <th style="width: 7%">Marca</th>
                                        <th style="width: 7%">Modelo</th>
                                        <th style="width: 7%">Série</th>
                                        <th style="width: 7%">Fogo</th>
                                        <th style="width: 7%">DOT</th>
                                        <th style="width: 7%">Qtde</th>
                                        <th style="width: 8%">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>PM 295/80R22,5 DV-RT2 265</td>
                                        <td>MICHELIN</td>
                                        <td>X MULTI EN</td>
                                        <td>3520</td>
                                        <td></td>
                                        <td>3520</td>
                                        <td>1,00</td>
                                        <td>850,00</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>PM 295/80R22,5 DV-RT2 265</td>
                                        <td>MICHELIN</td>
                                        <td>X MULTI EN</td>
                                        <td>1721</td>
                                        <td></td>
                                        <td>1721</td>
                                        <td>1,00</td>
                                        <td>850,00</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-12  p-0">
                                <hr style="border: 1px solid black;">
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p class="title-nota">Retenção ISS:</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção PIS:</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção COFINS:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p class="title-nota">Retenção IR:</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção CSLL:</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção INSS:</p>
                                </div>
                            </div>
                            <div class="col-12 p-0">
                                <hr style="border: 1px solid black;">
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h5><strong>VALOR TOTAL DA NOTA: R$ 4.038,00</strong></h5>
                                </div>
                            </div>
                            <div class="col-12  p-0">
                                <hr style="border: 1px solid black;">
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Código e Descrição do Serviço:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota"><strong>14.04 - Recauchutagem ou regeneração de pneus.</p></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Deduções (R$)</p>
                                    <p class="title-nota">0,00</p>
                                </div>
                                <div class="col-3 border border-black">
                                    <p class="title-nota">Base de Cálculo ISS (R$)</p>
                                    <p class="title-nota">4.038,00</p>
                                </div>
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Alíquota (%)</p>
                                    <p class="title-nota">2.00</p>
                                </div>
                                <div class="col-3 border border-black">
                                    <p class="title-nota">Valor do ISS Retido (R$)</p>
                                    <p class="title-nota">0,00</p>
                                </div>
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Valor do ISS (R$)</p>
                                    <p class="title-nota">80,76</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>OUTRAS INFORMAÇÕES</strong></h5>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 border border-dark">
                                    <div class="col-12">
                                        <p class="title-nota">-<strong>Valor Líquido: R$ 4.038,00 </strong></p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Vencimentos: 14/08/2025 - R$850.00 10/09/2025 - R$1062.67 08/10/2025 -
                                            R$1062.67 05/11/2025 - R$1062.66 </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Esta NFS-e foi emitida conforme Decreto nº 332 de 21/09/2017</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">-<strong> RETENÇÕES: *ISS: R$ 0,00 / *IR: R$0,00</strong></p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Pedido Smartphone.: 64984, 64997 </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Coletas/Ordens s Carreg.: Coleta.: 204123, 204819 Ordem Carreg.: 149102. </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Nr Placa: ,</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Vendedores: TESTE </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        /* linhas horizontais */
        .vr {
            border-right: 1px solid black;
        }

        .title-nota {
            font-size: 13px;
            line-height: 14px;
            margin-bottom: 0.30em
        }

        h5 {
            margin: 1px;
            font-size: 1.15rem
        }

        hr {
            padding: 0;
            margin: 0;
        }

        .pontilhado {
            padding: 0;
            margin: 7px;
        }

        .logo-municipio {
            max-width: 80px;
            height: auto;
        }

        .logo-atz {
            /* max-width: 80px; */
            height: auto;
        }

        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .col,
        [class*="col-"] {
            padding-left: 3px !important;
            padding-right: 3px !important;
        }

        /* ajuste para telas pequenas */
        @media (max-width: 576px) {
            p {
                font-size: 9px;
            }

            h5 {
                font-size: 0.5rem;
            }
                .logo-municipio,
                .logo-atz {
                    max-width: 45px;
                }
            }

            /* ajustar quando imprimir */
            @media print {

                /* exibe apenas o card-body */
                body * {
                    visibility: hidden;
                }

                .card-body * {
                    visibility: visible;
                }

                .card-body {
                    width: 100% !important;
                    padding: 1cm !important;
                }
            }
    </style>
@stop
