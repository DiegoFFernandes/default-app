<div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" id="tabCarcacas" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-carcaca-entrada" data-toggle="pill" href="#painel-carcaca-entrada"
                role="tab" aria-controls="painel-carcaca-entrada" aria-selected="true">
                Entradas
            </a>
        </li>
        <li class="nav-item" @if ($canEdit) style="display:none;" @endif>
            <a class="nav-link" id="tab-carcaca-saida" data-toggle="pill" href="#painel-carcaca-saida" role="tab"
                aria-controls="painel-carcaca-saida" aria-selected="false">
                Saídas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-carcaca-pronta" data-toggle="pill" href="#painel-carcaca-pronta" role="tab"
                aria-controls="painel-carcaca-pronta" aria-selected="false">
                Prontos
            </a>
        </li>
    </ul>
</div>
