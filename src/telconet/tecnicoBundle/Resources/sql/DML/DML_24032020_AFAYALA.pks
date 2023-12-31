-- Insertar en la tabla ADMI_PARAMETRO_CAB
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROD_VELOCIDAD_TELEWORKER',
    'PROD_VELOCIDAD_TELEWORKER',
    'TECNICO',
    'TELEWORKER',
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );
-- Insertar en la tabla ADMI_PARAMETRO_DET
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_TELEWORKER'
    ),
    'VELOCIDAD 5 MB',  -- DESCRIPCION
    '5', --VALOR1
    'MB', --VALOR2
    NULL,
    NULL,
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_TELEWORKER'
    ),
    'VELOCIDAD 10 MB',  -- DESCRIPCION
    '10', --VALOR1
    'MB', --VALOR2
    NULL,
    NULL,
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_TELEWORKER'
    ),
    'VELOCIDAD 20 MB',  -- DESCRIPCION
    '20', --VALOR1
    'MB', --VALOR2
    NULL,
    NULL,
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );

update DB_COMERCIAL.ADMI_PRODUCTO set FUNCION_PRECIO='if ([VELOCIDAD]==5) { PRECIO = 19.00; } else if ([VELOCIDAD]==10) { PRECIO = 24.99; } else if ([VELOCIDAD]==20) { PRECIO = 33.40; }' 
where ID_PRODUCTO=1271;--if ([VELOCIDAD]==10) { PRECIO = 24.99; }

update DB_COMERCIAL.ADMI_PRODUCTO set FUNCION_PRECIO='if ([VELOCIDAD]==5) { PRECIO = 2.00; } else if ([VELOCIDAD]==10) { PRECIO = 0.01; } else if ([VELOCIDAD]==20) { PRECIO = 2.00; }' 
where ID_PRODUCTO=1272;--if ([VELOCIDAD]==10) { PRECIO = 0.01; }

COMMIT;

/
