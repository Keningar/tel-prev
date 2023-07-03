/**
 * Documentación para crear características y modificar características para 
 * producto CLEAR CHANNEL PUNTO A PUNTO
 *
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 02-04-2023
 */


 INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'HABILITAR_APROVISIO_CLEAR_CHANNEL', 
    'HABILITAR_APROVISIO_CLEAR_CHANNEL', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

--INSERT ADMI_PARAMETRO_DET

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
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
            nombre_parametro = 'HABILITAR_APROVISIO_CLEAR_CHANNEL' 
    ),
    'CLEAR CHANNEL PUNTO A PUNTO', 
    'NO', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;

/
