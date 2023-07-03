/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Se configura parametros estados permitidos para puntos y servicios del ws informacionCliente
 *
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 20-04-2020 - Versión Inicial.
 */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'EXTRANET_WS_INFOCLIENTE','Parametros usados en el webservice de informacionCliente','SOPORTE','INFORMACIONCLIENTE','Activo','jobedon', SYSDATE, '127.0.0.1',null,null,null);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT APC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE'),'ESTADOS_PUNTO','Trasladado,Eliminado,Cancelado,Anulado,In-Corte,Pendiente,Activo,Eliminado',null,null,null,'Activo','jobedon',sysdate,'127.0.0.1',null,null,null,null,18,null,null,null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT APC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE'),'ESTADOS_SERVICIO','Factible,PrePlanificada,EnVerificacion,EnPruebas,Cancelado,Anulado,Rechazada,Asignada,AsignadoTarea,Eliminado,Trasladado,Pendiente,Cancel,Inactivo,Activo,In-Corte',null,null,null,'Activo','jobedon',sysdate,'127.0.0.1',null,null,null,null,18,null,null,null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT APC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE'),'ESTADOS_EMPRESA_ROL','Cancelado,Inactivo,Anulado,Activo,Eliminado',null,null,null,'Activo','jobedon',sysdate,'127.0.0.1',null,null,null,null,18,null,null,null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT APC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE'),'ROLES_CLIENTES','Cliente,Pre-cliente',null,null,null,'Activo','jobedon',sysdate,'127.0.0.1',null,null,null,null,18,null,null,null);

COMMIT;

/