// Accordion de resumo (Local > Medida > Marca > Modelo) compartilhado entre as
// telas de Estoque: Carcaças da Casa e Pneus Novos.

function initAccordion(data, idDivAccordion = "accordionResumo") {
    let html = '';
    data.forEach((item, idx) => { html += renderNivel0(item, idx); });
    $("#" + idDivAccordion).html(html);
}

function renderNivel0(Nivel1Key, lIndex) {
    let inner = '';
    Nivel1Key.medida.forEach((item, maIndex) => {
        inner += renderNivel1(item, lIndex, maIndex);
    });

    return `
        <div class="card nivel1-card mb-2 border-0 shadow-sm">
            <div class="card-header p-0" style="background:#2d3748; border-left:4px solid #4299e1; border-radius:4px 4px 0 0;">
                <button class="btn btn-block text-white text-left accordion-item-header py-2 px-3"
                        data-toggle="collapse" data-target="#nivel0-${lIndex}">
                    <span style="font-size:0.87rem; font-weight:600;">
                        <i class="fas fa-map-marker-alt mr-1" style="color:#90cdf4;"></i>
                        ${Nivel1Key.local}
                    </span>
                    <span class="badge badge-pill badge-info" style="font-size:0.78rem;">${Nivel1Key.qtd} unid.</span>
                </button>
            </div>
            <div id="nivel0-${lIndex}" class="collapse">
                <div class="card-body p-2" style="background:#f7fafc;">
                    ${inner}
                </div>
            </div>
        </div>`;
}

function renderNivel1(Nivel2Key, lIndex, maIndex) {
    let inner = '';
    Nivel2Key.marca.forEach((item, moIndex) => {
        inner += renderNivel2(item, lIndex, maIndex, moIndex);
    });

    return `
        <div class="nivel2-container mb-2">
            <button class="btn btn-block text-left accordion-item-header py-2 px-3 bg-white border"
                    style="border-left:4px solid #718096 !important; font-size:0.82rem; font-weight:600; border-radius:4px;"
                    data-toggle="collapse" data-target="#medida-${lIndex}-${maIndex}">
                <span>
                    <i class="fas fa-ruler-combined mr-1 text-secondary"></i>
                    ${Nivel2Key.medida}
                </span>
                <span class="badge badge-pill badge-secondary" style="font-size:0.75rem;">${Nivel2Key.qtd} unid.</span>
            </button>
            <div id="medida-${lIndex}-${maIndex}" class="collapse mt-1 ml-2">
                ${inner}
            </div>
        </div>`;
}

function renderNivel2(Nivel3Key, lIndex, maIndex, moIndex) {
    let inner = '';
    Nivel3Key.modelo.forEach((item) => {
        inner += renderNivel3(item);
    });

    return `
        <div class="nivel3-container mb-1">
            <button class="btn btn-block text-left accordion-item-header py-1 px-3 bg-white border mt-1"
                    style="border-left:4px solid #17a2b8 !important; font-size:0.8rem; border-radius:4px;"
                    data-toggle="collapse" data-target="#marca-${lIndex}-${maIndex}-${moIndex}">
                <span>
                    <i class="fas fa-tag mr-1 text-info"></i>
                    ${Nivel3Key.marca}
                </span>
                <span class="badge badge-pill badge-info" style="font-size:0.72rem;">${Nivel3Key.qtd} unid.</span>
            </button>
            <div id="marca-${lIndex}-${maIndex}-${moIndex}" class="collapse mt-1 ml-2">
                <ul class="list-group list-group-flush">
                    ${inner}
                </ul>
            </div>
        </div>`;
}

function renderNivel3(Nivel4Key) {
    return `
        <li class="list-group-item px-3 py-1 d-flex justify-content-between align-items-center"
            style="border-left:3px solid #e2e8f0; border-radius:3px; font-size:0.78rem;">
            <span class="text-muted">
                <i class="fas fa-circle mr-1" style="font-size:0.5rem; vertical-align:middle; color:#cbd5e0;"></i>
                ${Nivel4Key.modelo}
            </span>
            <span class="badge badge-light border" style="font-size:0.72rem;">${Nivel4Key.qtd} unid.</span>
        </li>`;
}
