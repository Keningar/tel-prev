/**
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 26-02-2018 Se insertan las caracteristicas para el ciclo de facturacion.
  */
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CICLO_FACTURADO_MES',
    'N',
    'Activo',
    sysdate,
    'regulaCiclo_md',
    sysdate,
    'regulaCiclo_md',
    'COMERCIAL'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CICLO_FACTURADO_ANIO',
    'N',
    'Activo',
    sysdate,
    'regulaCiclo_md',
    sysdate,
    'regulaCiclo_md',
    'COMERCIAL'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FACTURA_REUBICA_TRASLADO',
    'N',
    'Activo',
    sysdate,
    'regulaCiclo_md',
    sysdate,
    'regulaCiclo_md',
    'COMERCIAL'
  );
COMMIT;
/**
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 26-02-2018 Actualiza el campo corteMasivo y es pago para contrato para que no se visualicen las dos formas de pago en la pantalla de
  *                         corte, de notificacion y cambio de ciclo.
  */
UPDATE DB_GENERAL.ADMI_FORMA_PAGO
SET CORTE_MASIVO = 'N', ES_PAGO_PARA_CONTRATO = 'N'
WHERE CODIGO_FORMA_PAGO='TRGM';

UPDATE DB_GENERAL.ADMI_FORMA_PAGO
SET CORTE_MASIVO = 'N', ES_PAGO_PARA_CONTRATO = 'N'
WHERE CODIGO_FORMA_PAGO='DPGM';

COMMIT;