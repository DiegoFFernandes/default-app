<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Binário do Chrome / Edge
    |--------------------------------------------------------------------------
    | Caminho do executável usado para gerar PDF em modo headless.
    | No Windows Server sem Chrome, aponte para o Edge:
    | C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe
    */
    'binary' => env('CHROME_BINARY', 'C:\Program Files\Google\Chrome\Application\chrome.exe'),

    /*
    |--------------------------------------------------------------------------
    | Timeout (segundos)
    |--------------------------------------------------------------------------
    | Tempo máximo de execução do processo do Chrome.
    */
    'timeout' => (int) env('CHROME_PDF_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Virtual time budget (ms)
    |--------------------------------------------------------------------------
    | Quanto o Chrome espera por CSS/imagens/JS antes de imprimir. Como o layout
    | busca os assets via HTTP (asset()), precisa de folga para a rede.
    */
    'virtual_time_budget' => (int) env('CHROME_PDF_VIRTUAL_TIME_BUDGET', 10000),

    /*
    |--------------------------------------------------------------------------
    | Ignorar erros de certificado
    |--------------------------------------------------------------------------
    | O APP_URL é HTTPS; em ambiente local/dev o certificado pode não validar
    | quando o servidor busca os próprios assets. Deixe true nesses casos.
    */
    'ignore_certificate_errors' => (bool) env('CHROME_PDF_IGNORE_CERT_ERRORS', true),

];
