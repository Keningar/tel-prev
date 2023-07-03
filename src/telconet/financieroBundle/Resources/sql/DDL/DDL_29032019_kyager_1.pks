   /**
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0
    * Script para consumir ws y obtener tipo de cambio del día.
    */
    
create or replace PACKAGE DB_FINANCIERO.FNKG_TIPO_CAMBIO
AS
  /*
  * Documentación para TYPE 'Lr_ResponseTipoCambioType'.
  * Record que me permite almancernar el resultado deL Xml
  *
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 1-03-2019
  */
TYPE Lr_ResponseTipoCambioType
IS
  RECORD
  (
    VALOR DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.VALOR%TYPE,
    QUETZAL DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.VALOR%TYPE,
    TIPO_CAMBIO DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.VALOR%TYPE,
    FECHA DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.VALOR%TYPE,
    SUBTOTAL DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL%TYPE,
    DETALLE VARCHAR2(32767));
  /**
  * Documentacion para la funcion F_GET_INFO_DOCUMENTO_CARAC
  *
  * La funcion retorna el valor en quetzales del documento.
  *
  * @param Fv_CaracteristicaId      IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.CARACTERISTICA_ID%TYPE
  * @param Fn_DocumentoId           IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.DOCUMENTO_ID%TYPE
  *
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 21-03-2019
  */
  FUNCTION F_GET_INFO_DOCUMENTO_CARAC(
      Fn_CaracteristicaId IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.CARACTERISTICA_ID%TYPE,
      Fn_DocumentoId      IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.DOCUMENTO_ID%TYPE)
    RETURN SYS_REFCURSOR;
  /*
  * Documentacion para el procedimiento 'P_WS_TIPO_CAMBIO'
  *
  * Procedimiento que genera xml y toma resultado del tipo de cambio.
  * para actualizar en la tabla
  *
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 1-03-2019
  */
  PROCEDURE P_WS_TIPO_CAMBIO(
      Pn_Id_Documento  IN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE,
      Pv_Fecha_Emision IN VARCHAR2,
      Pv_Usuario       IN VARCHAR2,
      Pv_CodigoError OUT VARCHAR2,
      Pv_MensajeError OUT VARCHAR2);
  /*
  * Documentacion para el procedimiento 'P_WS_TIPO_CAMBIO'
  *
  * Procedimiento que consume web service del tipo de cambio.
  *
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 1-03-2019
  */
  PROCEDURE P_VALIDA_TIPO_CAMBIO(
      Pv_Xml          IN XMLTYPE,
      Pr_responseTipo IN OUT FNKG_TIPO_CAMBIO.Lr_ResponseTipoCambioType,
      Pv_Namespace    IN VARCHAR2);
END FNKG_TIPO_CAMBIO;
/

create or replace PACKAGE BODY DB_FINANCIERO.FNKG_TIPO_CAMBIO
AS
  PROCEDURE P_WS_TIPO_CAMBIO(
      Pn_Id_Documento  IN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE,
      Pv_Fecha_Emision IN VARCHAR2,
      Pv_Usuario       IN VARCHAR2,
      Pv_CodigoError OUT VARCHAR2,
      Pv_MensajeError OUT VARCHAR2)
  AS
    CURSOR C_WsParametros
    IS
      SELECT APD.DESCRIPCION AS CODIGO,
        APD.VALOR1           AS DESCRIPCION
      FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
      WHERE APD.ESTADO    ='Activo'
      AND APD.PARAMETRO_ID=
        (SELECT APC.ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
        WHERE APC.NOMBRE_PARAMETRO IN ('WEB_SERVICE_PARAMETROS')
        )
    AND APD.EMPRESA_COD=
      (SELECT IEG.COD_EMPRESA
      FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG
      WHERE IEG.NOMBRE_EMPRESA='TELCONET GUATEMALA'
      );
    --
    CURSOR C_WsMetodo
    IS
      SELECT APD.VALOR1 AS DESCRIPCION
      FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
      WHERE ESTADO        ='Activo'
      AND APD.PARAMETRO_ID=
        (SELECT APC.ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
        WHERE APC.NOMBRE_PARAMETRO IN ('WEB_SERVICE_METODO')
        )
    AND APD.EMPRESA_COD=
      (SELECT IEG.COD_EMPRESA
      FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG
      WHERE IEG.NOMBRE_EMPRESA='TELCONET GUATEMALA'
      );
    --
    CURSOR C_Subtotal
    IS
      SELECT IDF.SUBTOTAL
      FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDF
      WHERE IDF.id_documento=Pn_Id_Documento;
    --
    CURSOR C_Id_Carac
    IS
      SELECT AC.ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC
      WHERE AC.DESCRIPCION_CARACTERISTICA='VALOR DOC QUETZALES';
    --
    l_request NAF47_TNET.SOAP_API.t_request;
    l_response NAF47_TNET.SOAP_API.t_response;
    l_url               VARCHAR2(32767);
    l_namespace         VARCHAR2(32767);
    l_method            VARCHAR2(32767);
    l_method_url        VARCHAR2(32767);
    l_soap_action       VARCHAR2(32767);
    l_pathCert          VARCHAR2(32767);
    l_pswCert           VARCHAR2(32767);
    l_caracteristica_id VARCHAR2(32767);
    l_InfoDocCarac      VARCHAR2(32767);
    --
    Lr_ResponseTipoCambio FNKG_TIPO_CAMBIO.Lr_ResponseTipoCambioType;
    Lr_InfoDocCarac DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE;
    Lrf_InfoDocCarac SYS_REFCURSOR;
    Le_Error      EXCEPTION;
    Lv_auxCia     VARCHAR2(2)    := '@';
    Lv_noRetError VARCHAR2(1000) := NULL;
  --
  BEGIN
    -- se recuperan parametros de webservice
    FOR lr_parametro IN C_WsParametros
    LOOP
      CASE lr_parametro.codigo
      WHEN 'URL' THEN
        l_url := lr_parametro.descripcion;
      WHEN 'NAMESPACE' THEN
        l_namespace := lr_parametro.descripcion;
      WHEN 'PATH_CERT' THEN
        l_pathCert := lr_parametro.descripcion;
      WHEN 'PWS_CERT' THEN
        l_pswCert := lr_parametro.descripcion;
      ELSE
        Lr_ResponseTipoCambio.detalle := 'No existen parámetros configurados';
      END CASE;
    END LOOP;
    -- validaciones de parametros
    CASE
    WHEN l_url                      IS NULL THEN
      Lr_ResponseTipoCambio.detalle := 'En parámetros generales WebService no se ha definido parámetro url';
      Raise Le_Error;
    WHEN l_namespace                IS NULL THEN
      Lr_ResponseTipoCambio.detalle := 'En parámetros generales WebService no se ha definido parámetro nameSpace';
      Raise Le_Error;
    WHEN instr(lower(l_url),'https')   = 0 THEN
      l_pathCert                      := NULL;
      l_pswCert                       := NULL;
    WHEN instr(lower(l_url),'https')   > 0 THEN
      IF l_pathCert                   IS NULL THEN
        Lr_ResponseTipoCambio.detalle := 'En parámetros generales WebService no se ha definido parámetro path_cert';
        Raise Le_Error;
      elsif l_pswCert                 IS NULL THEN
        Lr_ResponseTipoCambio.detalle := 'En parámetros generales WebService no se ha definido parámetro pws_cert';
        Raise Le_Error;
      END IF;
    END CASE;
    --
    IF C_WsMetodo%isopen THEN
      CLOSE C_WsMetodo;
    END IF;
    OPEN C_WsMetodo;
    FETCH C_WsMetodo INTO l_method;
    --
    IF C_WsMetodo%notfound THEN
      Lr_ResponseTipoCambio.Detalle := 'No se ha definido el método WEB_SERVICE_METODO en parámetros Generales';
      Raise Le_Error;
    END IF;
    CLOSE C_WsMetodo;
    --
    IF C_Subtotal%isopen THEN
      CLOSE C_Subtotal;
    END IF;
    OPEN C_Subtotal;
    FETCH C_Subtotal INTO Lr_ResponseTipoCambio.SUBTOTAL;
    IF C_Subtotal%notfound THEN
      Lr_ResponseTipoCambio.Detalle := 'No se ha obtenido el valor de la factura';
      Raise Le_Error;
    END IF;
    CLOSE C_Subtotal;
    --
    IF C_Id_Carac%isopen THEN
      CLOSE C_Id_Carac;
    END IF;
    OPEN C_Id_Carac;
    FETCH C_Id_Carac INTO l_caracteristica_id;
    IF C_Id_Carac%notfound THEN
      l_caracteristica_id := NULL;
      Raise Le_Error;
    END IF;
    CLOSE C_Id_Carac;
    --
    l_soap_action := trim(l_url||'='||l_method);
    --
    l_request:= NAF47_TNET.SOAP_API.new_request(p_method    => l_method, 
                                                p_namespace => l_namespace);
   --                                               
    NAF47_TNET.SOAP_API.add_parameter(p_request => l_request, 
                                      p_name    => 'fechainit', 
                                      p_type    => 'xs:string', 
                                      p_value   => Pv_Fecha_Emision);
    --                                  
    NAF47_TNET.SOAP_API.add_parameter(p_request => l_request, 
                                      p_name    => 'fechafin', 
                                      p_type    => 'xs:string', 
                                      p_value   => Pv_Fecha_Emision);
    --
    l_response    := NAF47_TNET.SOAP_API.invoke(p_request  => l_request, 
                                                p_url      => l_url, 
                                                p_action   => l_soap_action, 
                                                p_pathcert => l_pathCert, 
                                                p_pswcert  => l_pswCert, 
                                                p_error    => Lr_ResponseTipoCambio.detalle );
    --
    IF Lr_ResponseTipoCambio.detalle    IS NOT NULL AND Lr_ResponseTipoCambio.detalle!='No existen parámetros configurados' THEN
      Lr_ResponseTipoCambio.VALOR       := NULL;
      Lr_ResponseTipoCambio.TIPO_CAMBIO := NULL;
      Lr_ResponseTipoCambio.FECHA       := NULL;
    ELSE
    -- traduce xml recibido
      FNKG_TIPO_CAMBIO.P_VALIDA_TIPO_CAMBIO( l_response.doc, Lr_ResponseTipoCambio, l_namespace);
      Lr_ResponseTipoCambio.TIPO_CAMBIO := Lr_ResponseTipoCambio.VALOR;
      Lr_ResponseTipoCambio.QUETZAL     := (Lr_ResponseTipoCambio.SUBTOTAL* Lr_ResponseTipoCambio.VALOR);
    END IF;
    -- obtiene valor en quetzales
    Lrf_InfoDocCarac := FNKG_TIPO_CAMBIO.F_GET_INFO_DOCUMENTO_CARAC(l_caracteristica_id, Pn_Id_Documento);
    --
    FETCH Lrf_InfoDocCarac INTO Lr_InfoDocCarac;
    CLOSE Lrf_InfoDocCarac;
    --
    IF Lr_InfoDocCarac.VALOR                IS NOT NULL THEN
      Lr_ResponseTipoCambio.detalle         := 'Existe valor de quetzales para el documento.';
    ELSIF Lr_InfoDocCarac.CARACTERISTICA_ID IS NOT NULL THEN
      --
      UPDATE DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
      SET VALOR            =Lr_ResponseTipoCambio.QUETZAL
      WHERE DOCUMENTO_ID   =Pn_Id_Documento
      AND CARACTERISTICA_ID= l_caracteristica_id;
      COMMIT;
      --
      UPDATE DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
      SET VALOR=Lr_ResponseTipoCambio.TIPO_CAMBIO
      WHERE DOCUMENTO_ID=Pn_Id_Documento
      AND CARACTERISTICA_ID= (SELECT ID_CARACTERISTICA 
                                FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                                WHERE DESCRIPCION_CARACTERISTICA='TIPO CAMBIO QUETZALES'); 
      COMMIT;
      --
      UPDATE DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
      SET VALOR=Lr_ResponseTipoCambio.FECHA
      WHERE DOCUMENTO_ID=Pn_Id_Documento
      AND CARACTERISTICA_ID= (SELECT ID_CARACTERISTICA 
                                FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                                WHERE DESCRIPCION_CARACTERISTICA='FECHA CONVERSION QUETZALES'); 
      COMMIT;
    ELSE
      INSERT
      INTO DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
        (
          ID_DOCUMENTO_CARACTERISTICA,
          DOCUMENTO_ID,
          CARACTERISTICA_ID,
          VALOR,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION,
          ESTADO
        )
        VALUES
        (
          DB_FINANCIERO.SEQ_INFO_DOCUMENTO_CARACT.NEXTVAL,
          Pn_Id_Documento,
          l_caracteristica_id,
          Lr_ResponseTipoCambio.QUETZAL,
          sys_extract_utc(systimestamp),
          Pv_Usuario,
          '127.0.0.1',
          'Activo'
        );
      COMMIT;
      --
      INSERT
      INTO DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
        (
          ID_DOCUMENTO_CARACTERISTICA,
          DOCUMENTO_ID,
          CARACTERISTICA_ID,
          VALOR,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION,
          ESTADO
        )
        VALUES
        (
          DB_FINANCIERO.SEQ_INFO_DOCUMENTO_CARACT.NEXTVAL,
          Pn_Id_Documento,
          (SELECT ID_CARACTERISTICA
          FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA='TIPO CAMBIO QUETZALES'
          ),
          Lr_ResponseTipoCambio.TIPO_CAMBIO,
          sys_extract_utc(systimestamp),
          Pv_Usuario,
          '127.0.0.1',
          'Activo'
        );
      COMMIT;
      -- inserta fecha de conversion de tipo de cambio de la moneda quetzal
      INSERT
      INTO DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA
        (
          ID_DOCUMENTO_CARACTERISTICA,
          DOCUMENTO_ID,
          CARACTERISTICA_ID,
          VALOR,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION,
          ESTADO
        )
        VALUES
        (
          DB_FINANCIERO.SEQ_INFO_DOCUMENTO_CARACT.NEXTVAL,
          Pn_Id_Documento,
          (SELECT ID_CARACTERISTICA
          FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA='FECHA CONVERSION QUETZALES'
          ),
          Lr_ResponseTipoCambio.FECHA,
          sys_extract_utc(systimestamp),
          Pv_Usuario,
          '127.0.0.1',
          'Activo'
        );
      COMMIT;
    END IF;
    --
    IF Lr_ResponseTipoCambio.detalle IS NOT NULL AND Lr_ResponseTipoCambio.detalle!='No existen parámetros configurados' AND Lr_ResponseTipoCambio.detalle!='Existe valor de quetzales para el documento.' THEN
      Pv_MensajeError                := Lr_ResponseTipoCambio.detalle;
      Raise Le_Error;
    ELSE
      Pv_CodigoError  := 'x';
      Pv_MensajeError := 'Conversión Tipo de Cambio correcta.';
    END IF;
  EXCEPTION
  WHEN Le_Error THEN
    ROLLBACK;
    -- se registra en la tabla el mensaje de error
    Lr_ResponseTipoCambio.detalle := 'Error en FNKG_TIPO_CAMBIO, xml: '||Lr_ResponseTipoCambio.detalle;
    
  WHEN OTHERS THEN
    ROLLBACK;
    -- se registra en la tabla el mensaje de error
    
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                            'DB_FINANCIERO.FNKG_TIPO_CAMBIO.P_WS_TIPO_CAMBIO',
                                            Lr_ResponseTipoCambio.detalle || ' - '|| SQLCODE ||' -ERROR- ' || SQLERRM || ' - ' ||
                                            DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'telcos'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.0') );
                                            
    Lr_ResponseTipoCambio.detalle :='Error Interno. Favor notificar a Sistemas.';
  END P_WS_TIPO_CAMBIO;
  --
  FUNCTION F_GET_INFO_DOCUMENTO_CARAC
    (
      Fn_CaracteristicaId IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.CARACTERISTICA_ID%TYPE,
      Fn_DocumentoId      IN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.DOCUMENTO_ID%TYPE
    )
    RETURN SYS_REFCURSOR
  IS
    --
    Lr_InfoDocCarac SYS_REFCURSOR;
    --
  BEGIN
    OPEN Lr_InfoDocCarac FOR SELECT * FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA WHERE CARACTERISTICA_ID=Fn_CaracteristicaId AND DOCUMENTO_ID=Fn_DocumentoId AND ROWNUM = 1;
    --
    RETURN Lr_InfoDocCarac;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
    FNCK_TRANSACTION.INSERT_ERROR('F_GET_INFO_DOCUMENTO_CARAC', 
                                  'DB_FINANCIERO.FNKG_TIPO_CAMBIO.F_GET_INFO_DOCUMENTO_CARAC', 
                                  'ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' ||
                                   DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    --
  END F_GET_INFO_DOCUMENTO_CARAC;
--
  PROCEDURE P_VALIDA_TIPO_CAMBIO
    (
      Pv_Xml          IN XMLTYPE,
      Pr_responseTipo IN OUT FNKG_TIPO_CAMBIO.Lr_ResponseTipoCambioType,
      Pv_Namespace    IN VARCHAR2
    )
  AS
    pv_xml2 VARCHAR2
    (
      32767
    )
    ;
  BEGIN
    -- TAREA: Se necesita implantación para PROCEDURE FNKG_TIPO_CAMBIO.P_VALIDA_TIPO_CAMBIO
    pv_xml2 := Pv_Xml.getClobVal;
    pv_xml2 := REPLACE(pv_xml2, ' '||Pv_Namespace,'');
    FOR cursor_c IN
    (SELECT xt.*
      FROM xmltable( '//Var' passing XMLTYPE(pv_xml2) columns venta VARCHAR2(100) PATH './venta', fecha VARCHAR2(100) PATH './fecha' )xt
    )
    LOOP
      Pr_responseTipo.VALOR := cursor_c.venta;
      Pr_responseTipo.FECHA := cursor_c.fecha;
    END LOOP;
    -- se recuperan los mensajes retornados, advertencia, error, informativo
    FOR c IN
    (SELECT xt.*
      FROM xmltable('//faultstring' passing XMLTYPE(pv_xml2) columns mensaje VARCHAR2(800) PATH './faultstring' )xt
    )
    LOOP
      IF Pr_responseTipo.DETALLE IS NULL THEN
        Pr_responseTipo.DETALLE  := c.mensaje;
      ELSE
        Pr_responseTipo.DETALLE := Pr_responseTipo.DETALLE||', '||c.mensaje;
      END IF;
    END LOOP;
    --
  EXCEPTION
  WHEN OTHERS THEN

    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                           'DB_FINANCIERO.FNKG_TIPO_CAMBIO.P_VALIDA_TIPO_CAMBIO',
                                            Pr_responseTipo.detalle || ' - '|| SQLCODE ||' -ERROR- ' || SQLERRM || ' - ' ||
                                            DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'telcos'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.0') );
                                            
    Pr_responseTipo.detalle :='Error Interno. Favor notificar a Sistemas.';
    
  END P_VALIDA_TIPO_CAMBIO;
END FNKG_TIPO_CAMBIO;
/