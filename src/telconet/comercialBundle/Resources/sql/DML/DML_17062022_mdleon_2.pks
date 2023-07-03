/**
 * Documentación INSERT DE NUEVA CARACTERISTICA PARA PEDIDOS
 * INSERT de parámetros en la estructura  DB_COMERCIAL.ADMI_CARACTERISTICA y DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA y 
 * DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA.
 *
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 12-06-2022
 */

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
    'PEDIDO_ID',
    'S',
    'Activo',
    sysdate,
    'mdleon',
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
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='PEDIDO_ID'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' AND EMPRESA_COD=10); 

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
    'ID_BOOM',
    'S',
    'Activo',
    sysdate,
    'mdleon',
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
    (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='ID_BOOM'),
    SYSDATE,
    'mdleon',
    'Activo',
    'NO'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' AND EMPRESA_COD=10); 
