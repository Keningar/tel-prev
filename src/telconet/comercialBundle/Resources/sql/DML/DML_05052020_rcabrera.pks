/*
* @author Richard Cabrera <rcabrera@telconet.ec>
* @version 1.0 05-05-2020 - Se crea la solicitud 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y
*                          'SOLICITUD AGREGAR EQUIPO MASIVA', adicional se crea el parametro CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO
*/
INSERT INTO db_comercial.admi_tipo_solicitud VALUES (
    db_comercial.seq_admi_tipo_solicitud.nextval,
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    SYSDATE,
    'rcabrera',
    SYSDATE,
    'rcabrera',
    'Activo',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_comercial.admi_tipo_solicitud VALUES (
    db_comercial.seq_admi_tipo_solicitud.nextval,
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    SYSDATE,
    'rcabrera',
    SYSDATE,
    'rcabrera',
    'Activo',
    NULL,
    NULL,
    NULL
);


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO',
    'CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO',
    'TECNICO',
    'SOLICITUD DE AGREGAR EQUIPO',
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
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
    WHERE NOMBRE_PARAMETRO = 'CANTIDAD_MAXIMA_SOL_AGREGAR_EQUIPO'
    ),
    'CANTIDAD MAXIMA',
    '2000',
    NULL,
    NULL,
    NULL,
    'Activo',
    'rcabrera',
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


COMMIT;

/
