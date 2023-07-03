/**
 * Insert de nuevos SCOPES en tabla ADMI_PARAMETRO_DET 
 * @author Roberth Cobeña <rcobena@telconet.ec>
 * @version 1.0
 * @since 16/06/2021
 */
---
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    OBSERVACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,     
    '104',
    'TIPO SCOPE',
    'TIPO_SCOPE', 
    'SCGN', 
    'Dinamico + CGNAT',
    'Activo',
    'rcobena',
     SYSDATE,
    '127.0.0.1',
    'VALOR1: TIPO DE PARAMETRO, VALOR2: ABREVIATURA DEL SCOPE, VALOR3: NOMBRE DEL SCOPE'
  );
   
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    OBSERVACION
  )
VALUES 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,     
    '104',
    'TIPO SCOPE',
    'TIPO_SCOPE', 
    'SFP', 
    'Fijo + CGNAT',
    'Activo',
    'rcobena',
     SYSDATE,
    '127.0.0.1',
    'VALOR1: TIPO DE PARAMETRO, VALOR2: ABREVIATURA DEL SCOPE, VALOR3: NOMBRE DEL SCOPE'
  );  

COMMIT;
/