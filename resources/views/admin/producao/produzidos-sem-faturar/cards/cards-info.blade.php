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
        max-width: 65%;
    }
    .stat-secondary { border-left-color: #6c757d; }
    .stat-secondary .stat-title i, .stat-secondary .stat-value { color: #6c757d; }
    .stat-info { border-left-color: #17a2b8; }
    .stat-info .stat-title i, .stat-info .stat-value { color: #17a2b8; }
    .stat-warning { border-left-color: #e0a800; }
    .stat-warning .stat-title i, .stat-warning .stat-value { color: #c89100; }
    .stat-danger { border-left-color: #dc3545; }
    .stat-danger .stat-title i, .stat-danger .stat-value { color: #dc3545; }
    .stat-success { border-left-color: #28a745; }
    .stat-success .stat-title i, .stat-success .stat-value { color: #28a745; }
    @media (max-width: 575px) {
        .stat-card { padding: 8px 10px; }
        .stat-card .stat-value { font-size: 0.85rem; }
        .stat-card .stat-row { font-size: 0.67rem; }
    }
</style>

<div class="row mb-1">

    {{-- Total Pneus --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-info">
            <x-loading-card />
            <div class="stat-title"><i class="far fa-dot-circle"></i> Total Pneus</div>
            <div class="stat-value pneusTotal">0</div>
        </div>
    </div>

    @hasrole('admin|supervisor|gerente unidade|gerente comercial')
    {{-- Valor --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-success">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-dollar-sign"></i> Valor</div>
            <div class="stat-value" id="valorTotal">R$ 0,00</div>
        </div>
    </div>
    @endhasrole

    {{-- Expedicionado --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-warning">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-truck"></i> Expedicionado</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label text-success"><i class="fas fa-check mr-1"></i>Sim</span>
                    <span class="stat-row-val" id="expedicionadoSim">0</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label text-danger"><i class="fas fa-times mr-1"></i>Não</span>
                    <span class="stat-row-val" id="expedicionadoNao">0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Embarque --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-secondary">
            <x-loading-card />
            <div class="stat-title"><i class="fas fa-door-open"></i> Embarque</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label text-success"><i class="fas fa-check mr-1"></i>Com embarque</span>
                    <span class="stat-row-val" id="embarqueSim">0</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label text-danger"><i class="fas fa-times mr-1"></i>Sem embarque</span>
                    <span class="stat-row-val" id="embarqueNao">0</span>
                </div>
            </div>
        </div>
    </div>

</div>
