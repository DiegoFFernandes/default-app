@extends('layouts.master')

@section('title', 'Importar Pedágio — ConnectCar')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
@endsection

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-dark card-outline card-outline-tabs">

                    {{-- ── Cabeçalho com Tabs ───────────────────────────────────────── --}}
                    <div class="card-header p-0 d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs border-bottom-0" id="tabs-connectcar">
                            <li class="nav-item">
                                <a class="nav-link active" id="link-tab-importar" data-toggle="pill"
                                   href="#tab-importar" role="tab">
                                    <i class="fas fa-file-excel mr-1"></i> Importar Pedágio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="link-tab-mescla" data-toggle="pill"
                                   href="#tab-mescla" role="tab">
                                    <i class="fas fa-code-branch mr-1"></i> Aguardando Importação
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="link-tab-importados" data-toggle="pill"
                                   href="#tab-importados" role="tab">
                                    <i class="fas fa-check-circle mr-1"></i> Importados
                                </a>
                            </li>
                        </ul>
                        <div class="card-tools mr-2 d-flex align-items-center" style="gap:6px;">
                            <span class="badge badge-secondary d-none" id="badge-selecionados"></span>
                            <button type="button" class="btn btn-xs btn-primary d-none" id="btn-abrir-upload">
                                <i class="fas fa-upload mr-1"></i> Carregar arquivo
                            </button>
                            <a href="{{ route('despesa.index') }}" class="btn btn-xs btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Voltar
                            </a>
                        </div>
                    </div>

                    {{-- ── Conteúdo das Tabs ────────────────────────────────────────── --}}
                    <div class="tab-content" id="tabs-connectcar-content">

                        {{-- ── TAB 1: Importar Pedágio ──────────────────────────────── --}}
                        <div class="tab-pane fade show active" id="tab-importar" role="tabpanel">
                            <div class="card-body p-0">

                                {{-- Área vazia --}}
                                <div id="area-vazia" class="text-center text-muted py-5">
                                    <i class="fas fa-file-excel fa-3x mb-3 text-success"></i>
                                    <p class="mb-0">Nenhum arquivo carregado.</p>
                                    <p class="small">Clique em <strong>Carregar arquivo</strong> para selecionar o Excel do ConnectCar.</p>
                                    <button type="button" class="btn btn-sm btn-success mt-2" id="btn-abrir-upload-empty">
                                        <i class="fas fa-upload mr-1"></i> Carregar arquivo
                                    </button>
                                </div>

                                {{-- Card filtros --}}
                                <div id="card-filtros-cc" class="card collapsed-card mx-3 mt-3 mb-0 d-none">
                                    <div class="card-header py-2">
                                        <h6 class="card-title pt-2 small"><i class="fas fa-filter mr-1"></i> Filtros</h6>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-2" style="display:none;">
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-3 mb-2">
                                                <label class="small mb-1">Placa</label>
                                                <input type="text" id="filtro-cc-placa" class="form-control form-control-sm" placeholder="GJW6H72...">
                                            </div>
                                            <div class="col-12 col-md-3 mb-2">
                                                <label class="small mb-1">Data</label>
                                                <input type="text" id="filtro-cc-data" class="form-control form-control-sm" placeholder="dd/mm/aaaa...">
                                            </div>
                                            <div class="col-12 col-md-3 mb-2">
                                                <label class="small mb-1">Tipo de Transação</label>
                                                <input type="text" id="filtro-cc-tipo" class="form-control form-control-sm" placeholder="Passagem...">
                                            </div>
                                            <div class="col-12 col-md-2 mb-2">
                                                <label class="small mb-1">Valor</label>
                                                <input type="text" id="filtro-cc-valor" class="form-control form-control-sm" placeholder="0,00...">
                                            </div>
                                            <div class="col-12 col-md-1 mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btn-limpar-filtros-cc" title="Limpar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Toolbar seleção --}}
                                <div id="toolbar-selecao" class="px-3 pt-3 pb-2 d-flex align-items-center d-none" style="gap:8px;">
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-selecionar-todos">
                                        <i class="fas fa-check-double mr-1"></i> Selecionar todos
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-desmarcar-todos">
                                        <i class="fas fa-times mr-1"></i> Desmarcar todos
                                    </button>
                                    <small class="text-muted ml-2">Desmarque as linhas que <strong>não</strong> devem ser importadas.</small>
                                </div>

                                {{-- Tabela --}}
                                <div id="area-tabela" class="px-3 pb-3 d-none">
                                    <table class="table table-sm table-bordered table-hover mb-0 compact" id="tabela-revisar-connectcar">
                                        <thead class="thead-dark"><tr id="thead-connectcar"></tr></thead>
                                        <tbody></tbody>
                                        <tfoot class="thead-light"><tr id="tfoot-connectcar"></tr></tfoot>
                                    </table>
                                </div>
                            </div>

                            {{-- Footer Tab 1 --}}
                            <div class="card-footer d-flex justify-content-between align-items-center d-none" id="footer-tab1">
                                <small class="text-muted" id="txt-total-registros"></small>
                                <button type="button" class="btn btn-sm btn-primary" id="btn-previsualizar-mescla">
                                    <i class="fas fa-code-branch mr-1"></i> Pré-visualizar Mescla
                                </button>
                            </div>
                        </div>

                        {{-- ── TAB 2: Pré-visualização da Mescla ────────────────────── --}}
                        <div class="tab-pane fade" id="tab-mescla" role="tabpanel">
                            <div class="card-body p-0">

                                {{-- Resumo --}}
                                <div id="resumo-mescla" class="px-3 pt-3 pb-2 d-none">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="info-box info-box-custom">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text small">Registros</span>
                                                    <span class="info-box-number" id="resumo-total">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box info-box-custom">
                                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text small">Placas encontradas</span>
                                                    <span class="info-box-number" id="resumo-encontrados">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box info-box-custom">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text small">Veiculos não encontradas</span>
                                                    <span class="info-box-number" id="resumo-nao-encontrados">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Filtros mescla --}}
                                <div id="card-filtros-mescla" class="card mx-3 mt-0 mb-0 d-none">
                                    <div class="card-header py-2">
                                        <h6 class="card-title pt-2 small"><i class="fas fa-filter mr-1"></i> Filtros</h6>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-2 mb-2">
                                                <label class="small mb-1">Placa</label>
                                                <select id="filtro-mescla-placa" class="form-control form-control-sm">
                                                    <option value="">Todas</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4 mb-2">
                                                <label class="small mb-1">Motorista</label>
                                                <input type="text" id="filtro-mescla-motorista" class="form-control form-control-sm" placeholder="Nome...">
                                            </div>
                                            <div class="col-12 col-md-3 mb-2">
                                                <label class="small mb-1">Tipo</label>
                                                <input type="text" id="filtro-mescla-tipo" class="form-control form-control-sm" placeholder="Passagem...">
                                            </div>
                                            <div class="col-12 col-md-2 mb-2">
                                                <label class="small mb-1">Status</label>
                                                <select id="filtro-mescla-status" class="form-control form-control-sm">
                                                    <option value="">Todos</option>
                                                    <option value="Encontrado">Encontrado</option>
                                                    <option value="Não encontrado">Não encontrado</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-1 mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btn-limpar-filtros-mescla" title="Limpar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tabela mescla --}}
                                <div id="area-tabela-mescla" class="px-3 pb-3 d-none">
                                    <table class="table table-sm table-bordered table-hover mb-0 compact" id="tabela-mescla-connectcar">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width:40px;"><input type="checkbox" id="chk-all-mescla" title="Selecionar todos encontrados"></th>
                                                <th>Placa</th>
                                                <th>Marca / Modelo</th>
                                                <th>Cód.</th>
                                                <th>Motorista</th>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th class="text-right">Valor</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-center"></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right font-weight-bold"></th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {{-- Footer Tab 2 --}}
                            <div class="card-footer d-flex justify-content-between align-items-center d-none" id="footer-tab2">
                                <button type="button" class="btn btn-sm btn-secondary" id="btn-voltar-tab1">
                                    <i class="fas fa-arrow-left mr-1"></i> Voltar para Revisão
                                </button>
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <small class="text-muted" id="txt-selecionados-mescla"></small>
                                    <button type="button" class="btn btn-sm btn-success" id="btn-confirmar-importacao">
                                        <i class="fas fa-database mr-1"></i> Confirmar Importação
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ── TAB 3: Importados ────────────────────────────────────── --}}
                        <div class="tab-pane fade" id="tab-importados" role="tabpanel">
                            <div class="card-body p-0">

                                {{-- Resumo --}}
                                <div id="resumo-importados" class="px-3 pt-3 pb-2 d-none">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="info-box info-box-custom">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text small">Registros</span>
                                                    <span class="info-box-number" id="resumo-imp-total">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-box info-box-custom">
                                                <span class="info-box-icon bg-secondary"><i class="fas fa-dollar-sign"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text small">Total</span>
                                                    <span class="info-box-number" id="resumo-imp-valor">0,00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Filtros importados --}}
                                <div id="card-filtros-importados" class="card mx-3 mt-0 mb-0 d-none">
                                    <div class="card-header py-2">
                                        <h6 class="card-title pt-2 small"><i class="fas fa-filter mr-1"></i> Filtros</h6>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-2 mb-2">
                                                <label class="small mb-1">Placa</label>
                                                <select id="filtro-imp-placa" class="form-control form-control-sm">
                                                    <option value="">Todas</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4 mb-2">
                                                <label class="small mb-1">Motorista</label>
                                                <input type="text" id="filtro-imp-motorista" class="form-control form-control-sm" placeholder="Nome...">
                                            </div>
                                            <div class="col-12 col-md-5 mb-2">
                                                <label class="small mb-1">Tipo</label>
                                                <input type="text" id="filtro-imp-tipo" class="form-control form-control-sm" placeholder="Passagem...">
                                            </div>
                                            <div class="col-12 col-md-1 mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btn-limpar-filtros-imp" title="Limpar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tabela importados --}}
                                <div id="area-tabela-importados" class="px-3 pb-3 d-none">
                                    <table class="table table-sm table-bordered table-hover mb-0 compact" id="tabela-importados-connectcar">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Placa</th>
                                                <th>Marca / Modelo</th>
                                                <th>Motorista</th>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th class="text-right">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right font-weight-bold"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /tab-content --}}
                </div>
            </div>
        </div>
    </section>

    {{-- Modal importar Firebird --}}
    <div class="modal fade" id="modal-importar-firebird" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-database mr-2"></i> Confirmar Importação — Firebird</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">

                    {{-- Resumo dos selecionados --}}
                    <div class="alert alert-info py-2 mb-3" id="resumo-importar-fb">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="txt-resumo-importar-fb">Carregando...</span>
                    </div>

                    <div class="row">
                        {{-- Empresa --}}
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small mb-1">
                                Empresa <span class="text-danger">*</span>
                            </label>
                            <select id="select-empresa-fb" class="form-control form-control-sm">
                                <option value="">Selecione a empresa...</option>
                            </select>
                            <small class="text-muted">Carregado do Firebird</small>
                        </div>

                        {{-- Motorista --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="font-weight-bold small mb-1">
                                Motorista <span class="text-danger">*</span>
                            </label>
                            <select id="select-motorista-fb" class="form-control form-control-sm" style="width:100%;">
                                <option value="">Digite o nome para buscar...</option>
                            </select>
                            <small class="text-muted">Pesquise pelo nome no Firebird</small>
                        </div>

                        {{-- Tipo de Conta --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="font-weight-bold small mb-1">
                                Tipo de Conta <span class="text-danger">*</span>
                            </label>
                            <select id="select-tipoconta-fb" class="form-control form-control-sm">
                                <option value="">Selecione o tipo de conta...</option>
                            </select>
                            <small class="text-muted">Carregado do Firebird</small>
                        </div>

                        {{-- Histórico (carregado conforme Tipo de Conta) --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="font-weight-bold small mb-1">
                                Histórico <span class="text-danger">*</span>
                            </label>
                            <select id="select-historico-fb" class="form-control form-control-sm" disabled>
                                <option value="">Selecione primeiro o tipo de conta...</option>
                            </select>
                            <small class="text-muted" id="txt-loading-historico"></small>
                        </div>

                        {{-- Forma de Pagamento --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="font-weight-bold small mb-1">
                                Forma de Pagamento <span class="text-danger">*</span>
                            </label>
                            <select id="select-forma-pagamento-fb" class="form-control form-control-sm">
                                <option value="">Selecione a forma de pagamento...</option>
                            </select>
                            <small class="text-muted">Carregado do Firebird</small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success" id="btn-executar-importacao-fb">
                        <i class="fas fa-check mr-1"></i> Executar Importação
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal upload --}}
    <div class="modal fade" id="modal-upload-connectcar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="fas fa-upload mr-2"></i> Carregar arquivo ConnectCar</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Selecione o arquivo Excel (.xlsx / .xls) exportado pelo ConnectCar.
                        As primeiras 18 linhas e as colunas A, D, E, F, H, I, K, L serão ignoradas automaticamente.
                    </p>
                    <div class="form-group mb-0">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="arquivo-connectcar" accept=".xlsx,.xls" lang="pt">
                            <label class="custom-file-label" for="arquivo-connectcar">Nenhum arquivo selecionado</label>
                        </div>
                    </div>
                    <div id="erro-upload" class="text-danger small mt-2 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-processar-arquivo" disabled>
                        <i class="fas fa-cogs mr-1"></i> Processar
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
(function () {
    const ROUTES = {
        token:               "{{ csrf_token() }}",
        importarMysql:       "{{ route('despesa.connectcar.importar') }}",
        comprovantesParaMescla:   "{{ route('despesa.connectcar.comprovantes-mescla') }}",
        comprovantesImportados:   "{{ route('despesa.connectcar.comprovantes-importados') }}",
        verificarHash:       "{{ route('despesa.connectcar.verificar-hash') }}",
        veiculosBatch:       "{{ route('despesa.connectcar.veiculos-batch') }}",
        importarFirebird: "{{ route('despesa.connectcar.importar-firebird') }}",
        empresas:         "{{ route('firebird.empresas') }}",
        tiposConta:       "{{ route('firebird.tipos-conta') }}",
        historicos:       "{{ route('firebird.historicos') }}",
        formasPagamento:  "{{ route('get-form-pagamento') }}",
        searchPessoas:    "{{ route('pessoa.search') }}",
        dtLanguage:       "{{ asset('vendor/datatables/pt-BR.json') }}",
    };

    // ── Constantes de filtragem ────────────────────────────────────────────────
    const COLUNAS_IGNORADAS = new Set([0, 3, 4, 5, 7, 8, 10, 11]);
    const TIPOS_IGNORADOS   = new Set(["EstornoPassagem", "Recarga", "MensalidadePrepagoEmpresarial"]);

    // Índices nos dados filtrados
    const IDX_PLACA = 0, IDX_DATA = 1, IDX_TIPO = 2, IDX_VALOR = 3;

    function filtrarColunas(row) {
        return row.filter((_, i) => !COLUNAS_IGNORADAS.has(i));
    }

    // ── Estado ─────────────────────────────────────────────────────────────────
    let dadosCarregados   = [];
    let headersCarregados = [];
    let hashArquivo       = null;
    let nomeArquivo       = null;
    let dt                = null;
    let dtMescla          = null;
    let dadosMescla       = [];
    let dtImportados      = null;
    let dadosImportados   = [];

    // ── SHA-256 via Web Crypto API ─────────────────────────────────────────────
    async function calcularHashArquivo(file) {
        const buffer      = await file.arrayBuffer();
        const hashBuffer  = await crypto.subtle.digest("SHA-256", buffer);
        const hashArray   = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, "0")).join("");
    }

    // ── Modal upload ───────────────────────────────────────────────────────────
    function abrirModalUpload() {
        $("#arquivo-connectcar").val("");
        $(".custom-file-label[for='arquivo-connectcar']").text("Nenhum arquivo selecionado");
        $("#btn-processar-arquivo").prop("disabled", true);
        $("#erro-upload").addClass("d-none").text("");
        $("#modal-upload-connectcar").modal("show");
    }
    $("#btn-abrir-upload, #btn-abrir-upload-empty").on("click", abrirModalUpload);

    // Tab: mostrar botão "Carregar arquivo" só na Tab 1
    // e inicializar DataTable da mescla somente quando o painel estiver visível
    $("#link-tab-importar").on("shown.bs.tab", function () {
        $("#btn-abrir-upload").removeClass("d-none");
    });
    $("#link-tab-mescla").on("shown.bs.tab", function () {
        $("#btn-abrir-upload").addClass("d-none");
        carregarMescla();
    });
    $("#link-tab-importados").on("shown.bs.tab", function () {
        $("#btn-abrir-upload").addClass("d-none");
        carregarImportados();
    });

    // ── Checkboxes Tab 2 (Mescla) ─────────────────────────────────────────────
    // Retorna apenas linhas visíveis (respeitando filtro ativo)
    function todosMesclaVisiveis() {
        return dtMescla ? $(dtMescla.rows({ search: "applied" }).nodes()) : $();
    }
    // Retorna todas as linhas (para coletar selecionadas na confirmação)
    function todosMescla() { return dtMescla ? $(dtMescla.rows().nodes()) : $(); }

    function atualizarContadorMescla() {
        const vis   = todosMesclaVisiveis();
        const sel   = vis.find(".chk-mescla:checked").length;
        const total = vis.find(".chk-mescla:not(:disabled)").length;
        $("#txt-selecionados-mescla").html(
            "<strong>" + sel + "</strong> de <strong>" + total + "</strong> selecionado(s)"
        );
    }

    // Select all — age apenas nas linhas visíveis pelo filtro
    $(document).on("change", "#chk-all-mescla", function () {
        todosMesclaVisiveis().find(".chk-mescla:not(:disabled)").prop("checked", this.checked);
        atualizarContadorMescla();
    });

    $("#tabela-mescla-connectcar").on("change", ".chk-mescla", function () {
        const vis   = todosMesclaVisiveis();
        const total = vis.find(".chk-mescla:not(:disabled)").length;
        const check = vis.find(".chk-mescla:not(:disabled):checked").length;
        $("#chk-all-mescla").prop("checked", total > 0 && check === total);
        atualizarContadorMescla();
    });

    // ── Filtros Tab 2 (Mescla) ───────────────────────────────────────────────
    function filtrarMescla() {
        if (!dtMescla) return;
        const placa = $("#filtro-mescla-placa").val();
        dtMescla
            // Placa: match exato via regex (evita "GJW6" bater em "GJW6H72" e "GJW6802")
            .column(1).search(placa ? "^" + $.fn.dataTable.util.escapeRegex(placa) + "$" : "", true, false)
            .column(4).search($("#filtro-mescla-motorista").val())
            .column(6).search($("#filtro-mescla-tipo").val())
            .column(8).search($("#filtro-mescla-status").val())
            .draw();
        const vis   = todosMesclaVisiveis();
        const total = vis.find(".chk-mescla:not(:disabled)").length;
        const check = vis.find(".chk-mescla:not(:disabled):checked").length;
        $("#chk-all-mescla").prop("checked", total > 0 && check === total);
        atualizarContadorMescla();
    }

    // Placa agora é select → change; motorista e tipo mantêm keyup
    $("#filtro-mescla-placa").on("change", filtrarMescla);
    $("#filtro-mescla-motorista, #filtro-mescla-tipo").on("keyup", filtrarMescla);
    $("#filtro-mescla-status").on("change", filtrarMescla);
    $("#btn-limpar-filtros-mescla").on("click", function () {
        $("#filtro-mescla-placa").val("");
        $("#filtro-mescla-motorista, #filtro-mescla-tipo").val("");
        $("#filtro-mescla-status").val("");
        filtrarMescla();
    });

    // ── Seleção de arquivo ─────────────────────────────────────────────────────
    $("#arquivo-connectcar").on("change", async function () {
        const file = this.files[0];
        $("#erro-upload").addClass("d-none").text("");
        $("#btn-processar-arquivo").prop("disabled", true);
        hashArquivo = null;
        nomeArquivo = null;

        if (!file) return;

        const ext = file.name.split(".").pop().toLowerCase();
        if (!["xlsx", "xls"].includes(ext)) {
            $("#erro-upload").removeClass("d-none").text("Formato inválido. Use .xlsx ou .xls.");
            return;
        }

        $(".custom-file-label[for='arquivo-connectcar']").text(file.name);
        nomeArquivo = file.name;

        try {
            hashArquivo = await calcularHashArquivo(file);
        } catch (e) {
            // Hash opcional — não bloqueia o fluxo
        }

        $("#btn-processar-arquivo").prop("disabled", false);
    });

    // ── Processar arquivo ──────────────────────────────────────────────────────
    $("#btn-processar-arquivo").on("click", async function () {
        const file = $("#arquivo-connectcar")[0].files[0];
        if (!file) return;

        const $btn = $(this).prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processando...');

        // Verifica hash antes de processar
        if (hashArquivo) {
            try {
                const verificacao = await $.ajax({
                    url:  ROUTES.verificarHash,
                    type: "POST",
                    data: { _token: ROUTES.token, hash: hashArquivo },
                });

                if (verificacao.existe) {
                    $btn.prop("disabled", false).html('<i class="fas fa-cogs mr-1"></i> Processar');
                    Swal.fire({
                        icon:  "warning",
                        title: "Arquivo já importado",
                        html:  "<table class='table table-sm text-left mt-2'>"
                             + "<tr><td class='text-muted'>Arquivo</td><td><strong>" + verificacao.nm_arquivo + "</strong></td></tr>"
                             + "<tr><td class='text-muted'>Importado por</td><td><strong>" + verificacao.importado_por + "</strong></td></tr>"
                             + "<tr><td class='text-muted'>Data</td><td><strong>" + verificacao.importado_em + "</strong></td></tr>"
                             + "<tr><td class='text-muted'>Registros</td><td><strong>" + verificacao.total_registros + "</strong></td></tr>"
                             + "<tr><td class='text-muted'>Período</td><td><strong>" + (verificacao.dt_referencia_inicio || "—") + " → " + (verificacao.dt_referencia_fim || "—") + "</strong></td></tr>"
                             + "</table>",
                        confirmButtonText: "Entendido",
                    });
                    return;
                }
            } catch (e) {
                // Se a verificação falhar, permite continuar (não bloqueia)
            }
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const workbook = XLSX.read(e.target.result, { type: "array" });
                const sheet    = workbook.Sheets[workbook.SheetNames[0]];
                const rows     = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: "" });

                if (!rows || rows.length < 20) {
                    $("#erro-upload").removeClass("d-none").text("O arquivo não contém dados após a linha 19.");
                    $btn.prop("disabled", false).html('<i class="fas fa-cogs mr-1"></i> Processar');
                    return;
                }

                const headers = filtrarColunas(rows[18]);
                const data    = rows.slice(19)
                    .filter(r => r.some(c => String(c).trim() !== ""))
                    .filter(r => !TIPOS_IGNORADOS.has(String(r[6]).trim()))
                    .map(filtrarColunas)
                    .filter(r => r.some(c => String(c).trim() !== ""))
                    .map(r => {
                        const row = [...r];
                        if (typeof row[IDX_PLACA] === "string") {
                            row[IDX_PLACA] = row[IDX_PLACA].replace(/-/g, "");
                        }
                        return row;
                    });

                headersCarregados = headers;
                dadosCarregados   = data;

                $("#link-tab-importar").tab("show");
                $("#modal-upload-connectcar").modal("hide");

                renderTabela(headers, data);

            } catch (err) {
                $("#erro-upload").removeClass("d-none").text("Não foi possível processar o arquivo Excel.");
                $btn.prop("disabled", false).html('<i class="fas fa-cogs mr-1"></i> Processar');
            }
        };
        reader.readAsArrayBuffer(file);
    });

    // ── Renderizar DataTable Tab 1 ─────────────────────────────────────────────
    function renderTabela(headers, data) {
        if (dt) { dt.destroy(); dt = null; }
        if (dtMescla) { dtMescla.destroy(); dtMescla = null; }
        dadosMescla = [];
        $("#area-tabela-mescla, #resumo-mescla, #footer-tab2, #card-filtros-mescla").addClass("d-none");

        const lastDataIdx = headers.length - 1;
        const lastDtIdx   = headers.length;

        $("#thead-connectcar").html(
            '<th style="width:40px;"><input type="checkbox" id="chk-all" checked></th>'
            + headers.map(h => `<th class="text-nowrap">${h}</th>`).join("")
        );
        $("#tfoot-connectcar").html(
            "<th></th>"
            + headers.map((_, i) =>
                i === lastDataIdx
                    ? '<th class="text-right font-weight-bold" id="tfoot-sum"></th>'
                    : "<th></th>"
            ).join("")
        );
        $("#tabela-revisar-connectcar tbody").empty();

        $("#area-vazia").addClass("d-none");
        $("#btn-abrir-upload").removeClass("d-none");
        $("#card-filtros-cc, #toolbar-selecao, #area-tabela, #footer-tab1").removeClass("d-none");

        dt = $("#tabela-revisar-connectcar").DataTable({
            data, columns: [
                { data: null, orderable: false, searchable: false, className: "text-center", width: "40px",
                  render: () => '<input type="checkbox" class="chk-linha" checked>' },
                ...headers.map((_, i) => ({
                    data: i, defaultContent: "",
                    className: i === lastDataIdx ? "text-right" : "",
                    render: i === lastDataIdx
                        ? function (val, type) {
                            const n = Math.abs(parseFloat(String(val).replace(",", ".")) || 0);
                            return type === "display"
                                ? n.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) : n;
                          }
                        : undefined,
                })),
            ],
            rowCallback: (row, _, index) => $(row).addClass("linha-revisar").attr("data-index", index),
            footerCallback: function () {
                const api   = this.api();
                const total = api.column(lastDtIdx).data().reduce(
                    (acc, val) => acc + Math.abs(parseFloat(String(val).replace(",", ".")) || 0), 0
                );
                $(api.column(lastDtIdx).footer()).html(
                    "Total: <strong>" + total.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) + "</strong>"
                );
            },
            paging: false, ordering: false, searching: true, info: false,
            scrollY: "400px", scrollX: true, scrollCollapse: true, dom: "t",
            language: { url: ROUTES.dtLanguage },
        });

        atualizarContador();
    }

    // ── Checkboxes Tab 1 ──────────────────────────────────────────────────────
    function todosOsNos() { return dt ? $(dt.rows().nodes()) : $(); }

    function atualizarContador() {
        const total      = dadosCarregados.length;
        const sel        = todosOsNos().find(".chk-linha:checked").length;
        $("#badge-selecionados").removeClass("d-none").text(sel + " / " + total + " selecionado(s)");
        $("#txt-total-registros").html("Total lido: <strong>" + total + "</strong> registros");
    }

    $(document).on("change", "#chk-all", function () {
        const c = this.checked;
        todosOsNos().find(".chk-linha").prop("checked", c);
        todosOsNos().toggleClass("table-secondary", !c);
        atualizarContador();
    });
    $("#tabela-revisar-connectcar").on("change", ".chk-linha", function () {
        $(this).closest("tr").toggleClass("table-secondary", !this.checked);
        $("#chk-all").prop("checked", todosOsNos().find(".chk-linha:not(:checked)").length === 0);
        atualizarContador();
    });
    $("#btn-selecionar-todos").on("click", function () {
        todosOsNos().find(".chk-linha").prop("checked", true).end().removeClass("table-secondary");
        $("#chk-all").prop("checked", true); atualizarContador();
    });
    $("#btn-desmarcar-todos").on("click", function () {
        todosOsNos().find(".chk-linha").prop("checked", false).end().addClass("table-secondary");
        $("#chk-all").prop("checked", false); atualizarContador();
    });

    // ── Filtros Tab 1 ─────────────────────────────────────────────────────────
    $("#filtro-cc-placa").on("keyup", function () { if (dt) dt.column(1).search(this.value).draw(); });
    $("#filtro-cc-data").on("keyup",  function () { if (dt) dt.column(2).search(this.value).draw(); });
    $("#filtro-cc-tipo").on("keyup",  function () { if (dt) dt.column(3).search(this.value).draw(); });
    $("#filtro-cc-valor").on("keyup", function () { if (dt) dt.column(4).search(this.value).draw(); });
    $("#btn-limpar-filtros-cc").on("click", function () {
        $("#filtro-cc-placa, #filtro-cc-data, #filtro-cc-tipo, #filtro-cc-valor").val("");
        if (dt) dt.columns([1, 2, 3, 4]).search("").draw();
    });

    // ── Pré-visualizar Mescla ─────────────────────────────────────────────────
    $("#btn-previsualizar-mescla").on("click", async function () {
        const linhasSelecionadas = [];
        todosOsNos().each(function () {
            if ($(this).find(".chk-linha").is(":checked")) {
                linhasSelecionadas.push(dadosCarregados[parseInt($(this).data("index"))]);
            }
        });

        if (!linhasSelecionadas.length) {
            Swal.fire("Atenção", "Selecione ao menos um registro para pré-visualizar.", "warning");
            return;
        }

        const $btn = $(this).prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Salvando...');

        try {
            // Salva as linhas selecionadas no MySQL com st_arquivo = 'S'
            await $.ajax({
                url:         ROUTES.importarMysql,
                type:        "POST",
                contentType: "application/json",
                data:        JSON.stringify({
                    _token:       ROUTES.token,
                    rows:         linhasSelecionadas,
                    hash_arquivo: hashArquivo,
                    nm_arquivo:   nomeArquivo,
                }),
            });

            // shown.bs.tab cuida do carregarMescla() ao mudar de tab
            $("#link-tab-mescla").tab("show");

        } catch (err) {
            Swal.fire("Erro", "Não foi possível salvar os registros.", "error");
        }

        $btn.prop("disabled", false).html('<i class="fas fa-code-branch mr-1"></i> Pré-visualizar Mescla');
    });

    // ── Preparar dados Tab 2 (Mescla) — DataTable inicializado em shown.bs.tab ──
    function renderMescla(rows, mapa) {
        if (dtMescla) { dtMescla.destroy(); dtMescla = null; }

        dadosMescla = [];
        let totalValor = 0;
        const placasEncontradas    = new Set();
        const placasNaoEncontradas = new Set();

        rows.forEach(function (row) {
            // row vem do MySQL: { id, nr_placa, dt_despesa (YYYY-MM-DD), ds_observacao, vl_consumido, st_importado_fb }
            const placa = String(row.nr_placa || "").trim();
            const valor = Math.abs(parseFloat(row.vl_consumido) || 0);
            totalValor += valor;

            // Converte YYYY-MM-DD → dd/mm/yyyy para exibição
            const [yyyy, mm, dd] = String(row.dt_despesa || "").split("-");
            const dataFormatada  = dd && mm && yyyy ? dd + "/" + mm + "/" + yyyy : row.dt_despesa || "";

            const veiculo = mapa[placa];
            const achado  = !!veiculo;

            if (placa) {
                achado ? placasEncontradas.add(placa) : placasNaoEncontradas.add(placa);
            }

            dadosMescla.push({
                comprovante_id: row.id,
                placa,
                marcaModelo:   achado ? (veiculo.marca + " " + veiculo.modelo).trim() || "—" : "—",
                cdMotorista:   achado ? (veiculo.cd_motorista ?? "—") : "—",
                nmMotorista:   achado ? (veiculo.nm_motorista || "—") : "—",
                dataFormatada,
                tipo:          row.ds_observacao || "—",
                valor,
                achado,
            });
        });

        $("#resumo-total").text(rows.length);
        $("#resumo-encontrados").text(placasEncontradas.size);
        $("#resumo-nao-encontrados").text(placasNaoEncontradas.size);

        // Popula o select de placa com as placas únicas encontradas na tabela
        const placasUnicas = [...new Set(dadosMescla.map(d => d.placa).filter(Boolean))].sort();
        const $selPlaca = $("#filtro-mescla-placa").empty().append('<option value="">Todas</option>');
        placasUnicas.forEach(p => $selPlaca.append(new Option(p, p)));

        $("#filtro-mescla-placa").val("");
        $("#filtro-mescla-motorista, #filtro-mescla-tipo").val("");
        $("#filtro-mescla-status").val("");
        $("#area-tabela-mescla, #resumo-mescla, #footer-tab2, #card-filtros-mescla").removeClass("d-none");

        if (dtMescla) { dtMescla.destroy(); dtMescla = null; }

        dtMescla = $("#tabela-mescla-connectcar").DataTable({
            data: dadosMescla,
            columns: [
                { data: null, orderable: false, searchable: false, className: "text-center", width: "40px",
                  render: (_, __, row) => {
                    if (!row.achado) return '<input type="checkbox" class="chk-mescla" disabled title="Placa não encontrada no Firebird">';
                    return '<input type="checkbox" class="chk-mescla">';
                  }},
                { data: "placa",         title: "Placa" },
                { data: "marcaModelo",   title: "Marca / Modelo" },
                { data: "cdMotorista",   title: "Cód.", className: "text-center" },
                { data: "nmMotorista",   title: "Motorista" },
                { data: "dataFormatada", title: "Data" },
                { data: "tipo",          title: "Tipo" },
                { data: "valor",         title: "Valor", className: "text-right",
                  render: (v, type) => type === "display"
                    ? v.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) : v },
                { data: null, title: "Status", className: "text-center",
                  render: (_, __, row) => row.achado
                    ? '<span class="badge badge-success">Encontrado</span>'
                    : '<span class="badge badge-warning">Não encontrado</span>' },
            ],
            rowCallback: function (row, rowData) {
                if (!rowData.achado) $(row).addClass("table-warning");
            },
            footerCallback: function () {
                const api   = this.api();
                const total = api.column(7, { search: "applied" }).data().reduce((s, v) => s + v, 0);
                $(api.column(7).footer()).html(
                    "Total: <strong>" + total.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) + "</strong>"
                );
            },
            initComplete: function () { atualizarContadorMescla(); },
            paging: false, ordering: true, searching: true, info: false,
            scrollY: "380px", scrollX: true, scrollCollapse: true, dom: "t",
            language: { url: ROUTES.dtLanguage },
        });
    }

    // ── Carrega Tab 2 do MySQL + Firebird (chamada ao mostrar a tab) ──────────
    async function carregarMescla() {
        if (dtMescla) { dtMescla.destroy(); dtMescla = null; }
        dadosMescla = [];

        $("#area-tabela-mescla, #resumo-mescla, #footer-tab2, #card-filtros-mescla").addClass("d-none");
        $("#tabela-mescla-connectcar tbody").html(
            '<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Carregando registros...</td></tr>'
        );
        $("#area-tabela-mescla").removeClass("d-none");

        try {
            const comprovantes = await $.getJSON(ROUTES.comprovantesParaMescla);

            if (!comprovantes.length) {
                $("#tabela-mescla-connectcar tbody").html(
                    '<tr><td colspan="9" class="text-center py-4 text-muted">Nenhum registro encontrado.</td></tr>'
                );
                return;
            }

            const placas = [...new Set(comprovantes.map(c => String(c.nr_placa || "").trim()).filter(Boolean))];
            const mapa   = await $.ajax({ url: ROUTES.veiculosBatch, type: "POST", data: { _token: ROUTES.token, placas } });

            renderMescla(comprovantes, mapa);
        } catch (err) {
            $("#tabela-mescla-connectcar tbody").html(
                '<tr><td colspan="9" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Erro ao carregar registros.</td></tr>'
            );
        }
    }

    // ── Tab 3: Importados ─────────────────────────────────────────────────────
    function renderImportados(rows, mapa) {
        if (dtImportados) { dtImportados.destroy(); dtImportados = null; }
        dadosImportados = [];
        let totalValor = 0;

        rows.forEach(function (row) {
            const placa  = String(row.nr_placa || "").trim();
            const valor  = Math.abs(parseFloat(row.vl_consumido) || 0);
            totalValor  += valor;

            const [yyyy, mm, dd] = String(row.dt_despesa || "").split("-");
            const dataFormatada  = dd && mm && yyyy ? dd + "/" + mm + "/" + yyyy : row.dt_despesa || "";

            const veiculo    = mapa[placa];
            const marcaModelo = veiculo ? (veiculo.marca + " " + veiculo.modelo).trim() || "—" : "—";

            dadosImportados.push({
                placa,
                marcaModelo,
                nmMotorista:   row.nm_solicitante || "—",
                dataFormatada,
                tipo:          row.ds_observacao || "—",
                valor,
            });
        });

        $("#resumo-imp-total").text(rows.length);
        $("#resumo-imp-valor").text(totalValor.toLocaleString("pt-BR", { minimumFractionDigits: 2 }));

        const placasUnicas = [...new Set(dadosImportados.map(d => d.placa).filter(Boolean))].sort();
        const $selPlaca = $("#filtro-imp-placa").html('<option value="">Todas</option>');
        placasUnicas.forEach(p => $selPlaca.append($("<option>", { value: p, text: p })));

        $("#filtro-imp-motorista, #filtro-imp-tipo").val("");
        $("#resumo-importados, #card-filtros-importados, #area-tabela-importados").removeClass("d-none");

        dtImportados = $("#tabela-importados-connectcar").DataTable({
            data: dadosImportados,
            columns: [
                { data: "placa",         title: "Placa" },
                { data: "marcaModelo",   title: "Marca / Modelo" },
                { data: "nmMotorista",   title: "Motorista" },
                { data: "dataFormatada", title: "Data" },
                { data: "tipo",          title: "Tipo" },
                { data: "valor",         title: "Valor", className: "text-right",
                  render: (v, type) => type === "display"
                    ? v.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) : v },
            ],
            rowCallback: function (row) { $(row).addClass("table-secondary"); },
            footerCallback: function () {
                const api   = this.api();
                const total = api.column(5, { search: "applied" }).data().reduce((s, v) => s + v, 0);
                $(api.column(5).footer()).html(
                    "Total: <strong>" + total.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) + "</strong>"
                );
            },
            paging: false, ordering: false, searching: true, info: false,
            scrollY: "380px", scrollX: true, scrollCollapse: true, dom: "t",
            language: { url: ROUTES.dtLanguage },
        });

        // Filtros
        $("#filtro-imp-placa").off("change").on("change", function () {
            dtImportados.column(0).search($(this).val()).draw();
        });
        $("#filtro-imp-motorista").off("input").on("input", function () {
            dtImportados.column(2).search($(this).val()).draw();
        });
        $("#filtro-imp-tipo").off("input").on("input", function () {
            dtImportados.column(4).search($(this).val()).draw();
        });
        $("#btn-limpar-filtros-imp").off("click").on("click", function () {
            $("#filtro-imp-placa").val("").trigger("change");
            $("#filtro-imp-motorista, #filtro-imp-tipo").val("").trigger("input");
        });
    }

    async function carregarImportados() {
        if (dtImportados) { dtImportados.destroy(); dtImportados = null; }
        dadosImportados = [];

        $("#resumo-importados, #card-filtros-importados").addClass("d-none");
        $("#area-tabela-importados").removeClass("d-none");
        $("#tabela-importados-connectcar tbody").html(
            '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Carregando registros...</td></tr>'
        );

        try {
            const registros = await $.getJSON(ROUTES.comprovantesImportados);

            if (!registros.length) {
                $("#tabela-importados-connectcar tbody").html(
                    '<tr><td colspan="6" class="text-center py-4 text-muted">Nenhum registro importado ainda.</td></tr>'
                );
                return;
            }

            const placas = [...new Set(registros.map(r => String(r.nr_placa || "").trim()).filter(Boolean))];
            const mapa   = await $.ajax({ url: ROUTES.veiculosBatch, type: "POST", data: { _token: ROUTES.token, placas } });

            renderImportados(registros, mapa);
        } catch (err) {
            $("#tabela-importados-connectcar tbody").html(
                '<tr><td colspan="6" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Erro ao carregar registros.</td></tr>'
            );
        }
    }

    // ── Limpa Tab 1 completamente ao voltar (evita duplicatas no MySQL) ────────
    function resetTab1() {
        if (dt) { dt.destroy(); dt = null; }
        dadosCarregados   = [];
        headersCarregados = [];
        hashArquivo       = null;
        nomeArquivo       = null;

        $("#arquivo-connectcar").val("");
        $(".custom-file-label[for='arquivo-connectcar']").text("Nenhum arquivo selecionado");
        $("#area-vazia").removeClass("d-none");
        $("#btn-abrir-upload").addClass("d-none");
        $("#card-filtros-cc, #toolbar-selecao, #area-tabela, #footer-tab1").addClass("d-none");
        $("#filtro-cc-placa, #filtro-cc-data, #filtro-cc-tipo, #filtro-cc-valor").val("");
    }

    // ── Voltar para Tab 1 (botão + clique direto na tab) ─────────────────────
    $("#btn-voltar-tab1").on("click", function () {
        resetTab1();
        $("#link-tab-importar").tab("show");
    });

    $("#link-tab-importar").on("click", function () {
        if (dadosMescla.length) resetTab1();
    });

    // ── Select2 — inicialização dos três selects do modal ────────────────────
    const $modal = $("#modal-importar-firebird");

    $("#select-motorista-fb").select2({
        dropdownParent:     $modal,
        placeholder:        "Digite o nome para buscar...",
        allowClear:         true,
        width:              "100%",
        minimumInputLength: 2,
        language: { inputTooShort: () => "Digite ao menos 2 caracteres..." },
        ajax: {
            url:      ROUTES.searchPessoas,
            dataType: "json",
            delay:    300,
            data:     (params) => ({ q: params.term }),
            processResults: (data) => ({ results: data }),
        },
    });

    $("#select-tipoconta-fb").select2({
        dropdownParent: $modal,
        placeholder:    "Selecione o tipo de conta...",
        allowClear:     true,
        width:          "100%",
    });

    $("#select-historico-fb").select2({
        dropdownParent: $modal,
        placeholder:    "Selecione um histórico...",
        allowClear:     true,
        width:          "100%",
    });

    $("#select-empresa-fb").select2({
        dropdownParent: $modal,
        placeholder:    "Selecione a empresa...",
        allowClear:     true,
        width:          "100%",
    });

    $("#select-forma-pagamento-fb").select2({
        dropdownParent: $modal,
        placeholder:    "Selecione a forma de pagamento...",
        allowClear:     true,
        width:          "100%",
    });

    // ── Abrir modal de importação Firebird ────────────────────────────────────
    $("#btn-confirmar-importacao").on("click", function () {
        // Coleta linhas selecionadas (achado=true e checkbox marcado)
        const selecionadas = [];
        todosMescla().each(function () {
            if ($(this).find(".chk-mescla:checked").length) {
                const idx = dtMescla.row(this).index();
                selecionadas.push(dadosMescla[idx]);
            }
        });

        if (!selecionadas.length) {
            Swal.fire("Atenção", "Selecione ao menos um registro com placa encontrada no Firebird.", "warning");
            return;
        }

        // Validar que todas as linhas selecionadas têm a mesma placa
        const placasUnicas = [...new Set(selecionadas.map(r => r.placa))];
        if (placasUnicas.length > 1) {
            Swal.fire({
                icon:  "warning",
                title: "Placas diferentes selecionadas",
                html:  "A importação aceita apenas <strong>uma placa por vez</strong>.<br>" +
                       "Placas selecionadas: <strong>" + placasUnicas.join(", ") + "</strong>.<br>" +
                       "Use o filtro de placa ou desmarque as demais.",
            });
            return;
        }

        // Pré-popular motorista com os dados vindos do Firebird
        const primeiraLinha = selecionadas[0];
        const cdMotorista   = String(primeiraLinha.cdMotorista ?? "").trim();
        const nmMotorista   = String(primeiraLinha.nmMotorista ?? "—").trim();

        if (cdMotorista && cdMotorista !== "—" && cdMotorista !== "0") {
            const opt = new Option(nmMotorista, cdMotorista, true, true);
            $("#select-motorista-fb").empty().append(opt).trigger("change");
        } else {
            $("#select-motorista-fb").val(null).trigger("change");
        }

        // Resumo
        const totalVal = selecionadas.reduce((s, r) => s + r.valor, 0)
            .toLocaleString("pt-BR", { minimumFractionDigits: 2 });

        $("#txt-resumo-importar-fb").html(
            "Placa: <strong>" + placasUnicas[0] + "</strong> &nbsp;|&nbsp; " +
            "<strong>" + selecionadas.length + "</strong> registro(s) &nbsp;|&nbsp; " +
            "Motorista: <strong>" + nmMotorista + "</strong> &nbsp;|&nbsp; " +
            "Total: <strong>R$&nbsp;" + totalVal + "</strong>"
        );

        // Reset tipo conta + histórico e armazena payload
        $("#select-tipoconta-fb").val(null).trigger("change");
        $("#modal-importar-firebird").data("rows", selecionadas);

        $("#modal-importar-firebird").modal("show");
    });

    // ── Tipos de Conta — carrega ao abrir o modal (uma vez) ──────────────────
    let empresasCarregadas      = false;
    let tiposContaCarregados    = false;
    let formasPagtoCarregadas   = false;

    $modal.on("show.bs.modal", function () {
        // Empresas
        if (!empresasCarregadas) {
            $.getJSON(ROUTES.empresas, function (data) {
                const $sel = $("#select-empresa-fb").empty().append('<option value=""></option>');
                data.forEach(function (item) {
                    $sel.append(new Option(item.text, item.id));
                });
                $sel.trigger("change");
                empresasCarregadas = true;
            });
        }

        // Tipos de Conta
        if (!tiposContaCarregados) {
            $.getJSON(ROUTES.tiposConta, function (data) {
                const $sel = $("#select-tipoconta-fb").empty().append('<option value=""></option>');
                data.forEach(function (item) {
                    $sel.append(new Option(item.text, item.id));
                });
                $sel.trigger("change");
                tiposContaCarregados = true;
            });
        }

        // Formas de Pagamento
        if (!formasPagtoCarregadas) {
            $.getJSON(ROUTES.formasPagamento, function (data) {
                const $sel = $("#select-forma-pagamento-fb").empty().append('<option value=""></option>');
                data.forEach(function (item) {
                    const id   = item.CD_FORMAPAGTO ?? item.cd_formapagto;
                    const text = item.DS_FORMAPAGTO ?? item.ds_formapagto;
                    $sel.append(new Option(text, id));
                });
                $sel.trigger("change");
                formasPagtoCarregadas = true;
            });
        }
    });

    // Reset ao fechar o modal
    $modal.on("hidden.bs.modal", function () {
        $("#select-empresa-fb").val(null).trigger("change");
        $("#select-tipoconta-fb").val(null).trigger("change");
        $("#select-historico-fb")
            .empty().append('<option value=""></option>')
            .prop("disabled", true).trigger("change");
        $("#select-forma-pagamento-fb").val(null).trigger("change");
        $("#txt-loading-historico").text("");
    });

    // ── Histórico — carrega ao mudar Tipo de Conta ───────────────────────────
    $("#select-tipoconta-fb").on("change", function () {
        const cdTipoConta = this.value;
        const $selH       = $("#select-historico-fb");
        const $label      = $("#txt-loading-historico");

        $selH.empty().append('<option value=""></option>')
            .prop("disabled", true).trigger("change");
        $label.text("");

        if (!cdTipoConta) return;

        $label.html('<i class="fas fa-spinner fa-spin mr-1"></i> Carregando históricos...');

        $.getJSON(ROUTES.historicos, { cd_tipoconta: cdTipoConta }, function (data) {
            $selH.empty().append('<option value=""></option>');
            data.forEach(function (item) {
                $selH.append(new Option(item.text, item.id));
            });
            $selH.prop("disabled", false).trigger("change");
            $label.text(data.length + " histórico(s) disponível(is)");
        }).fail(function () {
            $label.html('<span class="text-danger">Erro ao carregar históricos.</span>');
        });
    });

    // ── Executar importação → AJAX para controller (dev: retorna JSON) ────────
    $("#btn-executar-importacao-fb").on("click", function () {
        const cdEmpresa      = $("#select-empresa-fb").val();
        const cdPessoa       = $("#select-motorista-fb").val();
        const nmMotorista    = $("#select-motorista-fb option:selected").text().trim();
        const cdTipoConta    = $("#select-tipoconta-fb").val();
        const cdHistorico    = $("#select-historico-fb").val();
        const cdFormaPagto   = $("#select-forma-pagamento-fb").val();
        const rows           = $("#modal-importar-firebird").data("rows") || [];

        if (!cdEmpresa) {
            Swal.fire("Atenção", "Selecione a empresa.", "warning");
            return;
        }
        if (!cdPessoa) {
            Swal.fire("Atenção", "Selecione o motorista.", "warning");
            return;
        }
        if (!cdTipoConta) {
            Swal.fire("Atenção", "Selecione o tipo de conta.", "warning");
            return;
        }
        if (!cdHistorico) {
            Swal.fire("Atenção", "Selecione o histórico.", "warning");
            return;
        }
        if (!cdFormaPagto) {
            Swal.fire("Atenção", "Selecione a forma de pagamento.", "warning");
            return;
        }

        const $btn = $(this).prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Importando...');

        $.ajax({
            url:  ROUTES.importarFirebird,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                _token:         ROUTES.token,
                cd_empresa:     cdEmpresa,
                cd_pessoa:      cdPessoa,
                nm_motorista:   nmMotorista,
                cd_tipoconta:   cdTipoConta,
                cd_historico:   cdHistorico,
                cd_forma_pagto: cdFormaPagto,
                rows:           rows,
            }),
            success: function (resp) {
                $("#modal-importar-firebird").modal("hide");

                const temErros = resp.erros && resp.erros.length;
                Swal.fire({
                    icon:  temErros ? "warning" : "success",
                    title: temErros ? "Importado com pendências" : "Importação concluída",
                    html:  "<p>" + resp.message + "</p>"
                        + (temErros
                            ? "<ul style='text-align:left;font-size:12px'>"
                              + resp.erros.map(e => "<li>" + e + "</li>").join("")
                              + "</ul>"
                            : ""),
                }).then(function () {
                    carregarMescla();
                });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : "Não foi possível processar a importação.";
                Swal.fire("Erro", msg, "error");
            },
            complete: function () {
                $btn.prop("disabled", false).html('<i class="fas fa-check mr-1"></i> Importar para Firebird');
            },
        });
    });
}());
</script>
@endsection
