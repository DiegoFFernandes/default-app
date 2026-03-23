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
        @include('admin.producao.producao-executor.components.resumo', [
            'painelPrincipal' => $painelPrincipal,
        ])
    </div>
</div>
