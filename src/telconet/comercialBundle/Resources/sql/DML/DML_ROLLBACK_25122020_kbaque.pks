
    /*
    * Rollback del DML (DML_25122020_kbaque.pks)
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 25-12-2020
    */

    --Eliminamos el detalle de los parametros.
    DELETE
    FROM
        DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE
        PARAMETRO_ID = (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        )
        AND DESCRIPCION = 'CORREO_POR_LINEA_NEGOCIO';

    COMMIT;
    /