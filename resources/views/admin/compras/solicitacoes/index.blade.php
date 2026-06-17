@extends('layouts.master')

@section('title', 'Solicitações de Compra')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
@stop

@section('content')
    <section class="content">

        {{-- Cards de status --}}
        @include('admin.compras.solicitacoes.cards.cards-status')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Solicitações de Compra</h3>
                        <div class="card-tools">
                            <a href="{{ route('compras.solicitacoes.create') }}" class="btn btn-danger btn-xs">
                                <i class="fas fa-plus"></i> Nova Solicitação
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped compact table-bordered table-font-small" id="table-solicitacoes"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Empresa</th>
                                    <th>Solicitante</th>
                                    <th>Data</th>
                                    <th>Justificativa</th>
                                    <th>Status</th>
                                    <th>Valor Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            window.route = {
                languageDatatables: "{{ asset('vendor/datatables/pt-br.json') }}",
                list:    '{{ route('compras.solicitacoes.list') }}',
                destroy: '/compras/solicitacoes/',
            };

            $('#table-solicitacoes').DataTable({
                processing: false,
                serverSide: false,
                ajax: {
                    url: window.route.list,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Carregando...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    complete: function() {
                        Swal.close();
                    }
                },
                columns: [{
                        data: 'CD_SOLICITACAO',
                        name: 'CD_SOLICITACAO',
                        width: '60px'
                    },
                    {
                        data: 'NM_EMPRESA',
                        name: 'NM_EMPRESA'
                    },
                    {
                        data: 'nm_solicitante',
                        name: 'nm_solicitante'
                    },
                    {
                        data: 'DT_SOLICITACAO',
                        name: 'DT_SOLICITACAO',
                        className: 'text-center',
                        width: '100px',
                        render: function(data) {
                            if (!data) return '-';
                            const d = data.substring(0, 10).split('-');
                            return d.length === 3 ? d[2] + '/' + d[1] + '/' + d[0] : data;
                        }
                    },
                    {
                        data: 'DS_JUSTIFICATIVA',
                        name: 'DS_JUSTIFICATIVA'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'vl_total_fmt',
                        name: 'vl_total_fmt',
                        orderable: false,
                        width: '120px'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        width: '180px'
                    },
                ],
                pageLength: 20,
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: window.route.languageDatatables
                },
                responsive: true,
                pagingType: 'simple',
            });

            // excluir rascunho
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Excluir rascunho?',
                    text: 'A solicitação #' + id + ' será excluída permanentemente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'Não'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: window.route.destroy + id,
                        method: 'DELETE',
                        data: {
                            _token: $('[name=csrf-token]').attr('content')
                        },
                        success: function(res) {
                            if (res.errors) {
                                Swal.fire('Erro', res.errors, 'error');
                            } else {
                                Swal.fire('Excluido!', res.success, 'success').then(() => {
                                    $('#table-solicitacoes').DataTable().ajax.reload();
                                });
                            }
                        }
                    });
                });
            });

        });
    </script>
@stop
