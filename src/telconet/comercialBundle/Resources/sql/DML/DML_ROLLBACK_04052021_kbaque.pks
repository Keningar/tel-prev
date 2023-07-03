
    /*
    * Rollback del DML (DML_04052021_kbaque.pks)
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 04-05-2021
    */
    --Eliminamos el detalle
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE
        PARAMETRO_ID = (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
        );

    --Eliminamos la cabecera.
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE
        NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR';

    --Eliminamos el tipo de solicitud
    DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD
        WHERE DESCRIPCION_SOLICITUD = 'SOLICITUD DE DISTRIBUIDOR'
        AND USR_CREACION = 'kbaque';

    --Eliminamos las características de los productos
    DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    WHERE
        CARACTERISTICA_ID IN (
            SELECT
                ID_CARACTERISTICA
            FROM
                DB_COMERCIAL.ADMI_CARACTERISTICA
            WHERE
                DESCRIPCION_CARACTERISTICA = 'RAZON_SOCIAL_CLT_DISTRIBUIDOR'
        );

    DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    WHERE
        CARACTERISTICA_ID IN (
            SELECT
                ID_CARACTERISTICA
            FROM
                DB_COMERCIAL.ADMI_CARACTERISTICA
            WHERE
                DESCRIPCION_CARACTERISTICA = 'IDENTIFICACION_CLT_DISTRIBUIDOR'
        );


    --Eliminamos las características
    DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE
        DESCRIPCION_CARACTERISTICA IN (
            'ES_DISTRIBUIDOR',
            'RAZON_SOCIAL_CLT_DISTRIBUIDOR',
            'IDENTIFICACION_CLT_DISTRIBUIDOR',
            'VENDEDOR_CLT_DISTRIBUIDOR',
            'PRODUCTOS_DISTRIBUIDOR'
        )
        AND ESTADO = 'Activo';

    COMMIT;
    /