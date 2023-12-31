/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 01-03-2021
 * Se crean las sentencias DML para insertar parámetros  relacionados con la facturación de solicitudes Netlifecam.
 */

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE ID_CARACTERISTICA IN ( SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin');

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE ID_CARACTERISTICA IN ( SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-CV206 (MINI-O)'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin');

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE ID_CARACTERISTICA IN ( SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TARJETA MICRO SD 32 GB KINGSTON'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin');


DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE ID_CARACTERISTICA IN ( SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO NETLIFECAM'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin');


DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO
WHERE ID_PRODUCTO IN ( SELECT ID_PRODUCTO
      FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'TARJETA MICRO SD'
      AND ESTADO             = 'Inactivo'
      AND USR_CREACION       = 'eholguin');



DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO
WHERE ID_PRODUCTO IN ( SELECT ID_PRODUCTO
      FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
      AND ESTADO             = 'Inactivo'
      AND USR_CREACION       = 'eholguin');



DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO
WHERE ID_PRODUCTO IN ( SELECT ID_PRODUCTO
      FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'CAMARA EZVIZ CS-CV206 (MINI-O)'
      AND ESTADO             = 'Inactivo'
      AND USR_CREACION       = 'eholguin');


COMMIT;
/
