@props(['modalId'])

<button id="btnTopoModal" class="btn btn-danger btnTopoModal">
    <i class="fas fa-arrow-up"></i>
</button>


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalId = @json($modalId) ?? '';
            const $modal = $('#' + modalId);            
            const $btn = $('#btnTopoModal');

            $modal.on('scroll', function() {
                if ($modal.scrollTop() > 300) {                    
                    $btn.show();
                } else {
                    $btn.hide();
                }
            });

            // Ao clicar no botão, rola o modal para o topo
            $btn.on('click', function() {
                $modal.animate({
                    scrollTop: 0
                }, 300);
            });
        });
    </script>
@endpush
