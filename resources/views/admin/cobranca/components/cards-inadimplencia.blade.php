<div class="row">
    <div class="col-6 col-md-2">
        <div class="info-box equal-info-box bg-gradient-light border">
            {{-- Icon loading --}}
            <div class="invisible info-loading loading-card">
                <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                    <div class="text-bold pt-2"></div>
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Carteira Total</span>
                <span class="info-box-number font-weight-bold" id="total_carteira">0,00</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="info-box equal-info-box bg-gradient-light border">
            {{-- Icon loading --}}
            <div class="invisible info-loading loading-card">
                <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                    <div class="text-bold pt-2"></div>
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Vencidos</span>
                <span class="info-box-number" id="vencidos">0,00</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-2">
        <div class="info-box equal-info-box bg-info">
            {{-- Icon loading --}}
            <div class="invisible info-loading loading-card">
                <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                    <div class="text-bold pt-2"></div>
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Atrasados até 60 Dias</span>
                <div class="d-flex justify-content-between">
                    <small>Total</small>
                    <small class="info-box-number" id="total_60_atrasados">0,00</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small>Valor</small>
                    <small class="info-box-number" id="vl_60_atrasados">0,00</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small>Atraso</small>
                    <small class="info-box-number" id="pc_atrasados">0%</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-2">
        {{-- Icon loading --}}
        <div class="invisible info-loading loading-card">
            <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                <div class="text-bold pt-2"></div>
            </div>
        </div>
        <div class="info-box bg-warning">
            <div class="info-box-content">
                <span class="info-box-text">Inadimplência</span>
                <div class="d-flex justify-content-between">
                    <small>Total</small>
                    <small class="info-box-number" id="total_maior_60_atrasados">0,00</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small>Valor</small>
                    <small class="info-box-number" id="vl_maior_60_atrasados">0,00</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small>Inadimplencia</small>
                    <small class="info-box-number" id="pc_inadimplencia">0%</small>
                </div>
            </div>
        </div>
    </div>
</div>
