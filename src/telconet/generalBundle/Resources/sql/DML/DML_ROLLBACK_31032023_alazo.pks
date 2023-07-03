 /**
  * Documentación para  reversar Registro parametro de SOLICITUD REQUERIMIENTOS DE CLIENTES
  *  
  * @author Andre Lazo <alazo@telconet.ec>
  * @version 1.0 31-03-2023
  */

--ROLLBACK 
Delete from DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE 
DET.PARAMETRO_ID IN (SELECT ID_PARAMETRO FROM DB_GENERAL.Admi_Parametro_Cab where NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES')  
and DET.DESCRIPCION = 'Solicitud Requerimientos de clientes' 
and DET.EMPRESA_COD = '33' ;


COMMIT;
/
