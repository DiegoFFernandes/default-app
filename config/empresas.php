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
        1 => 'Cambe',
        2 => '2',
        3 => 'Osvaldo Cruz',
        4 => '4',
        5 => 'Ponta Grossa',
        6 => 'Catanduva',
    ],

    /*
    |--------------------------------------------------------------------------
    | Empresas visíveis para o administrador (empresa == 0)
    |--------------------------------------------------------------------------
    | IDs que aparecem quando nenhuma empresa é selecionada (perfil admin).
    |
    */
    'admin_ids' => [1, 3, 5, 6],

    /*
    |--------------------------------------------------------------------------
    | Apelido padrão para empresas não mapeadas
    |--------------------------------------------------------------------------
    */
    'apelido_padrao' => 'OUTROS',

];
