INSERT INTO db_tokensecurity.user_token VALUES (
    (
        SELECT
            MAX(id_user_token) + 1
        FROM
            db_tokensecurity.user_token
    ),
    'MOVIL_COMERCIAL',
    '38083C7EE9121E17401883566A148AA5C2E2D55DC53BC4A94A026517DBFF3C6B',
    'Activo',
    462
);

INSERT INTO db_tokensecurity.web_service VALUES (
    (
        SELECT
            MAX(id_web_service) + 1
        FROM
            db_tokensecurity.web_service
    ),
    'ComercialMobileWSController',
    'procesar',
    1,
    'ACTIVO',
    462
);

INSERT INTO db_tokensecurity.web_service VALUES (
    (
        SELECT
            MAX(id_web_service) + 1
        FROM
            db_tokensecurity.web_service
    ),
    'SmsWSController',
    'procesarAction',
    1,
    'ACTIVO',
    462
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    FE_CREACION,
    USR_CREACION,
    TIPO,
    ESTADO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'VISUALIZAR_EN_MOVIL',
    'T',
     SYSDATE,
    'epin',
    'COMERCIAL',
    'Activo'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, ID_PRODUCTO, (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'VISUALIZAR_EN_MOVIL'), sysdate, null, 'epin', null, 'Activo', 'SI' 
FROM DB_COMERCIAL.ADMI_PRODUCTO
WHERE EMPRESA_COD = '18'
  AND ESTADO = 'Activo';

COMMIT;


/