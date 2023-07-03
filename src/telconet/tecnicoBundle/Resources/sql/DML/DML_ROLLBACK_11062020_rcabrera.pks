/**
 *
 * Se realiza script de ROLLBACK para eliminar: PARAMETROS PROYECTO PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 11-06-2020
 */

DECLARE

BEGIN             

  
  delete from DB_GENERAL.ADMI_PARAMETRO_DET where parametro_id = (SELECT ID_PARAMETRO
                                                              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                                WHERE NOMBRE_PARAMETRO = 'PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO');
  
  delete from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO';
      
  COMMIT;
    
  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK; 
END;

/ 
