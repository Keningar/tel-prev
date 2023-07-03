/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametro ACTIVACION_CICLOS_FACTURACION 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */

Insert into db_general.ADMI_PARAMETRO_CAB (ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
values (db_general.seq_admi_parametro_cab.nextval,'ACTIVACION_CICLOS_FACTURACION','CONTIENE PARAMETROS,MENSAJES,OTROS DATOS PARA LA ACTIVACION DE CICLO','COMERCIAL','DEBITOS','Activo','icromero',sysdate,'127.0.0.1',null,null,null);

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de ACTIVACION_CICLOS_FACTURACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='ACTIVACION_CICLOS_FACTURACION'),'MENSAJES_DEL_PROCESO','¿Está seguro de Activar el Ciclo de Facturación (%nombreCiclo%)? esto inactivara el ciclo que se encuentre activo.','Se Activó el Ciclo de Facturación (%nombreCiclo%)','Se presentaron problemas al activar el ciclo de Facturación (%nombreCiclo%)',null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar los ciclos  de facturacion 3 y 4
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 18-03-2022 - Version Inicial.
 */
INSERT INTO DB_FINANCIERO.ADMI_CICLO (ID_CICLO,NOMBRE_CICLO,FE_INICIO,FE_FIN,OBSERVACION,FE_CREACION,USR_CREACION,IP_CREACION,EMPRESA_COD,ESTADO,CODIGO) 
VALUES (DB_FINANCIERO.SEQ_ADMI_CICLO.nextval,'Ciclo (III) - 8 al 7',to_date('08-JUL-22','DD-MON-RR'),to_date('07-AUG-22','DD-MON-RR'),'Ciclo inicial configurado',SYSDATE,'icromero','127.0.0.1','18','Inactivo','CICLO3');

INSERT INTO DB_FINANCIERO.ADMI_CICLO (ID_CICLO,NOMBRE_CICLO,FE_INICIO,FE_FIN,OBSERVACION,FE_CREACION,USR_CREACION,IP_CREACION,EMPRESA_COD,ESTADO,CODIGO) 
VALUES (DB_FINANCIERO.SEQ_ADMI_CICLO.nextval,'Ciclo (IV) - 22 al 21',to_date('22-JUL-22','DD-MON-RR'),to_date('21-AUG-22','DD-MON-RR'),'Ciclo inicial configurado',SYSDATE,'icromero','127.0.0.1','18','Inactivo','CICLO4');

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar historial de los ciclos 3 y 4
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 18-03-2022 - Version Inicial.
 */
Insert into DB_FINANCIERO.ADMI_CICLO_HISTORIAL (ID_CICLO_HISTORIAL,CICLO_ID,NOMBRE_CICLO,FE_INICIO,FE_FIN,OBSERVACION,FE_CREACION,USR_CREACION,IP_CREACION,EMPRESA_COD,ESTADO) 
values (DB_FINANCIERO.SEQ_ADMI_CICLO_HISTORIAL.nextval,(select id_ciclo from DB_FINANCIERO.ADMI_CICLO where NOMBRE_CICLO = 'Ciclo (IV) - 22 al 21'),'Ciclo (IV) - 22 al 21',to_date('22-JUL-22','DD-MON-RR'),to_date('21-AUG-22','DD-MON-RR'),'Ciclo inicial configurado',SYSDATE,'icromero','127.0.0.1','18','Inactivo');

Insert into DB_FINANCIERO.ADMI_CICLO_HISTORIAL (ID_CICLO_HISTORIAL,CICLO_ID,NOMBRE_CICLO,FE_INICIO,FE_FIN,OBSERVACION,FE_CREACION,USR_CREACION,IP_CREACION,EMPRESA_COD,ESTADO) 
values (DB_FINANCIERO.SEQ_ADMI_CICLO_HISTORIAL.nextval,(select id_ciclo from DB_FINANCIERO.ADMI_CICLO where NOMBRE_CICLO = 'Ciclo (III) - 8 al 7'),'Ciclo (III) - 8 al 7',to_date('08-JUL-22','DD-MON-RR'),to_date('07-AUG-22','DD-MON-RR'),'Ciclo inicial configurado',SYSDATE,'icromero','127.0.0.1','18','Inactivo');

commit;