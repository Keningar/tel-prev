/**
 *
 * Se realiza el script del reverso de los parametros, caracteristicas, plantillas para el proyecto GPON.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-09-2019
 */

--Eliminamos el tipo de red.
--detalle
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_TIPO_RED'
    );
--cabecera
DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'PROD_TIPO_RED';
--Eliminamos el detalle de la relación de los productos con el tipo de red.
--detalle
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    );
--cabecera
DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'NUEVA_RED_GPON_TN';

--Eliminamos la relación de la caracteristica.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TIPO_RED'
    );
--Eliminamos la caracteristica.
DELETE FROM db_comercial.admi_caracteristica
WHERE
    descripcion_caracteristica = 'TIPO_RED';

--Eliminamos la solicitud
DELETE FROM db_comercial.admi_tipo_solicitud
WHERE
    descripcion_solicitud = 'SOLICITUD APROBACION SERVICIO TIPO RED MPLS';
--Eliminamos el motivo
DELETE FROM db_general.admi_motivo
WHERE
    nombre_motivo = 'SOLICITUD AL CREAR SERVICIO CON TIPO DE RED MPLS';
--Eliminamos la plantilla de correo
DELETE FROM db_comunicacion.admi_plantilla
WHERE
    codigo = 'AprbRchzSolMpls';
COMMIT;
/