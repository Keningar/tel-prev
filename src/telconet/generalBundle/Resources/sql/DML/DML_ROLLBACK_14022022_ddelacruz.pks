/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar detalle de parametros para bus de pagos de TN
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0
 * @since 14-02-2022 - Versión Inicial.
 */

DELETE FROM 
db_general.admi_parametro_det
WHERE PARAMETRO_ID in (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ESTADOS_CLIENTE_CONSULTA_PL'
            AND estado = 'Activo')
AND EMPRESA_COD = '10'
AND USR_CREACION = 'ddelacruz';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = 
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO' AND USR_CREACION = 'agiraldo')
AND USR_CREACION = 'agiraldo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE 
NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'
AND MODULO = 'FINANCIERO'
AND PROCESO= 'PAGOS'
AND USR_CREACION = 'agiraldo';

commit;

/
