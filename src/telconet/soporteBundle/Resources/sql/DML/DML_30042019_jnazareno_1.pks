--SE INSERTA NUEVA CARACTERISTICA PARA REQUIERE_MATERIAL
--
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) 
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'REQUIERE_MATERIAL',
    'C',
    'Activo',
    SYSDATE,
    'jnazareno',
    NULL,
    NULL,
    'SOPORTE'
);

--SE INSERTA NUEVA CARACTERISTICA PARA REQUIERE_RUTA_FIBRA
--
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) 
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'REQUIERE_RUTA_FIBRA',
    'C',
    'Activo',
    SYSDATE,
    'jnazareno',
    NULL,
    NULL,
    'SOPORTE'
);

COMMIT;

/