/**
 *
 * Se realiza script de ROLLBACK para eliminar las solicitudes: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVA'
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 07-05-2020
 */

DECLARE


BEGIN             

  delete from db_comercial.admi_tipo_solicitud where DESCRIPCION_SOLICITUD = 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO';
  delete from db_comercial.admi_tipo_solicitud where DESCRIPCION_SOLICITUD = 'SOLICITUD AGREGAR EQUIPO MASIVO';
  
  delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id = (SELECT ID_PARAMETRO
                                                              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                                WHERE NOMBRE_PARAMETRO = 'CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO');
  
  delete from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO';
      
  COMMIT;
    
  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK; 
END;

/ 
