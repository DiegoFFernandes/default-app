@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body p-3">
                        <h6 class="text-muted">Olá seja bem vindo(a), {{ $user_auth->name }}!</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">View 2.0</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                                data-target="#modalPersonalizarAtalhos">
                                <i class="fas fa-star mr-1 text-warning"></i> Personalizar atalhos
                            </button>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            @forelse ($atalhos as $secao => $itens)
                                <div class="col-md-12 {{ $loop->first ? '' : 'mt-2' }}">
                                    <div class="border-bottom mb-3 pb-1">
                                        <strong>{{ $secao }}</strong>
                                    </div>
                                    <div class="atalhos-container mb-2">
                                        @foreach ($itens as $item)
                                            <a href="{{ $item['url'] }}"
                                                class="card card-outline card-dark dashboard-shortcut mb-0">

                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon">
                                                            <i class="{{ $item['icone'] }} fa-2x mb-2"></i>
                                                        </div>

                                                        <div class="text-dark">
                                                            <div class="small font-weight-bold">
                                                                {{ $item['label'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">
                                    Nenhum atalho favoritado. Clique em "Personalizar atalhos" para escolher os seus.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
                <!-- /.row -->
    </section>

    <div class="modal fade" id="modalPersonalizarAtalhos" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Personalizar atalhos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAtalhosFavoritos">
                    <div class="modal-body">
                        <p class="text-muted">Marque os atalhos que deseja manter na sua home.</p>
                        @foreach ($atalhosDisponiveis as $secao => $itens)
                            <div class="mb-3">
                                <div class="border-bottom mb-2 pb-1">
                                    <strong>{{ $secao }}</strong>
                                </div>
                                <div class="row">
                                    @foreach ($itens as $item)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="atalho-{{ $item['chave'] }}" name="atalhos[]"
                                                    value="{{ $item['chave'] }}"
                                                    {{ in_array($item['chave'], $chavesFavoritas) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="atalho-{{ $item['chave'] }}">
                                                    <i class="{{ $item['icone'] }} mr-1"></i> {{ $item['label'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('css')
    <style>
        .atalhos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }

        .dashboard-shortcut {
            transition: all .2s ease;
            text-decoration: none !important;
            color: inherit;
            border-radius: 8px !important;
        }

        .dashboard-shortcut .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-shortcut:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .14) !important;
            border-color: #adb5bd !important;
        }

        .dashboard-shortcut .card-icon {
            border-radius: 8px;
            width: 42px;
            height: 42px;
            min-width: 42px;
            margin: 0 10px 0 0;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: rgba(0, 0, 0, .06);
        }

        .dashboard-shortcut .card-icon i {
            margin: 0 !important;
            font-size: 1.2rem;
        }

        /* Tablet: 3 colunas mínimas */
        @media (max-width: 768px) {
            .atalhos-container {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        /* Mobile: 2 colunas fixas */
        @media (max-width: 480px) {
            .atalhos-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .dashboard-shortcut .card-body {
                padding: 8px 10px !important;
            }

            .dashboard-shortcut .card-icon {
                width: 36px;
                height: 36px;
                min-width: 36px;
                margin-right: 8px;
            }

            .dashboard-shortcut .card-icon i {
                font-size: 1rem;
            }

            .dashboard-shortcut .small {
                font-size: 0.72rem;
            }

            .dashboard-shortcut small.text-muted {
                font-size: 0.65rem;
            }
        }
    </style>
@stop
@section('js')
    <script>
        $('#formAtalhosFavoritos').on('submit', function(e) {
            e.preventDefault();

            const atalhos = $(this).find('input[name="atalhos[]"]:checked')
                .map(function() {
                    return this.value;
                }).get();

            $.ajax({
                method: 'POST',
                url: "{{ route('home.atalhos-favoritos') }}",
                data: {
                    _token: $('[name=csrf-token]').attr('content'),
                    atalhos: atalhos,
                },
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atalhos salvos!',
                        confirmButtonColor: '#dc3545',
                    }).then(function() {
                        window.location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao salvar atalhos',
                        text: 'Por favor, tente novamente.',
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        });
    </script>
@stop
