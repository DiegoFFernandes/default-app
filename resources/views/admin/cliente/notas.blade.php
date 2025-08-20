@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Notas Emitidas</span>
                        <span class="info-box-number" id="totalNotas">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Pneus</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filtro (mudar de acordo com a nescessidade da página) -->
        <div class="row">
            <div class="col-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h5 class="card-title">Filtros</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2 mb-2">
                                <label for="filtro-nr-documento">Número da Nota</label>
                                <input type="text" class="form-control mt-1" id="filtro-nr-documento"
                                    placeholder="Digite o número da nota">
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="daterange">Data:</label>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="daterange"
                                            placeholder="Selecione a Data">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-notasEmitidas" data-toggle="pill"
                                    href="#painel-notasEmitidas" role="tab" aria-controls="painel-notasEmitidas"
                                    aria-selected="true">
                                    Notas Emitidas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-boletosAbertos" data-toggle="pill" href="#painel-boletosAbertos"
                                    role="tab" aria-controls="painel-boletosAbertos" aria-selected="false">
                                    Boletos em Aberto
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-notasEmitidas" role="tabpanel"
                                aria-labelledby="tab-notasEmitidas">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="table-notas-emitidas"
                                            class="table table-bordered table-striped compact table-font-small">
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-boletosAbertos" role="tabpanel"
                                aria-labelledby="tab-boletosAbertos">
                                <!-- tab boletosAbertos -->
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
        .info-box-text {
            font-size: 14px;
        }

        .info-box-number {
            font-weight: bold;
            font-size: 18px;
        }

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.6);
        }

        .btn-primary {
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.6);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#table-notas-emitidas').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 100,
                order: [[0, 'desc']], //exibe as ultimas 100 notas
                ajax: {
                    url: '{{ route('get-list-nota-emitida') }}',
                    data: function(d) {
                        d.numero_nota = $('#filtro-nr-documento').val();
                    }
                },
                columns: [{
                        data: 'NR_LANCAMENTO',
                        name: 'NR_LANCAMENTO',
                        title: 'Nº Lançamento'
                    },
                    {
                        data: 'NM_EMPRESA',
                        name: 'NM_EMPRESA',
                        title: 'Empresa'
                    },
                    {
                        data: 'NR_NOTA',
                        name: 'NR_NOTA',
                        title: 'Nº Nota'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente'
                    },
                    {
                        data: 'DS_DTEMISSAO',
                        name: 'DS_DTEMISSAO',
                        title: 'Data Emissão'
                    },
                    {
                        data: 'VL_CONTABIL',
                        name: 'VL_CONTABIL',
                        title: 'Valor Total'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Ações',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var data = api.rows({
                        search: 'applied'
                    }).data();

                    var countNotas = data.length;

                    //atualiza o card de totais de notas
                    $('#totalNotas').eq(0).text(countNotas);
                }
            });

            // filtrar por data
            $.fn.dataTable.ext.search.push((settings, data) => {
                const r = $('#daterange').val()?.split(' - ');
                if (!r || r.length < 2) return true;
                const d = s => { const [day, m, y] = s.split('/'); return new Date(y, m-1, day); };
                const rowDate = d(data[4]);
                return rowDate >= d(r[0]) && rowDate <= d(r[1]);
            });

            $('#submit-seach').on('click', function() {
                table.draw();
            });

            // filtro por número da nota
            $('#filtro-nr-documento').on('keyup change', function() {
                table.column(2).search(this.value).draw();
            });
        });
    </script>
@stop
