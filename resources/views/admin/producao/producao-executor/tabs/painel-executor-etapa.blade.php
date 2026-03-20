@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-exame-inicial-' . $painelPrincipal   ,
    'visible' => 'show active',
    'idTabela' => 'table-exame-inicial-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-raspa-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-raspa-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-prep-banda-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-prep-banda-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-escareacao-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-escareacao-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-limpeza-manchao-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-limpeza-manchao-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-cola-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-cola-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-emborrachamento-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-emborrachamento-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-vulcanizacao-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-vulcanizacao-' . $painelPrincipal,
])

@include('admin.producao.producao-executor.components.tabelas-setores', [
    'painelSub' => 'painel-exame-final-' . $painelPrincipal,
    'visible' => '',
    'idTabela' => 'table-exame-final-' . $painelPrincipal,
])
