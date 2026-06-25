<div class="card shadow-sm flex-fill">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs grupo-tabs grupo-tabs-resumo nav-tabs-resumo"
            id="navs-tabs-resumo-{{ $painelPrincipal }}" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="resumo-setor-tab-{{ $painelPrincipal }}"
                    data-toggle="pill" href="#resumo-setor-{{ $painelPrincipal }}" role="tab"
                    aria-controls="resumo-setor-{{ $painelPrincipal }}" aria-selected="true">
                    <i class="fas fa-industry mr-1"></i> Por Setor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="resumo-executor-tab-{{ $painelPrincipal }}"
                    data-toggle="pill" href="#resumo-executor-{{ $painelPrincipal }}" role="tab"
                    aria-controls="resumo-executor-{{ $painelPrincipal }}" aria-selected="false">
                    <i class="fas fa-user-cog mr-1"></i> Por Executor
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-2">
        <div class="tab-content" id="resumo-tabs-content-{{ $painelPrincipal }}">
            <div class="tab-pane fade show active" id="resumo-setor-{{ $painelPrincipal }}"
                role="tabpanel" aria-labelledby="resumo-setor-tab-{{ $painelPrincipal }}"
                data-tabela="setorResumo-{{ $painelPrincipal }}">
                <table id="setorResumo-{{ $painelPrincipal }}"
                    class="table compact table-bordered table-striped table-font-small">
                    <thead></thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            <div class="tab-pane fade" id="resumo-executor-{{ $painelPrincipal }}"
                role="tabpanel" aria-labelledby="resumo-executor-tab-{{ $painelPrincipal }}"
                data-tabela="executorResumo-{{ $painelPrincipal }}">
                <table id="executorResumo-{{ $painelPrincipal }}"
                    class="table compact table-bordered table-striped table-font-small">
                    <thead></thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
