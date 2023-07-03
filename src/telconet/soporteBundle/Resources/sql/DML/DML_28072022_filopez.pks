/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear parametro de límite de tareas permitidas consultar
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 28-07-2022 - Versión Inicial.
 */

--####################################################################################
--CREAR CABECERA DE NUEVO PARAMETRO
--####################################################################################
--

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'LIMITE_CONSULTA_TAREAS',
    'LIMITE DE TAREAS PERMITIDAS CONSULTAR EN SOPORTE TAREAS',
    'SOPORTE',
    'Activo',
    'filopez',
     SYSDATE,
    '127.0.0.1'
  );

--
--####################################################################################
--CREAR DETALLE DE NUEVO PARAMETRO
--####################################################################################
--
 
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'LIMITE_CONSULTA_TAREAS' AND ESTADO      = 'Activo'),
     'LIMITE CONSULTA TAREAS SOPORTE',
     '1000',
    'Activo',
    'filopez',
     SYSDATE,
    '127.0.0.1'
  );

  commit;

  /