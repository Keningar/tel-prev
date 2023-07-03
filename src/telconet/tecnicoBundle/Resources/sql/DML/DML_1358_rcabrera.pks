/*Se configuran los productos de seguridad logica que seran considerados para el registro de equipos*/


--Se ingresa la caracteristica 'REGISTRO EQUIPO'
INSERT
INTO db_comercial.ADMI_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_CARACTERISTICA.nextval,
    'REGISTRO EQUIPO',
    'N',
    'Activo',
    sysdate,
    'rcabrera',
    sysdate,
    'rcabrera',
    'TECNICA'
  );

--Se asocia la caracteristica con los productos de seguridad logica
INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1074,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1064,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1073,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1066,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1068,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1067,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1071,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );

INSERT
INTO db_comercial.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.nextval,
    1069,
    (SELECT ID_CARACTERISTICA
    FROM db_comercial.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'REGISTRO EQUIPO'
    ),
    sysdate,
    NULL,
    'rcabrera',
    NULL,
    'Activo',
    'NO'
  );


commit;

/
