/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 29-10-2019    
 * Se crean las sentencias DML para reversar configuraciones de la estructura 
 * DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 */

--configuración parámetros
--1
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                          WHERE NOMBRE_PARAMETRO = 'PROM_ROLES_CLIENTES'
                          AND ESTADO             = 'Activo');
                        
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROM_ROLES_CLIENTES'
  AND ESTADO             = 'Activo';

--2
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                          WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_NUMERO_MESES_EVALUA_FE_CONTRATO'
                          AND ESTADO             = 'Activo');

  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_NUMERO_MESES_EVALUA_FE_CONTRATO'
  AND ESTADO           = 'Activo';

  COMMIT;
/