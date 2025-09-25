<div class="menu-flutuante">
    <button class="botao-flutuante botao-principal" id="botao-principal">
        <i class="fas fa-plus"></i>
    </button>
    <div class="opcoes-flutuantes" id="opcoes-flutuantes">
        <button class="botao-flutuante botao-topo" id="botao-topo">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button class="botao-flutuante botao-recarregar" id="botao-recarregar">
            <i class="fas fa-sync"></i>
        </button>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botaoPrincipal = document.getElementById("botao-principal");
            const opcoesFlutuantes = document.getElementById("opcoes-flutuantes");
            const botaoTopo = document.getElementById("botao-topo");
            const botaoRecarregar = document.getElementById("botao-recarregar");

            let aberto = false;

            // evento para mudar o icone do botão principal
            botaoPrincipal.addEventListener("click", function() {
                aberto = !aberto;

                if (aberto) {
                    opcoesFlutuantes.classList.add("aberto");
                    botaoPrincipal.innerHTML = '<i class="fas fa-times"></i>';
                } else {
                    opcoesFlutuantes.classList.remove("aberto");
                    botaoPrincipal.innerHTML = '<i class="fas fa-plus"></i>';
                }
            });

            // volta para o topo
            botaoTopo.addEventListener("click", function() {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
                
                // fecha o menu
                aberto = false;
                opcoesFlutuantes.classList.remove("aberto");
                botaoPrincipal.innerHTML = '<i class="fas fa-plus"></i>';
            });

            // recarrega a pagina
            botaoRecarregar.addEventListener("click", function() {
                location.reload();
            });
        });
    </script>
@endpush
