let intervaloAtualizacao = null;
let intervaloRelogio = null;
let tempoRestante = 300; // 5 minutos em segundos


$("#atualizarTela").change(function () {
    iniciarAtualizacaoAutomatica.call(this);
});

$("#atualizarTela").prop("checked", true).trigger("change"); // Ativa a atualização automática ao carregar a página


function iniciarAtualizacaoAutomatica() {
    clearInterval(intervaloAtualizacao);
    clearInterval(intervaloRelogio);

    if ($(this).is(":checked")) {
        Swal.fire({
            title: "Atualização Automática",
            text: "A tela será atualizada a cada 5 minutos.",
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
        });

        // Habilitar atualização automática
        intervaloAtualizacao = setInterval(function () {
            let activeTab = $("#tab-pcp .nav-link.active");
            let empresa = activeTab.data("empresa");
            if (empresa) {
                initTable("pneus-lote-pcp-" + empresa, empresa);
            }

            tempoRestante = 300; // reseta o contador
        }, 300000); // Atualiza a cada 5 minutos (300.000 milissegundos)

        intervaloRelogio = setInterval(function () {
            tempoRestante--;
            let minutos = Math.floor(tempoRestante / 60);
            let segundos = tempoRestante % 60;

            let tempoFormatado = `${minutos.toString().padStart(2, "0")}:${segundos.toString().padStart(2, "0")}`;

            $("#minutosParaAtualizacao").text(tempoFormatado);

            if (tempoRestante <= 0) {
                tempoRestante = 300; // Reinicia o contador
            }
        }, 1000); // Atualiza o relógio a cada segundo
    } else {
        Swal.fire({
            title: "Atualização Automática Desativada",
            text: "A tela não será mais atualizada automaticamente.",
            icon: "info",
            timer: 2000,
            showConfirmButton: false,
        });

        // Desabilitar atualização automática
        clearInterval(intervaloAtualizacao);
        clearInterval(intervaloRelogio);
        $("#minutosParaAtualizacao").text("05:00");
        tempoRestante = 300; // Reinicia o contador
    }
}
