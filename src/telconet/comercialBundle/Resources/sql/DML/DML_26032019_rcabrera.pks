/* Se crea la caracteristica: PE-HSRP y se asocia a los servicios de internet y datos que seran usados en la activacion de un nuevo esquema llamado PE-HSRP */

--Registrar nueva caracteristica PE-HSRP
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PE-HSRP',
    'N',
    'Activo',
    sysdate,
    'rcabrera',
    NULL,
    NULL,
    'COMERCIAL'
  );


--Acosiar la caracteristica PE-HSRP a los productos: de internet y datos
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    237,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE descripcion_caracteristica = 'PE-HSRP'
    ),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    'Activo',
    'NO'
  );
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    236,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE descripcion_caracteristica = 'PE-HSRP'
    ),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    'Activo',
    'NO'
  );
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    242,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE descripcion_caracteristica = 'PE-HSRP'
    ),
    sysdate,
    sysdate,
    'rcabrera',
    'rcabrera',
    'Activo',
    'NO'
  );

commit;

/
