<div class="row">
    <div class="col-md-6">
        <div class="card">
            <x-loading-card />
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title card-title-chart">Pneus Gerente</h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <button href="#cliente-chart" class="btn btn-xs nav-link" data-toggle="tab">Clientes</button>
                        </li>
                        <li class="nav-item">
                            <button href="#supervisor-chart" class="btn btn-xs nav-link ml-1"
                                data-toggle="tab">Supervisor</button>
                        </li>
                        <li class="nav-item">
                            <button href="#gerente-chart" class="btn btn-xs nav-link active ml-1"
                                data-toggle="tab">Gerente</button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body" style="height: 300px">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="gerente-chart">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg pneusTotal"></span>
                                <span>Pneus Gerentes</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right calc-percentual">
                            </p>
                        </div>
                        {{-- Legenda fixa --}}
                        <div class="mb-2 text-center" id="legend-container-gerente"></div>

                        <div style="overflow-x: auto;">
                            <div class="position-relative mb-4" style="height: 150px;">
                                <canvas id="chartPneusGerente"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="cliente-chart">
                        <p class="d-flex flex-column">
                            <span class="text-bold text-lg pneusTotalCliente"></span>
                            <span>Pneus Clientes</span>
                        </p>
                        {{-- Legenda fixa --}}
                        <div class="mb-2 text-center" id="legend-container-cliente"></div>

                        <div style="overflow-x: auto;">
                            <div id="chart-container-cliente" style="height: 180px;">
                                <canvas id="chartPneusCliente"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="supervisor-chart">
                        <p class="d-flex flex-column">
                            <span class="text-bold text-lg pneusTotalSupervisor"></span>
                            <span>Pneus Supervisores</span>
                        </p>
                        {{-- Legenda fixa --}}
                        <div class="mb-2 text-center" id="legend-container-supervisor"></div>

                        <div style="overflow-x: auto;">
                            <div id="chart-container-supervisor" style="height: 180px;">
                                <canvas id="chartPneusSupervisor"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <x-loading-card />
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">Pneus Mês</h3>
            </div>
            <div class="card-body" style="height: 300px">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg pneusTotal"></span>
                        <span>Pneus Sem Faturar</span>
                    </p>
                    <p class="ml-auto d-flex flex-column text-right calc-percentual">
                    </p>
                </div>
                <div class="position-relative mb-4">
                    <canvas id="chartPneusMesAno" style="width: 100%; height: 150px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
