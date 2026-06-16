<div class="row">
    <div class="col-6 col-sm-6 col-md-4 col-lg-4">
        <div class="info-box info-box-custom">
            <span class="info-box-icon bg-secondary"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rascunhos</span>
                <span class="info-box-number">{{ $counts->get('RAS', 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-4 col-lg-4">
        <div class="info-box info-box-custom">
            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Em Aprovação</span>
                <span class="info-box-number">{{ $counts->get('APR', 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-4 col-lg-4">
        <div class="info-box info-box-custom">
            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Reprovadas</span>
                <span class="info-box-number">{{ $counts->get('REP', 0) }}</span>
            </div>
        </div>
    </div>
</div>
