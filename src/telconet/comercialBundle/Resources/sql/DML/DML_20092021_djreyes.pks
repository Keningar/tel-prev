-- Scripts para comercial
-- Actualizamos datos del nuevo producto GolTv
UPDATE ADMI_PRODUCTO
SET CODIGO_PRODUCTO = 'GTV1', NOMBRE_TECNICO = 'GTVPREMIUM'
WHERE ID_PRODUCTO = '1407'
AND CODIGO_PRODUCTO = 'GOL'
AND DESCRIPCION_PRODUCTO = 'GOLTV PLAY'
AND NOMBRE_TECNICO = 'OTROS';

-- Caracteristicas del producto
-- CAR 1 - Producto
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'GTVPREMIUM','T','Activo',sysdate,'djreyes','COMERCIAL'
);
-- CAR 2 - SSID
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SSID_GOLTV','T','Activo',sysdate,'djreyes','TECNICA'
);
-- CAR 3 - Usuario
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'USUARIO_GOLTV','T','Activo',sysdate,'djreyes','TECNICA'
);
-- CAR 4 - Clave
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PASSWORD_GOLTV','T','Activo',sysdate,'djreyes','TECNICA'
);
-- CAR 5 - Migrado
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'MIGRADO_GOLTV','T','Activo',sysdate,'djreyes','COMERCIAL'
);

-- ProductoCarateristicas
-- PCAR 1 - Producto
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'GTVPREMIUM'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);
-- PCAR 2 - SSID
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'SSID_GOLTV'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);
-- PCAR 3 - Usuario
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'USUARIO_GOLTV'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);
-- PCAR 4 - Clave
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'PASSWORD_GOLTV'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);
-- PCAR 5 - Migrado
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'MIGRADO_GOLTV'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);

-- PCAR 6 - Fecha minima de suscripcion
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'FECHA_MINIMA_SUSCRIPCION'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);

-- PCAR 7 - Fecha de activacion
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
	(
        SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
        where NOMBRE_TECNICO = 'GTVPREMIUM'
    ),
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'FECHA_ACTIVACION'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);

COMMIT;
/