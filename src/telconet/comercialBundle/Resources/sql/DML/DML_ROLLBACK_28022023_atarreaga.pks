/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 20-02-2023
 * Se crea DML para reverso de configuraciones Ecuanet.
 */

---DB_GENERAL---
--PROM_ESTADOS_ADENDUM
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_ADENDUM') and usr_creacion = 'atarreaga' and empresa_cod = '33';

 
--PROM_TENTATIVA_MENSAJES
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES') and usr_creacion = 'atarreaga' and empresa_cod = '33';

 
--PARAM_EVALUA_TENTATIVA  
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAM_EVALUA_TENTATIVA') and usr_creacion = 'atarreaga' and empresa_cod = '33';

--PROM_PARAMETROS
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_PARAMETROS') and usr_creacion = 'atarreaga' and empresa_cod = '33';

--PROM_ROLES_CLIENTES
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ROLES_CLIENTES') and usr_creacion = 'atarreaga' and empresa_cod = '33';


--PROM_ESTADOS_SERVICIO
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_SERVICIO') and usr_creacion = 'atarreaga' and empresa_cod = '33';


--PROM_ESTADOS_BAJA_SERV
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV') and usr_creacion = 'atarreaga' and empresa_cod = '33';


--PROM_PRIORIDAD_SECTORIZACION
Delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_PRIORIDAD_SECTORIZACION') and usr_creacion = 'atarreaga' and empresa_cod = '33';

COMMIT;
/
