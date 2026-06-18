<div class="tab-pane fade" id="painel-lista" role="tabpanel" aria-labelledby="tab-lista">
    <div class="card-body p-2">

        {{-- ── Cards de resumo ───────────────────────────────────────────── --}}
        <div class="row p-2 mb-2 bg-light rounded" id="painel-stats" style="display:none;">
            <div class="col-12 mb-2 d-flex align-items-center justify-content-end">
                <small class="text-muted mr-2">Período:</small>
                <div class="btn-group btn-group-sm" id="grupo-periodo">
                    <button class="btn btn-outline-secondary btn-periodo" data-days="7">7d</button>
                    <button class="btn btn-outline-secondary btn-periodo" data-days="15">15d</button>
                    <button class="btn btn-secondary btn-periodo" data-days="30">30d</button>
                    <button class="btn btn-outline-secondary btn-periodo" data-days="60">60d</button>
                    <button class="btn btn-outline-secondary btn-periodo" data-days="0">Tudo</button>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-danger">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number" id="stat-total-valor">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-calculator"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média</span>
                        <span class="info-box-number" id="stat-media-valor">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-arrow-up"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Maior</span>
                        <span class="info-box-number" id="stat-maior-valor">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-receipt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lançamentos</span>
                        <span class="info-box-number" id="stat-qtd-lancamentos">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Usuários</span>
                        <span class="info-box-number" id="stat-qtd-usuarios">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="info-box info-box-custom">
                    <span class="info-box-icon bg-secondary">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Não Vistos</span>
                        <span class="info-box-number" id="stat-nao-vistos">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Filtros ──────────────────────────────────────────────────────── --}}
        <div class="card collapsed-card mb-2">
            <div class="card-header py-1 px-2 d-flex align-items-center">
                <h6 class="card-title small mb-0 flex-grow-1">
                    <i class="fas fa-filter mr-1 text-secondary"></i> Filtros
                </h6>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool btn-xs" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body py-2 px-2">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 mb-1" id="col-filtro-usuario">
                        <input id="filtro-usuario" type="text" class="form-control form-control-sm"
                            placeholder="Usuário">
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-1">
                        <select id="filtro-tipo" class="form-control form-control-sm">
                            <option value="">Todos os Tipos</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Combustível">Combustível</option>
                            <option value="Hospedagem">Hospedagem</option>
                            <option value="Pedágio">Pedágio</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-1">
                        <select id="filtro-visto" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <option value="Sim">Visto: Sim</option>
                            <option value="Não">Visto: Não</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-1">
                        <input id="filtro-data" type="text" class="form-control form-control-sm"
                            placeholder="dd/mm/aaaa">
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-1 d-flex align-items-center">
                        <button type="button" class="btn btn-xs btn-secondary btn-block" id="btn-limpar-filtros">
                            <i class="fas fa-times mr-1"></i> Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DataTable ────────────────────────────────────────────────────── --}}
        <table class="table table-bordered compact table-font-small mb-2" id="tabela-comprovantes">
        </table>

        {{-- ── Gráficos — linha 1 ────────────────────────────────────────── --}}
        <div id="painel-graficos" class="mt-2" style="display:none;">
            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="card card-outline card-secondary mb-2">
                        <div class="card-header py-1 px-2">
                            <h6 class="card-title small mb-0"><i class="fas fa-user mr-1"></i>Valor por Usuário</h6>
                        </div>
                        <div class="card-body p-1" style="height:220px;overflow:hidden;">
                            <canvas id="chart-usuario-valor"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card card-outline card-secondary mb-2">
                        <div class="card-header py-1 px-2">
                            <h6 class="card-title small mb-0"><i class="fas fa-chart-pie mr-1"></i>Tipo de Despesa</h6>
                        </div>
                        <div class="card-body p-1" style="height:220px;">
                            <canvas id="chart-tipo-pizza"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline card-secondary mb-2">
                        <div class="card-header py-1 px-2">
                            <h6 class="card-title small mb-0"><i class="fas fa-list-ol mr-1"></i>Quantidade por Tipo</h6>
                        </div>
                        <div class="card-body p-1" style="height:220px;">
                            <canvas id="chart-tipo-qtd"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Gráficos — linha 2 ────────────────────────────────────── --}}
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card card-outline card-secondary mb-2">
                        <div class="card-header py-1 px-2">
                            <h6 class="card-title small mb-0"><i class="fas fa-calendar-alt mr-1"></i>Evolução Diária (Valor)</h6>
                        </div>
                        <div class="card-body p-1" style="height:200px;">
                            <canvas id="chart-por-dia"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline card-secondary mb-2">
                        <div class="card-header py-1 px-2">
                            <h6 class="card-title small mb-0"><i class="fas fa-eye-slash mr-1"></i>Status Auditoria</h6>
                        </div>
                        <div class="card-body p-1" style="height:200px;">
                            <canvas id="chart-status-visto"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
