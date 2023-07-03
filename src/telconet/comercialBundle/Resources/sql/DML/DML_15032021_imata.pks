----- INSERT PARA AGREGAR PARAMETRO PARA CANAL DE EXTRANET

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CANALES_PUNTO_VENTA'),'NUEVO CANAL EXTRANET',
'CANAL_EXTRANET',null,null,null,'Activo','imata',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,null);

---- INSERT QUE SE DEBE EJECUTAR EN EL ESQUEMA tokensecurity, para agregar un nuevo usuario.

INSERT INTO db_tokensecurity.web_service VALUES 
( ( SELECT MAX(id_web_service) + 1 FROM db_tokensecurity.web_service ),
'ComercialMobileWSControllerRest', 'procesarAction', 1, 'ACTIVO', 
( SELECT id_application FROM db_tokensecurity.application WHERE NAME = 'WS-RDA' ) );

commit;

---- INSERTS PARA AGREGAR USUARIO DE VENTA EXTRANET

INSERT INTO DB_COMERCIAL.INFO_PERSONA (
    id_persona,
    titulo_id,
    origen_prospecto,
    tipo_identificacion,
    identificacion_cliente,
    tipo_empresa,
    tipo_tributario,
    nombres,
    apellidos,
    razon_social,
    representante_legal,
    nacionalidad,
    direccion,
    login,
    cargo,
    direccion_tributaria,
    genero,
    estado,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    estado_civil,
    fecha_nacimiento,
    calificacion_crediticia,
    origen_ingresos,
    origen_web,
    contribuyente_especial,
    paga_iva,
    numero_conadis,
    pais_id
) VALUES (
    DB_COMERCIAL.SEQ_INFO_PERSONA.NEXTVAL,
    NULL,
    'N',
    'RUC',
    NULL,
    NULL,
    'NAT',
    'Canal',
    'Extranet',
    NULL,
    NULL,
    'NAC',
    NULL,
    'extranet',
    NULL,
    NULL,
    NULL,
    'Activo',
    sysdate,
    'imata',
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    'S',
    'N',
    'S',
    NULL,
    NULL
);



INSERT 
 INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL
 (
  ID_PERSONA_ROL,
  PERSONA_ID,
  EMPRESA_ROL_ID,
  OFICINA_ID,
  DEPARTAMENTO_ID,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  CUADRILLA_ID,
  PERSONA_EMPRESA_ROL_ID,
  PERSONA_EMPRESA_ROL_ID_TTCO,
  REPORTA_PERSONA_EMPRESA_ROL_ID,
  ES_PREPAGO,
  USR_ULT_MOD,
  FE_ULT_MOD
 )
 VALUES
 (
 DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL.NEXTVAL,
 (SELECT ID_PERSONA FROM DB_COMERCIAL.INFO_PERSONA WHERE LOGIN='extranet' AND ESTADO='Activo'),
 (SELECT IER.ID_EMPRESA_ROL FROM DB_COMERCIAL.INFO_EMPRESA_ROL IER JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL = IER.ROL_ID
  JOIN DB_GENERAL.ADMI_TIPO_ROL ATR ON ATR.ID_TIPO_ROL = AR.TIPO_ROL_ID WHERE ATR.DESCRIPCION_TIPO_ROL='Empleado' AND AR.DESCRIPCION_ROL='Calidad Software'
  AND IER.EMPRESA_COD='18' AND IER.ESTADO='Activo' AND ROWNUM=1),
 (SELECT ID_OFICINA FROM DB_COMERCIAL.INFO_OFICINA_GRUPO WHERE NOMBRE_OFICINA='MEGADATOS - GUAYAQUIL' AND EMPRESA_ID=18 AND ESTADO='Activo'),
 (SELECT ID_DEPARTAMENTO FROM DB_GENERAL.ADMI_DEPARTAMENTO WHERE NOMBRE_DEPARTAMENTO='Sistemas' AND ESTADO='Activo' AND EMPRESA_COD='18'),
 'Activo',
 'imata',
 SYSDATE,
 '127.0.0.1',
 NULL,
 NULL,
 NULL,
 NULL,
 'S',
 NULL,
 NULL 
 );


----INSERT PARA AGREGAR PARAMETRIZAR LOS DATOS PARA LA CREACION DE UNA TAREA PARA UN TRASLADO

INSERT 
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'DATOS_CREACION_TAREA','Parametro para almacenar los datos al crear una tarea para un traslado de extranet','TRASLADO_EXTRANET','','Activo','imata',SYSDATE,
    '127.0.0.1', null, null, null);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'DATOS_CREACION_TAREA'),
    'DATOS_CREAR_TAREA','cartieda','TRASLADO DE SERVICIO','Se realiza el traslado del servicio desde la Extranet de forma correcta','Interno','Activo','imata',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,'Registro Interno','18',NULL,NULL,NULL);


----INSERT PARA PARAMETRIZAR EL MENSAJE DE ERROR GENERAL DEL PROCESO DE TRASLADO
INSERT 
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'ERRORES_TRASLADO','Parametro para validar los mensajes de error del proceso de traslado','TRASLADO_EXTRANET','','Activo','imata',SYSDATE,
    '127.0.0.1', null, null, null);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'ERRORES_TRASLADO'),
    'ERRORES_PROCESO_TRASLADO','Su solicitud no puede ser procesada por este medio Por favor comuníquese al 3920000',NULL,NULL,NULL,'Activo','imata',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,'18',NULL,NULL,NULL);


COMMIT;
/
