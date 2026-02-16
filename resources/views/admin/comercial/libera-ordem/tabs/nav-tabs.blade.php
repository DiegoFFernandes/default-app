<div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-inserir" data-toggle="tab" href="#pedidos-bloqueados"
                role="tab">Pedidos</a>
        </li>
        @role('admin')
            <li class="nav-item">
                <a class="nav-link" id="tab-substituir-comissao" data-toggle="tab" href="#substituir-comissao"
                    role="tab">Substituir Comissão</a>
            </li>
        @endrole
        </ul>
    </div>
