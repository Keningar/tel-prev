/**
 * @author Ivan Romero<icromero@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se actualiza la vista VISTA_ESTADO_CUENTA_RESUMIDO se agrega alias en subquery de consulta de pagos reduce el costo de 800k a 500k segun pruebas realizada con Luis Lindao en ambiente prod
 *
 * @author Ivan Romero <icromero@telconet.ec>
 */

CREATE or REPLACE VIEW DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO
AS SELECT
ESTADO_CUENTA.PUNTO_ID, SUM(ESTADO_CUENTA.VALOR_TOTAL) SALDO
FROM
(SELECT

DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.punto_id,
round(DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.valor_total,2) as valor_total
FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB
WHERE DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.estado_impresion_fact
NOT IN ('Inactivo', 'Anulado','Anulada','Rechazada','Rechazado','Pendiente','Aprobada','Eliminado','ErrorGasto','ErrorDescuento','ErrorDuplicidad') AND TIPO_DOCUMENTO_ID not in (6,8)
UNION ALL
SELECT

DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.punto_id,
round(DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.valor_total,2)*-1 as valor_total
FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB
WHERE DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.estado_impresion_fact
NOT IN ('Inactivo', 'Anulado','Anulada','Rechazada','Rechazado','Pendiente','Aprobada','Eliminado') AND TIPO_DOCUMENTO_ID in (6,8)
UNION ALL
SELECT
DB_FINANCIERO.INFO_PAGO_CAB.punto_id,
round(DB_FINANCIERO.INFO_PAGO_DET.valor_pago,2)*-1 as valor_pago
FROM DB_FINANCIERO.INFO_PAGO_CAB,
DB_FINANCIERO.INFO_PAGO_DET
WHERE DB_FINANCIERO.INFO_PAGO_CAB.estado_pago NOT IN ('Inactivo', 'Anulado','Asignado')
AND DB_FINANCIERO.INFO_PAGO_CAB.id_pago = DB_FINANCIERO.INFO_PAGO_DET.pago_id
AND NOT EXISTS( SELECT anto.id_pago
                FROM DB_FINANCIERO.INFO_PAGO_CAB anto
                WHERE DB_FINANCIERO.INFO_PAGO_CAB.ANTICIPO_ID=anto.ID_PAGO 
                AND anto.ESTADO_PAGO='Cerrado'
                AND anto.TIPO_DOCUMENTO_ID = 10
              )     
) ESTADO_CUENTA
GROUP BY ESTADO_CUENTA.PUNTO_ID;
COMMIT;