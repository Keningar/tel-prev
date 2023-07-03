/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 26-06-2020
 * Se crean las sentencias DML para reversar configuraciones
 */


DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='PROCESO_DE_EJECUCION';

COMMIT;
/