
    /*
    * Se realiza la inserción de parámetros para validar si se permite el ingreso de la orden de servicio
    * a pesar de que el cliente tenga deuda.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 21-10-2022
    */
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'VALIDACION_CLOUD_IAAS',
        'PARAMETROS AUXILIARES QUE INTERACTUAN CON EL PRODUCTO CLOUD IAAS',
        'COMERCIAL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1'
    );
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                    NOMBRE_PARAMETRO = 'VALIDACION_CLOUD_IAAS'
                AND ESTADO = 'Activo'
        ),
        'PERMITIR_CLT_DEUDA',
        'SI',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
    COMMIT;
    /