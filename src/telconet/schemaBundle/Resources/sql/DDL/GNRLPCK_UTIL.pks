CREATE OR REPLACE PACKAGE DB_GENERAL.GNRLPCK_UTIL
AS

  /**
   * Función que lanza una excepción según el mensaje proporcionado.
   * @author Luis Cabrera <lcabrera@telconet.ec>
   * @version 1.0
   * @since 19-10-2018
   */
  PROCEDURE P_RAISE_EXCEPTION(Pn_Code NUMBER DEFAULT 20000, Pv_Alert VARCHAR2, Pv_Message VARCHAR2);

  /**
   * Función que devuelve si una empresa aplica a un proceso o no.
   * Se basa en el parámetro EMPRESA_APLICA_PROCESO.
   * @author Luis Cabrera <lcabrera@telconet.ec>
   * @version 1.0
   * @since 18-10-2018
   */
  FUNCTION F_EMPRESA_APLICA_PROCESO(Pv_Proceso    DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
                                    Pv_EmpresaCod DB_GENERAL.ADMI_PARAMETRO_DET.EMPRESA_COD%TYPE)
    RETURN VARCHAR2;

  /**
  * INSERT_ERROR, realiza un insert en la tabla DB_FINANCIERO.INFO_ERROR
  *
  * @author Alexander Samaniego <awsamaniego@telconet.ec>
  * @version 26-07-2016
  * @since 1.0
  * @author Edson Franco <efranco@telconet.ec>
  * @version 1.1 14-08-2017 - Se agrega la sentencia 'PRAGMA AUTONOMOUS_TRANSACTION' para hacer los procesos de manera independiente al insertar el
  *                           error
  *
  * @param Pv_Aplicacion   IN INFO_ERROR.APLICACION%TYPE,
  * @param Pv_Proceso      IN INFO_ERROR.PROCESO%TYPE,
  * @param Pv_DetalleError IN INFO_ERROR.DETALLE_ERROR%TYPE,
  * @param Pv_UsrCreacion  IN INFO_ERROR.USR_CREACION%TYPE,
  * @param Pt_FeCreacion   IN INFO_ERROR.FE_CREACION%TYPE,
  * @param Pv_IpCreacion   IN INFO_ERROR.IP_CREACION%TYPE
  *
  */
  PROCEDURE INSERT_ERROR(
      Pv_Aplicacion   IN INFO_ERROR.APLICACION%TYPE,
      Pv_Proceso      IN INFO_ERROR.PROCESO%TYPE,
      Pv_DetalleError IN INFO_ERROR.DETALLE_ERROR%TYPE,
      Pv_UsrCreacion  IN INFO_ERROR.USR_CREACION%TYPE,
      Pd_FeCreacion   IN INFO_ERROR.FE_CREACION%TYPE,
      Pv_IpCreacion   IN INFO_ERROR.IP_CREACION%TYPE);

  /**
   * Realiza envío de correos muy grandes que no pueden ser enviados con UTL_MAIL
   *
   * @author Lizbeth Cruz <mlcruz@telconet.ec>
   * @version 1.0 17-10-2019
   *
   * @param Pv_From                      VARCHAR2,
   * @param Pv_To                        VARCHAR2,
   * @param Pv_DelimiterMails            VARCHAR2 DEFAULT ',',
   * @param Pv_Subject                   VARCHAR2,
   * @param Pv_ContentTypeMessageVarchar VARCHAR2,
   * @param Pcl_BodyMessage              CLOB,
   * @param Pv_ContentTypeMessageClob    VARCHAR2,
   * @param Pv_MimeTypeBody              VARCHAR2
   *
   */
  PROCEDURE P_SEND_MAIL_SMTP(
    Pv_From                         VARCHAR2,
    Pv_To                           VARCHAR2,
    Pv_DelimiterMails               VARCHAR2 DEFAULT ',',
    Pv_Subject                      VARCHAR2,
    Pv_BodyMessage                  VARCHAR2,
    Pv_ContentTypeMessageVarchar    VARCHAR2 DEFAULT 'text/plain; charset=iso-8859-1',
    Pcl_BodyMessage                 CLOB,
    Pv_ContentTypeMessageClob       VARCHAR2 DEFAULT 'text/html; charset=iso-8859-1');

  --
  /**
  * Realiza envio de correo con archivo adjunto ZIP
  *
  * @author Juan Martinez<jfmartinez@telconet.ec>
  * @version 1.0 18-08-2016
  *
  * @author Alejandro Domínguez Vargas <adominguez@telconet.ec> 
  * @version 1.1 29-09-2016 - Se agrega el parámetro p_mime_type_body por defecto "text/html" para soporte de contenido HTML del cuerpo del email.
  *                         - Se agrega el parámetro p_mime_type_attach por defecto "application/octet-stream" para el archivo adjunto.
  *
  * @param p_from_name IN VARCHAR2,
  * @param p_to_name   IN VARCHAR2,
  * @param p_subject IN VARCHAR2,
  * @param p_message  IN VARCHAR2,
  * @param p_oracle_directory   IN VARCHAR2,
  * @param p_binary_file   IN VARCHAR2
  *
  */
  PROCEDURE send_email_attach(p_from_name        VARCHAR2,
                              p_to_name          VARCHAR2,
                              p_subject          VARCHAR2,
                              p_message          VARCHAR2,
                              p_oracle_directory VARCHAR2,
                              p_binary_file      VARCHAR2, 
                              p_mime_type_body   VARCHAR2 DEFAULT 'text/html',
                              p_mime_type_attach VARCHAR2 DEFAULT 'application/octet-stream');


  /**
  * Documentación para la función F_GET_VARCHAR_REPLACED
  * Función que retorna una cadena eliminando los caracteres encontrados segun la expresion regular especificada,
  * tambien reemplaza los caracteres especificados usando la funcion translate.
  *
  * Fv_Cadena                 IN VARCHAR2, Texto que se evaluará y del cual se eliminarán los caracteres inválidos
  * Fv_ExpresionRegular       IN VARCHAR2, Expresión regular, con los caracteres que se desean eliminar.
  * Fv_CaracterReemplazo      IN VARCHAR2, Caracter por el cual se reemplazaran los caracteres encontrados por la expresion regular
  * Fv_CaracteresTranslateIn  IN VARCHAR2, Caracteres de entrada para la funcion translate que se reemplazarán
  * Fv_CaracteresTranslateOut IN VARCHAR2  Caracteres de salida, son los que reemplazarán a los caracteres de entrada usando la función
                                           translate
  * Retorna:
  * En tipo varchar2 texto sin caracteres inválidos
  *
  * @author Hector Ortega <haortega@telconet.ec>
  * @version 1.00 20-12-2016
  */
  FUNCTION F_GET_VARCHAR_REPLACED(
    Fv_Cadena IN VARCHAR2,
    Fv_ExpresionRegular IN VARCHAR2,
    Fv_CaracterReemplazo IN VARCHAR2,
    Fv_CaracteresTranslateIn IN VARCHAR2,
    Fv_CaracteresTranslateOut IN VARCHAR2
    )
  RETURN VARCHAR2;
  --
  /**
  * Documentación para la función F_REGEX_BY_PARAM
  * Función que retorna una cadena eliminando los caracterese especiales encontrados segun expresion regular configurada.
  *
  * Fn_IdEmpresa                  IN NUMBER,   Id de la empresa
  * Fv_Cadena                     IN VARCHAR2, Texto que se evaluará y del cual se eliminarán los caracteres especiales.
  * Fv_NombreParametroCabecera    IN VARCHAR2, Nombre del parametro donde se tienen configurado la expresion regular.
  *
  * Retorna:
  * En tipo varchar2 texto sin caracteres especiales.
  *
  * @author Ricardo Coello Quezada <rcoello@telconet.ec>
  * @version 1.00 30-01-2017
  */
  FUNCTION F_REGEX_BY_PARAM(
    Fn_IdEmpresa               IN   DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE,  
    Fv_Cadena                  IN   VARCHAR2,
    Fv_NombreParametroCabecera IN   VARCHAR2,
    Fv_Valor1                  IN   DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE
    )
  RETURN VARCHAR2;

  /**
  * Documentación para la función P_ENVIO_CORREO_POR_PARAMETROS
  * Función que realiza envío de notificación con los valores enviados como parámetro.
  *
  *   @param  Pv_NombreParameteroCab IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE  Nombre  del parámetro
  *   @param  Pv_EstadoParametroCab  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE            Estado  del parámetro cab
  *   @param  Pv_EstadoParametroDet  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE            Estado  del parámetro det
  *   @param  Pv_Valor1              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE            Valor 1 del parametro det
  *   @param  Pv_Valor2              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE            Valor 2 del parametro det
  *   @param  Pv_Valor3              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR3%TYPE            Valor 3 del parametro det
  *   @param  Pv_Valor4              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR4%TYPE            Valor 4 del parametro det
  *   @param  Pv_CodPlantilla        IN DB_COMUNICACION.ADMI_PLANTILLA.CODIGO%TYPE           Código de plantilla
  *   @param  Pv_Observacion         IN VARCHAR2,                                            Observación
  *   @param  Pv_Mensaje             IN CLOB                                                 Mensaje de error.
  *   @param  Pv_Charset             IN VARCHAR2 DEFAULT 'text/html; charset=UTF-8'
  *   @return Pv_MsnError            OUT VARCHAR2
  *
  * @author Edgar Holguin  <eholguin@telconet.ec>
  * @version 1.0 30-03-2017
  */
  PROCEDURE P_ENVIO_CORREO_POR_PARAMETROS(
      Pv_NombreParameteroCab IN  DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
      Pv_EstadoParametroCab  IN  DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
      Pv_EstadoParametroDet  IN  DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
      Pv_Valor1              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
      Pv_Valor2              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE,
      Pv_Valor3              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR3%TYPE,
      Pv_Valor4              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR4%TYPE,
      Pv_CodPlantilla        IN  DB_COMUNICACION.ADMI_PLANTILLA.CODIGO%TYPE,
      Pv_Observacion         IN  VARCHAR2,
      Pv_Mensaje             IN  CLOB,
      Pv_Charset             IN  VARCHAR2 DEFAULT 'text/html; charset=UTF-8',
      Pv_MsnError            OUT VARCHAR2);


  /**
  * Documentacion para la funcion F_GET_ADMI_PARAMETRO_CAB
  * la funcion F_GET_ADMI_PARAMETRO_CAB obtiene un registro de F_GET_ADMI_PARAMETRO_CAB, recibiendo el id del parámetro.
  *
  * @param  Fn_IdParametro           IN DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE   Recibe el id del parámetro
  * @author Edgar Holguín <eholguin@telconet.ec>
  * @version 1.0 09-01-2015
  */

  FUNCTION F_GET_ADMI_PARAMETRO_CAB(
      Fn_IdParametro           IN DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE)
    RETURN DB_GENERAL.ADMI_PARAMETRO_CAB%ROWTYPE;


  /**
  * Documentacion para la funcion F_GET_INFO_TRANSACCION_ID
  * la funcion F_GET_INFO_TRANSACCION_ID obtiene un registro de DB_SEGURIDAD.F_GET_INFO_TRANSACCION_ID.
  *
  * @param  Fv_NombreAccion    IN DB_SEGURIDAD.SIST_ACCION.NOMBRE_ACCION%TYPE   Recibe el nombre de la acción
  * @param  Fv_NombreModulo    IN DB_SEGURIDAD.SIST_MODULO.NOMBRE_MODULO%TYPE   Recibe el nombre del módulo
  * @return DB_SEGURIDAD.SEGU_RELACION_SISTEMA.ID_RELACION_SISTEMA%TYPE
  * @author Edgar Holguín <eholguin@telconet.ec>
  * @version 1.0 08-08-2017
  */

  FUNCTION F_GET_INFO_TRANSACCION_ID(
      Fv_NombreAccion    IN DB_SEGURIDAD.SIST_ACCION.NOMBRE_ACCION%TYPE,
      Fv_NombreModulo    IN DB_SEGURIDAD.SIST_MODULO.NOMBRE_MODULO%TYPE)
    RETURN DB_SEGURIDAD.SEGU_RELACION_SISTEMA.ID_RELACION_SISTEMA%TYPE;
  --

  /**
   * Documentación para F_GET_PARAMS_DETS
   * Función que obtiene los detalles de acuerdo al nombre del parámetro enviado
   * 
   * @author Lizbeth Cruz <mlcruz@telconet.ec>
   * @version 1.0 24/09/2017
   *
   * @param Fv_NombreParametro IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE Recibe el nombre del parámetro
   * @return SYS_REFCURSOR
   */
  FUNCTION F_GET_PARAMS_DETS(
    Fv_NombreParametro IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE)
  RETURN SYS_REFCURSOR;

  /**
   * Documentación para F_GET_PARAM_VALOR2
   * Función que obtiene el valor2 con el nombre del parámetro y el valor1 enviado
   * 
   * @author Lizbeth Cruz <mlcruz@telconet.ec>
   * @version 1.0 27/09/2017
   *
   * @param Fv_NombreParametro IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE Recibe el nombre del parámetro
   * @param Fv_Valor1 IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE Recibe el nombre del parámetro
   * @return VARCHAR2
   */
  FUNCTION F_GET_PARAM_VALOR2(
    Fv_NombreParametro  IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
    Fv_Valor1           IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE)
  RETURN VARCHAR2;

/**
* Documentacion para la funcion F_GET_ADMI_PARAMETROS_DET
* Funcion que obtiene los parametros del detalle de la tabla ADMI_PARAMETRO_DET según VALOR1 y VALOR2 enviado como parámetro
*
* @param  Fv_NombreParameteroCab IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE Recibe el codigo de la cabecera del parametro
* @param  Fv_EstadoParametroCab  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE           Recibe el estado de la cabecera del paramtero
* @param  Fv_EstadoParametroDet  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE           Recibe el estado del detalle del paramtero
* @param  Fv_Valor1              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE           Recibe un valor segun la configuracion de nuestro paramtero
* @param  Fv_Valor2              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE           Recibe un valor segun la configuracion de nuestro paramtero
* @return SYS_REFCURSOR          Devuelve el detalle de los parametros
* @author Edgar Holguín <eholguin@telconet.ec>
* @version 1.0 15-02-2018
*/
FUNCTION F_GET_ADMI_PARAMETROS_DET(
    Fv_NombreParameteroCab IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
    Fv_EstadoParametroCab  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
    Fv_EstadoParametroDet  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
    Fv_Valor1              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
    Fv_Valor2              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE)
  RETURN SYS_REFCURSOR;


/**
  * P_INSERT_LOG, realiza un insert en la tabla DB_GENERAL.INFO_LOG
  *
  * @author Ronny Morán Chancay <rmoranc@telconet.ec>
  * @version 01-04-2021
  * @since 1.0
  *
  * @param Pv_EmpresaCod   IN INFO_LOG.EMPRESA_COD  				Código de la empresa
  * @param Pv_TipoLog      IN INFO_LOG.TIPO_LOG                                 Tipo de LOG
  * @param Pv_Aplicacion   IN INFO_LOG.APLICACION				Nombre de la aplicación
  * @param Pd_Clase        IN INFO_LOG.CLASE					Nombre de la Clase
  * @param Pv_Metodo       IN INFO_LOG.METODO					Nombre del método
  * @param Pd_Accion       IN INFO_LOG.ACCION					Acción realizada 
  * @param Pd_Estado       IN INFO_LOG.ESTADO					Estado 
  * @param Pd_Descripcion  IN INFO_LOG.DESCRIPCION				Descripción del error 
  * @param Pd_ParametroIn  IN INFO_LOG.PARAMETRO_ENTRADA			Parámetro de entrada de la función
  * @param Pd_UsrCreacion  IN INFO_LOG.USR_CREACION				Usuario de creación del insert 
  *
  */
  PROCEDURE P_INSERT_LOG(
                            Pv_EmpresaCod                       IN DB_GENERAL.INFO_LOG.EMPRESA_COD%TYPE,
                            Pv_TipoLog                          IN DB_GENERAL.INFO_LOG.TIPO_LOG%TYPE,
                            Pv_Aplicacion                       IN DB_GENERAL.INFO_LOG.APLICACION%TYPE,
                            Pd_Clase                            IN DB_GENERAL.INFO_LOG.CLASE%TYPE,
                            Pv_Metodo                           IN DB_GENERAL.INFO_LOG.METODO%TYPE,
                            Pd_Accion                           IN DB_GENERAL.INFO_LOG.ACCION%TYPE,
                            Pd_Estado                           IN DB_GENERAL.INFO_LOG.ESTADO%TYPE,
                            Pd_Descripcion                      IN DB_GENERAL.INFO_LOG.DESCRIPCION%TYPE,
                            Pd_ParametroIn                      IN DB_GENERAL.INFO_LOG.PARAMETRO_ENTRADA%TYPE,
                            Pd_UsrCreacion                      IN DB_GENERAL.INFO_LOG.USR_CREACION%TYPE);

  /**
   * Función que realiza el envío del request para guardar un archivo en el NFS. La función ha sido tomada a partir de la función 
   * ya existente F_HTTPPOSTMULTIPART del paquete DB_FINANCIERO.FNKG_CARTERA_CLIENTES
   *
   * @author Lizbeth Cruz <mlcruz@telconet.ec>
   * @version 1.0 16-06-2021
   *
   * @param Fv_UrlMicroServicioNfs  VARCHAR2 Url del microservicio para guardar archivos NFS,
   * @param Fv_PathArchivo          VARCHAR2 Path del archivo que se desea guardar,
   * @param Fv_NombreArchivo        VARCHAR2 Nombre del archivo que se desea guardar,
   * @param Fv_PathAdicional        VARCHAR2 Path adicional donde se desea guardar el archivo,
   * @param Fv_CodigoApp            VARCHAR2 CODIGO_APP de la DB_GENERAL.ADMI_GESTION_DIRECTORIOS,
   * @param Fv_CodigoPath           VARCHAR2 cODIGO_PATH de la DB_GENERAL.ADMI_GESTION_DIRECTORIOS
   *
   */
  FUNCTION F_GUARDAR_ARCHIVO_NFS(   Fv_UrlMicroServicioNfs  IN VARCHAR2,
                                    Fv_PathArchivo          IN VARCHAR2,
                                    Fv_NombreArchivo        IN VARCHAR2,
                                    Fv_PathAdicional        IN VARCHAR2,
                                    Fv_CodigoApp            IN VARCHAR2,
                                    Fv_CodigoPath           IN VARCHAR2)
  RETURN VARCHAR2;

  /**
   * Función que busca una cadena dentro de un texto. La función ha sido tomada a partir de la función ya existente F_CONTAINS del paquete 
   * DB_FINANCIERO.FNKG_CARTERA_CLIENTES
   *
   * @author Lizbeth Cruz <mlcruz@telconet.ec>
   * @version 1.0 16-06-2021
   *
   * @param Fv_Texto        VARCHAR2 Texto en el cual se buscará,
   * @param Fv_TextoABuscar VARCHAR2 Texto que se buscará
   *
   */
  FUNCTION F_CONTIENE_TEXTO(Fv_Texto        IN VARCHAR2, 
                            Fv_TextoABuscar IN VARCHAR2)
  RETURN VARCHAR2;
  --
  --
 /**
  * Documentación para F_GET_PARAMS_DETS_EMPRESA
  * Función que obtiene los detalles de acuerdo al nombre del parámetro y código empresa enviado
  * 
  * @author Hector Lozano <hlozano@telconet.ec>
  * @version 1.0 02/03/2023
  *
  * @param Fv_NombreParametro IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE Recibe el nombre del parámetro
  * @param Fv_CodEmpresa      IN DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE Recibe el código empresa
  * @return SYS_REFCURSOR
  */
  FUNCTION F_GET_PARAMS_DETS_EMPRESA(Fv_NombreParametro IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                                     Fv_CodEmpresa      IN DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE)
  RETURN SYS_REFCURSOR; 

END GNRLPCK_UTIL;
/


CREATE OR REPLACE PACKAGE BODY DB_GENERAL.GNRLPCK_UTIL
AS

  PROCEDURE P_RAISE_EXCEPTION(Pn_Code NUMBER DEFAULT 20000, Pv_Alert VARCHAR2, Pv_Message VARCHAR2)
  IS
    Ln_Code  NUMBER;
    Lv_Alert VARCHAR2(300);
  BEGIN
    Ln_Code  := Pn_Code;
    Lv_Alert := Pv_Alert;
    IF Pv_Message IS NOT NULL THEN
        IF NVL(Ln_Code,-20000) >= -20000 THEN
            Lv_Alert := Ln_Code || ': ' || Pv_Alert;
            Ln_Code := -20001;
        END IF;
        RAISE_APPLICATION_ERROR(Ln_Code, Lv_Alert || ': ' || Pv_Message);
    END IF;
  END P_RAISE_EXCEPTION;

  FUNCTION F_EMPRESA_APLICA_PROCESO(Pv_Proceso    DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
                                    Pv_EmpresaCod DB_GENERAL.ADMI_PARAMETRO_DET.EMPRESA_COD%TYPE)
    RETURN VARCHAR2
  IS
    Lv_AplicaProceso DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE := 'N';
    CURSOR C_AplicaEmpresaProceso (Cv_NombreParametro DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                                   Cv_Proceso         DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
                                   Cv_EmpresaCod      DB_GENERAL.ADMI_PARAMETRO_DET.EMPRESA_COD%TYPE,
                                   Cv_EstadoActivo    VARCHAR2) IS
        SELECT VALOR2
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
               DB_GENERAL.ADMI_PARAMETRO_DET DET
         WHERE CAB.NOMBRE_PARAMETRO = Cv_NombreParametro
           AND CAB.ESTADO = Cv_EstadoActivo
           AND CAB.ID_PARAMETRO = DET.PARAMETRO_ID
           AND DET.ESTADO = Cv_EstadoActivo
           AND DET.VALOR1 = Cv_Proceso
           AND DET.EMPRESA_COD = Cv_EmpresaCod;
  BEGIN
    OPEN  C_AplicaEmpresaProceso (Cv_NombreParametro => 'EMPRESA_APLICA_PROCESO',
                                  Cv_Proceso         => Pv_Proceso,
                                  Cv_EmpresaCod      => Pv_EmpresaCod,
                                  Cv_EstadoActivo    => 'Activo');
    FETCH C_AplicaEmpresaProceso INTO Lv_AplicaProceso;
    CLOSE C_AplicaEmpresaProceso;

    RETURN NVL(Lv_AplicaProceso, 'N');
  EXCEPTION
    WHEN OTHERS THEN
        RETURN 'N';
  END F_EMPRESA_APLICA_PROCESO;

  --
  PROCEDURE INSERT_ERROR(
      Pv_Aplicacion   IN INFO_ERROR.APLICACION%TYPE,
      Pv_Proceso      IN INFO_ERROR.PROCESO%TYPE,
      Pv_DetalleError IN INFO_ERROR.DETALLE_ERROR%TYPE,
      Pv_UsrCreacion  IN INFO_ERROR.USR_CREACION%TYPE,
      Pd_FeCreacion   IN INFO_ERROR.FE_CREACION%TYPE,
      Pv_IpCreacion   IN INFO_ERROR.IP_CREACION%TYPE)
  AS
    --
    PRAGMA AUTONOMOUS_TRANSACTION;
    --
  BEGIN
    --
    INSERT
    INTO DB_GENERAL.INFO_ERROR
      (
        ID_ERROR,
        APLICACION,
        PROCESO,
        DETALLE_ERROR,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_GENERAL.SEQ_INFO_ERROR.NEXTVAL,
        Pv_Aplicacion,
        Pv_Proceso,
        Pv_DetalleError,
        Pv_UsrCreacion,
        Pd_FeCreacion,
        Pv_IpCreacion
      );
    --
    COMMIT;
    --
  END INSERT_ERROR;
--

PROCEDURE P_SEND_MAIL_SMTP(
  Pv_From                         VARCHAR2,
  Pv_To                           VARCHAR2,
  Pv_DelimiterMails               VARCHAR2 DEFAULT ',',
  Pv_Subject                      VARCHAR2,
  Pv_BodyMessage                  VARCHAR2,
  Pv_ContentTypeMessageVarchar    VARCHAR2 DEFAULT 'text/plain; charset=iso-8859-1',
  Pcl_BodyMessage                 CLOB,
  Pv_ContentTypeMessageClob       VARCHAR2 DEFAULT 'text/html; charset=iso-8859-1')
AS
  Lv_SmtpServer                 VARCHAR2(100) := 'sissmtp-int.telconet.net';
  Ln_SmtpPort                   NUMBER DEFAULT 25;
  Lv_MailConnection             UTL_SMTP.connection;
  Lv_MimeBoundary               VARCHAR2(50) := '----=*#abc1234321cba#*=';
  Lv_Step                       PLS_INTEGER  := 30000;
BEGIN
  Lv_MailConnection := UTL_SMTP.open_connection(Lv_SmtpServer, Ln_SmtpPort);
  UTL_SMTP.helo(Lv_MailConnection, Lv_SmtpServer);
  UTL_SMTP.mail(Lv_MailConnection, Pv_From);

  IF TRIM(Pv_To) IS NOT NULL THEN
    FOR Lr_CurrentRow IN (
      WITH TEST AS
        (SELECT Pv_To FROM DUAL)
        SELECT REGEXP_SUBSTR(Pv_To, '[^' || Pv_DelimiterMails || ']+', 1, ROWNUM) SPLIT
        FROM TEST
        CONNECT BY LEVEL <= LENGTH (REGEXP_REPLACE(Pv_To, '[^' || Pv_DelimiterMails || ']+'))  + 1)
    LOOP
      IF TRIM(Lr_CurrentRow.SPLIT) IS NOT NULL THEN
        UTL_SMTP.rcpt(Lv_MailConnection, TRIM(Lr_CurrentRow.SPLIT));
      END IF;
    END LOOP;
  END IF;

  UTL_SMTP.open_data(Lv_MailConnection);
  UTL_SMTP.write_data(Lv_MailConnection, 'From: ' || Pv_From || UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'To: ' || Pv_To|| UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'Date: ' || TO_CHAR(SYSDATE, 'DD-MON-YYYY HH24:MI:SS') || UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'Subject: ' || Pv_Subject|| UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'Reply-To: ' || Pv_From || UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'MIME-Version: 1.0' || UTL_TCP.crlf);
  UTL_SMTP.write_data(Lv_MailConnection, 'Content-Type: multipart/mixed; boundary="' || Lv_MimeBoundary || '"' || UTL_TCP.crlf || UTL_TCP.crlf);

  IF Pv_BodyMessage IS NOT NULL THEN
    UTL_SMTP.write_data(Lv_MailConnection, '--' || Lv_MimeBoundary || UTL_TCP.crlf);
    UTL_SMTP.write_data(Lv_MailConnection, 'Content-Type: ' || Pv_ContentTypeMessageVarchar || UTL_TCP.crlf || UTL_TCP.crlf);
    UTL_SMTP.write_data(Lv_MailConnection, Pv_BodyMessage);
    UTL_SMTP.write_data(Lv_MailConnection, UTL_TCP.crlf || UTL_TCP.crlf);
  ELSIF Pcl_BodyMessage IS NOT NULL THEN
    UTL_SMTP.write_data(Lv_MailConnection, '--' || Lv_MimeBoundary || UTL_TCP.crlf);
    UTL_SMTP.write_data(Lv_MailConnection, 'Content-Type: ' || Pv_ContentTypeMessageClob || UTL_TCP.crlf || UTL_TCP.crlf);
    FOR i IN 0 .. TRUNC((DBMS_LOB.getlength(Pcl_BodyMessage) - 1 )/Lv_Step) LOOP
      UTL_SMTP.WRITE_RAW_DATA(Lv_MailConnection, UTL_RAW.CAST_TO_RAW(UTL_TCP.CRLF || DBMS_LOB.substr(Pcl_BodyMessage, Lv_Step, i * Lv_Step + 1) 
                                                                     || UTL_TCP.CRLF));

    END LOOP;
    UTL_SMTP.write_data(Lv_MailConnection, UTL_TCP.crlf || UTL_TCP.crlf);
  END IF;

  UTL_SMTP.write_data(Lv_MailConnection, '--' || Lv_MimeBoundary || '--' || UTL_TCP.crlf);
  UTL_SMTP.close_data(Lv_MailConnection);
  UTL_SMTP.quit(Lv_MailConnection);

EXCEPTION
  WHEN UTL_smtp.transient_error OR UTL_smtp.permanent_error THEN
    UTL_smtp.quit(Lv_MailConnection);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'GNRLPCK_UTIL.P_SEND_MAIL_SMTP', 
                                          'Error al enviar correo por smtp UTL_smtp.transient_error ' || ' - ' || SQLCODE || ' -ERROR- ' 
                                          || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' '|| DBMS_UTILITY.FORMAT_ERROR_STACK,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  WHEN OTHERS THEN
      UTL_smtp.quit(Lv_MailConnection);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'GNRLPCK_UTIL.P_SEND_MAIL_SMTP', 
                                          'Error al enviar correo por smtp ' || ' - ' || SQLCODE || ' -ERROR- ' 
                                          || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' '|| DBMS_UTILITY.FORMAT_ERROR_STACK,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
END P_SEND_MAIL_SMTP;

PROCEDURE send_email_attach(p_from_name        VARCHAR2,
                            p_to_name          VARCHAR2,
                            p_subject          VARCHAR2,
                            p_message          VARCHAR2,
                            p_oracle_directory VARCHAR2,
                            p_binary_file      VARCHAR2, 
                            p_mime_type_body   VARCHAR2 DEFAULT 'text/html',
                            p_mime_type_attach VARCHAR2 DEFAULT 'application/octet-stream')
IS
-- encoded in Base64
-- this procedure uses the following nested functions:
--     binary_attachment - calls:
--     begin_attachment - calls:
--     write_boundary
--     write_mime_header
-- 
--     end attachment - calls;
--     write_boundary

  -- change the following line to refer to your mail server
  v_smtp_server VARCHAR2(100) := 'sissmtp-int.telconet.net';
  v_smtp_server_port NUMBER := 25;
  v_directory_name VARCHAR2(100);
  v_file_name VARCHAR2(100);
  v_mesg VARCHAR2(32767);
  v_conn UTL_SMTP.CONNECTION;
    --------------
  lw_lenght_line NUMBER;
  lw_pos_start   NUMBER;
  lw_pos_char    NUMBER;
  v_recipient    VARCHAR2(5000);
    --------------
-- 

  PROCEDURE write_mime_header(p_conn in out nocopy utl_smtp.connection,
    p_name in varchar2,
    p_value in varchar2)
  IS
  BEGIN
    UTL_SMTP.WRITE_RAW_DATA(
      p_conn,
      UTL_RAW.CAST_TO_RAW( p_name || ': ' || p_value || UTL_TCP.CRLF)
    );
  END write_mime_header;

-- 

  PROCEDURE write_boundary(p_conn IN OUT NOCOPY UTL_SMTP.CONNECTION,
    p_last IN BOOLEAN DEFAULT false)
  IS
  BEGIN
    IF (p_last) THEN
      UTL_SMTP.WRITE_DATA(p_conn, '--DMW.Boundary.605592468--'||UTL_TCP.CRLF);
    ELSE
      UTL_SMTP.WRITE_DATA(p_conn, '--DMW.Boundary.605592468'||UTL_TCP.CRLF);
    END IF;
  END write_boundary;

-- 

  PROCEDURE end_attachment(p_conn IN OUT NOCOPY UTL_SMTP.CONNECTION,
                           p_last IN BOOLEAN DEFAULT TRUE)
  IS
  BEGIN
    UTL_SMTP.WRITE_DATA(p_conn, UTL_TCP.CRLF);
    IF (p_last) THEN
      write_boundary(p_conn, p_last);
    END IF;
  END end_attachment;

-- 

  PROCEDURE begin_attachment(p_conn IN OUT NOCOPY UTL_SMTP.CONNECTION,
                             p_mime_type IN VARCHAR2 DEFAULT 'text/plain',
                             p_inline IN BOOLEAN DEFAULT false,
                             p_filename IN VARCHAR2 DEFAULT null,
                             p_transfer_enc in VARCHAR2 DEFAULT null)
  IS
  BEGIN
    write_boundary(p_conn);
    IF (p_transfer_enc IS NOT NULL) THEN
      write_mime_header(p_conn, 'Content-Transfer-Encoding',p_transfer_enc);
    END IF;
    write_mime_header(p_conn, 'Content-Type', p_mime_type);
    IF (p_filename IS NOT NULL) THEN
      IF (p_inline) THEN
        write_mime_header(
          p_conn,
          'Content-Disposition', 'inline; filename="' || p_filename || '"'
        );
      ELSE
        write_mime_header(
          p_conn,
          'Content-Disposition', 'attachment; filename="' || p_filename || '"'
        );
      END IF;
    END IF;
    UTL_SMTP.WRITE_DATA(p_conn, UTL_TCP.CRLF);
  END begin_attachment;

-- 

  PROCEDURE binary_attachment(p_conn IN OUT UTL_SMTP.CONNECTION,
                              p_file_name IN VARCHAR2,
                              p_mime_type in VARCHAR2)
  IS
    c_max_line_width CONSTANT PLS_INTEGER DEFAULT 54;
    v_amt BINARY_INTEGER := 672 * 3; /* ensures proper format; 2016 */
    v_bfile BFILE;
    v_file_length PLS_INTEGER;
    v_buf RAW(2100);
    v_modulo PLS_INTEGER;
    v_pieces PLS_INTEGER;
    v_file_pos pls_integer := 1;

  BEGIN
    begin_attachment(
      p_conn => p_conn,
      p_mime_type => p_mime_type,
      p_inline => TRUE,
      p_filename => p_file_name,
      p_transfer_enc => 'base64');
    BEGIN
      v_bfile := BFILENAME(p_oracle_directory, p_file_name);
      -- Get the size of the file to be attached
      v_file_length := DBMS_LOB.GETLENGTH(v_bfile);
      -- Calculate the number of pieces the file will be split up into
      v_pieces := TRUNC(v_file_length / v_amt);
      -- Calculate the remainder after dividing the file into v_amt chunks
      v_modulo := MOD(v_file_length, v_amt);
      IF (v_modulo <> 0) THEN
      -- Since the file does not devide equally
      -- we need to go round the loop an extra time to write the last
      -- few bytes - so add one to the loop counter.
        v_pieces := v_pieces + 1;
      END IF;
      DBMS_LOB.FILEOPEN(v_bfile, DBMS_LOB.FILE_READONLY);
      FOR i IN 1 .. v_pieces LOOP
      -- we can read at the beginning of the loop as we have already calculated
      -- how many iterations we will take and so do not need to check
      -- end of file inside the loop.
        v_buf := NULL;
        DBMS_LOB.READ(v_bfile, v_amt, v_file_pos, v_buf);
        v_file_pos := I * v_amt + 1;
        UTL_SMTP.WRITE_RAW_DATA(p_conn, UTL_ENCODE.BASE64_ENCODE(v_buf));
      END LOOP;
    END;
    DBMS_LOB.FILECLOSE(v_bfile);
    end_attachment(p_conn => p_conn);
  EXCEPTION
    WHEN NO_DATA_FOUND THEN
      end_attachment(p_conn => p_conn);
      DBMS_LOB.FILECLOSE(v_bfile);
  END binary_attachment;

-- 
-- Main Routine
-- 
BEGIN
-- 
-- Connect and set up header information:
-- 
  v_conn:= UTL_SMTP.OPEN_CONNECTION( v_smtp_server, v_smtp_server_port );
  UTL_SMTP.HELO( v_conn, v_smtp_server );
  UTL_SMTP.MAIL( v_conn, p_from_name );
  --UTL_SMTP.RCPT( v_conn, p_to_name );
  ----------
    lw_lenght_line := length(p_to_name);
    lw_pos_start   := 1;
    <<loopinterno>>
    LOOP
      lw_pos_char  := INSTR(p_to_name, ',', lw_pos_start);
      v_recipient := substr(p_to_name,
                             lw_pos_start,
                             lw_pos_char - lw_pos_start);
      utl_smtp.rcpt(v_conn, v_recipient);
      EXIT loopinterno WHEN lw_pos_char = lw_lenght_line;
      lw_pos_start := lw_pos_char + 1;
    END LOOP;
  -------------------
  UTL_SMTP.OPEN_DATA ( v_conn );
  UTL_SMTP.WRITE_DATA(v_conn, 'Subject: '||p_subject||UTL_TCP.CRLF);
-- 
  v_mesg:= 'Content-Transfer-Encoding: 7bit' || UTL_TCP.CRLF ||
    'Content-Type: multipart/mixed;boundary="DMW.Boundary.605592468"' || UTL_TCP.CRLF ||
    'Mime-Version: 1.0' || UTL_TCP.CRLF ||
    '--DMW.Boundary.605592468' || UTL_TCP.CRLF ||
    'Content-Transfer-Encoding: binary'||UTL_TCP.CRLF||
    'Content-Type: ' || p_mime_type_body ||UTL_TCP.CRLF ||
    UTL_TCP.CRLF || p_message || UTL_TCP.CRLF ;
-- 
  UTL_SMTP.write_data(v_conn, 'To: ' || p_to_name || UTL_TCP.crlf);



  UTL_SMTP.WRITE_RAW_DATA ( v_conn, UTL_RAW.CAST_TO_RAW(v_mesg) );
  --
  -- Add the Attachment
  --
  binary_attachment(
    p_conn => v_conn,
    p_file_name => p_binary_file,
    -- Modify the mime type at the beginning of this line depending
    -- on the type of file being loaded.
    p_mime_type => p_mime_type_attach || '; name="' || p_binary_file|| '"'
  );
  --
  -- Send the email
  --
  UTL_SMTP.CLOSE_DATA( v_conn );
  UTL_SMTP.QUIT( v_conn );

END send_email_attach;


  FUNCTION F_GET_VARCHAR_REPLACED(
    Fv_Cadena IN VARCHAR2,
    Fv_ExpresionRegular IN VARCHAR2,
    Fv_CaracterReemplazo IN VARCHAR2,
    Fv_CaracteresTranslateIn IN VARCHAR2,
    Fv_CaracteresTranslateOut IN VARCHAR2
    )
  RETURN VARCHAR2
  IS
  BEGIN

  RETURN TRIM(
           REPLACE(
             REPLACE(
               REPLACE(
                 TRANSLATE(
                   REGEXP_REPLACE(Fv_Cadena, Fv_ExpresionRegular, Fv_CaracterReemplazo),
                   Fv_CaracteresTranslateIn, Fv_CaracteresTranslateOut),
               Chr(9), ' '),
             Chr(10), ' '),
           Chr(13), ' '));

  EXCEPTION
    WHEN OTHERS THEN
      RETURN Fv_Cadena;

  END F_GET_VARCHAR_REPLACED;
  --
  FUNCTION F_REGEX_BY_PARAM(
    Fn_IdEmpresa               IN   DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE,  
    Fv_Cadena                  IN   VARCHAR2,
    Fv_NombreParametroCabecera IN   VARCHAR2,
    Fv_Valor1                  IN   DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE
    )
  RETURN VARCHAR2
  IS
  --
  CURSOR C_GetExpresionRegular(Cn_IdEmpresa        INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE,
                               Cv_EstadoActivo     DB_GENERAL.ADMI_PARAMETRO_CAB.ESTADO%TYPE,
                               Cv_Valor1           DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
                               Cv_NombreParametro  DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE)
  IS
  SELECT 
      APD.VALOR2
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC,
         DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE APC.ID_PARAMETRO      = APD.PARAMETRO_ID
    AND   APC.NOMBRE_PARAMETRO  = Cv_NombreParametro
    AND   APC.ESTADO            = Cv_EstadoActivo
    AND   APD.VALOR1            = Cv_Valor1
    AND   APD.ESTADO            = Cv_EstadoActivo
    AND   APD.VALOR4            = Cn_IdEmpresa;
  --
  Lv_ExpresionRegular     VARCHAR2(100);
  Lv_Cadena               VARCHAR2(3000);
  Lv_Estado               DB_GENERAL.ADMI_PARAMETRO_CAB.ESTADO%TYPE := 'Activo';
  --
  BEGIN
  --
  IF C_GetExpresionRegular%ISOPEN THEN
  CLOSE C_GetExpresionRegular;
  END IF;
  --
  OPEN C_GetExpresionRegular(Fn_IdEmpresa, 
                             Lv_Estado, 
                             Fv_Valor1, 
                             Fv_NombreParametroCabecera
                            );
  --
  FETCH C_GetExpresionRegular INTO Lv_ExpresionRegular;
  --
  CLOSE C_GetExpresionRegular;

  --
  RETURN TRIM(regexp_replace( Fv_Cadena, Lv_ExpresionRegular ,''));
  --
  EXCEPTION
    WHEN OTHERS THEN
      RETURN Fv_Cadena;

  END F_REGEX_BY_PARAM;

  PROCEDURE P_ENVIO_CORREO_POR_PARAMETROS(
      Pv_NombreParameteroCab IN  DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
      Pv_EstadoParametroCab  IN  DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
      Pv_EstadoParametroDet  IN  DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
      Pv_Valor1              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
      Pv_Valor2              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE,
      Pv_Valor3              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR3%TYPE,
      Pv_Valor4              IN  DB_GENERAL.ADMI_PARAMETRO_DET.VALOR4%TYPE,
      Pv_CodPlantilla        IN  DB_COMUNICACION.ADMI_PLANTILLA.CODIGO%TYPE,
      Pv_Observacion         IN  VARCHAR2,
      Pv_Mensaje             IN  CLOB,
      Pv_Charset             IN  VARCHAR2 DEFAULT 'text/html; charset=UTF-8',
      Pv_MsnError            OUT VARCHAR2)
  IS
    --
    Lc_MessageMail CLOB;
    Lrf_GetAdmiParamtrosDet SYS_REFCURSOR;
    Lr_GetAdmiParamtrosDet DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE;
    Lc_GetAliasPlantilla DB_FINANCIERO.FNKG_TYPES.Lr_AliasPlantilla;
    Lv_MsnError VARCHAR2(3000);
    --
  BEGIN
    --
    Lrf_GetAdmiParamtrosDet := NULL;
    --Verifica que pueda enviar correo
    Lrf_GetAdmiParamtrosDet := DB_FINANCIERO.FNCK_CONSULTS.F_GET_ADMI_PARAMETROS_DET(Pv_NombreParameteroCab, 
                                                                                     Pv_EstadoParametroCab, 
                                                                                     Pv_EstadoParametroDet, 
                                                                                     Pv_Valor1, 
                                                                                     Pv_Valor2, 
                                                                                     Pv_Valor3, 
                                                                                     Pv_Valor4);
    --
    FETCH Lrf_GetAdmiParamtrosDet INTO Lr_GetAdmiParamtrosDet;
    --
    CLOSE Lrf_GetAdmiParamtrosDet;
    --VERIFICA SI ESTA ACTIVADO PARA ENVIAR CORREO
    IF Lr_GetAdmiParamtrosDet.ID_PARAMETRO_DET IS NOT NULL THEN
      --
      Lc_GetAliasPlantilla := DB_FINANCIERO.FNCK_CONSULTS.F_GET_ALIAS_PLANTILLA(Pv_CodPlantilla);
      --
      IF Lr_GetAdmiParamtrosDet.ID_PARAMETRO_DET IS NOT NULL AND 
         Lc_GetAliasPlantilla.PLANTILLA          IS NOT NULL AND
         Lc_GetAliasPlantilla.ALIAS_CORREOS      IS NOT NULL AND 
         Lr_GetAdmiParamtrosDet.VALOR2           IS NOT NULL AND 
         Lr_GetAdmiParamtrosDet.VALOR3           IS NOT NULL THEN
        --
        Lc_MessageMail := REPLACE(TRIM(Lc_GetAliasPlantilla.PLANTILLA), '{{ strObservacion }}', TRIM(Pv_Observacion));
        Lc_MessageMail := DB_FINANCIERO.FNCK_CONSULTS.F_CLOB_REPLACE(Lc_MessageMail, '{{ strTrTable | raw }}', Pv_Mensaje);
        --Envia correo
        DB_FINANCIERO.FNCK_CONSULTS.P_SEND_MAIL(Lr_GetAdmiParamtrosDet.VALOR2, 
                                                Lc_GetAliasPlantilla.ALIAS_CORREOS, 
                                                Lr_GetAdmiParamtrosDet.VALOR3, 
                                                SUBSTR(Lc_MessageMail, 1, 32767), 
                                                Pv_Charset, 
                                                Lv_MsnError);
        --
        IF Lv_MsnError IS NOT NULL THEN
          --
          Pv_MsnError := 'No se pudo notificar por correo - ' || Lv_MsnError;
          --
        END IF;
        --
      END IF; --Lr_GetAdmiParamtrosDet.ID_PARAMETRO_DET ...
      --
    END IF; --Lrf_GetAdmiParamtrosDet
    --
  EXCEPTION
  WHEN OTHERS THEN

    Pv_MsnError := 'ERROR EN DB_GENERAL.GNRLPCK_UTIL.P_ENVIO_CORREO_POR_PARAMETROS';
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'GNRLPCK_UTIL.P_ENVIO_CORREO_POR_PARAMETROS', 
                                          'Error al enviar correo por parametros ' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  END P_ENVIO_CORREO_POR_PARAMETROS;


  FUNCTION F_GET_ADMI_PARAMETRO_CAB(
      Fn_IdParametro           IN DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE)
    RETURN DB_GENERAL.ADMI_PARAMETRO_CAB%ROWTYPE
  IS
  --
    CURSOR C_GetAdmiParametroCab(Cn_IdParametro DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE)
    IS
      SELECT
        APC.*
      FROM
        DB_GENERAL.ADMI_PARAMETRO_CAB APC
      WHERE
        APC.ID_PARAMETRO = Cn_IdParametro;
    --
    Lc_GetAdmiParametroCab C_GetAdmiParametroCab%ROWTYPE;
    --
  BEGIN
    --
    IF C_GetAdmiParametroCab%ISOPEN THEN
      --
      CLOSE C_GetAdmiParametroCab;
    --
    END IF;
    --
    OPEN C_GetAdmiParametroCab(Fn_IdParametro);
    --
    FETCH
      C_GetAdmiParametroCab
    INTO
      Lc_GetAdmiParametroCab;
    --
    CLOSE C_GetAdmiParametroCab;
    --
    RETURN Lc_GetAdmiParametroCab;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                            'GNRLPCK_UTIL.F_GET_ADMI_PARAMETRO_CAB', 
                                            'Error al consultar parametro ' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                            SYSDATE, 
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
    --
  END F_GET_ADMI_PARAMETRO_CAB;
  --

  FUNCTION F_GET_INFO_TRANSACCION_ID(
      Fv_NombreAccion    IN DB_SEGURIDAD.SIST_ACCION.NOMBRE_ACCION%TYPE,
      Fv_NombreModulo    IN DB_SEGURIDAD.SIST_MODULO.NOMBRE_MODULO%TYPE)
    RETURN DB_SEGURIDAD.SEGU_RELACION_SISTEMA.ID_RELACION_SISTEMA%TYPE
  IS
  --
    CURSOR C_GetSeguRelacionSistId(Cv_NombreAccion DB_SEGURIDAD.SIST_ACCION.NOMBRE_ACCION%TYPE,
                                   Cv_NombreModulo DB_SEGURIDAD.SIST_MODULO.NOMBRE_MODULO%TYPE)
    IS
      SELECT
        SRS.ID_RELACION_SISTEMA
      FROM
        DB_SEGURIDAD.SEGU_RELACION_SISTEMA SRS
      WHERE
        SRS.MODULO_ID = (SELECT SM.ID_MODULO FROM DB_SEGURIDAD.SIST_MODULO SM WHERE SM.ESTADO != 'Eliminado' AND SM.NOMBRE_MODULO=Cv_NombreModulo)
      AND 
        SRS.ACCION_ID = (SELECT SA.ID_ACCION FROM DB_SEGURIDAD.SIST_ACCION SA WHERE SA.ESTADO != 'Eliminado' AND SA.NOMBRE_ACCION=Cv_NombreAccion);
    --
    Ln_IdRelacionSistema NUMBER := 0;
    --
  BEGIN
    --
    IF C_GetSeguRelacionSistId%ISOPEN THEN
      --
      CLOSE C_GetSeguRelacionSistId;
    --
    END IF;
    --
    OPEN C_GetSeguRelacionSistId(Fv_NombreAccion,Fv_NombreModulo);
    --
    FETCH
      C_GetSeguRelacionSistId
    INTO
      Ln_IdRelacionSistema;
    --
    CLOSE C_GetSeguRelacionSistId;
    --
    RETURN Ln_IdRelacionSistema;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                            'GNRLPCK_UTIL.F_GET_INFO_TRANSACCION_ID', 
                                            'Error al consultar parametro ' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                            SYSDATE, 
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
    --
  END F_GET_INFO_TRANSACCION_ID;

  FUNCTION F_GET_PARAMS_DETS(
    Fv_NombreParametro  IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE)
  RETURN SYS_REFCURSOR
  IS
  --
    Lrf_ParamsDets SYS_REFCURSOR;
  --
  BEGIN
    OPEN Lrf_ParamsDets FOR SELECT APD.*
                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD
                            ON APD.PARAMETRO_ID = APC.ID_PARAMETRO
                            WHERE APC.NOMBRE_PARAMETRO = Fv_NombreParametro 
                            AND APC.ESTADO = 'Activo'
                            AND APD.ESTADO = 'Activo';
    --
    RETURN Lrf_ParamsDets;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'CUKG_CONSULTS.F_GET_PARAMS_DETS', 
                                          'Error al consultar parametro ' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  END F_GET_PARAMS_DETS;



  FUNCTION F_GET_PARAM_VALOR2(
    Fv_NombreParametro  IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
    Fv_Valor1           IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE)
  RETURN VARCHAR2
  IS
  --
    Lv_Valor2 VARCHAR2(300);
  --
  BEGIN
    SELECT APD.VALOR2 INTO Lv_Valor2
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD
    ON APD.PARAMETRO_ID = APC.ID_PARAMETRO
    WHERE APC.NOMBRE_PARAMETRO = Fv_NombreParametro 
    AND APD.VALOR1 = Fv_Valor1
    AND APC.ESTADO = 'Activo'
    AND APD.ESTADO = 'Activo'
    AND ROWNUM = 1;
    --
    RETURN Lv_Valor2;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
    RETURN NULL;

  END F_GET_PARAM_VALOR2;


    FUNCTION F_GET_ADMI_PARAMETROS_DET(
        Fv_NombreParameteroCab IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
        Fv_EstadoParametroCab  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
        Fv_EstadoParametroDet  IN DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
        Fv_Valor1              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE,
        Fv_Valor2              IN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE)
      RETURN SYS_REFCURSOR
    IS
      --
      Lr_AdmiParamtrosDet SYS_REFCURSOR;
      --
    BEGIN
      OPEN Lr_AdmiParamtrosDet FOR SELECT APD.* 
                                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC, 
                                         DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                    WHERE APC.ID_PARAMETRO = APD.PARAMETRO_ID 
                                    AND APC.ESTADO = NVL(Fv_EstadoParametroCab, APC.ESTADO ) 
                                    AND APD.ESTADO = NVL(Fv_EstadoParametroDet, APD.ESTADO ) 
                                    AND APC.NOMBRE_PARAMETRO  = NVL(Fv_NombreParameteroCab, APC.NOMBRE_PARAMETRO ) 
                                    AND APD.VALOR1 = NVL(Fv_Valor1, APD.VALOR1 ) 
                                    AND APD.VALOR2 = NVL(Fv_Valor2, APD.VALOR2 );
      --
      RETURN Lr_AdmiParamtrosDet;
      --
    EXCEPTION
    WHEN OTHERS THEN
      --
      --    
        DBMS_OUTPUT.PUT_LINE('F_GET_VALOR_CARACTERISTICA '||
                             ' ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || 
                             ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
        RETURN NULL;
      --
    END F_GET_ADMI_PARAMETROS_DET;



/**
  * P_INSERT_LOG, realiza un insert en la tabla DB_GENERAL.INFO_LOG
  *
  * @author Ronny Morán Chancay <rmoranc@telconet.ec>
  * @version 01-04-2021
  * @since 1.0
  *
  * @param Pv_EmpresaCod   IN INFO_LOG.EMPRESA_COD  				Código de la empresa
  * @param Pv_TipoLog      IN INFO_LOG.TIPO_LOG                                 Tipo de LOG
  * @param Pv_Aplicacion   IN INFO_LOG.APLICACION				Nombre de la aplicación
  * @param Pd_Clase        IN INFO_LOG.CLASE					Nombre de la Clase
  * @param Pv_Metodo       IN INFO_LOG.METODO					Nombre del método
  * @param Pd_Accion       IN INFO_LOG.ACCION					Acción realizada 
  * @param Pd_Estado       IN INFO_LOG.ESTADO					Estado 
  * @param Pd_Descripcion  IN INFO_LOG.DESCRIPCION				Descripción del error 
  * @param Pd_ParametroIn  IN INFO_LOG.PARAMETRO_ENTRADA			Parámetro de entrada de la función
  * @param Pd_UsrCreacion  IN INFO_LOG.USR_CREACION				Usuario de creación del insert 
  *
  */
  PROCEDURE P_INSERT_LOG(
                        Pv_EmpresaCod                       IN DB_GENERAL.INFO_LOG.EMPRESA_COD%TYPE,
                        Pv_TipoLog                          IN DB_GENERAL.INFO_LOG.TIPO_LOG%TYPE,
                        Pv_Aplicacion                       IN DB_GENERAL.INFO_LOG.APLICACION%TYPE,
                        Pd_Clase                            IN DB_GENERAL.INFO_LOG.CLASE%TYPE,
                        Pv_Metodo                           IN DB_GENERAL.INFO_LOG.METODO%TYPE,
                        Pd_Accion                           IN DB_GENERAL.INFO_LOG.ACCION%TYPE,
                        Pd_Estado                           IN DB_GENERAL.INFO_LOG.ESTADO%TYPE,
                        Pd_Descripcion                      IN DB_GENERAL.INFO_LOG.DESCRIPCION%TYPE,
                        Pd_ParametroIn                      IN DB_GENERAL.INFO_LOG.PARAMETRO_ENTRADA%TYPE,
                        Pd_UsrCreacion                      IN DB_GENERAL.INFO_LOG.USR_CREACION%TYPE)
  AS
    --
    PRAGMA AUTONOMOUS_TRANSACTION;
    --
  BEGIN
    
    INSERT
    INTO DB_GENERAL.INFO_LOG
      (
        ID_LOG,
        EMPRESA_COD,
        TIPO_LOG,
        ORIGEN_LOG,
	APLICACION,
	CLASE,
	METODO,
	ACCION,
	MENSAJE,
	ESTADO,
	DESCRIPCION,
	PARAMETRO_ENTRADA,
        USR_CREACION,
        FE_CREACION
      )
      VALUES
      (
        DB_GENERAL.SEQ_INFO_LOG.NEXTVAL,
        Pv_EmpresaCod,
        Pv_TipoLog,
        'TELCOS',
        Pv_Aplicacion,
        Pd_Clase,
        Pv_Metodo,
	Pd_Accion,
	NULL,
	Pd_Estado,
	Pd_Descripcion,
	Pd_ParametroIn,
	Pd_UsrCreacion,
        SYSDATE
      );
    --
    COMMIT;
    --
  END P_INSERT_LOG;
--

  FUNCTION F_GUARDAR_ARCHIVO_NFS(   Fv_UrlMicroServicioNfs  IN VARCHAR2,
                                    Fv_PathArchivo          IN VARCHAR2,
                                    Fv_NombreArchivo        IN VARCHAR2,
                                    Fv_PathAdicional        IN VARCHAR2,
                                    Fv_CodigoApp            IN VARCHAR2,
                                    Fv_CodigoPath           IN VARCHAR2)
  RETURN VARCHAR2
  AS
    LANGUAGE JAVA
    NAME 'HttpPostMultipart.guardarArchivoNfs(java.lang.String,java.lang.String,java.lang.String,
                                              java.lang.String,java.lang.String,java.lang.String) return java.lang.String';


  FUNCTION F_CONTIENE_TEXTO(Fv_Texto        IN VARCHAR2, 
                            Fv_TextoABuscar IN VARCHAR2)
  RETURN VARCHAR2
  AS
    LANGUAGE JAVA
    NAME 'HttpPostMultipart.contieneTexto(java.lang.String,java.lang.String) return java.lang.String';

  --
  --
  FUNCTION F_GET_PARAMS_DETS_EMPRESA(Fv_NombreParametro  IN DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                                     Fv_CodEmpresa       IN DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE)
  RETURN SYS_REFCURSOR
  IS
  --
    Lrf_ParamsDets SYS_REFCURSOR;
  --
  BEGIN
    OPEN Lrf_ParamsDets FOR SELECT APD.*
                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD
                            ON APD.PARAMETRO_ID = APC.ID_PARAMETRO
                            WHERE APC.NOMBRE_PARAMETRO = Fv_NombreParametro 
                            AND APC.ESTADO             = 'Activo'
                            AND APD.ESTADO             = 'Activo'
                            AND APD.EMPRESA_COD        = Fv_CodEmpresa ;
    --
    RETURN Lrf_ParamsDets;
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'GNRLPCK_UTIL.F_GET_PARAMS_DETS_EMPRESA', 
                                          'Error al consultar parametro ' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  END F_GET_PARAMS_DETS_EMPRESA; 

END GNRLPCK_UTIL;
/
