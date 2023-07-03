-- INGRESO DE LA CABECERA DE PARAMETROS DE 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
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
        'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS',
        'Lista de las infraestructuras por Ruta',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'TIPO_DE_INFRAESTRUCTURA_POR_RUTAS'
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
        'Nodo - ODF - Manga - Reserva - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'INTERURBANAS' ),
        'NODO',
        'NODO',
	'ODF,MANGA,RESERVA,ODF',
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
        'Manga - Reserva - ODF - Nodo',
        ( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'INTERURBANAS' ),
        'MANGA',
        'NODO',
	'RESERVA,ODF',
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
        'Nodo - ODF - Reserva - Manga',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'INTERURBANAS' ),
        'NODO',
        'MANGA',
	'ODF,RESERVA',
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
        'Nodo - ODF - Manga - Reserva - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'BACKBONE URBANO' ),
        'NODO',
        'NODO',
	'ODF,MANGA,RESERVA,ODF',
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
        'Manga - Reserva - ODF - Nodo',
        ( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'BACKBONE URBANO' ),
        'MANGA',
        'NODO',
	'RESERVA,ODF',
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
        'Nodo - ODF - Reserva - Manga',
        ( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'BACKBONE URBANO' ),
        'NODO',
        'MANGA',
	'ODF,RESERVA',
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
        'Nodo - ODF - Reserva - Manga - Caja - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'NODO',
        'NODO',
	'ODF,RESERVA,MANGA,CAJA DISPERSION,ODF',
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
        'Nodo - ODF - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'NODO',
        'CAJA DISPERSION',
	'ODF,RESERVA,MANGA',
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
        'ODF - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'ODF',
        'CAJA DISPERSION',
	'RESERVA,MANGA',
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
        'Nodo - ODF - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'NODO',
        'ODF',
	'ODF,RESERVA,MANGA,CAJA DISPERSION',
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
        'ODF - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'ODF',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Manga - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'MANGA',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Caja - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'CAJA DISPERSION',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Caja - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,MANGA',
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
        'Manga - Reserva - Manga - Caja - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'MPLS' ),
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
        'Nodo - ODF - Reserva - Manga - Caja - ODF - Nodo',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'NODO',
        'NODO',
	'ODF,RESERVA,MANGA,CAJA DISPERSION,ODF',
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
        'Nodo - ODF - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'NODO',
        'CAJA DISPERSION',
	'ODF,RESERVA,MANGA',
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
        'ODF - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'ODF',
        'CAJA DISPERSION',
	'RESERVA,MANGA',
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
        'Nodo - ODF - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'NODO',
        'ODF',
	'ODF,RESERVA,MANGA,CAJA DISPERSION',
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
        'ODF - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'ODF',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Manga - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'MANGA',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Caja - Reserva - Manga - Caja - ODF',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'CAJA DISPERSION',
        'ODF',
	'RESERVA,MANGA,CAJA DISPERSION',
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
        'Caja - Reserva - Manga - Caja',
	( SELECT ID_TIPO_ELEMENTO 
          FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
          WHERE NOMBRE_TIPO_ELEMENTO = 'FTTH' ),
        'CAJA DISPERSION',
        'CAJA DISPERSION',
	'RESERVA,MANGA',
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

COMMIT;

/

