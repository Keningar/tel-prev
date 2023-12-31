-- INGRESANDO PROGRESOS DE TAREA DE NETLIFECAM
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
'-5', 						-- CODIGO_TAREA 
'INSTALACION_NETLIFECAM', 	-- NOMBRE_TAREA 
'Tarea de Instalación de Producto Netlifecam', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);




-- PROGRESO DE ACTA EPP PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'5',--PORCENTAJE 
		'2',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'1',-- ORDEN 
		'18'-- ID EMPRESA 
);

-- PROGRESO DE SEGUIMIENTOS PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'5',--PORCENTAJE 
		'1',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'2',-- ORDEN 
		'18'-- ID EMPRESA 
);


-- PROGRESO DE FOTOS PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'10',--PORCENTAJE 
		'10',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'3',-- ORDEN 
		'18'-- ID EMPRESA 
);


-- PROGRESO DE INGRESOS DE EQUIPOS PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'10',--PORCENTAJE 
		'21',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'4',-- ORDEN 
		'18'-- ID EMPRESA 
);



-- PROGRESO DE ACTIVACION PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'40',--PORCENTAJE 
		'6',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'5',-- ORDEN 
		'18'-- ID EMPRESA 
);


-- PROGRESO DE ACTA PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'10',--PORCENTAJE 
		'7',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'6',-- ORDEN 
		'18'-- ID EMPRESA 
);


-- PROGRESO DE ENCUESTA PARA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'10',--PORCENTAJE 
		'8',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'7',-- ORDEN 
		'18'-- ID EMPRESA 
);


-- PROGRESO DE FINALIZAR  PARA TAREA INSTALACION_NETLIFECAM
Insert 
into DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
	(
		ID_PROGRESO_PORCENTAJE,
		PORCENTAJE,
		TIPO_PROGRESO_ID,
		TAREA_ID,
		ESTADO,
		USR_CREACION,
		FE_CREACION,
		IP_CREACION,
		USR_ULT_MOD,
		FE_ULT_MOD,
		ORDEN,
		EMPRESA_ID
	) 
	values 	
	(
		DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL,
		'10',--PORCENTAJE 
		'9',-- ID TIPO PROGRESO 
		'-5',-- ID TAREA 
		'Activo',-- ESTADO 
		'rmoranc',-- USR CREACION 
		SYSDATE,-- FECHA CREACION
		'127.0.0.1',-- IP CREACION 
		NULL,-- USR MODIFICACION
		NULL,-- FECHA MODIFICACION
		'8',-- ORDEN 
		'18'-- ID EMPRESA 
);





------------------------------------------------------------------------

--Id del producto de NETLIFECAM - Servicio Básico de Visualización Remota Residencial
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
    'Id del producto de NETLIFECAM Megadatos',
    'ID_PRODUCTO_NETLIFECAM_MD',
    '78',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
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




-- Parámetros necesarios para el flujo de NETLIFECAM en app tm operaciones.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='ACTIVACION_PRODUCTOS_MEGADATOS'),
        'NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
        'VISEG',
        'SERIE_ELEMENTO,MAC_ELEMENTO,MODELO_ELEMENTO,DESCRIPCION_ELEMENTO@SERIE_TARJETA,MAC_TARJETA,MODELO_TARJETA,DESCRIPCION_TARJETA',
        (select ID_PROGRESOS_TAREA from DB_SOPORTE.admi_progresos_tarea where NOMBRE_TAREA = 'INSTALACION_NETLIFECAM'),
        'NO',
        'Activo',
        'rmoranc',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        'En Valor1 se coloca el código del producto, valor2 son los equipos a ingresar, valor3 es el id del flujo del progreso, valor4 define si se activa en conjunto con el internet'
);

-- Actualizar valor de parametro Cableado Ethernet que causa afectacion en movil
update DB_GENERAL.ADMI_PARAMETRO_DET set valor4='NO' where id_parametro_det=15828;

COMMIT;
/