/* =====================================================================
   Itens próprios de Compras (COMPRA_ITEM) + parâmetro de fonte de itens
   Executar manualmente no Firebird (produção e teste).
   Charset das colunas texto: ISO8859_1, como as demais tabelas COMPRA_*.

   ATENÇÃO — se você pré-carregar COMPRA_SUBGRUPO ou COMPRA_ITEM com CDs
   explícitos (INSERT com CD fixo), sincronize o gerador logo depois, senão
   os primeiros cadastros pela tela colidem na PK. Exemplo:
       SET GENERATOR GEN_COMPRA_SUBGRUPO TO (SELECT MAX(CD_SUBGRUPO) FROM COMPRA_SUBGRUPO);
       SET GENERATOR GEN_COMPRA_ITEM     TO (SELECT MAX(CD_ITEM)     FROM COMPRA_ITEM);
   (Se sua versão do Firebird não aceitar subselect no SET GENERATOR, use o
    valor numérico do MAX diretamente, ex.: SET GENERATOR GEN_COMPRA_SUBGRUPO TO 15;)
   ===================================================================== */

/* ---------------------------------------------------------------------
   1) Subgrupos dos itens próprios (criado antes de COMPRA_ITEM por causa da FK)
   --------------------------------------------------------------------- */
CREATE TABLE COMPRA_SUBGRUPO (
    CD_SUBGRUPO  INTEGER      NOT NULL,
    DS_SUBGRUPO  VARCHAR(100) NOT NULL,
    DT_REGISTRO  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_COMPRA_SUBGRUPO PRIMARY KEY (CD_SUBGRUPO)
);

CREATE GENERATOR GEN_COMPRA_SUBGRUPO;

/* ---------------------------------------------------------------------
   2) Catálogo de itens próprios
   --------------------------------------------------------------------- */
CREATE TABLE COMPRA_ITEM (
    CD_ITEM            INTEGER      NOT NULL,
    DS_ITEM            VARCHAR(200) NOT NULL,
    SG_UNIDMED         VARCHAR(10),
    CD_SUBGRUPO_COMPRA INTEGER,
    ST_ATIVO           CHAR(1)      DEFAULT 'S' NOT NULL,
    CD_USUARIO         INTEGER,
    DT_REGISTRO        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_COMPRA_ITEM PRIMARY KEY (CD_ITEM),
    CONSTRAINT CK_CI_ATIVO CHECK (ST_ATIVO IN ('S','N')),
    CONSTRAINT FK_CI_SUBGRUPO FOREIGN KEY (CD_SUBGRUPO_COMPRA)
        REFERENCES COMPRA_SUBGRUPO (CD_SUBGRUPO)
);

CREATE GENERATOR GEN_COMPRA_ITEM;

/* ---------------------------------------------------------------------
   3) Parâmetro global da fonte de itens (linha única, ID = 1)
      ST_FONTE_ITEM: 'J' = itens Junsoft (tabela ITEM)
                     'P' = itens próprios (tabela COMPRA_ITEM)
   --------------------------------------------------------------------- */
CREATE TABLE COMPRA_PARAM (
    ID             INTEGER DEFAULT 1 NOT NULL,
    ST_FONTE_ITEM  CHAR(1) DEFAULT 'J' NOT NULL,
    CONSTRAINT PK_COMPRA_PARAM PRIMARY KEY (ID),
    CONSTRAINT CK_CP_FONTE CHECK (ST_FONTE_ITEM IN ('J','P'))
);

INSERT INTO COMPRA_PARAM (ID, ST_FONTE_ITEM) VALUES (1, 'J');

/* ---------------------------------------------------------------------
   4) Denormaliza a descrição do item na linha da solicitação.
      Necessário porque o item pode vir de ITEM ou de COMPRA_ITEM,
      cujos CD_ITEM são independentes (geradores distintos).
   --------------------------------------------------------------------- */
ALTER TABLE COMPRA_SOL_ITEM ADD DS_ITEM VARCHAR(200);

/* Backfill dos itens já existentes (todos vindos do Junsoft até aqui) */
UPDATE COMPRA_SOL_ITEM
   SET DS_ITEM = (
        SELECT IT.DS_ITEM FROM ITEM IT
        WHERE IT.CD_ITEM = COMPRA_SOL_ITEM.CD_ITEM
   )
 WHERE DS_ITEM IS NULL;
