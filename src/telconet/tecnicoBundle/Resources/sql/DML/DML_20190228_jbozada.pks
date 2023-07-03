--=======================================================================
--   Se crea caracteristica "TRASLADAR EXTENDER DUAL BAND" utilizada en
--   el proceso de traslado del equipo extender dual band.
--=======================================================================

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
    'TRASLADAR EXTENDER DUAL BAND',
    'T',
    'Activo',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'TECNICA'
  );
--=======================================================================
--      Se asocia el producto INTERNET con la caracteristica de 
--      traslado de extender dual band.
--=======================================================================
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE EMPRESA_COD='18' AND NOMBRE_TECNICO='INTERNET' AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'TRASLADAR EXTENDER DUAL BAND'),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
    'Activo',
    'NO'
  );

commit;
/