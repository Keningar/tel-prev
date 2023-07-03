
/*
* Rollback del DML (DML_19122019_cjaramilloe.pks)
* @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
* @version 1.0 19-12-2019
*/

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA T
    WHERE T.DESCRIPCION_CARACTERISTICA = 'SOLICITUD_CAMBIO_MASIVO_VENDEDOR_ORIGEN'
    AND T.USR_CREACION = 'cjaramilloe';

DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD T
    WHERE T.DESCRIPCION_SOLICITUD = 'SOLICITUD CAMBIO MASIVO CLIENTES VENDEDOR'
    AND T.USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET T
    WHERE T.DESCRIPCION = 'Solicitud Cambio Masivo Clientes Vendedor'
    AND T.USR_CREACION = 'cjaramilloe';

COMMIT;

/