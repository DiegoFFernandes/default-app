<div class="row">

    <div class="col-md-3 col-sm-4 col-xs-6">
        <div class="info-box">
            <x-loading-card />
            <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="far fa-dot-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Pneus</span>
                <span class="info-box-number pneusTotal"></span>
            </div>
        </div>
    </div>

    @hasrole('admin|supervisor|gerente unidade|gerente comercial')
        <div class="col-md-3 col-sm-4 col-xs-6">
            <div class="info-box">
                <x-loading-card />
                <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor</span>
                    <span class="info-box-number" id="valorTotal"></span>
                </div>
            </div>
        </div>
    @endhasrole

    <div class="col-md-3 col-sm-4 col-xs-6">
        <div class="info-box">
            <x-loading-card />
            <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Expedicionado</span>
                <span class="info-box-number" id="expedicionado"></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6">
        <div class="info-box">
            <x-loading-card />
            <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-door-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Embarque</span>
                <span class="info-box-number" id="embarque"></span>
            </div>
        </div>
    </div>
</div>
