@extends('layouts.master')

@section('title', $title_page)

@section('css')
<style>
    .kanban-wrapper {
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .kanban-board {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        min-width: max-content;
    }
    .kanban-col {
        width: 240px;
        background: #f1f3f5;
        border-radius: 6px;
        padding: 8px;
        flex-shrink: 0;
    }
    .kanban-col-header {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 2px 4px 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .kanban-cards {
        max-height: calc(100vh - 260px);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .kanban-card {
        background: #fff;
        border-radius: 5px;
        padding: 8px 10px;
        border-left: 3px solid currentColor;
        box-shadow: 0 1px 3px rgba(0,0,0,.07);
        text-decoration: none;
        color: #212529;
        display: block;
        transition: box-shadow 0.12s, transform 0.12s;
    }
    .kanban-card:hover {
        box-shadow: 0 3px 9px rgba(0,0,0,.14);
        transform: translateY(-1px);
        color: #212529;
        text-decoration: none;
    }
    .kanban-card-id {
        font-size: 0.68rem;
        color: #adb5bd;
        margin-bottom: 2px;
    }
    .kanban-card-empresa {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kanban-card-cc {
        font-size: 0.68rem;
        color: #868e96;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kanban-card-sol {
        font-size: 0.7rem;
        color: #6c757d;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .kanban-card-data {
        font-size: 0.65rem;
        color: #adb5bd;
    }
    .kanban-card-valor {
        font-size: 0.7rem;
        font-weight: 600;
        color: #28a745;
    }
    /* borda lateral por status */
    .kanban-col-RAS .kanban-card { color: #6c757d; }
    .kanban-col-ANA .kanban-card { color: #17a2b8; }
    .kanban-col-APR .kanban-card { color: #e0a800; }
    .kanban-col-APC .kanban-card { color: #007bff; }
    .kanban-col-REP .kanban-card { color: #dc3545; }
    .kanban-col-FIN .kanban-card { color: #28a745; }

    .kanban-empty {
        text-align: center;
        padding: 14px 0;
        font-size: 0.72rem;
        color: #adb5bd;
    }
</style>
@stop

@section('content')
<section class="content">
    <div class="card card-outline card-secondary mb-0">
        <div class="card-header py-2">
            <h3 class="card-title"><i class="fas fa-columns mr-1"></i> Kanban — Solicitações</h3>
            <div class="card-tools">
                <a href="{{ route('compras.solicitacoes.index') }}" class="btn btn-default btn-xs mr-1" title="Visão em lista">
                    <i class="fas fa-list"></i> Lista
                </a>
                @can('solicitacao-compra-criar')
                <a href="{{ route('compras.solicitacoes.create') }}" class="btn btn-danger btn-xs">
                    <i class="fas fa-plus"></i> Nova Solicitação
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-2">
            <div class="kanban-wrapper">
                <div class="kanban-board">
                    @foreach($colunas as $status => [$cor, $icon, $label])
                    @php
                        $cards = $grupos->get($status, collect());
                        $qt    = count($cards);
                    @endphp
                    <div class="kanban-col kanban-col-{{ $status }}">
                        <div class="kanban-col-header text-{{ $cor }}">
                            <span><i class="fas {{ $icon }} mr-1"></i>{{ $label }}</span>
                            <span class="badge badge-{{ $cor }}">{{ $qt }}</span>
                        </div>
                        <div class="kanban-cards">
                            @forelse($cards as $s)
                            @php
                                $urgMap  = ['I' => ['danger','Imediato'], 'U' => ['warning','Urgente'], 'N' => ['secondary','Necessário']];
                                [$urgCor, $urgLabel] = $urgMap[$s->ST_URGENCIA ?? 'N'] ?? ['secondary','Necessário'];

                                $dtRaw  = $s->DT_SOLICITACAO ?? '';
                                $dtPts  = explode('-', substr($dtRaw, 0, 10));
                                $dataBr = count($dtPts) === 3 ? "{$dtPts[2]}/{$dtPts[1]}/{$dtPts[0]}" : '-';

                                $nomeCompleto = $users[$s->CD_USUARIO_SOLICITANTE] ?? null;
                                $nmSol        = $nomeCompleto ? explode(' ', $nomeCompleto)[0] . ' ' . (explode(' ', $nomeCompleto)[1] ?? '') : '-';
                                $nmSol        = trim($nmSol);
                            @endphp
                            <a href="{{ route('compras.solicitacoes.show', $s->CD_SOLICITACAO) }}" class="kanban-card">
                                <div class="kanban-card-id">#{{ $s->CD_SOLICITACAO }}</div>
                                <div class="kanban-card-empresa" title="{{ $s->NM_EMPRESA }}">{{ $s->NM_EMPRESA }}</div>

                                @if(($s->DS_CENTROCUSTO ?? '-') !== '-')
                                <div class="kanban-card-cc" title="{{ $s->DS_CENTROCUSTO }}">{{ $s->DS_CENTROCUSTO }}</div>
                                @endif

                                <div class="kanban-card-sol" title="{{ $nomeCompleto }}">{{ $nmSol }}</div>

                                <div class="kanban-card-footer">
                                    <span class="badge badge-{{ $urgCor }}" style="font-size:0.62rem">{{ $urgLabel }}</span>
                                    <span class="kanban-card-data">{{ $dataBr }}</span>
                                </div>

                                @if($s->VL_TOTAL)
                                <div class="kanban-card-valor mt-1">
                                    R$ {{ number_format((float)$s->VL_TOTAL, 2, ',', '.') }}
                                </div>
                                @endif
                            </a>
                            @empty
                            <div class="kanban-empty">
                                <i class="fas fa-inbox"></i><br>Nenhuma
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@stop
