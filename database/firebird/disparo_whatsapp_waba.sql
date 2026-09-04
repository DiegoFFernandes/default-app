/* =====================================================================
   Disparo Automático via WhatsApp: migração do canal WppConnect (não
   oficial) para a API Oficial (WABA) no contexto de Nota + Boleto.
   Executar manualmente no Firebird de PRODUÇÃO depois que o script
   disparo_whatsapp.sql já tiver sido aplicado (ele criou o contexto
   original com CD_HANDLER = 'NOTA_BOLETO_WPP' / TP_CANAL = 'W').

   Depois de rodar este script o contexto continua INATIVO (ST_ATIVO
   não é alterado aqui) - ative pela tela quando estiver pronto.
   ===================================================================== */

/* ---------------------------------------------------------------------
   1) Troca o handler e o canal do contexto de Nota + Boleto: de
      WppConnect ('W') para API Oficial ('O'). Usa CD_HANDLER como
      filtro (em vez de CD_CONTEXTO, que varia por instalação) para o
      script funcionar igual em qualquer base.
   --------------------------------------------------------------------- */
UPDATE DISPARO_CONTEXTO
   SET CD_HANDLER = 'NOTA_BOLETO_WABA',
       TP_CANAL = 'O',
       DS_CONTEXTO = 'Envio Nota + Boleto (WhatsApp Oficial)'
 WHERE CD_HANDLER = 'NOTA_BOLETO_WPP';
