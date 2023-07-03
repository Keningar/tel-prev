/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Arcángel Farro <lfarro@telconet.ec>
 * @version 1.0 16-01-2023
 */


SET SERVEROUTPUT ON

--INSERT PARAMETRO PARA ENVIO_CORREO_BOC
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'ENVIO_CORREO_BOC',
        'REALIZA EL ENVIO DE CORREO POR PROCESO Y SERVICIO BOC EN ESPECIFICO',
        'SOPORTE',
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'ENVIO_CORREO_BOC'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    VALOR7
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'ENVIO_CORREO_BOC'
            AND ESTADO = 'Activo'
        ),
        'ENVIO CORREO PROCESO BOC',
        'BOC - ACTIVACION SERVICIOS DC',
        'SERVICIO CLOUD - VALIDACIÓN Y ACTIVACION',
        'notificaciones_cert@telconet.ec',
	    NULL,
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1',
        '10',
        NULL
);

COMMIT;
/

