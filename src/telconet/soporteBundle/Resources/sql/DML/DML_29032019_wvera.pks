--SE CREA NUEVO PROGRESO PARA LA VALIDACION DE CAJAS 
INSERT INTO 
"DB_SOPORTE"."ADMI_TIPO_PROGRESO" (ID_TIPO_PROGRESO, CODIGO, 
NOMBRE_TIPO_PROGRESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION) 
VALUES 
(DB_SOPORTE.SEQ_ADMI_TIPO_PROGRESO.NEXTVAL, 'VALIDA_CAJA', 'Valida Caja', 'Activo', 'admin', SYSDATE, '127.0.0.1');

--SE ACTUALIZAN LOS PROGRESOS PARA QUE LA SUMA DE 100  
UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" SET PORCENTAJE = '10' 
WHERE ID_PROGRESO_PORCENTAJE = 3;

UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" SET PORCENTAJE = '10'
WHERE ID_PROGRESO_PORCENTAJE = 24 ;

UPDATE "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" SET PORCENTAJE = '15'
WHERE ID_PROGRESO_PORCENTAJE = 48 ;


--SE CREA UN NUEVO REGISTRO PARA GUARDAR EL PROGRESO DE VALIDACION_CAJA 

INSERT INTO "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" 
(ID_PROGRESO_PORCENTAJE, PORCENTAJE, TIPO_PROGRESO_ID, TAREA_ID, ESTADO, 
USR_CREACION, FE_CREACION, IP_CREACION, FE_ULT_MOD, ORDEN, EMPRESA_ID)
VALUES (DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, '5', 
    (select ID_TIPO_PROGRESO from DB_SOPORTE.ADMI_TIPO_PROGRESO where CODIGO = 'VALIDA_CAJA'),
 '849', 'Activo', 'admin', SYSDATE, '127.0.0.1', SYSDATE, '10', '18');

INSERT INTO "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" (ID_PROGRESO_PORCENTAJE, 
PORCENTAJE, TIPO_PROGRESO_ID, TAREA_ID, ESTADO, USR_CREACION, FE_CREACION, 
IP_CREACION, FE_ULT_MOD, ORDEN, EMPRESA_ID) 
VALUES (DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, '5', 
    (select ID_TIPO_PROGRESO from DB_SOPORTE.ADMI_TIPO_PROGRESO where CODIGO = 'VALIDA_CAJA'),
 '849', 'Activo', 'admin', SYSDATE, '127.0.0.1', SYSDATE, '10', '10');

INSERT INTO "DB_SOPORTE"."INFO_PROGRESO_PORCENTAJE" (ID_PROGRESO_PORCENTAJE, 
PORCENTAJE, TIPO_PROGRESO_ID, TAREA_ID, ESTADO, USR_CREACION, FE_CREACION, 
IP_CREACION, FE_ULT_MOD, ORDEN, EMPRESA_ID) 
VALUES (DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, '5', 
    (select ID_TIPO_PROGRESO from DB_SOPORTE.ADMI_TIPO_PROGRESO where CODIGO = 'VALIDA_CAJA'),
 '850', 'Activo', 'admin', SYSDATE, '127.0.0.1', SYSDATE, '10', '10');

-- TIPO DOCUMENTO GENERAL
INSERT INTO 
"DB_GENERAL"."ADMI_TIPO_DOCUMENTO_GENERAL"  
VALUES(
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'KML',
    'KML',
    'Activo',
    'wvera',
    '127.0.0.1',
    sysdate,
    'wvera',
    sysdate,
    'S',
    'N',
    'N');

--PARAMETROS PARA TAREAS GIS 
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
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
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'PROCESO_TAREA_SUBJEFEDEPARTAMENTAL_GIS',
                 'PARAMETRO PARA LA CREACION DE TAREAS GIS (KML/COORDENADAS)',
                'TECNICO',
				'INSTALACION_TN',
				'Activo',
				'wvera',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);


Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='PROCESO_TAREA_SUBJEFEDEPARTAMENTAL_GIS'),
                'PARAMETRO PARA LA CREACION DE TAREAS GIS (KML/COORDENADAS)',
                'TAREAS DE GIS - DOCUMENTACION DE CLIENTES',
				'Registro de UM clientes TN/NL',
				'Actualizacion de DATA TECNICA de caja',
				NULL,
				'Activo',
				'wvera',
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
/
