
    /*
    * Rollback del DML (DML_29122022_kbaque.pks)
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 29-12-2022
    */
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE
        PARAMETRO_ID = (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
        )
    AND DESCRIPCION IN ( 'URL_TELCOCRM' );
    COMMIT;
    /