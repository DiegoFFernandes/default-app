@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-6 col-md-2">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text">Vencidos</span>
                        <span class="info-box-number" id="vencidos"></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text">Inadimplência</span>
                        <span class="info-box-number" id="pc_inadimplencia"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inadimplência por Vendedor</h3>
                    </div>
                    <div class="card-body pt-0">
                        <table id="tabela-inadimplencia-vendedor" class="table compact table-responsive table-font-small">
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-table-cliente" tabindex="-1" role="dialog"
            aria-labelledby="modal-table-cliente-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="modal-table-cliente-label">Detalhes Inadimplência</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="accordion" id="accordionCliente">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        table.dataTable {
            table-layout: fixed;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        @media (max-width: 768px) {
            .btn-detalhes {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #f0f0f0;
                cursor: pointer;
            }
        }
    </style>
@stop
@section('js')
    <script>
        var table = $('#tabela-inadimplencia-vendedor').DataTable({
            processing: false,
            serverSide: false,
            paging: false,
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            },
            ajax: "{{ route('get-inadimplencia') }}",
            columns: [{
                    data: 'action',
                    name: 'action',
                    title: ''
                },
                {
                    data: 'MES_ANO',
                    name: 'MES_ANO',
                    title: 'Mês/Ano',
                    "width": '33%'
                },
                {
                    data: 'VL_DOCUMENTO',
                    name: 'VL_DOCUMENTO',
                    title: 'Total',
                    visible: false
                },
                {
                    data: 'VL_SALDO',
                    name: 'VL_SALDO',
                    title: 'Vencido',
                    "width": '33%'
                },
                {
                    data: 'PC_INADIMPLENCIA',
                    name: 'PC_INADIMPLENCIA',
                    title: '%',
                    "width": '33%'
                }
            ],
            columnDefs: [{
                targets: [2, 3],
                render: $.fn.dataTable.render.number('.', ',', 2)
            }],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(',', '.') * 1 : typeof i === 'number' ? i : 0;
                };

                // Total over all pages
                totalTotal = api
                    .column(2)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                totalVencido = api
                    .column(3)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(2).footer()).html(
                    totalTotal.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );
                $(api.column(3).footer()).html(
                    totalVencido.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );

                var inadimplenciaPercentual = (totalVencido / totalTotal) * 100;
                $('#pc_inadimplencia').html(inadimplenciaPercentual.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '%');

                $('#vencidos').html(totalVencido.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
            },
        });

        $('#tabela-inadimplencia-vendedor tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            $('#accordionCliente').empty(); // Limpa antes

            $.ajax({
                type: "GET",
                url: "{{ route('get-inadimplencia-cliente', ['id' => '']) }}",
                data: {
                    mes: row.data().MES,
                    ano: row.data().ANO
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loading").removeClass('invisible');
                },
                success: function(response) {
                    data = Object.values(response);

                    $('#modal-table-cliente-label').html('Detalhes Inadimplência</br>' + row.data()
                        .MES_ANO);
                    $('#accordionCliente').empty(); // limpa antes de popular

                    data.forEach(function(item) {
                        let accordion = `
                        <div class="card card-outline">
                        <div class="card-header pt-1 pb-1" id="heading${item.CD_PESSOA}">
                            <h6 class="mb-0 d-flex align-items-center justify-content-between">
                                <button class="btn collapsed p-0 m-0 text-left" type="button"
                                        data-toggle="collapse"
                                        data-target="#collapse${item.CD_PESSOA}"
                                        aria-expanded="false"
                                        aria-controls="collapse${item.CD_PESSOA}" style="font-size: 13px;">
                                <b>${item.NM_PESSOA}</b>
                                </button>
                                <span class="badge badge-warning ml-2">
                                    ${parseFloat(item.VL_SALDO_AGRUPADO).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                </span>
                            </h6>
                        </div>
                        <div id="collapse${item.CD_PESSOA}" class="collapse" aria-labelledby="heading${item.CD_PESSOA}">
                    `;
                        item.DETALHES.forEach(function(detalhe) {
                            accordion += `
                                <div class="card-body p-1">
                                    <div class="card-body pt-2 pb-2">
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                        <tr>
                                            <th class="text-muted">Nota</th>
                                            <td>${detalhe.NR_DOCUMENTO}</td>
                                            <th class="text-muted">Venc.</th>
                                        <td>${formatDate(detalhe.DT_VENCIMENTO)}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Valor</th>
                                            <td><span class="text-success font-weight-bold">R$ ${parseFloat(detalhe.VL_SALDO).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span></td>
                                            <th class="text-muted">Juros</th>
                                            <td><span class="text-danger">R$ 0,00</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <hr class="mt-0 mb-2">
                        `;
                        });

                        accordion += `
                            </div>
                        </div>
                    `;

                        $('#accordionCliente').append(accordion);
                    });

                    $('#loading').addClass('invisible');
                    $('#modal-table-cliente').modal('show');
                }

            });

        });

        function formatDate(value) {
            if (!value) return "";
            const date = new Date(value);
            return (
                date.toLocaleDateString("pt-BR")
            );
        }
    </script>

@stop
