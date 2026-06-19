@if (!$canEdit)
    <button type="button" class="btn btn-primary btn-xs btn-sm-phone mr-1" id="btn-baixar-todos" title="Baixar carcaças selecionadas">
        <i class="fas fa-sign-out-alt mr-1"></i> Baixar Todos
    </button>
    <button type="button" class="btn btn-info btn-xs btn-sm-phone mr-1" id="btn-transferir-todos" title="Transferir para outro local">
        <i class="fas fa-exchange-alt mr-1"></i> Transferir Local
    </button>
@endif
@if (auth()->user()->hasPermissionTo('ver-adicionar-pedido-carcaca'))
    <button type="button" class="btn btn-success btn-xs btn-sm-phone" id="btn-criar-pedido" title="Criar pedido com carcaças selecionadas">
        <i class="fas fa-plus mr-1"></i> Criar Pedido
    </button>
@endif
