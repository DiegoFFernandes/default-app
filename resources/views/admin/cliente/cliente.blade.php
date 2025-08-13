@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-12 border border-dark rounded p-0">
                                    <div class="row">
                                        <div class="col-6 border-bottom border-dark p-0">
                                            <p><strong>Prezado:</strong>GILBERTO SOARES DA SILVA</p>
                                            <p>RECEBEMOS OS ITENS CONSTANTES NO DOCUMENTO INDICADO ABAIXO:</p>
                                        </div>
                                        <div class="col-3 border-bottom border-dark vr">
                                            <p><strong>Cód</p></strong>
                                        </div>
                                        <div class="col-3">
                                            <p>NOTA:</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3  border-end border-dark vr">
                                            <p><strong>Data de Recebimento </p></strong>
                                        </div>
                                        <div class="col-6 border-end border-dark vr">
                                            <p><strong>Identificação e Assinatura do Recebedor</p></strong>
                                        </div>
                                        <div class="col-3">
                                            <p>RPS</p>
                                            <p>SÉRIE</p>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="border-top: 2px dashed black;">
                            <div class="row">
                                <!-- Coluna esquerda -->
                                <div class="col-8 text-center">
                                    <p>
                                    <p><strong>MUNICIPIO DE CAMBÉ</strong></p>
                                    <p>SECRETARIA MUNICIPAL DE FAZENDA</p>
                                    <p><strong>Nota Fiscal de Serviços Eletrônica - NFS-e</p></strong>
                                    </p>
                                </div>
                                <!-- Coluna direita -->
                                <div class="col-4 border border-dark p-0">
                                    <div class="row m-0">
                                        <div class="col-6 border-bottom border-dark text-center vr">
                                            <p>Nº da Nota:</p>
                                        </div>
                                        <div class="col-6 border-bottom border-dark text-center">
                                            <p>Data de emissão</p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 text-center">
                                            <p>Código de Verificação</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>PRESTADOR DE SERVIÇOS</strong></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-9">
                                    <p>Nome/Razão Social</p>
                                </div>
                                <div class="row-3">
                                    <p>Inscrição Estadual</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p>CNPJ</p>
                                </div>
                                <div class="col-4">
                                    <p>Fone:</p>
                                </div>
                                <div class="col-4">
                                    <p>Email:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Endreço:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p>CEP: 86.900-000</p>
                                </div>
                                <div class="col-4">
                                    <p>Municipio: Cambé</p>
                                </div>
                                <div class="col-4">
                                    <p>Inscrição Municipal: 16058</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>TOMADOR DE SERVIÇOS</strong></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Nome/Razão Social:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p>CNPJ/CPF:</p>
                                </div>
                                <div class="col-4">
                                    <p>Inscrição Municipal:</p>
                                </div>
                                <div class="col-4">
                                    <p>Inscrição Estadual:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Endereço</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Complemento:</p>
                                </div>
                            </div>
                            <div class="row mt-0">
                                <div class="col-4">
                                    <p>Cep:</p>
                                </div>
                                <div class="col-4">
                                    <p>Email:</p>
                                </div>
                                <div class="col-4">
                                    <p>Fone:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p>Forma Pagamento:</p>
                                </div>
                                <div class="col-6">
                                    <p>Condição Pagamento:</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center"
                                   style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS</strong></h5>
                                </div>
                            </div>
                            <table class="table table-bordered table-sm text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%">Seq Item</th>
                                        <th style="width: 7%">Marca</th>
                                        <th style="width: 7%">Modelo</th>
                                        <th style="width: 7%">Série</th>
                                        <th style="width: 7%">Fogo</th>
                                        <th style="width: 7%">DOT</th>
                                        <th style="width: 7%">Qtde</th>
                                        <th style="width: 8%">Valor</th>
                                    </tr>
                                </thead>
                            </table>
                                <div class="row">
                                    <div class="col-12" style="color: rgb(0, 0, 0);">
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <p>Retenção ISS:</p>
                                    </div>
                                    <div class="col-4">
                                        <p>Retenção PIS:</p>
                                    </div>
                                    <div class="col-4">
                                        <p>Retenção COFINS:</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <p>Retenção IR:</p>
                                    </div>
                                    <div class="col-4">
                                        <p>Retenção CSLL:</p>
                                    </div>
                                    <div class="col-4">
                                        <p>Retenção INSS:</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" style="color: rgb(0, 0, 0);">
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h5><strong>VALOR TOTAL DA NOTA: R$ 4.038,00</strong></h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" style="color: rgb(0, 0, 0);">
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p>Código e Descrição do Serviço:</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p>14.04 - Recauchutagem ou regeneração de pneus.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2 border border-black">
                                        <p>Deduções (R$)</p>
                                        <p>0,00</p>
                                    </div>
                                    <div class="col-3 border border-black">
                                        <p>Base de Cálculo ISS (R$)</p>
                                        <p>4.038,00</p>
                                    </div>
                                    <div class="col-2 border border-black">
                                        <p>Alíquota (%)</p>
                                        <p>2.00</p>
                                    </div>
                                    <div class="col-3 border border-black">
                                        <p>Valor do ISS Retido (R$)</p>
                                        <p>0,00</p>
                                    </div>
                                    <div class="col-2 border border-black">
                                        <p>Valor do ISS (R$)</p>
                                        <p>80,76</p>
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
                                            <p>-<strong> Líquido: R$4.038,00</strong></p>
                                        </div>
                                        <div class="col-12">
                                            <p>- Vencimentos: 14/08/2025 - R$850.00 10/09/2025 - R$1062.67 08/10/2025 -
                                                R$1062.67 05/11/2025 - R$1062.66</p>
                                        </div>
                                        <div class="col-12">
                                            <p>- Esta NFS-e foi emitida conforme Decreto nº 332 de 21/09/2017</p>
                                        </div>
                                        <div class="col-12">
                                            <p>-<strong> RETENÇÕES: *ISS: R$ 0,00 / *IR: R$0,00</strong></p>
                                        </div>
                                        <div class="col-12">
                                            <p>Pedido Smartphone.: 64984, 64997</p>
                                        </div>
                                        <div class="col-12">
                                            <p>Coletas/Ordens Carreg.: Coleta.: 204123, 204819 Ordem Carreg.: 149102. </p>
                                        </div>
                                        <div class="col-12">
                                            <p>Nr Placa: ,</p>
                                        </div>
                                        <div class="col-12">
                                            <p>- Vendedores.: 25204 - OUR - CESAR ORLANDO DOS SANTOS</p>
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
        .vr {
            border-right: 1px solid black;
        }

        p {
            font-size: 14px;
        }

        /* ajusta headers*/
        h5 {
            margin: 3px 0 !important;
        }

        /* remove espaço entre linhas*/
        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* reduz padding interno nas colunas */
        .col {
            padding-left: 3px !important;
            padding-right: 3px !important;
        }

        /* reduz espaçamentos sem quebrar o layout */
        .mt-3,
        .mt-2,
        .mt-1,
        .mt-0 {
            margin-top: 3px !important;
        }
    </style>
@stop
