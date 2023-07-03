/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 27-09-2019    
 * Se crean las sentencias DML para reversar configuraciones de la estructura 
 * DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET por tipos de promociones.
 */

-- actualización parámetros valores de instalación
  update ADMI_PARAMETRO_DET APD 
  set valor5      = '' ,
  APD.OBSERVACION = ''
  where apd.ESTADO    = 'Activo'
  AND apd.VALOR2      = 'EFECTIVO'
  AND apd.VALOR1      = 'CO'
  and APD.EMPRESA_COD = '18'; 


  update ADMI_PARAMETRO_DET APD 
  set valor5      = '',
  APD.OBSERVACION = ''
  where apd.ESTADO    = 'Activo'
  AND apd.VALOR2      = 'EFECTIVO'
  AND apd.VALOR1      = 'FO'
  and APD.EMPRESA_COD = '18'; 

--configuración parámetros
--1
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                          WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
                          AND ESTADO             = 'Activo');
                        
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_BAJA_SERV'
  AND ESTADO             = 'Activo';

--2
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                          WHERE NOMBRE_PARAMETRO = 'PROM_SOL_CAMBIOS_TEC'
                          AND ESTADO             = 'Activo');
                            
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROM_SOL_CAMBIOS_TEC'
  AND ESTADO             = 'Activo';


  COMMIT;
/
