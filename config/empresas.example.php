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

    /*
    |--------------------------------------------------------------------------
    | Prefeitura por empresa (NFS-e)
    |--------------------------------------------------------------------------
    | Cada empresa emite NFS-e por uma prefeitura diferente. Alimenta o
    | cabeçalho do layout da nota (admin.layouts.layout-nota-atz) via
    | NotaLayoutData. Empresa nova = uma entrada aqui, sem código.
    |
    | Campos:
    |   nome       -> título do cabeçalho (ex: 'MUNICÍPIO DE EXEMPLO')
    |   secretaria -> subtítulo
    |   logo       -> caminho do brasão relativo a public/ (usado com asset())
    |   decreto    -> texto legal em "Outras Informações" (varia por município!)
    |
    | Empresas não mapeadas caem em 'municipio_padrao'.
    */
    'municipios' => [

        1 => [
            'nome'       => 'MUNICÍPIO DE EXEMPLO',
            'secretaria' => 'SECRETARIA MUNICIPAL DE FAZENDA',
            'logo'       => 'img/municipios/exemplo.png',
            'decreto'    => '',
            'url_consulta_publica' => 'https://www.nfse.gov.br/ConsultaPublica/',
        ],

    ],

    // Fallback para empresa sem mapeamento: brasão genérico, sem dados fiscais
    // de outra cidade.
    'municipio_padrao' => [
        'nome'       => '',
        'secretaria' => 'SECRETARIA MUNICIPAL DE FAZENDA',
        'logo'       => 'img/municipio.png',
        'decreto'    => '',
        'url_consulta_publica' => 'https://www.nfse.gov.br/ConsultaPublica/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout (Blade) da NFS-e por empresa
    |--------------------------------------------------------------------------
    | Cada prefeitura tem um desenho de NFS-e diferente. Os dados vêm sempre do
    | mesmo NotaLayoutData; só a VIEW muda. Empresas não mapeadas
    | usam 'layout_padrao'.
    */
    'layouts' => [
        // 2 => 'admin.layouts.layout-nota-atz-emp-3',
    ],

    'layout_padrao' => 'admin.layouts.layout-nota-atz',

];
