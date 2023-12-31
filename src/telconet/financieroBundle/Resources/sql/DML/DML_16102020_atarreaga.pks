/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 16-10-2020    
 * Se crea DML para configuraciones de procesos para reubicación de MD.
 */

--SOLICITUD FACTURACION POR REUBICACION
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
ID_TIPO_SOLICITUD,
DESCRIPCION_SOLICITUD,
FE_CREACION,
USR_CREACION,
ESTADO,
TAREA_ID,
ITEM_MENU_ID,
PROCESO_ID
) VALUES (
DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
'SOLICITUD FACTURACION POR REUBICACION',
SYSDATE,
'atarreaga',
'Activo',
null,
null,
null);

--SOLICITUD NOTA CREDITO POR REUBICACION
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
ID_TIPO_SOLICITUD,
DESCRIPCION_SOLICITUD,
FE_CREACION,
USR_CREACION,
ESTADO,
TAREA_ID,
ITEM_MENU_ID,
PROCESO_ID
) VALUES (
DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
'SOLICITUD NOTA CREDITO POR REUBICACION',
SYSDATE,
'atarreaga',
'Activo',
null,
null,
null);


-- SOLICITUD FACTURACION POR REUBICACION'
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
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' AND ESTADO = 'Activo'),
'Solicitud facturacion por reubicacion',
'SOLICITUD FACTURACION POR REUBICACION',
(
SELECT IPC.ID_PLAN 
FROM  
 DB_COMERCIAL.INFO_PLAN_CAB IPC, 
 DB_COMERCIAL.INFO_PLAN_DET IPD 
WHERE 
 IPC.ID_PLAN = IPD.PLAN_ID AND 
 IPC.NOMBRE_PLAN = 'REUBICACION' AND 
 IPC.EMPRESA_COD = 18
),
null,
'Reubicación según tarea No. ',
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
null,
null,
'telcos_reubica',
'18',
'S',
null,
null); 

--PARAMETRO DONDE SE CONFIGURA EL DETALLE PARA LA FACTURACION POR REUBICACION
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
EMPRESA_COD)
VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
( SELECT ID_PARAMETRO 
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
  WHERE NOMBRE_PARAMETRO = 'DESCRIPCION_TIPO_FACTURACION' AND ESTADO = 'Activo'),
'PARAMETRO DONDE SE CONFIGURA EL DETALLE PARA LA FACTURACION POR REUBICACION',
'telcos_reubica',
'VALOR2',
'VALOR3',
'MD',
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
'18');

--caracteristicas
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'SOLICITUD_FACT_REUBICACION',
'N',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Solicitud de Factura por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'SOLICITUD_NC_REUBICACION',
'N',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Solicitud de Nota de Crédito por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'NUMERO_TAREA_REUBICACION',  
'N',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Número de tarea por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'OBSERVACION_NC_REUBICACION',
'T',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Observación de Nota de Crédito por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'AUTORIZADO_NC_REUBICACION',
'T',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Persona que autoriza Nota de Crédito por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'PORCENTAJE_NC_REUBICACION',
'N',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Porcentaje de Nota de Crédito por reubicación');

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'MOTIVO_NC_REUBICACION',
'T',
'Activo',
SYSDATE,
'atarreaga',
'FINANCIERO',
'Motivo de Nota de Crédito por reubicación');

--PARAMETRO_CAB CARACTERISTICAS_NC_REUBICACION
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
ID_PARAMETRO,
NOMBRE_PARAMETRO,
DESCRIPCION,
MODULO,
PROCESO,
ESTADO,
USR_CREACION,
FE_CREACION,
IP_CREACION
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'PROCESO_REUBICACION',
'PARAMETRO PADRE PARA VALORES USADOS EN EL PROYECTO DE REUBICACION.',
'FINANCIERO',
'REUBICACION',
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1');


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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MOTIVO_SOLICITUD_FACT',
'Traslado / Reubicacion',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'PERSONAL_AUTORIZA_NC',
'SELECT NO_EMPLE, NOMBRE 
FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS 
WHERE PUESTO = ''CSC1'' 
AND NOMBRE_DEPTO = ''SERVICIO AL CLIENTE'' 
AND ESTADO =''A'' ',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'PERSONAL_AUTORIZA_NC',
'SELECT NO_EMPLE, NOMBRE 
FROM NAF47_TNET.V_EMPLEADOS_EMPRESAS 
WHERE PUESTO = ''COIP'' 
AND NOMBRE_DEPTO = ''IP CONTACT CENTER'' 
AND ESTADO = ''A''',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'PORCENTAJE_NC',
'50|100',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MOTIVO_NC',
'Problemas con el Servicio',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MOTIVO_NC',
'Por retención del cliente',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');
 

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MOTIVO_NC',
'Problemas en la instalación',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MOTIVO_NC',
'Por antigüedad del cliente',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'VIGENCIA_SOLICITUD_NC',
'30',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
null,
null,
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'MESES_SERVICIO_ACTIVO',
'24',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'DIAS_HABILES TAREA_REUBICACION',
'8',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');


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
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'TAREA_AUTOMATICA_REUBICACION',
464539,
'Facturar reubicacion de equipos',
'PROCESOS TAREAS ATC',
'Su ayuda con la FC y NC por reubicación en base a tarea Num. ',
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
null,
null,
'VERONICA CESIBEL VARELA VERA',
'18',
'empleado');

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
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROCESO_REUBICACION'),
'SOLICITUD_NOTA_CREDITO_REUBICACION',
'SOLICITUD_NC_REUBICACION',
'SOLICITUD NOTA CREDITO POR REUBICACION',
'telcos_reubica',
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
null,
null,
null,
'18');


--PARAMETRO_CAB TAREAS_REUBICACION
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
ID_PARAMETRO,
NOMBRE_PARAMETRO,
DESCRIPCION,
MODULO,
PROCESO,
ESTADO,
USR_CREACION,
FE_CREACION,
IP_CREACION
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'NOMBRES_TAREAS_REUBICACION',
'NOMBRES DE TAREAS DE REUBICACION PARA SOLICITUDES DE FACT Y NC',
'FINANCIERO',
'REUBICACION',
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NOMBRES_TAREAS_REUBICACION'),
'Reubicacion Equipos en Cliente',
'Reubicacion Equipos en Cliente',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

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
VALOR5,
EMPRESA_COD
) VALUES (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NOMBRES_TAREAS_REUBICACION'),
'Reubicacion Equipos en Cliente Sin Fibra',
'Reubicacion Equipos en Cliente Sin Fibra',
null,
null,
null,
'Activo',
'atarreaga',
SYSDATE,
'127.0.0.1',
null,
'18');

COMMIT;
/
