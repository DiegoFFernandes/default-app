<aside class="control-sidebar control-sidebar-{{ config('adminlte.right_sidebar_theme') }}">
    <div class="p-3">
        <h5>Customizações</h5>
        <hr class="mb-2">
        <div class="mb-4">
            <h6>Notificações</h6>
            <input id="ativarNotificacoesCheckbox" type="checkbox" value="1" class="mr-1"
                @if (Auth::user()->notifications == 'S') checked @endif>
            <span>Ativar Notificações</span>            
        </div>
        <div class="mb-4">
            <h6>Menu Lateral</h6>
            <input id="CollapsedSidebarCheckbox" type="checkbox" value="1" class="mr-1" checked>
            <span>Collapsed</span>
        </div>
        @role('admin')
        <div class="mb-4">
            <h6>Manutenção</h6>
            <button id="btn-limpar-cache" type="button" class="btn btn-outline-secondary btn-sm btn-block">
                <i class="fas fa-broom mr-1"></i> Limpar Cache
            </button>
            <small class="text-muted d-block mt-1">Config, rotas e views.</small>
        </div>
        @endrole
    </div>
</aside>
