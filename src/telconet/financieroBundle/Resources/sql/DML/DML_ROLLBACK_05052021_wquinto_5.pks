/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML se eliminar menu de proceso de anulacion, antes de ser asignado a perfil
 */

DELETE FROM DB_SEGURIDAD.SEGU_ASIGNACION 
    WHERE PERFIL_ID=(SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Anulacion Pagos');

DELETE FROM DB_SEGURIDAD.SIST_PERFIL 
    WHERE NOMBRE_PERFIL = 'Anulacion Pagos';

DELETE FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA
    WHERE MODULO_ID=(SELECT ID_MODULO FROM DB_SEGURIDAD.SIST_MODULO WHERE NOMBRE_MODULO = 'infopagocab/anulacionPagos')
    AND ACCION_ID=(SELECT ID_ACCION FROM DB_SEGURIDAD.SIST_ACCION WHERE NOMBRE_ACCION =  'indexAnulacionPagos')
    AND ITEM_MENU_ID=(SELECT ID_ITEM_MENU FROM DB_SEGURIDAD.SIST_ITEM_MENU WHERE NOMBRE_ITEM_MENU ='Anulacion Pagos');

DELETE FROM DB_SEGURIDAD.SIST_ACCION 
    WHERE NOMBRE_ACCION =  'indexAnulacionPagos';

DELETE FROM DB_SEGURIDAD.SIST_MODULO 
    WHERE NOMBRE_MODULO = 'infopagocab/anulacionPagos';

DELETE FROM DB_SEGURIDAD.SIST_ITEM_MENU 
    WHERE NOMBRE_ITEM_MENU ='Anulacion Pagos';


