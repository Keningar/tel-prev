--Caracteristica para el producto

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
                                       ID_CARACTERISTICA,
                                       DESCRIPCION_CARACTERISTICA,
                                       TIPO_INGRESO,
                                       ESTADO,
                                       FE_CREACION,
                                       USR_CREACION,
                                       FE_ULT_MOD,
                                       USR_ULT_MOD,
                                       TIPO,
                                       DETALLE_CARACTERISTICA
                                      )
VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'Relacionar Proyecto',
    'S',
    'Activo',
    sysdate,
    'banton',
    NULL,
    NULL,
    'COMERCIAL',
    NULL
  );
 --Agregamos a todos los productos de TN la característica.

 INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    SELECT
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ID_PRODUCTO,
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='Relacionar Proyecto'),
    SYSDATE,
    'banton',
    'Activo',
    'SI'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' AND EMPRESA_COD=10);   
    

    
COMMIT;
/
