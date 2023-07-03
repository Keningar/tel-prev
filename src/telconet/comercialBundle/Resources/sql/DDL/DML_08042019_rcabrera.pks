/* Se configura la característica SDWAN-CAMBIO_EQUIPO la cual va ser utilizada en la herramienta cambio CPE, va permitir reutilizar la serie
   de un equipo ya instalado */


--Registrar nueva caracteristica SDWAN-CAMBIO_EQUIPO
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SDWAN-CAMBIO_EQUIPO',
    'N',
    'Activo',
    sysdate,
    'rcabrera',
    NULL,
    NULL,
    'COMERCIAL'
  );
--Registrar la caracteristica relacionada a los productos
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    237,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE descripcion_caracteristica = 'SDWAN-CAMBIO_EQUIPO'
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
    WHERE descripcion_caracteristica = 'SDWAN-CAMBIO_EQUIPO'
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
    WHERE descripcion_caracteristica = 'SDWAN-CAMBIO_EQUIPO'
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

