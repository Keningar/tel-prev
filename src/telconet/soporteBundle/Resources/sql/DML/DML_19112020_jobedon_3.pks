-- MIGRACION DE ARBOL DE HIPOTESIS
DECLARE
    TYPE ITEMARRAY IS VARRAY(500) OF VARCHAR(1000);
    TYPE DEPARTAMENTOSARRAY IS VARRAY(500) OF ITEMARRAY;

    DEPARTAMENTOS DEPARTAMENTOSARRAY;
    
    IPCCL1 ITEMARRAY;
    NOC    ITEMARRAY;

    ITEMS  ITEMARRAY;
    
    nombredepartamento      VARCHAR2(100);
    iddepartamento          VARCHAR2(100);
    
    hipotesis  VARCHAR2(100);
    tipocaso VARCHAR2(100);
    
    TOTAL     NUMBER;
    IDHIPOTESIS NUMBER;
    EMPRESACOD NUMBER := 10;
    IDPARAMETROCAB NUMBER;
    IDCLIENTES NUMBER;
    IDBACKBONE NUMBER;
    IDTIPOCASO NUMBER;
    IDPADET    NUMBER;
    
    DESCHIPOTESIS VARCHAR2(150);
    Lv_MensajeError VARCHAR2(1500);
    
    Lb_nuevo boolean;
    Lv_esNuevo VARCHAR2(25);

BEGIN

    IPCCL1 := ITEMARRAY(
      'Backbone|Corte de Fibra Ruta 144|',
      'Backbone|Problema Fisico|',
      'Backbone|Problema Logico|',
      'Tecnico|Problema Fisico|',
      'Tecnico|Problema Logico|'
    );
  
    NOC := ITEMARRAY(
      'Backbone|Corte de fibra ruta de 144 hilos|',
      'Backbone|Atenuación enlace Backbone|',
      'Backbone|Corte de fibra enlace Principal|',
      'Backbone|Corte de fibra enlace Backup|',
      'Backbone|Problemas enlaces caídos por Trabajo Programado|',
      'Backbone|Caída equipo de Backbone|',
      'Backbone|Incidencia no atribuible a Telconet|',
      'Backbone|Caída enlace salida internacional|',
      'Backbone|Atenuación enlace salida internacional|',
      'Backbone|Saturación de Enlace Backbone|',
      'Backbone|Problemas UPS descarga rápida de baterías|',
      'Backbone|Manipulación por terceros|',
      'Backbone|Reinicio de equipo de backbone|',
      'Backbone|Problema enlace radial|',
      'Tecnico|Caída del servicio|',
      'Tecnico|Saturación enlace cliente|',
      'Tecnico|Caída de WIFI|',
      'Tecnico|Intermitencia del servicio|',
      'Tecnico|Arranque de generador|',
      'Tecnico|Extensión de VM por trabajo programado|',
      'Tecnico|Corte de fibra enlaces Telefónica|',
      'Tecnico|Atenuación de fibra enlaces Telefónica|'
    );
    
    SELECT APC.ID_PARAMETRO INTO IDPARAMETROCAB 
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC 
    WHERE APC.NOMBRE_PARAMETRO = 'CATEGORIA_HIPOTESIS' AND APC.ESTADO = 'Activo';
    
    
    SELECT A.ID_TIPO_CASO INTO IDCLIENTES FROM DB_SOPORTE.ADMI_TIPO_CASO A WHERE A.NOMBRE_TIPO_CASO = 'Tecnico';
    
    SELECT A.ID_TIPO_CASO INTO IDBACKBONE FROM DB_SOPORTE.ADMI_TIPO_CASO A WHERE A.NOMBRE_TIPO_CASO = 'Backbone';

    
    DEPARTAMENTOS := DEPARTAMENTOSARRAY(IPCCL1, NOC);
    
    FOR d IN 1 .. DEPARTAMENTOS.count LOOP
      ITEMS := DEPARTAMENTOS(d);
      TOTAL := ITEMS.COUNT;
      
      IF (d = 1) THEN
        nombredepartamento := 'IPCCL1'; --IPCCL1
        iddepartamento     := 132;
      ELSIF(d = 2) THEN
        nombredepartamento := 'NOC'; --NOC
        iddepartamento     := 133;
      END IF;
      
      FOR i in 1 .. TOTAL LOOP
        IDTIPOCASO := 0;
        IDHIPOTESIS := 0;
        DESCHIPOTESIS := '';
        tipocaso := SUBSTR(items(i),0,(INSTR(items(i),'|')-1)); 
        hipotesis  := SUBSTR(items(i), (INSTR(items(i),'|')+1), LENGTH(items(i)) );
        hipotesis  := SUBSTR(hipotesis,0,(INSTR(hipotesis,'|')-1));
        
        IF tipocaso = 'Tecnico' THEN
          IDTIPOCASO := IDCLIENTES;
        ELSIF tipocaso = 'Backbone' THEN
          IDTIPOCASO := IDBACKBONE;
        END IF;
        
        BEGIN
        
          SELECT A.ID_HIPOTESIS, A.DESCRIPCION_HIPOTESIS INTO IDHIPOTESIS, DESCHIPOTESIS
          FROM DB_SOPORTE.ADMI_HIPOTESIS A
          WHERE TRIM(TRANSLATE(UPPER(A.DESCRIPCION_HIPOTESIS),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) = TRIM(TRANSLATE(UPPER(hipotesis),'áéíóúÁÉÍÓÚ','aeiouAEIOU'))
          AND A.EMPRESA_COD                                                             = EMPRESACOD
          AND A.ESTADO                                                                  = 'Activo'
          AND A.TIPO_CASO_ID                                                            = IDTIPOCASO;
          
          
        EXCEPTION
          WHEN NO_DATA_FOUND THEN
            -- AGREGA NUEVO SINTOMA
            IDHIPOTESIS := DB_SOPORTE.SEQ_ADMI_HIPOTESIS.NEXTVAL;
            DESCHIPOTESIS := hipotesis;
            INSERT INTO DB_SOPORTE.ADMI_HIPOTESIS
            (ID_HIPOTESIS,NOMBRE_HIPOTESIS,DESCRIPCION_HIPOTESIS,ESTADO,USR_CREACION,FE_CREACION,EMPRESA_COD,TIPO_CASO_ID,USR_ULT_MOD,FE_ULT_MOD) VALUES 
            (IDHIPOTESIS,hipotesis,hipotesis,'Activo','jobedon',SYSDATE, EMPRESACOD,IDTIPOCASO,'jobedon',SYSDATE);
            COMMIT;
            Lb_nuevo := true;
            
          WHEN OTHERS THEN
            Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
            DBMS_OUTPUT.PUT_LINE(Lv_MensajeError);
        
        END;
        
        SELECT NVL(MAX(APD.ID_PARAMETRO_DET), 0)
        INTO IDPADET
        FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
        WHERE APD.PARAMETRO_ID = IDPARAMETROCAB
        AND APD.ESTADO         = 'Estado'
        AND APD.EMPRESA_COD    = EMPRESACOD
        AND APD.DESCRIPCION    = DESCHIPOTESIS
        AND APD.VALOR1         = IDHIPOTESIS
        AND APD.VALOR2         = iddepartamento;
        
        Lv_esNuevo := 'Existente';
        IF Lb_nuevo THEN
          Lv_esNuevo := 'Nuevo';
        END IF;
        
        IF IDPADET = 0 THEN
          INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
          (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD) VALUES 
          (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, IDPARAMETROCAB, DESCHIPOTESIS,IDHIPOTESIS,iddepartamento,Lv_esNuevo,'Activo','jobedon',SYSDATE,'127.0.0.1',EMPRESACOD);
          COMMIT;
        END IF;
        
      END LOOP;
            
    END LOOP;

END;

/