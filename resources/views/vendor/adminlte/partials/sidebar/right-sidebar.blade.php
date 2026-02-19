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
    </div>
</aside>
