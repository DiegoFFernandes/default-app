@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-7">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lote de Expedição</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm" id="novoLoteExpedicaoBtn" data-toggle="modal"
                                data-target="#loteExpedicaoModal">
                                Novo Lote
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped compact" id="loteExpedicaoTable"
                                style="width:100%; font-size:12px">
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </section>

    <div class="modal fade" id="loteExpedicaoModal" tabindex="-1" role="dialog" aria-labelledby="loteExpedicaoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Lote de Expedição</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-2 pb-1">
                    <form id="loteExpedicaoForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="empresa">Empresa</label>
                                    <select class="form-control form-control-sm select2" name="empresa" id="empresa">
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->CD_EMPRESA }}">{{ $empresa->NM_EMPRESA }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="emissao">Emissão</label>
                                    <input type="date" class="form-control form-control-sm" name="emissao" id="emissao"
                                        placeholder="Data de Emissão">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-12">
                                <div class="form-group mb-2">
                                    <label for="vendedor">Vendedor</label>
                                    <select class="form-control form-control-sm select2" name="vendedor" id="vendedor">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label for="observacao">Observações</label>
                                    <textarea class="form-control form-control-sm" name="observacao" id="observacao" rows="2"
                                        placeholder="Observações"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" data-dismiss="modal">Fechar</button>
                    <button class="btn btn-sm btn-primary" form="loteExpedicaoForm">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('css')
    <style>
        /* Reduz altura e fonte do Select2 com Bootstrap 4 */
        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.8125rem + 2px);
            /* semelhante ao form-control-sm */
            font-size: 12px;
            /* 14px */

        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#loteExpedicaoTable').DataTable({
                responsive: true,
                "paging": true,
                "bInfo": false,
                "pagingType": "simple",
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                ajax: {
                    url: "{{ route('get-lote-expedicao') }}", // URL to fetch data                    
                },
                columns: [{
                        data: 'LOTE',
                        name: 'LOTE',
                        title: 'Lote'
                    },
                    {
                        data: 'NM_VENDEDOR',
                        name: 'NM_VENDEDOR',
                        title: 'Vendedor'
                    },
                    {
                        data: 'EMISSAO',
                        name: 'EMISSAO',
                        title: 'Emissão'
                    },
                    {
                        data: 'IDEMPRESA',
                        name: 'IDEMPRESA',
                        title: 'Emp'
                    },
                    {
                        data: 'SITUACAO',
                        name: 'SITUACAO',
                        title: 'Situação'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: '#',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: [2],
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('DD/MM/YYYY');
                    }
                }],
                order: [
                    [0, 'desc']
                ],
            });

            $('#vendedor').select2({
                placeholder: "Buscar Vendedor",
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true,
                minimumInputLength: 2,
                dropdownParent: $('#loteExpedicaoModal'),
                ajax: {
                    url: " {{ route('get-search-vendedor') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.NM_VENDEDOR,
                                    id: item.ID
                                }
                            })

                        };
                    },
                    cache: true
                }
            }).change(function(el) {
                var data = $(el.target).select2('data');
                $('#cd_vendedor').val(data[0].id || '');
            });

            $('#novoLoteExpedicaoBtn').on('click', function() {

                $('#loteExpedicaoForm')[0].reset();
                $('#emissao').val(moment().format('YYYY-MM-DD'));
                $('#loteExpedicaoModal').modal('show');
            });

            $('#loteExpedicaoForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('post-create-lote-expedicao') }}",
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#loteExpedicaoForm')[0].reset();
                            $('#loteExpedicaoModal').modal('hide');
                            $('#loteExpedicaoTable').DataTable().ajax.reload();
                            msgToastr(response.success, 'success');
                        } else {
                            msgToastr(response.errors, 'error');
                        }

                    },
                    error: function(xhr) {
                        msgToastr(xhr.responseJSON.errors, 'error');
                    }
                });
            });
        });
    </script>

@stop
