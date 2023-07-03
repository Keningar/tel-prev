/**
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0
 * @since 21-10-2020
 * Se crean las sentencias DML para reversar configuraciones de la estructura 
 * CARACTERISTICA.
 */

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='ORIGEN_PROMOCION_EDITADA';

COMMIT;
/