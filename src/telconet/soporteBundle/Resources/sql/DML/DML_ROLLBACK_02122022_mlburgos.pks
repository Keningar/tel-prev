/** 
 * @author Leonela Burgos <mlburgos@telconet.ec>
 * @version 1.0 
 * @since 17-11-2022
 * Se crea DML ROLLBACK de configuraciones del Proyecto Tarjetas ABU
 */

DELETE FROM DB_SOPORTE.ADMI_TAREA WHERE 
PROCESO_ID=(select ID_PROCESO from  DB_SOPORTE.ADMI_PROCESO where nombre_proceso='TARJETA ABU') 
AND NOMBRE_TAREA='Cambiar forma de pago';  

DELETE FROM DB_SOPORTE.ADMI_PROCESO_EMPRESA 
WHERE PROCESO_ID=(select ID_PROCESO from  DB_SOPORTE.ADMI_PROCESO where nombre_proceso='TARJETA ABU')
AND EMPRESA_COD='18';  

DELETE FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO='TARJETA ABU'; 
    
    COMMIT;
