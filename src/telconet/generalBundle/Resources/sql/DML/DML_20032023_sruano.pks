/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Steven Ruano <sruano@telconet.ec>
 * @version 1.0 20-03-2023
 *
 */
 
SET SERVEROUTPUT ON

--INSERT PARAMETRO PARA LISTADO_PRODUCTOS_IP_ADICIONALES
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
        'LISTA_CONFIMAR_SERVICIO_CON_SERVIDOR_ALQUILER',
        'Lista para confirmar servicio que tenga servidor de alquiler en TN',
        'TECNICO',
        'Activo',
        'sruano',
        SYSDATE,
        '127.0.0.1'
);


-- INGRESO LOS DETALLES DE LA CABECERA 'LISTADO_PRODUCTOS_IPS_ADICIONALES'
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
            WHERE NOMBRE_PARAMETRO = 'LISTA_CONFIMAR_SERVICIO_CON_SERVIDOR_ALQUILER'
            AND ESTADO = 'Activo'
        ),
        'LISTADO_SERVICIO_SERVIDORES_ALQUILER',
        'HOUSING',
        'TN',
        NULL,
        NULL,
        'Activo',
        'sruano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL
        );

COMMIT;
/
