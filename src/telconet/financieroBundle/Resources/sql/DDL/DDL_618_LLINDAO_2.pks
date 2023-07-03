-- creación de indice para consumo de procesos contables
create index DB_FINANCIERO.INF_IDX_DOCFIN012 on DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB (FE_EMISION, OFICINA_ID, TIPO_DOCUMENTO_ID, ESTADO_IMPRESION_FACT)
  tablespace DB_TELCONET
  pctfree 10
  initrans 2
  maxtrans 255
  storage
  (
    initial 64K
    next 1M
    minextents 1
    maxextents unlimited
  );
/

