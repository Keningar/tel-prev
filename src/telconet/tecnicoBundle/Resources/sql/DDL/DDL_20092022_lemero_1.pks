create or replace PACKAGE DB_GENERAL.GNKG_WEB_SERVICE AS
  
  /**
  * Documentacion para el procedimiento P_WEB_SERVICE
  *
  * Método encargado del consumo de webservice
  *
  * @param Pv_Url             IN  NUMBER   Recibe la url del webservice
  * @param Pcl_Mensaje        IN  VARCHAR2 Recibe el mensaje en formato JSON,XML,ETC
  * @param Pv_Application     IN  VARCHAR2 Recibe el content type por ejemplo (application/json)
  * @param Pv_Charset         IN  VARCHAR2 Recibe el charset en el que se envia el mensaje
  * @param Pv_UrlFileDigital  IN  VARCHAR2 Ruta del certificado digital
  * @param Pv_PassFileDigital IN  VARCHAR2 contraseña para acceder al certificado digital
  * @param Pv_Respuesta       OUT VARCHAR2 Retorna la respuesta del webservice
  * @param Pv_Error           OUT VARCHAR2 Retorna un mensaje de error en caso de existir
  *
  * @author Germán Valenzuela <gvalenzuela@telconet.ec>
  * @version 1.0 25-09-2017
  *
  * @author Germán Valenzuela <gvalenzuela@telconet.ec>
  * @version 1.1 17-10-2017
  */ 
  PROCEDURE P_WEB_SERVICE(
      Pv_Url             IN  VARCHAR2,
      Pcl_Mensaje        IN  CLOB,
      Pv_Application     IN  VARCHAR2,
      Pv_Charset         IN  VARCHAR2,
      Pv_UrlFileDigital  IN  VARCHAR2,
      Pv_PassFileDigital IN  VARCHAR2,
      Pcl_Respuesta      OUT CLOB,
      Pv_Error           OUT VARCHAR2);
  
  /**
  * Documentacion para el procedimiento P_GET
  *
  * Método encargado del consumo de webservice GET
  *
  * @param Pv_Url             IN  VARCHAR2 Recibe la url del webservice
  * @param Pcl_Headers        IN  CLOB     Recibe un json de headers dinámicos
  * @param Pn_Code            OUT NUMBER   Retorna código de error
  * @param Pv_Mensaje         OUT VARCHAR2 Retorna mensaje de transacción
  * @param Pcl_Data           OUT CLOB     Retorna un json respuesta del webservice
  *
  * @author Marlon Plúas <mpluas@telconet.ec>
  * @version 1.0 23-12-2019
  */    
  PROCEDURE P_GET(Pv_Url      IN  VARCHAR2,
                  Pcl_Headers IN  CLOB,
                  Pn_Code     OUT NUMBER,
                  Pv_Mensaje  OUT VARCHAR2,
                  Pcl_Data    OUT CLOB);
  
  /**
  * Documentacion para el procedimiento P_POST
  *
  * Método encargado del consumo de webservice POST
  *
  * @param Pv_Url             IN  VARCHAR2 Recibe la url del webservice
  * @param Pcl_Headers        IN  CLOB     Recibe un json de headers dinámicos
  * @param Pcl_Content        IN  CLOB     Recibe un json request
  * @param Pn_Code            OUT NUMBER   Retorna código de error
  * @param Pv_Mensaje         OUT VARCHAR2 Retorna mensaje de transacción
  * @param Pcl_Data           OUT CLOB     Retorna un json respuesta del webservice
  *
  * @author Marlon Plúas <mpluas@telconet.ec>
  * @version 1.0 23-12-2019
  *
  * @author Felix Caicedo <facaicedo@telconet.ec>
  * @version 1.1 18-06-2020 - Se aumenta el tiempo de respuesta a 120 segundos.
  *
  * @author Felix Caicedo <facaicedo@telconet.ec>
  * @version 1.2 21-07-2020 - Se define la conexión persistente y se aumenta el tiempo de respuesta a 180 segundos.
  *
  * @author Leonardo Mero <lemero@telconet.ec>
  * @version 1.3 20-09-2022 - Se aumenta el valor de caracteres maximos en el header para tolerar tokens extensos
  */                 
  PROCEDURE P_POST(Pv_Url      IN  VARCHAR2,
                   Pcl_Headers IN  CLOB,
                   Pcl_Content IN  CLOB,
                   Pn_Code     OUT NUMBER,
                   Pv_Mensaje  OUT VARCHAR2,
                   Pcl_Data    OUT CLOB);
  
  /**
  * Documentacion para el procedimiento P_PUT
  *
  * Método encargado del consumo de webservice PUT
  *
  * @param Pv_Url             IN  VARCHAR2 Recibe la url del webservice
  * @param Pcl_Headers        IN  CLOB     Recibe un json de headers dinámicos
  * @param Pcl_Content        IN  CLOB     Recibe un json request
  * @param Pn_Code            OUT NUMBER   Retorna código de error
  * @param Pv_Mensaje         OUT VARCHAR2 Retorna mensaje de transacción
  * @param Pcl_Data           OUT CLOB     Retorna un json respuesta del webservice
  *
  * @author Marlon Plúas <mpluas@telconet.ec>
  * @version 1.0 23-12-2019
  */                   
  PROCEDURE P_PUT(Pv_Url      IN  VARCHAR2,
                  Pcl_Headers IN  CLOB,
                  Pcl_Content IN  CLOB,
                  Pn_Code     OUT NUMBER,
                  Pv_Mensaje  OUT VARCHAR2,
                  Pcl_Data    OUT CLOB);
  
  /**
  * Documentacion para el procedimiento P_DELETE
  *
  * Método encargado del consumo de webservice DELETE
  *
  * @param Pv_Url             IN  VARCHAR2 Recibe la url del webservice
  * @param Pcl_Headers        IN  CLOB     Recibe un json de headers dinámicos
  * @param Pcl_Content        IN  CLOB     Recibe un json request
  * @param Pn_Code            OUT NUMBER   Retorna código de error
  * @param Pv_Mensaje         OUT VARCHAR2 Retorna mensaje de transacción
  * @param Pcl_Data           OUT CLOB     Retorna un json respuesta del webservice
  *
  * @author Marlon Plúas <mpluas@telconet.ec>
  * @version 1.0 23-12-2019
  */                 
  PROCEDURE P_DELETE(Pv_Url      IN  VARCHAR2,
                     Pcl_Headers IN  CLOB,
                     Pcl_Content IN  CLOB,
                     Pn_Code     OUT NUMBER,
                     Pv_Mensaje  OUT VARCHAR2,
                     Pcl_Data    OUT CLOB);
  
  /**
  * Documentacion para el procedimiento P_PATCH
  *
  * Método encargado del consumo de webservice PATCH
  *
  * @param Pv_Url             IN  VARCHAR2 Recibe la url del webservice
  * @param Pcl_Headers        IN  CLOB     Recibe un json de headers dinámicos
  * @param Pcl_Content        IN  CLOB     Recibe un json request
  * @param Pn_Code            OUT NUMBER   Retorna código de error
  * @param Pv_Mensaje         OUT VARCHAR2 Retorna mensaje de transacción
  * @param Pcl_Data           OUT CLOB     Retorna un json respuesta del webservice
  *
  * @author Marlon Plúas <mpluas@telconet.ec>
  * @version 1.0 23-12-2019
  */                   
  PROCEDURE P_PATCH(Pv_Url      IN  VARCHAR2,
                    Pcl_Headers IN  CLOB,
                    Pcl_Content IN  CLOB,
                    Pn_Code     OUT NUMBER,
                    Pv_Mensaje  OUT VARCHAR2,
                    Pcl_Data    OUT CLOB);                  

END GNKG_WEB_SERVICE;
/

create or replace PACKAGE BODY DB_GENERAL.GNKG_WEB_SERVICE AS

  PROCEDURE P_WEB_SERVICE(Pv_Url             IN  VARCHAR2,
                          Pcl_Mensaje        IN  CLOB,
                          Pv_Application     IN  VARCHAR2,
                          Pv_Charset         IN  VARCHAR2,
                          Pv_UrlFileDigital  IN  VARCHAR2,
                          Pv_PassFileDigital IN  VARCHAR2,
                          Pcl_Respuesta      OUT CLOB,
                          Pv_Error           OUT VARCHAR2) AS
    
    Lhttp_Request   UTL_HTTP.REQ;
    Lhttp_Response  UTL_HTTP.RESP; 
    Lv_Respuesta    CLOB;
    Lv_Response     CLOB;

  BEGIN

    UTL_HTTP.set_wallet(Pv_UrlFileDigital, Pv_PassFileDigital);

    Lhttp_Request := UTL_HTTP.BEGIN_REQUEST(Pv_Url, 'POST');

    UTL_HTTP.SET_HEADER(Lhttp_Request, 'content-type', Pv_Application);

    UTL_HTTP.SET_HEADER(Lhttp_Request, 'Content-Length', length(Pcl_Mensaje));

    UTL_HTTP.SET_BODY_CHARSET(Lhttp_Request, Pv_Charset);

    UTL_HTTP.WRITE_TEXT(Lhttp_Request, Pcl_Mensaje);
    
    
    Lhttp_Response := UTL_HTTP.GET_RESPONSE(Lhttp_Request);

    BEGIN

      LOOP

        UTL_HTTP.READ_LINE(Lhttp_Response, Lv_Response);

        Lv_Respuesta := Lv_Respuesta || Lv_Response;

      END LOOP;

        UTL_HTTP.END_RESPONSE(Lhttp_Response);   

    EXCEPTION

      WHEN UTL_HTTP.END_OF_BODY THEN

        UTL_HTTP.END_RESPONSE(Lhttp_Response);

    END;

    Pcl_Respuesta := Lv_Respuesta;

  EXCEPTION

    WHEN OTHERS THEN

      Pv_Error := 'Error en el proceso GNKG_WEB_SERVICE.P_WEB_SERVICE ' || SQLERRM;

      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('GNKG_WEB_SERVICE',
                                           'GNKG_WEB_SERVICE.P_WEB_SERVICE', 
                                           SQLERRM, 
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'), 
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1') );

  END P_WEB_SERVICE;
  
  PROCEDURE P_GET(Pv_Url      IN  VARCHAR2,
                  Pcl_Headers IN  CLOB,
                  Pn_Code     OUT NUMBER,
                  Pv_Mensaje  OUT VARCHAR2,
                  Pcl_Data    OUT CLOB)
  AS
   Lv_Req           UTL_HTTP.req;
   Lv_Resp          UTL_HTTP.resp;
   Lv_Error         VARCHAR2(1000);
   Ln_CountHeaders  NUMBER;
   Lv_NameHeader    VARCHAR2(250);
   Lv_ValorHeader   VARCHAR2(1500);
   Lv_ValueHeader   VARCHAR2(1500);
   Lcl_Response     CLOB;
   Lcl_Respuesta    CLOB;
   Le_Data          EXCEPTION;
   Le_Headers       EXCEPTION;
  BEGIN
    -- VALIDACIONES
    IF Pv_Url IS NULL THEN
      Lv_Error := 'El campo Pv_Url es obligatorio';
      RAISE Le_Data;
    END IF;
    IF Pcl_Headers = empty_clob() THEN
      Lv_Error := 'El campo Pcl_Headers es obligatorio';
      RAISE Le_Data;
    END IF;
    -- URL
    Lv_Req := UTL_HTTP.begin_request(Pv_Url, 'GET');
    UTL_HTTP.set_transfer_timeout(80);
    -- HEADERS
    APEX_JSON.PARSE(Pcl_Headers);
    Ln_CountHeaders := APEX_JSON.GET_COUNT(P_PATH => 'headers');
    IF Ln_CountHeaders IS NULL THEN
      Lv_Error := 'No se ha encontrado la cabecera headers';
      RAISE Le_Headers;
    END IF;
    FOR I IN 1 .. Ln_CountHeaders LOOP
      Lv_NameHeader := APEX_JSON.get_members(P_PATH => 'headers')(I);
      Lv_ValorHeader := 'headers.' || Lv_NameHeader;
      Lv_ValueHeader := APEX_JSON.get_varchar2(P_PATH => Lv_ValorHeader);
      UTL_HTTP.set_header(Lv_Req, replace(Lv_NameHeader, '"', ''), Lv_ValueHeader);
    END LOOP;
    Lv_Resp := UTL_HTTP.get_response(Lv_Req);
    -- OBTENER LA RESPUESTA.
    BEGIN
      LOOP
        UTL_HTTP.READ_LINE(Lv_Resp, Lcl_Response);
        Lcl_Respuesta := Lcl_Respuesta || Lcl_Response;
      END LOOP;
      UTL_HTTP.END_RESPONSE(Lv_Resp);
    EXCEPTION
      WHEN UTL_HTTP.END_OF_BODY THEN UTL_HTTP.END_RESPONSE(Lv_Resp);
    END;
    Pn_Code    := 0;
    Pv_Mensaje := 'Ok';
    Pcl_Data   := Lcl_Respuesta;
  EXCEPTION
    WHEN Le_Data THEN 
      Pn_Code    := 1;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_GET',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN Le_Headers THEN 
      Pn_Code    := 2;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_GET',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN OTHERS THEN 
      Pn_Code    := 99;
      Pv_Mensaje := SQLERRM;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_GET',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
  END P_GET;
  
  PROCEDURE P_POST(Pv_Url      IN  VARCHAR2,
                   Pcl_Headers IN  CLOB,
                   Pcl_Content IN  CLOB,
                   Pn_Code     OUT NUMBER,
                   Pv_Mensaje  OUT VARCHAR2,
                   Pcl_Data    OUT CLOB)
  AS
   Lv_Req           UTL_HTTP.req;
   Lv_Resp          UTL_HTTP.resp;
   Lv_Error         VARCHAR2(1000);
   Ln_CountHeaders  NUMBER;
   Lv_NameHeader    VARCHAR2(250);
   Lv_ValorHeader   VARCHAR2(1500);
   Lv_ValueHeader   VARCHAR2(1500);
   Lcl_Response     CLOB;
   Lcl_Respuesta    CLOB;
   Le_Data          EXCEPTION;
   Le_Headers       EXCEPTION;
   Ln_LongitudRequest   NUMBER;
   Ln_LongitudIdeal     NUMBER          := 32767;
   Ln_Offset            NUMBER          := 1;
   Ln_Buffer            VARCHAR2(2000);
   Ln_Amount            NUMBER          := 2000;
  BEGIN
    -- VALIDACIONES
    IF Pv_Url IS NULL THEN
      Lv_Error := 'El campo Pv_Url es obligatorio';
      RAISE Le_Data;
    END IF;
    IF Pcl_Headers = empty_clob() THEN
      Lv_Error := 'El campo Pcl_Headers es obligatorio';
      RAISE Le_Data;
    END IF;
    -- CONEXION PERSISTENTE
    UTL_HTTP.set_persistent_conn_support(true);
    -- TIME OUT
    UTL_HTTP.set_transfer_timeout(180);
    -- URL
    Lv_Req := UTL_HTTP.begin_request(Pv_Url, 'POST');
    -- HEADERS
    APEX_JSON.PARSE(Pcl_Headers);
    Ln_CountHeaders := APEX_JSON.GET_COUNT(P_PATH => 'headers');
    IF Ln_CountHeaders IS NULL THEN
      Lv_Error := 'No se ha encontrado la cabecera headers';
      RAISE Le_Headers;
    END IF;
    FOR I IN 1 .. Ln_CountHeaders LOOP
      Lv_NameHeader := APEX_JSON.get_members(P_PATH => 'headers')(I);
      Lv_ValorHeader := 'headers.' || Lv_NameHeader;
      Lv_ValueHeader := APEX_JSON.get_varchar2(P_PATH => Lv_ValorHeader);
      UTL_HTTP.set_header(Lv_Req, replace(Lv_NameHeader, '"', ''), Lv_ValueHeader);
    END LOOP;
    IF Pcl_Content IS NOT NULL THEN
        Ln_LongitudRequest := DBMS_LOB.getlength(Pcl_Content);
        IF Ln_LongitudRequest <= Ln_LongitudIdeal THEN
            UTL_HTTP.set_header(Lv_Req, 'Content-Length', LENGTH(Pcl_Content));
            UTL_HTTP.write_text(Lv_Req, Pcl_Content);
        ELSE
            UTL_HTTP.SET_HEADER(Lv_Req, 'Transfer-Encoding', 'chunked');
            WHILE (Ln_Offset < Ln_LongitudRequest)
            LOOP
                DBMS_LOB.READ(Pcl_Content, Ln_Amount, Ln_Offset, Ln_Buffer);
                UTL_HTTP.WRITE_TEXT(Lv_Req, Ln_Buffer);
                Ln_Offset := Ln_Offset + Ln_Amount;
            END LOOP;
        END IF;
    END IF;
    Lv_Resp := UTL_HTTP.get_response(Lv_Req);
    -- OBTENER LA RESPUESTA.
    BEGIN
      LOOP
        UTL_HTTP.READ_LINE(Lv_Resp, Lcl_Response);
        Lcl_Respuesta := Lcl_Respuesta || Lcl_Response;
      END LOOP;
      UTL_HTTP.END_RESPONSE(Lv_Resp);
    EXCEPTION
      WHEN UTL_HTTP.END_OF_BODY THEN UTL_HTTP.END_RESPONSE(Lv_Resp);
    END;
    Pn_Code    := 0;
    Pv_Mensaje := 'Ok';
    Pcl_Data   := Lcl_Respuesta;
  EXCEPTION
    WHEN Le_Data THEN 
      Pn_Code    := 1;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_POST',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN Le_Headers THEN 
      Pn_Code    := 2;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_POST',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN OTHERS THEN 
      Pn_Code    := 99;
      Pv_Mensaje := SQLERRM;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_POST',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
  END P_POST;
  
  PROCEDURE P_PUT(Pv_Url      IN  VARCHAR2,
                  Pcl_Headers IN  CLOB,
                  Pcl_Content IN  CLOB,
                  Pn_Code     OUT NUMBER,
                  Pv_Mensaje  OUT VARCHAR2,
                  Pcl_Data    OUT CLOB)
  AS
   Lv_Req           UTL_HTTP.req;
   Lv_Resp          UTL_HTTP.resp;
   Lv_Error         VARCHAR2(1000);
   Ln_CountHeaders  NUMBER;
   Lv_NameHeader    VARCHAR2(250);
   Lv_ValorHeader   VARCHAR2(500);
   Lv_ValueHeader   VARCHAR2(1000);
   Lcl_Response     CLOB;
   Lcl_Respuesta    CLOB;
   Le_Data          EXCEPTION;
   Le_Headers       EXCEPTION;
  BEGIN
    -- VALIDACIONES
    IF Pv_Url IS NULL THEN
      Lv_Error := 'El campo Pv_Url es obligatorio';
      RAISE Le_Data;
    END IF;
    IF Pcl_Headers = empty_clob() THEN
      Lv_Error := 'El campo Pcl_Headers es obligatorio';
      RAISE Le_Data;
    END IF;
    -- URL
    Lv_Req := UTL_HTTP.begin_request(Pv_Url, 'PUT');
    UTL_HTTP.set_transfer_timeout(80);
    -- HEADERS
    APEX_JSON.PARSE(Pcl_Headers);
    Ln_CountHeaders := APEX_JSON.GET_COUNT(P_PATH => 'headers');
    IF Ln_CountHeaders IS NULL THEN
      Lv_Error := 'No se ha encontrado la cabecera headers';
      RAISE Le_Headers;
    END IF;
    FOR I IN 1 .. Ln_CountHeaders LOOP
      Lv_NameHeader := APEX_JSON.get_members(P_PATH => 'headers')(I);
      Lv_ValorHeader := 'headers.' || Lv_NameHeader;
      Lv_ValueHeader := APEX_JSON.get_varchar2(P_PATH => Lv_ValorHeader);
      UTL_HTTP.set_header(Lv_Req, replace(Lv_NameHeader, '"', ''), Lv_ValueHeader);
    END LOOP;
    IF Pcl_Content IS NOT NULL THEN
      UTL_HTTP.set_header(Lv_Req, 'Content-Length', LENGTH(Pcl_Content));
      UTL_HTTP.write_text(Lv_Req, Pcl_Content);
    END IF;
    Lv_Resp := UTL_HTTP.get_response(Lv_Req);
    -- OBTENER LA RESPUESTA.
    BEGIN
      LOOP
        UTL_HTTP.READ_LINE(Lv_Resp, Lcl_Response);
        Lcl_Respuesta := Lcl_Respuesta || Lcl_Response;
      END LOOP;
      UTL_HTTP.END_RESPONSE(Lv_Resp);
    EXCEPTION
      WHEN UTL_HTTP.END_OF_BODY THEN UTL_HTTP.END_RESPONSE(Lv_Resp);
    END;
    Pn_Code    := 0;
    Pv_Mensaje := 'Ok';
    Pcl_Data   := Lcl_Respuesta;
  EXCEPTION
    WHEN Le_Data THEN 
      Pn_Code    := 1;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PUT',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN Le_Headers THEN 
      Pn_Code    := 2;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PUT',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN OTHERS THEN 
      Pn_Code    := 99;
      Pv_Mensaje := SQLERRM;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PUT',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
  END P_PUT;
  
  PROCEDURE P_DELETE(Pv_Url      IN  VARCHAR2,
                     Pcl_Headers IN  CLOB,
                     Pcl_Content IN  CLOB,
                     Pn_Code     OUT NUMBER,
                     Pv_Mensaje  OUT VARCHAR2,
                     Pcl_Data    OUT CLOB)
  AS
   Lv_Req           UTL_HTTP.req;
   Lv_Resp          UTL_HTTP.resp;
   Lv_Error         VARCHAR2(1000);
   Ln_CountHeaders  NUMBER;
   Lv_NameHeader    VARCHAR2(250);
   Lv_ValorHeader   VARCHAR2(500);
   Lv_ValueHeader   VARCHAR2(1000);
   Lcl_Response     CLOB;
   Lcl_Respuesta    CLOB;
   Le_Data          EXCEPTION;
   Le_Headers       EXCEPTION;
  BEGIN
    -- VALIDACIONES
    IF Pv_Url IS NULL THEN
      Lv_Error := 'El campo Pv_Url es obligatorio';
      RAISE Le_Data;
    END IF;
    IF Pcl_Headers = empty_clob() THEN
      Lv_Error := 'El campo Pcl_Headers es obligatorio';
      RAISE Le_Data;
    END IF;
    -- URL
    Lv_Req := UTL_HTTP.begin_request(Pv_Url, 'DELETE');
    UTL_HTTP.set_transfer_timeout(80);
    -- HEADERS
    APEX_JSON.PARSE(Pcl_Headers);
    Ln_CountHeaders := APEX_JSON.GET_COUNT(P_PATH => 'headers');
    IF Ln_CountHeaders IS NULL THEN
      Lv_Error := 'No se ha encontrado la cabecera headers';
      RAISE Le_Headers;
    END IF;
    FOR I IN 1 .. Ln_CountHeaders LOOP
      Lv_NameHeader := APEX_JSON.get_members(P_PATH => 'headers')(I);
      Lv_ValorHeader := 'headers.' || Lv_NameHeader;
      Lv_ValueHeader := APEX_JSON.get_varchar2(P_PATH => Lv_ValorHeader);
      UTL_HTTP.set_header(Lv_Req, replace(Lv_NameHeader, '"', ''), Lv_ValueHeader);
    END LOOP;
    IF Pcl_Content IS NOT NULL THEN
      UTL_HTTP.set_header(Lv_Req, 'Content-Length', LENGTH(Pcl_Content));
      UTL_HTTP.write_text(Lv_Req, Pcl_Content);
    END IF;
    Lv_Resp := UTL_HTTP.get_response(Lv_Req);
    -- OBTENER LA RESPUESTA.
    BEGIN
      LOOP
        UTL_HTTP.READ_LINE(Lv_Resp, Lcl_Response);
        Lcl_Respuesta := Lcl_Respuesta || Lcl_Response;
      END LOOP;
      UTL_HTTP.END_RESPONSE(Lv_Resp);
    EXCEPTION
      WHEN UTL_HTTP.END_OF_BODY THEN UTL_HTTP.END_RESPONSE(Lv_Resp);
    END;
    Pn_Code    := 0;
    Pv_Mensaje := 'Ok';
    Pcl_Data   := Lcl_Respuesta;
  EXCEPTION
    WHEN Le_Data THEN 
      Pn_Code    := 1;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_DELETE',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN Le_Headers THEN 
      Pn_Code    := 2;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_DELETE',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN OTHERS THEN 
      Pn_Code    := 99;
      Pv_Mensaje := SQLERRM;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_DELETE',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
  END P_DELETE;

  PROCEDURE P_PATCH(Pv_Url      IN  VARCHAR2,
                    Pcl_Headers IN  CLOB,
                    Pcl_Content IN  CLOB,
                    Pn_Code     OUT NUMBER,
                    Pv_Mensaje  OUT VARCHAR2,
                    Pcl_Data    OUT CLOB)
  AS
   Lv_Req           UTL_HTTP.req;
   Lv_Resp          UTL_HTTP.resp;
   Lv_Error         VARCHAR2(1000);
   Ln_CountHeaders  NUMBER;
   Lv_NameHeader    VARCHAR2(250);
   Lv_ValorHeader   VARCHAR2(500);
   Lv_ValueHeader   VARCHAR2(1000);
   Lcl_Response     CLOB;
   Lcl_Respuesta    CLOB;
   Le_Data          EXCEPTION;
   Le_Headers       EXCEPTION;
  BEGIN
    -- VALIDACIONES
    IF Pv_Url IS NULL THEN
      Lv_Error := 'El campo Pv_Url es obligatorio';
      RAISE Le_Data;
    END IF;
    IF Pcl_Headers = empty_clob() THEN
      Lv_Error := 'El campo Pcl_Headers es obligatorio';
      RAISE Le_Data;
    END IF;
    -- URL
    Lv_Req := UTL_HTTP.begin_request(Pv_Url, 'PATCH');
    UTL_HTTP.set_transfer_timeout(80);
    -- HEADERS
    APEX_JSON.PARSE(Pcl_Headers);
    Ln_CountHeaders := APEX_JSON.GET_COUNT(P_PATH => 'headers');
    IF Ln_CountHeaders IS NULL THEN
      Lv_Error := 'No se ha encontrado la cabecera headers';
      RAISE Le_Headers;
    END IF;
    FOR I IN 1 .. Ln_CountHeaders LOOP
      Lv_NameHeader := APEX_JSON.get_members(P_PATH => 'headers')(I);
      Lv_ValorHeader := 'headers.' || Lv_NameHeader;
      Lv_ValueHeader := APEX_JSON.get_varchar2(P_PATH => Lv_ValorHeader);
      UTL_HTTP.set_header(Lv_Req, replace(Lv_NameHeader, '"', ''), Lv_ValueHeader);
    END LOOP;
    IF Pcl_Content IS NOT NULL THEN
      UTL_HTTP.set_header(Lv_Req, 'Content-Length', LENGTH(Pcl_Content));
      UTL_HTTP.write_text(Lv_Req, Pcl_Content);
    END IF;
    Lv_Resp := UTL_HTTP.get_response(Lv_Req);
    -- OBTENER LA RESPUESTA.
    BEGIN
      LOOP
        UTL_HTTP.READ_LINE(Lv_Resp, Lcl_Response);
        Lcl_Respuesta := Lcl_Respuesta || Lcl_Response;
      END LOOP;
      UTL_HTTP.END_RESPONSE(Lv_Resp);
    EXCEPTION
      WHEN UTL_HTTP.END_OF_BODY THEN UTL_HTTP.END_RESPONSE(Lv_Resp);
    END;
    Pn_Code    := 0;
    Pv_Mensaje := 'Ok';
    Pcl_Data   := Lcl_Respuesta;
  EXCEPTION
    WHEN Le_Data THEN 
      Pn_Code    := 1;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PATCH',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN Le_Headers THEN 
      Pn_Code    := 2;
      Pv_Mensaje := Lv_Error;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PATCH',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
    WHEN OTHERS THEN 
      Pn_Code    := 99;
      Pv_Mensaje := SQLERRM;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('CONSUMO WEB SERVICE', 
                                           'GNKG_WEB_SERVICE.P_PATCH',
                                           Pv_Mensaje,
                                           NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_GENERAL'),
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1'));
  END P_PATCH;

END GNKG_WEB_SERVICE;