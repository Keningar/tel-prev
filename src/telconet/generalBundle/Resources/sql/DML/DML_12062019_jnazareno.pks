--CREACION DE PARAMETRO DE LA RUTA DE IMAGENES TM-OPERACIONES
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'RUTA_BASE_IMG_TM_OPERACIONES',
    'RETORNA LA RUTA BASE DE LAS IMAGENES DE TM-OPERACIONES',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'RUTA_BASE_IMG_TM_OPERACIONES'
            ),
    'RUTA BASE DE LAS IMAGENES TM-OPERACIONES',
    'http://images.telconet.net/others/telcos/tm-operaciones/',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--CREACION DE PARAMETRO DE LA RUTA DE IMAGENES TM-OPERACIONES
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'DPT_SIN_FILTRO',
    'PARAMETRO PARA RETORNAR LOS DEPARTAMENTOS QUE NO SE FILTRAN POR CATEGORIA',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'DPT_SIN_FILTRO'
            ),
    'PARAMETRO DE ID DEPARTAMENTO QUE NO REQUIEREN FILTO EN ARBOL DE CATEGORIAS',
    '117',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

COMMIT;
/
