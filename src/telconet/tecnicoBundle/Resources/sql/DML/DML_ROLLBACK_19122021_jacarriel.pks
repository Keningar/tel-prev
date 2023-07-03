/*
 *
 * Se elimina cabecera y detalle de nuevo parametro de tipos de servicios de telconet.
 *	 
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 29-11-2021
 *
*/

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB CABECERA
WHERE
    NOMBRE_PARAMETRO = 'SERVICIOS DE TELCONET';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'SERVICIOS DE TELCONET'
    )
    AND DESCRIPCION = 'PARAMETRO_DE_SERVICIOS_DE_TELCONET';

commit;