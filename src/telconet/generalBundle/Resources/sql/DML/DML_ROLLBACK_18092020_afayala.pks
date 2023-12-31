-- ELIMINACION LOS DETALLES DE LA CABECERA 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
-- DETALLE NO REQUIERE ENLACE
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo');

-- ELIMINACION DE LA CABECERA DE PARAMETROS DE 'DETALLES_PRODUCTO_DIRECT_LINK_MPLS'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS';

/*
 * ➜ Eliminar una cabecera CARACTERISTICAS_SERVICIOS_CONFIRMACION.
 *
 * ➜ Elimina el detalle sobre el nuevo parámetro.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 22-09-2020 - Versión Inicial.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'CARACTERISTICAS_SERVICIO_CONFIRMACION');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'CARACTERISTICAS_SERVICIO_CONFIRMACION';

-- ELIMINA LOS DETALLES DE LA CABECERA 'CREAR_TAREA_INTERNA_SERVICIOS'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_SERVICIOS' AND ESTADO = 'Activo');

--ELIMINACION DE LA CABECERA DE PARAMETROS DE 'CREAR_TAREA_INTERNA_SERVICIOS'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_SERVICIOS';

-- ELIMINA LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_CORTE'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CORTE' AND ESTADO = 'Activo');

--ELIMINACION DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_CORTE'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CORTE';

-- ELIMINA LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_CANCELAR'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CANCELAR' AND ESTADO = 'Activo');

--ELIMINACION DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_CANCELAR'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CANCELAR';

-- ELIMINA LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_REACTIVACION'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_REACTIVACION' AND ESTADO = 'Activo');

--ELIMINACION DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_REACTIVACION'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_REACTIVACION';

COMMIT;
/
