<aside class="control-sidebar control-sidebar-{{ config('adminlte.right_sidebar_theme') }}">
    <div class="p-3">
        <h5>Configurações</h5>
        <hr class="mb-2">
        <div class="mb-4"><input id="ativarNotificacoesCheckbox" type="checkbox" value="1" class="mr-1" @if (Auth::user()->notifications == 'S')
            checked
        @endif><span>Ativar Notificações</span></div>
    </div>
</aside>
