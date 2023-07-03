INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 'CLONACION_FACTURA', 'N', 'Activo', sysdate, 'gnarea', null, null, 'FINANCIERO', null);
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 'NUMERO_FACTURA_PADRE', 'N', 'Activo', sysdate, 'gnarea', null, null, 'FINANCIERO', null);

INSERT into db_general.admi_parametro_cab values (db_general.seq_admi_parametro_cab.nextval,'CLONACION DE FACTURAS', 'PARAMETROS DE CONFIGURACION DE CLONACION DE FACTURAS Y PREFACTURAS','FINANCIERO', NULL, 'Activo', 'gnarea', sysdate, '0.0.0.0',null, null, null);

--PREFACTURAS EN TN


Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MOSTRAR_FECHA_PREFACTURA_PADRE','S',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Bandera para copia y muestra de la fecha en el frontend de la clonacion de facturaS'); 

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CARACTERISTICAS_CLONACION_PREFACTURAS','HORAS SOPORTE',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Estados permitidos para poder clonar facturas');

------ESTADOS Y MENSAJES-ESTADOS
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'ESTADOS_CLONACION_PREFACTURAS','Pendiente',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Estados permitidos para poder clonar facturas');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_ESTADOS_PREFACTURA','La pre-factura no esta un estado valido',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de Advertencia de Caracteristicas de facturas a clonar en nuevas facturas');

--MOSTRAR FECHA DE CONSUMO EN TN
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MOSTRAR_FECHA_FACTURA_PADRE','S',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Bandera para copia y muestra de la fecha en el frontend de la clonacion de facturaS'); 

--> MENSAJE DE PERFIL

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_PERFIL_NO_PERMITIDO','El usuario no cuenta con el perfil requerido',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Tiene el mensaje que se muestra si el usuario no cuenta con un perfil permitido para clonacion');

--COMPENSACION SOLIDARIA EN TN (por FAC-CABECERA EN getDescuentoCompensacion)
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_COMPENSACION_SOLIDARIA','Su factura de origen de Clonación posee un descuento de compensación solidaria',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de Advertencia de Caracteristicas de facturas a clonar en nuevas facturas');

---ESTADOS PERMITIDOS Y MENSAJE, PARA CLONACION DE FACTURA (TN)
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'ESTADOS_CLONACION_FACTURAS','Activo',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Estados permitidos para poder clonar facturas');
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'ESTADOS_CLONACION_FACTURAS','Cerrado',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Estados permitidos para poder clonar facturas');
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'ESTADOS_CLONACION_FACTURAS','Anulado',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Estados permitidos para poder clonar facturas');    

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_ESTADOS_FACTURA','La factura no esta un estado valido',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de Advertencia de Caracteristicas de facturas a clonar en nuevas facturas');

--PRODUCTOS Y MENSAJE NO PERMITIDOS PARA CLONACION (TN)
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CHEQUEO_PRODUCTOS','MIGRACION DE SALDO INICIAL',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Busqueda de Productos en facturas-padre en nuevas facturas clonadas');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_PRODUCTOS','Su factura de origen de Clonación posee un producto de %nombre_producto%',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de advertencia (CHEQUEO_PRODUCTOS) de Productos en facturas por clonar');

--CARACTERISTICAS QUE SE CLONAN DE LA FACTURA (TN)
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CARACTERISTICAS_CLONACION_FACTURAS','HORAS SOPORTE',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Caracteristicas de facturas a clonar en nuevas facturas');



-- CHEQUEO DE CARACTERISTICAS ESPECIALES EN FACTURA
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CHEQUEO_CARACTERISTICAS','CARTERA_DACION',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Busqueda de Caracteristicas de facturas');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_CARACTERISTICAS','Su factura de origen de Clonación posee la característica %nombre_caracteristica%',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de Advertencia (CHEQUEO_CARACTERISTICAS) de Caracteristicas de facturas a clonar en nuevas facturas');


--CHEQUEO DE IVAS ESPECIALES EN FACTURA Y MENSAJE DE ADVERTENCIA DE IVA EN FACTURA A CLONAR
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CHEQUEO_IMPUESTOS','IVA 14%',NULL,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,
    'Busqueda de iva del 14% en algun detalle de la factura a clonar. Valor1: Mensaje a mostrar, Valor2: Descripcion AdmiImpuesto');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_IMPUESTO_IVA 14%','un producto con IVA diferente al actual',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,
    'Mensaje de iva del 14% en algun detalle de la factura a clonar. Valor1: Mensaje a mostrar, (MENSAJE_IMPUESTO_Descripcion:AdmiImpuesto)');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_IMPUESTO_ICE 15%','un producto con ICE, y actualmente no graba',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,
    'Mensaje de ICE actualmente no grabado en algun detalle de la factura a clonar. Valor1: Mensaje a mostrar, Valor2: Descripcion AdmiImpuesto');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_IMPUESTOS','Su factura de origen de Clonación posee %impuesto_descripcion% .',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,'10',null,null,'Mensaje de Advertencia (CHEQUEO_IVA) de IVA de facturas a clonar en nuevas facturas');



--EMPRESAS PERMITIDA PARA PREFACTURACION y FACTURACION (CLONACION)

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CHEQUEO_EMPRESA_CLON_FACTURAS','10',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,null,null,null,'Chequeo de empresas para iniciar clonacion');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'CHEQUEO_EMPRESA_CLON_PREFACTURAS','10',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,null,null,null,'Chequeo de empresas para iniciar clonacion');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_EMPRESA_CLON_FACTURAS','La empresa no es permitida para clonar',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,null,null,null,'Chequeo de empresas para iniciar clonacion');

Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) values (db_general.seq_admi_parametro_det.nextval,(select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'CLONACION DE FACTURAS'),
    'MENSAJE_CHEQUEO_EMPRESA_CLON_PREFACTURAS','La empresa no es permitda a clonar',null,null,null,'Activo','gnarea',sysdate,'0.0.0.0',null,null,null,null,null,null,null,'Chequeo de empresas para iniciar clonacion');

commit;

