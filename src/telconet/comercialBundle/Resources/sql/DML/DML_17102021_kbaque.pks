
    /*
    * Se realiza la inserción de parámetros.
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 17-10-2021
    */
    --Ingreso de la cabecera para los parámetros necesarios para la interacción con CLOUD_FORM.
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
        'PARAMETROS_CLOUD_FORM',
        'PARAMETROS AUXILIARES PARA LOS PRODUCTOS CLOUD_FORM',
        'COMERCIAL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1'
    );
    --Ingresos de listado de los productos que se van a considerar para el consumo del mismo.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD,
        OBSERVACION
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_CLOUD_FORM' AND ESTADO = 'Activo'),
        'LISTADO_PRODUCTOS',
        1181,
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10,
        'Valor1: Id del producto de la tabla AdmiProducto'
    );
    COMMIT;
    /