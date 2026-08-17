<?php

namespace App\Services\Disparos;

use RuntimeException;

class DisparoHandlerRegistry
{
    private const MAP = [
        'NOTA_BOLETO'     => NotaBoletoHandler::class,
        'NOTA_BOLETO_WPP' => NotaBoletoWppHandler::class,
    ];

    public function resolve(string $cdHandler): DisparoHandlerInterface
    {
        if (!isset(self::MAP[$cdHandler])) {
            throw new RuntimeException("Handler de disparo não encontrado: {$cdHandler}");
        }

        return app(self::MAP[$cdHandler]);
    }
}
