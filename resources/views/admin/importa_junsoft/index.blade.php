@extends('layouts.master')

@section('title', 'Importação Junsoft')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-secondary card-outline">
                    @include('components.loading-card')
                    <div class="card-header">
                        <h3 class="card-title">Importar Item</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
                            <label>Importar por Marca</label>
                            <select id="importa-produto" class="form-control form-control-sm" style="width: 100%;">
                                <option selected="selected" value="">Selecione a marca</option>
                                @foreach ($marcas as $m)
                                    <option value="{{ $m->CD_MARCA }}">{{ $m->DS_MARCA }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="submit-importa" class="btn btn-sm btn-secondary">Importar Item</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script type="text/javascript">
        $('#importa-produto').select2({
            theme: 'bootstrap4'
        });

        $('#submit-importa').click(function() {

            if($("#importa-produto option:selected").val() == ""){
                Swal.fire({
                    title: 'Atenção',
                    text: 'Selecione uma marca para importar os itens',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }


            $.ajax({
                url: "{{ route('importa-item.index') }}",
                method: 'GET',
                data: {
                    cd_marca: $("#importa-produto option:selected").val(),
                    _token: $("#_token").val()
                },
                beforeSend: function() {
                    $('.loading-card').removeClass('invisible');
                },
                success: function(response) {                

                    if (response.success) {
                        Swal.fire({
                            title: 'Sucesso',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        $('.loading-card').addClass('invisible');
                    } else {
                        Swal.fire({
                            title: 'Erro',
                            text: response.error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        $('.loading-card').addClass('invisible');
                    }

                }
            });
        });
    </script>
@stop
