-- MIGRACION DE ARBOL DE SINTOMAS
DECLARE
    TYPE ITEMARRAY IS VARRAY(500) OF VARCHAR(1000);
    TYPE DEPARTAMENTOSARRAY IS VARRAY(500) OF ITEMARRAY;

    DEPARTAMENTOS DEPARTAMENTOSARRAY;
    
    IPCCL1 ITEMARRAY;
    NOC    ITEMARRAY;

    ITEMS  ITEMARRAY;
    
    nombredepartamento      VARCHAR2(100);
    iddepartamento          VARCHAR2(100);
    
    sintoma  VARCHAR2(100);
    tipocaso VARCHAR2(100);
    
    TOTAL     NUMBER;
    IDSINTOMA NUMBER;
    EMPRESACOD NUMBER := 10;
    IDPARAMETROCAB NUMBER;
    IDCLIENTES NUMBER;
    IDBACKBONE NUMBER;
    IDTIPOCASO NUMBER;
    IDPADET    NUMBER;
    
    DESCSIMTOMA VARCHAR2(150);
    Lv_MensajeError VARCHAR2(1500);
    
    Lb_nuevo boolean;
    Lv_esNuevo VARCHAR2(25);
BEGIN
    IPCCL1 := ITEMARRAY(
      'Backbone|Corte de Fibra|',
      'Backbone|Problema AP|',
      'Tecnico|Indisponibilidad del Servicio|',
      'Tecnico|Intermitencias / Lentitud del Servicio|',
      'Tecnico|No llego al ancho de banda contratado|',
      'Tecnico|No puede acceder a una pagina|',
      'Tecnico|No llego a una red|',
      'Tecnico|Problema con CORREO|',
      'Tecnico|Problema con TELEFONIA|',
      'Tecnico|Problema con ZOOM|',
      'Tecnico|Problema con Wifi|',
      'Tecnico|Problema con VPN|',
      'Tecnico|Problema con CAMARAS|',
      'Tecnico|Problema con FIREWALL|'
    );
  
    NOC := ITEMARRAY(
      'Backbone|Corte de Fibra Óptica|',
      'Backbone|Atenuación en Fibra Óptica|',
      'Backbone|Caída enlace principal|',
      'Backbone|Caída enlace backup|',
      'Backbone|Extensión VM Trabajo programado|',
      'Backbone|Reinicio de equipos de Backbone|',
      'Backbone|Arranque de Generador en Nodo|',
      'Backbone|Caída proveedor internacional|',
      'Backbone|Intermitencias en el servicio|',
      'Backbone|Intermitencia en el enlace|',
      'Backbone|Caída del nodo|',
      'Backbone|Problema enlace radial|',
      'Tecnico|Caída enlace principal|',
      'Tecnico|Caída enlace backup|',
      'Tecnico|Caída servicio cliente|',
      'Tecnico|Arranque de generador en el nodo|',
      'Tecnico|Extensión VM Trabajo programado|',
      'Tecnico|Corte de Fibra Óptica|',
      'Tecnico|Atenuación en Fibra Óptica|'
    );
    
    SELECT APC.ID_PARAMETRO INTO IDPARAMETROCAB 
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC 
    WHERE APC.NOMBRE_PARAMETRO = 'CATEGORIA_SINTOMA' AND APC.ESTADO = 'Activo';
    
    
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
        IDSINTOMA := 0;
        DESCSIMTOMA := '';
        Lb_nuevo := false;
        tipocaso := SUBSTR(items(i),0,(INSTR(items(i),'|')-1)); 
        sintoma  := SUBSTR(items(i), (INSTR(items(i),'|')+1), LENGTH(items(i)) );
        sintoma  := SUBSTR(sintoma,0,(INSTR(sintoma,'|')-1));
        
        IF tipocaso = 'Tecnico' THEN
          IDTIPOCASO := IDCLIENTES;
        ELSIF tipocaso = 'Backbone' THEN
          IDTIPOCASO := IDBACKBONE;
        END IF;
        
        BEGIN
        
          SELECT A.ID_SINTOMA, A.DESCRIPCION_SINTOMA INTO IDSINTOMA, DESCSIMTOMA
          FROM DB_SOPORTE.ADMI_SINTOMA A
          WHERE TRIM(TRANSLATE(UPPER(A.DESCRIPCION_SINTOMA),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) = TRIM(TRANSLATE(UPPER(sintoma),'áéíóúÁÉÍÓÚ','aeiouAEIOU'))
          AND A.EMPRESA_COD                                                             = EMPRESACOD
          AND A.ESTADO                                                                  = 'Activo'
          AND A.TIPO_CASO_ID                                                            = IDTIPOCASO;
          
          
        EXCEPTION
          WHEN NO_DATA_FOUND THEN
            -- AGREGA NUEVO SINTOMA
            IDSINTOMA := DB_SOPORTE.SEQ_ADMI_SINTOMA.NEXTVAL;
            DESCSIMTOMA := sintoma;
            INSERT INTO DB_SOPORTE.ADMI_SINTOMA
            (ID_SINTOMA,NOMBRE_SINTOMA,DESCRIPCION_SINTOMA,ESTADO,USR_CREACION,FE_CREACION,EMPRESA_COD,TIPO_CASO_ID,USR_ULT_MOD,FE_ULT_MOD) VALUES 
            (IDSINTOMA,sintoma,sintoma,'Activo','jobedon',SYSDATE, EMPRESACOD,IDTIPOCASO,'jobedon',SYSDATE);
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
        AND APD.DESCRIPCION    = DESCSIMTOMA
        AND APD.VALOR1         = IDSINTOMA
        AND APD.VALOR2         = iddepartamento;
        
        Lv_esNuevo := 'Existente';
        IF Lb_nuevo THEN
          Lv_esNuevo := 'Nuevo';
        END IF;
        
        IF IDPADET = 0 THEN
          INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
          (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD) VALUES 
          (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, IDPARAMETROCAB, DESCSIMTOMA,IDSINTOMA,iddepartamento,Lv_esNuevo,'Activo','jobedon',SYSDATE,'127.0.0.1',EMPRESACOD);
          COMMIT;
        END IF;
        
      END LOOP;
            
    END LOOP;
END;

/