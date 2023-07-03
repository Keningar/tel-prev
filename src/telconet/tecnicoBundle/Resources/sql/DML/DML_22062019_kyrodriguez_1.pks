/* Karen Rodríguez
* Descripción:  
* Script que regulariza la factibilidad para las máquinas vituales existentes
*/

DECLARE

                                                                         
  CURSOR C_Factibilidad iS SELECT 
                              SERVICIO.id_Servicio,
                              (SELECT RELACION.ELEMENTO_ID_A
                              FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO RELACION
                              WHERE ELEMENTO_ID_B =((SELECT SPC_H.VALOR
                                                      FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC_H
                                                      JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_H
                                                      ON APC_H.ID_PRODUCTO_CARACTERISITICA= SPC_H.PRODUCTO_CARACTERISITICA_ID
                                                      JOIN db_comercial.admi_caracteristica C1_H
                                                      ON APC_H.CARACTERISTICA_ID          =C1_H.ID_CARACTERISTICA
                                                      WHERE SPC_H.SERVICIO_ID             =SERVICIO.ID_SERVICIO
                                                      AND C1_H.DESCRIPCION_CARACTERISTICA = 'VCENTER'
                                                      AND SPC_H.ESTADO                   <>'Eliminado'
                                                    ))
                              AND RELACION.ESTADO<>'Eliminado'
                              )
                              AS
                                HYPERVIEW,
                                (SELECT SPC_H.VALOR
                                FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC_H
                                JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_H
                                ON APC_H.ID_PRODUCTO_CARACTERISITICA= SPC_H.PRODUCTO_CARACTERISITICA_ID
                                JOIN db_comercial.admi_caracteristica C1_H
                                ON APC_H.CARACTERISTICA_ID          =C1_H.ID_CARACTERISTICA
                                WHERE SPC_H.SERVICIO_ID             =SERVICIO.ID_SERVICIO
                                AND C1_H.DESCRIPCION_CARACTERISTICA = 'VCENTER'
                                AND SPC_H.ESTADO                   <>'Eliminado'
                                )
                              AS
                                VCENT,
                                (SELECT SPC_H.VALOR
                                FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC_H
                                JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC_H
                                ON APC_H.ID_PRODUCTO_CARACTERISITICA= SPC_H.PRODUCTO_CARACTERISITICA_ID
                                JOIN db_comercial.admi_caracteristica C1_H
                                ON APC_H.CARACTERISTICA_ID          =C1_H.ID_CARACTERISTICA
                                WHERE SPC_H.SERVICIO_ID             =SERVICIO.ID_SERVICIO
                                AND C1_H.DESCRIPCION_CARACTERISTICA = 'CLUSTER'
                                AND SPC_H.ESTADO                   <>'Eliminado'
                                )
                              AS
                                CLUST 
                                , MV.NOMBRE_ELEMENTO , MV.ID_ELEMENTO 
                                FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO 
                                RIGHT JOIN
                                (SELECT DETALLE_ELEMENTO.DETALLE_VALOR ,
                                  ELEMENTO.ID_ELEMENTO,
                                  ELEMENTO.NOMBRE_ELEMENTO
                                FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DETALLE_ELEMENTO
                                JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO
                                ON ELEMENTO.ID_ELEMENTO               =DETALLE_ELEMENTO.ELEMENTO_ID
                                WHERE DETALLE_ELEMENTO.DETALLE_NOMBRE = 'REF_SERVICIO_MV'
                                AND ELEMENTO.ESTADO                  <>'Eliminado'
                                AND DETALLE_ELEMENTO.ESTADO          <>'Eliminado'
                                ORDER BY DETALLE_ELEMENTO.DETALLE_VALOR ASC
                                ) MV 
                                ON MV.DETALLE_VALOR = SERVICIO.ID_SERVICIO 
                                WHERE SERVICIO.ESTADO NOT IN ('Eliminado', 'Cancel') ORDER BY SERVICIO.ID_SERVICIO , MV.NOMBRE_ELEMENTO ASC ;

                            
Ln_Servicio int                   := 0;
Lv_Hyperview  varchar2 (10)      := 0;
Lv_Vcenter  varchar2 (10)        := 0;
Lv_Cluster    varchar2 (10)      := 0;
Ln_Idmv     int                   := 0;
Lv_Nombremv varchar2 (100)       := '';
Ln_Pc_idhyperview int             := 0;
Ln_Pc_idvcenter int               := 0;
Ln_Pc_idcluster int               := 0;


BEGIN
  
 SELECT PROD_CARACT.ID_PRODUCTO_CARACTERISITICA 
 INTO
 Ln_Pc_idhyperview
 FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT 
 WHERE PROD_CARACT.CARACTERISTICA_ID  IN (SELECT CARACT.ID_CARACTERISTICA 
                                           FROM DB_COMERCIAL.ADMI_CARACTERISTICA CARACT 
                                           WHERE CARACT.DESCRIPCION_CARACTERISTICA IN ( 'HYPERVIEW')
                                           AND CARACT.ESTADO='Activo');
                                                               
 SELECT PROD_CARACT.ID_PRODUCTO_CARACTERISITICA 
 INTO
 Ln_Pc_idvcenter
 FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT 
 WHERE PROD_CARACT.CARACTERISTICA_ID  IN (SELECT CARACT.ID_CARACTERISTICA 
                                           FROM DB_COMERCIAL.ADMI_CARACTERISTICA CARACT 
                                           WHERE CARACT.DESCRIPCION_CARACTERISTICA IN ( 'VCENTER')
                                           AND CARACT.ESTADO='Activo');

 SELECT PROD_CARACT.ID_PRODUCTO_CARACTERISITICA 
 INTO
 Ln_Pc_idcluster
 FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT 
 WHERE PROD_CARACT.CARACTERISTICA_ID  IN (SELECT CARACT.ID_CARACTERISTICA 
                                           FROM DB_COMERCIAL.ADMI_CARACTERISTICA CARACT 
                                           WHERE CARACT.DESCRIPCION_CARACTERISTICA IN ( 'CLUSTER')
                                           AND CARACT.ESTADO='Activo');
                                                               
  OPEN C_Factibilidad; 
    LOOP  
      FETCH C_Factibilidad INTO Ln_Servicio, Lv_Hyperview, Lv_Vcenter, Lv_Cluster, Lv_Nombremv, Ln_Idmv; 
          /* INI Inserta  INFO_SERVICIO_PROD_CARACT de HyperView*/
          INSERT
          INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
            (
              ID_SERVICIO_PROD_CARACT,
              SERVICIO_ID,
              PRODUCTO_CARACTERISITICA_ID,
              VALOR,
              FE_CREACION,
              FE_ULT_MOD,
              USR_CREACION,
              USR_ULT_MOD,
              ESTADO,
              REF_SERVICIO_PROD_CARACT_ID
            )
            VALUES
            (
              DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
              Ln_Servicio,
              Ln_Pc_idhyperview,
              Lv_Hyperview,
              SYSDATE,
              NULL,
              'REGULARIZA_DC',
              NULL,
              'Activo',
              Ln_Idmv
            );
          /* FIN INSERTA  INFO_SERVICIO_PROD_CARACT DE HYPERVIEW*/
          
          /* INI INSERTA  INFO_SERVICIO_PROD_CARACT DE VCENTER*/
          INSERT
          INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
            (
              ID_SERVICIO_PROD_CARACT,
              SERVICIO_ID,
              PRODUCTO_CARACTERISITICA_ID,
              VALOR,
              FE_CREACION,
              FE_ULT_MOD,
              USR_CREACION,
              USR_ULT_MOD,
              ESTADO,
              REF_SERVICIO_PROD_CARACT_ID
            )
            VALUES
            (
              DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
              Ln_Servicio,
              Ln_Pc_idvcenter,
              Lv_Vcenter,
              SYSDATE,
              NULL,
              'REGULARIZA_DC',
              NULL,
              'Activo',
              Ln_Idmv
            );
          /* FIN INSERTA  INFO_SERVICIO_PROD_CARACT DE VCENTER*/
          
          /* INI INSERTA  INFO_SERVICIO_PROD_CARACT DE CLUSTER*/
          INSERT
          INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
            (
              ID_SERVICIO_PROD_CARACT,
              SERVICIO_ID,
              PRODUCTO_CARACTERISITICA_ID,
              VALOR,
              FE_CREACION,
              FE_ULT_MOD,
              USR_CREACION,
              USR_ULT_MOD,
              ESTADO,
              REF_SERVICIO_PROD_CARACT_ID
            )
            VALUES
            (
              DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.nextval,
              Ln_Servicio,
              Ln_Pc_idcluster,
              Lv_Cluster,
              SYSDATE,
              NULL,
              'REGULARIZA_DC',
              NULL,
              'Activo',
              Ln_Idmv
            );
          /* FIN Inserta  INFO_SERVICIO_PROD_CARACT de Cluster*/

	COMMIT;

      EXIT WHEN  C_Factibilidad%notfound; 
     

    END LOOP; --Fin del ciclo
  CLOSE C_Factibilidad; --Cierra el cursor
  DBMS_OUTPUT.PUT_LINE('El proceso ha finalizado con exito');

  EXCEPTION
  WHEN OTHERS THEN
    ROLLBACK;
    DBMS_OUTPUT.put_line('Error: '||sqlerrm);
  END;

/
