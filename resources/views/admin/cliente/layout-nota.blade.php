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
                                            <p class="title-nota">RECEBEMOS OS ITENS CONSTANTES NO DOCUMENTO INDICADO
                                                ABAIXO:</p>
                                        </div>
                                        <div class="col-3 border-bottom border-dark vr">
                                            <p class="title-nota"><strong>Cód:</strong> {{ $data[0]->CD_PESSOA }}</p>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">NOTA:<strong> {{ $data[0]->NR_NOTA }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3  border-end border-dark vr">
                                            <p class="title-nota"><strong>Data de Recebimento </p></strong>
                                        </div>
                                        <div class="col-6 border-end border-dark vr">
                                            <p class="title-nota"><strong>Identificação e Assinatura do Recebedor</p>
                                            </strong>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">RPS:<strong> {{ $data[0]->NR_RPS }}</strong></p>
                                            <p class="title-nota">SÉRIE:<strong> {{ $data[0]->CD_SERIE }}</strong></p>
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
                                            <p class="title-nota"><strong>{{ $data[0]->NR_NOTA }}</strong></p>
                                        </div>
                                        <div class="col-6 border-bottom border-dark text-center">
                                            <p class="title-nota">Data de emissão</p>
                                            <p class="title-nota"><strong>{{ $data[0]->DS_DTEMISSAO }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 text-center">
                                            <p class="title-nota"><strong>Código de Verificação</strong></p>
                                            <p class="title-nota"><strong>{{ $data[0]->CD_AUTENTICACAO }}</strong></p>
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
                                            <p class="title-nota">Nome/Razão Social: {{ $data[0]->NM_EMPRESA }}</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Inscrição Estadual: {{ $data[0]->NR_INSCESTEMPRESA }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <p class="title-nota">CNPJ:{{ $data[0]->NR_CNPJEMPRESA }}</p>
                                        </div>
                                        <div class="col-3">
                                            <p class="title-nota">Fone: {{ $data[0]->NR_FONEEMPRESA }}</p>
                                        </div>
                                        <div class="col-5">
                                            <p class="title-nota">Email: {{ $data[0]->DS_EMAILEMPRESA }} </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="title-nota">Endereço: {{ $data[0]->DS_ENDEMPRESA }}, Bairro:
                                                {{ $data[0]->DS_BAIRROEMPRESA }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-4">
                                            <p class="title-nota">CEP: {{ $data[0]->NR_CEPEMPRESA }}</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Município: {{ $data[0]->DS_MUNICIPIOEMP }}</p>
                                        </div>
                                        <div class="col-4">
                                            <p class="title-nota">Inscrição Municipal: {{ $data[0]->NR_INSCMUNEMPRESA }}
                                            </p>
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
                                    <p class="title-nota">Nome/Razão Social: {{ $data[0]->NM_PESSOA }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <p class="title-nota">CNPJ/CPF: {{ $data[0]->NR_CNPJCPF }}</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Inscrição Municipal: {{ $data[0]->NR_INSCMUN }} </p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Inscrição Estadual: {{ $data[0]->NR_INSCESTPESSOA }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Endereço: {{ $data[0]->DS_ENDERECOPESSOA }}, N°
                                        {{ $data[0]->NR_ENDPESSOA }}, Bairro: {{ $data[0]->DS_BAIRROPESSOA }},
                                        {{ $data[0]->DS_MUNPESSOA }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="title-nota">Complemento: {{ $data[0]->DS_COMPPESSOA }}</p>
                                </div>
                            </div>
                            <div class="row mt-0">
                                <div class="col-4">
                                    <p class="title-nota">Cep: {{ $data[0]->NR_CEPPESSOA }}</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Email: {{ $data[0]->DS_EMAIL }} </p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Fone: {{ $data[0]->NR_FONE }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="title-nota">Forma Pagamento: {{ $data[0]->DS_FORMAPAGTO }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="title-nota">Condição Pagamento: {{ $data[0]->DS_CONDPAGTO }}</p>
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
                                        <th>Seq</th>
                                        <th style="width: 30%">Item</th>
                                        <th style="width: 10%">Marca</th>
                                        <th style="width: 10%">Modelo</th>
                                        <th style="width: 10%">Série</th>
                                        <th style="width: 10%">Fogo</th>
                                        <th style="width: 10%">DOT</th>
                                        <th style="width: 10%">Qtde</th>
                                        <th style="width: 10%">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $d)
                                        <tr>
                                            <td>{{ $d->SEQ}}</td>
                                            <td>{{ $d->O_DS_ITEM }}</td>
                                            <td>{{ $d->O_DS_MARCA }}</td>
                                            <td>{{ $d->O_DS_MODELO }}</td>
                                            <td>{{ $d->O_NR_SERIE }}</td>
                                            <td>{{ $d->O_NR_FOGO }}</td>
                                            <td>{{ $d->O_NR_DOT }}</td>
                                            <td>{{ $d->O_QTDE }}</td>
                                            <td class="vl-unitario">{{ $d->O_VL_UNITARIO }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="col-12  p-0">
                                <hr style="border: 1px solid black;">
                            </div>
                            <div class="row mt-1">
                                <div class="col-4">
                                    <p class="title-nota">Retenção ISS: {{ number_format($data[0]->VL_ISSQN_RETIDO, 2, ',', '.') }}</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção PIS: 0,00</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção COFINS: 0,00</p>
                                </div>
                            </div>
                            <div class="row mt-1 mb-1">
                                <div class="col-4">
                                    <p class="title-nota">Retenção IR: {{ number_format($data[0]->VL_IR, 2, ',', '.') }}</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção CSLL: 0,00</p>
                                </div>
                                <div class="col-4">
                                    <p class="title-nota">Retenção INSS: 0,00</p>
                                </div>
                            </div>
                            <div class="col-12 p-0">
                                <hr style="border: 1px solid black;">
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h5><strong>VALOR TOTAL DA NOTA: R$ {{number_format($data[0]->VL_CONTABIL, 2, ',', '.')}}</strong></h5>
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
                                    <p class="title-nota"><strong>14.04 - Recauchutagem ou regeneração de pneus.</p>
                                    </strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Deduções (R$)</p>
                                    <p class="valorFormatado">0,00</p>
                                </div>
                                <div class="col-3 border border-black">
                                    <p class="title-nota">Base de Cálculo ISS (R$)</p>
                                    <p class="valorFormatado">{{ $data[0]->VL_CONTABIL }}</p>
                                </div>
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Alíquota (%)</p>
                                    <p class="title-nota">2.00</p>
                                </div>
                                <div class="col-3 border border-black">
                                    <p class="title-nota">Valor do ISS Retido (R$)</p>
                                    <p class="valorFormatado">{{ $data[0]->VL_ISSQN_RETIDO }}</p>
                                </div>
                                <div class="col-2 border border-black">
                                    <p class="title-nota">Valor do ISS (R$)</p>
                                    <p class="valorFormatado">{{ $data[0]->VL_ISSQN }}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center"
                                    style="background: rgb(189, 188, 188); color: rgb(0, 0, 0); ">
                                    <h5><strong>OUTRAS INFORMAÇÕES</strong></h5>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 border border-dark mt-1 mb-1">
                                    <div class="row mb-1 mt-1">
                                        <div class="col-12">
                                        <p class="title-nota">-<strong>Valor Líquido: R$ {{ number_format($data[0]->VL_CONTABIL, 2, ',', '.') }}
                                            </strong></p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Vencimentos: {{ $data[0]->O_DS_CONDPAGTO }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Esta NFS-e foi emitida conforme Decreto nº 332 de
                                            21/09/2017</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">-<strong> RETENÇÕES: *ISS: R$
                                                {{ number_format($data[0]->VL_ISSQN_RETIDO, 2, ',', '.') }} / *IR: R$ {{ number_format($data[0]->VL_IR, 2, ',', '.') }}</strong></p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Pedido Smartphone.: 64984, 64997 </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Coletas/Ordens s Carreg.: Coleta.: 204123, 204819 Ordem
                                            Carreg.: 149102. </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">Nr Placa: ,</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="title-nota">- Vendedores: {{ $data[0]->NM_VENDEDOR }}</p>
                                    </div>
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

        .valorFormatado {
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

@section('js')

    <script>
        function formatarReal(valor, tipo) {
            if (!valor) return 'R$ 0,00';

            let numero;

            if (tipo === 'unitario') {
                // valor com 000 → divide por 1000
                numero = Number(valor.toString().replace(/\./g, '')) / 1000;
            } else if (tipo === 'valorFormatado') {
                // valores com 00 → apenas transforma em número
                numero = Number(valor);
            } else {
                // fallback
                numero = Number(valor);
            }

            return numero.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // formata os valor que vem com  tres zeros
            document.querySelectorAll('.vl-unitario').forEach(el => {
                el.textContent = formatarReal(el.textContent, 'unitario');
            });

            // formata os valor que vem com  dois zeros
            document.querySelectorAll('.valorFormatado').forEach(el => {
                el.textContent = formatarReal(el.textContent, 'valorFormatado');
            });
        });
    </script>

@stop
