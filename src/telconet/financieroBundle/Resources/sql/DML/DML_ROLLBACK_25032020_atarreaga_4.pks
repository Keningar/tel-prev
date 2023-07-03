/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 25-03-2020    
 * Se crea DML para reversar configuraciones de item menú de reporte Tributario Banco Gye.
 */

DELETE FROM DB_SEGURIDAD.SEGU_ASIGNACION 
WHERE PERFIL_ID = (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Reporte Tributario de Bco. Guayaquil')
    AND RELACION_SISTEMA_ID = (SELECT ID_RELACION_SISTEMA 
                               FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = (SELECT ID_MODULO 
                                                                                    FROM DB_SEGURIDAD.SIST_MODULO 
                                                                                    WHERE NOMBRE_MODULO = 'mostrarReporteTributario')); 

DELETE FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA
WHERE MODULO_ID  = (SELECT ID_MODULO FROM DB_SEGURIDAD.SIST_MODULO WHERE NOMBRE_MODULO = 'mostrarReporteTributario' AND ESTADO = 'Activo')
    AND ITEM_MENU_ID = (SELECT ID_ITEM_MENU FROM DB_SEGURIDAD.SIST_ITEM_MENU WHERE NOMBRE_ITEM_MENU ='Reporte Tributario Bco. Guayaquil' AND ESTADO = 'Activo'); 


DELETE FROM DB_SEGURIDAD.SIST_MODULO WHERE NOMBRE_MODULO = 'mostrarReporteTributario';

DELETE FROM DB_SEGURIDAD.SIST_ITEM_MENU WHERE NOMBRE_ITEM_MENU = 'Reporte Tributario Bco. Guayaquil';

DELETE FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA WHERE PERFIL_ID = (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Reporte Tributario de Bco. Guayaquil');    

DELETE FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Reporte Tributario de Bco. Guayaquil';

COMMIT;
/
