/**
 *
 * Se realiza el script de reverso de los parametros para poder listar 
 * los tipos de autorizaciones que manejará la ventana de Autorización.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 16-12-2019
 */

--Eliminamos el detalle de los parametros
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'TIPOS_AUTORIZACIONES'
    );
--Eliminamos la cabecera.
DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'TIPOS_AUTORIZACIONES';

COMMIT;
/