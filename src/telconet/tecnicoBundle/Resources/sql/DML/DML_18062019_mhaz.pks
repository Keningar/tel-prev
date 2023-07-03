/**
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0
 * @since 17-06-2019    
 * Se realizan los siguientes cambios para el proceso de amortización por cancelación voluntaria:
 * Se crea caracteristicas para equipos DUAL BAND.
 * Se insertan equipos DUAL BAND en parámetro RETIRO_EQUIPOS_SOPORTE.
 * Se realiza Update en el valor6 para replicar los precios de los equipos valor2 en parámetro
 * RETIRO_EQUIPOS_SOPORTE y crear datos parametrizables al usuario.
 * Se modifican valores del parámetros de instalación PROM_PRECIO_INSTALACION.
 * Se crean las sentencias DML para insertar y modificar parámetros (CAB,DET).
 * Se modifica VALOR4 de parámetro PORCENTAJE_DESCUENTO_INSTALACION.
 */

--SE INSERTAN CARACTERISTICAS PARA EQUIPOS DUAL BAND.
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  FE_ULT_MOD,
  USR_ULT_MOD,
  TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'WIFI DUAL BAND',
    'N'     ,
    'Activo',
    SYSDATE ,
    'mhaz'  ,
    NULL    ,
    NULL    ,
    'COMERCIAL'
);
--
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  FE_ULT_MOD,
  USR_ULT_MOD,
  TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EXTENDER DUAL BAND',
    'N'     ,
    'Activo',
    SYSDATE ,
    'mhaz'  ,
    NULL    ,
    NULL    ,
    'COMERCIAL'
);
COMMIT;

-- SE INSERTA PARÁMETROS A TABLA RETIRO_EQUIPOS_SOPORTE.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
      AND    ESTADO           = 'Activo' ),
    'WIFI DUAL BAND',
    'HUAWEI',
    175,
    (SELECT ID_CARACTERISTICA
     FROM   DB_COMERCIAL.ADMI_CARACTERISTICA
     WHERE  DESCRIPCION_CARACTERISTICA = 'WIFI DUAL BAND'
     AND    TIPO_INGRESO               = 'N'
     AND    TIPO                       = 'COMERCIAL'
     AND    ESTADO                     = 'Activo'),
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.0.0.1',
    NULL,
    NULL,
    NULL,
    'S',
    '18',
    175,
    NULL,
    NULL
  );
-- EXTENDER DUAL BAND
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
      AND    ESTADO           = 'Activo' ),
    'EXTENDER DUAL BAND',
    'HUAWEI',
    75,
    (SELECT ID_CARACTERISTICA
     FROM   DB_COMERCIAL.ADMI_CARACTERISTICA
     WHERE  DESCRIPCION_CARACTERISTICA = 'EXTENDER DUAL BAND'
     AND    TIPO_INGRESO               = 'N'
     AND    TIPO                       = 'COMERCIAL'
     AND    ESTADO                     = 'Activo'),
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.0.0.1',
    NULL,
    NULL,
    NULL,
    'S',
    '18',
    75,
    NULL,
    NULL
  );
-- FACTURA DETALLADA
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
  VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND    ESTADO           = 'Activo'),
    'WIFI DUAL BAND',
    (SELECT ID_PRODUCTO
     FROM   DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE  DESCRIPCION_PRODUCTO = 'WiFi Dual Band'
     AND    ESTADO = 'Activo'),
    NULL,
    ( SELECT ID_CARACTERISTICA
      FROM   DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE  DESCRIPCION_CARACTERISTICA = 'WIFI DUAL BAND'
      AND    ESTADO              = 'Activo'
      AND    USR_CREACION        = 'mhaz' ),
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    'N'
  );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
  VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND    ESTADO           = 'Activo'),
    'EXTENDER DUAL BAND',
    ( SELECT ID_PRODUCTO
      FROM   DB_COMERCIAL.ADMI_PRODUCTO 
      WHERE  DESCRIPCION_PRODUCTO = 'Extender Dual Band'
      AND    ESTADO = 'Activo'),
    NULL,
    ( SELECT ID_CARACTERISTICA
      FROM   DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE  DESCRIPCION_CARACTERISTICA = 'EXTENDER DUAL BAND'
      AND    ESTADO              = 'Activo'
      AND    USR_CREACION        = 'mhaz'),
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    'N'
  );
-- UPDATE PARÁMETRO RETIRO_EQUIPOS_SOPORTE.
--DESCRIPCION CABECERA
UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB
SET    DESCRIPCION = 'VALORES A FACTURAR EN EL RETIRO DE EQUIPOS POR SOPORTE. DESCRIPCIÓN: EQUIPO, V1=TECNOLOGÍA, V2=PRECIO, V3=CARACTERISTICA_ID, V4=dependientes, v5= S/N, v6=precios editables presentación en cancelación voluntaria.'
WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
AND    MODULO = 'FINANCIERO'
AND    ESTADO = 'Activo';
--CPE TELLION
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR5       = 'N',
        VALOR6       = 85,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE'
AND     VALOR1       = 'TELLION'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- CPE ONT - TELLION
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 85,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE ONT'
AND     VALOR1       = 'TELLION'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- CPE WIFI
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 40,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE WIFI'
AND     VALOR1       = 'TELLION'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- FUENTE DE PODER - TELLION
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 10,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'FUENTE DE PODER'
AND     VALOR1       = 'TELLION'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
--CPE HUAWEI
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET      VALOR5       = 'N',
        VALOR6       = 125,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE'
AND     VALOR1       = 'HUAWEI'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- CPE ONT - HUAWEI
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 125,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE ONT'
AND     VALOR1       = 'HUAWEI'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- ROSETA
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 10,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'ROSETA'
AND     VALOR1       = 'HUAWEI'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- FUENTE DE PODER - HUAWEI
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 10,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'FUENTE DE PODER'
AND     VALOR1       = 'HUAWEI'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- CPE ADSL - ADSL
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 30,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'CPE ADSL'
AND     VALOR1       = 'ADSL'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- FUENTE DE PODER - ADSL
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 10,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'FUENTE DE PODER'
AND     VALOR1       = 'ADSL'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- FUENTE DE PODER AP CISCO - CISCO
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 90,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'FUENTE DE PODER AP CISCO'
AND     VALOR1       = 'CISCO'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );
-- SMARTWIFI - CISCO
UPDATE  DB_GENERAL.ADMI_PARAMETRO_DET
SET     VALOR6       = 300,
        USR_ULT_MOD  = 'mhaz',
        FE_ULT_MOD   = SYSDATE
WHERE   DESCRIPCION  = 'SMARTWIFI'
AND     VALOR1       = 'CISCO'
AND     PARAMETRO_ID = ( SELECT ID_PARAMETRO
                         FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                         WHERE  NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
                         AND    ESTADO           = 'Activo' );                                                
COMMIT;
-- Se actualiza descripción de "permanencia mimima" a "permanencia mínima"
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET    DESCRIPCION  = 'Tiempo en meses de permanencia mínima del servicio mandatorio Internet',
       USR_ULT_MOD  = 'mhaz',
       FE_ULT_MOD   = SYSDATE
WHERE  VALOR1       = 'PERMANENCIA SERVICIOS ADICIONALES'
AND    PARAMETRO_ID = ( SELECT ID_PARAMETRO
                        FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
                        AND    ESTADO           = 'Activo' );

-- Se Actualiza el valor1 permanencia mínima a permanencia mínima 24 meses.
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET    DESCRIPCION  = 'Tiempo en meses de permanencia mínima del servicio mandatorio Internet',
       VALOR1       = 'PERMANENCIA MINIMA 24 MESES',
       VALOR3       = '30/04/2019',
       USR_ULT_MOD  = 'mhaz',
       FE_ULT_MOD   = SYSDATE
WHERE  VALOR1       = 'PERMANENCIA MINIMA'
AND    PARAMETRO_ID = ( SELECT ID_PARAMETRO
                        FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
                        AND    ESTADO           = 'Activo' );

-- Se Inserta parámetro de permanencia mínima 36 Meses.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
  EMPRESA_COD
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND    ESTADO           = 'Activo' ),
    'Tiempo en meses de permanencia mínima del servicio mandatorio Internet ',
    'PERMANENCIA MINIMA 36 MESES',
    36,
    '01/05/2019',
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
COMMIT;

-- Se modifica parámetros de instalación para proceso de amortización.
-- FO.
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET    VALOR4      = 50          , -- VALOR BASE DE INSTALACIÓN 50% = $50
       DESCRIPCION = 'VALOR DE LA INSTALACION DEPENDIENDO DE LA ULTIMA MILLA DEL SERVICIO',
       USR_ULT_MOD = 'mhaz'      ,
       FE_ULT_MOD  = SYSDATE     ,
       IP_ULT_MOD  = '172.0.0.1',
       OBSERVACION = 'V3= PRECIO DE INST 100% FO; V4= PRECIO DE INST 50% FO'
WHERE  PARAMETRO_ID =(SELECT ID_PARAMETRO
                      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB 
                      WHERE  NOMBRE_PARAMETRO = 'PROM_PRECIO_INSTALACION'
                      AND    ESTADO           =  'Activo')
AND VALOR1         = 'FO'                      
AND VALOR2         = 'INSTALACION HOME';
--CO
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET    VALOR4      = 25          , -- VALOR BASE DE INSTALACIÓN 50% = $25
       DESCRIPCION = 'VALOR DE LA INSTALACION DEPENDIENDO DE LA ULTIMA MILLA DEL SERVICIO',
       USR_ULT_MOD = 'mhaz'      ,
       FE_ULT_MOD  = SYSDATE     ,
       IP_ULT_MOD  = '172.0.0.1',
       OBSERVACION = 'V3= PRECIO DE INST 100% CO; V4= PRECIO DE INST 50% CO'
WHERE  PARAMETRO_ID =(SELECT ID_PARAMETRO
                      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB 
                      WHERE  NOMBRE_PARAMETRO = 'PROM_PRECIO_INSTALACION'
                      AND    ESTADO           =  'Activo')
AND VALOR1         = 'CO'                      
AND VALOR2         = 'INSTALACION HOME';
COMMIT;

-- SE MODIFICA VALOR4 DE PARÁMETRO PORCENTAJE_DESCUENTO_INSTALACION PARA PROCESO DE AMORTIZACIÓN EN CANCELACIÓN VOLUNTARIA.
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 0        ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'EFECTIVO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'AHORRO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4      = 100      ,
       USR_ULT_MOD = 'mhaz'   ,
       FE_ULT_MOD  = SYSDATE  ,
       IP_ULT_MOD  = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1 = 'FO'                       
AND    VALOR2 = 'TARJETA DINERS'
AND    ESTADO = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'TARJETA MASTERCARD'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'TARJETA AMERICAN'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'TARJETA VISA'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'TARJETA DISCOVER'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'TARJETA CUOTAFACIL / ALIAS SOLIDARIO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 100      ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'FO'                       
AND    VALOR2       = 'CORRIENTE'
AND    ESTADO       = 'Activo';
-- COBRE.
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 0        ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'EFECTIVO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 25       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'AHORRO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4      = 50       ,
       USR_ULT_MOD = 'mhaz'   ,
       FE_ULT_MOD  = SYSDATE  ,
       IP_ULT_MOD  = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1 = 'CO'                       
AND    VALOR2 = 'TARJETA DINERS'
AND    ESTADO = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'TARJETA MASTERCARD'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'TARJETA AMERICAN'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'TARJETA VISA'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'TARJETA DISCOVER'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'TARJETA CUOTAFACIL / ALIAS SOLIDARIO'
AND    ESTADO       = 'Activo';
--
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET    VALOR4       = 50       ,
       USR_ULT_MOD  = 'mhaz'   ,
       FE_ULT_MOD   = SYSDATE  ,
       IP_ULT_MOD   = '127.0.0.1',
       OBSERVACION  = 'V4= PRECIO DE INSTALACION SEGÚN FORMA DE PAGO' 
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO 
                       FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE  NOMBRE_PARAMETRO = 'PORCENTAJE_DESCUENTO_INSTALACION'
                       AND    ESTADO           = 'Activo')
AND    VALOR1       = 'CO'                       
AND    VALOR2       = 'CORRIENTE'
AND    ESTADO       = 'Activo';
COMMIT;
/
