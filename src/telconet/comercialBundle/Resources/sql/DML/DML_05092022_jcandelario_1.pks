/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametro INVOCACION_KONIBIT_ACTUALIZACION 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 29-06-2022 - Version Inicial.
 */

Insert into db_general.ADMI_PARAMETRO_CAB (ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
values (db_general.seq_admi_parametro_cab.nextval,'INVOCACION_KONIBIT_ACTUALIZACION','CONTIENE PARAMETROS PARA INVOCACION A SERVICIO KONIBIT','TECNICO','DEBITOS','Activo','icromero',sysdate,'127.0.0.1',null,null,null);
/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'MENSAJES_TRASLADO_KONIBIT','Se realizó el traslado del producto correctamente en Konibit','No se realizó el traslado del producto en Konibit. Error: No se obtuvo respuesta del proveedor','No se realizó el traslado del producto en Konibit. Error:',null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'MENSAJES_CRS_KONIBIT','Se realizó el cambio de razón social del producto correctamente en Konibit','No se realizó el cambio de razón social del producto en Konibit. Error: No se obtuvo respuesta del proveedor','No se realizó el cambio de razón social del producto en Konibit. Error',null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'MENSAJES_CRS_LOGIN_KONIBIT','Se realizó el cambio de razón social del producto correctamente en Konibit','No se realizó el cambio de razón social del producto en Konibit. Error: No se obtuvo respuesta del proveedor','No se realizó el cambio de razón social del producto en Konibit. Error',null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'WS_KONIBIT','https://serviciosnetlife.konibit.mx/netlife-midd/','traslado','crs','A','Activo','icromero',sysdate,'127.0.0.1',null,null,null,'updatePunto','18','konibit','001',null);

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'TEMPLATES','errorCorreoCrsKonibit','errorCorreoTrasladoKonibit',null,null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='INVOCACION_KONIBIT_ACTUALIZACION'),'EMAIL_DATA','notificaciones@telconet.ec','CAMBIO DE RAZÓN SOCIAL NO REGISTRADO EN KONIBIT','TRASLADO NO REGISTRADO EN KONIBIT','vyepez@netlife.net.ec,tecnico_senior@netlife.net.ec,producto@netlife.net.ec','Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar la caracteristica CANT MAX KONIBIT
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CANT MAX KONIBIT','N','Activo',sysdate,'icromero','TECNICO'
);

-- ProductoCarateristicas
-- CANT MAX KONIBIT - Producto ECOMMERCE BASIC
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where DESCRIPCION_PRODUCTO = 'ECOMMERCE BASIC'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT'
    ),
    sysdate, 'icromero', 'Activo', 'SI'
);

-- ProductoCarateristicas
-- CANT MAX KONIBIT - Producto Netlife Assistance Pro
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT'
    ),
    sysdate, 'icromero', 'Activo', 'SI'
);

-- ProductoCarateristicaComportamiento
-- CANT MAX KONIBIT - Producto Netlife Assistance Pro
Insert into DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO (ID_PROD_CARAC_COMP,PRODUCTO_CARACTERISTICA_ID,ES_VISIBLE,EDITABLE,VALORES_SELECCIONABLE,VALORES_DEFAULT,ESTADO,FE_CREACION,FE_ULT_MOD,USR_CREACION,USR_ULT_MOD) 
values (DB_COMERCIAL.SEQ_ADMI_PROD_CARAC_COMP.NEXTVAL,(select ID_PRODUCTO_CARACTERISITICA from DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA where PRODUCTO_ID = (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO where DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro') AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT')),0,0,null,'1','Activo',sysdate ,null,'icromero',null);
-- ProductoCarateristicaComportamiento
-- CANT MAX KONIBIT - Producto ECOMMERCE BASIC
Insert into DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO (ID_PROD_CARAC_COMP,PRODUCTO_CARACTERISTICA_ID,ES_VISIBLE,EDITABLE,VALORES_SELECCIONABLE,VALORES_DEFAULT,ESTADO,FE_CREACION,FE_ULT_MOD,USR_CREACION,USR_ULT_MOD) 
values (DB_COMERCIAL.SEQ_ADMI_PROD_CARAC_COMP.NEXTVAL,(select ID_PRODUCTO_CARACTERISITICA from DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA where PRODUCTO_ID = (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO where DESCRIPCION_PRODUCTO = 'ECOMMERCE BASIC') AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT')),0,0,null,'1','Activo',sysdate ,null,'icromero',null);


COMMIT;
/
