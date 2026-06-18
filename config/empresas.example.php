<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Apelidos das Empresas
    |--------------------------------------------------------------------------
    | Mapeie o CD_EMPRESA para o apelido que o usuário verá na tela.
    | Empresas não listadas aqui exibem o valor de 'apelido_padrao'.
    |
    */
    'apelidos' => [
        1 => 'Empresa 1',
        2 => 'Empresa 2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Empresas visíveis para o administrador (empresa == 0)
    |--------------------------------------------------------------------------
    | IDs que aparecem quando nenhuma empresa é selecionada (perfil admin).
    |
    */
    'admin_ids' => [1],

    /*
    |--------------------------------------------------------------------------
    | Apelido padrão para empresas não mapeadas
    |--------------------------------------------------------------------------
    */
    'apelido_padrao' => 'OUTROS',

];
