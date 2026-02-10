@if (!$canEdit)
    <button type="button" class="btn btn-secondary btn-xs" style="width: 100px;" id="btn-baixar-todos">
        Baixar Todos
    </button>
    <button type="button" class="btn btn-secondary btn-xs" style="width: 100px;" id="btn-transferir-todos">
        Transferir Local
    </button>
@endif
@if (!auth()->user()->hasRole('vendedor|supervisor'))
    <button type="button" class="btn btn-secondary btn-xs" style="width: 100px;" id="btn-criar-pedido">
        Criar Pedido
    </button>
@endif
