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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Manga - Caja - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'MANGA',
        'NODO',
	'RESERVA,MANGA,CAJA DISPERSION,ODF',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Nodo - ODF - Reserva - CDP - Manga - CDP',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'TRADICIONALES' ),
        'NODO',
        'CDP',
	'ODF,RESERVA,CDP,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Nodo - ODF - Reserva - CDP - Manga - Manga',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'TRADICIONALES' ),
        'NODO',
        'MANGA',
	'ODF,RESERVA,CDP,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Nodo - ODF - Reserva - CDP - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'TRADICIONALES' ),
        'NODO',
        'CAJA DISPERSION',
	'ODF,RESERVA,CDP,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'URBANIZACION' ),
        'MANGA',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Caja - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'URBANIZACION' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'CJTO' ),
        'MANGA',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Caja - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'CJTO' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'EDIFICIO' ),
        'MANGA',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Caja - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'EDIFICIO' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'CENTRO COMERCIAL' ),
        'MANGA',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Caja - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'CENTRO COMERCIAL' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Manga - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'LDD' ),
        'MANGA',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
            AND ESTADO = 'Activo'
        ),
        'Caja - Reserva - Caja - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'LDD' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,CAJA DISPERSION,MANGA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

-- Insertar el elemento Reserva para combo de elementos
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'ELEMENTOS_PASIVO_RUTA'
            AND ESTADO = 'Activo'
        ),
        'COMBO_ELEMENTOS',
        'RESERVA',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'ELEMENTOS_PASIVO_RUTA'
            AND ESTADO = 'Activo'
        ),
        'COMBO_ELEMENTOS',
        'CDP',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- Creación de parámetros para el proyecto de almacenamiento NFS
INSERT INTO db_general.ADMI_GESTION_DIRECTORIOS
(
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    db_general.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    4,
    28, --33
    'TelcosWeb',
    '593',
    'TN',
    'Tecnico',
    'SubidaRutas',
    'Activo',
    sysdate,
    'afayala'
);



COMMIT;

/

