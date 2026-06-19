@extends('layouts.master')

@section('title', 'Importar Pedágio — ConnectCar')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-dark card-outline">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-file-import mr-2"></i> Importar Pedágio — ConnectCar
                        </h3>
                        <div class="card-tools d-flex align-items-center" style="gap:6px;">
                            <span class="badge badge-secondary d-none" id="badge-selecionados"></span>
                            <button type="button" class="btn btn-xs btn-primary" id="btn-abrir-upload">
                                <i class="fas fa-upload mr-1"></i> Carregar arquivo
                            </button>
                            <a href="{{ route('despesa.index') }}" class="btn btn-xs btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Voltar
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">

                        {{-- Área vazia (antes de carregar arquivo) --}}
                        <div id="area-vazia" class="text-center text-muted py-5">
                            <i class="fas fa-file-excel fa-3x mb-3 text-success opacity-50"></i>
                            <p class="mb-0">Nenhum arquivo carregado.</p>
                            <p class="small">Clique em <strong>Carregar arquivo</strong> para selecionar o Excel do ConnectCar.</p>
                        </div>

                        {{-- Card de filtros (visível após carregar) --}}
                        <div id="card-filtros-cc" class="card collapsed-card mx-3 mt-3 mb-0 d-none">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0 small"><i class="fas fa-filter mr-1"></i> Filtros</h6>
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
                                    <div class="col-12 col-md-1 mb-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btn-limpar-filtros-cc" title="Limpar filtros">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Toolbar de seleção (visível após carregar) --}}
                        <div id="toolbar-selecao" class="px-3 pt-3 pb-2 d-flex align-items-center d-none" style="gap:8px;">
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-selecionar-todos">
                                <i class="fas fa-check-double mr-1"></i> Selecionar todos
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-desmarcar-todos">
                                <i class="fas fa-times mr-1"></i> Desmarcar todos
                            </button>
                            <small class="text-muted ml-2">
                                Desmarque as linhas que <strong>não</strong> devem ser importadas.
                            </small>
                        </div>

                        {{-- Tabela --}}
                        <div id="area-tabela" class="px-3 pb-3 d-none">
                            <table class="table table-sm table-bordered table-hover mb-0 compact" id="tabela-revisar-connectcar">
                                <thead class="thead-dark">
                                    <tr id="thead-connectcar"></tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="thead-light">
                                    <tr id="tfoot-connectcar"></tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center d-none" id="footer-importar">
                        <small class="text-muted" id="txt-total-registros"></small>
                        <form method="POST" action="{{ route('despesa.connectcar.importar') }}" id="form-importar-connectcar">
                            @csrf
                            <input type="hidden" name="headers" id="input-headers">
                            <input type="hidden" name="rows"    id="input-rows-selecionados">
                            <button type="submit" class="btn btn-sm btn-success" id="btn-confirmar-importacao">
                                <i class="fas fa-database mr-1"></i> Importar Selecionados
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Modal upload --}}
    <div class="modal fade" id="modal-upload-connectcar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-upload mr-2"></i> Carregar arquivo ConnectCar
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
    // ── Constantes de filtragem ────────────────────────────────────────────────
    const COLUNAS_IGNORADAS = new Set([0, 3, 4, 5, 7, 8, 10, 11]);
    const TIPOS_IGNORADOS   = new Set(["EstornoPassagem", "Recarga", "MensalidadePrepagoEmpresarial"]);

    function filtrarColunas(row) {
        return row.filter((_, i) => !COLUNAS_IGNORADAS.has(i));
    }

    // ── Estado ─────────────────────────────────────────────────────────────────
    let dadosCarregados   = [];
    let headersCarregados = [];
    let dt                = null;

    // ── Abertura do modal ──────────────────────────────────────────────────────
    $("#btn-abrir-upload").on("click", function () {
        $("#arquivo-connectcar").val("");
        $(".custom-file-label[for='arquivo-connectcar']").text("Nenhum arquivo selecionado");
        $("#btn-processar-arquivo").prop("disabled", true);
        $("#erro-upload").addClass("d-none").text("");
        $("#modal-upload-connectcar").modal("show");
    });

    // ── Seleção de arquivo ─────────────────────────────────────────────────────
    $("#arquivo-connectcar").on("change", function () {
        const file = this.files[0];
        $("#erro-upload").addClass("d-none").text("");
        $("#btn-processar-arquivo").prop("disabled", true);

        if (!file) return;

        const ext = file.name.split(".").pop().toLowerCase();
        if (!["xlsx", "xls"].includes(ext)) {
            $("#erro-upload").removeClass("d-none").text("Formato inválido. Use .xlsx ou .xls.");
            return;
        }

        $(".custom-file-label[for='arquivo-connectcar']").text(file.name);
        $("#btn-processar-arquivo").prop("disabled", false);
    });

    // ── Processar arquivo ──────────────────────────────────────────────────────
    $("#btn-processar-arquivo").on("click", function () {
        const file = $("#arquivo-connectcar")[0].files[0];
        if (!file) return;

        const $btn = $(this).prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processando...');

        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const workbook = XLSX.read(e.target.result, { type: "array" });
                const sheet    = workbook.Sheets[workbook.SheetNames[0]];
                const rows     = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: "" });

                if (!rows || rows.length < 20) {
                    $("#erro-upload").removeClass("d-none")
                        .text("O arquivo não contém dados após a linha 19.");
                    $btn.prop("disabled", false).html('<i class="fas fa-cogs mr-1"></i> Processar');
                    return;
                }

                const headers = filtrarColunas(rows[18]);
                const data    = rows.slice(19)
                    .filter(r => r.some(c => String(c).trim() !== ""))          // ignora linhas em branco (inclusive última)
                    .filter(r => !TIPOS_IGNORADOS.has(String(r[6]).trim()))
                    .map(filtrarColunas)
                    .filter(r => r.some(c => String(c).trim() !== ""))          // re-filtra após remoção de colunas
                    .map(r => {
                        const row = [...r];
                        if (typeof row[0] === "string") {
                            row[0] = row[0].replace(/-/g, "");                  // remove hífen da placa (índice 0 nos dados)
                        }
                        return row;
                    });

                headersCarregados = headers;
                dadosCarregados   = data;

                $("#modal-upload-connectcar").modal("hide");
                renderTabela(headers, data);

            } catch (err) {
                $("#erro-upload").removeClass("d-none")
                    .text("Não foi possível processar o arquivo Excel.");
                $btn.prop("disabled", false).html('<i class="fas fa-cogs mr-1"></i> Processar');
            }
        };
        reader.readAsArrayBuffer(file);
    });

    // ── Renderizar DataTable ───────────────────────────────────────────────────
    function renderTabela(headers, data) {
        if (dt) { dt.destroy(); dt = null; }

        const lastDataIdx = headers.length - 1; // índice no array de dados (0-based)
        const lastDtIdx   = headers.length;     // índice no DT (0 = checkbox, 1..N = dados)

        // Rebuild thead
        $("#thead-connectcar").html(
            '<th style="width:40px;"><input type="checkbox" id="chk-all" checked></th>'
            + headers.map(h => `<th class="text-nowrap">${h}</th>`).join("")
        );

        // Rebuild tfoot: células vazias para todas menos a última (soma)
        $("#tfoot-connectcar").html(
            "<th></th>"
            + headers.map((_, i) =>
                i === lastDataIdx
                    ? '<th class="text-right font-weight-bold" id="tfoot-sum"></th>'
                    : "<th></th>"
            ).join("")
        );

        $("#tabela-revisar-connectcar tbody").empty();

        // Mostrar container ANTES de iniciar o DataTable para que scrollY calcule a altura corretamente
        $("#area-vazia").addClass("d-none");
        $("#card-filtros-cc, #toolbar-selecao, #area-tabela, #footer-importar").removeClass("d-none");

        dt = $("#tabela-revisar-connectcar").DataTable({
            data:    data,
            columns: [
                {
                    data:       null,
                    orderable:  false,
                    searchable: false,
                    className:  "text-center",
                    width:      "40px",
                    render:     () => '<input type="checkbox" class="chk-linha" checked>',
                },
                ...headers.map((_, i) => ({
                    data:           i,
                    defaultContent: "",
                    className:      i === lastDataIdx ? "text-right" : "",
                    render: i === lastDataIdx
                        ? function (val, type) {
                            const n = Math.abs(parseFloat(String(val).replace(",", ".")) || 0);
                            return type === "display"
                                ? n.toLocaleString("pt-BR", { minimumFractionDigits: 2 })
                                : n;
                          }
                        : undefined,
                })),
            ],
            rowCallback: function (row, rowData, index) {
                $(row).addClass("linha-revisar").attr("data-index", index);
            },
            footerCallback: function () {
                const api   = this.api();
                const total = api.column(lastDtIdx).data().reduce(function (acc, val) {
                    return acc + (Math.abs(parseFloat(String(val).replace(",", ".")) || 0));
                }, 0);
                $(api.column(lastDtIdx).footer()).html(
                    "Total: <strong>" + total.toLocaleString("pt-BR", { minimumFractionDigits: 2 }) + "</strong>"
                );
            },
            paging:         false,
            ordering:       false,
            searching:      true,
            info:           false,
            scrollY:        "400px",
            scrollX:        true,
            scrollCollapse: true,
            dom:            "t",
            language:       { url: "{{ asset('vendor/datatables/pt-BR.json') }}" },
        });

        atualizarContador();
    }

    // ── Helpers de checkbox ────────────────────────────────────────────────────
    function todosOsNos() {
        return dt ? $(dt.rows().nodes()) : $();
    }

    function atualizarContador() {
        const total      = dadosCarregados.length;
        const selecionados = todosOsNos().find(".chk-linha:checked").length;
        $("#badge-selecionados")
            .removeClass("d-none")
            .text(selecionados + " / " + total + " selecionado(s)");
        $("#txt-total-registros").html("Total lido: <strong>" + total + "</strong> registros");
    }

    $(document).on("change", "#chk-all", function () {
        const checked = this.checked;
        todosOsNos().find(".chk-linha").prop("checked", checked);
        todosOsNos().toggleClass("table-secondary", !checked);
        atualizarContador();
    });

    $("#tabela-revisar-connectcar").on("change", ".chk-linha", function () {
        $(this).closest("tr").toggleClass("table-secondary", !this.checked);
        $("#chk-all").prop("checked", todosOsNos().find(".chk-linha:not(:checked)").length === 0);
        atualizarContador();
    });

    $("#btn-selecionar-todos").on("click", function () {
        todosOsNos().find(".chk-linha").prop("checked", true).end().removeClass("table-secondary");
        $("#chk-all").prop("checked", true);
        atualizarContador();
    });

    $("#btn-desmarcar-todos").on("click", function () {
        todosOsNos().find(".chk-linha").prop("checked", false).end().addClass("table-secondary");
        $("#chk-all").prop("checked", false);
        atualizarContador();
    });

    // ── Filtros ────────────────────────────────────────────────────────────────
    // DT col 0=checkbox 1=placa 2=data 3=tipo 4=valor
    $("#filtro-cc-placa").on("keyup", function () {
        if (dt) dt.column(1).search(this.value).draw();
    });
    $("#filtro-cc-data").on("keyup", function () {
        if (dt) dt.column(2).search(this.value).draw();
    });
    $("#filtro-cc-tipo").on("keyup", function () {
        if (dt) dt.column(3).search(this.value).draw();
    });
    $("#filtro-cc-valor").on("keyup", function () {
        if (dt) dt.column(4).search(this.value).draw();
    });
    $("#btn-limpar-filtros-cc").on("click", function () {
        $("#filtro-cc-placa, #filtro-cc-data, #filtro-cc-tipo, #filtro-cc-valor").val("");
        if (dt) dt.columns([1, 2, 3, 4]).search("").draw();
    });

    // ── Submit ─────────────────────────────────────────────────────────────────
    $("#form-importar-connectcar").on("submit", function (e) {
        const selecionados = [];
        todosOsNos().each(function () {
            if ($(this).find(".chk-linha").is(":checked")) {
                selecionados.push(dadosCarregados[parseInt($(this).data("index"))]);
            }
        });

        if (!selecionados.length) {
            e.preventDefault();
            Swal.fire("Atenção", "Selecione ao menos um registro para importar.", "warning");
            return;
        }

        $("#input-headers").val(JSON.stringify(headersCarregados));
        $("#input-rows-selecionados").val(JSON.stringify(selecionados));
    });
}());
</script>
@endsection
