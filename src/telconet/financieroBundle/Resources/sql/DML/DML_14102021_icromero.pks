/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalles de DEBITOS_PLANIFICADOS
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 14-10-2021 - Version Inicial.
 */
-- parametros MD
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS'),'EstadosContratosValidaRecurrente','Activo|Cancelado|Pendiente|PorAutorizar','Parametro',null,null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS'),'FormasPagoValidaRecurrente','3|11','Parametro',null,null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);
-- parametros TN
Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS'),'EstadosContratosValidaRecurrente','Activo|Cancelado|Pendiente|PorAutorizar','Parametro',null,null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'10',null,null,null);

Insert into db_general.admi_parametro_det (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
values (db_general.seq_admi_parametro_det.nextval,(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS'),'FormasPagoValidaRecurrente','3|11','Parametro',null,null,'Activo','icromero',sysdate,'127.0.0.1',null,null,null,null,'10',null,null,null);

COMMIT;

