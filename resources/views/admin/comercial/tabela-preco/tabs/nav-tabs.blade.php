<div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-inserir" data-toggle="tab" href="#painel-inserir" role="tab">Inserir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-cadastradas" data-toggle="tab" href="#painel-cadastradas"
                role="tab">Cadastradas</a>
        </li>
        @if (auth()->user()->hasRole('admin|diretoria|gerente comercial|usuario comercial'))
            <li class="nav-item">
                <a class="nav-link" id="tab-associadas" data-toggle="tab" href="#painel-associadas"
                    role="tab">Associadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-divergencia" data-toggle="tab" href="#painel-divergencia"
                    role="tab">Divergências</a>
            </li>
        @endif
    </ul>
</div>
