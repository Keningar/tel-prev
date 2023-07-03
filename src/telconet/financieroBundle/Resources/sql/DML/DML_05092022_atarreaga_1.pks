/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 05-09-2022
 * Se crea DML de configuraciones de mejora de generación de débitos.
 */
 
--Crear parámetro cabecera 

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAM_GENERACION_DEBITOS',
    'Parámetros definidos para el proceso de mejora generación de débitos',
    'FINANCIERO',
    'DEBITOS',
    'Activo',
    'atarreaga',
    SYSDATE,
    '127.0.0.1',
    'atarreaga',
    SYSDATE,
    NULL
  );
  

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'FLUJO_GENERACION_DEBITO',    'SI',   NULL,   NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: Flujo por empresa para las opciones de mejora en la generación de débitos');   
    
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'CONFIGURACION_NFS',    'GeneracionDebitos',   'TelcosWeb',   'Debitos',  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: PathAdicional; VALOR2: App; VALOR3: SubModulo');   
        
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIOS_DEBITOS',   'Todos', 'ESTADO_TODOS',   NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: estado de servicio para presentar en la pantalla de generación débitos; VALOR2: Valor detalle de la descripción del estado');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIOS_DEBITOS',     'Activo',   'ESTADO_SERVICIO',  NULL,   NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: estado de servicio para presentar en la pantalla de generación débitos; VALOR2: Valor detalle de la descripción del estado');
    
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIOS_DEBITOS',    'In-Corte',    'ESTADO_SERVICIO',  NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: estado de servicio para presentar en la pantalla de generación débitos; VALOR2: Valor detalle de la descripción del estado');
    

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIOS_DEBITOS',   'Cancel',    'ESTADO_SERVICIO',   NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: estado de servicio para presentar en la pantalla de generación débitos; VALOR2: Valor detalle de la descripción del estado');    

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIOS_DEBITOS',   'Trasladado',    'ESTADO_SERVICIO',   NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: estado de servicio para presentar en la pantalla de generación débitos; VALOR2: Valor detalle de la descripción del estado');    
   
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'CONFIGURACION_FORMATO_EXCEL_CLIENTE',    '0',   '1',   '0',  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: Hoja excel; VALOR2: posición de fila inicial del excel; VALOR3: posición de columna de las identificaciones'); 
    
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_EXT_ARCHIVO',    'Extensio&#769;n de archivo no va&#769;lida, favor verificar que sea formato excel.',   'Extensión de archivo no váilda, favor verificar que sea formato excel.',   NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje validación de extensión del archivo para la pantalla generación débitos; VALOR2: mensaje validación de extensión del archivo para el proyecto Java generación débitos');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ARCHIVO_CLIENTES',    'Archivo Clientes Exclusión: %archivo% <br>',   NULL,   NULL,  NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de selección archivo excel');     

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_FECHA_DESDE_HASTA',    'Debe seleccionar fecha activacio&#769;n desde y hasta.',    NULL,    NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de selección fecha activación');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_FECHA_DESDE_MENOR',    'Debe seleccionar una Fecha Activacio&#769;n Desde menor a la Fecha Activacio&#769;n Hasta.',    NULL,    NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de selección fecha activación');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_FECHA_HASTA_MENOR_ACTUAL',    'Debe seleccionar una Fecha Activacio&#769;n Hasta menor o igual a la fecha actual.',    NULL,    NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de selección fecha activación');
    

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ESTADOS_OS',    'Estados Servicios: %estadosOS% <br>',    NULL,    NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de presentación estados de servicios'); 
    
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_FECHA_ACTIVACION',    'Fechas Activacio&#769;n: F. desde: %fechaDesde% F. hasta: %fechaHasta% <br>',    NULL,    NULL,    NULL,    NULL,
    NULL,    NULL,    'Activo',    'atarreaga',    SYSDATE,    '127.0.0.1',    '18',    'VALOR1: mensaje de presentación fecha activación');  

  Insert into DB_GENERAL.ADMI_PARAMETRO_DET 
  (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
  (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
  ),
  'MENSAJE_MOTIVO_RECHAZO_HOMOLOGADO','Motivo Rechazo: <br> %motivoRechazo% <br>',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: mensaje de presentación motivos de rechazos bancarios homologado');
    
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_RECHAZO_HOMOLOGADO','ERROR NUMERO CTA/TC',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: Motivo de rechazo homologado para presentar en la pantalla de generación débitos. Se relaciona con el detalle de parámetro motivo rechazo debito');


  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_RECHAZO_HOMOLOGADO','CTA/TC INACTIVA/CERRADA',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: Motivo de rechazo homologado para presentar en la pantalla de generación débitos. Se relaciona con el detalle de parámetro motivo rechazo debito');

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_RECHAZO_HOMOLOGADO','FALTA DE FONDOS',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: Motivo de rechazo homologado para presentar en la pantalla de generación débitos. Se relaciona con el detalle de parámetro motivo rechazo debito');

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_RECHAZO_HOMOLOGADO','AUTORIZACION DEBITO PENDIENTE DE ENVIO B.P.',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: Motivo de rechazo homologado para presentar en la pantalla de generación débitos. Se relaciona con el detalle de parámetro motivo rechazo debito');

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
  values (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_RECHAZO_HOMOLOGADO','TC INVALIDA/BLOQUEADA/EXPIRADA/ROBADA',null,null,null,'Activo','atarreaga',SYSDATE,'127.0.0.1',null,null,null,null,'18',null,null,'VALOR1: Motivo de rechazo homologado para presentar en la pantalla de generación débitos. Se relaciona con el detalle de parámetro motivo rechazo debito');

--Creación de características
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO,DETALLE_CARACTERISTICA) 
VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'RUTA_NFS_GENERACION_DEBITO','T','Activo',SYSDATE,'atarreaga',null,null,'FINANCIERO',NULL);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO,DETALLE_CARACTERISTICA) 
VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'ESTADO_SERVICIO_GENERACION_DEBITO','T','Activo',SYSDATE,'atarreaga',null,null,'FINANCIERO',NULL);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO,DETALLE_CARACTERISTICA) 
VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'FE_ACTIVACION_DESDE_GENERACION_DEBITO','T','Activo',SYSDATE,'atarreaga',null,null,'FINANCIERO',NULL);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO,DETALLE_CARACTERISTICA) 
VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'FE_ACTIVACION_HASTA_GENERACION_DEBITO','T','Activo',SYSDATE,'atarreaga',null,null,'FINANCIERO',NULL);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO,DETALLE_CARACTERISTICA) 
VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'MOTIVOS_RECHAZO_GENERACION_DEBITO','T','Activo',SYSDATE,'atarreaga',null,null,'FINANCIERO',NULL); 

COMMIT;
/
