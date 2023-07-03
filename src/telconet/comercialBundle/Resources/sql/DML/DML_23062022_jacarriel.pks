/**
 * Documentación para quitar propiedad requiere equipo FORTI INTERNET SAFE
 *
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 23-06-2022
 */

 --Actualizar parametros producto INTERNET SAFE
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2 = '' WHERE DESCRIPCION = 'EQUIPOS EN PRODUCTO TN' AND valor1 = 'INTERNET SAFE';

COMMIT;

/
