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

@php
    $fmt = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');
    $urg = function($stat) {
        $rows = [];
        if (($stat?->QT_I ?? 0) > 0) $rows[] = ['text-danger',    'bolt',       'Imediato',   $stat->QT_I];
        if (($stat?->QT_U ?? 0) > 0) $rows[] = ['text-warning',   'exclamation','Urgente',    $stat->QT_U];
        if (($stat?->QT_N ?? 0) > 0) $rows[] = ['text-secondary', 'minus',      'Necessário', $stat->QT_N];
        return $rows;
    };
@endphp

<div class="row mb-1">

    {{-- Rascunho --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-secondary">
            <div class="stat-title"><i class="fas fa-file-alt"></i> Rascunhos</div>
            <div class="stat-value">{{ $stats->get('RAS')?->QT ?? 0 }}</div>
            @if($urgRows = $urg($stats->get('RAS')))
            <div class="stat-rows">
                @foreach($urgRows as [$cor, $icon, $label, $qt])
                <div class="stat-row">
                    <span class="stat-row-label {{ $cor }}"><i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}</span>
                    <span class="stat-row-val">{{ $qt }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Análise de Compra --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-info">
            <div class="stat-title"><i class="fas fa-search"></i> Análise de Compra</div>
            <div class="stat-value">{{ $stats->get('ANA')?->QT ?? 0 }}</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Valor estimado</span>
                    <span class="stat-row-val">{{ $fmt($stats->get('ANA')?->VL_TOTAL) }}</span>
                </div>
                @foreach($urg($stats->get('ANA')) as [$cor, $icon, $label, $qt])
                <div class="stat-row">
                    <span class="stat-row-label {{ $cor }}"><i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}</span>
                    <span class="stat-row-val">{{ $qt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Em Aprovação --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-warning">
            <div class="stat-title"><i class="fas fa-clock"></i> Em Aprovação</div>
            <div class="stat-value">{{ $stats->get('APR')?->QT ?? 0 }}</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Valor total</span>
                    <span class="stat-row-val">{{ $fmt($stats->get('APR')?->VL_TOTAL) }}</span>
                </div>
                @foreach($urg($stats->get('APR')) as [$cor, $icon, $label, $qt])
                <div class="stat-row">
                    <span class="stat-row-label {{ $cor }}"><i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}</span>
                    <span class="stat-row-val">{{ $qt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Reprovadas --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-danger">
            <div class="stat-title"><i class="fas fa-times-circle"></i> Reprovadas</div>
            <div class="stat-value">{{ $stats->get('REP')?->QT ?? 0 }}</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Valor total</span>
                    <span class="stat-row-val">{{ $fmt($stats->get('REP')?->VL_TOTAL) }}</span>
                </div>
                @foreach($urg($stats->get('REP')) as [$cor, $icon, $label, $qt])
                <div class="stat-row">
                    <span class="stat-row-label {{ $cor }}"><i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}</span>
                    <span class="stat-row-val">{{ $qt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Finalizadas --}}
    <div class="col-6 col-md mb-2">
        <div class="stat-card stat-success">
            <div class="stat-title"><i class="fas fa-flag-checkered"></i> Finalizadas</div>
            <div class="stat-value">{{ $stats->get('FIN')?->QT ?? 0 }}</div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="stat-row-label">Valor total</span>
                    <span class="stat-row-val">{{ $fmt($stats->get('FIN')?->VL_TOTAL) }}</span>
                </div>
                @foreach($urg($stats->get('FIN')) as [$cor, $icon, $label, $qt])
                <div class="stat-row">
                    <span class="stat-row-label {{ $cor }}"><i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}</span>
                    <span class="stat-row-val">{{ $qt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
