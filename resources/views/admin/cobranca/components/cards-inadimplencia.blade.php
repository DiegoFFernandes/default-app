<style>
    .stat-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,.09);
        border-left: 4px solid;
        border-radius: 4px;
        padding: 10px 12px;
        height: 100%;
        position: relative;
    }
    .stat-card .stat-title {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-card .stat-title i { font-size: 0.7rem; }
    .stat-card .stat-value {
        font-size: 1rem;
        font-weight: 700;
        word-break: break-all;
        line-height: 1.3;
    }
    .stat-card .stat-rows { margin-top: 1px; }
    .stat-card .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        font-size: 0.71rem;
        padding: 2px 0;
        border-top: 1px solid rgba(0,0,0,.05);
    }
    .stat-card .stat-row-label { color: #6c757d; flex-shrink: 0; }
    .stat-card .stat-row-val {
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 58%;
    }

    /* Cores */
    .stat-primary { border-left-color: #007bff; }
    .stat-primary .stat-title i, .stat-primary .stat-value { color: #007bff; }

    .stat-danger { border-left-color: #dc3545; }
    .stat-danger .stat-title i, .stat-danger .stat-value { color: #dc3545; }

    .stat-info { border-left-color: #17a2b8; }
    .stat-info .stat-title i, .stat-info .stat-value { color: #17a2b8; }

    .stat-warning { border-left-color: #e0a800; }
    .stat-warning .stat-title i, .stat-warning .stat-value { color: #c89100; }

    .stat-purple { border-left-color: #6f42c1; }
    .stat-purple .stat-title i, .stat-purple .stat-value { color: #6f42c1; }

    @media (max-width: 575px) {
        .stat-card { padding: 8px 10px; }
        .stat-card .stat-value { font-size: 0.85rem; }
        .stat-card .stat-row { font-size: 0.67rem; }
    }
</style>

<div class="row mb-1">
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-primary">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-wallet"></i> Carteira Total</div>
            <div class="stat-value" id="total_carteira">0,00</div>
        </div>
    </div>

    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-danger">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-exclamation-circle"></i> Vencidos</div>
            <div class="stat-value" id="vencidos">0,00</div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-md mb-2">
        <div class="stat-card stat-info">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-clock"></i> Atrasados até 60 Dias</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Total a Receber</span>
                    <span class="stat-row-val" id="total_60_atrasados">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Atrasado</span>
                    <span class="stat-row-val" id="vl_60_atrasados">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">%</span>
                    <span class="stat-row-val" id="pc_atrasados">0%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-md mb-2">
        <div class="stat-card stat-warning">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-exclamation-triangle"></i> Inadimplência</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Total a Receber</span>
                    <span class="stat-row-val" id="total_maior_60_atrasados">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Atrasado</span>
                    <span class="stat-row-val" id="vl_maior_60_atrasados">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">%</span>
                    <span class="stat-row-val" id="pc_inadimplencia">0%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-md mb-2">
        <div class="stat-card stat-purple">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-university"></i> Cartório/Protesto</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Qtd Títulos</span>
                    <span class="stat-row-val" id="qtd_cartorio_protesto">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Valor</span>
                    <span class="stat-row-val" id="vl_cartorio_protesto">0,00</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Cartório/Prot.</span>
                    <span class="stat-row-val" id="pc_cartorio_protesto">0%</span>
                </div>
            </div>
        </div>
    </div>
</div>
