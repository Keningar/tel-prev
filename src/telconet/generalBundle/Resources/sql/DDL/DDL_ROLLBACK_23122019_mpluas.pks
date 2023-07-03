SET DEFINE OFF 
CREATE OR REPLACE PACKAGE DB_GENERAL.GNKG_WEB_SERVICE AS
  
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
      
END GNKG_WEB_SERVICE;
/
CREATE OR REPLACE PACKAGE BODY DB_GENERAL.GNKG_WEB_SERVICE AS

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

END GNKG_WEB_SERVICE;
/
