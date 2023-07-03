/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback cabecera y detalle de parametros para registro de ips controladoras
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 10-1-2023 - Versión Inicial.
 */

--1
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'CISCO'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';

--2
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'HUAWEI'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';

--3
DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE valor1 = 'RUCKUS'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';

--4
DELETE FROM DB_GENERAL.admi_parametro_cab 
  WHERE  nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';

DELETE FROM db_comercial.admi_producto_caracteristica 
WHERE producto_id = (SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'WIFI GPON'
            AND empresa_cod = 10
            AND nombre_tecnico = 'SAFECITYWIFI'
            AND estado = 'Activo')
AND caracteristica_id = (SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'IP CONTROLADORA');
            

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
WHERE DESCRIPCION_CARACTERISTICA = 'IP CONTROLADORA';

COMMIT;

/

