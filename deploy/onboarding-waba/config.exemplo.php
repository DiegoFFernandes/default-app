<?php

/**
 * Copie este arquivo para config.php no servidor e preencha os valores.
 * config.php NAO vai para o git - ele guarda o app secret.
 */
return [
    // Configuracoes do app -> Basico
    'app_id'     => '',
    'app_secret' => '',

    // Facebook Login for Business -> Configuracoes -> ID da configuracao
    'config_id'  => '',

    'graph_version' => 'v26.0',

    // Trava simples de acesso: so quem tem a senha abre a pagina.
    // Esta pagina executa acoes privilegiadas com o app secret, entao nao
    // pode ficar aberta na internet.
    'senha' => '',
];
