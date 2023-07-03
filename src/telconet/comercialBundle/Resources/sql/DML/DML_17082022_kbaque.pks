/**
 *
 * Ingreso y actualización de nuevos tipos de autorizaciones y nuevos motivos para la creación, aprobación, rechazo de solicitudes.
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 */

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET
    VALOR1 = 'Autorización Cortesía'
WHERE
        PARAMETRO_ID = (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
        )
    AND VALOR2 = 'AUTORIZACION_CAMBIO_DOCUMENTO';

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Autorización Traslado',
    'SOLICITUD_TRASLADO',
    'TN',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Autorización Reubicación',
    'SOLICITUD_REUBICACION',
    'TN',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Cliente con facturación MRC mayor o igual que $3000.00USD',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Cliente con más de 10 puntos activos y facturando',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Acuerdo comercial para quitar cliente a la competencia',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Cierre de venta con facturación MRC superior o igual que $800,00USD',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'El costo de instalación, reubicación o traslado se va facturar en cuotas',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'El costo de instalación, reubicación o traslado se incluye en el valor mensual',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Cliente tiene un valor por Mbps superior al precio de lista',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '11054',
    'Primera instalación se realizó en sitio incorrecto',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET
    VALOR4 = 'TIPO'
WHERE
    PARAMETRO_ID = 1102;

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Pendiente',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'PendienteAutorizar',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'PendienteFact',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Finalizada',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Aprobado',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Rechazado',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR4,
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
            NOMBRE_PARAMETRO = 'TIPOS_AUTORIZACIONES'
    ),
    'PARAMETRO DE LOS ESTADOS DE TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Todos',
    'ESTADO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
COMMIT;
/