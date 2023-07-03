/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para regularizar las caracteristica de los servicios(planes/productos) activos atados a un producto konibit
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0
 * @since 27-07-2022
 */


DECLARE
 --Obtener todos los servicios(Planes y Productos) que esten atados a un producto konibit
 CURSOR C_ServiciosKonibit IS
           SELECT serv.id_servicio
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
            AND s.estado IN ('Activo', 'In-Corte');
    TYPE servicio IS TABLE OF C_ServiciosKonibit%ROWTYPE INDEX BY PLS_INTEGER;
       

  --Obtener el Id producto de los productos konibit para consultar el id_producto_caracterisitica para insertar en la tabla info_servicio_prod_caract 
  CURSOR C_ProductoIdProd(Cn_IdServicio db_comercial.info_servicio.id_servicio%TYPE) IS
            SELECT serv.producto_id
            FROM  db_comercial.info_servicio serv
            WHERE serv.id_servicio = Cn_IdServicio;
            
  --Obtener el Id producto de los planes que tienen incluido konibit para consultar el id_producto_caracterisitica para insertar en la tabla info_servicio_prod_caract 
  CURSOR C_ProductoIdPlan(Cn_IdServicio db_comercial.info_servicio.id_servicio%TYPE) IS
            SELECT Plan_Det.Producto_Id 
            FROM db_comercial.info_servicio info
            INNER JOIN db_comercial.info_plan_cab plan_cab
            ON info.plan_id = plan_cab.id_plan
            INNER JOIN db_comercial.info_plan_det plan_det
            ON plan_det.plan_id = plan_cab.id_plan
            WHERE Plan_Det.Producto_Id IN (1262, 1263)
            AND plan_cab.estado = plan_det.estado
            AND info.estado IN ('Activo', 'In-Corte')
            AND info.id_servicio = Cn_IdServicio;
            
  --Obtener id_producto_caracterisitica 
  CURSOR C_ProductoCaract(Cn_ProductoId db_comercial.info_servicio.producto_id%TYPE) IS
            SELECT pc.id_producto_caracterisitica
            FROM db_comercial.admi_producto_caracteristica pc
            WHERE pc.caracteristica_id = 1733
            AND pc.producto_id = Cn_ProductoId;
            
  --verificar si existe o no el registro de la caracteristica en la tabla info_servicio_prod_caract para  para evitar duplicidad en el insert        
  CURSOR C_ExistProdCara(Cn_ProdCarac db_comercial.info_servicio_prod_caract.producto_caracterisitica_id%TYPE, Cn_IdServicio db_comercial.info_servicio.id_servicio%TYPE) IS          
            SELECT COUNT(*) FROM db_comercial.info_servicio_prod_caract s
            WHERE s.producto_caracterisitica_id = Cn_ProdCarac
            And s.servicio_id = Cn_IdServicio
            AND s.estado = 'Activo';


 Lc_Servicio       servicio;
 Ln_limiteBulk     CONSTANT PLS_INTEGER DEFAULT 5000;
 Ln_IdServicio     NUMBER := 0;
 Ln_IdProductoProd NUMBER := 0;
 Ln_IdProductoPlan NUMBER := 0;
 Ln_IdProducto     NUMBER := 0;
 Ln_IdProducCarac  NUMBER := 0;
 Ln_ExisteProd     NUMBER := 0;
 Ln_limite_Commit  NUMBER := 500;
 Ln_Cont           NUMBER := 0;
 i                 PLS_INTEGER := 0;
 
BEGIN


   OPEN C_ServiciosKonibit;
    LOOP
      FETCH C_ServiciosKonibit BULK COLLECT INTO Lc_Servicio LIMIT Ln_limiteBulk;
      EXIT WHEN Lc_Servicio.COUNT = 0;
      BEGIN
      i := Lc_Servicio.FIRST;
        WHILE (i IS NOT NULL) LOOP
          BEGIN
                  --se abre el cursor para obtner el idproducto del producto konibit en caso que no exista se busca el idproducto en los planes 
                  OPEN  C_ProductoIdProd(Lc_Servicio(i).id_servicio);
                  FETCH C_ProductoIdProd INTO Ln_IdProductoProd;
                  
                  IF C_ProductoIdProd%NOTFOUND THEN 
                    
                        OPEN  C_ProductoIdPlan(Lc_Servicio(i).id_servicio);
                        FETCH C_ProductoIdPlan INTO Ln_IdProductoPlan;
                        IF C_ProductoIdPlan%NOTFOUND THEN 
                            Ln_IdProducto := Ln_IdProductoPlan;     
                        END IF;
                    
                        CLOSE C_ProductoIdPlan;
                  ELSE
                        Ln_IdProducto := Ln_IdProductoProd;
                  END IF;
                  CLOSE C_ProductoIdProd;  
                  
                  --Se obtiene el idProductoCaracteristica para insertar en la tabla info_servicio_prod_caract
                  OPEN  C_ProductoCaract(Ln_IdProducto);
                  FETCH C_ProductoCaract INTO Ln_IdProducCarac;
                  CLOSE C_ProductoCaract;
                  
                  --Se verifica que el registro no existe en la tabla para insertar. Se lo consulta por servicio y idProductoCaracteristica y estado 
                  OPEN  C_ExistProdCara(Ln_IdProducCarac,Lc_Servicio(i).id_servicio);
                  FETCH C_ExistProdCara INTO Ln_ExisteProd;
                  CLOSE C_ExistProdCara;
                  
                  --Si el registro no existe Ln_ExisteProd = 0, caso contrario es = 1.
                  IF (Ln_ExisteProd = 0) THEN
                      
                      INSERT INTO db_comercial.info_servicio_prod_caract (
                        id_servicio_prod_caract,
                        servicio_id,
                        producto_caracterisitica_id,
                        valor,
                        fe_creacion,
                        usr_creacion,
                        estado
                      ) VALUES (
                        db_comercial.seq_info_servicio_prod_caract.nextval,
                        Lc_Servicio(i).id_servicio,
                        Ln_IdProducCarac,
                        'SI',
                         sysdate,
                        'Regu_konibit',
                        'Activo'
                      );
                      Ln_Cont := Ln_Cont + 1;
                    
                  END IF;
                  
                  --Cada 500 registros se hace el commit
                  IF Ln_Cont = Ln_limite_Commit THEN
                          COMMIT;
                          Ln_Cont := 0;
                  END IF;
              
          EXCEPTION
            WHEN OTHERS THEN
                  dbms_output.put_line('ERROR. ' || sqlerrm);
          END;
        i := Lc_Servicio.NEXT(i);
        END LOOP;
      END;
    END LOOP;
   CLOSE C_ServiciosKonibit;
   
   COMMIT;
    dbms_output.put_line('Proceso Finalizado');
   
EXCEPTION
    WHEN OTHERS THEN
        dbms_output.put_line('ERROR NO CONTROLADO. ' || sqlerrm);
        ROLLBACK;
    
    
END;

/