/**
 * DML PARA REVERSAR LOS REGISTROS INGRESADOS.
 */

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '1';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '2';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '647';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '816';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '1188';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'TELEWORKER') AND CARACTERISTICA_ID = '1189';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'IP TELEWORKER') AND CARACTERISTICA_ID = '640';

DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID =
  (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'IP TELEWORKER') AND CARACTERISTICA_ID = '647';

  UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  SET VISIBLE_COMERCIAL = 'NO'
  WHERE CARACTERISTICA_ID = 1213 AND PRODUCTO_ID = 1271;
  
  UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  SET VISIBLE_COMERCIAL = 'NO'
  WHERE CARACTERISTICA_ID = 1213 AND PRODUCTO_ID = 1272;

  UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  SET VISIBLE_COMERCIAL = 'NO'
  WHERE CARACTERISTICA_ID = 942 AND PRODUCTO_ID = 1272;
  
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET VALOR5 = null, VALOR6 = null
  WHERE ID_PARAMETRO_DET = 10313;


COMMIT;

/
