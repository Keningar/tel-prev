/*
 *
 * Se elimina parametro para codigo de trabajo en TN.
 *	 
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 11-11-2022
 *
*/

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'
    )
    AND VALOR1 in ('CODIGO_TRABAJO_TN','TIPOS_DE_SERVICIO') ;

COMMIT;