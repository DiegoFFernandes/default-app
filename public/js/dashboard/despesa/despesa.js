$(document).ready(function () {
    initFormSubmit();
    initCamera();
    initTabEvents();
    initFiltros();
});

// ─── Estado global de fotos ───────────────────────────────────────────────────
let fotosParaEnviar = []; // array de File (câmera + galeria)

// ─── DataTable ────────────────────────────────────────────────────────────────
let tabelaComprovantes;

function initDataTable() {
    tabelaComprovantes = $("#tabela-comprovantes").DataTable({
        processing: false,
        serverSide: false,
        ajax: { url: window.routes.getComprovantes, type: "GET" },
        language: { url: window.routes.languageDatatables },
        order: [[2, "desc"]],
        columns: [
            { data: "nm_usuario", title: "Usuário" },
            { data: "tp_despesa", title: "Tipo", render: renderTipo },
            {
                data: "vl_consumido",
                title: "Valor",
                render: (d) => "R$ " + d,
                className: "text-right",
            },
            { data: "dt_despesa", title: "Data", className: "text-center" },
            { data: "ds_observacao", title: "Observação" },
            {
                data: "st_visto",
                title: "Visto",
                render: renderVisto,
                className: "text-center",
            },
            {
                data: null,
                title: "Ações",
                render: renderAcoes,
                orderable: false,
                searchable: false,
                className: "text-center",
                width: "90px",
            },
        ],
        responsive: true,
        paging: false,
        scrollY: "400px",
        scrollCollapse: true,
        dom: "ti",
        drawCallback: function () {
            agendarAtualizacaoStats(this.api());
        },
    });

    $("#tabela-comprovantes")
        .on("preXhr.dt", function () {
            Swal.fire({
                title: "Carregando...",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });
        })
        .on("xhr.dt", function () {
            Swal.close();
        });

    $("#btn-reload-lista").on("click", function () {
        tabelaComprovantes.ajax.reload(null, false);
    });
}

function renderTipo(data) {
    const labels = {
        ALI: '<span class="badge badge-success badge-tipo">Alimentação</span>',
        COM: '<span class="badge badge-warning badge-tipo">Combustível</span>',
        HOS: '<span class="badge badge-info badge-tipo">Hospedagem</span>',
        PED: '<span class="badge badge-secondary badge-tipo">Pedágio</span>',
    };
    return labels[data] || data;
}

function renderVisto(data) {
    return data === "S"
        ? '<span class="badge badge-success">Sim</span>'
        : '<span class="badge badge-danger">Não</span>';
}

function renderAcoes(data, type, row) {
    const temFotos = row.fotos && row.fotos.length > 0;
    const fotosAttr = temFotos
        ? `data-fotos='${JSON.stringify(row.fotos)}'`
        : "";

    const fotosBtn = temFotos
        ? `<button class="btn btn-xs btn-info btn-ver-fotos mr-1" ${fotosAttr} title="Ver fotos"><i class="fas fa-images"></i></button>`
        : `<button class="btn btn-xs btn-secondary mr-1" disabled title="Sem fotos"><i class="fas fa-image"></i></button>`;

    const dtIso = row.dt_despesa.split("/").reverse().join("-");
    const obs    = row.ds_observacao !== "-" ? row.ds_observacao : "";
    const editBtn = `<button class="btn btn-xs btn-warning btn-editar mr-1"
        data-id="${row.id}" data-tp="${row.tp_despesa}"
        data-valor="${row.vl_consumido}" data-data="${dtIso}"
        data-obs="${obs}" title="Editar"><i class="fas fa-edit"></i></button>`;

    let vistoBtn = "";
    if (window.canStatusDespesas) {
        const vistoClass =
            row.st_visto === "S" ? "btn-success" : "btn-outline-success";
        const vistoIcon =
            row.st_visto === "S" ? "fa-check-circle" : "fa-circle";
        const vistoTitle =
            row.st_visto === "S"
                ? "Marcar como não visto"
                : "Marcar como visto";
        vistoBtn = `<button class="btn btn-xs ${vistoClass} btn-toggle-visto" data-id="${row.id}" title="${vistoTitle}"><i class="fas ${vistoIcon}"></i></button>`;
    }

    return editBtn + fotosBtn + vistoBtn;
}

// ─── Toggle Visto ─────────────────────────────────────────────────────────────
$(document).on("click", ".btn-toggle-visto", function () {
    const url = window.routes.toggleVisto.replace(":id", $(this).data("id"));
    $.ajax({
        url,
        type: "POST",
        data: { _token: window.routes.token },
        success: () => tabelaComprovantes.ajax.reload(null, false),
        error: () =>
            Swal.fire("Erro", "Não foi possível atualizar o status.", "error"),
    });
});

// ─── Editar Comprovante ───────────────────────────────────────────────────────
$(document).on("click", ".btn-editar", function () {
    const btn = $(this);
    $("#edit-id").val(btn.data("id"));
    $("#edit-tp_despesa").val(btn.data("tp"));
    $("#edit-vl_consumido").val(btn.data("valor"));
    $("#edit-dt_despesa").val(btn.data("data"));
    $("#edit-ds_observacao").val(btn.data("obs"));
    $("#modal-editar").modal("show");
});

$("#btn-salvar-edicao").on("click", function () {
    const id  = $("#edit-id").val();
    const url = window.routes.updateDespesa.replace(":id", id);
    const btn = $(this);

    btn.prop("disabled", true).html(
        '<i class="fas fa-spinner fa-spin mr-1"></i> Salvando...',
    );

    const vlRaw = $("#edit-vl_consumido")
        .val()
        .replace(/\./g, "")
        .replace(",", ".");

    $.ajax({
        url,
        type: "PUT",
        data: {
            _token:        window.routes.token,
            tp_despesa:    $("#edit-tp_despesa").val(),
            vl_consumido:  vlRaw,
            dt_despesa:    $("#edit-dt_despesa").val(),
            ds_observacao: $("#edit-ds_observacao").val(),
        },
        success: function (res) {
            $("#modal-editar").modal("hide");
            Swal.fire({
                icon: "success",
                title: "Sucesso!",
                text: res.message,
                timer: 2000,
                showConfirmButton: false,
            });
            tabelaComprovantes.ajax.reload(null, false);
        },
        error: function (xhr) {
            const msg =
                xhr.responseJSON?.error || "Erro ao atualizar comprovante.";
            Swal.fire("Erro", msg, "error");
        },
        complete: function () {
            btn.prop("disabled", false).html(
                '<i class="fas fa-save mr-1"></i> Salvar',
            );
        },
    });
});

// ─── Modal de Fotos (lista) ────────────────────────────────────────────────────
$(document).on("click", ".btn-ver-fotos", function () {
    const fotos = $(this).data("fotos");
    const inner = $("#carousel-fotos-inner");
    const indicators = $("#carousel-indicators");

    inner.empty();
    indicators.empty();
    $("#sem-fotos").hide();
    $("#btn-download-foto").hide();

    if (!fotos || fotos.length === 0) {
        $("#sem-fotos").show();
    } else {
        fotos.forEach(function (url, idx) {
            const active = idx === 0 ? "active" : "";
            indicators.append(
                `<li data-target="#carousel-fotos" data-slide-to="${idx}" class="${active}"></li>`,
            );
            inner.append(`
                <div class="carousel-item ${active}">
                    <img src="${url}" class="d-block mx-auto"
                         style="max-height:70vh;max-width:100%;object-fit:contain;" alt="Comprovante ${idx + 1}">
                    <div class="carousel-caption d-none d-md-block">
                        <small>${idx + 1} / ${fotos.length}</small>
                    </div>
                </div>
            `);
        });
        $("#btn-download-foto").show();
    }

    $("#modal-fotos").modal("show");
});

$("#carousel-fotos").on("slid.bs.carousel", function () {
    // URL da foto ativa é atualizada automaticamente via DOM ao clicar em Download
});

$("#btn-download-foto").on("click", function () {
    const url = $("#carousel-fotos-inner .carousel-item.active img").attr("src");
    if (!url) return;

    const btn = $(this);
    btn.prop("disabled", true);

    fetch(url)
        .then((r) => r.blob())
        .then((blob) => {
            const a = document.createElement("a");
            a.href = URL.createObjectURL(blob);
            a.download = "comprovante_" + Date.now() + ".jpg";
            a.click();
            URL.revokeObjectURL(a.href);
        })
        .finally(() => btn.prop("disabled", false));
});

// ─── Câmera (getUserMedia) ────────────────────────────────────────────────────
let cameraStream = null;
let fotosSessao = [];
let todosDispositivos = []; // todos os videoinputs
let camerasTraseiras = []; // filtradas e ordenadas (principal primeiro)
let rearIndex = 0; // índice atual nas câmeras traseiras
let currentDeviceId = null;

function initCamera() {
    $("#btn-abrir-camera").on("click", abrirCamera);
    $("#btn-fechar-camera").on("click", fecharCamera);
    $("#btn-capturar").on("click", capturarFoto);
    $("#btn-alternar-camera").on("click", ciclarCamera);
    $("#btn-usar-fotos").on("click", usarFotosSessao);

    $("#btn-abrir-galeria").on("click", () =>
        $("#fotos-galeria").trigger("click"),
    );
    $("#fotos-galeria").on("change", onGaleriaChange);
    $("#btn-limpar-fotos").on("click", limparTodasFotos);

    $(document).on("click", ".btn-remover-foto", function () {
        fotosParaEnviar.splice(parseInt($(this).data("idx")), 1);
        atualizarPreviewGlobal();
    });

    $("#modal-camera").on("hidden.bs.modal", pararStream);
    initScanner();
}

async function abrirCamera() {
    fotosSessao = [];
    $("#fotos-capturadas").empty();
    $("#fotos-capturadas-container").hide();
    $("#btn-usar-fotos").hide();
    $("#qtd-fotos-capturadas").text("0");
    $("#camera-erro").hide();
    $("#btn-capturar").prop("disabled", false);
    $("#modal-camera").modal("show");
    await iniciarStreamInicial();
    if (scannerMode) iniciarLoopScanner();
}

function fecharCamera() {
    $("#modal-camera").modal("hide");
}

// Primeiro acesso: abre com facingMode e depois enumera para detectar ultra-wide
async function iniciarStreamInicial() {
    pararStream();
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: "environment" },
                width: { ideal: 1920 },
                height: { ideal: 1080 },
            },
            audio: false,
        });
        const video = document.getElementById("camera-video");
        video.srcObject = cameraStream;
        currentDeviceId = cameraStream
            .getVideoTracks()[0]
            .getSettings().deviceId;

        // Com permissão concedida, enumera e tenta selecionar a câmera principal
        await detectarEAjustarCamera();
    } catch (err) {
        exibirErroCamera(err);
    }
}

async function detectarEAjustarCamera() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        todosDispositivos = devices.filter((d) => d.kind === "videoinput");

        // Filtra câmeras traseiras (labels disponíveis após obter permissão)
        const traseiras = todosDispositivos.filter(
            (d) =>
                d.label.match(/facing back|back camera|rear/i) ||
                (d.label && !d.label.match(/front|facing front|user|selfie/i)),
        );
        const lista = traseiras.length > 0 ? traseiras : todosDispositivos;

        // Ordena: câmera "camera2 0" (ultra-wide em Samsung/Pixel) vai para o fim
        // A câmera principal costuma ser camera2 2 no S21, ou simplesmente não ser "0"
        camerasTraseiras = [...lista].sort((a, b) => {
            const score = (label) => {
                if (/ultra.?wide|\b0\.5x\b/i.test(label)) return 2; // ultra-wide explícito — fim
                if (/camera2 0[,\s]/i.test(label)) return 1; // camera2 0 — penúltimo
                return 0; // demais — primeiro
            };
            return score(a.label) - score(b.label);
        });

        // Descobre índice da câmera que está ativa agora
        rearIndex = camerasTraseiras.findIndex(
            (d) => d.deviceId === currentDeviceId,
        );
        if (rearIndex < 0) rearIndex = 0;

        // Se a câmera atual parece ser ultra-wide e há outras opções, troca automaticamente
        const atual = camerasTraseiras[rearIndex];
        const isUltraWide =
            atual &&
            (/ultra.?wide|\b0\.5x\b/i.test(atual.label) ||
                /camera2 0[,\s]/i.test(atual.label));

        if (isUltraWide && camerasTraseiras.length > 1) {
            rearIndex = 0; // índice 0 após o sort já é a principal
            await iniciarStreamPorId(camerasTraseiras[0].deviceId);
        }

        atualizarLabelBotaoAlternar();
    } catch (_) {
        // Silencioso — continua com o stream atual
    }
}

async function ciclarCamera() {
    if (camerasTraseiras.length > 1) {
        rearIndex = (rearIndex + 1) % camerasTraseiras.length;
        await iniciarStreamPorId(camerasTraseiras[rearIndex].deviceId);
        atualizarLabelBotaoAlternar();
    } else if (todosDispositivos.length > 1) {
        // Dispositivo com apenas 1 traseira: alterna frente/trás
        const front = todosDispositivos.find(
            (d) =>
                d.label.match(/front|facing front|user|selfie/i) &&
                d.deviceId !== currentDeviceId,
        );
        const target = front ? front.deviceId : camerasTraseiras[0]?.deviceId;
        if (target) await iniciarStreamPorId(target);
        atualizarLabelBotaoAlternar();
    }
}

async function iniciarStreamPorId(deviceId) {
    pararStream();
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: { exact: deviceId } },
            audio: false,
        });
        document.getElementById("camera-video").srcObject = cameraStream;
        currentDeviceId = cameraStream
            .getVideoTracks()[0]
            .getSettings().deviceId;
    } catch (err) {
        exibirErroCamera(err);
    }
}

function atualizarLabelBotaoAlternar() {
    const total = camerasTraseiras.length;
    const label =
        total > 1
            ? `<i class="fas fa-sync-alt mr-1"></i> Câmera ${rearIndex + 1}/${total}`
            : `<i class="fas fa-sync-alt mr-1"></i> Alternar`;
    $("#btn-alternar-camera").html(label);
}

function pararStream() {
    pararLoopScanner();
    if (cameraStream) {
        cameraStream.getTracks().forEach((t) => t.stop());
        cameraStream = null;
    }
    const video = document.getElementById("camera-video");
    if (video) video.srcObject = null;
    $("#btn-capturar").prop("disabled", false);
}

function exibirErroCamera(err) {
    const msgs = {
        NotAllowedError:
            "Permissão de câmera negada. Verifique as configurações do navegador.",
        NotFoundError: "Nenhuma câmera encontrada neste dispositivo.",
        NotReadableError: "A câmera está sendo usada por outro aplicativo.",
    };
    $("#camera-erro-msg").text(
        msgs[err.name] || "Erro ao acessar câmera: " + err.message,
    );
    $("#camera-erro").show();
    $("#btn-capturar").prop("disabled", true);
}

function capturarFoto() {
    const video = document.getElementById("camera-video");
    const canvas = document.getElementById("camera-canvas");

    if (!video.videoWidth) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    if (scannerMode) {
        const { x, y, w, h, vw, vh } = guideRect(video);
        const sx = video.videoWidth / vw;
        const sy = video.videoHeight / vh;
        canvas.width  = Math.round(w * sx);
        canvas.height = Math.round(h * sy);
        canvas.getContext("2d").drawImage(
            video,
            x * sx, y * sy, w * sx, h * sy,
            0, 0, canvas.width, canvas.height,
        );
    } else {
        canvas.getContext("2d").drawImage(video, 0, 0);
    }

    canvas.toBlob(
        function (blob) {
            const file = new File([blob], `comprovante_${Date.now()}.jpg`, {
                type: "image/jpeg",
            });
            fotosSessao.push(file);

            const url = URL.createObjectURL(blob);
            const num = fotosSessao.length;
            $("#fotos-capturadas").append(`
            <div class="position-relative mr-1 mb-1" style="display:inline-block;">
                <img src="${url}" style="height:64px;width:64px;object-fit:cover;border-radius:4px;border:2px solid #28a745;">
                <span class="badge badge-dark position-absolute" style="top:2px;left:2px;font-size:9px;">${num}</span>
            </div>
        `);
            $("#fotos-capturadas-container").show();
            $("#qtd-fotos-capturadas").text(num);
            $("#btn-usar-fotos").show();

            $("#camera-video").css("opacity", "0.3");
            setTimeout(() => $("#camera-video").css("opacity", "1"), 150);
        },
        "image/jpeg",
        0.92,
    );
}

function usarFotosSessao() {
    fotosSessao.forEach((f) => fotosParaEnviar.push(f));
    fotosSessao = [];
    fecharCamera();
    atualizarPreviewGlobal();
}

// ─── Scanner de Documento (guia visual + crop) ───────────────────────────────
let scannerMode = false;
let scannerAnimFrame = null;

const GUIDE_MARGIN = 0.07; // 7% de margem lateral
const GUIDE_ASPECT = 1.414; // proporção A4

function initScanner() {
    $("#btn-scanner").on("click", toggleScannerMode);
}

function toggleScannerMode() {
    scannerMode = !scannerMode;

    if (scannerMode) {
        $("#btn-scanner").removeClass("btn-outline-info").addClass("btn-info");
        iniciarLoopScanner();
    } else {
        $("#btn-scanner").removeClass("btn-info").addClass("btn-outline-info");
        desativarScanner();
    }
}

function guideRect(video) {
    const vw = video.clientWidth;
    const vh = video.clientHeight;
    const margin = vw * GUIDE_MARGIN;
    const w = vw - margin * 2;
    const h = Math.min(w * GUIDE_ASPECT, vh * 0.88);
    return { x: margin, y: (vh - h) / 2, w, h, vw, vh };
}

function iniciarLoopScanner() {
    pararLoopScanner();
    const video = document.getElementById("camera-video");
    const canvas = document.getElementById("scanner-canvas");
    $(canvas).show();

    function loop() {
        if (!scannerMode) return;
        const { x, y, w, h, vw, vh } = guideRect(video);
        canvas.width = vw;
        canvas.height = vh;
        const ctx = canvas.getContext("2d");

        ctx.clearRect(0, 0, vw, vh);
        ctx.fillStyle = "rgba(0,0,0,0.45)";
        ctx.fillRect(0, 0, vw, vh);
        ctx.clearRect(x, y, w, h);

        ctx.strokeStyle = "#00e676";
        ctx.lineWidth = 2;
        ctx.strokeRect(x, y, w, h);

        const c = Math.min(w, h) * 0.07;
        ctx.lineWidth = 4;
        ctx.strokeStyle = "#fff";
        [
            [x,     y,     c,  0,  0,  c],
            [x + w, y,    -c,  0,  0,  c],
            [x,     y + h, c,  0,  0, -c],
            [x + w, y + h,-c,  0,  0, -c],
        ].forEach(([px, py, dx1, dy1, dx2, dy2]) => {
            ctx.beginPath();
            ctx.moveTo(px + dx1, py + dy1);
            ctx.lineTo(px, py);
            ctx.lineTo(px + dx2, py + dy2);
            ctx.stroke();
        });

        scannerAnimFrame = requestAnimationFrame(loop);
    }
    scannerAnimFrame = requestAnimationFrame(loop);
}

function pararLoopScanner() {
    if (scannerAnimFrame) {
        cancelAnimationFrame(scannerAnimFrame);
        scannerAnimFrame = null;
    }
}

function desativarScanner() {
    pararLoopScanner();
    const canvas = document.getElementById("scanner-canvas");
    if (canvas) {
        canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
        $(canvas).hide();
    }
}

// ─── Galeria ──────────────────────────────────────────────────────────────────
function onGaleriaChange() {
    Array.from(this.files).forEach((f) => fotosParaEnviar.push(f));
    $(this).val(""); // limpa para permitir selecionar os mesmos arquivos novamente
    atualizarPreviewGlobal();
}

// ─── Preview global (câmera + galeria) ────────────────────────────────────────
function atualizarPreviewGlobal() {
    const container = $("#preview-container");
    container.empty();

    if (fotosParaEnviar.length === 0) {
        $("#preview-fotos").hide();
        return;
    }

    fotosParaEnviar.forEach(function (file, idx) {
        const url = URL.createObjectURL(file);
        container.append(`
            <div class="position-relative mr-1 mb-1" style="display:inline-block;">
                <img src="${url}" style="height:80px;width:80px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                <button type="button" class="btn-remover-foto" data-idx="${idx}"
                    style="position:absolute;top:2px;right:2px;background:rgba(220,53,69,.85);border:none;
                           border-radius:50%;width:18px;height:18px;padding:0;line-height:18px;color:#fff;font-size:10px;cursor:pointer;">
                    &times;
                </button>
            </div>
        `);
    });

    $("#qtd-preview").text(fotosParaEnviar.length);
    $("#preview-fotos").show();
}

function limparTodasFotos() {
    fotosParaEnviar = [];
    atualizarPreviewGlobal();
}

// ─── Formulário de Registro ───────────────────────────────────────────────────
function initFormSubmit() {
    $("#form-registrar-comprovante").on("submit", function (e) {
        e.preventDefault();

        const btn = $("#btn-salvar");
        btn.prop("disabled", true).html(
            '<i class="fas fa-spinner fa-spin mr-1"></i> Salvando...',
        );

        const formData = new FormData(this);

        // Converte valor com máscara monetária para número decimal
        const vlRaw = $("#vl_consumido")
            .val()
            .replace(/\./g, "")
            .replace(",", ".");
        formData.set("vl_consumido", vlRaw);

        // Adiciona todas as fotos (câmera + galeria)
        fotosParaEnviar.forEach(function (file) {
            formData.append("fotos[]", file, file.name);
        });

        $.ajax({
            url: window.routes.storeDespesa,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": window.routes.token },
            success: function (res) {
                Swal.fire({
                    icon: "success",
                    title: "Sucesso!",
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
                resetForm();
            },
            error: function (xhr) {
                const msg =
                    xhr.responseJSON?.error || "Erro ao registrar comprovante.";
                Swal.fire("Erro", msg, "error");
            },
            complete: function () {
                btn.prop("disabled", false).html(
                    '<i class="fas fa-save mr-1"></i> Registrar Comprovante',
                );
            },
        });
    });
}

function resetForm() {
    $("#form-registrar-comprovante")[0].reset();
    $("#dt_despesa").val(new Date().toISOString().split("T")[0]);
    fotosParaEnviar = [];
    atualizarPreviewGlobal();
    if ($("#tp_despesa").data("select2")) {
        $("#tp_despesa").val("").trigger("change");
    }
}

// ─── Filtros da lista ─────────────────────────────────────────────────────────
function initFiltros() {
    if (!window.canStatusDespesas) {
        $("#col-filtro-usuario").hide();
    }

    $("#filtro-usuario").on("keyup", function () {
        tabelaComprovantes.column(0).search(this.value).draw();
    });

    $("#filtro-tipo").on("change", function () {
        tabelaComprovantes.column(1).search(this.value).draw();
    });

    $("#filtro-visto").on("change", function () {
        tabelaComprovantes.column(5).search(this.value).draw();
    });

    $("#filtro-data").on("keyup", function () {
        tabelaComprovantes.column(3).search(this.value).draw();
    });

    $("#btn-limpar-filtros").on("click", function () {
        $("#filtro-usuario, #filtro-data").val("");
        $("#filtro-tipo, #filtro-visto").val("");
        tabelaComprovantes.columns().search("").draw();
    });

    $(document).on("click", ".btn-periodo", function () {
        statsDias = parseInt($(this).data("days"));
        $(".btn-periodo")
            .removeClass("btn-secondary")
            .addClass("btn-outline-secondary");
        $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
        if (tabelaComprovantes) {
            atualizarStats(tabelaComprovantes);
        }
    });
}

// ─── Recarrega DataTable ao entrar na aba Lista ───────────────────────────────
function initTabEvents() {
    $("#tab-lista").on("shown.bs.tab", function () {
        $("#tools-lista").show();
        if (!tabelaComprovantes) {
            initDataTable();
        } else {
            tabelaComprovantes.ajax.reload(null, false);
        }
    });
    $("#tab-registrar").on("shown.bs.tab", function () {
        $("#tools-lista").hide();
    });
}

// ─── Stats e Gráficos ─────────────────────────────────────────────────────────
let statsDias = 30;
let statsDebounce;

function agendarAtualizacaoStats(api) {
    clearTimeout(statsDebounce);
    statsDebounce = setTimeout(function () {
        atualizarStats(api);
    }, 250);
}

function filtrarPorPeriodo(rows) {
    if (!statsDias) return rows;
    const limite = new Date();
    limite.setDate(limite.getDate() - statsDias);
    limite.setHours(0, 0, 0, 0);
    return rows.filter(function (r) {
        const p = r.dt_despesa.split("/");
        return new Date(+p[2], p[1] - 1, +p[0]) >= limite;
    });
}

function parseMoeda(str) {
    return parseFloat(String(str).replace(/\./g, "").replace(",", ".")) || 0;
}

function fmtMoeda(val) {
    return (
        "R$ " +
        (typeof formatarValorBR === "function"
            ? formatarValorBR(val)
            : val.toLocaleString("pt-BR", { minimumFractionDigits: 2 }))
    );
}

function atualizarStats(api) {
    // Usa os registros visíveis (filtros da tabela) e aplica o filtro de período
    const rowsTabela = api.rows({ search: "applied" }).data().toArray();
    const rows = filtrarPorPeriodo(rowsTabela);

    if (!rows.length) {
        $("#painel-stats, #painel-graficos").hide();
        return;
    }

    // ── Métricas ──────────────────────────────────────────────────────────────
    const valores = rows.map((r) => parseMoeda(r.vl_consumido));
    const totalValor = valores.reduce((a, b) => a + b, 0);
    const mediaValor = totalValor / valores.length;
    const maiorValor = Math.max(...valores);
    const usuariosUnicos = new Set(rows.map((r) => r.nm_usuario)).size;
    const naoVistos = rows.filter((r) => r.st_visto === "N").length;
    $("#stat-total-valor").text(fmtMoeda(totalValor));
    $("#stat-media-valor").text(fmtMoeda(mediaValor));
    $("#stat-maior-valor").text(fmtMoeda(maiorValor));
    $("#stat-qtd-lancamentos").text(rows.length);
    $("#stat-qtd-usuarios").text(usuariosUnicos);
    $("#stat-nao-vistos").text(naoVistos);

    // ── Dados para gráficos ───────────────────────────────────────────────────
    const tiposLabel = {
        ALI: "Alimentação",
        COM: "Combustível",
        HOS: "Hospedagem",
        PED: "Pedágio",
    };

    // Valor por usuário
    const byUser = {};
    rows.forEach((r, i) => {
        byUser[r.nm_usuario] = (byUser[r.nm_usuario] || 0) + valores[i];
    });
    const userItems = Object.entries(byUser)
        .sort((a, b) => b[1] - a[1])
        .map(([nome, valor]) => ({ nome, valor }));

    // Valor por tipo
    const byTipoValor = {};
    rows.forEach((r, i) => {
        const lbl = tiposLabel[r.tp_despesa] || r.tp_despesa;
        byTipoValor[lbl] = (byTipoValor[lbl] || 0) + valores[i];
    });
    const tipoValorItems = Object.entries(byTipoValor).map(([nome, valor]) => ({
        nome,
        valor,
    }));

    // Quantidade por tipo
    const byTipoQtd = {};
    rows.forEach((r) => {
        const lbl = tiposLabel[r.tp_despesa] || r.tp_despesa;
        byTipoQtd[lbl] = (byTipoQtd[lbl] || 0) + 1;
    });
    const tipoQtdItems = Object.entries(byTipoQtd).map(([nome, valor]) => ({
        nome,
        valor,
    }));

    // Valor por dia (ordenado cronologicamente)
    const byDia = {};
    rows.forEach((r, i) => {
        byDia[r.dt_despesa] = (byDia[r.dt_despesa] || 0) + valores[i];
    });
    const diasOrdenados = Object.keys(byDia).sort((a, b) => {
        const [da, ma, ya] = a.split("/").map(Number);
        const [db, mb, yb] = b.split("/").map(Number);
        return new Date(ya, ma - 1, da) - new Date(yb, mb - 1, db);
    });

    // Status auditoria (pizza)
    const vistos = rows.filter((r) => r.st_visto === "S").length;
    const statusItems = [
        { nome: "Visto", valor: vistos },
        { nome: "Não visto", valor: naoVistos },
    ].filter((i) => i.valor > 0);

    // ── Renderiza gráficos usando chart-helpers.js ────────────────────────────
    // Ajusta altura do bar de usuários dinamicamente antes de renderizar
    const dynH = Math.max(220, userItems.length * 24 + 40);
    $("#chart-usuario-valor")
        .closest(".card-body")
        .css("height", dynH + "px");

    barStatic("chart-usuario-valor", userItems);
    pizzaStatic("chart-tipo-pizza", tipoValorItems);
    barStatic("chart-tipo-qtd", tipoQtdItems);
    barVertical("chart-por-dia", diasOrdenados, [
        {
            label: "Valor (R$)",
            data: diasOrdenados.map((d) => byDia[d]),
            color: "#48bb78",
        },
    ]);
    pizzaStatic("chart-status-visto", statusItems);

    $("#painel-stats, #painel-graficos").show();
}
