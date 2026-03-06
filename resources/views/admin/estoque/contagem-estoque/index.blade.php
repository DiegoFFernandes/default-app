@extends('layouts.master')

@section('title', 'Lotes de Estoque')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-4 mb-3">
                <div class="card card-dark card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Contagem Estoque</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="tp_lote" class="small">Tipo Lote</label>
                            <select class="form-control form-control-sm" id="tp_lote">
                                <option value="I" selected>Inventario</option>
                                <option value="E">Entrada</option>
                                <option value="T">Transferencia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tp_produto" class="small">Categoria</label>
                            <select class="form-control form-control-sm" id="tp_produto">
                                <option value="0">Selecione</option>
                                <option value="1">Banda</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cd_marca" class="small">Marca do Produto</label>
                            <select class="form-control form-control-sm" id="cd_marca" style="width: 100%">
                                <option value="0">Selecione</option>
                                @foreach ($marcaLote as $m)
                                    <option value="{{ $m->id }}">{{ $m->ds_marca_lote }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ds_lote" class="small">Descrição</label>
                            <input type="text" class="form-control form-control-sm" id="ds_lote"
                                placeholder="Descrição para o Lote: Banda/Consertos...">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-xs btn-success float-right" id="btnCriarLote">Criar Lote</button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8 mb-3">
                <div class="card card-dark card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lotes Criados</h3>
                    </div>
                    <div class="card-body">
                        <table id="table-lote" class="table table-bordered table-font-small">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        @supports (-webkit-touch-callout: none) {

            input,
            select,
            textarea {
                font-size: 16px;
            }
        }

        .form-control {
            font-size: 16px;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript">
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getLotes: "{{ route('estoque.get-lotes') }}",
            criarLote: "{{ route('estoque.cria-lote') }}",
            deleteLote: "{{ route('estoque.delete-lote') }}",
        }        

        $('#table-lote').DataTable({
            processing: false,
            serverSide: false,
            responsive: true,
            language: {
                url: window.routes.languageDatatables,
            },
            pagingType: "simple",
            ajax: {

                url: window.routes.getLotes
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    title: 'ID'
                },
                {
                    data: 'descricao',
                    name: 'descricao',
                    title: 'Descrição'
                },
                {
                    data: 'tp_produto',
                    name: 'tp_produto',
                    title: 'Produto'
                },
                {
                    data: 'ds_marca',
                    name: 'ds_marca',
                    title: 'Marca'
                },
                {
                    data: 'qtd_itens',
                    name: 'qtd_itens',
                    title: 'Qtde'
                },
                {
                    data: 'ps_liquido_total',
                    name: 'ps_liquido_total',
                    title: 'Peso',
                    render: function(data, type, full, meta) {
                        // Formata o valor numérico para usar ponto como separador decimal e vírgula como separador de milhar
                        return parseFloat(data).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title: 'Status'
                },
                {
                    data: 'tp_lote',
                    name: 'tp_lote',
                    title: 'Tipo Lote'
                },
                {
                    data: 'cd_usuario',
                    name: 'cd_usuario',
                    title: 'Usuário',
                    visible: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Criado em'
                },
                {
                    data: 'Actions',
                    name: 'Actions',
                    title: 'Ações',
                    orderable: false,
                    serachable: false,
                    sClass: 'text-center text-nowrap',
                },
            ],


            order: [
                [0, "desc"]
            ],
        });

        $('#btnCriarLote').click(function() {

            let marca = $('#cd_marca').val();

            $.ajax({
                url: window.routes.criarLote,
                method: 'GET',
                data: {
                    tp_lote: $('#tp_lote').val(),
                    tp_produto: $('#tp_produto').val(),
                    cd_marca: marca,
                    ds_lote: $("#ds_lote").val(),
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function() {

                },
                success: function(result) {
                    $("#loading").addClass('hidden');
                    if (result.errors) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: result.errors,
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso',
                            text: result.success,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                    $('#table-lote').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {

                        let response = xhr.responseJSON;

                        let mensagens = '<ul class="text-left">';

                        response.errors.forEach(function(erro) {
                            mensagens += `<li>${erro}</li>`;
                        });

                        mensagens += '</ul>';

                        Swal.fire({
                            icon: 'warning',
                            title: 'Erro de validação',
                            html: mensagens
                        });
                    }
                }
            });
        });

        $('#table-lote').on('click', '.delete', function() {
            let id_lote = $(this).data();
            $.ajax({
                method: 'DELETE',
                url: window.routes.deleteLote,
                data: {
                    idlote: id_lote['idlote'],
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function() {
                    $('#loading').removeClass('hidden');
                },
                success: function(result) {
                    $('#loading').addClass('hidden');
                    if (result.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: result.error,
                        });
                        return false;
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso',
                            text: result.success,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        $('#table-lote').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao excluir o lote. Por favor, tente novamente.',
                    });
                }
            });
        });
    </script>
@endsection
