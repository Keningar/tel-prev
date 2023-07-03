/**
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.0
 * @since 27-09-2021    
 * Se crea DML para insertar configuración de parámetros 
 * de codigos de plantillas que tienen caracteres especiales
 */

SET SERVEROUTPUT ON

DECLARE

  LN_ID_PARAMETRO DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE;
  
BEGIN

  LN_ID_PARAMETRO := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
  
  -- INSERT DEL PARAMETRO DE CABECERA
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
      LN_ID_PARAMETRO,
      'PLANTILLAS_CON_CARACTERES_ESPECIALES',
      'PLANTILLAS CON CARACTERES ESPECIALES',
      'TECNICO',
      'ENVIO DE CORREO',
      'Activo',
      'jnazareno',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL
    );
    

  -- INSERT EN DETALLE
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
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      LN_ID_PARAMETRO,
      'PLANTILLAS CON CARACTERES ESPECIALES',
      'NOTI_GEN_TELEFO',
      NULL,
      NULL,
      NULL,
      'Activo',
      'jnazareno',
      SYSDATE,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );
    
  COMMIT;
  DBMS_OUTPUT.PUT_LINE('EXITO');
  DBMS_OUTPUT.PUT_LINE('ID_PARAMETRO: ' || LN_ID_PARAMETRO);
  
EXCEPTION
WHEN OTHERS THEN

  ROLLBACK;
  DBMS_OUTPUT.PUT_LINE('ERROR: '|| SQLERRM);
  
END;
/