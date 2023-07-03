/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para rollback para eliminar las regularizaciones de las caracteristica de los servicios(planes/productos) activos atados a un producto konibit
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0
 * @since 27-07-2022
 */
DELETE
FROM db_comercial.info_servicio_prod_caract
WHERE servicio_id IN
  ( SELECT serv.id_servicio
            FROM  db_comercial.info_servicio serv
            WHERE serv.producto_id IN ( 1262, 1263 )
            AND serv.estado IN ( 'Activo', 'In-Corte' )
        UNION
            SELECT s.id_servicio 
            FROM db_comercial.info_servicio s
            WHERE s.plan_id IN (SELECT PLAN.id_plan
                                FROM db_comercial.Info_Plan_Cab Plan
                                INNER JOIN db_comercial.info_plan_det plan_det
                                ON plan_det.plan_id = plan.id_plan
                                WHERE plan_det.producto_id in (1262, 1263)
                                AND plan_det.estado = Plan.estado)
            AND s.estado IN ('Activo', 'In-Corte')
  )
  AND estado = 'Activo'
  AND usr_creacion = 'Regu_konibit';
  
COMMIT;