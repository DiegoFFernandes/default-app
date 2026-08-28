<div class="card-header p-0 d-flex justify-content-between align-items-center">
    <div class="flex-grow-1">
        <ul class="nav nav-tabs border-bottom-0" id="tabCarcacas" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-carcaca-entrada" data-toggle="pill" href="#painel-carcaca-entrada"
                    role="tab" aria-controls="painel-carcaca-entrada" aria-selected="true">
                    <i class="fas fa-sign-in-alt mr-1"></i> Entradas
                </a>
            </li>
            <li class="nav-item" @if ($canEdit) style="display:none;" @endif>
                <a class="nav-link" id="tab-carcaca-saida" data-toggle="pill" href="#painel-carcaca-saida" role="tab"
                    aria-controls="painel-carcaca-saida" aria-selected="false">
                    <i class="fas fa-sign-out-alt mr-1"></i> Saídas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-carcaca-pronta" data-toggle="pill" href="#painel-carcaca-pronta" role="tab"
                    aria-controls="painel-carcaca-pronta" aria-selected="false">
                    <i class="fas fa-warehouse mr-1"></i> Prontos Deposito
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-carcaca-pronta-terceiros" data-toggle="pill" href="#painel-carcaca-pronta-terceiros" role="tab"
                    aria-controls="painel-carcaca-pronta-terceiros" aria-selected="false">
                    <i class="fas fa-handshake mr-1"></i> Prontos em Terceiros
                </a>
            </li>
        </ul>
    </div>
</div>
