<div class="row">
    <div class="col-12 col-lg-8 mb-3 d-flex flex-column">
        <div class="card shadow-sm border-0 flex-fill">
            @include('admin.producao.producao-executor.tabs.navs-tabs', [
                'painelPrincipal' => $painelPrincipal,
            ])
            <div class="card-body">
                <span class="badge badge-info badge-empresa">Empresa:</span>
                <span class="badge badge-info badge-periodo">Periodo:</span>
                <div class="tab-content">
                    @include('admin.producao.producao-executor.tabs.painel-executor-etapa', [
                        'painelPrincipal' => $painelPrincipal,
                    ])
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 mb-3 d-flex flex-column">
        <div class="card shadow-sm border-0 flex-fill">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="resumo-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="resumo-setor-tab" data-toggle="pill" href="#resumo-setor"
                            role="tab" aria-controls="resumo-setor" aria-selected="true">Resumo por Setor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resumo-executor-tab" data-toggle="pill" href="#resumo-executor"
                            role="tab" aria-controls="resumo-executor" aria-selected="false">Resumo por
                            Executor</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="resumo-tabs-content">
                    <div class="tab-pane fade show active" id="resumo-setor" role="tabpanel"
                        aria-labelledby="resumo-setor-tab">
                        <div class="table-responsive">
                            <table id="setorResumo" class="table compact table-bordered table-striped"
                                style="font-size: 12px;"></table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="resumo-executor" role="tabpanel"
                        aria-labelledby="resumo-executor-tab">
                        <div class="table-responsive">
                            <table id="executorResumo" class="table compact table-bordered table-striped"
                                style="font-size: 12px;"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
