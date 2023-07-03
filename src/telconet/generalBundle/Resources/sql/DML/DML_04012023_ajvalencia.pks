/**
 * Documentación para crear parametros en DB_GENERAL.ADMI_PARAMETRO_CAB
 * DB_GENERAL.ADMI_PARAMETRO_DET
 *
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 03-01-2023
 * 
 */
 
 --INSERT ADMI_PARAMETRO_CAB

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
    'PROD_ANCHO BANDA', 
    'PROD_ANCHO BANDA', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'PROD_REQUIERE TRANSPORTE', 
    'PROD_REQUIERE TRANSPORTE', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'ESTADO_CLEAR_CHANNEL', 
    'ESTADO_CLEAR_CHANNEL', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'OTROS_PE', 
    'OTROS_PE', 
    'TECNICO',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'PE_TELCONET', 
    'PE_TELCONET', 
    'TECNICO',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'MODELO_BACKUP', 
    'MODELO_BACKUP', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'RANGO_VLANS_CH', 
    'RANGO_VLANS_CH', 
    'TECNICO',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'VALIDACIONES_CLEAR_CHANNEL', 
    'VALIDACIONES_CLEAR_CHANNEL', 
    'COMERCIAL',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1'
);

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
    'CARACT_BACKUP_CLEAR_CHANNEL', 
    'CARACT_BACKUP_CLEAR_CHANNEL', 
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
            nombre_parametro = 'PROD_ANCHO BANDA' 
    ),
    'PROD_ANCHO BANDA', 
    '4GE', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'PROD_ANCHO BANDA'
    ),
    'PROD_ANCHO BANDA', 
    '5GE', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'PROD_REQUIERE TRANSPORTE'
    ),
    'PROD_REQUIERE TRANSPORTE', 
    'SI', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'PROD_REQUIERE TRANSPORTE'
    ),
    'PROD_REQUIERE TRANSPORTE', 
    'NO', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
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
            nombre_parametro = 'ESTADO_CLEAR_CHANNEL' 
    ),
    'ESTADO_CLEAR_CHANNEL',
    'CLEAR CHANNEL PUNTO A PUNTO', 
    'Pendiente',
    'AsignadoTarea',
    'Asignada',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'OTROS_PE' 
    ),
    'OTROS_PE',
    'PE UFINET DCGYE',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'OTROS_PE' 
    ),
    'OTROS_PE',
    'PE UFINET GOSSEAL', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
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
            nombre_parametro = 'PE_TELCONET' 
    ),
    'PE_TELCONET_ASIGNAR',
    'pe2asruiodc.telconet.net',
    'ASR-9006-AC-V2',
    'CISCO',
    'ROUTER',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
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
            nombre_parametro = 'PE_TELCONET' 
    ),
    'PE_TELCONET_ASIGNAR',
    'pe2asrgyedc.telconet.net',
    'ASR-9006-AC-V2',
    'CISCO',
    'ROUTER',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
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
            nombre_parametro = 'PE_TELCONET' 
    ),
    'PE_TELCONET_ASIGNAR',
    'pe3asrgyedc.telconet.net',
    'ASR-9006-AC-V2',
    'CISCO',
    'ROUTER',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
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
            nombre_parametro = 'PE_TELCONET' 
    ),
    'PE_TELCONET_ASIGNAR',
    'pe1asruiodc.telconet.net',
    'NEXUS 7000',
    'CISCO',
    'SWITCH',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'MODELO_BACKUP' 
    ),
    'TIPOS_DE_BACKUP',
    'BGP',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'MODELO_BACKUP' 
    ),
    'TIPOS_DE_BACKUP',
    'Última Milla',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);





INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
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
            nombre_parametro = 'RANGO_VLANS_CH' 
    ),
    'RANGO_VLANS_CLEAR_CHANNEL',
    'WAN', 
    '1701',
    '1800', 
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'VALIDACIONES_CLEAR_CHANNEL' 
    ),
    'TIPO_PRODUCTO_MODELO',
    'CPE',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'CAPACIDAD OTN',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'Tecnologia OTN',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'Encriptados OTN',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'RUTA SDH',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'CAPACIDAD2',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'CAPACIDAD1',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'CAPACIDAD E1',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);


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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'Relacionar Proyecto',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'ANCHO BANDA',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

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
            nombre_parametro = 'CARACT_BACKUP_CLEAR_CHANNEL' 
    ),
    'CARACT_BACKUP_PRINCIPAL',
    'REQUIERE TRANSPORTE',
    'Activo',
    'ajvalencia',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;

/



