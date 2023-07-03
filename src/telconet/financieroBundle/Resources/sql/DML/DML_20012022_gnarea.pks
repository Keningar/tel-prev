BEGIN

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
     ID_PARAMETRO_DET,
     PARAMETRO_ID,
     DESCRIPCION,
     VALOR1,
     VALOR2,
     VALOR3,
     VALOR4,
     ESTADO,
     USR_CREACION,
     FE_CREACION,
     IP_CREACION,
     USR_ULT_MOD,
     FE_ULT_MOD,
     IP_ULT_MOD,
     EMPRESA_COD
)
VALUES
(
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
       SELECT ID_PARAMETRO
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES'
     ),
     'TAG NOMBRE COMERCIAL',
     'nombreComercial',
     null,
     null,
     null,
     'Activo',
     'gnarea',
     SYSDATE,
     '127.0.0.1',
     null,
     null,
     null,
     '10'
);
COMMIT;
END;