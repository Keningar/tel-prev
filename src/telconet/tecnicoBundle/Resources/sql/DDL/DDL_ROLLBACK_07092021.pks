create or replace PACKAGE DB_INFRAESTRUCTURA.INFRKG_KONIBIT AS 
  
  /**
   * Documentación para EXKG_MD_CONSULTAS
   * Paquete que contiene procesos de tipo consulta, que usará la empresa de MEGADATOS
   * 
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 19/10/2019
   */
   
  /**
   * Documentación para P_ENVIA_NOTIFICACION
   * Procedimiento que se encarga del envio de notificaciones a Konibit, cuando
   * un servicio que contiene un plan con producto konibit o producto adicional
   * konibit cuando este se active, cancele, corte, reactive, o se realice un cambio
   * de plan
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 25/11/2019
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.1 07/09/2021 - Se mejora la validacion de respuesta de llamado del WS a konibit
   *                          para que desvuelva el mensaje de la aplicacion y no solo de la ejecucion
   * 
   * @param Pn_idServicio  IN NUMBER   Id del Servicio
   * @param Pv_tipoProceso IN VARCHAR2 Tipo de Proceso: ACTIVAR, CORTAR, RECONECTAR, CAMBIOPLAN, CANCELAR
   * @param Pv_tipoTrx     IN VARCHAR2 Tipo de TRansaccion : INDIVIDIAL O MASIVA
   * @param Pv_usrCreacion IN VARCHAR2 Usuario que realiza la transaccion
   * @param Pv_ipCreacion  IN VARCHAR2 Ip del usuario que realiza la transacción
   * 
   */
  PROCEDURE P_ENVIA_NOTIFICACION(Pn_idServicio  IN NUMBER,
                                  Pv_tipoProceso IN VARCHAR2,
                                  Pv_tipoTrx     IN VARCHAR2,
                                  Pv_usrCreacion IN VARCHAR2,
                                  Pv_ipCreacion  IN VARCHAR2,
                                  Pv_error       OUT VARCHAR2);
                                  
  /**
   * Documentación para F_SERVICIO_TIENE_CARAC
   * Función que valida que un servicio tenga producto adicional KONIBIT
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 25/11/2019
   *
   * @param Pn_idServicio     IN NUMBER   Id del Servicio a validar
   * @param Pv_caracteristica IN VARCHAR2 Caracteristica a evaluar "KONIBIT"
   *
   * @return BOOLEAN TRUE si tiene producto adicional, FALSE si no lo tiene
   *
   */
  FUNCTION F_SERVICIO_TIENE_CARAC(Pn_idServicio     IN NUMBER,
                                   Pv_caracteristica IN VARCHAR2)
  RETURN BOOLEAN;
  
  /**
   * Documentación para F_PLAN_TIENE_CARAC
   * Función que valida que un plan tenga producto KONIBIT
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 25/11/2019
   *
   * @param Pn_idServicio     IN NUMBER   Id del Servicio a validar
   * @param Pv_caracteristica IN VARCHAR2 Caracteristica a evaluar "KONIBIT"
   *
   * @return BOOLEAN TRUE si tiene producto en el plan, FALSE si no lo tiene
   *
   */
  FUNCTION F_PLAN_TIENE_CARAC(Pn_idServicio         IN NUMBER,
                               Pv_caracteristica IN VARCHAR2)
  RETURN BOOLEAN;
  
  /**
   * Documentación para F_OBTIENE_CONTACT_CUENTA
   * Función que obtiene los contactos de un cliente
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 25/11/2019
   *
   * @param Pn_idPersonaRol  IN NUMBER   Id Empresa Rol del Cliente
   * @param Pv_formaContacto IN VARCHAR2 Caracteristica a evaluar "Correo Electronico"
   *
   * @return VARCHAR2 Todos los contactos indicado, separados por coma
   *
   */
  FUNCTION F_OBTIENE_CONTACT_CUENTA(Pn_idPersonaRol  IN NUMBER,
                                     Pv_formaContacto IN VARCHAR2)
  RETURN VARCHAR2;
  
  /**
   * Documentación para F_OBTIENE_CICLO_CUENTA
   * Función que obtiene el cliclo de facturación de un cliente
   *
   * @author José Bedón Sánchez <jobedon@telconet.ec>
   * @version 1.0 25/11/2019
   *
   * @param Pn_idPersonaRol  IN NUMBER   Id Empresa Rol del Cliente
   * @param Pv_caracteristica IN VARCHAR2 Caracteristica a evaluar "CICLO_FACTURACION"
   *
   * @return VARCHAR2 Ciclo de Facturación del Cliente
   *
   */
  FUNCTION F_OBTIENE_CICLO_CUENTA(Pn_idPersonaRol   IN NUMBER,
                                    Pv_caracteristica IN VARCHAR2)
  RETURN VARCHAR2;
  
  /**
   * F_SERVICIO_CARACTERISTICA, valida si un servicio tiene un producto konibit
   * @author Ivan Mata <imata@telconet.ec>
   * @version 13-03-2020
   * @since 1.0
   *
   * @return VARCHAR2 TRUE si tiene producto en el plan, FALSE si no lo tiene
   
   */
  FUNCTION F_SERVICIO_CARACTERISTICA(Pn_idServicio     IN NUMBER,
                                     Pv_caracteristica IN VARCHAR2)
  RETURN VARCHAR2;
  
END INFRKG_KONIBIT;

/

create or replace PACKAGE BODY DB_INFRAESTRUCTURA.INFRKG_KONIBIT
AS


  PROCEDURE P_ENVIA_NOTIFICACION(Pn_idServicio  IN NUMBER,
                                  Pv_tipoProceso IN VARCHAR2,
                                  Pv_tipoTrx     IN VARCHAR2,
                                  Pv_usrCreacion IN VARCHAR2,
                                  Pv_ipCreacion  IN VARCHAR2,
                                  Pv_error       OUT VARCHAR2)
  IS
    
    CURSOR C_OBTIENE_INFO_GENERAL(Cn_idServicio NUMBER)
    IS
      SELECT
        DB_COMERCIAL.PERSONA_EMPRESA_ROL.ID_PERSONA_ROL ID_PERSONA_ROL,
        PERSONA_EMPRESA_ROL.OFICINA_ID OFICINA_ID,
        PERSONA.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE,
        DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_CANTON_FORMA_PAGO(PERSONA_EMPRESA_ROL.ID_PERSONA_ROL ,NULL)  FORMA_DE_PAGO,
        DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS.F_INFORMACION_CONTRATO_CLI('NUMERO_CONTRATO', PERSONA_EMPRESA_ROL.ID_PERSONA_ROL, PERSONA_EMPRESA_ROL.ESTADO) CONTRATO,
        DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS.F_INFORMACION_CONTRATO_CLI('DESCRIPCION_CUENTA', PERSONA_EMPRESA_ROL.ID_PERSONA_ROL, PERSONA_EMPRESA_ROL.ESTADO) TIPO_CUENTA,
        DB_FINANCIERO.FNCK_FACTURACION.F_WS_FECHA_PAGO_FACTURA(PERSONA_EMPRESA_ROL.ID_PERSONA_ROL, EMPRESA_ROL.EMPRESA_COD)  FECHA_PAGO,
        DB_INFRAESTRUCTURA.INFRKG_KONIBIT.F_OBTIENE_CONTACT_CUENTA(PERSONA_EMPRESA_ROL.ID_PERSONA_ROL, 'Correo Electronico') CORREOS,
        DB_INFRAESTRUCTURA.INFRKG_KONIBIT.F_OBTIENE_CICLO_CUENTA(PERSONA_EMPRESA_ROL.ID_PERSONA_ROL, 'CICLO_FACTURACION') CICLO_FACT,
        CASE
          WHEN PERSONA.RAZON_SOCIAL IS NOT NULL
          THEN PERSONA.RAZON_SOCIAL
          ELSE PERSONA.NOMBRES || ' '|| PERSONA.APELLIDOS
        END NOMBRES
      FROM 
        DB_COMERCIAL.INFO_PERSONA PERSONA
        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_EMPRESA_ROL ON PERSONA.ID_PERSONA                 = PERSONA_EMPRESA_ROL.PERSONA_ID
        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL                 ON PERSONA_EMPRESA_ROL.EMPRESA_ROL_ID = EMPRESA_ROL.ID_EMPRESA_ROL
        INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO                             ON PUNTO.PERSONA_EMPRESA_ROL_ID       = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
        INNER JOIN DB_COMERCIAL.INFO_SERVICIO SERVICIO                       ON SERVICIO.PUNTO_ID                  = PUNTO.ID_PUNTO
      WHERE 
        EMPRESA_ROL.EMPRESA_COD = 18
        AND SERVICIO.ID_SERVICIO = Cn_idServicio
        AND ROWNUM<=1;
    
    CURSOR C_OBTIENE_PUNTOS_POR_CTA(Cn_idPersonaEmpresaRol NUMBER, Cn_idServicio NUMBER)
    IS
      SELECT 
        PUNTO.ID_PUNTO ID_PUNTO,
        PUNTO.LOGIN LOGIN,
        JURI.NOMBRE_JURISDICCION COBERTURA,
        PUNTO.DIRECCION,
        SECTOR.NOMBRE_SECTOR SECTOR,
        CANTON.NOMBRE_CANTON CIUDAD,
        PUNTO.LONGITUD,
        PUNTO.LATITUD,
        PUNTO.ESTADO,
        ( SELECT LISTAGG(IPFC.VALOR, ';')
        WITHIN GROUP (ORDER BY IPFC.PUNTO_ID)
        FROM DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPFC, DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC
        WHERE   PUNTO.ID_PUNTO     = IPFC.PUNTO_ID
        AND IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO AND
        DESCRIPCION_FORMA_CONTACTO IN ('Telefono Movil','Telefono Fijo','Telefono Movil Claro',
        'Telefono Movil Movistar','Telefono Movil CNT','Telefono Traslado'
        ) AND rownum <=2) TELEFONOS,
        NVL(TRIM(DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO(PUNTO.ID_PUNTO, 'MAIL')),'')  CORREOS,
        TRIM(TO_CHAR(TRUNC(NVL(DB_FINANCIERO.FNCK_COM_ELECTRONICO.F_GET_SALDO_CLIENTE_BY_PUNTO(PUNTO.ID_PUNTO), 0),2),'99999999990D99')) SALDO
      FROM 
        DB_COMERCIAL.INFO_PUNTO PUNTO
        LEFT JOIN DB_COMERCIAL.ADMI_SECTOR SECTOR                        ON SECTOR.ID_SECTOR           = PUNTO.SECTOR_ID
        LEFT JOIN DB_COMERCIAL.ADMI_PARROQUIA PARROQUIA                  ON SECTOR.PARROQUIA_ID        = PARROQUIA.ID_PARROQUIA
        LEFT JOIN DB_COMERCIAL.ADMI_CANTON CANTON                        ON PARROQUIA.canton_id        = CANTON.ID_CANTON
        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_CANTON_JURISDICCION CANTON_JUR ON PARROQUIA.canton_id        = CANTON_JUR.CANTON_ID
        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_JURISDICCION JURI              ON CANTON_JUR.JURISDICCION_ID = JURI.ID_JURISDICCION
        LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO OFI_GRU                ON JURI.OFICINA_ID            = OFI_GRU.ID_OFICINA
      WHERE 
        OFI_GRU.EMPRESA_ID = 18
        AND PUNTO.PERSONA_EMPRESA_ROL_ID= Cn_idPersonaEmpresaRol
        AND EXISTS (SELECT 1 FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO WHERE SERVICIO.PUNTO_ID = PUNTO.ID_PUNTO AND SERVICIO.ID_SERVICIO = Cn_idServicio);
    
    CURSOR C_SERVICIOS_POR_PUNTO(Cn_idPunto NUMBER, Cn_idServicio NUMBER)
    IS
      SELECT
        SERVICIO.ID_SERVICIO,
        PROD.CODIGO_PRODUCTO COD_PROD,
        CASE
          WHEN PROD1.CODIGO_PRODUCTO IS NOT NULL
          THEN PROD1.CODIGO_PRODUCTO
          ELSE PROD.CODIGO_PRODUCTO
        END CODIGO_PRODUCTO,
        CASE
          WHEN PROD1.DESCRIPCION_PRODUCTO IS NOT NULL
          THEN PROD1.DESCRIPCION_PRODUCTO
          ELSE PROD.DESCRIPCION_PRODUCTO
        END PRODUCTO,
        UM.NOMBRE_TIPO_MEDIO ULTIMA_MILLA,
        PLANC.ID_PLAN,
        PLANC.NOMBRE_PLAN,
        SERVICIO.ESTADO,
        SERVICIO.LOGIN_AUX,
        (SERVICIO.PRECIO_VENTA*SERVICIO.CANTIDAD) VALOR
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO
        LEFT JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO TECNICO ON TECNICO.SERVICIO_ID = SERVICIO.ID_SERVICIO
        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_MEDIO UM      ON UM.ID_TIPO_MEDIO    = TECNICO.ULTIMA_MILLA_ID
        LEFT JOIN DB_COMERCIAL.INFO_PLAN_CAB PLANC           ON PLANC.ID_PLAN       = SERVICIO.PLAN_ID
        LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET PLAND           ON PLAND.PLAN_ID       = PLANC.ID_PLAN
        LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD            ON PROD.ID_PRODUCTO    = PLAND.PRODUCTO_ID
        LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD1           ON PROD1.ID_PRODUCTO   = SERVICIO.PRODUCTO_ID
      WHERE
        ( SERVICIO.PLAN_ID IS NULL
        OR ( SERVICIO.PLAN_ID   > 0
        AND (PLAND.ESTADO)      = (PLANC.ESTADO) ) )       
        AND SERVICIO.PUNTO_ID   = Cn_idPunto
        AND SERVICIO.ID_SERVICIO = Cn_idServicio;
        
    CURSOR C_PARAMETROS_WS(Cv_EmpresaId VARCHAR2, Cv_NombreParametro VARCHAR2) 
    IS
      SELECT 
        DET.VALOR1,
        DET.VALOR2,
        DET.VALOR3,
        DET.VALOR4
      FROM 
        DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
        DB_GENERAL.ADMI_PARAMETRO_DET DET
      WHERE CAB.ID_PARAMETRO   =  DET.PARAMETRO_ID
      AND CAB.ESTADO           = 'Activo'
      AND DET.ESTADO           = 'Activo'
      AND CAB.MODULO           = 'INFRAESTRUCTURA'
      AND DET.EMPRESA_COD      =  Cv_EmpresaId
      AND CAB.NOMBRE_PARAMETRO =  Cv_NombreParametro;
      
    CURSOR C_GET_MAP_TRX_PROC(Cv_parametro VARCHAR2, Cv_valor1 VARCHAR2)
    IS
      SELECT 
        VALOR1,
        VALOR2
      FROM 
        DB_GENERAL.ADMI_PARAMETRO_CAB APC,
        DB_GENERAL.ADMI_PARAMETRO_DET APD
      WHERE
        APC.ID_PARAMETRO = APD.PARAMETRO_ID
        AND APC.ESTADO = 'Activo'
        AND APD.ESTADO = 'Activo'
        AND APC.NOMBRE_PARAMETRO = Cv_parametro
        AND APD.VALOR1 = Cv_valor1;
    
    Lb_tieneProdCarac BOOLEAN      := FALSE;
    Lb_tienePlanCarac BOOLEAN      := FALSE;
    Lv_caracteristica VARCHAR2(20) := 'KONIBIT';
    Lc_infoBasica     C_OBTIENE_INFO_GENERAL%ROWTYPE;
    Lc_punto          C_OBTIENE_PUNTOS_POR_CTA%ROWTYPE;
    Lc_servicio       C_SERVICIOS_POR_PUNTO%ROWTYPE;
    Lc_ParametrosWs   C_PARAMETROS_WS%ROWTYPE;
    Lc_ParametrosGDA  C_PARAMETROS_WS%ROWTYPE;
    Lc_map_proc       C_GET_MAP_TRX_PROC%ROWTYPE;
    Lc_map_trx        C_GET_MAP_TRX_PROC%ROWTYPE;
    Lv_login          DB_COMERCIAL.INFO_PUNTO.LOGIN%TYPE;
    Lc_jsonResponse   CLOB;
    Lv_procesoParam   VARCHAR2(50) := 'PROCESO_WS_GDA';
    Lv_paramGDA       VARCHAR2(50) := 'PARAM_WS_GDA';
    Lcl_Respuesta     CLOB;
    Lv_Error          VARCHAR2(4000);
    
    Lv_Codigo         VARCHAR2(30) := ROUND(DBMS_RANDOM.VALUE(1000,9999))||TO_CHAR(SYSDATE,'DDMMRRRRHH24MISS');
    Lv_schema         VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck            VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_prc            VARCHAR2(50) := 'P_ENVIA_NOTIFICACION';
    Lv_estado         VARCHAR2(10);
    Le_infoBasica     EXCEPTION;
    Le_customError    EXCEPTION;
  BEGIN 
  
    -- Se validan procesos
    IF C_GET_MAP_TRX_PROC%ISOPEN THEN
      CLOSE C_GET_MAP_TRX_PROC;
    END IF;
    
    OPEN C_GET_MAP_TRX_PROC('TIPOS_PROCESOS_KONIBIT', Pv_tipoProceso);
    FETCH C_GET_MAP_TRX_PROC INTO Lc_map_proc;
    CLOSE C_GET_MAP_TRX_PROC;
       
    IF Lc_map_proc.VALOR1 IS NULL THEN
      Lv_error := 'Proceso ' || Pv_tipoProceso || ' no soportado';
      RAISE Le_customError;
    END IF;
    
    -- Se validan transacciones
    IF C_GET_MAP_TRX_PROC%ISOPEN THEN
      CLOSE C_GET_MAP_TRX_PROC;
    END IF;
    
    OPEN C_GET_MAP_TRX_PROC('TIPOS_TRX_KONIBIT', Pv_tipoTrx);
    FETCH C_GET_MAP_TRX_PROC INTO Lc_map_trx;
    CLOSE C_GET_MAP_TRX_PROC;
       
    IF Lc_map_trx.VALOR1 IS NULL THEN
      Lv_Error := 'Transaccion ' || Pv_tipoTrx || ' no soportado';
      RAISE Le_customError;
    END IF;
  
    -- Solo se valida la caracteristica en plan y producto adicional si es que la transaccion es individual
    -- Para masivos se realizan las validaciones en los respectivos procesos
    IF (Lc_map_trx.VALOR2 = 'INDIVIDUAL') THEN
      Lb_tieneProdCarac := F_SERVICIO_TIENE_CARAC(Pn_idServicio,Lv_caracteristica);
      Lb_tienePlanCarac := F_PLAN_TIENE_CARAC(Pn_idServicio,Lv_caracteristica);
      IF (NOT Lb_tieneProdCarac AND NOT Lb_tienePlanCarac) THEN
        Lv_Error := 'Servicio no contiene un producto KONIBIT';
        RAISE Le_customError;
      END IF;
    END IF;
    
    -- Recupero Informacion de la cabecera
    IF C_OBTIENE_INFO_GENERAL%ISOPEN THEN
      CLOSE C_OBTIENE_INFO_GENERAL;
    END IF;
    
    OPEN C_OBTIENE_INFO_GENERAL(Pn_idServicio);
    FETCH C_OBTIENE_INFO_GENERAL INTO Lc_infoBasica;
    CLOSE C_OBTIENE_INFO_GENERAL;
    
    IF Lc_infoBasica.IDENTIFICACION_CLIENTE IS NULL THEN
      RAISE Le_infoBasica;
    END IF;
    
    
    -- Obtengo parametros generales para el webservice a GDA
    IF C_PARAMETROS_WS%ISOPEN THEN
      CLOSE C_PARAMETROS_WS;
    END IF;

    OPEN C_PARAMETROS_WS(18, Lv_paramGDA);
     FETCH C_PARAMETROS_WS 
        INTO Lc_ParametrosGDA;
    CLOSE C_PARAMETROS_WS;
    
    --Se construye JSON Request    
    
    APEX_JSON.INITIALIZE_CLOB_OUTPUT;
    APEX_JSON.OPEN_OBJECT;
    
  
    APEX_JSON.OPEN_OBJECT('datos');
    
    APEX_JSON.WRITE('cliente',           Lc_infoBasica.NOMBRES);
    APEX_JSON.WRITE('identificacion',    Lc_infoBasica.IDENTIFICACION_CLIENTE);
    APEX_JSON.WRITE('correos',           Lc_infoBasica.CORREOS);
    APEX_JSON.WRITE('id_oficina',        Lc_infoBasica.OFICINA_ID);
    APEX_JSON.WRITE('ciclo_facturacion', Lc_infoBasica.CICLO_FACT);
    APEX_JSON.WRITE('forma_pago',        Lc_infoBasica.FORMA_DE_PAGO);
    APEX_JSON.WRITE('contrato',          Lc_infoBasica.CONTRATO);
    APEX_JSON.WRITE('tipo_cuenta',       Lc_infoBasica.TIPO_CUENTA);
    APEX_JSON.WRITE('fecha_maxima_pago', Lc_infoBasica.FECHA_PAGO);
    APEX_JSON.WRITE('correos',           Lc_infoBasica.CORREOS);
    
    APEX_JSON.OPEN_ARRAY('puntos');
    
    -- Recupero información del Punto donde se encuentra el servicio
    FOR Lc_punto IN C_OBTIENE_PUNTOS_POR_CTA(Lc_infoBasica.ID_PERSONA_ROL, Pn_idServicio)
    LOOP
      APEX_JSON.OPEN_OBJECT;
      APEX_JSON.WRITE('id_punto',  Lc_punto.ID_PUNTO);
      APEX_JSON.WRITE('login',     Lc_punto.LOGIN);
      APEX_JSON.WRITE('cobertura', Lc_punto.COBERTURA);
      APEX_JSON.WRITE('direccion', Lc_punto.DIRECCION);
      APEX_JSON.WRITE('ciudad',    Lc_punto.CIUDAD);
      APEX_JSON.WRITE('sector',    Lc_punto.SECTOR);
      APEX_JSON.WRITE('longitud',  Lc_punto.LONGITUD);
      APEX_JSON.WRITE('latitud',   Lc_punto.LATITUD);
      APEX_JSON.WRITE('estado',    Lc_punto.ESTADO);
      APEX_JSON.WRITE('telefonos', Lc_punto.TELEFONOS);
      APEX_JSON.WRITE('correos',   Lc_punto.CORREOS);
      APEX_JSON.WRITE('saldo',     Lc_punto.SALDO);
      
      Lv_login := Lc_punto.LOGIN;
      
      APEX_JSON.OPEN_ARRAY('servicios');
      
      -- Recupero información de los Servicios que contiene el servicio
      FOR Lc_servicio IN C_SERVICIOS_POR_PUNTO(Lc_punto.ID_PUNTO, Pn_idServicio)
      LOOP
        APEX_JSON.OPEN_OBJECT;
        APEX_JSON.WRITE('id_servicio', Lc_servicio.ID_SERVICIO);
        
        APEX_JSON.WRITE('codigo_producto', Lc_servicio.CODIGO_PRODUCTO);
        APEX_JSON.WRITE('producto',        Lc_servicio.PRODUCTO);
        APEX_JSON.WRITE('ultima_milla',    Lc_servicio.ULTIMA_MILLA);
        APEX_JSON.WRITE('id_plan',         Lc_servicio.ID_PLAN);
        APEX_JSON.WRITE('plan',            Lc_servicio.NOMBRE_PLAN);
        APEX_JSON.WRITE('estado',          Lc_servicio.ESTADO);
        APEX_JSON.WRITE('login_aux',       Lc_servicio.LOGIN_AUX);
        APEX_JSON.WRITE('valor',           Lc_servicio.VALOR);
        APEX_JSON.CLOSE_OBJECT;
      END LOOP;
      APEX_JSON.CLOSE_ARRAY();
      
      APEX_JSON.CLOSE_OBJECT;
    END LOOP;
    APEX_JSON.CLOSE_ARRAY();
    
    APEX_JSON.CLOSE_OBJECT;
    
    APEX_JSON.WRITE('empresa',              Lc_ParametrosGDA.VALOR1);
    APEX_JSON.WRITE('login',                Lv_login);
    APEX_JSON.WRITE('identificacion',       Lc_infoBasica.IDENTIFICACION_CLIENTE);
    APEX_JSON.WRITE('opcion',               Lc_map_proc.VALOR2);
    APEX_JSON.WRITE('ejecutaComando',       Lc_ParametrosGDA.VALOR2);
    APEX_JSON.WRITE('usrCreacion',          Pv_usrCreacion);
    APEX_JSON.WRITE('ipCreacion',           Pv_ipCreacion);
    APEX_JSON.WRITE('comandoConfiguracion', Lc_ParametrosGDA.VALOR3);
    
    APEX_JSON.CLOSE_OBJECT;
    
    Lc_jsonResponse := APEX_JSON.GET_CLOB_OUTPUT;
    APEX_JSON.free_output;
    
    
    -- Obtengo datos de los parametros
    IF C_PARAMETROS_WS%ISOPEN THEN
      CLOSE C_PARAMETROS_WS;
    END IF;

    OPEN C_PARAMETROS_WS(18, Lv_procesoParam);
     FETCH C_PARAMETROS_WS 
        INTO Lc_ParametrosWs;
    CLOSE C_PARAMETROS_WS;
        
    DB_GENERAL.GNKG_WEB_SERVICE.P_WEB_SERVICE(Pv_Url             => Lc_ParametrosWs.VALOR1,
                                              Pcl_Mensaje        => Lc_jsonResponse, --Lc_jsonResponse
                                              Pv_Application     => Lc_ParametrosWs.VALOR3,
                                              Pv_Charset         => Lc_ParametrosWs.VALOR4,
                                              Pv_UrlFileDigital  => NULL,
                                              Pv_PassFileDigital => NULL,
                                              Pcl_Respuesta      => Lcl_Respuesta,
                                              Pv_Error           => Lv_Error);
    
    
    IF Lv_Error IS NOT NULL THEN
      Lv_estado := 'ERROR';
    ELSE
      Lv_estado := 'OK';
    END IF;
    
    IF (Lc_ParametrosGDA.VALOR4 = 'S' OR Lv_Error IS NOT NULL) THEN
    
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                           Lv_prc, 
                                           Lv_Codigo ||'| Estado : ' || Lv_estado  ||  ' - idServicio : ' || Pn_idServicio || ' - tipoProceso : ' || Pv_tipoProceso || 
                                           ' - tipoTrx: '|| Pv_tipoTrx, 
                                           Lv_schema, 
                                           SYSDATE, 
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  
      Lcl_Respuesta := SUBSTR(Lcl_Respuesta,0,3000);
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck,
                                           Lv_prc,
                                           Lv_Codigo||'| Estado : ' || Lv_estado || ' - Respuesta : ' ||Lcl_Respuesta,
                                           Lv_schema,
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
                                           
    END IF;
    
    
    EXCEPTION
    
      WHEN Le_customError THEN
        Pv_error := Lv_error;
    
      WHEN Le_infoBasica THEN
      
        Lv_Error := SUBSTR(Lv_Codigo ||'| Estado : ERROR_INFO_BASICA - idServicio : ' || Pn_idServicio || ' - tipoProceso : ' || Pv_tipoProceso || 
                           ' - tipoTrx: '|| Pv_tipoTrx || ' - Error : ' || SQLERRM,0,3000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_prc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
        Pv_error := Lv_Error;
    
      WHEN OTHERS THEN
        
        Lv_Error := SUBSTR(Lv_Codigo ||'| Estado : ERROR_GENERAL - idServicio : ' || Pn_idServicio || ' - tipoProceso : ' || Pv_tipoProceso || 
                           ' - tipoTrx: '|| Pv_tipoTrx || ' - Error : ' || SQLERRM,0,3000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_prc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  
        Pv_error := Lv_Error;
    
  END P_ENVIA_NOTIFICACION;
  
  
  FUNCTION F_SERVICIO_TIENE_CARAC(Pn_idServicio     IN NUMBER,
                                   Pv_caracteristica IN VARCHAR2)
    RETURN BOOLEAN
  IS
    CURSOR C_PROD_CARC(Cn_idServicio NUMBER, Cv_caracteristica VARCHAR2)
    IS
      SELECT ISR.ID_SERVICIO,
        ISR.PRODUCTO_ID
      FROM DB_COMERCIAL.INFO_SERVICIO ISR
      WHERE ISR.PRODUCTO_ID IS NOT NULL
      AND ISR.ID_SERVICIO    = Cn_idServicio
      AND EXISTS
        (SELECT 1
        FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
          DB_COMERCIAL.ADMI_CARACTERISTICA AC
        WHERE APC.PRODUCTO_ID             = ISR.PRODUCTO_ID
        AND AC.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
        AND APC.ESTADO                    = 'Activo'
        AND AC.ESTADO                     = 'Activo'
        AND AC.DESCRIPCION_CARACTERISTICA = Cv_caracteristica
        );
    Ln_idServicio       NUMBER;
    Ln_idCaracteristica NUMBER;
    
    Lv_Error            VARCHAR2(4000);
    Lv_schema           VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck              VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_fnc              VARCHAR2(50) := 'F_SERVICIO_TIENE_CARAC';
    
  BEGIN
  
    IF C_PROD_CARC%ISOPEN THEN
      CLOSE C_PROD_CARC;
    END IF;
  
    OPEN C_PROD_CARC(Pn_idServicio, Pv_caracteristica);
    FETCH C_PROD_CARC INTO Ln_idServicio, Ln_idCaracteristica;
    IF Ln_idServicio IS NOT NULL THEN
      RETURN TRUE;
    END IF;
    RETURN FALSE;
    
    EXCEPTION
      WHEN OTHERS THEN
        
        Lv_Error := SUBSTR('Estado : ERROR - Pn_idServicio : ' || Pn_idServicio || ' - Pv_caracteristica : ' || Pv_caracteristica ||
                           ' - Code Error : ' || SQLCODE ||' - Error : ' || SQLERRM, 0, 4000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_fnc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  
        
        RETURN FALSE;
                                             
  END F_SERVICIO_TIENE_CARAC;
  
  
  
  FUNCTION F_PLAN_TIENE_CARAC(Pn_idServicio     IN NUMBER,
                               Pv_caracteristica IN VARCHAR2)
    RETURN BOOLEAN
  IS
    CURSOR C_PLAN_CARC(Cn_idServicio NUMBER, Cv_caracteristica VARCHAR2)
    IS
      SELECT ISR.ID_SERVICIO,
        AP.ID_PRODUCTO
      FROM DB_COMERCIAL.INFO_SERVICIO ISR,
        DB_COMERCIAL.INFO_PLAN_DET IPD,
        DB_COMERCIAL.ADMI_PRODUCTO AP
      WHERE ISR.PLAN_ID  IS NOT NULL
      AND ISR.PLAN_ID     = IPD.PLAN_ID
      AND AP.ID_PRODUCTO  = IPD.PRODUCTO_ID
      AND ISR.ID_SERVICIO = Cn_idServicio
      AND EXISTS
        (SELECT 1
        FROM ADMI_PRODUCTO_CARACTERISTICA APC,
          ADMI_CARACTERISTICA AC
        WHERE APC.PRODUCTO_ID             = AP.ID_PRODUCTO
        AND AC.ID_CARACTERISTICA          = APC.CARACTERISTICA_ID
        AND APC.ESTADO                    = 'Activo'
        AND AC.ESTADO                     = 'Activo'
        AND AC.DESCRIPCION_CARACTERISTICA = Cv_caracteristica
        );
    Ln_idServicio       NUMBER;
    Ln_idCaracteristica NUMBER;
    
    Lv_Error            VARCHAR2(4000);
    Lv_schema           VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck              VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_fnc              VARCHAR2(50) := 'F_PLAN_TIENE_CARAC';
  BEGIN
    
    IF C_PLAN_CARC%ISOPEN THEN
      CLOSE C_PLAN_CARC;
    END IF;
  
    OPEN C_PLAN_CARC(Pn_idServicio, Pv_caracteristica);
    FETCH C_PLAN_CARC INTO Ln_idServicio, Ln_idCaracteristica;
    CLOSE C_PLAN_CARC;
    IF Ln_idServicio IS NOT NULL THEN
      RETURN TRUE;
    END IF;
    RETURN FALSE;
    
    EXCEPTION
      WHEN OTHERS THEN
        
        Lv_Error := SUBSTR('Estado : ERROR - Pn_idServicio : ' || Pn_idServicio || ' - Pv_caracteristica : ' || Pv_caracteristica ||
                           ' - Code Error : ' || SQLCODE ||' - Error : ' || SQLERRM, 0, 4000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_fnc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
        
        RETURN FALSE;
        
  END F_PLAN_TIENE_CARAC;
  
  FUNCTION F_OBTIENE_CONTACT_CUENTA(Pn_idPersonaRol  IN NUMBER,
                                     Pv_formaContacto IN VARCHAR2)
  RETURN VARCHAR2
  IS
    CURSOR C_GET_CONTACTO_CUENTA(Cn_idPersonaRol NUMBER, Cv_formaContacto VARCHAR2)
    IS
      SELECT LISTAGG(ipc.valor,',') WITHIN GROUP (ORDER BY ipc.valor ) CORREO
      FROM DB_COMERCIAL.info_persona_forma_contacto ipc,
       DB_COMERCIAL.info_persona_empresa_rol iper,
       DB_COMERCIAL.info_persona ip,
       DB_COMERCIAL.admi_forma_contacto afc
      WHERE afc.descripcion_forma_contacto = Cv_formaContacto
      AND afc.id_forma_contacto            = ipc.forma_contacto_id 
      AND ipc.persona_id                   = ip.id_persona
      AND ip.id_persona                    = iper.persona_id
      AND iper.id_persona_rol              = Cn_idPersonaRol
      AND iper.estado                      = 'Activo'
      AND ipc.estado                       = 'Activo'
      AND afc.estado                       = 'Activo';
      
    Lv_dato   VARCHAR2(3000);
    
    Lv_Error  VARCHAR2(4000);
    Lv_schema VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck    VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_fnc    VARCHAR2(50) := 'F_OBTIENE_CONTACT_CUENTA';
  BEGIN
  
    IF C_GET_CONTACTO_CUENTA%ISOPEN THEN
      CLOSE C_GET_CONTACTO_CUENTA;
    END IF;
  
    OPEN C_GET_CONTACTO_CUENTA(Pn_idPersonaRol, Pv_formaContacto);
    FETCH C_GET_CONTACTO_CUENTA INTO Lv_dato;
    CLOSE C_GET_CONTACTO_CUENTA;
    
    RETURN Lv_dato;
    
    
    EXCEPTION
      WHEN OTHERS THEN
        
        Lv_Error := SUBSTR('Estado : ERROR - Pn_idPersonaRol : ' || Pn_idPersonaRol || ' - Pv_formaContacto : ' || Pv_formaContacto ||
                           ' - Code Error : ' || SQLCODE ||' - Error : ' || SQLERRM, 0, 4000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_fnc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
        
        RETURN '';
  
  END F_OBTIENE_CONTACT_CUENTA;
  
  
  
  FUNCTION F_OBTIENE_CICLO_CUENTA(Pn_idPersonaRol  IN NUMBER,
                                   Pv_caracteristica IN VARCHAR2)
  RETURN VARCHAR2
  IS
    CURSOR C_OBTIENE_CICLO_CTA(Cn_idPersonaRol NUMBER, Cv_formaContacto VARCHAR2)
    IS
      SELECT 
        CI.NOMBRE_CICLO
      FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
        DB_COMERCIAL.ADMI_CARACTERISTICA CA,
        DB_FINANCIERO.ADMI_CICLO CI
      WHERE IPER.ID_PERSONA_ROL = Cn_idPersonaRol
      AND IPERC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
      AND IPERC.CARACTERISTICA_ID = CA.ID_CARACTERISTICA
      AND CA.DESCRIPCION_CARACTERISTICA = Cv_formaContacto
      AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = CI.ID_CICLO
      AND IPERC.ESTADO = 'Activo'
      AND ROWNUM = 1 ;
      
    Lv_dato   VARCHAR2(100);
    
    Lv_Error  VARCHAR2(4000);
    Lv_schema VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck    VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_fnc    VARCHAR2(50) := 'F_OBTIENE_CICLO_CUENTA';
    
  BEGIN
  
    OPEN C_OBTIENE_CICLO_CTA(Pn_idPersonaRol, Pv_caracteristica);
    FETCH C_OBTIENE_CICLO_CTA INTO Lv_dato;
    CLOSE C_OBTIENE_CICLO_CTA;
    
    RETURN Lv_dato;
    
    
    EXCEPTION
      WHEN OTHERS THEN
        
        Lv_Error := SUBSTR('Estado : ERROR - Pn_idPersonaRol : ' || Pn_idPersonaRol || ' - Pv_caracteristica : ' || Pv_caracteristica ||
                           ' - Code Error : ' || SQLCODE ||' - Error : ' || SQLERRM, 0, 4000);
                           
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_fnc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
        
        RETURN '';
  
  END F_OBTIENE_CICLO_CUENTA;
  
  FUNCTION F_SERVICIO_CARACTERISTICA(Pn_idServicio     IN NUMBER,
                                   Pv_caracteristica IN VARCHAR2)
    RETURN VARCHAR2
  IS
    CURSOR C_PROD_CARC(Cn_idServicio NUMBER, Cv_caracteristica VARCHAR2)
    IS
      SELECT
          isr.id_servicio
      FROM
          db_comercial.info_servicio isr
      WHERE
          isr.id_servicio = Cn_idServicio
          AND ( EXISTS (
              SELECT
                  1
              FROM
                  db_comercial.info_plan_cab                  cab,
                  db_comercial.info_plan_det                  det,
                  db_comercial.admi_producto                  prod,
                  db_comercial.admi_producto_caracteristica   apc,
                  db_comercial.admi_caracteristica            ac
              WHERE
                  det.plan_id = isr.plan_id
                  AND det.producto_id = prod.id_producto
                  AND apc.producto_id = prod.id_producto
                  AND ac.id_caracteristica = apc.caracteristica_id
                  AND apc.estado = 'Activo'
                  AND ac.estado = 'Activo'
                  AND ac.descripcion_caracteristica = Cv_caracteristica
          )
                OR EXISTS (
              SELECT
                  1
              FROM
                  db_comercial.admi_producto_caracteristica   apc,
                  db_comercial.admi_caracteristica            ac
              WHERE
                  apc.producto_id = isr.producto_id
                  AND ac.id_caracteristica = apc.caracteristica_id
                  AND apc.estado = 'Activo'
                  AND ac.estado = 'Activo'
                  AND ac.descripcion_caracteristica = Cv_caracteristica
          ) );
    Ln_idServicio       NUMBER;
    Ln_idCaracteristica NUMBER;

    Lv_Error            VARCHAR2(4000);
    Lv_schema           VARCHAR2(25) := 'DB_INFRAESTRUCTURA';
    Lv_pck              VARCHAR2(50) := 'INFRKG_KONIBIT';
    Lv_fnc              VARCHAR2(50) := 'F_SERVICIO_CARACTERISTICA';
    Lv_Mensaje          VARCHAR2(10) := 'False';

  BEGIN

    IF C_PROD_CARC%ISOPEN THEN
      CLOSE C_PROD_CARC;
    END IF;

    OPEN C_PROD_CARC(Pn_idServicio, Pv_caracteristica);
    FETCH C_PROD_CARC INTO Ln_idServicio;
    CLOSE C_PROD_CARC;

    IF Ln_idServicio IS NOT NULL THEN
      Lv_Mensaje := 'True'; 
    END IF;
    RETURN Lv_Mensaje;

    EXCEPTION
      WHEN OTHERS THEN

        Lv_Error := SUBSTR('Estado : ERROR - Pn_idServicio : ' || Pn_idServicio || ' - Pv_caracteristica : ' || Pv_caracteristica ||
                           ' - Code Error : ' || SQLCODE ||' - Error : ' || SQLERRM, 0, 4000);

        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR(Lv_pck, 
                                             Lv_fnc, 
                                             Lv_Error, 
                                             Lv_schema, 
                                             SYSDATE, 
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));


        RETURN Lv_Mensaje;

  END F_SERVICIO_CARACTERISTICA;
  
END INFRKG_KONIBIT;

/
