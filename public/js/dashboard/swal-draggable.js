/**
 * makeSwalDraggable
 *
 * Deixa o popup do SweetAlert2 atualmente aberto arrastável pela área do título,
 * permitindo mover o modal para o lado e enxergar o que está atrás dele sem precisar
 * fechá-lo. SweetAlert2 não tem suporte nativo a isso — este helper aplica o
 * comportamento no popup vivo a cada chamada.
 *
 * Chame dentro do `didOpen` do Swal.fire (o popup só existe no DOM nesse momento):
 *
 *   Swal.fire({
 *       title: 'Minha tela',
 *       html: '...',
 *       didOpen: function () {
 *           makeSwalDraggable();
 *       }
 *   });
 *
 * @param {string} [handleSelector='.swal2-title'] Seletor (dentro do popup) usado como
 *        "pega" do arrasto. Passe outro seletor se o título não for adequado (ex: some
 *        modais usam showConfirmButton:false e um cabeçalho customizado no html).
 */
function makeSwalDraggable(handleSelector) {
    var popup = Swal.getPopup();
    if (!popup) {
        return;
    }

    var handle = popup.querySelector(handleSelector || '.swal2-title');
    if (!handle) {
        return;
    }

    handle.style.cursor = 'move';

    var arrastando = false;
    var offsetX = 0;
    var offsetY = 0;

    function aoMoverMouse(e) {
        if (!arrastando) {
            return;
        }
        popup.style.left = (e.clientX - offsetX) + 'px';
        popup.style.top = (e.clientY - offsetY) + 'px';
    }

    function aoSoltarMouse() {
        arrastando = false;
        document.body.style.userSelect = '';
        document.removeEventListener('mousemove', aoMoverMouse);
        document.removeEventListener('mouseup', aoSoltarMouse);
    }

    handle.addEventListener('mousedown', function (e) {
        var rect = popup.getBoundingClientRect();

        // Tira o popup do modo centralizado do SweetAlert2 e fixa a posição atual antes
        // de começar a mover, senão o primeiro movimento faria ele "pular".
        popup.style.position = 'fixed';
        popup.style.margin = '0';
        popup.style.left = rect.left + 'px';
        popup.style.top = rect.top + 'px';

        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;
        arrastando = true;

        document.body.style.userSelect = 'none';
        document.addEventListener('mousemove', aoMoverMouse);
        document.addEventListener('mouseup', aoSoltarMouse);
    });
}
