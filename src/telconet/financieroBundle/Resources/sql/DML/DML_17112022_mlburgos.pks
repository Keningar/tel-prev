/** 
 * @author Leonela Burgos <mlburgos@telconet.ec>
 * @version 1.0 
 * @since 17-11-2022
 * Se crea DML de configuraciones del Proyecto Tarjetas ABU
 */

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
    EMPRESA_COD
  )
VALUES 
(   db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'PARAMETROS_WEBSERVICES_TAREA',
    'http://telcos-ws-lb.telconet.ec/rs/comercial/ws/rest/ejecutar',
    'crearTareaCasoSoporte',
    'application/json',
    'UTF-8',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1',
    '18'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
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
(    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ),
    'PROCESO',
    'PROCESO',
    'TARJETA ABU',
    'Activo',
    'mlburgos',
    SYSDATE,
    '127.0.0.1',
    '18'
);

UPDATE 
DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR3 = 'INT' 
WHERE 
    VALOR1='ORIGEN_TAREA' AND 
    DESCRIPCION ='TAREA_CIERRE_ABU'  AND 
    PARAMETRO_ID = (SELECT
                      ID_PARAMETRO
                    FROM
                      DB_GENERAL.ADMI_PARAMETRO_CAB
                    WHERE
                    NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU');
                      
UPDATE 
DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR3 = 'S' 
WHERE 
    VALOR1='GENERA_TAREA' AND 
    DESCRIPCION ='TAREA_CIERRE_ABU'  AND 
    PARAMETRO_ID = (SELECT
                      ID_PARAMETRO
                    FROM
                      DB_GENERAL.ADMI_PARAMETRO_CAB
                    WHERE
                      NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'); 


COMMIT;
