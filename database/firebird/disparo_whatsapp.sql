/* =====================================================================
   Disparo Automático via WhatsApp (canal adicional ao e-mail existente)
   Executar manualmente no Firebird de PRODUÇÃO - já aplicado e testado
   na base de teste.

   Depois de rodar este script, ative o contexto pela tela (Disparo
   Automático > Contextos de Disparo > ícone do relógio na linha
   "Envio Nota + Boleto (WhatsApp)") quando estiver pronto para começar -
   ele já nasce INATIVO (ST_ATIVO = 'N') de propósito.
   ===================================================================== */

/* ---------------------------------------------------------------------
   1) Canal do contexto (E-mail x WhatsApp) + parâmetros específicos do
      WhatsApp: limite diário de envios e janela de horário permitida.
   --------------------------------------------------------------------- */
ALTER TABLE DISPARO_CONTEXTO ADD TP_CANAL CHAR(1) DEFAULT 'E' NOT NULL;
ALTER TABLE DISPARO_CONTEXTO ADD NR_LIMITEDIARIO INTEGER;
ALTER TABLE DISPARO_CONTEXTO ADD HR_JANELAINICIO VARCHAR(5);
ALTER TABLE DISPARO_CONTEXTO ADD HR_JANELAFIM VARCHAR(5);

/* ---------------------------------------------------------------------
   2) Telefone de destino do envio (equivalente a DS_EMAILDEST, mas para
      o canal WhatsApp).
   --------------------------------------------------------------------- */
ALTER TABLE DISPARO_ENVIO ADD DS_TELEFONE VARCHAR(20);

/* ---------------------------------------------------------------------
   3) Novo status ST_ENVIO = 'L' (Limite Atingido): pendente que nasceu
      fora da fila ativa porque NR_LIMITEDIARIO já estava cheio no
      momento da criação. Fica visível na tela com o motivo, e só volta
      para 'A' se o usuário clicar em "Reenviar" manualmente (ação que
      ignora o limite de propósito). A constraint precisa ser recriada
      (Firebird não permite ALTER direto em CHECK).
   --------------------------------------------------------------------- */
ALTER TABLE DISPARO_ENVIO DROP CONSTRAINT CK_DISPARO_ENVIO_STATUS;
ALTER TABLE DISPARO_ENVIO ADD CONSTRAINT CK_DISPARO_ENVIO_STATUS CHECK (ST_ENVIO IN ('A', 'E', 'F', 'V', 'L'));

/* ---------------------------------------------------------------------
   4) Contexto do canal WhatsApp (handler NOTA_BOLETO_WPP). Nasce
      INATIVO - ative pela tela quando quiser começar a enviar.
      Valores de NR_LIMITEDIARIO/HR_JANELA* abaixo são só o ponto de
      partida (aquecimento do número) - ajustáveis depois pela tela.
   --------------------------------------------------------------------- */
INSERT INTO DISPARO_CONTEXTO (
    CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, HR_EXECUCAO, NR_TENTATIVAS, ST_ATIVO, DT_INICIOENVIO, DT_CRIACAO,
    TP_CANAL, NR_LIMITEDIARIO, HR_JANELAINICIO, HR_JANELAFIM
) VALUES (
    GEN_ID(GEN_DISPARO_CONTEXTO, 1), 'Envio Nota + Boleto (WhatsApp)', 'NOTA_BOLETO_WPP', '00:00:00', 2, 'N', CURRENT_DATE, CURRENT_TIMESTAMP,
    'W', 25, '08:00', '20:00'
);

/* ---------------------------------------------------------------------
   5) Documenta os status possíveis de ST_ENVIO na própria coluna (visível
      no IBExpert/isql ao inspecionar a tabela) - 'P' não é listado porque
      nunca é gravado de fato: é um status virtual (COALESCE(ST_ENVIO,'P'))
      usado só na tela, quando a nota ainda nem tem linha em DISPARO_ENVIO.
   --------------------------------------------------------------------- */
COMMENT ON COLUMN DISPARO_ENVIO.ST_ENVIO IS 'Status do envio: A=Aguardando (na fila, ainda nao enviado) | E=Enviado (sucesso total) | V=Enviado com ressalva (falha parcial - pelo menos um destino/anexo recebeu) | F=Falha definitiva (sem destino valido cadastrado ou tentativas esgotadas) | L=Limite Atingido (WhatsApp: fila cheia no momento da criacao - so volta a A via reenvio manual)';
