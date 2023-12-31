/**
 * PKS para insertar parametros para uso del TMO
 *
 * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
 * @version 1.0 10-11-2021
 */

--ID DEL PRODUCTO SAFECITYDATOS
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'ID DEL PRODUCTO SAFECITYDATOS',
    'ID_PRODUCTO_SAFECITYDATOS',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE  NOMBRE_TECNICO = 'SAFECITYDATOS'),
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

--ID DEL PRODUCTO SAFECITYSWPOE
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'ID DEL PRODUCTO SAFECITYSWPOE',
    'ID_PRODUCTO_SAFECITYSWPOE',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE  NOMBRE_TECNICO = 'SAFECITYSWPOE'),
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

--ID DEL PRODUCTO SAFECITYWIFI
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'ID DEL PRODUCTO SAFECITYWIFI',
    'ID_PRODUCTO_SAFECITYWIFI',
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE  NOMBRE_TECNICO = 'SAFECITYWIFI'),
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

-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_PRODUCTOS_FTTX
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'2', 						-- CODIGO_TAREA 
'INSTALACION_PRODUCTOS_FTTX', 			-- NOMBRE_TAREA 
'Tareas de Instalación Telconet de productos FTTX', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'jnazareno', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FORMULARIO_EPP'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '1', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'SEGUIMIENTO'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '2', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FOTO'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '3', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'INGRESO_MATERIALES'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '4', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '30', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ACTIVACION_SERVICIO'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '5', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ACTAS'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '6', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ENCUESTAS'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '7', 
    '10'
);

--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE INSTALACION_PRODUCTOS_FTTX
--
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FINALIZAR'), 
    '2', 
    'Activo', 
    'jnazareno', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '8', 
    '10'
);

-- INSERT DE LOS EQUIPOS QUE NO REQUIEREN MAC PARA REALIZAR UNA   
  -- INSTALACION O CAMBIO DE EQUIPO
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
      1563,
      'EQUIPO PERMITIDO QUE NO REQUIERE MAC PARA REALIZAR UNA INSTALACION O CAMBIO DE EQUIPO',
      'CAMARA IP',
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

COMMIT ;

/
