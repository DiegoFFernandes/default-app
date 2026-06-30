<div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-inserir" data-toggle="tab" href="#painel-inserir" role="tab">
                <i class="fas fa-plus-circle mr-1"></i>
                <span class="d-none d-sm-inline">Inserir</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-cadastradas" data-toggle="tab" href="#painel-cadastradas" role="tab">
                <i class="fas fa-table mr-1"></i>
                <span class="d-none d-sm-inline">Cadastradas</span>
            </a>
        </li>
        @if (auth()->user()->hasRole('admin|diretoria|gerente comercial|usuario comercial'))
            <li class="nav-item">
                <a class="nav-link" id="tab-associadas" data-toggle="tab" href="#painel-associadas" role="tab">
                    <i class="fas fa-link mr-1"></i>
                    <span class="d-none d-sm-inline">Associadas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-divergencia" data-toggle="tab" href="#painel-divergencia" role="tab">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span class="d-none d-sm-inline">Divergências</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-item-faltante" data-toggle="tab" href="#painel-item-faltante" role="tab">
                    <i class="fas fa-search-minus mr-1"></i>
                    <span class="d-none d-sm-inline">Itens Faltantes</span>
                </a>
            </li>
        @endif
    </ul>
</div>
