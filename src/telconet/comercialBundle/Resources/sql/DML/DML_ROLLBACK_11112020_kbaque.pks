
    /*
    * Rollback del DML (DML_11112020_kbaque.pks)
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 11-11-2020
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
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
        );

    --Eliminamos la cabecera.
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE
        NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION';

    --Eliminamos el tipo de solicitud
    DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD
        WHERE DESCRIPCION_SOLICITUD = 'SOLICITUD DE REACTIVACION'
        AND USR_CREACION = 'kbaque';

    --Eliminamos las características
    DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE
        DESCRIPCION_CARACTERISTICA IN (
            'REFERENCIA_CLIENTE',
            'REFERENCIA_USUARIO',
            'REFERENCIA_USUARIO_COBRANZA',
            'REFERENCIA_TAREA',
            'REFERENCIA_SALDO_P',
            'REFERENCIA_SALDO_R'
        )
        AND ESTADO = 'Activo';

    --Eliminamos los motivo
    DELETE FROM DB_GENERAL.ADMI_MOTIVO
    WHERE
        NOMBRE_MOTIVO IN ('PENDIENTE REGULARIZACION COMERCIAL','EN GESTION LEGAL','FALTA DE ACUERDO CON EL CLIENTE')
        AND USR_CREACION = 'kbaque';

    COMMIT;
    /