/**
 * Documentación INSERTS admi_prod_carac_comportamiento
 *
 * Nuevos parámetro de comportamiento de las caracteristicas de los productos de MD
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 26-05-2021
 */

DECLARE

CURSOR C_Productos(Cv_Estado VARCHAR2, Cv_NombreTecnico VARCHAR2, Cv_EmpresaCod VARCHAR2) IS
		SELECT
			PRO.ID_PRODUCTO,
			PRO.EMPRESA_COD,
			PRO.CODIGO_PRODUCTO,
            PRO.descripcion_producto AS PRODUCTO_NOMBRE,
			DET.DESCRIPCION AS DESCRIPCION_PRODUCTO,
			PRO.FUNCION_COSTO,
			PRO.INSTALACION,
			PRO.ESTADO,
			PRO.FE_CREACION,
			PRO.USR_CREACION,
			PRO.IP_CREACION,
			PRO.CTA_CONTABLE_PROD,
			PRO.CTA_CONTABLE_PROD_NC,
			PRO.ES_PREFERENCIA,
			PRO.ES_ENLACE,
			PRO.REQUIERE_PLANIFICACION,
			PRO.REQUIERE_INFO_TECNICA,
			PRO.NOMBRE_TECNICO,
			PRO.CTA_CONTABLE_DESC,
			PRO.TIPO,
			PRO.ES_CONCENTRADOR,
			PRO.FUNCION_PRECIO,
			PRO.SOPORTE_MASIVO,
			PRO.ESTADO_INICIAL,
			PRO.GRUPO,
			PRO.COMISION_VENTA,
			PRO.COMISION_MANTENIMIENTO,
			PRO.USR_GERENTE,
			PRO.CLASIFICACION,
			PRO.REQUIERE_COMISIONAR,
			PRO.SUBGRUPO,
			PRO.LINEA_NEGOCIO,
			DET.VALOR4 AS VISIBLE,
			DET.VALOR5 AS EDITABLE,
			DET.VALOR6 AS POR_DEFECTO,
			CASE
					WHEN NVL(IMP.PORCENTAJE_IMPUESTO, 0) > 0 THEN 'S' ELSE 'N'
			END AS PORCENTAJE_IMPUESTO
		FROM
			DB_COMERCIAL.ADMI_PRODUCTO PRO
		INNER JOIN DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO IMP ON
			PRO.ID_PRODUCTO = IMP.PRODUCTO_ID
		INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET ON
			DET.VALOR1 = PRO.CODIGO_PRODUCTO
		INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB ON
			DET.PARAMETRO_ID = CAB.ID_PARAMETRO
		WHERE
			CAB.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
			AND DET.ESTADO = Cv_Estado
			AND DET.EMPRESA_COD = Cv_EmpresaCod
			AND IMP.IMPUESTO_ID = 1
			AND IMP.ESTADO = Cv_Estado
			AND PRO.ESTADO = Cv_Estado
			AND PRO.NOMBRE_TECNICO <> Cv_NombreTecnico
			AND PRO.ES_CONCENTRADOR <> 'SI'
			AND PRO.EMPRESA_COD = Cv_EmpresaCod
		ORDER BY
			DET.DESCRIPCION ASC;

	CURSOR C_Caracteristica(Cn_IdProducto NUMBER, Cv_Estado VARCHAR2) IS
		SELECT 
		APROC.ID_PRODUCTO_CARACTERISITICA AS ID_PRODUCTO_CARACTERISTICA,
		AC.DESCRIPCION_CARACTERISTICA AS DESCRIPCION_CARACTERISTICA,
		AP.CODIGO_PRODUCTO AS CODIGO_PRODUCTO,
		'NO' AS VISIBLE,
		'NO' AS EDITABLE,
		NULL AS OPCIONES,
		NULL AS POR_DEFECTO,
		NULL AS TIPO_ENTRADA
			FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
			INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APROC ON AP.ID_PRODUCTO = APROC.PRODUCTO_ID 
			INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC ON AC.ID_CARACTERISTICA = APROC.CARACTERISTICA_ID
			WHERE AP.ID_PRODUCTO = Cn_IdProducto
				AND AP.ESTADO = Cv_Estado
			   AND APROC.VISIBLE_COMERCIAL = 'SI'
			   AND APROC.ESTADO = Cv_Estado
			   AND AC.ESTADO = Cv_Estado
			   AND AP.EMPRESA_COD = '18'
			   AND APROC.ID_PRODUCTO_CARACTERISITICA NOT IN 
			   (
					SELECT TO_NUMBER(TRIM(APD.VALOR2))
						FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
							INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
							WHERE APD.ESTADO = Cv_Estado
								AND APC.ESTADO = Cv_Estado
								AND APD.EMPRESA_COD = '18'
								AND APC.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL'
			   )
			   AND AC.ID_CARACTERISTICA NOT IN 
			   (
					SELECT TO_NUMBER(TRIM(APD2.VALOR1))
						FROM DB_GENERAL.ADMI_PARAMETRO_DET APD2
							INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC2 ON APC2.ID_PARAMETRO = APD2.PARAMETRO_ID
							WHERE APD2.ESTADO = Cv_Estado
								AND APC2.ESTADO = Cv_Estado
								AND APD2.EMPRESA_COD = '18'
								AND APC2.NOMBRE_PARAMETRO = 'CARACTERISTICAS_IGNORADAS_TM_COMERCIAL'
			   )
		UNION 
		SELECT 
		   APROC.ID_PRODUCTO_CARACTERISITICA AS ID_PRODUCTO_CARACTERISTICA,
		   AC.DESCRIPCION_CARACTERISTICA ,
		   APD.VALOR1 AS CODIGO_PRODUCTO,
		   APD.VALOR3 AS VISIBLE,
		   APD.VALOR4 AS EDITABLE,
		   APD.VALOR5 AS OPCIONES,
		   APD.VALOR6 AS POR_DEFECTO,
		   APD.VALOR7 AS TIPO_ENTRADA
		FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
		INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APROC ON AP.ID_PRODUCTO = APROC.PRODUCTO_ID 
		INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC ON AC.ID_CARACTERISTICA = APROC.CARACTERISTICA_ID 
		INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD ON TRIM(APD.VALOR1) = TRIM(AP.CODIGO_PRODUCTO)
		INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC ON APC.ID_PARAMETRO = APD.PARAMETRO_ID
		   WHERE AP.ID_PRODUCTO = Cn_IdProducto
		   AND TO_NUMBER(TRIM(APD.VALOR2)) = APROC.ID_PRODUCTO_CARACTERISITICA 
		   AND AP.ESTADO = Cv_Estado
		   AND APROC.VISIBLE_COMERCIAL = 'SI'
		   AND APROC.ESTADO = Cv_Estado
		   AND AC.ESTADO = Cv_Estado
		   AND APD.ESTADO = Cv_Estado
		   AND APC.NOMBRE_PARAMETRO  = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL'
		   AND APC.ESTADO = Cv_Estado
		   AND AP.EMPRESA_COD = APD.EMPRESA_COD
		   ORDER BY ID_PRODUCTO_CARACTERISTICA;
    LN_VISIBLE  NUMBER;
    LN_EDITABLE NUMBER;
    Pv_Empresa  VARCHAR2(5) := '18';
    LV_DEFAULT  VARCHAR2(200);
	BEGIN

      --ACTUALIZAR PRODUCTO_CARACTERISTICA DEL PRODUCTO WADB
        update DB_GENERAL.admi_parametro_det
        set valor2 = 13816
        where parametro_id = 1204
        and valor1 = 'WADB';
      --
	  FOR I IN C_Productos('Activo', 'FINANCIERO', Pv_Empresa) LOOP

			FOR I1 IN C_Caracteristica(I.ID_PRODUCTO, I.ESTADO) LOOP
            
                IF I1.VISIBLE = 'SI' OR  
				   (I1.DESCRIPCION_CARACTERISTICA = 'VISUALIZAR_EN_MOVIL' AND
				    I.ID_PRODUCTO = 1207 OR
					I.ID_PRODUCTO = 78 OR
					I.ID_PRODUCTO = 210 OR
					I.ID_PRODUCTO = 939 OR
					I.ID_PRODUCTO = 1130 OR
					I.ID_PRODUCTO = 1262 OR
					I.ID_PRODUCTO = 1263 OR
					I.ID_PRODUCTO = 1232 OR 
					I.ID_PRODUCTO = 1231 OR 
					I.ID_PRODUCTO = 1332 OR 
					I.ID_PRODUCTO = 1320 OR 
					I.ID_PRODUCTO = 1321 
					)  THEN
                    LN_VISIBLE := 1;
                ELSE
                    LN_VISIBLE := 0;
                END IF;
                
                IF I1.EDITABLE = 'SI' THEN
                    LN_EDITABLE := 1;
                ELSE
                    LN_EDITABLE := 0;
                END IF;

                IF I.PRODUCTO_NOMBRE = 'I. PROTEGIDO MULTI PAID' AND I1.DESCRIPCION_CARACTERISTICA = 'ANTIVIRUS' THEN
                    LV_DEFAULT := 'KASPERSKY';
                ELSIF I.PRODUCTO_NOMBRE = 'Netlife Zone' AND I1.DESCRIPCION_CARACTERISTICA = 'TIEMPO CONEXION' THEN
                    LV_DEFAULT := 'Ilimitado';
                ELSE
                    LV_DEFAULT := i1.por_defecto;
                END IF;
                    
                INSERT INTO db_comercial.admi_prod_carac_comportamiento (
                    id_prod_carac_comp,
                    producto_caracteristica_id,
                    es_visible,
                    editable,
                    valores_seleccionable,
                    valores_default,
                    estado,
                    fe_creacion,
                    usr_creacion
                ) VALUES (
                    db_comercial.seq_admi_prod_carac_comp.nextval,
                    i1.id_producto_caracteristica,
                    ln_visible,
                    ln_editable,
                    i1.opciones,
                    LV_DEFAULT,
                    'Activo',
                    sysdate,
                    'migracionMD'
                );
			END LOOP;

	  END LOOP;
COMMIT;
END;

/