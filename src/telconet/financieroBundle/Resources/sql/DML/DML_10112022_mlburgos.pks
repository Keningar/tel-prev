/** 
 * @author Leonela Burgos <mlburgos@telconet.ec>
 * @version 1.0 
 * @since 10-11-2022
 * Se crea DML de configuraciones del Proyecto Tarjetas ABU
 */


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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'KEY ENCRYPT DECRYPT',
    'SECRET_KEY_ENCRYPT_DECRYPT',
    'c69555ab183de6672b1ebf6100bbed59186a5d72',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
); 

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'RELLENO DE IDENTIFICACION',
    'RELLENO_IDENTIFICACION',
    '0',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
); 

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'LADO RELLENO DE IDENTIFICACION',
    'LADO_RELLENO_IDENTIFICACION',
    'LTRIM',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
);

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'RELLENO DE NUMERO TARJETA ANTIGUO',
    'RELLENO_NUMERO_TARJETA_ANTIGUO',
    '0',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1',
    18
); 

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'LADO RELLENO DE NUMERO TARJETA ANTIGUO',
    'LADO_RELLENO_NUMERO_TARJETA_ANTIGUO',
    'LTRIM',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
);  

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'RELLENO DE NUMERO TARJETA NUEVO',
    'RELLENO_NUMERO_TARJETA_NUEVO',
    '0',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
); 

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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'LADO RELLENO DE NUMERO TARJETA NUEVO',
    'LADO_RELLENO_NUMERO_TARJETA_NUEVO',
    'LTRIM',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
);  


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
    EMPRESA_COD
  )
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'USUARIO CREACION',
    'USUARIO_CREACION',
    'Abu-telcos',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1', 
    18
);   

COMMIT;