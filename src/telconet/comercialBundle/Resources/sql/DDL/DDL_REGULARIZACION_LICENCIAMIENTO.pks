UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET FUNCION_PRECIO = 'if ("[OTROS]"=="Licencia VmWare SP por RAM GB" ) {PRECIO=15.00; } 
else if ("[SISTEMA OPERATIVO]"=="Licencia Windows Server STD 2008/2012/2016 Core Fisico o Virtual" ) { PRECIO=3.00; } 
else if ("[BASE DE DATOS]"=="Licencia SQL Server Standard SPLA 2008,2012,2014,2016" ) { PRECIO=376.00; } 
else if ("[BASE DE DATOS]"=="Adicional Licencia SQL Server Standard SPLA 2008,2012,2014,2016" ) { PRECIO=188.00; } 
else if ("[BASE DE DATOS]"=="Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016" ) { PRECIO=1482.00; } 
else if ("[BASE DE DATOS]"=="Adicional Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016" ) { PRECIO=741.00; } 
else if ("[BASE DE DATOS]"=="Licencia Microsft Terminal Services" ) { PRECIO=7.50; } 
else if ("[SISTEMA OPERATIVO]"=="Suscripcion Red Hat Small Instance ( hasta 4 Cores fisicos o Virtuales) (COD: MCT2567)" ) { PRECIO=56.00; } 
else if ("[SISTEMA OPERATIVO]"=="Suscripcion Red Hat Large Instance ( Más de 4 Cores fisicos o Virtuales) (COD: MCT2568)" ) { PRECIO=116.00; } 
else if ("[APLICACIONES]"=="Lic. SysCenter DC 2012 Proc Licencia de SystemCenter Datacenter 2012 por Procesador T6L-00249" ) { PRECIO=70.00; } 
else if ("[SISTEMA OPERATIVO]"=="Lic. Windows Svr DC - Proc Licencia Windows Server DataCenter por Procesador P71-01031" ) { PRECIO=165.00; } 
else if ("[BASE DE DATOS]"=="Lic. Sql Server WebEdit Core Licencia SQL Server Web Edition SPLA" ) { PRECIO=48.00; } 
else if ("[BASE DE DATOS]"=="Adicional Licencia SQL Server Web Edition SPLA 2008,2012,2014,2016" ) { PRECIO=24.00; } 
else if ("[SISTEMA OPERATIVO]"=="Lic. Windows Svr DC - Core Licencia Windows Server DataCenter por Core 9EA-0039" ) { PRECIO=21.00; }
else if ("[APLICACIONES]"=="Lic. SysCenter DC Core Licencia de SystemCenter Datacenter por Core 9EP-00037" ) { PRECIO=8.50; } 
else if ("[APLICACIONES]"=="LICENCIAMIENTO DE RESPALDO DE MAQUINAS ORACLE VM" ) { PRECIO=101.40;}  
else if ("[APLICACIONES]"=="Licencia VEEAM Replicacion x vm" ) { PRECIO=50.00;}   
else if ("[SISTEMA OPERATIVO]"=="Centos Linux" ) { PRECIO=0.01;} 
else if ("[SISTEMA OPERATIVO]"=="Ubuntu Server" ) { PRECIO=0.01;}
else if ("[SISTEMA OPERATIVO]"=="Oracle Linux" ) { PRECIO=0.01;} 
else if ("[APLICACIONES]"=="Veeam Backup y Replication Enterprise Plus for Hyper-V  -  Servidor Virtual - Cloud Rental" ) { PRECIO=15.26;} 
else if ("[APLICACIONES]"=="Veeam Backup y Replication Enterprise Plus for VmWare  -  Servidor Virtual - Cloud Rental" ) { PRECIO=15.26;} 
else if ("[APLICACIONES]"=="Veeam Cloud Connect Backup - Servidor Virtual - Cloud Rental " ) { PRECIO=7.04;} 
else if ("[APLICACIONES]"=="Veeam Agent for Linux - Servidor Fisico - Cloud Rental" ) { PRECIO=18.80;} 
else if ("[APLICACIONES]"=="Veeam Agent for Windows - Servidor Fisico - Cloud Rental" ) { PRECIO=18.80;} 
else if ("[APLICACIONES]"=="Veeam Cloud Connect Backup - Servidor Fisico - Cloud Rental" ) { PRECIO=10.16;} ' WHERE ID_PRODUCTO = (SELECT id_producto FROM
DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS LICENCIAMIENTO SE' AND ESTADO = 'Activo');

/


DECLARE

  CURSOR C_Servicios_por_secuencial(Cv_Secuencial INTEGER)
          IS
            SELECT 
              S.ID_SERVICIO,
              P.ID_PUNTO,
              S.ESTADO,
              P.LOGIN,
              S.PRODUCTO_ID,
              PROD.SUBGRUPO,
              PROD.DESCRIPCION_PRODUCTO,
              (SELECT
                CASE NVL(
                    (SELECT C.DESCRIPCION_CARACTERISTICA
                    FROM 
                      DB_COMERCIAL.ADMI_PRODUCTO P1,
                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                      DB_COMERCIAL.ADMI_CARACTERISTICA C
                    WHERE P1.ID_PRODUCTO             = APC.PRODUCTO_ID
                    AND C.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
                    AND C.ESTADO                     = 'Activo'
                    AND APC.ESTADO                   = 'Activo'
                    AND P1.ID_PRODUCTO               = PROD.ID_PRODUCTO
                    AND C.DESCRIPCION_CARACTERISTICA = 'PRODUCTO_PREFERENCIAL_GRUPO'
                    ),'N')
                  WHEN 'N'
                  THEN 'N'
                  ELSE 'S'
                END
              FROM DUAL
              ) ES_PREFERENCIAL,
              (SELECT
                CASE NVL(
                    (SELECT C.DESCRIPCION_CARACTERISTICA
                    FROM 
                      DB_COMERCIAL.ADMI_PRODUCTO P1,
                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                      DB_COMERCIAL.ADMI_CARACTERISTICA C
                    WHERE P1.ID_PRODUCTO             = APC.PRODUCTO_ID
                    AND C.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
                    AND C.ESTADO                     = 'Activo'
                    AND APC.ESTADO                   = 'Activo'
                    AND P1.ID_PRODUCTO               = PROD.ID_PRODUCTO
                    AND P1.ESTADO                    = 'Activo'
                    AND C.DESCRIPCION_CARACTERISTICA = 'ES_POOL_RECURSOS'
                    ),'N')
                  WHEN 'N'
                  THEN 'N'
                  ELSE 'S'
                END
              FROM DUAL
              ) ES_POOL_RECURSOS,
              (SELECT
                CASE NVL(
                    (SELECT C.DESCRIPCION_CARACTERISTICA
                    FROM 
                      DB_COMERCIAL.ADMI_PRODUCTO P1,
                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                      DB_COMERCIAL.ADMI_CARACTERISTICA C
                    WHERE P1.ID_PRODUCTO             = APC.PRODUCTO_ID
                    AND C.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
                    AND C.ESTADO                     = 'Activo'
                    AND APC.ESTADO                   = 'Activo'
                    AND P1.ID_PRODUCTO               = PROD.ID_PRODUCTO
                    AND P1.ESTADO                    = 'Activo'
                    AND C.DESCRIPCION_CARACTERISTICA = 'ES_HOUSING'
                    ),'N')
                  WHEN 'N'
                  THEN 'N'
                  ELSE 'S'
                END
              FROM DUAL
              ) ES_HOUSING,
              (SELECT
                CASE NVL(
                    (SELECT C.DESCRIPCION_CARACTERISTICA
                    FROM 
                      DB_COMERCIAL.ADMI_PRODUCTO P1,
                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                      DB_COMERCIAL.ADMI_CARACTERISTICA C
                    WHERE P1.ID_PRODUCTO             = APC.PRODUCTO_ID
                    AND C.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
                    AND C.ESTADO                     = 'Activo'
                    AND APC.ESTADO                   = 'Activo'
                    AND P1.ID_PRODUCTO               = PROD.ID_PRODUCTO
                    AND P1.ESTADO                    = 'Activo'
                    AND C.DESCRIPCION_CARACTERISTICA = 'ES_LICENCIAMIENTO_SO'
                    ),'N')
                  WHEN 'N'
                  THEN 'N'
                  ELSE 'S'
                END
              FROM DUAL
              ) ES_LICENCIAMIENTO_SO,
              (SELECT
                CASE NVL(
                    (SELECT C.DESCRIPCION_CARACTERISTICA
                    FROM 
                      DB_COMERCIAL.ADMI_PRODUCTO P1,
                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                      DB_COMERCIAL.ADMI_CARACTERISTICA C
                    WHERE P1.ID_PRODUCTO             = APC.PRODUCTO_ID
                    AND C.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
                    AND C.ESTADO                     = 'Activo'
                    AND APC.ESTADO                   = 'Activo'
                    AND P1.ID_PRODUCTO               = PROD.ID_PRODUCTO
                    AND P1.ESTADO                    = 'Activo'
                    AND C.DESCRIPCION_CARACTERISTICA = 'ES_ALQUILER_SERVIDORES'
                    ),'N')
                  WHEN 'N'
                  THEN 'N'
                  ELSE 'S'
                END
              FROM DUAL
              ) ES_ALQUILER_SERVIDORES,
              (SELECT NVL(
                  (SELECT SERVPC.ID_SERVICIO_PROD_CARACT
                  FROM 
                    DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                    DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                    DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                  WHERE SERVPC.SERVICIO_ID               = S.ID_SERVICIO
                  AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                  AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                  AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'SUBTIPO_SOLUCION'
                  AND SERVPC.ESTADO                      = 'Activo'
                  ),0)
                FROM DUAL
               ) TIENE_CAMBIO
            FROM 
              DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC1,
              DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC1,
              DB_COMERCIAL.ADMI_CARACTERISTICA C1,
              DB_COMERCIAL.INFO_SERVICIO S,
              DB_COMERCIAL.INFO_PUNTO P,
              DB_COMERCIAL.ADMI_PRODUCTO PROD
            WHERE ISPC1.SERVICIO_ID               = S.ID_SERVICIO
            AND S.PRODUCTO_ID                     = PROD.ID_PRODUCTO
            AND S.PUNTO_ID                        = P.ID_PUNTO
            AND ISPC1.PRODUCTO_CARACTERISITICA_ID = APC1.ID_PRODUCTO_CARACTERISITICA
            AND APC1.CARACTERISTICA_ID            = C1.ID_CARACTERISTICA
            AND C1.DESCRIPCION_CARACTERISTICA     = 'SECUENCIAL_GRUPO'
            AND ISPC1.VALOR                       = TO_CHAR(Cv_Secuencial);

  TYPE servicio_type IS TABLE OF C_Servicios_por_secuencial%ROWTYPE
       INDEX BY PLS_INTEGER;
       
  servicios_tt servicio_type;
  
  tieneHousing  varchar2(10) := 'N';
  tieneAlquiler varchar2(10) := 'N';
  tienePoolRec  varchar2(10) := 'N';
  subtiposConf  varchar2(100):= '';
  tipoCore      varchar2(50) := '';
  idServProCaractDisco number := 0;
  idServProCaractProcesador number := 0;
  idServProCaractMemoria number := 0;
  idServProCaractAlquiler number := 0;  
      
BEGIN

    FOR SECUENCIAL IN
    (
        SELECT 
          DISTINCT(ISPC.VALOR) SECUENCIAL          
        FROM 
          DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
          DB_COMERCIAL.ADMI_CARACTERISTICA C
        WHERE ISPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
        AND APC.CARACTERISTICA_ID              = C.ID_CARACTERISTICA
        AND C.DESCRIPCION_CARACTERISTICA       = 'SECUENCIAL_GRUPO'
        AND ISPC.ESTADO                        = 'Activo'
        ORDER BY TO_NUMBER(ISPC.VALOR) ASC
    )
    LOOP
    
      subtiposConf := NULL;
            
      IF C_Servicios_por_secuencial%ISOPEN THEN
        CLOSE C_Servicios_por_secuencial;
      END IF;
      
      --Determinar si la solucion creada contiene alquiler de servidores ( CLUOD IAAS DEDICADO )
      --si no tiene alquiler de servidores
      OPEN C_Servicios_por_secuencial(SECUENCIAL.SECUENCIAL);      
      LOOP
        FETCH C_Servicios_por_secuencial 
        bulk collect into servicios_tt;
        
        FOR indx  IN 1 .. servicios_tt.count
        LOOP
        
        IF servicios_tt(indx).TIENE_CAMBIO = 0 THEN
        
            DBMS_OUTPUT.PUT_LINE(servicios_tt(indx).ID_SERVICIO||' HO '||servicios_tt(indx).ES_HOUSING||' ALQ '||servicios_tt(indx).ES_ALQUILER_SERVIDORES||
            ' POOL '||servicios_tt(indx).ES_POOL_RECURSOS);        
            IF servicios_tt(indx).ES_HOUSING = 'S' THEN tieneHousing := 'S'; ELSE tieneHousing := 'N'; END IF;
            IF servicios_tt(indx).ES_ALQUILER_SERVIDORES = 'S' THEN tieneAlquiler := 'S'; ELSE tieneAlquiler := 'N'; END IF;
            IF servicios_tt(indx).ES_POOL_RECURSOS = 'S' THEN tienePoolRec := 'S'; ELSE tienePoolRec := 'N'; END IF;
          
        END IF;
        
        END LOOP;
        
        EXIT WHEN C_Servicios_por_secuencial%NOTFOUND;
      END LOOP;
    
      CLOSE C_Servicios_por_secuencial;
      --
      IF C_Servicios_por_secuencial%ISOPEN THEN
        CLOSE C_Servicios_por_secuencial;
      END IF;
      --
      OPEN C_Servicios_por_secuencial(SECUENCIAL.SECUENCIAL);      
      LOOP
        FETCH C_Servicios_por_secuencial 
        bulk collect into servicios_tt;
        
        FOR indx  IN 1 .. servicios_tt.count
        LOOP
            
            IF servicios_tt(indx).TIENE_CAMBIO = 0 THEN
            
                --regularizar los servicios ligados a la solucion  
                 IF servicios_tt(indx).ES_PREFERENCIAL = 'S' THEN                     
                    
                    INSERT
                      INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                        (
                          DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                          servicios_tt(indx).ID_SERVICIO,
                          (SELECT ID_PRODUCTO_CARACTERISITICA
                          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                          WHERE PRODUCTO_ID     = servicios_tt(indx).PRODUCTO_ID
                          AND CARACTERISTICA_ID =
                            (SELECT ID_CARACTERISTICA
                            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPO_SOLUCION'
                            AND ESTADO                       = 'Activo'
                            )
                          ),
                          'COMUNICACIONES',
                          sysdate,
                          sysdate,
                          'arsuarez',
                          'arsuarez',
                          'Activo',
                          NULL
                        );
                        
                    IF tieneHousing = 'S' THEN subtiposConf := 'HOUSING|'; END IF;
                    IF tienePoolRec = 'S' THEN 
                      IF tieneAlquiler = 'S' THEN
                        subtiposConf := subtiposConf||'CLOUD IAAS - DEDICADO|';
                      ELSE
                        subtiposConf := subtiposConf||'CLOUD IAAS - COMPARTIDO|';
                      END IF;
                    END IF;
                                    
                    DBMS_OUTPUT.PUT_LINE(subtiposConf);
                    
                    IF subtiposConf is not null THEN
                    
                      INSERT
                      INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                        (
                          DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                          servicios_tt(indx).ID_SERVICIO,
                          (SELECT ID_PRODUCTO_CARACTERISITICA
                          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                          WHERE PRODUCTO_ID     = servicios_tt(indx).PRODUCTO_ID
                          AND CARACTERISTICA_ID =
                            (SELECT ID_CARACTERISTICA
                            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPOS_CORE_CONFIGURADOS'
                            AND ESTADO                       = 'Activo'
                            )
                          ),
                          subtiposConf,
                          sysdate,
                          sysdate,
                          'arsuarez',
                          'arsuarez',
                          'Activo',
                          NULL
                        );
                    
                    END IF;
                    
              ELSE
              
                  -- SERVICIOS CORES O NO PREFERENCIALES
                  IF servicios_tt(indx).ES_HOUSING = 'S' OR  servicios_tt(indx).SUBGRUPO = 'DATACENTER HOUSING' THEN 
                  
                    INSERT
                      INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                        (
                          DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                          servicios_tt(indx).ID_SERVICIO,
                          (SELECT ID_PRODUCTO_CARACTERISITICA
                          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                          WHERE PRODUCTO_ID     = servicios_tt(indx).PRODUCTO_ID
                          AND CARACTERISTICA_ID =
                            (SELECT ID_CARACTERISTICA
                            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPO_SOLUCION'
                            AND ESTADO                       = 'Activo'
                            )
                          ),
                          'HOUSING',
                          sysdate,
                          sysdate,
                          'arsuarez',
                          'arsuarez',
                          'Activo',
                          NULL
                        );
                        
                  END IF;
                  
                  IF servicios_tt(indx).ES_POOL_RECURSOS = 'S' OR  servicios_tt(indx).SUBGRUPO = 'DATACENTER CLOUD IAAS' THEN 
                  
                      IF tieneAlquiler = 'S' THEN tipoCore := 'CLOUD IAAS - DEDICADO'; ELSE tipoCore := 'CLOUD IAAS - COMPARTIDO'; END IF;
                    
                      INSERT
                        INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
                          (
                            DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
                            servicios_tt(indx).ID_SERVICIO,
                            (SELECT ID_PRODUCTO_CARACTERISITICA
                            FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                            WHERE PRODUCTO_ID     = servicios_tt(indx).PRODUCTO_ID
                            AND CARACTERISTICA_ID =
                              (SELECT ID_CARACTERISTICA
                              FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                              WHERE DESCRIPCION_CARACTERISTICA = 'SUBTIPO_SOLUCION'
                              AND ESTADO                       = 'Activo'
                              )
                            ),
                            tipoCore,
                            sysdate,
                            sysdate,
                            'arsuarez',
                            'arsuarez',
                            'Activo',
                            NULL
                          );
                        
                  END IF;
              
                  IF servicios_tt(indx).ES_POOL_RECURSOS = 'S' OR servicios_tt(indx).ES_LICENCIAMIENTO_SO = 'S' OR servicios_tt(indx).ES_ALQUILER_SERVIDORES  = 'S' THEN
                                        
                          
                          --SE GUARDAN LAS CARACTERISISTICAS DIFERENCIALES DE CADA SERVICIO
                          IF servicios_tt(indx).ES_POOL_RECURSOS = 'S' THEN                                                      
                                
                                 --CORREGIR VALORES DE RECURSOS ASIGNADOS CON REFERENCIA
                                
                                SELECT NVL((SELECT SERVPC.ID_SERVICIO_PROD_CARACT                                
                                    FROM 
                                      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                    WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                    AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                    AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                    AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'DISCO'
                                    AND SERVPC.ESTADO                      = 'Activo'),0) INTO idServProCaractDisco FROM DUAL;
                                  
                                if idServProCaractDisco <> 0 then
                                
                                    UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
                                      SET REF_SERVICIO_PROD_CARACT_ID = idServProCaractDisco
                                    WHERE ID_SERVICIO_PROD_CARACT   =
                                      (SELECT SERVPC.ID_SERVICIO_PROD_CARACT
                                      FROM 
                                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                      WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                      AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                      AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                      AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'DISCO_VALUE'
                                      AND SERVPC.ESTADO                      = 'Activo'
                                      );
                                
                                end if;
                                
                                SELECT NVL((SELECT SERVPC.ID_SERVICIO_PROD_CARACT                                
                                    FROM 
                                      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                    WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                    AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                    AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                    AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'PROCESADOR'
                                    AND SERVPC.ESTADO                      = 'Activo'),0) INTO idServProCaractProcesador FROM DUAL;
                                  
                                if idServProCaractProcesador <> 0 then
                                
                                    UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
                                      SET REF_SERVICIO_PROD_CARACT_ID = idServProCaractProcesador
                                    WHERE ID_SERVICIO_PROD_CARACT   =
                                      (SELECT SERVPC.ID_SERVICIO_PROD_CARACT
                                      FROM 
                                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                      WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                      AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                      AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                      AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'PROCESADOR_VALUE'
                                      AND SERVPC.ESTADO                      = 'Activo'
                                      );
                                
                                end if;
                                
                                SELECT NVL((SELECT SERVPC.ID_SERVICIO_PROD_CARACT                                
                                    FROM 
                                      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                    WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                    AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                    AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                    AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'MEMORIA RAM'
                                    AND SERVPC.ESTADO                      = 'Activo'),0) INTO idServProCaractMemoria FROM DUAL;
                                  
                                if idServProCaractMemoria <> 0 then
                                
                                    UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
                                      SET REF_SERVICIO_PROD_CARACT_ID = idServProCaractMemoria
                                    WHERE ID_SERVICIO_PROD_CARACT   =
                                      (SELECT SERVPC.ID_SERVICIO_PROD_CARACT
                                      FROM 
                                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                      WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                      AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                      AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                      AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'MEMORIA RAM_VALUE'
                                      AND SERVPC.ESTADO                      = 'Activo'
                                      );
                                
                                end if;
                                                                    
                          END IF;                                                    
                          
                          IF servicios_tt(indx).ES_ALQUILER_SERVIDORES = 'S' THEN
                                                      
                                
                              SELECT NVL((SELECT SERVPC.ID_SERVICIO_PROD_CARACT                                
                                    FROM 
                                      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                    WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                    AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                    AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                    AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'TIPO ALQUILER SERVIDOR'
                                    AND SERVPC.ESTADO                      = 'Activo'),0) INTO idServProCaractAlquiler FROM DUAL;
                                  
                                if idServProCaractAlquiler <> 0 then
                                
                                    UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
                                      SET REF_SERVICIO_PROD_CARACT_ID = idServProCaractAlquiler
                                    WHERE ID_SERVICIO_PROD_CARACT   =
                                      (SELECT SERVPC.ID_SERVICIO_PROD_CARACT
                                      FROM 
                                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SERVPC,
                                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADMIPC,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA ADMIC
                                      WHERE SERVPC.SERVICIO_ID               = servicios_tt(indx).ID_SERVICIO
                                      AND SERVPC.PRODUCTO_CARACTERISITICA_ID = ADMIPC.ID_PRODUCTO_CARACTERISITICA
                                      AND ADMIPC.CARACTERISTICA_ID           = ADMIC.ID_CARACTERISTICA
                                      AND ADMIC.DESCRIPCION_CARACTERISTICA   = 'TIPO ALQUILER SERVIDOR_VALUE'
                                      AND SERVPC.ESTADO                      = 'Activo'
                                      );
                                
                                end if;
                                                
                          END IF;
                  
                  END IF;
                    
              END IF;                
          
            END IF;
          
        END LOOP; 
        
        EXIT WHEN C_Servicios_por_secuencial%NOTFOUND;
        
      END LOOP;
    
      CLOSE C_Servicios_por_secuencial;
            
    END LOOP;
    
    commit;

EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  raise_application_error(-20001,'UN ERROR A OCURRIDO - '||SQLERRM || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' -ERROR- '||SQLERRM);
END;

/

DECLARE

coincidencias number := 0;

BEGIN

FOR LICENCIA IN
(
    SELECT 
      S.ID_SERVICIO,
      SPC.ID_SERVICIO_PROD_CARACT,
      TRIM(SPC.VALOR) VALOR
    FROM 
      DB_COMERCIAL.INFO_SERVICIO S,
      DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC,
      DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PC,
      DB_COMERCIAL.ADMI_CARACTERISTICA AC
    WHERE 
          S.ID_SERVICIO                 = SPC.SERVICIO_ID
    AND SPC.PRODUCTO_CARACTERISITICA_ID = PC.ID_PRODUCTO_CARACTERISITICA
    AND PC.CARACTERISTICA_ID            = AC.ID_CARACTERISTICA
    AND SPC.ESTADO                      = 'Activo'
    AND AC.DESCRIPCION_CARACTERISTICA   = 'TIPO LICENCIAMIENTO SERVICE'
)
LOOP

  FOR TIPOS IN
  (
      SELECT 
        trim(SUBSTR(DET.DESCRIPCION,6,LENGTH(DET.DESCRIPCION))) TIPO,
        DET.VALOR1 VALOR
      FROM 
        DB_GENERAL.ADMI_PARAMETRO_CAB CAB ,
        DB_GENERAL.ADMI_PARAMETRO_DET DET
      WHERE CAB.ID_PARAMETRO    = DET.PARAMETRO_ID
      AND CAB.NOMBRE_PARAMETRO IN ('PROD_BASE DE DATOS','PROD_SISTEMA OPERATIVO','PROD_APLICACIONES','PROD_OTROS')
      AND DET.ESTADO            = 'Activo'
  )
  LOOP
  
      IF TIPOS.VALOR = LICENCIA.VALOR THEN
      
        UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SET VALOR = TIPOS.TIPO||'@'||LICENCIA.VALOR WHERE ID_SERVICIO_PROD_CARACT = LICENCIA.ID_SERVICIO_PROD_CARACT;
      
      ELSE
      
          SELECT INSTR(TIPOS.VALOR,LICENCIA.VALOR) INTO coincidencias FROM DUAL; 
          
          IF coincidencias <> 0 THEN
          
              UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SET VALOR = TIPOS.TIPO||'@'||TIPOS.VALOR WHERE ID_SERVICIO_PROD_CARACT = LICENCIA.ID_SERVICIO_PROD_CARACT;
          
          END IF;
      
      END IF;
      
  
  END LOOP;
  
END LOOP;

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  raise_application_error(-20001,'UN ERROR A OCURRIDO - '||SQLERRM || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' -ERROR- '||SQLERRM);
END;

/

