/**
 * Documentación INSERT DE PARAMETRO NUMERO DE DIAS PRECEDENTES
 *
 * Se inserta parámetro para definir el limite de numero de dias precedentes
 * a la fecha actual para mostrar los registros del listado (grid) de clientes.
 *
 * @author Emilio Flores <eaflores@telconet.ec>
 * @version 1.0 18-04-2023
 */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'N_DIAS_PRECEDENTES',
    'LIMITE DE DIAS PRECEDENTES A FECHA ACTUAL',
    'COMERCIAL',
    NULL,
    'Activo',
    'eaflores',
    SYSDATE,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'N_DIAS_PRECEDENTES'
    ),
    'LIMITE DE DIAS PRECEDENTES EN LA BANDEJA COMERCIAL CLIENTE',
    '30',
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'eaflores',
    SYSDATE,
    '127.0.0.1',
    NULL
);

COMMIT;
/