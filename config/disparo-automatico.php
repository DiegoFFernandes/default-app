<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Carência antes do disparo automático (e-mail e WhatsApp)
    |--------------------------------------------------------------------------
    |
    | Uma nota só entra na fila de disparo (gerarPendentes) depois de já ter
    | essa quantidade de minutos de idade - dá tempo hábil de cancelar a nota
    | antes do envio automático chegar no cliente. Sem isso, uma nota emitida
    | pouco antes de uma execução poderia ser enviada em poucos minutos, sem
    | chance de correção.
    |
    | Usado em NotaBoletoHandler::gerarPendentes(), NotaBoletoWppHandler::
    | gerarPendentes() (limite superior da consulta) e em DisparoContexto::
    | marcarExecutado()/marcarUltimaExecucao() (a marca d'água avança só até
    | "agora - carência", não até "agora" - senão uma nota mais nova que a
    | carência ficaria pra trás da marca d'água e nunca mais seria reconsiderada).
    |
    */
    'carencia_minutos' => 60,

];
