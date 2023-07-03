--SCRIP QUE REGISTRA CLIENTES PARA CONTROL DE INFORME EJECUTIVO
SET SERVEROUTPUT ON;
DECLARE

  --QUERY PARA OBTENER TODAS LAS RAZONES SOCIALES.
  CURSOR C_GetRazonSocial
  IS
    SELECT RAZON_SOCIAL || ' ' || NOMBRES || ' ' || APELLIDOS CLIENTE
    , IDENTIFICACION_CLIENTE
      FROM DB_COMERCIAL.INFO_PERSONA
    WHERE IDENTIFICACION_CLIENTE IN ('0992182326001');

  --VARIABLES LOCALES
  Ln_IdServicioRecursoCab  NUMBER;
  Lv_UsrCreacion           VARCHAR2(30) := 'userIE';
  Lv_IpCreacion            VARCHAR2(30) := NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1');

BEGIN

  IF C_GetRazonSocial%ISOPEN THEN
    CLOSE C_GetRazonSocial;
  END IF;
  
  /*
   *************************************************
   *                   PARÁMETRO                   *
   * Razón social para envío de Informe Ejecutivo  *
   *************************************************
  */
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
     'Razón social para envío de Informe Ejecutivo',
     'Razones sociales a las que se enviará automáticamente los IE',
     'SOPORTE',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    COMMIT;
    -- FIN PARAMETRO CAB

  --RECORREMOS TODAS LAS RAZONES SOCIALES.
  FOR RazonSocial IN C_GetRazonSocial LOOP


        --INSERTAMOS LA DETALLE.
        INSERT
        INTO DB_GENERAL.ADMI_PARAMETRO_DET
          (
            ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION,
            EMPRESA_COD,
            OBSERVACION
          )
          VALUES
          (
            DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
           (
              SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'Razón social para envío de Informe Ejecutivo'
            ),
           'Razones sociales a las que se enviará automáticamente los IE',
           RazonSocial.CLIENTE,
           RazonSocial.IDENTIFICACION_CLIENTE,
           'Activo',
           Lv_UsrCreacion,
           SYSDATE,
           Lv_IpCreacion,
           '10',
           'VALOR1: NOMBRE DE CLIENTE, VALOR2: IDENTIFICACION DE CLIENTE'
          );

      COMMIT;

  END LOOP; --FIN CLIENTES

EXCEPTION
  WHEN OTHERS THEN
    ROLLBACK;
    DBMS_OUTPUT.PUT_LINE('Error al registrar clientes para Informe ejecutivo: ');
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('SoporteInformeEjecutivo',
                                         'SoporteInformeEjecutivo',
                                         'Error: ' || SQLCODE || ' - ERROR_STACK:'||
                                            DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: '||
                                            DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                          Lv_UsrCreacion,
                                          SYSDATE,
                                          Lv_IpCreacion);
END;
/

  /*
   ************************************
   *         PARÁMETRO                *
   * Tiempo mínimo de cierre de caso  *
   ************************************
  */
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
     'Tiempo mínimo de cierre de caso',
     'Tiempo mínimo considerado para generar informe ejecutivo automáticamente',
     'SOPORTE',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    COMMIT;

  --INSERTAMOS LA DETALLE.
    INSERT
    INTO DB_GENERAL.ADMI_PARAMETRO_DET
      (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD,
        OBSERVACION
      )
      VALUES
      (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
       (
          SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'Tiempo mínimo de cierre de caso'
        ),
       'Tiempo mínimo (minutos) considerado para generar IE automáticamente',
       '480',
       'Activo',
       'kyrodriguez',
       SYSDATE,
       '127.0.0.1',
       '10',
       'VALOR1: Tiempo en minutos que será leído para el control de envío 
       automático de informe ejecutivo'
      );
     COMMIT;
     /
