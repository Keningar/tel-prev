--EJECUTAR EN DB_COMERCIAL
-- Agregar la caracteristica ANALITICA_CONSUMO_WS
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ANALITICA_CONSUMO_WS',
    'S',
    'Activo',
    SYSDATE,
    'lemero',
    NULL,
    NULL,
    'TECNICO',
    NULL
  ); 
  
-- Agregar la caracteristica FORMATO_RESOLUCION 
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FORMATO_RESOLUCION',
    'S',
    'Activo',
    SYSDATE,
    'lemero',
    NULL,
    NULL,
    'TECNICO',
    NULL
  ); 

-- Agregar la caracteristica TIPO_CAMARA
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TIPO_CAMARA',
    'S',
    'Activo',
    SYSDATE,
    'lemero',
    NULL,
    NULL,
    'TECNICO',
    NULL
  ); 

-- Agregar la caracteristica POSICION_CAMARA
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'POSICION_CAMARA',
    'S',
    'Activo',
    SYSDATE,
    'lemero',
    NULL,
    NULL,
    'TECNICO',
    NULL
  ); 

--Relacionar la caracteristica ANALITICA_CONSUMO_WS con el servicio SAFECITYDATOS
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
     DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE ADMI_PRODUCTO.NOMBRE_TECNICO = 'SAFECITYDATOS'
    AND ESTADO                         ='Activo'
    ),
    (SELECT DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'ANALITICA_CONSUMO_WS'
    AND ESTADO                       ='Activo'
    AND ROWNUM = 1
    ),
    SYSDATE,
    NULL,
    'lemero',
    NULL,
    'Activo',
    'NO'
  );

--Relacionar la caracteristica FORMATO_RESOLUCION con el servicio SAFECITYDATOS
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
     DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE ADMI_PRODUCTO.NOMBRE_TECNICO = 'SAFECITYDATOS'
    AND ESTADO                         ='Activo'
    ),
    (SELECT DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'FORMATO_RESOLUCION'
    AND ESTADO                       ='Activo'
    AND ROWNUM = 1
    ),
    SYSDATE,
    NULL,
    'lemero',
    NULL,
    'Activo',
    'NO'
  );
  
--Relacionar la caracteristica TIPO_CAMARA con el servicio SAFECITYDATOS
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
     DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE ADMI_PRODUCTO.NOMBRE_TECNICO = 'SAFECITYDATOS'
    AND ESTADO                         ='Activo'
    ),
    (SELECT DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_CAMARA'
    AND ESTADO                       ='Activo'
    AND ROWNUM = 1
    ),
    SYSDATE,
    NULL,
    'lemero',
    NULL,
    'Activo',
    'NO'
  );

--Relacionar la caracteristica POSICION_CAMARA con el servicio SAFECITYDATOS
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
     DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE ADMI_PRODUCTO.NOMBRE_TECNICO = 'SAFECITYDATOS'
    AND ESTADO                         ='Activo'
    ),
    (SELECT DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'POSICION_CAMARA'
    AND ESTADO                       ='Activo'
    AND ROWNUM = 1
    ),
    SYSDATE,
    NULL,
    'lemero',
    NULL,
    'Activo',
    'NO'
  );
  

COMMIT;
/