    /*
    * Se realiza el script del reverso de los parámetros.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 17-10-2021
    */

    --Eliminamos el detalle de los parametros
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE
        PARAMETRO_ID = (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_CLOUD_FORM'
        );
    --Eliminamos la cabecera.
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE
        NOMBRE_PARAMETRO = 'PARAMETROS_CLOUD_FORM';
    COMMIT;
    /