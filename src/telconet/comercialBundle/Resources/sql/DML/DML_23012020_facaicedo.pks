--=======================================================================
--   Ingreso de la característica 'ID_VIP_TECNICO' para la marca si corresponde el cliente a VIP Técnico
--   Ingreso de la característica 'ID_VIP_CIUDAD' para la asignación de la ciudad de los Ingenieros VIP
--   Ingreso de la cabecera de parámetro 'ID_CIUDADES_VIP_TECNICO' y los detalles de cabecera
--   para la lista de las ciudades de los Ingenieros VIP
--=======================================================================

-- INGRESO DE LA CARACTERÍSTICA 'ID_VIP_TECNICO'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ID_VIP_TECNICO',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CARACTERÍSTICA 'ID_VIP_CIUDAD'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ID_VIP_CIUDAD',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CABECERA DE PARÁMETROS PARA LAS CIUDADES DE LOS INGENIEROS VIP
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'ID_CIUDADES_VIP_TECNICO',
        'Cabecera de los ID de los cantones o ciudades que se les asignan a los Ingenieros VIP Técnico',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE CABECERA PARA CADA CIUDAD DE LOS INGENIEROS VIP
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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'ID_CIUDADES_VIP_TECNICO'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        (
            SELECT DB_GENERAL.ADMI_CANTON.ID_CANTON FROM DB_GENERAL.ADMI_CANTON
            INNER JOIN DB_GENERAL.ADMI_PROVINCIA ON DB_GENERAL.ADMI_PROVINCIA.ID_PROVINCIA = DB_GENERAL.ADMI_CANTON.PROVINCIA_ID
            INNER JOIN DB_GENERAL.ADMI_REGION    ON DB_GENERAL.ADMI_REGION.ID_REGION       = DB_GENERAL.ADMI_PROVINCIA.REGION_ID
            INNER JOIN DB_GENERAL.ADMI_PAIS      ON DB_GENERAL.ADMI_PAIS.ID_PAIS           = DB_GENERAL.ADMI_REGION.PAIS_ID
            WHERE DB_GENERAL.ADMI_PAIS.NOMBRE_PAIS           = 'ECUADOR'
            AND   DB_GENERAL.ADMI_PROVINCIA.NOMBRE_PROVINCIA = 'GUAYAS'
            AND   DB_GENERAL.ADMI_CANTON.NOMBRE_CANTON       = 'GUAYAQUIL'
        ),
        'Activo',
        'facaicedo',
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
            WHERE NOMBRE_PARAMETRO = 'ID_CIUDADES_VIP_TECNICO'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        (
            SELECT DB_GENERAL.ADMI_CANTON.ID_CANTON FROM DB_GENERAL.ADMI_CANTON
            INNER JOIN DB_GENERAL.ADMI_PROVINCIA ON DB_GENERAL.ADMI_PROVINCIA.ID_PROVINCIA = DB_GENERAL.ADMI_CANTON.PROVINCIA_ID
            INNER JOIN DB_GENERAL.ADMI_REGION    ON DB_GENERAL.ADMI_REGION.ID_REGION       = DB_GENERAL.ADMI_PROVINCIA.REGION_ID
            INNER JOIN DB_GENERAL.ADMI_PAIS      ON DB_GENERAL.ADMI_PAIS.ID_PAIS           = DB_GENERAL.ADMI_REGION.PAIS_ID
            WHERE DB_GENERAL.ADMI_PAIS.NOMBRE_PAIS           = 'ECUADOR'
            AND   DB_GENERAL.ADMI_PROVINCIA.NOMBRE_PROVINCIA = 'PICHINCHA'
            AND   DB_GENERAL.ADMI_CANTON.NOMBRE_CANTON       = 'QUITO'
        ),
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- INGRESO DE LA CABECERA DE PARÁMETROS PARA LA CANTIDAD DE NÚMEROS QUE DEBE POSEER UNA EXTENSIÓN DE TELÉFONO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CANTIDAD_NUMERO_EXTENSION',
        'Cantidad de números que debe poseer una extensión de teléfono',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO EL DETALLE PARA LA CABECERA 'CANTIDAD_NUMERO_EXTENSION'
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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CANTIDAD_NUMERO_EXTENSION'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        4,
        'Activo',
        'facaicedo',
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
