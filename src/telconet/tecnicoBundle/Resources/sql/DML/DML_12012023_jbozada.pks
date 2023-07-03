--Bloque anónimo para crear un nuevo parámetro para validación y uso de sleep
SET SERVEROUTPUT ON
DECLARE
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'TIEMPO ESPERA PROCESOS',
    'Parámetros para recuperar tiempo de espera de procesos que lo necesiten',
    'TECNICO',
    NULL,
    'Activo',
    'jbozada',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='TIEMPO ESPERA PROCESOS'
     AND estado            ='Activo'
    ),
    'OBTENER POTENCIA',
    '11',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
