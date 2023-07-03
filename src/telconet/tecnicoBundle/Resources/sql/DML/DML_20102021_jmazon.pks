--####################################################################################################################
--# Regularización de servicios ECDF que no tengan las caracteristicas FECHA_ACTIVACION y FECHA_MINIMA_SUSCRIPCION ###
--####################################################################################################################

SET SERVEROUTPUT ON;
DECLARE
    --Constantes
    Ln_CounterRow              NUMBER 		:= 0;
    Ln_NombreTecnico		   VARCHAR2(4)	:= 'ECDF';
    Ln_ConfirmarServ		   VARCHAR2(25)	:= 'confirmarServicio';
    Ln_FeOrigenCRS			   VARCHAR2(25)	:= 'feOrigenCambioRazonSocial';
    Ln_FeOrigenTraslado        VARCHAR2(25)	:= 'feOrigServicioTrasladado';
   
   CURSOR C_INFO_SERVICIO IS
          	SELECT X.*
				FROM(
				--Todos los servicios ECDF en estado Activo - InCorte
						SELECT  ise.ID_SERVICIO as ID_SERVICIO, ise.estado AS ESTADO, ip.LOGIN, ier.EMPRESA_COD, ip.ID_PUNTO
						FROM DB_COMERCIAL.INFO_PUNTO ip 
						LEFT JOIN DB_COMERCIAL.INFO_SERVICIO ise ON ise.PUNTO_ID = ip.ID_PUNTO
						LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper ON iper.ID_PERSONA_ROL = ip.PERSONA_EMPRESA_ROL_ID 
						LEFT JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ier ON ier.ID_EMPRESA_ROL = iper.EMPRESA_ROL_ID
                        WHERE ip.ESTADO = 'Activo'
						AND ise.ESTADO IN ('Activo','In-Corte')
                        AND ise.PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO ap WHERE NOMBRE_TECNICO = Ln_NombreTecnico)
						AND ier.EMPRESA_COD = '18'
						ORDER BY ise.ID_SERVICIO DESC
			)X;
    
    CURSOR C_INFO_SERVICIO_PROD_CARACT (Cn_IdServicio NUMBER) IS
          	SELECT Y.*
				FROM(
					SELECT  CASE 
        		WHEN  MAX(ispc.SERVICIO_ID) > 0  THEN
        		1
        		ELSE
        		0
        		END AS SERVICIO_ID
						FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ispc
                        LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA apc ON apc.ID_PRODUCTO_CARACTERISITICA = ispc.PRODUCTO_CARACTERISITICA_ID
                        LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA ac ON ac.ID_CARACTERISTICA = apc.CARACTERISTICA_ID
                        WHERE ispc.ESTADO = 'Activo'
                        AND ispc.SERVICIO_ID = Cn_IdServicio
                        AND ac.DESCRIPCION_CARACTERISTICA = 'FECHA_ACTIVACION'
               )Y;
			
	CURSOR C_FECHA_A_INSERTAR (Cn_idServicio NUMBER , Cn_Accion VARCHAR2)	IS
		SELECT 
		TO_CHAR( ISH.FE_CREACION,'YYYY-MM-DD HH24:MI:SS') AS FE_ACTIVACION,
		TO_CHAR(ADD_MONTHS(ISH.FE_CREACION,3), 'YYYY-MM-DD HH24:MI:SS')  AS FE_SUSCRIPCION
		    FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
		    WHERE ISH.SERVICIO_ID = Cn_idServicio
		    AND   ISH.ID_SERVICIO_HISTORIAL =  (SELECT MAX(ISHT.ID_SERVICIO_HISTORIAL) 
		                                        FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISHT
		                                        JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISHT.SERVICIO_ID
		                                        WHERE ISHT.SERVICIO_ID = Cn_idServicio
		                                        AND   ISER.ESTADO      = 'Activo'
		                                        AND   ISHT.ACCION      = Cn_Accion);
                                                
    CURSOR C_OBTIENE_SERVICIO_X_HISTORIAL (Cn_idServicio NUMBER, Cn_Accion VARCHAR2) IS
    SELECT 
		 MAX(ISER.ID_SERVICIO) AS ID_SERVICIO
        FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
            JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISH.SERVICIO_ID
		    WHERE ISER.ID_SERVICIO = Cn_idServicio
            AND   ISH.ACCION      = Cn_Accion;
    
    
    
   type Lr_ServicioType 	is table of C_INFO_SERVICIO%ROWTYPE;
   Lr_Servicio              Lr_ServicioType;
   Lr_ServProdCaract        C_INFO_SERVICIO_PROD_CARACT%ROWTYPE;
   Lr_FechaAInsertar	    C_FECHA_A_INSERTAR%ROWTYPE;
   Lr_ServicioXHistorial	C_OBTIENE_SERVICIO_X_HISTORIAL%ROWTYPE;
   Lr_Accion                VARCHAR2(25):= NULL;
   Lr_ServicioId            NUMBER:=NULL;
   Lr_EstadoServicio        VARCHAR2(25):= NULL;
   Lr_Login                 VARCHAR2(40):= NULL;
   i PLS_INTEGER;
   
BEGIN
	  OPEN C_INFO_SERVICIO;
      FETCH C_INFO_SERVICIO BULK COLLECT INTO Lr_Servicio LIMIT 2000;
        i := Lr_Servicio.FIRST;

        WHILE (i IS NOT NULL)
        LOOP
            Lr_ServicioId       := Lr_Servicio(i).ID_SERVICIO;
            Lr_EstadoServicio   := Lr_Servicio(i).ESTADO;
            Lr_Login            := Lr_Servicio(i).LOGIN;
            
            IF C_INFO_SERVICIO_PROD_CARACT%ISOPEN THEN
              CLOSE C_INFO_SERVICIO_PROD_CARACT;
            END IF;
            OPEN C_INFO_SERVICIO_PROD_CARACT(Lr_ServicioId);
            	FETCH C_INFO_SERVICIO_PROD_CARACT
             	INTO Lr_ServProdCaract;
            CLOSE C_INFO_SERVICIO_PROD_CARACT;
           
            --Si no tiene caracteristica FECHA_ACTIVACION
			IF Lr_ServProdCaract.SERVICIO_ID <= 0 THEN
				
                --Buscamos si es servicio activado por CRS por medio del historial
                IF C_OBTIENE_SERVICIO_X_HISTORIAL%ISOPEN THEN
	              CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
	            END IF;
	            OPEN C_OBTIENE_SERVICIO_X_HISTORIAL(Lr_ServicioId, Ln_FeOrigenCRS);
	            	FETCH C_OBTIENE_SERVICIO_X_HISTORIAL
	             	INTO Lr_ServicioXHistorial;
	            CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
                
                IF Lr_ServicioXHistorial.ID_SERVICIO IS NOT NULL
                THEN
                Lr_Accion := Ln_FeOrigenCRS;
                ELSE 
                    --Buscamos si es servicio activado por Traslado por medio del historial
                     IF C_OBTIENE_SERVICIO_X_HISTORIAL%ISOPEN THEN
                      CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
                    END IF;
                    OPEN C_OBTIENE_SERVICIO_X_HISTORIAL(Lr_ServicioId, Ln_FeOrigenTraslado);
                        FETCH C_OBTIENE_SERVICIO_X_HISTORIAL
                        INTO Lr_ServicioXHistorial;
                    CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
                    
                    IF Lr_ServicioXHistorial.ID_SERVICIO IS NOT NULL
                    THEN
                    Lr_Accion := Ln_FeOrigenTraslado;
                    ELSE 
                        --Buscamos si es servicio activado por medio del historial
                         IF C_OBTIENE_SERVICIO_X_HISTORIAL%ISOPEN THEN
                          CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
                        END IF;
                        OPEN C_OBTIENE_SERVICIO_X_HISTORIAL(Lr_ServicioId, Ln_ConfirmarServ);
                            FETCH C_OBTIENE_SERVICIO_X_HISTORIAL
                            INTO Lr_ServicioXHistorial;
                        CLOSE C_OBTIENE_SERVICIO_X_HISTORIAL;
                        
                        IF Lr_ServicioXHistorial.ID_SERVICIO IS NOT NULL
                        THEN
                        Lr_Accion := Ln_ConfirmarServ;
                        END IF;
                        
                    END IF;
                
                END IF; 
                
                --Consultamos fecha a insertar por id_servicio y acción
	            IF C_FECHA_A_INSERTAR%ISOPEN THEN
	              CLOSE C_FECHA_A_INSERTAR;
	            END IF;
	            OPEN C_FECHA_A_INSERTAR(Lr_ServicioId, Lr_Accion);
	            	FETCH C_FECHA_A_INSERTAR
	             	INTO Lr_FechaAInsertar;
	            CLOSE C_FECHA_A_INSERTAR;
                
	           
	           --INSERT FECHA DE ACTIVACION
	           INSERT INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                        (
                          DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                          Lr_ServicioId,
                          (SELECT ID_PRODUCTO_CARACTERISITICA
                          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                          WHERE PRODUCTO_ID     = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO ap WHERE NOMBRE_TECNICO = Ln_NombreTecnico)
                          AND CARACTERISTICA_ID =
                            (SELECT ID_CARACTERISTICA
                            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = 'FECHA_ACTIVACION'
                            AND ESTADO                       = 'Activo'
                            )
                          ),
                          Lr_FechaAInsertar.FE_ACTIVACION,
                          sysdate,
                          sysdate,
                          'regulaCaracECDF',
                          'regulaCaracECDF',
                          'Activo',
                          NULL
                        );
	           
	           --INSERT FECHA DE FIN DE SUSCRIPCION
                INSERT INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                        (
                          DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                          Lr_ServicioId,
                          (SELECT ID_PRODUCTO_CARACTERISITICA
                          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                          WHERE PRODUCTO_ID     = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO ap WHERE NOMBRE_TECNICO = Ln_NombreTecnico)
                          AND CARACTERISTICA_ID =
                            (SELECT ID_CARACTERISTICA
                            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = 'FECHA_MINIMA_SUSCRIPCION'
                            AND ESTADO                       = 'Activo'
                            )
                          ),
                          Lr_FechaAInsertar.FE_SUSCRIPCION,
                          sysdate,
                          sysdate,
                          'regulaCaracECDF',
                          'regulaCaracECDF',
                          'Activo',
                          NULL
                        );    
--              INSERT HISTORIAL
                    INSERT INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
                    VALUES (DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL,
                    Lr_ServicioId, 'regulaCaracECDF', SYSDATE,'127.0.0.1', Lr_EstadoServicio,
                    NULL, 'Se regulariza características del servicio por Fase 2 del producto ECDF', 'regulaCaracECDF');

                --Mensaje 
	          	dbms_output.put_line('REGULARIZADO, LOGIN: ' || Lr_Login || ', ID DEL SERVICIO: ' || Lr_ServicioId);
			
	          -- Si tiene Caracteristica, no inserta nada
	         ELSE
	         	--Mensaje
	            dbms_output.put_line('NO REGULARIZADO, LOGIN: ' || Lr_Login || ', ID DEL SERVICIO: ' || Lr_ServicioId);
	        END IF;
			Ln_CounterRow := Ln_CounterRow + 1;
            i := Lr_Servicio.NEXT(i);
        END LOOP;
        
	  CLOSE C_INFO_SERVICIO;
    COMMIT;
	dbms_output.put_line('Se procesaron: ' || Ln_CounterRow || ' Registros');
    EXCEPTION
        WHEN OTHERS THEN
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('REGULARIZACION DE CARACTERISTICAS PARA EL PRODUCTO ECDF',
                                                 'REGULARIZACION EN LA INFO_SERVICIO_PROD_CARACT ',
                                                 'Error en RESPALDO DE INFO_SERVICIO_PROD_CARACT: ' || SQLERRM || ' ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                                 NVL(SYS_CONTEXT('USERENV', 'HOST'), USER),
                                                 SYSDATE,
                                                 NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
            ROLLBACK;
END;