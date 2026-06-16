@extends('layouts.master')

@section('title', 'Aprovações Pendentes')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Aprovações Pendentes para Mim</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered compact table-font-small" id="table-aprovacoes" style="width:100%">
                        <thead>
                            <tr>
                                <th>Solicitação</th>
                                <th>Empresa</th>
                                <th>Data Sol.</th>
                                <th>Justificativa</th>
                                <th>Meu Cargo</th>
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

{{-- Modal Reprovar --}}
@include('admin.compras.aprovacoes.modals.modal-reprovar')
@stop

@section('js')
<script>
$(document).ready(function () {

    const token = $('[name=csrf-token]').attr('content');

    const dt = $('#table-aprovacoes').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route('compras.aprovacoes.list') }}',
            beforeSend: function () {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            complete: function () {
                Swal.close();
            }
        },
        columns: [
            { data: 'CD_SOLICITACAO',  name: 'CD_SOLICITACAO', width: '90px' },
            { data: 'NM_EMPRESA',      name: 'NM_EMPRESA' },
            { data: 'DT_SOLICITACAO',  name: 'DT_SOLICITACAO', width: '100px' },
            { data: 'DS_JUSTIFICATIVA',name: 'DS_JUSTIFICATIVA' },
            { data: 'DS_CARGO',        name: 'DS_CARGO', width: '120px' },
            { data: 'vl_total_fmt',    name: 'vl_total_fmt', orderable: false, width: '110px' },
            { data: 'Actions',         name: 'Actions', orderable: false, searchable: false, width: '200px' },
        ],
        pageLength: 20,
        language: { url: '{{ asset('vendor/datatables/pt-br.json') }}' },
        responsive: true,
    });

    // Aprovar
    $('body').on('click', '.btn-aprovar', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Aprovar solicitação?',
            input: 'text',
            inputLabel: 'Observação (opcional)',
            inputPlaceholder: 'Deixe em branco se não houver observações',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Aprovar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post('{{ route('compras.aprovacoes.aprovar') }}', {
                _token:          token,
                id_etapa:        id,
                ds_observacao:   result.value || '',
            }, function (res) {
                if (res.errors) {
                    Swal.fire('Atenção', res.errors, 'warning');
                } else {
                    Swal.fire('Aprovado!', res.success, 'success');
                    dt.ajax.reload();
                }
            });
        });
    });

    // Reprovar — abre modal
    $('body').on('click', '.btn-reprovar', function () {
        $('#reprovar_id_etapa').val($(this).data('id'));
        $('#reprovar_motivo').val('');
        $('#modal-reprovar').modal('show');
    });

    $('#btn-confirmar-reprovar').click(function () {
        const motivo = $('#reprovar_motivo').val().trim();
        if (!motivo) {
            Swal.fire('Atenção', 'O motivo da reprovação é obrigatório.', 'warning');
            return;
        }
        $.post('{{ route('compras.aprovacoes.reprovar') }}', {
            _token:        token,
            id_etapa:      $('#reprovar_id_etapa').val(),
            ds_observacao: motivo,
        }, function (res) {
            $('#modal-reprovar').modal('hide');
            if (res.errors) {
                Swal.fire('Erro', res.errors, 'error');
            } else {
                Swal.fire('Reprovado', res.success, 'success');
                dt.ajax.reload();
            }
        });
    });

});
</script>
@stop
