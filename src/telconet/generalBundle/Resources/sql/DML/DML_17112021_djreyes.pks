-- Parametros para formulario de soporte
-- CAB 1 - Cabecera general para todo el proceso de formulario
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
(
  ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,
  FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD
) 
VALUES (
  db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'LISTADO_VALORES_PRODUCTOS_TV',
  'Listar los valores para los diferentes campos de seleccion en solicitudes',
  'TECNICO','PRODUCTOS_TV','Activo','djreyes',SYSDATE,'127.0.0.1',NULL,NULL,NULL
);

-- DET 1 - Valores para opcion de Recurrente 1
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'VALOR-RECURRENTE','GTVPREMIUM', 1, 'SI', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 2 - Valores para opcion de Recurrente 2
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'VALOR-RECURRENTE','GTVPREMIUM', 2, 'NO', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 3 - Valores para opcion de Forma contrato 1
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'FORMA-CONTRATO','GTVPREMIUM', 1, 'Netlife', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 4 - Valores para opcion de Forma contrato 2
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'FORMA-CONTRATO','GTVPREMIUM', 2, 'Otros', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 5 - Valores para opcion de Plan contratado 1
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'PLAN-CONTRATO','GTVPREMIUM', 1, 'Ecuador', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 6 - Valores para opcion de Plan contratado 2
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'PLAN-CONTRATO','GTVPREMIUM', 2, 'Perú', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 7 - Valores para opcion de Plan contratado 3
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'PLAN-CONTRATO','GTVPREMIUM', 3, 'Uruguay', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 8 - Valores para opcion de Plan contratado 4
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'PLAN-CONTRATO','GTVPREMIUM', 4, 'Portugal', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 9 - Valores para opcion de Dispositivos 1
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'DISPOSITIVOS','GTVPREMIUM', 1, 'Movil', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 10 - Valores para opcion de Dispositivos 2
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'DISPOSITIVOS','GTVPREMIUM', 2, 'PC', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 11 - Valores para opcion de Dispositivos 3
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
  ),
  'DISPOSITIVOS','GTVPREMIUM', 3, 'Chromecast', NULL, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL,
  'valor1=NombreTecnico del producto, valor2=Posicion del item, valor3=Valor a presentar'
);

-- DET 12 - Valores para opcion de Dispositivos 3
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	WHERE NOMBRE_PARAMETRO = 'ADMI_CORREO_FORMULARIO_SOPORTE'
  ),
  'ADMINISTRACIÓN DE CORREOS USADOS PARA LA NOTIFICACIÓN DEL FORMULARIO L2 DE SOPORTE','GTVPREMIUM', null, null, null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto'
);

-- DET 13 - Parametrizamos la nueva tarea para formulario
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'TAREA USADA PARA FORMULARIO DE SOPORTE','GTVPREMIUM', 'GOLTV', 
  (
    SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'GOLTV'
  )
  , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: nombre de la tarea, valor3: id de la tarea'
);
-- DET 14 - Parametrizamos la nueva plantilla de formulario
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'DATOS USADOS PARA ENVIO DE CORREO DEL FORMULARIO DE SOPORTE','GTVPREMIUM', 'soporte@netlife.net.ec', 'Formulario de Soporte LV2' , 'GOLTV-FORML2', 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo remitente, valor3: Asunto del correo, valor4: codigo de plantilla'
);
-- DET 15 - Los correos habilitados
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE','GTVPREMIUM', 'lbarahona@netlife.net.ec', null , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo destinatario'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE','GTVPREMIUM', 'play@goltv.tv', null , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo destinatario'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE','GTVPREMIUM', 'v.cordero@goltv.tv', null , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo destinatario'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE','GTVPREMIUM', 'soporte.tecnico@iroute.com.ec', null , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo destinatario'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO_INTEGRACION_FORMULARIO_SOPORTE'
  ),
  'CORREOS DESTINATARIOS USADOS PARA EL FORMULARIO DE SOPORTE','GTVPREMIUM', 'alfredo.bastreri@postmultimedia.com', null , null, 'Activo',
  'djreyes', SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null,
  'valor1: nombre tecnico del producto, valor2: correo destinatario'
);

-- Cambios de razon social
-- DET 16 - Productos permitidos en cambio de razon social
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES
(
  db_general.seq_admi_parametro_det.nextval,
  (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	  WHERE NOMBRE_PARAMETRO = 'NOMBRE_TECNICO_PRODUCTOSTV_CRS'
  ),
  'FLUJO_CRS','GTVPREMIUM', null, null, null, 'Activo', 'djreyes',
  SYSDATE, '127.0.0.1', null, null, null, null, '18', null, null, null
);

COMMIT;
/
