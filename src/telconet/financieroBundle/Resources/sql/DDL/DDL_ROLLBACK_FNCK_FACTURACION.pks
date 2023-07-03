CREATE OR REPLACE PACKAGE DB_FINANCIERO.FNCK_FACTURACION AS

  /*
  * Documentación para TYPE 'Lrf_Result'.
  *
  * Tipo de datos para el retorno de la información correspondiente a pagos o facturas.
  *
  * @author Ricardo Robles <rrobles@telconet.ec>
  * @version 1.0 05-06-2019
  *
  */
  TYPE Lr_Listado IS RECORD(
    TOTAL_REGISTRO NUMBER);
    --
  TYPE Lt_Result IS TABLE OF Lr_Listado;
   --
  TYPE Lrf_Result
    IS
      REF CURSOR;

  /*
  * Documentación para procedimiento P_PREFACTURA_X_CLIENTE.
  *
  * Procedimiento para facturar los servicios de los clientes que se les aplica un cambio de ciclo.
  * Genera una factura proporcional en base al ciclo de facturacion del cliente.
  * Costo del Query C_PuntosFact: 6968
  * Costo del Query C_DetServic: 13
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 12-09-2017 - Version inicial
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.1 24-10-2017 - Modificación del query principal, se elimina el filtro por ciclos, se realiza la factura de alcance
  *                           a todos los clientes que posean dicha caracteristica.
  *                           Se elimina el ingreso del mes y año de consumo, y se agrega el rango del consumo en la facturas de alcance.
  *
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.2 16-12-2019 - Se agrega en el cursor 'C_PuntosFact' el campo del select 'fe_UltFact_Periodo' al group by, para solventar
  *                           y obtener la información de los puntos a facturar. 
  *
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.3 26-12-2019 - Se excluye el proceso que realizaba para las solicitudes de descuento único.
  *                         - Se cambia el nombre de usuario de la factura a 'telcos_cambio_ciclo'.
  */
  PROCEDURE P_PREFACTURA_X_CLIENTE;

  /*
  * Documentación para procedimiento P_PERIODO_FACTURACION.
  *
  * Procedimiento para extraer los dias proporcionales que se le deben facturar al cliente.
  *
  * @param Pn_EmpresaCod            IN   NUMBER  (Codigo de la empresa)
  * @param Pd_FechaFinUltFact       IN   DATE    (Fecha fin de la ultima factura del cliente)
  * @param Pn_IdCiclo               IN   NUMBER  (Codigo del ciclo de facturacion)
  * @param Pd_FechaInicioFact       OUT  DATE    (Fecha de inicio del rango de la factura proporcional)
  * @param Pd_FechaFinFact          OUT  DATE    (Fecha fin del rango de la factura proporcional)
  * @param Pn_CantidadDiasAFact     OUT  NUMBER  (Cantidad de dias a facturar)
  * @param Pn_CantidadDiasTotalMes  OUT  NUMBER  (Cantidad de dias que tiene el mes a facturar)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 12-09-2017 - Version inicial
  */
  PROCEDURE P_PERIODO_FACTURACION(Pn_EmpresaCod             IN  NUMBER,
                                  Pd_FechaFinUltFact        IN  DATE,
                                  Pn_IdCiclo                IN  NUMBER,
                                  Pv_FechaInicioFact        OUT VARCHAR2,
                                  Pv_FechaFinFact           OUT VARCHAR2,
                                  Pn_CantidadDiasAFact      OUT NUMBER,
                                  Pn_CantidadDiasTotalMes   OUT NUMBER);

 /**
  * Procedimiento que obtiene la siguiente fecha a facturar en base a un ciclo específico.
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0
  * @since 08-06-2018
  *
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.1
  * @since 27-09-2018
  * Se envía por parámetro la fecha a validar, por defecto es el SYSDATE.
  */
  PROCEDURE P_OBTIENE_FE_SIG_CICLO_FACT(Pn_IdCiclo       IN  NUMBER,
                                        Pd_FeAValidar    IN  DATE DEFAULT SYSDATE,
                                        Pd_FeFacturacion OUT DATE);

 /**
  * Procedimiento que crea facturas de alcance por CRS.
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0
  * @since 13-06-2018
  *
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.1
  * @since 28-08-2018
  * Se agrega el filtro por característica_Id para obtener los puntos a facturar.
  * Se fija en NULL el valor de los campos MES_CONSUMO y ANIO_CONSUMO para que que no sea tomada por la validación de la facturación mensual de
  * facturas creadas.
  *
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.2
  * @since 27-09-2018
  * Se modifican los parámetros al llamar a procedimiento que obtiene la siguiente fecha a facturar en base a un ciclo. P_OBTIENE_FE_SIG_CICLO_FACT
  *
  * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
  * @version 1.3 03-12-2019 - Se valida si existe Solicitud de Descuento Promocional Mensual para ser aplicada en la Facturación. 
  *
  * @author José Candelario <jcandelario@telconet.ec>
  * @version 1.4 01-07-2020 - Se agregan excepciones WHEN OTHERS a los BEGIN internos y se limita los campos observación a 500 carácteres.
  */
  PROCEDURE P_FACTURACION_ALCANCE_CRS(Pv_UsrCreacion IN VARCHAR2);

  /**
   * Documentación para P_FACTURACION_UNICA
   * Procedimiento que realiza la facturacion única.
   * 
   * @author Hector Lozano <hlozano@telconet.ec>
   * @version 1.0 22/08/2018
   *
   * @author Edgar Holguín <eholguin@telconet.ec>
   * @version 1.1 16/01/2019 Se realiza corrección en cáĺculo de impuesto, descuento y valor en detalle del documento.
   *
   * @autor Hector Lozano <hlozano@telconet.ec>
   * @version 1.2 26/03/2019 Se realiza el cálculo del valor del impuesto del IVA e ICE correspondiente al detalle, si este aplica.
   * 
   * @author Edgar Holguín <eholguin@telconet.ec>
   * @version 1.3 30/06/2020 Se agrega condición que excluye servicios que ya hayan sido facturados.
   *
   * @author Edgar Holguín <eholguin@telconet.ec>
   * @version 1.4 30/07/2020 Se agrega condición que excluye servicios que ya hayan sido facturados, se valida servicio a nivel de detalle de factura.
   *
   * @param Pv_UsrCreacion       IN VARCHAR2 Recibe el usuario
   * @param Pv_PrefijoEmpresa    IN VARCHAR2 Recibe el prefijo de la empresa
   * @param Pv_EmpresaCod        IN VARCHAR2 Recibe el código de la empresa
   */
  PROCEDURE P_FACTURACION_UNICA(Pv_UsrCreacion     IN VARCHAR2,
                                Pv_PrefijoEmpresa  IN VARCHAR2,
                                Pv_EmpresaCod      IN VARCHAR2);

 /**
  * Procedimiento actualiza el registro de INFO_SERVICIO_CARACTERISTICA.
  * Es de tipo AUTONOMUS TRANSACTION para no perder el update.
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0
  * @since 13-06-2018
  */
  PROCEDURE P_UPDATE_AT_SERV_CARAC (Pr_InfoServicioCaracteristica IN  DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA%ROWTYPE,
                                    Pv_MsnError                   OUT VARCHAR2);
  /*
  * Documentación para procedimiento P_SENSA_CICLO.
  *
  * Procedimiento para facturar los servicios de los clientes que se les aplica un cambio de ciclo.
  * Genera una factura proporcional en base al ciclo de facturacion del cliente.
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 12-09-2017 - Version inicial
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.1 24-09-2017 - Se modifica la funcion para que no realice el filtro de ciclos
  */
  PROCEDURE P_SENSA_CICLO;

  /*
  * Documentación para procedimiento P_INSERT_CABECERA_FACTURACION.
  *
  * Procedimiento para insertar la cabecera de la factura.
  *
  * @param Pr_Docum_Fin_Cab  IN   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE  (Registro de la dabecera)
  * @param Pv_Error          OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.1 24-09-2017 - Se agrega el campo de rango de consumo para ser insertado en la cabecera.
  */
  PROCEDURE P_INSERT_CABECERA_FACTURACION(Pr_Docum_Fin_Cab IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_INSERT_HISTORIAL_FACTURACION.
  *
  * Procedimiento para insertar el historial de la factura.
  *
  * @param Pr_Docum_Hist  IN   DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE  (Registro del historial)
  * @param Pv_Error       OUT  VARCHAR2                                        (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_INSERT_HISTORIAL_FACTURACION(Pr_Docum_Hist IN  DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE,
                                           Pv_Error      OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_INSERT_DETALLE_FACTURACION.
  *
  * Procedimiento para insertar los detalles de la factura.
  *
  * @param Pr_Docum_Fin_Det  IN   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE  (Registro del detalle)
  * @param Pv_Error          OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_INSERT_DETALLE_FACTURACION(Pr_Docum_Fin_Det IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE,
                                         Pv_Error         OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_INSERT_IMPUESTO_FACTURACION.
  *
  * Procedimiento para insertar los impuestos de la factura.
  *
  * @param Pr_Docum_Fin_Imp  IN   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE  (Registro de los impuestos)
  * @param Pv_Error          OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_INSERT_IMPUESTO_FACTURACION(Pr_Docum_Fin_Imp IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_INSERT_DET_SOL_HIST.
  *
  * Procedimiento para insertar el historico del detalle de la solicitud.
  *
  * @param Pr_Det_Sol_Hist  IN   DB_COMERCIAL.INFO_DETALLE_SOL_HIST%ROWTYPE  (Registro historico de solicitudes)
  * @param Pv_Error         OUT  VARCHAR2                                    (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_INSERT_DET_SOL_HIST(Pr_Det_Sol_Hist IN  DB_COMERCIAL.INFO_DETALLE_SOL_HIST%ROWTYPE,
                                  Pv_Error        OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_ACTUAL_CABECERA_FACTURACION.
  *
  * Procedimiento para actualizar la cabecera de la factura.
  *
  * @param Pr_Docum_Fin_Cab  IN   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE  (Registro de la cabecera)
  * @param Pv_Error          OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_ACTUAL_CABECERA_FACTURACION(Pr_Docum_Fin_Cab IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_ACTUAL_NUMERAC_FACTURACION.
  *
  * Procedimiento para actualizar la numeracion del SRI.
  *
  * @param Pr_AdmiNumeracion  IN   FNKG_TYPES.Lr_AdmiNumeracion  (Registro de la cabecera)
  * @param Pv_Error           OUT  VARCHAR2                      (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_ACTUAL_NUMERAC_FACTURACION(Pr_AdmiNumeracion IN  FNKG_TYPES.Lr_AdmiNumeracion,
                                         Pv_Error          OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_ACTUAL_PERS_EMP_ROL_CARACT.
  *
  * Procedimiento para actualizar la caracteristica
  *
  * @param Pr_Pers_Emp_Rol_Caract  IN   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC%ROWTYPE  (Registro)
  * @param Pv_Error                OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_ACTUAL_PERS_EMP_ROL_CARACT(Pr_Pers_Emp_Rol_Caract IN  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC%ROWTYPE,
                                         Pv_Error            OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_ACTUAL_DET_SOLICITUD.
  *
  * Procedimiento para actualizar el estado del historial, actualiza los registros procesados.
  *
  * @param Pr_Det_Solicitud  IN   DB_COMERCIAL.INFO_DETALLE_SOLICITUD%ROWTYPE  (Actualiza la solicitud)
  * @param Pv_Error          OUT  VARCHAR2                                     (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 14-09-2017 - Version inicial
  */
  PROCEDURE P_ACTUAL_DET_SOLICITUD(Pr_Det_Solicitud IN  DB_COMERCIAL.INFO_DETALLE_SOLICITUD%ROWTYPE,
                                   Pv_Error         OUT VARCHAR2);

  /*
  * Documentación para procedimiento P_PERIODO_FACTURACION_X_PUNTO.
  *
  * Procedimiento para extraer los dias restantes a facturar y rango de fecha a facturar.
  *
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.1
  * @since 26/07/2018
  * Se agrega el filtro del estado de la característica en el cursor principal para obtener el ciclo actual del cliente.
  *
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.2
  * @since 15-01-2018
  * Se agregan parámetros Pv_TipoProceso y Pn_ServicioId para poder determinar si un servicio sufrió CRS. Para los cambio de precio, es necesario
  * modificar el rango de fechas, debido a que el proceso de facturación de CRS, crea una nueva factura por el ciclo establecido del nuevo cliente.
  *
  * @param Pn_EmpresaCod             IN   NUMBER    (Codigo de la empresa)
  * @param Pv_FechaActivacion        IN   VARCHAR2  (Fecha de activacion del servicio)
  * @param Pn_PuntoPadre             IN   NUMBER    (Punto padre a facturar)
  * @param Pv_TipoProceso            IN   VARCHAR2  (Tipo de proceso a validar)
  * @param Pn_ServicioId             IN   DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE    (Servicio a verificar CRS)
  * @param Pd_FechaInicioPeriodo     OUT  VARCHAR2  (Fecha de inicio del periodo)
  * @param Pd_FechaFinPeriodo        OUT  VARCHAR2  (Fecha fin del periodo)
  * @param Pn_CantidadDiasTotalMes   OUT  NUMBER    (Cantidad de dias del ciclo)
  * @param Pn_CantidadDiasRestantes  OUT  NUMBER    (Cantidad de dias restantes del mes)
  */
  PROCEDURE P_PERIODO_FACTURACION_X_PUNTO(  Pn_EmpresaCod            IN  NUMBER,
                                            Pv_FechaActivacion       IN  VARCHAR2,
                                            Pn_PuntoPadre            IN  NUMBER,
                                            Pv_TipoProceso           IN  VARCHAR2 DEFAULT NULL,
                                            Pn_ServicioId            IN  DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE DEFAULT NULL,
                                            Pd_FechaInicioPeriodo    OUT VARCHAR2,
                                            Pd_FechaFinPeriodo       OUT VARCHAR2,
                                            Pn_CantidadDiasTotalMes  OUT NUMBER,
                                            Pn_CantidadDiasRestantes OUT NUMBER );

  /*
  * Documentación para procedimiento P_INSERT_INFO_DOC_CARACT.
  *
  * Procedimiento para insertar el registro de la tabla INFO_DOCUMENTO_CARACTERISTICA.
  *
  * @param Pr_InfoDocCaract  IN   DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE  (Variable Registro)
  * @param Pv_Error          OUT  VARCHAR2                                             (Variable para capturar errores)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 05-10-2017 - Version inicial
  */
  PROCEDURE P_INSERT_INFO_DOC_CARACT(Pr_InfoDocCaract IN  DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE,
                                     Pv_Error         OUT VARCHAR2);
  
  /*
  * Documentación para procedimiento F_GET_FECHA_ULT_FACT.
  *
  * Procedimiento para extraer la ultima fecha de facturación de un punto.
  *
  * @param Pn_PuntFact  IN   NUMBER  (Punto de Facturación)
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.0 12-10-2017 - Version inicial
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.1 24-10-2017 - Modificación de la forma de extraer el mes y año de consumo para las facturas con rango.
  *
  * @author Jorge Guerrero <jguerrerop@telconet.ec>
  * @version 1.1 26-04-2018 - Se excluyen las facturas de telcos Contrato (facturas de instalación).
  * 
  * @author Anabelle Peñaherrera <apenaherrerap@telconet.ec>
  * @version 1.2 28-06-2018 - Se agrega verificación que la Ultima Factura sea del servicio que se recibe por parametro.   
  *
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.3 18-12-2019 - Se modifica la llamada a consultar de los usuarios por medio del parámetro 'SOLICITUDES_DE_CONTRATO'.
  */
  FUNCTION F_GET_FECHA_ULT_FACT(Pn_PuntFact NUMBER, Fn_IdServicio DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE)
    RETURN VARCHAR2;


  /**
  * Documentacion para el procedimiento P_FACT_OFFICE365_CANCEL
  *
  * Método encargado de facturar los sericios con producto Office365 cancelados.
  *
  * @param Pn_ServicioId      IN NUMBER   Id del servicio a facturar. 
  * @param Pv_PrefijoEmpresa  IN VARCHAR2 Prefijo de la empresa 
  * @param Pv_EmpresaCod      IN VARCHAR2 Código de empresa
  * @param Pv_UsrCreacion     IN VARCHAR2 Usuario de creación
  * @param Pv_Ip              IN VARCHAR2 Ip de creación
  *
  * @author Edgar Holguin <eholguin@telconet.ec>
  * @version 1.0 04-07-2018
  * @author Edgar Holguin <eholguin@telconet.ec>
  * @version 1.1 31-10-2018 Se modifica fórmula de valor a facturar (precio*cantidad*12)- valor facturado.
  *
  * @author Edgar Holguin <eholguin@telconet.ec>
  * @version 1.2 22-01-2018 Se agrega NVL para valor total facturado para escenario cuando es NULL
  */      
  PROCEDURE P_FACT_OFFICE365_CANCEL(Pn_ServicioId      IN NUMBER,
                                    Pv_PrefijoEmpresa  IN VARCHAR2,
                                    Pv_EmpresaCod      IN VARCHAR2,
                                    Pv_UsrCreacion     IN VARCHAR2,
                                    Pv_Ip              IN VARCHAR2);

  /**
  * Documentación para función F_WS_FECHA_PAGO_FACTURA.
  * Función para mostrar la fecha de pago de una factura FAC-FACP.
  * Costo del Query C_ObtenerInfFacturacionCliente: 14
  * Costo del Query C_ObtenerDiaLaborable: 2
  * Costo del Query C_getParametros: 3
  *
  * @author Ricardo Robles <rrobles@telconet.ec>
  * @version 1.0 05-06-2019
  *
  * @param  NUMBER IN Fn_IdPersonaRol
  * @param  NUMBER IN Fn_EmpresaCod
  */                                  
  FUNCTION F_WS_FECHA_PAGO_FACTURA(Fn_IdPersonaRol IN DB_FINANCIERO.INFO_PERSONA_EMPRESA_ROL.ID_PERSONA_ROL%TYPE,
                                   Fn_EmpresaCod   IN DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE)
    RETURN VARCHAR2;

  /**
  * Documentación para procedimiento P_WS_ULTIMAS_FACTURAS_X_PUNTO.
  * Procedimiento para listar las n últimas facturas FAC-FACP por cada punto del cliente.
  * Costo del Query 28.
  *
  * @author Ricardo Robles <rrobles@telconet.ec>
  * @version 1.0 05-06-2019
  *
  * @param  NUMBER     IN  Pn_IdPunto
  * @param  NUMBER     IN  Pn_CantidadDocs
  * @param  Lrf_Result OUT Prf_Result
  * @param  VARCHAR2   OUT Pv_Status
  * @param  VARCHAR2   OUT Pv_Mensaje
  */                                  
  PROCEDURE P_WS_ULTIMAS_FACTURAS_X_PUNTO(Pn_IdPunto      IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                          Pn_CantidadDocs IN  NUMBER,
                                          Prf_Result      OUT Lrf_Result,
                                          Pv_Status       OUT VARCHAR2,
                                          Pv_Mensaje      OUT VARCHAR2 );

  /**
  * Documentación para procedimiento P_WS_ULTIMOS_PAGOS_X_PUNTO.
  * Procedimiento para listar los n últimos pagos 'PAG','ANT','ANTS','PAGC','ANTC' por cada punto del cliente.
  * Costo del Query 23.
  *
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.0 05-06-2019
  *
  * @param  NUMBER     IN  Pn_IdPunto
  * @param  NUMBER     IN  Pn_CantidadDocs
  * @param  Lrf_Result OUT Prf_Result
  * @param  VARCHAR2   OUT Pv_Status
  * @param  VARCHAR2   OUT Pv_Mensaje
  */                                  
  PROCEDURE P_WS_ULTIMOS_PAGOS_X_PUNTO(Pn_IdPunto      IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                       Pn_CantidadDocs IN  NUMBER,
                                       Prf_Result      OUT Lrf_Result,
                                       Pv_Status       OUT VARCHAR2,
                                       Pv_Mensaje      OUT VARCHAR2 );  

  /**
  * Documentación para procedimiento P_WS_TIPO_NEGOCIO_X_PUNTO.
  * Procedimiento para obtener el tipo de negocio de un plan de internet: 'HOME','PYME','PRO' por cada punto del cliente.
  * Costo del Query 4.
  *
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.0 05-06-2019
  *
  * @param  NUMBER     IN  Pn_IdPunto
  * @param  Lrf_Result OUT Prf_Result
  * @param  VARCHAR2   OUT Pv_Status
  * @param  VARCHAR2   OUT Pv_Mensaje
  */
  PROCEDURE P_WS_TIPO_NEGOCIO_X_PUNTO(Pn_IdPunto IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                      Prf_Result OUT Lrf_Result,
                                      Pv_Status  OUT VARCHAR2,
                                      Pv_Mensaje OUT VARCHAR2 );

  /**
  * Documentación para procedimiento P_WS_FECHA_FIRMA_CONTRATO.
  * Procedimiento para obtener la fecha de firma de contrato del cliente.
  * Costo del Query 12.
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.0 05-06-2019
  *
  * @param  NUMBER     IN Pn_IdPunto
  * @param  Lrf_Result OUT Prf_Result
  * @param  VARCHAR2   OUT Pv_Status
  * @param  VARCHAR2   OUT Pv_Mensaje
  */                                              
  PROCEDURE P_WS_FECHA_FIRMA_CONTRATO(Pn_IdPunto IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                      Prf_Result OUT Lrf_Result,
                                      Pv_Status  OUT VARCHAR2,
                                      Pv_Mensaje OUT VARCHAR2); 

  /**
  * Documentación para el procedure 'P_GET_VALOR_TOTAL_NC_BY_FACT'.
  *
  * Procedimiento que obtiene el saldo disponible, de la diferencia entre factura y el valor total de notas de créditos. 
  * Costo del Query 9
  * @author Alex Arreaga <atarreaga@telconet.ec>
  * @version 1.0 29-01-2020
  *
  * PARÁMETROS:
  * @param Pn_IdDocumento IN   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE  (Id del documento)
  * @param Pn_Saldo       OUT  NUMBER  (Diferencia entre el valor de la factura y el total de las notas de créditos)
  */
   PROCEDURE P_GET_VALOR_TOTAL_NC_BY_FACT(
    Pn_IdDocumento IN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE,
    Pn_Saldo  OUT NUMBER );  
  
END FNCK_FACTURACION;
/


CREATE OR REPLACE PACKAGE BODY DB_FINANCIERO.FNCK_FACTURACION AS

  PROCEDURE P_PREFACTURA_X_CLIENTE
  AS

    CURSOR C_Impuesto(Pv_TipImp VARCHAR2, Pv_Estado VARCHAR2)
    IS
      select id_impuesto, porcentaje_impuesto
      from DB_GENERAL.admi_impuesto
      where tipo_impuesto = Pv_TipImp
      AND estado = Pv_Estado;

    CURSOR C_Empresa(Pv_PrefijoEmp VARCHAR2)
    IS
      select cod_empresa
      from DB_COMERCIAL.info_empresa_grupo
      where prefijo =Pv_PrefijoEmp;

    CURSOR C_PuntosFact
    IS
      SELECT ise.punto_facturacion_id,
              ipda.gasto_administrativo,
              ipe.paga_iva,
              ise.tipo_orden,
              DB_FINANCIERO.FNCK_CONSULTS.F_VALIDA_CLIENTE_COMPENSADO(iper.ID_PERSONA_ROL, iper.OFICINA_ID, ier.EMPRESA_COD, ip.SECTOR_ID, ise.PUNTO_FACTURACION_ID ) COMPENSACION,
              (select MAX(IPERC.VALOR)
              from DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC iperc,
                   DB_COMERCIAL.ADMI_CARACTERISTICA ac
              where iperc.PERSONA_EMPRESA_ROL_ID=iper.id_persona_rol
              and AC.ID_CARACTERISTICA=IPERC.CARACTERISTICA_ID
              and iperc.ESTADO='Activo'
              and AC.DESCRIPCION_CARACTERISTICA='CICLO_FACTURACION') as ciclo_id,
              (FNCK_FACTURACION.F_GET_FECHA_ULT_FACT(ise.punto_facturacion_id, ise.id_servicio)) AS fe_UltFact_Periodo,
              iper.ID_PERSONA_ROL,
              iper.OFICINA_ID
            FROM DB_COMERCIAL.info_servicio_historial ish
            JOIN DB_COMERCIAL.info_servicio ise
            ON ise.id_servicio=ish.servicio_id
            LEFT JOIN DB_COMERCIAL.info_plan_cab ipc
            ON ipc.id_plan=ise.plan_id
            JOIN DB_COMERCIAL.info_punto ip
            ON ip.id_punto=ise.punto_facturacion_id
            LEFT JOIN DB_COMERCIAL.info_punto_dato_adicional ipda
            ON ipda.punto_id=ip.id_punto
            JOIN DB_COMERCIAL.info_persona_empresa_rol iper
            ON iper.id_persona_rol=ip.persona_empresa_rol_id
            JOIN DB_COMERCIAL.info_persona ipe
            ON ipe.id_persona = iper.persona_id
            JOIN DB_COMERCIAL.info_empresa_rol ier
            ON ier.id_empresa_rol=iper.empresa_rol_id
            JOIN DB_COMERCIAL.admi_tipo_negocio atn
            ON atn.id_tipo_negocio=ip.tipo_negocio_id
            JOIN DB_GENERAL.admi_rol ar
            ON ar.id_rol                 = ier.rol_id
            WHERE ish.estado             = 'Activo'
            AND ise.estado               = 'Activo'
            AND ise.cantidad             > 0
            AND ier.empresa_cod          = 18
            AND ise.es_venta             = 'S'
            AND ar.descripcion_rol       = 'Cliente'
            AND ise.precio_venta         > 0
            AND atn.codigo_tipo_negocio <> 'ISPM'
            AND ise.frecuencia_producto  = 1
            AND EXISTS
              (SELECT 1
               FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERCA,
                    DB_COMERCIAL.ADMI_CARACTERISTICA ACA
               WHERE IPERCA.PERSONA_EMPRESA_ROL_ID=iper.id_persona_rol
               AND IPERCA.VALOR='N'
               AND IPERCA.ESTADO='Activo'
               AND IPERCA.CARACTERISTICA_ID=ACA.ID_CARACTERISTICA
               AND ACA.DESCRIPCION_CARACTERISTICA='CAMBIO_CICLO_FACTURADO')
            GROUP BY ise.punto_facturacion_id,
              ipe.paga_iva,
              ipda.gasto_administrativo,
              ise.tipo_orden,
              (FNCK_FACTURACION.F_GET_FECHA_ULT_FACT(ise.punto_facturacion_id, ise.id_servicio)),
              iper.ID_PERSONA_ROL,
              iper.OFICINA_ID,
              ip.SECTOR_ID,
              ier.EMPRESA_COD;
            

      CURSOR C_DetServic(Pn_PuntoFact NUMBER)
      IS
      SELECT
              ish.servicio_id,
              ise.punto_id,
              ise.punto_facturacion_id,
              ise.plan_id,
              ise.producto_id,
              ise.cantidad,
              ise.precio_venta,
              ise.valor_descuento,
              ise.descuento_unitario,
              ise.porcentaje_descuento,
              iper.id_persona_rol,
              iper.oficina_id,
              ise.tipo_orden,
              ise.FRECUENCIA_PRODUCTO,
              ise.DESCRIPCION_PRESENTA_FACTURA,
              atn.nombre_tipo_negocio
            FROM DB_COMERCIAL.info_servicio_historial ish
            JOIN DB_COMERCIAL.info_servicio ise
            ON ise.id_servicio=ish.servicio_id
            LEFT JOIN DB_COMERCIAL.info_plan_cab ipc
            ON ipc.id_plan=ise.plan_id
            JOIN DB_COMERCIAL.info_punto ip
            ON ip.id_punto=ise.punto_facturacion_id
            JOIN DB_COMERCIAL.info_persona_empresa_rol iper
            ON iper.id_persona_rol=ip.persona_empresa_rol_id
            JOIN DB_COMERCIAL.info_empresa_rol ier
            ON ier.id_empresa_rol=iper.empresa_rol_id
            JOIN DB_COMERCIAL.admi_tipo_negocio atn
            ON atn.id_tipo_negocio       =ip.tipo_negocio_id
            WHERE ish.estado             = 'Activo'
            AND ise.estado               = 'Activo'
            AND ise.cantidad             > 0
            AND ise.precio_venta         > 0
            AND ier.empresa_cod          = 18
            AND ise.es_venta             = 'S'
            AND ise.punto_facturacion_id = Pn_PuntoFact
            AND atn.codigo_tipo_negocio <> 'ISPM'
            AND ise.frecuencia_producto  = 1
            GROUP BY ish.servicio_id,
              ise.punto_id,
              ise.punto_facturacion_id,
              ise.plan_id,
              ise.producto_id,
              ise.cantidad,
              ise.precio_venta,
              iper.id_persona_rol,
              ise.valor_descuento,
              ise.descuento_unitario,
              ise.porcentaje_descuento,
              ise.FRECUENCIA_PRODUCTO,
              ise.DESCRIPCION_PRESENTA_FACTURA,
              iper.oficina_id,
              ise.tipo_orden,
              atn.nombre_tipo_negocio
            ORDER BY ise.punto_facturacion_id;

      CURSOR C_TipoDocumento
      IS
        SELECT id_tipo_documento
        FROM DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO
        WHERE CODIGO_TIPO_DOCUMENTO='FACP';

      CURSOR C_ProductReubicTrasl(Pv_CodigProduct VARCHAR2,Pv_EmpPrefijo VARCHAR2)
      IS
        select ap.id_producto,
               TO_NUMBER(REGEXP_SUBSTR(ap.funcion_precio,'[[:digit:]]+[[:digit:]]'), '99999.99') as precio
        from DB_COMERCIAL.admi_producto ap
        join DB_COMERCIAL.info_empresa_grupo ieg on ieg.cod_empresa=ap.empresa_cod
        where ap.codigo_producto=Pv_CodigProduct
        and ieg.prefijo=Pv_EmpPrefijo;
      
      CURSOR C_Caracteristica(Pv_NombCaract VARCHAR2)
      IS
        SELECT ID_CARACTERISTICA
        FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = Pv_NombCaract;

      Lc_Impuesto C_Impuesto%ROWTYPE;
      Lc_Empresa  C_Empresa%ROWTYPE;
      Lc_TipoDocumento C_TipoDocumento%ROWTYPE;
      Lc_ProductReubicTrasl C_ProductReubicTrasl%ROWTYPE;
      Lc_Caracteristica C_Caracteristica%ROWTYPE;

      Lv_TipImp VARCHAR2(100) := 'IVA';
      Lv_Estado VARCHAR2(100) := 'Activo';
      Lv_PrefijoEmp VARCHAR2(100) := 'MD';
      Ln_IdDoc NUMBER:=0;
      Ln_IdDocDet NUMBER:=0;
      Lb_ObtenerInfo BOOLEAN:=TRUE;
      Lv_Codigo VARCHAR2(100):='';
      Lv_UsuarioFactura VARCHAR2(200):='telcos_cambio_ciclo';

      Lv_FechaInicioFact    VARCHAR2(500):='';
      Lv_FechaFinFact       VARCHAR2(500):='';
      Ln_CantidadDiasFact   NUMBER:=0;
      Ln_CantidadDiasTotal  NUMBER:=0;
      Ln_Contador           NUMBER:=0;

      Ln_ValorProporcional  NUMBER(9,2):=0;
      Ln_PorceDesc          NUMBER(9,2):=0;
      Ln_ValorDesc          NUMBER(9,2):=0;
      Ln_Impuesto_Det       NUMBER(9,2):=0;

      Ln_ValorSubFctCab     NUMBER(9,2):=0;
      Ln_ValorDescFctCab    NUMBER(9,2):=0;
      Ln_ValorTotalFctCab   NUMBER(9,2):=0;
      Ln_ValorImpFctCab     NUMBER(9,2):=0;
      Lv_Secuencia          VARCHAR2(1000):='';
      Lv_Numeracion         VARCHAR2(1000):='';

      Lv_EsMatriz              VARCHAR2(5):='';
      Lv_EsOficinaFacturacion  VARCHAR2(5):='';
      Lv_CodigoNumeracion      VARCHAR2(10):='';

      Lv_Error                 VARCHAR2(4000):='';

      Lrf_Numeracion                DB_FINANCIERO.FNKG_TYPES.Lrf_AdmiNumeracion;
      Lr_AdmiNumeracion             DB_FINANCIERO.FNKG_TYPES.Lr_AdmiNumeracion;

      Lr_Docum_Fin_Cab              DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE;
      Lr_Docum_Hist                 DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE;
      Lr_Docum_Fin_Det              DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE;
      Lr_Docum_Fin_Imp              DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE;
      Lr_Pers_Emp_Rol_Caract        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC%ROWTYPE;
      Lr_InfoDocCaract              DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE;

      Error_Cntrl                   EXCEPTION;

  BEGIN

    Lv_EsMatriz               := 'S';
    Lv_EsOficinaFacturacion   := 'S';
    Lv_CodigoNumeracion       := 'FACE';

    OPEN C_Impuesto(Lv_TipImp,Lv_Estado);
    FETCH C_Impuesto INTO Lc_Impuesto;
    CLOSE C_Impuesto;

    OPEN C_Empresa(Lv_PrefijoEmp);
    FETCH C_Empresa INTO Lc_Empresa;
    CLOSE C_Empresa;

    OPEN C_TipoDocumento;
    FETCH C_TipoDocumento INTO Lc_TipoDocumento;
    CLOSE C_TipoDocumento;

    --Recorre Puntos de Facturacion encontrados
    FOR J IN C_PuntosFact LOOP

        BEGIN
        --Extrae informacion inicial para armar cabecera
        Ln_IdDoc := DB_FINANCIERO.SEQ_INFO_DOC_FINANCIERO_CAB.NEXTVAL;

        --Valida que el cliente no tenga factura previa
        IF J.fe_UltFact_Periodo = 'X' THEN
          Ln_CantidadDiasFact := 0;

          --Actualiza Registro en Caracteristica
          Lr_Pers_Emp_Rol_Caract:=NULL;
          Lr_Pers_Emp_Rol_Caract.Persona_Empresa_Rol_Id:=J.ID_PERSONA_ROL;
          Lr_Pers_Emp_Rol_Caract.Valor:='N';
          Lr_Pers_Emp_Rol_Caract.Estado:='Eliminado';

          Lv_Error:=NULL;
          DB_FINANCIERO.FNCK_FACTURACION.P_ACTUAL_PERS_EMP_ROL_CARACT(Lr_Pers_Emp_Rol_Caract,Lv_Error);

          IF Lv_Error IS NOT NULL THEN
            Lv_Error:='P_ACTUAL_PERS_EMP_ROL_CARACT:'||Lv_Error;
            RAISE Error_Cntrl;
          END IF;

        ELSE

          --Calculo de fechas y numeros de dias a facturar (proporcional)
          DB_FINANCIERO.FNCK_FACTURACION.P_PERIODO_FACTURACION(TO_NUMBER(Lc_Empresa.cod_empresa),
                                                               TO_DATE(J.fe_UltFact_Periodo,'DD/MM/YYYY'),
                                                               TO_NUMBER(J.ciclo_id),
                                                               Lv_FechaInicioFact,
                                                               Lv_FechaFinFact,
                                                               Ln_CantidadDiasFact,
                                                               Ln_CantidadDiasTotal);
        END IF;

        --Valida si hay dias que facturar
        IF Ln_CantidadDiasFact > 0 THEN

        --Ingreso de Cabecera
        Lr_Docum_Fin_Cab:=NULL;
        Lr_Docum_Fin_Cab.Id_Documento:=Ln_IdDoc;
        Lr_Docum_Fin_Cab.Oficina_Id:=NULL;
        Lr_Docum_Fin_Cab.Punto_Id:=J.punto_facturacion_id;
        Lr_Docum_Fin_Cab.Tipo_Documento_Id:=5;
        Lr_Docum_Fin_Cab.Entrego_Retencion_Fte:='N';
        Lr_Docum_Fin_Cab.Estado_Impresion_Fact:='Pendiente';
        Lr_Docum_Fin_Cab.Es_Automatica:='S';
        Lr_Docum_Fin_Cab.Prorrateo:='S';
        Lr_Docum_Fin_Cab.Reactivacion:='N';
        Lr_Docum_Fin_Cab.Recurrente:='N';
        Lr_Docum_Fin_Cab.Comisiona:='S';
        Lr_Docum_Fin_Cab.Fe_Creacion:=sysdate;
        Lr_Docum_Fin_Cab.Usr_Creacion:=Lv_Usuariofactura;
        Lr_Docum_Fin_Cab.Subtotal:=0;
        Lr_Docum_Fin_Cab.Subtotal_Cero_Impuesto:=0;
        Lr_Docum_Fin_Cab.Subtotal_Con_Impuesto:=0;
        Lr_Docum_Fin_Cab.Subtotal_Descuento:=0;
        Lr_Docum_Fin_Cab.Valor_Total:=0;
        Lr_Docum_Fin_Cab.Rango_Consumo:='Del '|| TO_CHAR(TO_DATE(Lv_FechaInicioFact,'DD/MM/YYYY'),'DD MONTH YYYY','nls_date_language=Spanish') ||' al '|| TO_CHAR(TO_DATE(Lv_FechaFinFact,'DD/MM/YYYY'),'DD MONTH YYYY','nls_date_language=Spanish');
        Lr_Docum_Fin_Cab.Fe_Emision:=NULL;

        Lv_Error:=NULL;
        DB_FINANCIERO.FNCK_FACTURACION.P_INSERT_CABECERA_FACTURACION(Lr_Docum_Fin_Cab,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_INSERT_CABECERA_FACTURACION:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;

        OPEN C_Caracteristica('FACTURA_ALCANCE');
        FETCH C_Caracteristica INTO Lc_Caracteristica;
        CLOSE C_Caracteristica;

        Lr_InfoDocCaract:=NULL;
        Lr_InfoDocCaract.Documento_Id:=Ln_IdDoc;
        Lr_InfoDocCaract.Caracteristica_Id:=Lc_Caracteristica.ID_CARACTERISTICA;
        Lr_InfoDocCaract.Valor:='X';
        Lr_InfoDocCaract.Fe_Creacion:=SYSDATE;
        Lr_InfoDocCaract.Usr_Creacion:=Lv_Usuariofactura;
        Lr_InfoDocCaract.Ip_Creacion:=NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1');
        Lr_InfoDocCaract.Estado:='Activo';

        DB_FINANCIERO.FNCK_FACTURACION.P_INSERT_INFO_DOC_CARACT(Lr_InfoDocCaract,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_INSERT_INFO_DOC_CARACT:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;

        --Ingresa en el Historial
        Lr_Docum_Hist:=NULL;
        Lr_Docum_Hist.Documento_Id:=Ln_IdDoc;
        Lr_Docum_Hist.Motivo_Id:=NULL;
        Lr_Docum_Hist.Fe_Creacion:=SYSDATE;
        Lr_Docum_Hist.Usr_Creacion:=Lv_UsuarioFactura;
        Lr_Docum_Hist.Estado:='Pendiente';
        Lr_Docum_Hist.Observacion:='Se crea la factura proporcional';

        Lv_Error:=NULL;
        DB_FINANCIERO.FNCK_FACTURACION.P_INSERT_HISTORIAL_FACTURACION(Lr_Docum_Hist,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_INSERT_HISTORIAL_FACTURACION:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;

        --Recorrido de Servicios del Punto
        FOR JJ IN C_DetServic(J.punto_facturacion_id) LOOP

          Ln_IdDocDet:=DB_FINANCIERO.SEQ_INFO_DOC_FINANCIERO_DET.NEXTVAL;

          --Datos para llenar el detalle
          Ln_ValorProporcional:=(Ln_CantidadDiasFact*JJ.precio_venta)/Ln_CantidadDiasTotal;

          Ln_Impuesto_Det:=((Ln_ValorProporcional)*Lc_Impuesto.porcentaje_impuesto)/100;

          --Ingreso de detalles de la factura
          Lr_Docum_Fin_Det:=NULL;
          Lr_Docum_Fin_Det.Id_Doc_Detalle:=Ln_IdDocDet;
          Lr_Docum_Fin_Det.Documento_Id:=Ln_IdDoc;
          Lr_Docum_Fin_Det.Plan_Id:=JJ.plan_id;
          Lr_Docum_Fin_Det.Punto_Id:=JJ.punto_id;
          Lr_Docum_Fin_Det.Cantidad:=JJ.cantidad;
          Lr_Docum_Fin_Det.Precio_Venta_Facpro_Detalle:=Ln_ValorProporcional;
          Lr_Docum_Fin_Det.Porcetanje_Descuento_Facpro:=Ln_PorceDesc;
          Lr_Docum_Fin_Det.Descuento_Facpro_Detalle:=Ln_ValorDesc;
          Lr_Docum_Fin_Det.Valor_Facpro_Detalle:=Ln_ValorProporcional;
          Lr_Docum_Fin_Det.Costo_Facpro_Detalle:=Ln_ValorProporcional;
          Lr_Docum_Fin_Det.Observaciones_Factura_Detalle:='Factura proporcional del '||Lv_FechaInicioFact||' al '||Lv_FechaFinFact||' por cambio de Ciclo';
          Lr_Docum_Fin_Det.Fe_Creacion:=SYSDATE;
          Lr_Docum_Fin_Det.Fe_Ult_Mod:=NULL;
          Lr_Docum_Fin_Det.Usr_Creacion:=Lv_UsuarioFactura;
          Lr_Docum_Fin_Det.Usr_Ult_Mod:=NULL;
          Lr_Docum_Fin_Det.Empresa_Id:=Lc_Empresa.cod_empresa;
          Lr_Docum_Fin_Det.Oficina_Id:=JJ.oficina_id;
          Lr_Docum_Fin_Det.Producto_Id:=JJ.producto_id;
          Lr_Docum_Fin_Det.Motivo_Id:=NULL;
          Lr_Docum_Fin_Det.Pago_Det_Id:=NULL;
          Lr_Docum_Fin_Det.Servicio_Id:=JJ.servicio_id;

          Lv_Error:=NULL;
          DB_FINANCIERO.FNCK_FACTURACION.P_INSERT_DETALLE_FACTURACION(Lr_Docum_Fin_Det,Lv_Error);

          IF Lv_Error IS NOT NULL THEN
            Lv_Error:='P_INSERT_DETALLE_FACTURACION:'||Lv_Error;
            RAISE Error_Cntrl;
          END IF;

          --Acumuladores de Valores del detalle
          Ln_ValorSubFctCab:=Ln_ValorSubFctCab+(Ln_ValorProporcional*JJ.cantidad);

          Lr_Docum_Fin_Imp:=NULL;
          Lr_Docum_Fin_Imp.Detalle_Doc_Id:=Ln_IdDocDet;
          Lr_Docum_Fin_Imp.Impuesto_Id:=Lc_Impuesto.id_impuesto;
          Lr_Docum_Fin_Imp.Valor_Impuesto:=Ln_Impuesto_Det;
          Lr_Docum_Fin_Imp.Porcentaje:=Lc_Impuesto.porcentaje_impuesto;
          Lr_Docum_Fin_Imp.Fe_Creacion:=SYSDATE;
          Lr_Docum_Fin_Imp.Fe_Ult_Mod:=NULL;
          Lr_Docum_Fin_Imp.Usr_Creacion:=Lv_UsuarioFactura;
          Lr_Docum_Fin_Imp.Usr_Ult_Mod:=NULL;

          Lv_Error:=NULL;
          DB_FINANCIERO.FNCK_FACTURACION.P_INSERT_IMPUESTO_FACTURACION(Lr_Docum_Fin_Imp,Lv_Error);

          IF Lv_Error IS NOT NULL THEN
            Lv_Error:='P_INSERT_IMPUESTO_FACTURACION:'||Lv_Error;
            RAISE Error_Cntrl;
          END IF;

          --Encerar datos del detalle
          Ln_ValorProporcional:=0;

        END LOOP;

        --Calculo de valores para cabecera
        Ln_ValorImpFctCab:=((Ln_ValorSubFctCab)*Lc_Impuesto.porcentaje_impuesto)/100;
        Ln_ValorTotalFctCab:=(Ln_ValorSubFctCab)+Ln_ValorImpFctCab;

        --Obtiene la Numeracion de la Factura
        Lrf_Numeracion:=DB_FINANCIERO.FNCK_CONSULTS.F_GET_NUMERACION(NULL,Lv_EsMatriz,Lv_EsOficinaFacturacion,NULL,Lv_CodigoNumeracion);
        LOOP
          FETCH Lrf_Numeracion INTO Lr_AdmiNumeracion;
          EXIT
        WHEN Lrf_Numeracion%notfound;
          Lv_Secuencia :=LPAD(Lr_AdmiNumeracion.SECUENCIA,9,'0');
          Lv_Numeracion:=Lr_AdmiNumeracion.NUMERACION_UNO || '-'||Lr_AdmiNumeracion.NUMERACION_DOS||'-'||Lv_Secuencia;
        END LOOP;
        CLOSE Lrf_Numeracion;

        --Actualiza Cabecera con valores de la factura
        Lr_Docum_Fin_Cab:=NULL;
        Lr_Docum_Fin_Cab.Id_Documento:=Ln_IdDoc;
        Lr_Docum_Fin_Cab.Subtotal:=Ln_ValorSubFctCab;
        Lr_Docum_Fin_Cab.Subtotal_Cero_Impuesto:=Ln_ValorSubFctCab;
        Lr_Docum_Fin_Cab.Subtotal_Con_Impuesto:=Ln_ValorImpFctCab;
        Lr_Docum_Fin_Cab.Subtotal_Descuento:=Ln_ValorDescFctCab;
        Lr_Docum_Fin_Cab.Valor_Total:=Ln_ValorTotalFctCab;
        Lr_Docum_Fin_Cab.Subtotal_Servicios:=Ln_ValorSubFctCab;
        Lr_Docum_Fin_Cab.Impuestos_Servicios:=Ln_ValorImpFctCab;
        Lr_Docum_Fin_Cab.Oficina_Id:=J.OFICINA_ID;
        Lr_Docum_Fin_Cab.Fe_Emision:=SYSDATE;
        Lr_Docum_Fin_Cab.Numero_Factura_Sri:=Lv_Numeracion;
        Lr_Docum_Fin_Cab.Es_Electronica:='S';

        Lv_Error:=NULL;
        DB_FINANCIERO.FNCK_FACTURACION.P_ACTUAL_CABECERA_FACTURACION(Lr_Docum_Fin_Cab,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_ACTUAL_CABECERA_FACTURACION:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;
 
        --Actualizamos la secuencia de numeracion SRI
        Lv_Error:=NULL;
        DB_FINANCIERO.FNCK_FACTURACION.P_ACTUAL_NUMERAC_FACTURACION(Lr_AdmiNumeracion,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_ACTUAL_NUMERAC_FACTURACION:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;

        --Actualiza Registro en Caracteristica
        Lr_Pers_Emp_Rol_Caract:=NULL;
        Lr_Pers_Emp_Rol_Caract.Persona_Empresa_Rol_Id:=J.ID_PERSONA_ROL;
        Lr_Pers_Emp_Rol_Caract.Valor:='S';
        Lr_Pers_Emp_Rol_Caract.Estado:='Eliminado';

        Lv_Error:=NULL;
        DB_FINANCIERO.FNCK_FACTURACION.P_ACTUAL_PERS_EMP_ROL_CARACT(Lr_Pers_Emp_Rol_Caract,Lv_Error);

        IF Lv_Error IS NOT NULL THEN
          Lv_Error:='P_ACTUAL_PERS_EMP_ROL_CARACT:'||Lv_Error;
          RAISE Error_Cntrl;
        END IF;

        --Encera valores obtenidos de la cabecera
        Ln_ValorSubFctCab:=0;
        Ln_ValorImpFctCab:=0;
        Ln_ValorTotalFctCab:=0;
        Lv_Numeracion:='';
        Lv_Secuencia:='';

        --Controlador de COMMIT
        Ln_Contador:=Ln_Contador+1;
        IF Ln_Contador > 5000 THEN
          COMMIT;
          Ln_Contador:=0;
        END IF;

      END IF;

    EXCEPTION
        WHEN Error_Cntrl THEN
          DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                'FNCK_FACTURACION.P_PREFACTURA_X_CLIENTE',
                                                'Error en el punto '|| J.punto_facturacion_id || ' - ' || Lv_Error,
                                                NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                                SYSDATE,
                                                NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
        WHEN OTHERS THEN
          DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                'FNCK_FACTURACION.P_PREFACTURA_X_CLIENTE',
                                                'Error en el punto '|| J.punto_facturacion_id || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                                NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                                SYSDATE,
                                                NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
    END;

    END LOOP;

    COMMIT;

  EXCEPTION
    WHEN OTHERS THEN
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                            'FNCK_FACTURACION.P_PREFACTURA_X_CLIENTE',
                                            'CODERROR' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );

  END P_PREFACTURA_X_CLIENTE;

  PROCEDURE P_PERIODO_FACTURACION(Pn_EmpresaCod             IN  NUMBER,
                                  Pd_FechaFinUltFact        IN  DATE,
                                  Pn_IdCiclo                IN  NUMBER,
                                  Pv_FechaInicioFact        OUT VARCHAR2,
                                  Pv_FechaFinFact           OUT VARCHAR2,
                                  Pn_CantidadDiasAFact      OUT NUMBER,
                                  Pn_CantidadDiasTotalMes   OUT NUMBER)
  IS

      Lv_DiaInicialPeriodo      VARCHAR2(3);
      Lv_DiaFinalPeriodo        VARCHAR2(3);
      Ln_CantDias               NUMBER;

  BEGIN

      select TO_CHAR(ac.FE_INICIO,'DD') as iniCiclo,
             TO_CHAR(ac.FE_FIN,'DD') as finCiclo
             INTO Lv_DiaInicialPeriodo, Lv_DiaFinalPeriodo
      from DB_FINANCIERO.ADMI_CICLO ac
      where ac.ID_CICLO=Pn_IdCiclo
      and ac.EMPRESA_COD=Pn_EmpresaCod;

      select TO_CHAR(Pd_FechaFinUltFact+1,'DD/MM/YYYY')
      into Pv_FechaInicioFact
      from dual;

      select to_char(to_date(Lv_DiaInicialPeriodo,'DD')-1,'DD/MM/YYYY')
      into Pv_FechaFinFact
      from dual;
      
      select (TO_DATE(Pv_FechaFinFact,'DD/MM/YYYY')+1)-TO_DATE(Pv_FechaInicioFact,'DD/MM/YYYY')
      into Ln_CantDias
      from dual;
      
      IF Ln_CantDias < 0 THEN
        Pn_CantidadDiasAFact:=0;
      ELSE
        Pn_CantidadDiasAFact:=Ln_CantDias;
      END IF;

      select TO_CHAR(LAST_DAY(sysdate),'DD')
      into Pn_CantidadDiasTotalMes
      from dual;
      
  EXCEPTION
  WHEN OTHERS THEN
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 
                                          'FNCK_FACTURACION_MENSUAL.P_PERIODO_FACTURACION', 
                                          'Error ' ||SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                 || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE, 
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'), 
                                          SYSDATE, 
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );

  END P_PERIODO_FACTURACION;

  PROCEDURE P_OBTIENE_FE_SIG_CICLO_FACT(Pn_IdCiclo       IN  NUMBER,
                                        Pd_FeAValidar    IN  DATE DEFAULT SYSDATE,
                                        Pd_FeFacturacion OUT DATE) AS
    Lv_FeInicio VARCHAR2(20);
  BEGIN
    SELECT TO_CHAR(FE_INICIO,'DD') INTO Lv_FeInicio FROM DB_FINANCIERO.ADMI_CICLO WHERE ID_CICLO = Pn_IdCiclo;
    --SE OBTIENE LA FECHA DE FACTURACIÓN DEL MES ENVIADO POR PARÁMETRO EN BASE AL CICLO_ID
    Pd_FeFacturacion := TO_DATE(Lv_FeInicio ||  '/' || TO_CHAR(Pd_FeAValidar, 'MM') || '/' || TO_CHAR(Pd_FeAValidar, 'YYYY'),'DD/MM/YYYY');
    --SE OBTIENE LA FECHA DE LA ÚLTIMA FACTURA DEL CICLO
    IF( TRUNC(Pd_FeFacturacion) > TRUNC(Pd_FeAValidar)) THEN
        --SI LA FECHA DE FACTURACIÓN DEL MES ACTUAL DEL CICLO ES MAYOR A LA FECHA A VALIDAR, LA FACTURACIÓN SE EJECUTÓ EL MES ANTERIOR
        Pd_FeFacturacion := ADD_MONTHS(Pd_FeFacturacion,-1);
    END IF;
    --SE OBTIENE EL SIGUIENTE PERÍODO A FACTURAR POR REGULARIZACIÓN DE CRS
    Pd_FeFacturacion := TRUNC(ADD_MONTHS(Pd_FeFacturacion, 1));
  EXCEPTION
    WHEN OTHERS THEN
      Pd_FeFacturacion := NULL;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           'FNCK_FACTURACION_MENSUAL.P_OBTIENE_FE_SIG_CICLO_FACT',
                                           'Error Pn_IdCiclo:' ||Pn_IdCiclo || '|Pd_FeAValidar:' || Pd_FeAValidar
                                                    || ' -ERROR- '            || SQLERRM
                                                    || ' - ERROR_STACK: '     || DBMS_UTILITY.FORMAT_ERROR_STACK
                                                    || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  END P_OBTIENE_FE_SIG_CICLO_FACT;

  PROCEDURE P_FACTURACION_ALCANCE_CRS(Pv_UsrCreacion VARCHAR2) AS

  Lv_EmpresaCod                 VARCHAR2(2) := 0;
  Lv_PrefijoEmpresa             VARCHAR2(5) := '';
  Lv_EstadoActivo               VARCHAR2(15) := 'Activo';
  Lv_EstadoInactivo             VARCHAR2(15) := 'Inactivo';
  Lv_EstadoPendiente            VARCHAR2(15) := 'Pendiente';
  Lv_ValorS                     VARCHAR2(1)  := 'S';
  Lv_ValorN                     VARCHAR2(1)  := 'N';
  Lv_NombreProcedimiento        VARCHAR2(50) := 'FNCK_FACTURACION.P_FACTURACION_ALCANCE_CRS';
  Lv_IpLocal                    VARCHAR2(15) := '127.0.0.1';
  Lv_FeActivacion               VARCHAR2(100);
  Lv_RangoConsumo               VARCHAR2(2000):='';
  Lv_Numeracion                 VARCHAR2(1000);
  Lv_Secuencia                  VARCHAR2(1000);
  Ln_DescuentoFacProDetalle     NUMBER;
  Ln_PrecioVentaFacProDetalle   NUMBER;
  Ln_ValorImpuesto              NUMBER;
  Ln_Porcentaje                 NUMBER;
  Ln_IdImpuesto                 NUMBER;
  Ln_DiasXFact                  NUMBER;
  Ln_DiasTotales                NUMBER;
  Ln_DescuentoCompensacion      NUMBER;
  Ln_IdOficina                  NUMBER;
  Lrf_AdmiParametroDet          SYS_REFCURSOR;
  Lr_AdmiParametroDet           DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE;
  Lr_InfoDocumentoFinancieroCab DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE;
  Lr_InfoDocumentoFinancieroHis DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE;
  Lr_InfoDocumentoFinancieroDet INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE;
  Lr_InfoDocumentoFinancieroImp INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE;
  Ln_PorcentajeDescuento        DB_COMERCIAL.INFO_DETALLE_SOLICITUD.PORCENTAJE_DESCUENTO%TYPE;
  Ln_IdDetalleSolicitud         DB_COMERCIAL.INFO_DETALLE_SOLICITUD.ID_DETALLE_SOLICITUD%TYPE;
  Ln_Subtotal                   INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL%TYPE;
  Ln_SubtotalConImpuesto        INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_CON_IMPUESTO%TYPE;
  Ln_SubtotalDescuento          INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_DESCUENTO%TYPE;
  Ln_ValorTotal                 INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE;
  Lv_MsnError                   VARCHAR2(5000) := NULL;
  Le_Error                      EXCEPTION;
  Ld_FechaFinalCiclo            DATE;
  Lv_EsMatriz                   VARCHAR2(1) := Lv_ValorS;
  Lv_EsOficinaFacturacion       VARCHAR2(1) := Lv_ValorS;
  Lv_CodigoNumeracion           VARCHAR2(4) := 'FACE';
  --Variables de la numeracion
  Lrf_Numeracion                FNKG_TYPES.Lrf_AdmiNumeracion;
  Lr_AdmiNumeracion             FNKG_TYPES.Lr_AdmiNumeracion;

  CURSOR C_ObtienePuntosPorCRS (Cv_PrefijoEmpresa   VARCHAR2,
                                Cn_IdCaracteristica DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE,
                                Cn_CRSCaractId      DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE,
                                Cv_EstadoPERCarac   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC.ESTADO%TYPE,
                                Cv_EstadoServCarac  DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.ESTADO%TYPE,
                                Cv_ValorServCarac   DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.VALOR%TYPE) IS
    SELECT DISTINCT
        ISE.PUNTO_FACTURACION_ID,
        IPER.OFICINA_ID,
        (SELECT TO_NUMBER(VALOR)
           FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
          WHERE PERC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
            AND PERC.CARACTERISTICA_ID = Cn_IdCaracteristica
            AND PERC.ESTADO = Cv_EstadoPERCarac) AS CICLO_ACTUAL_ID
    FROM
        DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA ISC,
        DB_COMERCIAL.INFO_SERVICIO ISE,
        DB_COMERCIAL.INFO_PUNTO IP,
        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
    WHERE   ISC.ESTADO = Cv_EstadoServCarac
        AND ISC.VALOR = Cv_ValorServCarac
        AND ISC.CARACTERISTICA_ID = Cn_CRSCaractId
        AND FE_FACTURACION = TRUNC(SYSDATE)
        AND ISC.SERVICIO_ID = ISE.ID_SERVICIO
        AND DB_FINANCIERO.FNCK_CONSULTS.F_GET_PREFIJO_BY_PUNTO(ISE.PUNTO_FACTURACION_ID,NULL) = Cv_PrefijoEmpresa
        AND ISE.PUNTO_FACTURACION_ID = IP.ID_PUNTO
        AND IP.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL;

    --Cursor que obtiene los servicios a facturar para ese punto.
    CURSOR C_ObtieneServiciosXPto (Cn_PuntoFacturacionId DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                   Cn_CaracteristicaId   DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE,
                                   Cv_EstadoServicio     DB_COMERCIAL.INFO_SERVICIO.ESTADO%TYPE,
                                   Cn_CantidadServicio   DB_COMERCIAL.INFO_SERVICIO.CANTIDAD%TYPE,
                                   Cv_EsVentaServicio    DB_COMERCIAL.INFO_SERVICIO.ES_VENTA%TYPE,
                                   Cn_PrecioVentaServ    DB_COMERCIAL.INFO_SERVICIO.PRECIO_VENTA%TYPE,
                                   Cn_FrecuenciaServ     DB_COMERCIAL.INFO_SERVICIO.FRECUENCIA_PRODUCTO%TYPE,
                                   Cv_EstadoServCarac    DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.ESTADO%TYPE,
                                   Cv_ValorServCarac     DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.VALOR%TYPE)
    IS
        SELECT DISTINCT iser.id_servicio, 
        iser.producto_id, 
        iser.plan_id, 
        iser.punto_id, 
        iser.cantidad, 
        TRUNC(iser.precio_venta,2) AS PRECIO_VENTA,
        NVL(iser.porcentaje_descuento,0) AS  porcentaje_descuento, 
        NVL(iser.valor_descuento,0) AS  valor_descuento, 
        iser.punto_facturacion_id, 
        iser.estado
        FROM DB_COMERCIAL.INFO_SERVICIO iser,
             DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA ISC
        WHERE iser.PUNTO_FACTURACION_ID = Cn_PuntoFacturacionId
        AND iser.ESTADO=Cv_EstadoServicio 
        AND iser.cantidad>Cn_CantidadServicio
        AND iser.ES_VENTA=Cv_EsVentaServicio
        AND iser.precio_venta>Cn_PrecioVentaServ
        AND iser.frecuencia_producto = Cn_FrecuenciaServ
        AND NVL(iser.PRECIO_VENTA*iser.CANTIDAD,0)>=NVL(iser.VALOR_DESCUENTO,0)
        AND ISC.SERVICIO_ID = iser.ID_SERVICIO
        AND ISC.ESTADO = Cv_EstadoServCarac
        AND ISC.VALOR = Cv_ValorServCarac
        AND CARACTERISTICA_ID = Cn_CaracteristicaId;

    CURSOR C_ObtieneCaracteristica (Cv_DescripcionCaract DB_COMERCIAL.ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA%TYPE,
                                    Cv_Tipo              DB_COMERCIAL.ADMI_CARACTERISTICA.TIPO%TYPE,
                                    Cv_Estado            DB_COMERCIAL.ADMI_CARACTERISTICA.ESTADO%TYPE) IS
        SELECT ID_CARACTERISTICA
          FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = Cv_DescripcionCaract
           AND ESTADO = Cv_Estado
           AND TIPO   = Cv_Tipo;
    Lr_ObtieneCaracteristica  C_ObtieneCaracteristica%ROWTYPE;
    Lr_ObtieneCaracteristica2 C_ObtieneCaracteristica%ROWTYPE;

    CURSOR C_ObtieneInfoServCarac (Cn_ServicioId       DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.SERVICIO_ID%TYPE,
                                   Cv_Estado           DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.ESTADO%TYPE,
                                   Cn_CaracteristicaId DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.CARACTERISTICA_ID%TYPE,
                                   Cv_Valor            DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.VALOR%TYPE,
                                   Cd_FeFacturacion    DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.FE_FACTURACION%TYPE) IS
        --COSTO DEL QUERY 3
        --Cursor que obtiene los valores de la caraterística de un servicio según su fecha de facturación y estado.
        SELECT *
          FROM DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA
         WHERE SERVICIO_ID = NVL(Cn_ServicioId, SERVICIO_ID)
           AND CARACTERISTICA_ID = Cn_CaracteristicaId
           AND ESTADO = Cv_Estado
           AND VALOR  = Cv_Valor
           AND FE_FACTURACION = NVL(TRUNC(Cd_FeFacturacion), FE_FACTURACION);

    Lr_InfoServicioCaracteristica C_ObtieneInfoServCarac%ROWTYPE;
  BEGIN

        --Se obtiene la característica por CRS para excluir los servicios.
    OPEN C_ObtieneCaracteristica('FACTURACION_CRS_CICLO_FACT', 'COMERCIAL', Lv_EstadoActivo);
    FETCH C_ObtieneCaracteristica
        INTO Lr_ObtieneCaracteristica;
    CLOSE C_ObtieneCaracteristica;

    OPEN C_ObtieneCaracteristica ('CICLO_FACTURACION', 'COMERCIAL', Lv_EstadoActivo);
    FETCH C_ObtieneCaracteristica
        INTO Lr_ObtieneCaracteristica2;
    CLOSE C_ObtieneCaracteristica;

    --Se obtiene el porcentaje IVA Activo
    SELECT PORCENTAJE_IMPUESTO INTO Ln_Porcentaje
      FROM DB_GENERAL.ADMI_IMPUESTO
     WHERE TIPO_IMPUESTO LIKE 'IVA%'
       AND ESTADO = Lv_EstadoActivo
       AND PORCENTAJE_IMPUESTO > 0
       AND ROWNUM = 1;

    Lrf_AdmiParametroDet := DB_GENERAL.GNRLPCK_UTIL.F_GET_PARAMS_DETS('CICLO_FACTURACION_EMPRESA');
    LOOP
      FETCH Lrf_AdmiParametroDet INTO Lr_AdmiParametroDet;
      EXIT WHEN Lrf_AdmiParametroDet%NOTFOUND;

      IF Lr_AdmiParametroDet.VALOR1 != Lv_ValorS THEN
        CONTINUE;
      END IF;

      Lv_PrefijoEmpresa := Lr_AdmiParametroDet.VALOR2;
      --SE Obtienen los datos de la empresa
      FNCK_FACTURACION_MENSUAL.GET_PREFIJO_OFICINA(Lr_AdmiParametroDet.EMPRESA_COD,Lv_PrefijoEmpresa,Ln_IdOficina);

      FOR Lr_PuntosAFacturar IN C_ObtienePuntosPorCRS(Cv_PrefijoEmpresa   => Lv_PrefijoEmpresa,
                                                      Cn_IdCaracteristica => Lr_ObtieneCaracteristica2.ID_CARACTERISTICA,
                                                      Cn_CRSCaractId      => Lr_ObtieneCaracteristica.ID_CARACTERISTICA,
                                                      Cv_EstadoPERCarac   => Lv_EstadoActivo,
                                                      Cv_EstadoServCarac  => Lv_EstadoActivo,
                                                      Cv_ValorServCarac   => Lv_ValorS)
      LOOP
        BEGIN

            /*Se obtiene la fecha inicial del siguiente mes a facturar para restarle un día.
              Y obtener el último día del ciclo actual*/
            P_OBTIENE_FE_SIG_CICLO_FACT(Pn_IdCiclo       => Lr_PuntosAFacturar.CICLO_ACTUAL_ID,
                                        Pd_FeAValidar    => SYSDATE,
                                        Pd_FeFacturacion => Ld_FechaFinalCiclo);
            Ld_FechaFinalCiclo := Ld_FechaFinalCiclo -1;
            Lv_RangoConsumo := TO_CHAR(SYSDATE, 'DD MONTH YYYY','NLS_DATE_LANGUAGE=SPANISH')
                                || ' AL ' ||
                               TO_CHAR(Ld_FechaFinalCiclo, 'DD MONTH YYYY','NLS_DATE_LANGUAGE=SPANISH');
            Ln_DiasXFact    := TRUNC(Ld_FechaFinalCiclo) - TRUNC(SYSDATE);
            --Se le suma el día 0, día en que se ejecuta la facturación.
            Ln_DiasXFact    := Ln_DiasXFact +1;
            Ln_DiasTotales  := TO_NUMBER(TO_CHAR(LAST_DAY(SYSDATE),'DD'));

            --SE CREA LA CABECERA DEL DOCUMENTO DEL PUNTO A FACTURAR.
            Lr_InfoDocumentoFinancieroCab                       := NULL;
            Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO          := SEQ_INFO_DOC_FINANCIERO_CAB.NEXTVAL;
            Lr_InfoDocumentoFinancieroCab.OFICINA_ID            := Lr_PuntosAFacturar.OFICINA_ID;
            Lr_InfoDocumentoFinancieroCab.PUNTO_ID              := Lr_PuntosAFacturar.PUNTO_FACTURACION_ID;
            Lr_InfoDocumentoFinancieroCab.TIPO_DOCUMENTO_ID     := 1;
            Lr_InfoDocumentoFinancieroCab.ES_AUTOMATICA         := Lv_ValorS;
            Lr_InfoDocumentoFinancieroCab.PRORRATEO             := Lv_ValorN;
            Lr_InfoDocumentoFinancieroCab.REACTIVACION          := Lv_ValorN;
            Lr_InfoDocumentoFinancieroCab.RECURRENTE            := Lv_ValorN;
            Lr_InfoDocumentoFinancieroCab.COMISIONA             := Lv_ValorS;
            Lr_InfoDocumentoFinancieroCab.FE_CREACION           := SYSDATE;
            Lr_InfoDocumentoFinancieroCab.USR_CREACION          := Pv_UsrCreacion;
            Lr_InfoDocumentoFinancieroCab.ES_ELECTRONICA        := Lv_ValorS;
            Lr_InfoDocumentoFinancieroCab.MES_CONSUMO           := NULL;
            Lr_InfoDocumentoFinancieroCab.ANIO_CONSUMO          := NULL;
            Lr_InfoDocumentoFinancieroCab.RANGO_CONSUMO         := Lv_RangoConsumo;
            Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT := Lv_EstadoPendiente;

            FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
            IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
            END IF;
            --Con la informacion de cabecera se inserta el historial
            Lr_InfoDocumentoFinancieroHis                       := NULL;
            Lr_InfoDocumentoFinancieroHis.ID_DOCUMENTO_HISTORIAL:= SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL;
            Lr_InfoDocumentoFinancieroHis.DOCUMENTO_ID          := Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
            Lr_InfoDocumentoFinancieroHis.FE_CREACION           := SYSDATE;
            Lr_InfoDocumentoFinancieroHis.USR_CREACION          := Pv_UsrCreacion;
            Lr_InfoDocumentoFinancieroHis.ESTADO                := Lv_EstadoPendiente;
            Lr_InfoDocumentoFinancieroHis.OBSERVACION           := 'Se crea la factura por alcance de Cambio de Razón Social';
            FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_HST(Lr_InfoDocumentoFinancieroHis,Lv_MsnError);
            IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
            END IF;

            FOR Lr_Servicios IN C_ObtieneServiciosXPto (Lr_PuntosAFacturar.PUNTO_FACTURACION_ID,
                                                        Lr_ObtieneCaracteristica.ID_CARACTERISTICA,
                                                        Lv_EstadoActivo,
                                                        0,
                                                        Lv_ValorS,
                                                        0,
                                                        1,
                                                        Lv_EstadoActivo,
                                                        Lv_ValorS)
            LOOP

                OPEN C_ObtieneInfoServCarac(Lr_Servicios.ID_SERVICIO, Lv_EstadoActivo, Lr_ObtieneCaracteristica.ID_CARACTERISTICA, Lv_ValorS, TRUNC(SYSDATE));
                FETCH C_ObtieneInfoServCarac INTO Lr_InfoServicioCaracteristica;
                --Se actualiza la caracteristica del servicio a procesado.
                Lr_InfoServicioCaracteristica.ESTADO      := Lv_EstadoInactivo;
                Lr_InfoServicioCaracteristica.VALOR       := Lv_ValorN;
                Lr_InfoServicioCaracteristica.USR_ULT_MOD := Pv_UsrCreacion;
                Lr_InfoServicioCaracteristica.IP_ULT_MOD  := Lv_IpLocal;
                Lr_InfoServicioCaracteristica.OBSERVACION := SUBSTR('Se procesa la característica en el proceso de alcance de CRS: '
                                                             || Lr_InfoServicioCaracteristica.OBSERVACION,0,500);
                P_UPDATE_AT_SERV_CARAC (Lr_InfoServicioCaracteristica, Lv_MsnError);
                CLOSE C_ObtieneInfoServCarac;
                IF Lv_MsnError IS NOT NULL THEN
                    RAISE Le_Error;
                END IF;
                --Con los servicios verifico si posee descuento unico
                Ln_DescuentoFacProDetalle:=0; 
                --Con los valores obtenidos procedo hacer los calculos para cada servicio
                Ln_PrecioVentaFacProDetalle:=0;
                Ln_PrecioVentaFacProDetalle:=ROUND((Lr_Servicios.cantidad * Lr_Servicios.precio_venta),2);
                --SE CALCULA EL PROPORCIONAL
                Ln_PrecioVentaFacProDetalle:= ROUND(Ln_DiasXFact * Ln_PrecioVentaFacProDetalle/Ln_DiasTotales, 2);

                FNCK_FACTURACION_MENSUAL.GET_SOL_DESCT_UNICO(Lr_Servicios.id_servicio,Ln_IdDetalleSolicitud,Ln_PorcentajeDescuento);
                --Si posee porcentaje de descuento, realizo los calculos
                --Debo actualizar la solicitud
                IF Ln_PorcentajeDescuento IS NOT NULL AND Ln_PorcentajeDescuento>0 THEN
                  FNCK_FACTURACION_MENSUAL.UPD_SOL_DESCT_UNICO(Ln_IdDetalleSolicitud);
                  Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle *Ln_PorcentajeDescuento)/100,2);
                --Verifico si posee descuento fijo por porcentaje o valor; ya que este es el mandatorio  
                ELSIF Lr_Servicios.porcentaje_descuento>0 THEN
                  Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle*Lr_Servicios.porcentaje_descuento)/100,2);
                ELSIF Lr_Servicios.valor_descuento  >0 THEN
                  Ln_DescuentoFacProDetalle := ROUND(Lr_Servicios.valor_descuento,2); 
                ELSE  
                  FNCK_FACTURACION_MENSUAL.GET_SOL_DESCT_PROMOCIONAL(Lr_Servicios.id_servicio,Ln_IdDetalleSolicitud,Ln_PorcentajeDescuento); 
                  IF Ln_PorcentajeDescuento IS NOT NULL AND Ln_PorcentajeDescuento>0 THEN
                    FNCK_FACTURACION_MENSUAL.UPD_SOL_DESCT_UNICO(Ln_IdDetalleSolicitud);
                    Ln_DescuentoFacProDetalle:=ROUND((Ln_PrecioVentaFacProDetalle*Ln_PorcentajeDescuento)/100,2);
                  END IF;
                END IF;

                --SE INSERTA EL DETALLE DEL DOCUMENTO
                --Calcula el valor del impuesto correspondiente al detalle
                Ln_ValorImpuesto := 0;
                Ln_ValorImpuesto := ((Ln_PrecioVentaFacProDetalle-Ln_DescuentoFacProDetalle)*Ln_Porcentaje/100);

                --Con el precio de venta nuevo procedemos a ingresar los valores del detalle
                Lr_InfoDocumentoFinancieroDet                            :=NULL;
                Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE             :=SEQ_INFO_DOC_FINANCIERO_DET.NEXTVAL;
                Lr_InfoDocumentoFinancieroDet.DOCUMENTO_ID               :=Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
                Lr_InfoDocumentoFinancieroDet.PUNTO_ID                   :=Lr_Servicios.punto_id;
                Lr_InfoDocumentoFinancieroDet.PLAN_ID                    :=Lr_Servicios.plan_id;
                Lr_InfoDocumentoFinancieroDet.CANTIDAD                   :=Lr_Servicios.cantidad;
                Lr_InfoDocumentoFinancieroDet.PRECIO_VENTA_FACPRO_DETALLE:=ROUND(Ln_PrecioVentaFacProDetalle,2);
                Lr_InfoDocumentoFinancieroDet.PORCETANJE_DESCUENTO_FACPRO:=Ln_PorcentajeDescuento;
                Lr_InfoDocumentoFinancieroDet.DESCUENTO_FACPRO_DETALLE   :=Ln_DescuentoFacProDetalle;
                Lr_InfoDocumentoFinancieroDet.VALOR_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
                Lr_InfoDocumentoFinancieroDet.COSTO_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
                Lr_InfoDocumentoFinancieroDet.FE_CREACION                :=SYSDATE;
                Lr_InfoDocumentoFinancieroDet.USR_CREACION               :=Pv_UsrCreacion;
                Lr_InfoDocumentoFinancieroDet.PRODUCTO_ID                :=Lr_Servicios.producto_id;
                Lr_InfoDocumentoFinancieroDet.SERVICIO_ID                :=Lr_Servicios.id_servicio;
                --Obtengo la Fe_activacion del servicio
                Lv_FeActivacion                                              :=FNCK_FACTURACION_MENSUAL.GET_FECHA_ACTIVACION(Lr_Servicios.id_servicio);
                Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE  :=TRIM('Consumo: '||Lv_RangoConsumo);  
                IF Lv_FeActivacion                                           IS NOT NULL THEN
                  Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE:=TRIM(Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE 
                                                                                    || ', Fecha de Activacion: '|| Lv_FeActivacion);
                END IF;

                --SI EL DETALLE ES MAYOR A 0, SE INSERTA. CASO CONTRARIO CONTINÚA ITERANDO
                IF Ln_PrecioVentaFacProDetalle > 0 THEN
                    FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_DET(Lr_InfoDocumentoFinancieroDet,Lv_MsnError);
                ELSE
                    CONTINUE;
                END IF;
                IF Lv_MsnError IS NOT NULL THEN
                    RAISE Le_Error;
                END IF;

                --Con los valores de detalle insertado, podemos ingresar el impuesto
                Lr_InfoDocumentoFinancieroImp               :=NULL;
                Lr_InfoDocumentoFinancieroImp.ID_DOC_IMP    :=SEQ_INFO_DOC_FINANCIERO_IMP.NEXTVAL;
                Lr_InfoDocumentoFinancieroImp.DETALLE_DOC_ID:=Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE;

                --Modificar funcion del impuesto
                --Debemos obtener el impuesto en base al porcentaje enviado en el arreglo
                Ln_IdImpuesto                               := FNCK_FACTURACION_MENSUAL.F_CODIGO_IMPUESTO_X_PORCEN(Ln_Porcentaje);
                --
                Lr_InfoDocumentoFinancieroImp.IMPUESTO_ID   :=Ln_IdImpuesto;
                Lr_InfoDocumentoFinancieroImp.VALOR_IMPUESTO:=Ln_ValorImpuesto;
                Lr_InfoDocumentoFinancieroImp.PORCENTAJE    :=Ln_Porcentaje;
                Lr_InfoDocumentoFinancieroImp.FE_CREACION   :=SYSDATE;
                Lr_InfoDocumentoFinancieroImp.USR_CREACION  :=Pv_UsrCreacion;
                FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_IMP(Lr_InfoDocumentoFinancieroImp,Lv_MsnError);
                IF Lv_MsnError IS NOT NULL THEN
                    RAISE Le_Error;
                END IF;
            END LOOP;

            --Se debe obtener las sumatorias de los Subtotales y se actualiza las cabeceras
            Ln_Subtotal              := 0;
            Ln_SubtotalDescuento     := 0;
            Ln_SubtotalConImpuesto   := 0;
            Ln_ValorTotal            := 0;
            Ln_DescuentoCompensacion := 0;

            Ln_Subtotal            := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_SUBTOTAL(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
            Ln_SubtotalDescuento   := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_DESCUENTO(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
            Ln_SubtotalConImpuesto := ROUND( NVL(FNCK_FACTURACION_MENSUAL.P_SUMAR_IMPUESTOS(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);

            Ln_ValorTotal          := NVL( NVL(Ln_Subtotal, 0) - NVL(Ln_SubtotalDescuento, 2) - NVL(Ln_DescuentoCompensacion, 0) + NVL(Ln_SubtotalConImpuesto, 0), 0);

            --Actualizo los valores
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL               := Ln_Subtotal;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CERO_IMPUESTO := Ln_Subtotal;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CON_IMPUESTO  := Ln_SubtotalConImpuesto;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_DESCUENTO     := Ln_SubtotalDescuento;
            Lr_InfoDocumentoFinancieroCab.DESCUENTO_COMPENSACION := Ln_DescuentoCompensacion;
            Lr_InfoDocumentoFinancieroCab.VALOR_TOTAL            := Ln_ValorTotal;

            --Actualizo la numeracion y el estado
            IF Ln_ValorTotal >0 THEN
              Lrf_Numeracion:=FNCK_CONSULTS.F_GET_NUMERACION(Lv_PrefijoEmpresa,Lv_EsMatriz,Lv_EsOficinaFacturacion,Ln_IdOficina,Lv_CodigoNumeracion);
              --Debo recorrer la numeracion obtenida
              LOOP
                FETCH Lrf_Numeracion INTO Lr_AdmiNumeracion;
                EXIT
              WHEN Lrf_Numeracion%notfound;
                Lv_Secuencia :=LPAD(Lr_AdmiNumeracion.SECUENCIA,9,'0');
                Lv_Numeracion:=Lr_AdmiNumeracion.NUMERACION_UNO || '-'||Lr_AdmiNumeracion.NUMERACION_DOS||'-'||Lv_Secuencia;
              END LOOP;
              --Cierro la numeracion
              CLOSE Lrf_Numeracion;

              Lr_InfoDocumentoFinancieroCab.NUMERO_FACTURA_SRI   :=Lv_Numeracion;
              Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT:=Lv_EstadoPendiente;
              Lr_InfoDocumentoFinancieroCab.FE_EMISION           :=TRUNC(SYSDATE);

              --Actualizo los valores de la cabecera
              FNCK_TRANSACTION.UPDATE_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO,Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
              IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
              END IF;

              --Incremento la numeracion
              Lr_AdmiNumeracion.SECUENCIA:=Lr_AdmiNumeracion.SECUENCIA+1;
              FNCK_TRANSACTION.UPDATE_ADMI_NUMERACION(Lr_AdmiNumeracion.ID_NUMERACION,Lr_AdmiNumeracion,Lv_MsnError);
              IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
              END IF;
            ELSE
                Lv_MsnError := 'La factura no tiene valores correctos PUNTO_FACTURACION_ID:' || Lr_InfoDocumentoFinancieroCab.PUNTO_ID;
                RAISE Le_Error;
            END IF;
            COMMIT;
        EXCEPTION
          WHEN Le_Error THEN
            ROLLBACK;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error: ' || Lv_MsnError,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
          WHEN OTHERS THEN
            ROLLBACK;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                 Lv_NombreProcedimiento,
                                                 'Error ' || SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                 || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || 
                                                 DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                                 NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                                 SYSDATE,
                                                 NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
        END;
      END LOOP;
    END LOOP;
    --END LOOP Lrf_AdmiParametroDet
    CLOSE Lrf_AdmiParametroDet;

    --SE ACTUALIZAN LAS CARACTERISTICAS QUE NO CUMPLIERON LAS CONDICIONES, PERO ESTABAN PLANIFICADAS PARA ESTA FECHA
    FOR Lr_Filas IN C_ObtieneInfoServCarac(NULL, Lv_EstadoActivo, Lr_ObtieneCaracteristica.ID_CARACTERISTICA, Lv_ValorS, TRUNC(SYSDATE))
    LOOP
        BEGIN
            Lr_Filas.ESTADO      := Lv_EstadoInactivo;
            Lr_Filas.VALOR       := Lv_ValorN;
            Lr_Filas.USR_ULT_MOD := Pv_UsrCreacion;
            Lr_Filas.IP_ULT_MOD  := Lv_IpLocal;
            Lr_Filas.OBSERVACION := SUBSTR('La característica no cumple con las validaciones para ser procesada: '
                                 || Lr_Filas.OBSERVACION,0,500);
            P_UPDATE_AT_SERV_CARAC (Lr_Filas, Lv_MsnError);
            IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
            END IF;
        EXCEPTION
            WHEN Le_Error THEN
                DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error: ' || Lv_MsnError,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
            WHEN OTHERS THEN
              DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                   Lv_NombreProcedimiento,
                                                   'Error ' || SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                   || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || 
                                                   DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                                   NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                                   SYSDATE,
                                                   NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
        END;
    END LOOP;
  EXCEPTION
    WHEN OTHERS THEN
      ROLLBACK;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error ' || SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                    || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
  END P_FACTURACION_ALCANCE_CRS;


  PROCEDURE P_FACTURACION_UNICA(Pv_UsrCreacion     IN VARCHAR2,
                                Pv_PrefijoEmpresa  IN VARCHAR2,
                                Pv_EmpresaCod      IN VARCHAR2) AS

    Lv_EmpresaCod                 VARCHAR2(2)   := Pv_EmpresaCod;
    Lv_PrefijoEmpresa             VARCHAR2(5)   := Pv_PrefijoEmpresa;
    Lv_EstadoActivo               VARCHAR2(15)  := 'Activo';
    Lv_EstadoInactivo             VARCHAR2(15)  := 'Inactivo';
    Lv_EstadoPendiente            VARCHAR2(15)  := 'Pendiente';
    Lv_ValorS                     VARCHAR2(1)   := 'S';
    Lv_ValorN                     VARCHAR2(1)   := 'N';
    Lv_NombreProcedimiento        VARCHAR2(50)  := 'FNCK_FACTURACION.P_FACTURACION_UNICA';
    Lv_IpLocal                    VARCHAR2(15)  := '127.0.0.1';
    Lv_FeActivacion               VARCHAR2(100);
    Lv_RangoConsumo               VARCHAR2(2000):= '';
    Lv_Numeracion                 VARCHAR2(1000):= '';
    Lv_Secuencia                  VARCHAR2(1000);
    Lv_ParamCaractNoIncluir       VARCHAR2(1000):= 'CARACTERISTICAS_NO_INCLUIDAS';
    Ln_Contador                   NUMBER:=0;
    Ln_DescuentoFacProDetalle     NUMBER;
    Ln_PrecioVentaFacProDetalle   NUMBER;
    Ln_PrecioAdicional            NUMBER;
    Ln_ValorImpuesto              NUMBER;
    Ln_BanderaImpuesto            DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO.PORCENTAJE_IMPUESTO%TYPE;
    Ln_ValorImpuestoAdicional     NUMBER;
    Ln_BanderaImpuestoAdicional   NUMBER;
    Ln_Porcentaje                 NUMBER;
    Pn_IdDocDetalle		          INFO_DOCUMENTO_FINANCIERO_DET.ID_DOC_DETALLE%TYPE;
    Ln_PorcentajeImpAdicional	  NUMBER;
    Ln_IdImpuestoImpAdicional	  NUMBER;
    Ln_IdImpuesto                 NUMBER;
    Ln_DescuentoCompensacion      NUMBER;
    Ln_IdOficina                  NUMBER;
    Lb_ObtenerCaracAdicional      BOOLEAN;
    Lb_ObtenerCantAdicional       BOOLEAN;
    Lr_AdmiParametroDet           DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE;
    Lr_InfoDocumentoFinancieroCab DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE;
    Lr_InfoDocumentoFinancieroHis DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE;
    Lr_InfoDocumentoFinancieroDet INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE;
    Lr_InfoDocumentoFinancieroImp INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE;
    Ln_PorcentajeDescuento        DB_COMERCIAL.INFO_DETALLE_SOLICITUD.PORCENTAJE_DESCUENTO%TYPE;
    Ln_IdDetalleSolicitud         DB_COMERCIAL.INFO_DETALLE_SOLICITUD.ID_DETALLE_SOLICITUD%TYPE;
    Ln_Subtotal                   INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL%TYPE;
    Ln_SubtotalConImpuesto        INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_CON_IMPUESTO%TYPE;
    Ln_SubtotalDescuento          INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_DESCUENTO%TYPE;
    Ln_ValorTotal                 INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE;
    Lv_MsnError                   VARCHAR2(5000) := NULL;
    Le_Error                      EXCEPTION;
    Lv_EsMatriz                   VARCHAR2(1) := Lv_ValorS;
    Lv_EsOficinaFacturacion       VARCHAR2(1) := Lv_ValorS;
    Lv_CodigoNumeracion           VARCHAR2(4) := 'FACE';
    --Variables de la numeración
    Lrf_Numeracion                FNKG_TYPES.Lrf_AdmiNumeracion;
    Lr_AdmiNumeracion             FNKG_TYPES.Lr_AdmiNumeracion;

    --Cursor que obtiene los puntos a facturar.
    --Costo Query: 13210
    CURSOR C_ObtienePuntos (Cv_PrefijoEmpresa       VARCHAR2,
                            Cv_EstadoServicio       DB_COMERCIAL.INFO_SERVICIO.ESTADO%TYPE,
                            Cv_EstadoHistorial      DB_COMERCIAL.INFO_SERVICIO_HISTORIAL.ESTADO%TYPE,
                            Cn_CantidadServicio     DB_COMERCIAL.INFO_SERVICIO.CANTIDAD%TYPE,
                            Cv_EsVentaServicio      DB_COMERCIAL.INFO_SERVICIO.ES_VENTA%TYPE,
                            Cn_PrecioVentaServ      DB_COMERCIAL.INFO_SERVICIO.PRECIO_VENTA%TYPE,
                            Cn_FrecuenciaServ       DB_COMERCIAL.INFO_SERVICIO.FRECUENCIA_PRODUCTO%TYPE,
                            Cn_IdCaracteristica     DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE,
                            Cv_ValorS               DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT.VALOR%TYPE) 
    IS
      SELECT DISTINCT
        ISE.PUNTO_FACTURACION_ID,
        IPER.OFICINA_ID,
        PERS.PAGA_IVA
      FROM
        DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH,
        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA AC,
        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
        DB_COMERCIAL.INFO_SERVICIO ISE,
        DB_COMERCIAL.INFO_PUNTO IP,
        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
        DB_COMERCIAL.INFO_PERSONA PERS
      WHERE AC.CARACTERISTICA_ID         = Cn_IdCaracteristica
      AND AC.ID_PRODUCTO_CARACTERISITICA = ISPC.PRODUCTO_CARACTERISITICA_ID
      AND ISPC.SERVICIO_ID               = ISE.ID_SERVICIO
      AND ISPC.VALOR                     = Cv_ValorS
      AND ISE.ESTADO                     = Cv_EstadoServicio
      AND ISE.ID_SERVICIO                = ISH.SERVICIO_ID
      AND ISH.ESTADO                     = Cv_EstadoHistorial
      AND ISH.FE_CREACION                between TRUNC(SYSDATE) and TO_TIMESTAMP(TO_CHAR(SYSDATE, 'YYYY-MM-DD')||' 23:59:59','RRRR/MM/DD HH24:MI:SS')
      AND ISE.cantidad                   > Cn_CantidadServicio
      AND ISE.ES_VENTA                   = Cv_EsVentaServicio
      AND ISE.precio_venta               > Cn_PrecioVentaServ
      AND ISE.frecuencia_producto        = Cn_FrecuenciaServ
      AND DB_FINANCIERO.FNCK_CONSULTS.F_GET_PREFIJO_BY_PUNTO(ISE.PUNTO_FACTURACION_ID,NULL) = Cv_PrefijoEmpresa
      AND ISE.PUNTO_FACTURACION_ID       = IP.ID_PUNTO
      AND IP.PERSONA_EMPRESA_ROL_ID      = IPER.ID_PERSONA_ROL
      AND PERS.ID_PERSONA                = IPER.PERSONA_ID
      AND NOT EXISTS (SELECT    FAC.ID_DOCUMENTO 
                        FROM    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB FAC
                        JOIN    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DET  ON DET.DOCUMENTO_ID  = FAC.ID_DOCUMENTO
                        WHERE   FAC.USR_CREACION = 'telcos_fact_unica'
                        AND     DET.SERVICIO_ID  = ISH.SERVICIO_ID
                        AND     FAC.ESTADO_IMPRESION_FACT IN (
                                                                SELECT APD.DESCRIPCION
                                                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
                                                                JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC
                                                                ON APD.PARAMETRO_ID        = APC.ID_PARAMETRO
                                                                WHERE APC.NOMBRE_PARAMETRO = 'ESTADOS_FACT_UNICA_VALIDAS'
                                                                AND APC.ESTADO             = 'Activo'
                                                                AND APD.ESTADO             = 'Activo'
                                                              ));

      --Cursor que obtiene los servicios a facturar para ese punto.
      --Costo Query: 8 
      CURSOR C_ObtieneServiciosXPto (Cn_PuntoFacturacionId DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                     Cv_EstadoServicio     DB_COMERCIAL.INFO_SERVICIO.ESTADO%TYPE,
                                     Cn_CantidadServicio   DB_COMERCIAL.INFO_SERVICIO.CANTIDAD%TYPE,
                                     Cv_EsVentaServicio    DB_COMERCIAL.INFO_SERVICIO.ES_VENTA%TYPE,
                                     Cn_PrecioVentaServ    DB_COMERCIAL.INFO_SERVICIO.PRECIO_VENTA%TYPE,
                                     Cn_FrecuenciaServ     DB_COMERCIAL.INFO_SERVICIO.FRECUENCIA_PRODUCTO%TYPE,
                                     Cv_EstadoServCarac    DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.ESTADO%TYPE,
                                     Cv_ValorS             DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT.VALOR%TYPE,
                                     Cv_ParamNoIncluye     VARCHAR2)
      IS
        SELECT DISTINCT ISER.id_servicio, 
          ISER.producto_id, 
          ISER.plan_id, 
          ISER.punto_id, 
          ISER.cantidad, 
          TRUNC(ISER.precio_venta,2) AS PRECIO_VENTA,
          NVL(ISER.porcentaje_descuento,0) AS  porcentaje_descuento, 
          NVL(ISER.valor_descuento,0) AS  valor_descuento, 
          ISER.punto_facturacion_id, 
          ISER.estado,
          AP.descripcion_producto
          FROM DB_COMERCIAL.INFO_SERVICIO ISER,
            DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH,
            DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA AC,
            DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
            DB_COMERCIAL.ADMI_PRODUCTO AP
          WHERE ISER.PUNTO_FACTURACION_ID            = Cn_PuntoFacturacionId
          AND ISER.ESTADO                            = Cv_EstadoServicio 
          AND ISER.cantidad                          > Cn_CantidadServicio
          AND ISER.ES_VENTA                          = Cv_EsVentaServicio
          AND ISER.precio_venta                      > Cn_PrecioVentaServ
          AND ISER.frecuencia_producto               = Cn_FrecuenciaServ
          AND NVL(ISER.PRECIO_VENTA*ISER.CANTIDAD,0) >= NVL(ISER.VALOR_DESCUENTO,0)
          AND AC.ID_PRODUCTO_CARACTERISITICA         = ISPC.PRODUCTO_CARACTERISITICA_ID
          AND ISPC.SERVICIO_ID                       = ISER.ID_SERVICIO
          AND ISER.ID_SERVICIO                       = ISH.SERVICIO_ID
          AND ISH.ESTADO                             = Cv_EstadoServicio
          AND ISH.FE_CREACION between TRUNC(SYSDATE) and TO_TIMESTAMP(TO_CHAR(SYSDATE, 'YYYY-MM-DD')||' 23:59:59','RRRR/MM/DD HH24:MI:SS')
          AND ISPC.VALOR                             = Cv_ValorS
          AND AP.ID_PRODUCTO                         = ISER.PRODUCTO_ID
          AND ISPC.ESTADO                            = Cv_EstadoServCarac
          AND ISER.ID_SERVICIO                       not in (SELECT DISTINCT ISER.id_servicio
                                                               FROM DB_COMERCIAL.INFO_SERVICIO ISER,
                                                                 DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                                                                 DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
                                                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC
                                                               where ISER.PUNTO_FACTURACION_ID = Cn_PuntoFacturacionId
                                                               AND APC.ID_PRODUCTO_CARACTERISITICA = ISPC.PRODUCTO_CARACTERISITICA_ID
                                                               AND AC.ID_CARACTERISTICA = APC.CARACTERISTICA_ID
                                                               AND ISPC.SERVICIO_ID = ISER.ID_SERVICIO
                                                               AND AC.DESCRIPCION_CARACTERISTICA in (SELECT APD.valor1
                                                                                                       FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC,
                                                                                                         DB_GENERAL.ADMI_PARAMETRO_DET APD
                                                                                                       WHERE APC.ID_PARAMETRO   = APD.PARAMETRO_ID
                                                                                                       AND APC.ESTADO           = Cv_EstadoServCarac 
                                                                                                       AND APD.ESTADO           = Cv_EstadoServCarac 
                                                                                                       AND APC.NOMBRE_PARAMETRO = Cv_ParamNoIncluye))

        AND NOT EXISTS (SELECT  FAC.ID_DOCUMENTO 
                        FROM    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB FAC
                        JOIN    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DET  ON DET.DOCUMENTO_ID  = FAC.ID_DOCUMENTO
                        WHERE   FAC.USR_CREACION = 'telcos_fact_unica'
                        AND     DET.SERVICIO_ID  = ISER.ID_SERVICIO
                        AND     FAC.ESTADO_IMPRESION_FACT IN (
                                                                SELECT APD.DESCRIPCION
                                                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
                                                                JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC
                                                                ON APD.PARAMETRO_ID        = APC.ID_PARAMETRO
                                                                WHERE APC.NOMBRE_PARAMETRO = 'ESTADOS_FACT_UNICA_VALIDAS'
                                                                AND APC.ESTADO             = 'Activo'
                                                                AND APD.ESTADO             = 'Activo'
                                                              ));

      --Cursor que obtiene la caracteristica asociada.
      --Costo Query: 2 
      CURSOR C_ObtieneCaracteristica (Cv_DescripcionCaract DB_COMERCIAL.ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA%TYPE,
                                      Cv_Tipo              DB_COMERCIAL.ADMI_CARACTERISTICA.TIPO%TYPE,
                                      Cv_Estado            DB_COMERCIAL.ADMI_CARACTERISTICA.ESTADO%TYPE)
      IS
        SELECT ID_CARACTERISTICA
          FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = Cv_DescripcionCaract
          AND ESTADO                       = Cv_Estado
          AND TIPO                         = Cv_Tipo;

      Lr_ObtieneCaracteristica  C_ObtieneCaracteristica%ROWTYPE;
      Lr_ObtieneCaracteristica2 C_ObtieneCaracteristica%ROWTYPE;

      --Cursor que obtiene la cantidad a facturar adicional.
      --Costo Query: 5 
      CURSOR C_ObtieneCantAdicional (Cn_ServicioId         DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.SERVICIO_ID%TYPE,
                                     Cn_CaracteristicaId   DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.CARACTERISTICA_ID%TYPE)
      IS
        SELECT ISPC.VALOR,
          AC.ID_PRODUCTO_CARACTERISITICA
        FROM DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA ISC,
          DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA AC
        WHERE TO_NUMBER(ISC.VALOR)         = ISPC.ID_SERVICIO_PROD_CARACT 
        AND AC.ID_PRODUCTO_CARACTERISITICA = ISPC.PRODUCTO_CARACTERISITICA_ID
        AND ISC.SERVICIO_ID                = Cn_ServicioId
        AND ISC.CARACTERISTICA_ID          = Cn_CaracteristicaId;

      Lr_ObtieneCantAdicional  C_ObtieneCantAdicional%ROWTYPE;

      --Cursor que obtiene el valor del adicional asociado.
      --Costo Query: 5
      CURSOR C_ObtieneValorAdicional (Cv_NombreParametro  DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                                      Cv_Valor1           DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE)
      IS
        SELECT APD.VALOR4,
          APD.VALOR5 
        FROM DB_GENERAL.ADMI_PARAMETRO_DET APD,
          DB_GENERAL.ADMI_PARAMETRO_CAB APC
        WHERE APD.PARAMETRO_ID   = APC.ID_PARAMETRO
        AND APC.NOMBRE_PARAMETRO = Cv_NombreParametro
        AND APD.VALOR1           = Cv_Valor1;

      Lr_ObtieneValorAdicional  C_ObtieneValorAdicional%ROWTYPE;
  
      --Cursor que obtiene la caracteristica de Facturable adicional.
      --Costo Query: 14
      CURSOR C_ObtieneCaracAdicional (Cn_ServicioId         DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT.SERVICIO_ID%TYPE,
                                      Cn_CaracteristicaId   DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE)
      IS
        SELECT AC.ID_CARACTERISTICA,
          AC.DESCRIPCION_CARACTERISTICA 
        FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
          DB_COMERCIAL.ADMI_CARACTERISTICA AC
        WHERE APC.ID_PRODUCTO_CARACTERISITICA = ISPC.PRODUCTO_CARACTERISITICA_ID
        AND AC.ID_CARACTERISTICA              = APC.CARACTERISTICA_ID
        AND ISPC.SERVICIO_ID                  = Cn_ServicioId
        AND AC.ID_CARACTERISTICA              = Cn_CaracteristicaId ;

      Lr_ObtieneCaracAdicional  C_ObtieneCaracAdicional%ROWTYPE;

    BEGIN

      OPEN C_ObtieneCaracteristica('FACTURACION_UNICA', 'COMERCIAL', Lv_EstadoActivo);
      FETCH C_ObtieneCaracteristica
        INTO Lr_ObtieneCaracteristica;
      CLOSE C_ObtieneCaracteristica;

      OPEN C_ObtieneCaracteristica ('FACTURABLE_ADICIONAL', 'COMERCIAL', Lv_EstadoActivo);
      FETCH C_ObtieneCaracteristica
        INTO Lr_ObtieneCaracteristica2;
      CLOSE C_ObtieneCaracteristica;

      --Se obtiene el porcentaje IVA Activo
      SELECT PORCENTAJE_IMPUESTO INTO Ln_Porcentaje
        FROM DB_GENERAL.ADMI_IMPUESTO
      WHERE TIPO_IMPUESTO LIKE 'IVA%'
      AND ESTADO              = Lv_EstadoActivo
      AND PORCENTAJE_IMPUESTO > 0
      AND ROWNUM              = 1;
 

      FNCK_FACTURACION_MENSUAL.GET_PREFIJO_OFICINA(Lv_EmpresaCod,Lv_PrefijoEmpresa,Ln_IdOficina);

      FOR Lr_PuntosAFacturar IN C_ObtienePuntos(Lv_PrefijoEmpresa,
                                                Lv_EstadoActivo,
                                                Lv_EstadoActivo,
                                                0,
                                                Lv_ValorS,
                                                0,
                                                0,
                                                Lr_ObtieneCaracteristica.ID_CARACTERISTICA,
                                                Lv_ValorS)
      LOOP

        BEGIN

          --SE CREA LA CABECERA DEL DOCUMENTO DEL PUNTO A FACTURAR.
          Lr_InfoDocumentoFinancieroCab                       := NULL;
          Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO          := SEQ_INFO_DOC_FINANCIERO_CAB.NEXTVAL;
          Lr_InfoDocumentoFinancieroCab.OFICINA_ID            := Lr_PuntosAFacturar.OFICINA_ID;
          Lr_InfoDocumentoFinancieroCab.PUNTO_ID              := Lr_PuntosAFacturar.PUNTO_FACTURACION_ID;
          Lr_InfoDocumentoFinancieroCab.TIPO_DOCUMENTO_ID     := 1;
          Lr_InfoDocumentoFinancieroCab.ES_AUTOMATICA         := Lv_ValorS;
          Lr_InfoDocumentoFinancieroCab.PRORRATEO             := Lv_ValorN;
          Lr_InfoDocumentoFinancieroCab.REACTIVACION          := Lv_ValorN;
          Lr_InfoDocumentoFinancieroCab.RECURRENTE            := Lv_ValorN;
          Lr_InfoDocumentoFinancieroCab.COMISIONA             := Lv_ValorS;
          Lr_InfoDocumentoFinancieroCab.FE_CREACION           := SYSDATE;
          Lr_InfoDocumentoFinancieroCab.USR_CREACION          := Pv_UsrCreacion;
          Lr_InfoDocumentoFinancieroCab.ES_ELECTRONICA        := Lv_ValorS;
          Lr_InfoDocumentoFinancieroCab.MES_CONSUMO           := TO_CHAR(SYSDATE,'MM');
          Lr_InfoDocumentoFinancieroCab.ANIO_CONSUMO          := TO_CHAR(SYSDATE,'YYYY');
          Lr_InfoDocumentoFinancieroCab.RANGO_CONSUMO         := Lv_RangoConsumo;
          Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT := Lv_EstadoPendiente;
          
          FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
          IF Lv_MsnError IS NOT NULL THEN
            RAISE Le_Error;
          END IF;

          --Con la informacion de cabecera se inserta el historial
          Lr_InfoDocumentoFinancieroHis                       := NULL;
          Lr_InfoDocumentoFinancieroHis.ID_DOCUMENTO_HISTORIAL:= SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL;
          Lr_InfoDocumentoFinancieroHis.DOCUMENTO_ID          := Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
          Lr_InfoDocumentoFinancieroHis.FE_CREACION           := SYSDATE;
          Lr_InfoDocumentoFinancieroHis.USR_CREACION          := Pv_UsrCreacion;
          Lr_InfoDocumentoFinancieroHis.ESTADO                := Lv_EstadoPendiente;
          Lr_InfoDocumentoFinancieroHis.OBSERVACION           := 'Se crea la factura por servicio';
          FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_HST(Lr_InfoDocumentoFinancieroHis,Lv_MsnError);
          IF Lv_MsnError IS NOT NULL THEN
            RAISE Le_Error;
          END IF;

          FOR Lr_Servicios IN C_ObtieneServiciosXPto (Lr_PuntosAFacturar.PUNTO_FACTURACION_ID,
                                                      Lv_EstadoActivo,
                                                      0,
                                                      Lv_ValorS,
                                                      0,
                                                      0,
                                                      Lv_EstadoActivo,
                                                      Lv_ValorS,
                                                      Lv_ParamCaractNoIncluir)
          LOOP
 
            --Con los valores obtenidos procedo hacer los calculos para cada servicio

            OPEN C_ObtieneCaracAdicional(Lr_Servicios.ID_SERVICIO,Lr_ObtieneCaracteristica2.ID_CARACTERISTICA);
              FETCH C_ObtieneCaracAdicional INTO Lr_ObtieneCaracAdicional;
              Lb_ObtenerCaracAdicional:=C_ObtieneCaracAdicional%NOTFOUND;
            CLOSE C_ObtieneCaracAdicional;

            IF Lb_ObtenerCaracAdicional THEN
              Ln_PrecioVentaFacProDetalle:=0;
              Ln_PrecioVentaFacProDetalle:=ROUND(Lr_Servicios.precio_venta,2);
            ELSE
              OPEN C_ObtieneCantAdicional(Lr_Servicios.ID_SERVICIO,Lr_ObtieneCaracteristica2.ID_CARACTERISTICA);
                FETCH C_ObtieneCantAdicional INTO Lr_ObtieneCantAdicional;
                Lb_ObtenerCantAdicional:=C_ObtieneCantAdicional%NOTFOUND;
              CLOSE C_ObtieneCantAdicional;
             
              --SE CALCULA EL VALOR ADICIONAL
              IF Lb_ObtenerCantAdicional THEN
                Ln_PrecioVentaFacProDetalle:=0;
                Ln_PrecioVentaFacProDetalle:=ROUND(Lr_Servicios.precio_venta,2);
              ELSE
                --SE OBTIENEN LOS PARAMETROS DE LA CARACTERISTICA ADICIONAL
                OPEN C_ObtieneValorAdicional('FACTURABLES_FACTURACION_UNICA',Lr_ObtieneCantAdicional.ID_PRODUCTO_CARACTERISITICA);
                  FETCH C_ObtieneValorAdicional INTO Lr_ObtieneValorAdicional;
                CLOSE C_ObtieneValorAdicional;

                Ln_PrecioVentaFacProDetalle:=0;
                Ln_PrecioAdicional:=ROUND(((Lr_ObtieneCantAdicional.VALOR-Lr_ObtieneValorAdicional.VALOR5)*Lr_ObtieneValorAdicional.VALOR4),2);
                Ln_PrecioVentaFacProDetalle:=ROUND((Lr_Servicios.precio_venta+Ln_PrecioAdicional),2);
              END IF;
            END IF;

            FNCK_FACTURACION_MENSUAL.GET_SOL_DESCT_UNICO(Lr_Servicios.id_servicio,Ln_IdDetalleSolicitud,Ln_PorcentajeDescuento);
            --Si posee porcentaje de descuento, realizo los calculos
            --Debo actualizar la solicitud
            IF Ln_PorcentajeDescuento IS NOT NULL AND Ln_PorcentajeDescuento>0 THEN
              FNCK_FACTURACION_MENSUAL.UPD_SOL_DESCT_UNICO(Ln_IdDetalleSolicitud);
              Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle * Lr_Servicios.cantidad * Ln_PorcentajeDescuento)/100,2);
              --Verifico si posee descuento fijo por porcentaje o valor; ya que este es el mandatorio  
            ELSIF Lr_Servicios.porcentaje_descuento>0 THEN
              Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle * Lr_Servicios.cantidad * Lr_Servicios.porcentaje_descuento)/100,2);
            ELSIF Lr_Servicios.valor_descuento  >0 THEN
              Ln_DescuentoFacProDetalle := ROUND(Lr_Servicios.valor_descuento,2); 
            ELSE  
              Ln_DescuentoFacProDetalle := 0;
            END IF;

            --SE INSERTA EL DETALLE DEL DOCUMENTO
            --Calcula el valor del impuesto correspondiente al detalle
            Ln_BanderaImpuesto:=0;
            IF(Lr_Servicios.plan_id IS NOT NULL AND Lr_Servicios.plan_id>0)THEN
              Ln_BanderaImpuesto:=FNCK_FACTURACION_MENSUAL_TN.F_VERIFICAR_IMPUESTO_PLAN(Lr_Servicios.plan_id);
            ELSIF(Lr_Servicios.producto_id IS NOT NULL AND Lr_Servicios.producto_id>0)THEN  
              Ln_BanderaImpuesto:=FNCK_FACTURACION_MENSUAL_TN.F_VERIFICAR_IMPUESTO_PRODUCTO(Lr_Servicios.producto_id,'IVA');
            END IF;
            --
            Ln_ValorImpuesto := 0;
            --
            Ln_ValorImpuestoAdicional:=0;
            --
            Ln_BanderaImpuestoAdicional:=FNCK_FACTURACION_MENSUAL_TN.F_VERIFICAR_IMPUESTO_PRODUCTO(Lr_Servicios.producto_id,'ICE');
            --
            IF(Ln_BanderaImpuestoAdicional>0) THEN
              --Se obtiene la información del impuesto ICE en estado Activo
              Ln_PorcentajeImpAdicional :=FNCK_FACTURACION_MENSUAL_TN.F_OBTENER_IMPUESTO('ICE');
              Ln_IdImpuestoImpAdicional :=FNCK_FACTURACION_MENSUAL.F_CODIGO_IMPUESTO('ICE');
              Ln_ValorImpuestoAdicional :=(((Ln_PrecioVentaFacProDetalle * Lr_Servicios.cantidad)-Ln_DescuentoFacProDetalle)
                                             *Ln_PorcentajeImpAdicional/100);
            END IF;
            --
            IF (Lr_PuntosAFacturar.PAGA_IVA='S' AND Ln_BanderaImpuesto>0) THEN
              --Se calcula el porcentaje
              Ln_ValorImpuesto := (((Ln_PrecioVentaFacProDetalle * Lr_Servicios.cantidad)-Ln_DescuentoFacProDetalle+Ln_ValorImpuestoAdicional)
                                     * Ln_Porcentaje/100);
            END IF;
            
            --Con el precio de venta nuevo procedemos a ingresar los valores del detalle
            Lr_InfoDocumentoFinancieroDet                            :=NULL;
            Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE             :=SEQ_INFO_DOC_FINANCIERO_DET.NEXTVAL;
            Lr_InfoDocumentoFinancieroDet.DOCUMENTO_ID               :=Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
            Lr_InfoDocumentoFinancieroDet.PUNTO_ID                   :=Lr_Servicios.punto_id;
            Lr_InfoDocumentoFinancieroDet.PLAN_ID                    :=Lr_Servicios.plan_id;
            Lr_InfoDocumentoFinancieroDet.CANTIDAD                   :=Lr_Servicios.cantidad;
            Lr_InfoDocumentoFinancieroDet.PRECIO_VENTA_FACPRO_DETALLE:=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.PORCETANJE_DESCUENTO_FACPRO:=Ln_PorcentajeDescuento;
            Lr_InfoDocumentoFinancieroDet.DESCUENTO_FACPRO_DETALLE   :=Ln_DescuentoFacProDetalle;
            Lr_InfoDocumentoFinancieroDet.VALOR_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.COSTO_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.FE_CREACION                :=SYSDATE;
            Lr_InfoDocumentoFinancieroDet.USR_CREACION               :=Pv_UsrCreacion;
            Lr_InfoDocumentoFinancieroDet.PRODUCTO_ID                :=Lr_Servicios.producto_id;
            Lr_InfoDocumentoFinancieroDet.SERVICIO_ID                :=Lr_Servicios.id_servicio;
            --Obtengo la Fe_activacion del servicio
            Lv_FeActivacion                                             :=FNCK_FACTURACION_MENSUAL.GET_FECHA_ACTIVACION(Lr_Servicios.id_servicio);
            Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE :=TRIM('Facturación de Servicio: '|| Lr_Servicios.descripcion_producto);  
            IF Lv_FeActivacion                                          IS NOT NULL THEN
              Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE:=TRIM(Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE 
                                                                                  || ', Fecha de Activacion: '|| Lv_FeActivacion);
            END IF;

            --SI EL DETALLE ES MAYOR A 0, SE INSERTA. CASO CONTRARIO CONTINÚA ITERANDO
            IF Ln_PrecioVentaFacProDetalle > 0 THEN
              FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_DET(Lr_InfoDocumentoFinancieroDet,Lv_MsnError);
            ELSE
              CONTINUE;
            END IF;
            IF Lv_MsnError IS NOT NULL THEN
              RAISE Le_Error;
            END IF;

            --Con los valores de detalle insertado, podemos ingresar el impuesto
            IF Ln_ValorImpuesto>0 THEN
            --
              Lr_InfoDocumentoFinancieroImp               :=NULL;
              Lr_InfoDocumentoFinancieroImp.ID_DOC_IMP    :=SEQ_INFO_DOC_FINANCIERO_IMP.NEXTVAL;
              Lr_InfoDocumentoFinancieroImp.DETALLE_DOC_ID:=Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE;  

              --Modificar funcion del impuesto
              --Debemos obtener el impuesto en base al porcentaje enviado en el arreglo
              Ln_IdImpuesto                               := FNCK_FACTURACION_MENSUAL.F_CODIGO_IMPUESTO_X_PORCEN(Ln_Porcentaje);
              --
              Lr_InfoDocumentoFinancieroImp.IMPUESTO_ID   :=Ln_IdImpuesto;
              Lr_InfoDocumentoFinancieroImp.VALOR_IMPUESTO:=Ln_ValorImpuesto;
              Lr_InfoDocumentoFinancieroImp.PORCENTAJE    :=Ln_Porcentaje;
              Lr_InfoDocumentoFinancieroImp.FE_CREACION   :=SYSDATE;
              Lr_InfoDocumentoFinancieroImp.USR_CREACION  :=Pv_UsrCreacion;
              FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_IMP(Lr_InfoDocumentoFinancieroImp,Lv_MsnError);
              IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
              END IF;
            --
            END IF;

            --Se verifica impuesto adicionales
            IF (Ln_BanderaImpuestoAdicional>0) THEN
              Pn_IdDocDetalle :=Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE;
              DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('FNCK_FACTURACION_MENSUAL', 'Pn_IdDocDetalle', Pn_IdDocDetalle);
              --Se procede a crear el impuesto adicional
              FNCK_FACTURACION_MENSUAL_TN.P_CREAR_IMPUESTO_ADICIONAL(
                  Pn_IdDocDetalle,
                  Ln_IdImpuestoImpAdicional,
                  Ln_ValorImpuestoAdicional,
                  Ln_PorcentajeImpAdicional
                  );
            END IF;

          END LOOP;

          --Se debe obtener las sumatorias de los Subtotales y se actualiza las cabeceras
          Ln_Subtotal              := 0;
          Ln_SubtotalDescuento     := 0;
          Ln_SubtotalConImpuesto   := 0;
          Ln_ValorTotal            := 0;
          Ln_DescuentoCompensacion := 0;

          Ln_Subtotal            := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_SUBTOTAL(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
          Ln_SubtotalDescuento   := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_DESCUENTO(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
          Ln_SubtotalConImpuesto := ROUND( NVL(FNCK_FACTURACION_MENSUAL.P_SUMAR_IMPUESTOS(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);

          Ln_ValorTotal :=NVL(NVL(Ln_Subtotal,0) - NVL(Ln_SubtotalDescuento,2) - NVL(Ln_DescuentoCompensacion,0) + NVL(Ln_SubtotalConImpuesto,0),0);

         --Actualizo los valores
          Lr_InfoDocumentoFinancieroCab.SUBTOTAL               := Ln_Subtotal;
          Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CERO_IMPUESTO := Ln_Subtotal;
          Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CON_IMPUESTO  := Ln_SubtotalConImpuesto;
          Lr_InfoDocumentoFinancieroCab.SUBTOTAL_DESCUENTO     := Ln_SubtotalDescuento;
          Lr_InfoDocumentoFinancieroCab.DESCUENTO_COMPENSACION := Ln_DescuentoCompensacion;
          Lr_InfoDocumentoFinancieroCab.VALOR_TOTAL            := Ln_ValorTotal;

          --Actualizo la numeracion y el estado
          IF Ln_ValorTotal >0 AND Lv_PrefijoEmpresa = 'MD' THEN 
            Lrf_Numeracion:=FNCK_CONSULTS.F_GET_NUMERACION(Lv_PrefijoEmpresa,Lv_EsMatriz,Lv_EsOficinaFacturacion,Ln_IdOficina,Lv_CodigoNumeracion);
            --Debo recorrer la numeracion obtenida
            LOOP
              FETCH Lrf_Numeracion INTO Lr_AdmiNumeracion;
              EXIT
            WHEN Lrf_Numeracion%notfound;
              Lv_Secuencia :=LPAD(Lr_AdmiNumeracion.SECUENCIA,9,'0');
              Lv_Numeracion:=Lr_AdmiNumeracion.NUMERACION_UNO || '-'||Lr_AdmiNumeracion.NUMERACION_DOS||'-'||Lv_Secuencia;
            END LOOP;
            --Cierro la numeracion
            CLOSE Lrf_Numeracion;

            --Incremento la numeracion
            Lr_AdmiNumeracion.SECUENCIA:=Lr_AdmiNumeracion.SECUENCIA+1;
            FNCK_TRANSACTION.UPDATE_ADMI_NUMERACION(Lr_AdmiNumeracion.ID_NUMERACION,Lr_AdmiNumeracion,Lv_MsnError);
            IF Lv_MsnError IS NOT NULL THEN
              RAISE Le_Error;
            END IF;

          END IF;

          Lr_InfoDocumentoFinancieroCab.NUMERO_FACTURA_SRI   :=Lv_Numeracion;
          Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT:=Lv_EstadoPendiente;
          Lr_InfoDocumentoFinancieroCab.FE_EMISION           :=TRUNC(SYSDATE);

          --Actualizo los valores de la cabecera
          FNCK_TRANSACTION.UPDATE_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO,Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
          IF Lv_MsnError IS NOT NULL THEN
            RAISE Le_Error;
          END IF;

          --Controlador de COMMIT
          Ln_Contador:=Ln_Contador+1;
          IF Ln_Contador > 1000 THEN
            COMMIT;
            Ln_Contador:=0;
          END IF;

        EXCEPTION
          WHEN Le_Error THEN
            ROLLBACK;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error: ' || Lv_MsnError,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
        END;
      END LOOP;
      COMMIT;
    EXCEPTION
      WHEN OTHERS THEN
        ROLLBACK;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                             Lv_NombreProcedimiento,
                                             'Error ' || SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                      ||DBMS_UTILITY.FORMAT_ERROR_STACK||' - ERROR_BACKTRACE: '||DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                              NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                              SYSDATE,
                                              NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
  END P_FACTURACION_UNICA;  


  PROCEDURE P_SENSA_CICLO
  AS

  BEGIN

      DB_FINANCIERO.FNCK_FACTURACION.P_PREFACTURA_X_CLIENTE;

  END P_SENSA_CICLO;

  PROCEDURE P_INSERT_CABECERA_FACTURACION(Pr_Docum_Fin_Cab IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_Docum_Fin_Cab.Id_Documento IS NULL OR Pr_Docum_Fin_Cab.Id_Documento <= 0 THEN
      Lv_Identificador:=DB_FINANCIERO.SEQ_INFO_DOC_FINANCIERO_CAB.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_Docum_Fin_Cab.Id_Documento;
    END IF;

    INSERT 
    INTO DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB (
          ID_DOCUMENTO,
          OFICINA_ID,
          PUNTO_ID,
          TIPO_DOCUMENTO_ID,
          ENTREGO_RETENCION_FTE,
          ESTADO_IMPRESION_FACT,
          ES_AUTOMATICA,
          PRORRATEO,
          REACTIVACION,
          RECURRENTE,
          COMISIONA,
          FE_CREACION,
          USR_CREACION,
          SUBTOTAL,
          SUBTOTAL_CERO_IMPUESTO,
          SUBTOTAL_CON_IMPUESTO,
          SUBTOTAL_DESCUENTO,
          VALOR_TOTAL,
          MES_CONSUMO,
          ANIO_CONSUMO,
          FE_EMISION,
          RANGO_CONSUMO)
          VALUES 
          (Lv_Identificador,
          Pr_Docum_Fin_Cab.Oficina_Id,
          Pr_Docum_Fin_Cab.Punto_Id,
          Pr_Docum_Fin_Cab.Tipo_Documento_Id,
          Pr_Docum_Fin_Cab.Entrego_Retencion_Fte,
          Pr_Docum_Fin_Cab.Estado_Impresion_Fact,
          Pr_Docum_Fin_Cab.Es_Automatica,
          Pr_Docum_Fin_Cab.Prorrateo,
          Pr_Docum_Fin_Cab.Reactivacion,
          Pr_Docum_Fin_Cab.Recurrente,
          Pr_Docum_Fin_Cab.Comisiona,
          Pr_Docum_Fin_Cab.Fe_Creacion,
          Pr_Docum_Fin_Cab.Usr_Creacion,
          Pr_Docum_Fin_Cab.Subtotal,
          Pr_Docum_Fin_Cab.Subtotal_Cero_Impuesto,
          Pr_Docum_Fin_Cab.Subtotal_Con_Impuesto,
          Pr_Docum_Fin_Cab.Subtotal_Descuento,
          Pr_Docum_Fin_Cab.Valor_Total,
          Pr_Docum_Fin_Cab.Mes_Consumo,
          Pr_Docum_Fin_Cab.Anio_Consumo,
          Pr_Docum_Fin_Cab.Fe_Emision,
          Pr_Docum_Fin_Cab.Rango_Consumo);
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_CABECERA_FACTURACION;

  PROCEDURE P_INSERT_HISTORIAL_FACTURACION(Pr_Docum_Hist IN  DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE,
                                           Pv_Error      OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_Docum_Hist.Id_Documento_Historial IS NULL OR Pr_Docum_Hist.Id_Documento_Historial <= 0 THEN
      Lv_Identificador:=DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_Docum_Hist.Id_Documento_Historial;
    END IF;

    INSERT
    INTO
      DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL
      (
        ID_DOCUMENTO_HISTORIAL,
        DOCUMENTO_ID,
        MOTIVO_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO,
        OBSERVACION
      )
      VALUES
      (
        Lv_Identificador,
        Pr_Docum_Hist.Documento_Id,
        Pr_Docum_Hist.Motivo_Id,
        Pr_Docum_Hist.Fe_Creacion,
        Pr_Docum_Hist.Usr_Creacion,
        Pr_Docum_Hist.Estado,
        Pr_Docum_Hist.Observacion
      );
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_HISTORIAL_FACTURACION;

  PROCEDURE P_INSERT_DETALLE_FACTURACION(Pr_Docum_Fin_Det IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE,
                                         Pv_Error         OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_Docum_Fin_Det.Id_Doc_Detalle IS NULL OR Pr_Docum_Fin_Det.Id_Doc_Detalle <= 0 THEN
      Lv_Identificador:=DB_FINANCIERO.SEQ_INFO_DOC_FINANCIERO_DET.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_Docum_Fin_Det.Id_Doc_Detalle;
    END IF;

    INSERT
    INTO
      DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET
      (
        ID_DOC_DETALLE,
        DOCUMENTO_ID,
        PLAN_ID,
        PUNTO_ID,
        CANTIDAD,
        PRECIO_VENTA_FACPRO_DETALLE,
        PORCETANJE_DESCUENTO_FACPRO,
        DESCUENTO_FACPRO_DETALLE,
        VALOR_FACPRO_DETALLE,
        COSTO_FACPRO_DETALLE,
        OBSERVACIONES_FACTURA_DETALLE,
        FE_CREACION,
        FE_ULT_MOD,
        USR_CREACION,
        USR_ULT_MOD,
        EMPRESA_ID,
        OFICINA_ID,
        PRODUCTO_ID,
        MOTIVO_ID,
        PAGO_DET_ID,
        SERVICIO_ID
      )
      VALUES
      (
        Lv_Identificador,
        Pr_Docum_Fin_Det.Documento_Id,
        Pr_Docum_Fin_Det.Plan_Id,
        Pr_Docum_Fin_Det.Punto_Id,
        Pr_Docum_Fin_Det.Cantidad,
        Pr_Docum_Fin_Det.Precio_Venta_Facpro_Detalle,
        Pr_Docum_Fin_Det.Porcetanje_Descuento_Facpro,
        Pr_Docum_Fin_Det.Descuento_Facpro_Detalle,
        Pr_Docum_Fin_Det.Valor_Facpro_Detalle,
        Pr_Docum_Fin_Det.Costo_Facpro_Detalle,
        Pr_Docum_Fin_Det.Observaciones_Factura_Detalle,
        Pr_Docum_Fin_Det.Fe_Creacion,
        Pr_Docum_Fin_Det.Fe_Ult_Mod,
        Pr_Docum_Fin_Det.Usr_Creacion,
        Pr_Docum_Fin_Det.Usr_Ult_Mod,
        Pr_Docum_Fin_Det.Empresa_Id,
        Pr_Docum_Fin_Det.Oficina_Id,
        Pr_Docum_Fin_Det.Producto_Id,
        Pr_Docum_Fin_Det.Motivo_Id,
        Pr_Docum_Fin_Det.Pago_Det_Id,
        Pr_Docum_Fin_Det.Servicio_Id
      );
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_DETALLE_FACTURACION;

  PROCEDURE P_INSERT_IMPUESTO_FACTURACION(Pr_Docum_Fin_Imp IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_Docum_Fin_Imp.Id_Doc_Imp IS NULL OR Pr_Docum_Fin_Imp.Id_Doc_Imp <= 0 THEN
      Lv_Identificador:=DB_FINANCIERO.SEQ_INFO_DOC_FINANCIERO_IMP.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_Docum_Fin_Imp.Id_Doc_Imp;
    END IF;

    INSERT
    INTO
      DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP
      (
        ID_DOC_IMP,
        DETALLE_DOC_ID,
        IMPUESTO_ID,
        VALOR_IMPUESTO,
        PORCENTAJE,
        FE_CREACION,
        FE_ULT_MOD,
        USR_CREACION,
        USR_ULT_MOD
      )
      VALUES
      (
        Lv_Identificador,
        Pr_Docum_Fin_Imp.Detalle_Doc_Id,
        Pr_Docum_Fin_Imp.Impuesto_Id,
        Pr_Docum_Fin_Imp.Valor_Impuesto,
        Pr_Docum_Fin_Imp.Porcentaje,
        Pr_Docum_Fin_Imp.Fe_Creacion,
        Pr_Docum_Fin_Imp.Fe_Ult_Mod,
        Pr_Docum_Fin_Imp.Usr_Creacion,
        Pr_Docum_Fin_Imp.Usr_Ult_Mod
      );
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_IMPUESTO_FACTURACION;

  PROCEDURE P_INSERT_DET_SOL_HIST(Pr_Det_Sol_Hist IN  DB_COMERCIAL.INFO_DETALLE_SOL_HIST%ROWTYPE,
                                  Pv_Error        OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_Det_Sol_Hist.Id_Solicitud_Historial IS NULL OR Pr_Det_Sol_Hist.Id_Solicitud_Historial <= 0 THEN
      Lv_Identificador:=DB_COMERCIAL.SEQ_INFO_DETALLE_SOL_HIST.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_Det_Sol_Hist.Id_Solicitud_Historial;
    END IF;

    INSERT
    INTO DB_COMERCIAL.INFO_DETALLE_SOL_HIST
      (
        ID_SOLICITUD_HISTORIAL,
        DETALLE_SOLICITUD_ID,
        ESTADO,
        OBSERVACION,
        USR_CREACION,
        FE_CREACION
      )
      VALUES
      (
        Lv_Identificador,
        Pr_Det_Sol_Hist.Detalle_Solicitud_Id,
        Pr_Det_Sol_Hist.Estado,
        Pr_Det_Sol_Hist.Observacion,
        Pr_Det_Sol_Hist.Usr_Creacion,
        Pr_Det_Sol_Hist.Fe_Creacion
      );
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_DET_SOL_HIST;

  PROCEDURE P_ACTUAL_CABECERA_FACTURACION(Pr_Docum_Fin_Cab IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE,
                                          Pv_Error         OUT VARCHAR2)
  IS

  BEGIN

    UPDATE DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB
    SET SUBTOTAL=NVL(Pr_Docum_Fin_Cab.Subtotal,SUBTOTAL),
        SUBTOTAL_CERO_IMPUESTO=NVL(Pr_Docum_Fin_Cab.Subtotal_Cero_Impuesto,SUBTOTAL_CERO_IMPUESTO),
        SUBTOTAL_CON_IMPUESTO=NVL(Pr_Docum_Fin_Cab.Subtotal_Con_Impuesto,SUBTOTAL_CON_IMPUESTO),
        SUBTOTAL_DESCUENTO=NVL(Pr_Docum_Fin_Cab.Subtotal_Descuento,SUBTOTAL_DESCUENTO),
        VALOR_TOTAL=NVL(Pr_Docum_Fin_Cab.Valor_Total,VALOR_TOTAL),
        SUBTOTAL_SERVICIOS=NVL(Pr_Docum_Fin_Cab.Subtotal_Servicios,SUBTOTAL_SERVICIOS),
        IMPUESTOS_SERVICIOS=NVL(Pr_Docum_Fin_Cab.Impuestos_Servicios,IMPUESTOS_SERVICIOS),
        OFICINA_ID=NVL(Pr_Docum_Fin_Cab.Oficina_Id,OFICINA_ID),
        FE_EMISION=NVL(Pr_Docum_Fin_Cab.Fe_Emision,FE_EMISION),
        NUMERO_FACTURA_SRI=NVL(Pr_Docum_Fin_Cab.Numero_Factura_Sri,NUMERO_FACTURA_SRI),
        ES_ELECTRONICA=NVL(Pr_Docum_Fin_Cab.Es_Electronica,ES_ELECTRONICA)
    WHERE ID_DOCUMENTO=NVL(Pr_Docum_Fin_Cab.Id_Documento,ID_DOCUMENTO);
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_ACTUAL_CABECERA_FACTURACION;

  PROCEDURE P_ACTUAL_NUMERAC_FACTURACION(Pr_AdmiNumeracion IN  FNKG_TYPES.Lr_AdmiNumeracion,
                                          Pv_Error          OUT VARCHAR2)
  IS

  BEGIN
    
    UPDATE
      DB_COMERCIAL.ADMI_NUMERACION
    SET
      EMPRESA_ID           = NVL(Pr_AdmiNumeracion.Empresa_Id, EMPRESA_ID),
      OFICINA_ID           = NVL(Pr_AdmiNumeracion.Oficina_Id, OFICINA_ID),
      DESCRIPCION          = NVL(Pr_AdmiNumeracion.Descripcion, DESCRIPCION),
      CODIGO               = NVL(Pr_AdmiNumeracion.Codigo, CODIGO),
      NUMERACION_UNO       = NVL(Pr_AdmiNumeracion.Numeracion_uno, NUMERACION_UNO),
      NUMERACION_DOS       = NVL(Pr_AdmiNumeracion.Numeracion_dos, NUMERACION_DOS),
      SECUENCIA            = NVL(Pr_AdmiNumeracion.Secuencia + 1, SECUENCIA),
      FE_CREACION          = NVL(Pr_AdmiNumeracion.Fe_Creacion, FE_CREACION),
      USR_CREACION         = NVL(Pr_AdmiNumeracion.Usr_Creacion, USR_CREACION),
      FE_ULT_MOD           = NVL(Pr_AdmiNumeracion.Fe_Ult_Mod, FE_ULT_MOD),
      USR_ULT_MOD          = NVL(Pr_AdmiNumeracion.Usr_Ult_Mod, USR_ULT_MOD),
      TABLA                = NVL(Pr_AdmiNumeracion.Tabla, TABLA),
      ESTADO               = NVL(Pr_AdmiNumeracion.Estado, ESTADO),
      NUMERO_AUTORIZACION  = NVL(Pr_AdmiNumeracion.Numero_Autorizacion, NUMERO_AUTORIZACION),
      PROCESOS_AUTOMATICOS = NVL(Pr_AdmiNumeracion.Procesos_Automaticos, PROCESOS_AUTOMATICOS),
      TIPO_ID              = NVL(Pr_AdmiNumeracion.Tipo_Id, TIPO_ID)
    WHERE
      ID_NUMERACION = Pr_AdmiNumeracion.ID_NUMERACION;
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_ACTUAL_NUMERAC_FACTURACION;

  PROCEDURE P_ACTUAL_PERS_EMP_ROL_CARACT(Pr_Pers_Emp_Rol_Caract IN  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC%ROWTYPE,
                                         Pv_Error              OUT VARCHAR2)
  IS

  BEGIN

    UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
    SET ESTADO=Pr_Pers_Emp_Rol_Caract.Estado,
        VALOR=Pr_Pers_Emp_Rol_Caract.Valor
    WHERE PERSONA_EMPRESA_ROL_ID=Pr_Pers_Emp_Rol_Caract.Persona_Empresa_Rol_Id
    AND ESTADO='Activo'
    AND VALOR='N';
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_ACTUAL_PERS_EMP_ROL_CARACT;

  PROCEDURE P_ACTUAL_DET_SOLICITUD(Pr_Det_Solicitud IN  DB_COMERCIAL.INFO_DETALLE_SOLICITUD%ROWTYPE,
                                   Pv_Error         OUT VARCHAR2)
  IS

  BEGIN

    UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
    SET ESTADO                =Pr_Det_Solicitud.Estado
    WHERE ID_DETALLE_SOLICITUD=Pr_Det_Solicitud.Id_Detalle_Solicitud;
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_ACTUAL_DET_SOLICITUD;

  PROCEDURE P_PERIODO_FACTURACION_X_PUNTO(  Pn_EmpresaCod            IN  NUMBER,
                                            Pv_FechaActivacion       IN  VARCHAR2,
                                            Pn_PuntoPadre            IN  NUMBER,
                                            Pv_TipoProceso           IN  VARCHAR2 DEFAULT NULL,
                                            Pn_ServicioId            IN  DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE DEFAULT NULL,
                                            Pd_FechaInicioPeriodo    OUT VARCHAR2,
                                            Pd_FechaFinPeriodo       OUT VARCHAR2,
                                            Pn_CantidadDiasTotalMes  OUT NUMBER,
                                            Pn_CantidadDiasRestantes OUT NUMBER )
  IS

      Lv_DiaInicialPeriodo      VARCHAR2(3):='';
      Lv_DiaFinalPeriodo        VARCHAR2(3):='';
      Ln_CantDias               NUMBER:=0;
      Ln_DiaFechActiv           NUMBER:=0;
      Lv_MesAnioActiv           VARCHAR2(20):='';
      Ln_ServicioCaractId       DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.ID_SERVICIO_CARACTERISTICA%TYPE;
      Ln_CicloOrigenId          DB_COMERCIAL.ADMI_CICLO.ID_CICLO%TYPE;
      Ln_CicloActual            DB_COMERCIAL.ADMI_CICLO.ID_CICLO%TYPE;

      CURSOR C_GetTotalServCRS(Cn_ServicioId   DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA.SERVICIO_ID%TYPE,
                               Cv_EstadoActivo VARCHAR2 DEFAULT 'Activo',
                               Cv_ValorS       VARCHAR2 DEFAULT 'S',
                               Cv_CRSCaract    DB_COMERCIAL.ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA%TYPE DEFAULT 'FACTURACION_CRS_CICLO_FACT')
      IS
        SELECT ISC.ID_SERVICIO_CARACTERISTICA, ISC.CICLO_ORIGEN_ID
          FROM DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA ISC,
               DB_COMERCIAL.ADMI_CARACTERISTICA AC
         WHERE ISC.SERVICIO_ID = Cn_ServicioId
           AND ISC.ESTADO = Cv_EstadoActivo
           AND ISC.VALOR = Cv_ValorS
           AND ISC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
           AND AC.DESCRIPCION_CARACTERISTICA = Cv_CRSCaract
           AND AC.ESTADO = Cv_EstadoActivo;

  BEGIN

      BEGIN
        SELECT TO_CHAR(ac.FE_INICIO,'DD') AS iniCiclo,
               TO_CHAR(ac.FE_FIN,'DD')    AS finCiclo,
               AC.ID_CICLO
        INTO Lv_DiaInicialPeriodo, Lv_DiaFinalPeriodo, Ln_CicloActual
        FROM DB_FINANCIERO.ADMI_CICLO AC
        WHERE AC.EMPRESA_COD=Pn_EmpresaCod
        AND AC.ID_CICLO     =
          (SELECT MAX(IPERC.VALOR)
          FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
            DB_COMERCIAL.ADMI_CARACTERISTICA ACA,
            DB_COMERCIAL.INFO_PUNTO IP
          WHERE IP.ID_PUNTO                 =Pn_PuntoPadre
          AND IPERC.PERSONA_EMPRESA_ROL_ID  =IP.PERSONA_EMPRESA_ROL_ID
          AND IPERC.CARACTERISTICA_ID       =ACA.ID_CARACTERISTICA
          AND IPERC.ESTADO                  = 'Activo'
          AND ACA.DESCRIPCION_CARACTERISTICA='CICLO_FACTURACION'
          );
      EXCEPTION
        WHEN OTHERS THEN
          NULL;
      END;

      --Se verifica si el servicio es origen CRS
      IF NVL(Pn_ServicioId, 0) > 0 AND Pv_TipoProceso = 'cambioPrecio' THEN
        OPEN  C_GetTotalServCRS (Cn_ServicioId => Pn_ServicioId);
        FETCH C_GetTotalServCRS INTO Ln_ServicioCaractId, Ln_CicloOrigenId;
        CLOSE C_GetTotalServCRS;

        --Si tiene un CRS y el ciclo origen del CRS es diferente al ciclo Actual, se establece el rango del ciclo en base al origen
        IF NVL (Ln_ServicioCaractId, 0) > 0 AND Ln_CicloOrigenId <> Ln_CicloActual THEN
            SELECT TO_CHAR(ac.FE_INICIO,'DD'),
                   TO_CHAR(ac.FE_FIN,'DD')
              INTO Lv_DiaInicialPeriodo, Lv_DiaFinalPeriodo
              FROM DB_FINANCIERO.ADMI_CICLO AC
             WHERE AC.ID_CICLO = Ln_CicloOrigenId;
        END IF;
      END IF;

      Ln_DiaFechActiv:=to_char(to_date(Pv_FechaActivacion,'YYYY-MM-DD'),'DD');
      Lv_MesAnioActiv:=to_char(to_date(Pv_FechaActivacion,'YYYY-MM-DD'),'YYYY-MM');

      IF Lv_DiaInicialPeriodo IS NULL THEN

        Lv_DiaInicialPeriodo := to_char(to_date(Lv_MesAnioActiv,'YYYY-MM'),'DD');
        Lv_DiaFinalPeriodo := to_char(to_date(Lv_MesAnioActiv,'YYYY-MM'),'DD');

      END IF;

      IF to_number(Lv_DiaInicialPeriodo) <= Ln_DiaFechActiv THEN

        select Lv_MesAnioActiv||'-'||Lv_DiaInicialPeriodo
        into Pd_FechaInicioPeriodo
        from dual;

      ELSE

        select to_char(add_months(to_date(Lv_MesAnioActiv,'YYYY-MM'),-1),'YYYY-MM')||'-'||Lv_DiaInicialPeriodo
        into Pd_FechaInicioPeriodo
        from dual;

      END IF;

      select to_char(add_months(to_date(Pd_FechaInicioPeriodo,'YYYY-MM-DD'),+1)-1,'YYYY-MM-DD')
      into Pd_FechaFinPeriodo
      from dual;

      select (to_date(Pd_FechaFinPeriodo,'YYYY-MM-DD')+1)-to_date(Pd_FechaInicioPeriodo,'YYYY-MM-DD')
      into Pn_CantidadDiasTotalMes
      from dual;

      select (to_date(Pd_FechaFinPeriodo,'YYYY-MM-DD')+1)-to_date(Pv_FechaActivacion,'YYYY-MM-DD')
      into Ln_CantDias
      from dual;

      IF Ln_CantDias < 0 THEN
        Pn_CantidadDiasRestantes:=0;
      ELSE
        Pn_CantidadDiasRestantes:=Ln_CantDias;
      END IF;

  END P_PERIODO_FACTURACION_X_PUNTO;

  PROCEDURE P_INSERT_INFO_DOC_CARACT(Pr_InfoDocCaract IN  DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE,
                                     Pv_Error         OUT VARCHAR2)
  IS
    Lv_Identificador NUMBER:=0;
  BEGIN
    IF Pr_InfoDocCaract.Id_Documento_Caracteristica IS NULL OR Pr_InfoDocCaract.Id_Documento_Caracteristica <= 0 THEN
      Lv_Identificador:=DB_FINANCIERO.SEQ_INFO_DOCUMENTO_CARACT.NEXTVAL;
    ELSE
      Lv_Identificador:=Pr_InfoDocCaract.Id_Documento_Caracteristica;
    END IF;

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
        FE_ULT_MOD,
        USR_ULT_MOD,
        IP_ULT_MOD,
        ESTADO
      )
      VALUES
      (
        Lv_Identificador,
        Pr_InfoDocCaract.Documento_Id,
        Pr_InfoDocCaract.Caracteristica_Id,
        Pr_InfoDocCaract.Valor,
        Pr_InfoDocCaract.Fe_Creacion,
        Pr_InfoDocCaract.Usr_Creacion,
        Pr_InfoDocCaract.Ip_Creacion,
        Pr_InfoDocCaract.Fe_Ult_Mod,
        Pr_InfoDocCaract.Usr_Ult_Mod,
        Pr_InfoDocCaract.Ip_Ult_Mod,
        Pr_InfoDocCaract.Estado
      );
  EXCEPTION
    WHEN OTHERS THEN
      Pv_Error:=SQLCODE || '-' || SQLERRM;
  END P_INSERT_INFO_DOC_CARACT;

  FUNCTION F_GET_FECHA_ULT_FACT(Pn_PuntFact NUMBER, Fn_IdServicio DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE)
    RETURN VARCHAR2
  IS
    CURSOR C_Ult_Fact(Pn_Punto NUMBER, Cn_IdServicio DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE )
    IS
      SELECT TO_NUMBER(CAB.MES_CONSUMO) AS MES_CONSUMO,
        CAB.ANIO_CONSUMO,
        (SELECT MAX(CAR.VALOR)
        FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA CAR,
          DB_COMERCIAL.ADMI_CARACTERISTICA AC
        WHERE CAR.CARACTERISTICA_ID      =AC.ID_CARACTERISTICA
        AND CAR.DOCUMENTO_ID             =CAB.ID_DOCUMENTO
        AND AC.DESCRIPCION_CARACTERISTICA='CICLO_FACTURACION'
        AND AC.ESTADO                    ='Activo'
        AND CAR.ESTADO                   ='Activo'
        ) AS CICLO,
        CAB.RANGO_CONSUMO,
        (SELECT MAX(CAR.VALOR)
        FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA CAR,
          DB_COMERCIAL.ADMI_CARACTERISTICA AC
        WHERE CAR.CARACTERISTICA_ID      =AC.ID_CARACTERISTICA
        AND CAR.DOCUMENTO_ID             =CAB.ID_DOCUMENTO
        AND AC.DESCRIPCION_CARACTERISTICA='CICLO_FACTURADO_MES'
        AND AC.ESTADO                    ='Activo'
        AND CAR.ESTADO                   ='Activo'
        ) as CAR_MES,
        (SELECT MAX(CAR.VALOR)
        FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA CAR,
          DB_COMERCIAL.ADMI_CARACTERISTICA AC
        WHERE CAR.CARACTERISTICA_ID      =AC.ID_CARACTERISTICA
        AND CAR.DOCUMENTO_ID             =CAB.ID_DOCUMENTO
        AND AC.DESCRIPCION_CARACTERISTICA='CICLO_FACTURADO_ANIO'
        AND AC.ESTADO                    ='Activo'
        AND CAR.ESTADO                   ='Activo'
        ) as CAR_ANIO
      FROM INFO_DOCUMENTO_FINANCIERO_CAB CAB,
      INFO_DOCUMENTO_FINANCIERO_DET DET
      WHERE CAB.PUNTO_ID             =Pn_Punto
      AND CAB.ESTADO_IMPRESION_FACT IN ('Activo','Pendiente','Cerrado')
      AND CAB.TIPO_DOCUMENTO_ID IN (1,5)
      AND CAB.ES_AUTOMATICA='S'
      AND CAB.USR_CREACION NOT IN (SELECT DET.VALOR6 FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB 
                                    JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET ON CAB.ID_PARAMETRO = DET.PARAMETRO_ID WHERE
                                    CAB.NOMBRE_PARAMETRO = 'SOLICITUDES_DE_CONTRATO' AND CAB.ESTADO = 'Activo'
                                    AND DET.EMPRESA_COD = '18')
      AND CAB.ID_DOCUMENTO = DET.DOCUMENTO_ID
      AND DET.SERVICIO_ID  = Cn_IdServicio
      ORDER BY CAB.ID_DOCUMENTO DESC;

    CURSOR C_FechCiclo(Pn_IdCiclo NUMBER)
    IS
      SELECT TO_CHAR(FE_INICIO,'DD') AS DIA
      FROM ADMI_CICLO
      WHERE ID_CICLO=Pn_IdCiclo;

    Lc_Ult_Fact C_Ult_Fact%ROWTYPE;
    Lc_FechCiclo C_FechCiclo%ROWTYPE;
    Ld_FechaIni DATE;
    Lv_FechaFin VARCHAR2(200):='';

  BEGIN

    OPEN C_Ult_Fact(Pn_PuntFact,Fn_IdServicio);
    FETCH C_Ult_Fact INTO Lc_Ult_Fact;
    CLOSE C_Ult_Fact;

    IF Lc_Ult_Fact.CICLO IS NULL THEN
      IF Lc_Ult_Fact.RANGO_CONSUMO IS NULL THEN
        Ld_FechaIni:=TO_DATE('01/'||Lc_Ult_Fact.MES_CONSUMO||'/'||Lc_Ult_Fact.ANIO_CONSUMO,'DD/MM/YYYY');
      ELSE
        Ld_FechaIni:=TO_DATE('01/'||Lc_Ult_Fact.CAR_MES||'/'||Lc_Ult_Fact.CAR_ANIO,'DD/MM/YYYY');
      END IF;
    ELSE
      OPEN C_FechCiclo(Lc_Ult_Fact.CICLO);
      FETCH C_FechCiclo INTO Lc_FechCiclo;
      CLOSE C_FechCiclo;
      IF Lc_Ult_Fact.RANGO_CONSUMO IS NULL THEN
        Ld_FechaIni:=TO_DATE(Lc_FechCiclo.DIA||'/'||Lc_Ult_Fact.MES_CONSUMO||'/'||Lc_Ult_Fact.ANIO_CONSUMO,'DD/MM/YYYY');
      ELSE
        Ld_FechaIni:=TO_DATE(Lc_FechCiclo.DIA||'/'||Lc_Ult_Fact.CAR_MES||'/'||Lc_Ult_Fact.CAR_ANIO,'DD/MM/YYYY');
      END IF;
    END IF;

    Lv_FechaFin:=TO_CHAR(ADD_MONTHS(Ld_FechaIni-1,1),'DD/MM/YYYY');

    RETURN Lv_FechaFin;

  EXCEPTION
    WHEN OTHERS THEN
      RETURN 'X';

  END F_GET_FECHA_ULT_FACT;


  PROCEDURE P_FACT_OFFICE365_CANCEL(Pn_ServicioId      IN NUMBER,
                                    Pv_PrefijoEmpresa  IN VARCHAR2,
                                    Pv_EmpresaCod      IN VARCHAR2,
                                    Pv_UsrCreacion     IN VARCHAR2,
                                    Pv_Ip              IN VARCHAR2) AS
  -- Constantes--
  Lv_EstadoActivo               VARCHAR2(15)  := 'Activo';
  Lv_EstadoInactivo             VARCHAR2(15)  := 'Inactivo';
  Lv_EstadoPendiente            VARCHAR2(15)  := 'Pendiente';
  Lv_EstadoCancelado            VARCHAR2(20)  := 'Cancel';
  Lv_DescripcionProducto        VARCHAR2(50)  := 'NetlifeCloud';
  Lv_ValorS                     VARCHAR2(1)   := 'S';
  Lv_ValorN                     VARCHAR2(1)   := 'N';
  Lv_ModuloComercial            VARCHAR2(20)  := 'COMERCIAL';
  Lv_ParametroPeriodoRenovacion VARCHAR2(50)  := 'PERIODO_RENOVAR_LICOFFICE365';
  Lv_NombreProcedimiento        VARCHAR2(50)  := 'FNCK_FACTURACION_MENSUAL.P_FACT_OFFICE365_CANCEL';
  Lv_IpLocal                    VARCHAR2(15)  := '127.0.0.1';
  Lv_AccionHistActivacion       VARCHAR2(20)  := 'confirmarServicio';
  Lv_AccionHistRenovacion       VARCHAR2(50)  := 'renovarLicenciaOffice365';
  Lv_NombreParametroCab         VARCHAR2(30)  := 'DESCRIPCION_TIPO_FACTURACION';

  --Cursor que sirve para  obtener la fecha de activación del servicio enviado como parámetro.
  CURSOR C_FECHA_ACTIVACION(Cn_IdServicio DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE) IS
    SELECT MAX (ISH.FE_CREACION) FE_CREACION
    FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
    WHERE ISH.SERVICIO_ID = Cn_IdServicio
    AND   ISH.ID_SERVICIO_HISTORIAL =  (SELECT MAX(ISHT.ID_SERVICIO_HISTORIAL) 
                                            FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISHT
                                            JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISHT.SERVICIO_ID
                                            WHERE ISHT.SERVICIO_ID = Cn_IdServicio
                                            AND   ISER.ESTADO      = Lv_EstadoCancelado
                                            AND   ISHT.ACCION      = Lv_AccionHistRenovacion)
       OR ISH.ID_SERVICIO_HISTORIAL =  (SELECT MAX(ISHT.ID_SERVICIO_HISTORIAL) 
                                        FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISHT
                                        JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISHT.SERVICIO_ID
                                        WHERE ISHT.SERVICIO_ID = Cn_IdServicio
                                        AND   ISER.ESTADO      = Lv_EstadoCancelado
                                        AND   ISHT.ACCION      = Lv_AccionHistActivacion);
  
  --Cursor que sirve para  obtener para obtener las características según los filtros enviados como parámetro.
  CURSOR C_GET_PARAMETROS(Cv_EmpresaCod VARCHAR2, Cv_NombreParametro VARCHAR2, Cv_Modulo VARCHAR2, Cv_Estado VARCHAR2) IS
    SELECT DET.VALOR1,
           DET.VALOR2,
           DET.VALOR3,
           DET.VALOR4
    FROM   DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
           DB_GENERAL.ADMI_PARAMETRO_DET DET
    WHERE CAB.ID_PARAMETRO     =  DET.PARAMETRO_ID
    AND   CAB.ESTADO           =  Cv_Estado
    AND   DET.ESTADO           =  Cv_Estado
    AND   CAB.MODULO           =  Cv_Modulo
    AND   DET.EMPRESA_COD      =  Cv_EmpresaCod
    AND   CAB.NOMBRE_PARAMETRO =  Cv_NombreParametro;

  --Cursor que sirve para obtener la primera factura proporcional del servicio enviado como parámetro.
  --Costo query: 5
  CURSOR C_GET_FACT_PRO(Cv_DescProducto VARCHAR2, Cn_IdServicio NUMBER) IS
    SELECT IDFD.*
    FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFD
    JOIN DB_COMERCIAL.ADMI_PRODUCTO  AP ON AP.ID_PRODUCTO = IDFD.PRODUCTO_ID
    WHERE AP.DESCRIPCION_PRODUCTO = Cv_DescProducto 
    AND   IDFD.DOCUMENTO_ID       =(
                                     SELECT MIN(IDFC.ID_DOCUMENTO)
                                     FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC
                                     WHERE IDFC.TIPO_DOCUMENTO_ID = 5
                                     AND IDFC.USR_CREACION = 'telcos_proporcional'
                                     AND IDFC.PUNTO_ID = (
                                                          SELECT ISER.PUNTO_FACTURACION_ID
                                                          FROM DB_COMERCIAL.INFO_SERVICIO ISER
                                                          WHERE ISER.ID_SERVICIO = Cn_IdServicio)
                                   );

  --Cursor que obtiene la fecha posterior n meses después contados a partir de la última fecha de creación del historial del servicio.
  --Costo Query: 28
  CURSOR  C_GET_FE_FINPERIODO(Cn_IdServicio NUMBER , Cn_NumMesesTotal NUMBER) IS
    SELECT TO_CHAR(ADD_MONTHS(ISH.FE_CREACION,Cn_NumMesesTotal), 'DD MONTH YYYY','NLS_DATE_LANGUAGE=SPANISH')
    FROM   DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
    WHERE  ISH.SERVICIO_ID = Cn_IdServicio
    AND    (ISH.ID_SERVICIO_HISTORIAL =  (  SELECT MAX(ISHT.ID_SERVICIO_HISTORIAL) 
                                            FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISHT
                                            JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISHT.SERVICIO_ID
                                            WHERE ISHT.SERVICIO_ID = Cn_IdServicio
                                            AND   ISER.ESTADO      = Lv_EstadoCancelado
                                            AND   ISHT.ACCION      = Lv_AccionHistRenovacion)
            OR ISH.ID_SERVICIO_HISTORIAL = (SELECT MAX(ISHT.ID_SERVICIO_HISTORIAL) 
                                            FROM  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISHT
                                            JOIN  DB_COMERCIAL.INFO_SERVICIO ISER ON ISER.ID_SERVICIO = ISHT.SERVICIO_ID
                                            WHERE ISHT.SERVICIO_ID = Cn_IdServicio
                                            AND   ISER.ESTADO      = Lv_EstadoCancelado
                                            AND   ISHT.ACCION      = Lv_AccionHistActivacion)
            AND NOT EXISTS (SELECT IH.* FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL IH 
                            WHERE  IH.SERVICIO_ID = Cn_IdServicio 
                            AND    IH.ACCION      = Lv_AccionHistRenovacion) 
           );

   -- Cursor que obtiene el punto a facturar según el id servicio enviado como parámetro.
   -- Costo Query: 10
   CURSOR C_GET_PTOS_FACTURAR(Cv_PrefijoEmpresa VARCHAR2,Cv_DescripcionProducto VARCHAR2, Cn_IdServicio NUMBER) IS
     SELECT DISTINCT ISE.PUNTO_FACTURACION_ID,IPER.OFICINA_ID
     FROM DB_COMERCIAL.INFO_SERVICIO            ISE
     JOIN DB_COMERCIAL.INFO_PUNTO               IPT  ON IPT.ID_PUNTO        = ISE.PUNTO_FACTURACION_ID             
     JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.ID_PERSONA_ROL = IPT.PERSONA_EMPRESA_ROL_ID
     JOIN DB_COMERCIAL.ADMI_PRODUCTO            PRO  ON PRO.ID_PRODUCTO     = ISE.PRODUCTO_ID
     JOIN DB_COMERCIAL.INFO_SERVICIO_HISTORIAL  ISH  ON ISE.ID_SERVICIO     = ISH.SERVICIO_ID
     WHERE ISE.ID_SERVICIO  = Cn_IdServicio;

  --Cursor que obtiene datos del servicio a facturar .
  --Costo Query: 5
  CURSOR C_GET_SERV_FACTURAR (  Cn_IdServicio      DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE) IS
     SELECT ISE.ID_SERVICIO,
            ISE.PRODUCTO_ID,
            ISE.PLAN_ID,
            ISE.PUNTO_ID,
            ISE.CANTIDAD,
            TRUNC(ISE.PRECIO_VENTA,2) AS PRECIO_VENTA,
            NVL(ISE.PORCENTAJE_DESCUENTO,0) AS  PORCENTAJE_DESCUENTO, 
            NVL(ISE.VALOR_DESCUENTO,0) AS  VALOR_DESCUENTO, 
            ISE.PUNTO_FACTURACION_ID, 
            ISE.ESTADO,
            ISH.FE_CREACION
     FROM DB_COMERCIAL.INFO_SERVICIO            ISE
     JOIN DB_COMERCIAL.ADMI_PRODUCTO            PRO  ON PRO.ID_PRODUCTO     = ISE.PRODUCTO_ID
     JOIN DB_COMERCIAL.INFO_SERVICIO_HISTORIAL  ISH  ON ISE.ID_SERVICIO     = ISH.SERVICIO_ID
     WHERE ISE.ID_SERVICIO  = Cn_IdServicio
     AND   ISE.ESTADO       = Lv_EstadoCancelado
     AND   ISH.ESTADO       = Lv_EstadoCancelado;


  Lv_EmpresaCod                 VARCHAR2(2)   := Pv_EmpresaCod;
  Lv_PrefijoEmpresa             VARCHAR2(5)   := Pv_PrefijoEmpresa;
  Lv_RangoConsumo               VARCHAR2(2000):='';
  Lv_Numeracion                 VARCHAR2(1000);
  Lv_Secuencia                  VARCHAR2(1000);
  Lv_FeActivacion               VARCHAR2(100);
  Ln_DescuentoFacProDetalle     NUMBER;
  Ln_PrecioVentaFacProDetalle   NUMBER;
  Ln_ValorImpuesto              NUMBER;
  Ln_Porcentaje                 NUMBER;
  Ln_IdImpuesto                 NUMBER;
  Ln_ContServicios              NUMBER;
  Ln_DescuentoCompensacion      NUMBER;
  Ln_IdOficina                  NUMBER;
  Ln_NumMesesRestantesFacturar  NUMBER := 0;
  Ln_NumMesesTotal              NUMBER := 0;
  Ln_NumMesesActivo             NUMBER := 0;
  Lr_AdmiParametroDet           DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE;
  Lr_InfoDocumentoFinancieroCab DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE;
  Lr_InfoDocumentoFinancieroHis DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE;
  Lr_InfoDocumentoFinancieroDet INFO_DOCUMENTO_FINANCIERO_DET%ROWTYPE;
  Lr_InfoDocumentoFinancieroImp INFO_DOCUMENTO_FINANCIERO_IMP%ROWTYPE;
  Ln_PorcentajeDescuento        DB_COMERCIAL.INFO_DETALLE_SOLICITUD.PORCENTAJE_DESCUENTO%TYPE;
  Ln_IdDetalleSolicitud         DB_COMERCIAL.INFO_DETALLE_SOLICITUD.ID_DETALLE_SOLICITUD%TYPE;
  Ln_Subtotal                   INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL%TYPE;
  Ln_SubtotalConImpuesto        INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_CON_IMPUESTO%TYPE;
  Ln_SubtotalDescuento          INFO_DOCUMENTO_FINANCIERO_CAB.SUBTOTAL_DESCUENTO%TYPE;
  Ln_ValorTotal                 INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE;
  Lv_DescripcionFacturaDet      INFO_DOCUMENTO_FINANCIERO_DET.OBSERVACIONES_FACTURA_DETALLE%TYPE := '';
  Ln_ValorNetlifeCloudFact      DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET.PRECIO_VENTA_FACPRO_DETALLE%TYPE  := 0;
  Lv_MsnError                   VARCHAR2(5000) := NULL;
  Le_Error                      EXCEPTION;
  Ld_FechaFinalCiclo            DATE;
  Lv_EsMatriz                   VARCHAR2(1)   := Lv_ValorS;
  Lv_EsOficinaFacturacion       VARCHAR2(1)   := Lv_ValorS;
  Lv_CodigoNumeracion           VARCHAR2(4)   := 'FACE';
  Lv_FechaFinPeriodo            VARCHAR2(100) := '';
  --Variables de la numeracion
  Lrf_Numeracion                FNKG_TYPES.Lrf_AdmiNumeracion;
  Lr_AdmiNumeracion             FNKG_TYPES.Lr_AdmiNumeracion;
  Lc_ParametrosCm               C_GET_PARAMETROS%ROWTYPE;
  Lc_FactProporcional           C_GET_FACT_PRO%ROWTYPE;
  Lc_FechaActivacion            C_FECHA_ACTIVACION%ROWTYPE;
  Lr_GetAdmiParamtrosDet        DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE;
  Lrf_GetAdmiParamtrosDet       SYS_REFCURSOR;

  BEGIN

    IF C_GET_PARAMETROS%ISOPEN THEN
      CLOSE C_GET_PARAMETROS;
    END IF;

    OPEN C_GET_PARAMETROS(Pv_EmpresaCod,Lv_ParametroPeriodoRenovacion,Lv_ModuloComercial,Lv_EstadoActivo);
     FETCH C_GET_PARAMETROS
        INTO Lc_ParametrosCm;
    CLOSE C_GET_PARAMETROS;

    Ln_NumMesesTotal := NVL(Lc_ParametrosCm.VALOR1,0);

    --
    -- Obtengo la descripción que debe ir mediante
    Lrf_GetAdmiParamtrosDet := FNCK_CONSULTS.F_GET_ADMI_PARAMETROS_DET(Lv_NombreParametroCab, 
                                                                       Lv_EstadoActivo, 
                                                                       Lv_EstadoActivo, 
                                                                       Pv_UsrCreacion,
                                                                       NULL, 
                                                                       NULL,
                                                                       Lv_PrefijoEmpresa
                                                                       );
    --
    FETCH Lrf_GetAdmiParamtrosDet INTO Lr_GetAdmiParamtrosDet;
    --
    CLOSE Lrf_GetAdmiParamtrosDet;

    IF Lr_GetAdmiParamtrosDet.ID_PARAMETRO_DET IS NOT NULL AND TRIM(Lr_GetAdmiParamtrosDet.VALOR2) IS NOT NULL THEN
      Lv_DescripcionFacturaDet := Lr_GetAdmiParamtrosDet.VALOR2;
    END IF;
        

    --Se obtiene el porcentaje IVA Activo
    SELECT PORCENTAJE_IMPUESTO INTO Ln_Porcentaje
      FROM DB_GENERAL.ADMI_IMPUESTO
     WHERE TIPO_IMPUESTO LIKE 'IVA%'
       AND ESTADO = Lv_EstadoActivo
       AND PORCENTAJE_IMPUESTO > 0
       AND ROWNUM = 1;

      --SE Obtienen los datos de la empresa
      FNCK_FACTURACION_MENSUAL.GET_PREFIJO_OFICINA(Lv_EmpresaCod,Lv_PrefijoEmpresa,Ln_IdOficina);

      FOR Lr_PuntosAFacturar IN C_GET_PTOS_FACTURAR(Pv_PrefijoEmpresa,
                                                    Lv_DescripcionProducto,
                                                    Pn_ServicioId)
      LOOP
        BEGIN

        Ln_ContServicios := 0;

        Lv_RangoConsumo  := '';


        FOR Lr_Servicios IN C_GET_SERV_FACTURAR (Pn_ServicioId)
        LOOP
          IF C_FECHA_ACTIVACION%ISOPEN THEN
            CLOSE C_FECHA_ACTIVACION;
          END IF;

          OPEN C_FECHA_ACTIVACION(Lr_Servicios.ID_SERVICIO);
           FETCH C_FECHA_ACTIVACION
              INTO Lc_FechaActivacion;
          CLOSE C_FECHA_ACTIVACION;

          IF C_GET_FE_FINPERIODO%ISOPEN THEN
            CLOSE C_GET_FE_FINPERIODO;
          END IF;

          IF C_GET_FACT_PRO%ISOPEN THEN
            CLOSE C_GET_FACT_PRO;
          END IF;

          OPEN C_GET_FE_FINPERIODO(Lr_Servicios.ID_SERVICIO,Ln_NumMesesTotal);
           FETCH C_GET_FE_FINPERIODO
              INTO Lv_FechaFinPeriodo;
          CLOSE C_GET_FE_FINPERIODO;


          OPEN C_GET_FACT_PRO(Lv_DescripcionProducto,Lr_Servicios.ID_SERVICIO);
           FETCH C_GET_FACT_PRO
              INTO Lc_FactProporcional;
          CLOSE C_GET_FACT_PRO;

          SELECT ROUND(MONTHS_BETWEEN(SYSDATE,Lc_FechaActivacion.FE_CREACION),1)
          INTO Ln_NumMesesActivo
          FROM DUAL;

          Ln_NumMesesActivo := ROUND(Ln_NumMesesActivo,0);

          IF Ln_NumMesesActivo < Ln_NumMesesTotal THEN
            -- OBTENGO MESES RESTANTES A FACTURAR
            Ln_NumMesesRestantesFacturar := Ln_NumMesesTotal - Ln_NumMesesActivo;
            Ln_NumMesesRestantesFacturar := ROUND(Ln_NumMesesRestantesFacturar,0);

            Lv_RangoConsumo := TO_CHAR((SYSDATE), 'DD MONTH YYYY','NLS_DATE_LANGUAGE=SPANISH') || ' AL ' || Lv_FechaFinPeriodo;



            IF Ln_ContServicios = 0 THEN
                --SE CREA LA CABECERA DEL DOCUMENTO DEL PUNTO A FACTURAR.
                Lr_InfoDocumentoFinancieroCab                       := NULL;
                Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO          := SEQ_INFO_DOC_FINANCIERO_CAB.NEXTVAL;
                Lr_InfoDocumentoFinancieroCab.OFICINA_ID            := Lr_PuntosAFacturar.OFICINA_ID;
                Lr_InfoDocumentoFinancieroCab.PUNTO_ID              := Lr_PuntosAFacturar.PUNTO_FACTURACION_ID;
                Lr_InfoDocumentoFinancieroCab.TIPO_DOCUMENTO_ID     := 1;
                Lr_InfoDocumentoFinancieroCab.ES_AUTOMATICA         := Lv_ValorS;
                Lr_InfoDocumentoFinancieroCab.PRORRATEO             := Lv_ValorN;
                Lr_InfoDocumentoFinancieroCab.REACTIVACION          := Lv_ValorN;
                Lr_InfoDocumentoFinancieroCab.RECURRENTE            := Lv_ValorN;
                Lr_InfoDocumentoFinancieroCab.COMISIONA             := Lv_ValorS;
                Lr_InfoDocumentoFinancieroCab.FE_CREACION           := SYSDATE;
                Lr_InfoDocumentoFinancieroCab.USR_CREACION          := Pv_UsrCreacion;
                Lr_InfoDocumentoFinancieroCab.ES_ELECTRONICA        := Lv_ValorS;
                Lr_InfoDocumentoFinancieroCab.MES_CONSUMO           := TO_CHAR(SYSDATE,'MM');
                Lr_InfoDocumentoFinancieroCab.ANIO_CONSUMO          := TO_CHAR(SYSDATE,'YYYY');
                Lr_InfoDocumentoFinancieroCab.RANGO_CONSUMO         := Lv_RangoConsumo;
                Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT := Lv_EstadoPendiente;

                FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
                IF Lv_MsnError IS NOT NULL THEN
                    RAISE Le_Error;
                END IF;
                --Con la informacion de cabecera se inserta el historial
                Lr_InfoDocumentoFinancieroHis                       := NULL;
                Lr_InfoDocumentoFinancieroHis.ID_DOCUMENTO_HISTORIAL:= SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL;
                Lr_InfoDocumentoFinancieroHis.DOCUMENTO_ID          := Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
                Lr_InfoDocumentoFinancieroHis.FE_CREACION           := SYSDATE;
                Lr_InfoDocumentoFinancieroHis.USR_CREACION          := Pv_UsrCreacion;
                Lr_InfoDocumentoFinancieroHis.ESTADO                := Lv_EstadoPendiente;
                Lr_InfoDocumentoFinancieroHis.OBSERVACION           := 'Se crea la factura por cancelacion de servicio NetlifeCloud';
                FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_HST(Lr_InfoDocumentoFinancieroHis,Lv_MsnError);
                IF Lv_MsnError IS NOT NULL THEN
                    RAISE Le_Error;
                END IF;                  
            END IF;

            Ln_ContServicios := Ln_ContServicios + 1;


            --Con los servicios verifico si posee descuento unico

            --Con los valores obtenidos procedo hacer los calculos para cada servicio
            Ln_ValorNetlifeCloudFact   := DB_FINANCIERO.FNCK_CANCELACION_VOL.F_GET_TOTAL_FACTURADO(Lv_DescripcionProducto,Lr_Servicios.ID_SERVICIO,Lc_FechaActivacion.FE_CREACION);
            Ln_PrecioVentaFacProDetalle:= 0;
            Ln_PrecioVentaFacProDetalle:= ROUND((NVL(Lr_Servicios.cantidad,0) * NVL(Lr_Servicios.precio_venta,0) * NVL(Ln_NumMesesTotal,0)),2)-ROUND(NVL(Ln_ValorNetlifeCloudFact,0),2);

            FNCK_FACTURACION_MENSUAL.GET_SOL_DESCT_UNICO(Lr_Servicios.id_servicio,Ln_IdDetalleSolicitud,Ln_PorcentajeDescuento);
            --Si posee porcentaje de descuento, realizo los calculos
            --Debo actualizar la solicitud
            IF Ln_PorcentajeDescuento IS NOT NULL AND Ln_PorcentajeDescuento>0 THEN
              FNCK_FACTURACION_MENSUAL.UPD_SOL_DESCT_UNICO(Ln_IdDetalleSolicitud);
              Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle *Ln_PorcentajeDescuento)/100,2);
            --Verifico si posee descuento fijo por porcentaje o valor; ya que este es el mandatorio  
            ELSIF Lr_Servicios.porcentaje_descuento>0 THEN
              Ln_DescuentoFacProDetalle := ROUND((Ln_PrecioVentaFacProDetalle*Lr_Servicios.porcentaje_descuento)/100,2);
            ELSIF Lr_Servicios.valor_descuento  >0 THEN
              Ln_DescuentoFacProDetalle := ROUND(Lr_Servicios.valor_descuento,2); 
            ELSE  
              Ln_DescuentoFacProDetalle := 0;
            END IF;

            --SE INSERTA EL DETALLE DEL DOCUMENTO
            --Calcula el valor del impuesto correspondiente al detalle
            Ln_ValorImpuesto := 0;
            Ln_ValorImpuesto := ((Ln_PrecioVentaFacProDetalle + Ln_DescuentoFacProDetalle)*Ln_Porcentaje/100);

            --Con el precio de venta nuevo procedemos a ingresar los valores del detalle
            Lr_InfoDocumentoFinancieroDet                            :=NULL;
            Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE             :=SEQ_INFO_DOC_FINANCIERO_DET.NEXTVAL;
            Lr_InfoDocumentoFinancieroDet.DOCUMENTO_ID               :=Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO;
            Lr_InfoDocumentoFinancieroDet.PUNTO_ID                   :=Lr_Servicios.punto_id;
            Lr_InfoDocumentoFinancieroDet.PLAN_ID                    :=Lr_Servicios.plan_id;
            Lr_InfoDocumentoFinancieroDet.CANTIDAD                   :=Lr_Servicios.cantidad;
            Lr_InfoDocumentoFinancieroDet.PRECIO_VENTA_FACPRO_DETALLE:=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.PORCETANJE_DESCUENTO_FACPRO:=Ln_PorcentajeDescuento;
            Lr_InfoDocumentoFinancieroDet.DESCUENTO_FACPRO_DETALLE   :=Ln_DescuentoFacProDetalle;
            Lr_InfoDocumentoFinancieroDet.VALOR_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.COSTO_FACPRO_DETALLE       :=ROUND(Ln_PrecioVentaFacProDetalle,2);
            Lr_InfoDocumentoFinancieroDet.FE_CREACION                :=SYSDATE;
            Lr_InfoDocumentoFinancieroDet.USR_CREACION               :=Pv_UsrCreacion;
            Lr_InfoDocumentoFinancieroDet.PRODUCTO_ID                :=Lr_Servicios.producto_id;
            Lr_InfoDocumentoFinancieroDet.SERVICIO_ID                :=Lr_Servicios.id_servicio;
            --Obtengo la Fe_activacion del servicio
            Lv_FeActivacion                                          :=FNCK_FACTURACION_MENSUAL.GET_FECHA_ACTIVACION(Lr_Servicios.id_servicio);

            Lr_InfoDocumentoFinancieroDet.OBSERVACIONES_FACTURA_DETALLE  := Lv_DescripcionFacturaDet;

            --SI EL DETALLE ES MAYOR A 0, SE INSERTA. CASO CONTRARIO CONTINÚA ITERANDO
            IF Ln_PrecioVentaFacProDetalle > 0 THEN
                FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_DET(Lr_InfoDocumentoFinancieroDet,Lv_MsnError);
            ELSE
                CONTINUE;
            END IF;
            IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
            END IF;

            --Con los valores de detalle insertado, podemos ingresar el impuesto
            Lr_InfoDocumentoFinancieroImp               :=NULL;
            Lr_InfoDocumentoFinancieroImp.ID_DOC_IMP    :=SEQ_INFO_DOC_FINANCIERO_IMP.NEXTVAL;
            Lr_InfoDocumentoFinancieroImp.DETALLE_DOC_ID:=Lr_InfoDocumentoFinancieroDet.ID_DOC_DETALLE;

            --Modificar funcion del impuesto
            --Debemos obtener el impuesto en base al porcentaje enviado en el arreglo
            Ln_IdImpuesto                               := FNCK_FACTURACION_MENSUAL.F_CODIGO_IMPUESTO_X_PORCEN(Ln_Porcentaje);
            --
            Lr_InfoDocumentoFinancieroImp.IMPUESTO_ID   :=Ln_IdImpuesto;
            Lr_InfoDocumentoFinancieroImp.VALOR_IMPUESTO:=Ln_ValorImpuesto;
            Lr_InfoDocumentoFinancieroImp.PORCENTAJE    :=Ln_Porcentaje;
            Lr_InfoDocumentoFinancieroImp.FE_CREACION   :=SYSDATE;
            Lr_InfoDocumentoFinancieroImp.USR_CREACION  :=Pv_UsrCreacion;
            FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_IMP(Lr_InfoDocumentoFinancieroImp,Lv_MsnError);
            IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
            END IF;
          END IF;
        END LOOP;

            --Se debe obtener las sumatorias de los Subtotales y se actualiza las cabeceras
            Ln_Subtotal              := 0;
            Ln_SubtotalDescuento     := 0;
            Ln_SubtotalConImpuesto   := 0;
            Ln_ValorTotal            := 0;
            Ln_DescuentoCompensacion := 0;

            Ln_Subtotal            := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_SUBTOTAL(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
            Ln_SubtotalDescuento   := ROUND( NVL(FNCK_FACTURACION_MENSUAL.F_SUMAR_DESCUENTO(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);
            Ln_SubtotalConImpuesto := ROUND( NVL(FNCK_FACTURACION_MENSUAL.P_SUMAR_IMPUESTOS(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO),0), 2);

            Ln_ValorTotal          := NVL( NVL(Ln_Subtotal, 0) - NVL(Ln_SubtotalDescuento, 2) - NVL(Ln_DescuentoCompensacion, 0) + NVL(Ln_SubtotalConImpuesto, 0), 0);

            --Actualizo los valores
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL               := Ln_Subtotal;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CERO_IMPUESTO := Ln_Subtotal;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_CON_IMPUESTO  := Ln_SubtotalConImpuesto;
            Lr_InfoDocumentoFinancieroCab.SUBTOTAL_DESCUENTO     := Ln_SubtotalDescuento;
            Lr_InfoDocumentoFinancieroCab.DESCUENTO_COMPENSACION := Ln_DescuentoCompensacion;
            Lr_InfoDocumentoFinancieroCab.VALOR_TOTAL            := Ln_ValorTotal;

            --Actualizo la numeracion y el estado
            IF Ln_ValorTotal >0 THEN
              Lrf_Numeracion:=FNCK_CONSULTS.F_GET_NUMERACION(Lv_PrefijoEmpresa,Lv_EsMatriz,Lv_EsOficinaFacturacion,Ln_IdOficina,Lv_CodigoNumeracion);
              --Debo recorrer la numeracion obtenida
              LOOP
                FETCH Lrf_Numeracion INTO Lr_AdmiNumeracion;
                EXIT
              WHEN Lrf_Numeracion%notfound;
                Lv_Secuencia :=LPAD(Lr_AdmiNumeracion.SECUENCIA,9,'0');
                Lv_Numeracion:=Lr_AdmiNumeracion.NUMERACION_UNO || '-'||Lr_AdmiNumeracion.NUMERACION_DOS||'-'||Lv_Secuencia;
              END LOOP;
              --Cierro la numeracion
              CLOSE Lrf_Numeracion;

              Lr_InfoDocumentoFinancieroCab.NUMERO_FACTURA_SRI   :=Lv_Numeracion;
              Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT:=Lv_EstadoPendiente;
              Lr_InfoDocumentoFinancieroCab.FE_EMISION           :=TRUNC(SYSDATE);

              --Actualizo los valores de la cabecera
              FNCK_TRANSACTION.UPDATE_INFO_DOC_FINANCIERO_CAB(Lr_InfoDocumentoFinancieroCab.ID_DOCUMENTO,Lr_InfoDocumentoFinancieroCab,Lv_MsnError);
              IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
              END IF;

              --Incremento la numeracion
              Lr_AdmiNumeracion.SECUENCIA:=Lr_AdmiNumeracion.SECUENCIA+1;
              FNCK_TRANSACTION.UPDATE_ADMI_NUMERACION(Lr_AdmiNumeracion.ID_NUMERACION,Lr_AdmiNumeracion,Lv_MsnError);
              IF Lv_MsnError IS NOT NULL THEN
                RAISE Le_Error;
              END IF;
            ELSE
                Lv_MsnError := 'La factura no tiene valores correctos PUNTO_FACTURACION_ID:' || Lr_InfoDocumentoFinancieroCab.PUNTO_ID;
                RAISE Le_Error;
            END IF;
            COMMIT;
        EXCEPTION
          WHEN Le_Error THEN
            ROLLBACK;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error: ' || Lv_MsnError,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
        END;
      END LOOP;
  EXCEPTION
    WHEN OTHERS THEN
      ROLLBACK;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           Lv_NombreProcedimiento,
                                           'Error ' || SQLCODE || ' -ERROR- ' || SQLERRM || ' - ERROR_STACK: '
                                                    || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpLocal));
  END P_FACT_OFFICE365_CANCEL;


  PROCEDURE P_UPDATE_AT_SERV_CARAC (Pr_InfoServicioCaracteristica IN  DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA%ROWTYPE,
                                    Pv_MsnError                   OUT VARCHAR2) AS
    PRAGMA AUTONOMOUS_TRANSACTION;
  BEGIN
    DB_COMERCIAL.COMEK_TRANSACTION.P_UPDATE_INFO_SERVICIO_CARAC(Pr_InfoServicioCaracteristica, Pv_MsnError);
    COMMIT;
  END P_UPDATE_AT_SERV_CARAC;

  FUNCTION F_WS_FECHA_PAGO_FACTURA(Fn_IdPersonaRol IN DB_FINANCIERO.INFO_PERSONA_EMPRESA_ROL.ID_PERSONA_ROL%TYPE,
                                   Fn_EmpresaCod   IN DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE)
    RETURN VARCHAR2
    IS
      Lv_FechaRetorna       VARCHAR2(60) := NULL;
      Lv_TipoFac            VARCHAR2(15) := 'FAC';
      Lv_TipoFacp           VARCHAR2(15) := 'FACP';
      Lv_EstadoPendiente    VARCHAR2(15) := 'Pendiente';
      Lv_EstadoAnulado      VARCHAR2(15) := 'Anulado' ;
      Lv_EstadoActivo       VARCHAR2(15) := 'Activo';
      Ld_fecha              DATE;
      Lv_Ciclo              VARCHAR2(30) := '';
      Lv_FormaPago          VARCHAR2(30) := '';
      Ln_DiasPago           NUMBER(10)   := 0;
      Ld_FechaPago          DATE;
      Lv_NombreParametro    VARCHAR2(60) := 'DIAS FECHA MAXIMA PAGO';
      Lv_DescEfectivo       VARCHAR2(60) := 'EFECTIVO';
      Lv_DescTarjetaDebBanc VARCHAR2(60) := 'TARJETA DE CREDITO-DEBITO BANCARIO';
      Lv_Modulo             VARCHAR2(60) := 'FINANCIERO';
   
    CURSOR C_ObtenerInfFacturacionCliente(Cn_idPersonaRol DB_FINANCIERO.INFO_PERSONA_EMPRESA_ROL.ID_PERSONA_ROL%TYPE)
    IS
      SELECT IDFC.FE_EMISION,
        DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_CANTON_FORMA_PAGO(IPER.ID_PERSONA_ROL, NULL)  FORMA_DE_PAGO,
        DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.F_INFO_CLIENTE_CICLOFAC('CICLO_FACTURACION',IPER.ID_PERSONA_ROL) CICLO_FACTURACION
      FROM DB_FINANCIERO.INFO_PERSONA_EMPRESA_ROL IPER,
        DB_FINANCIERO.INFO_CONTRATO IC,
        DB_COMERCIAL.INFO_PUNTO IP,
        DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,
        DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ITDF
      WHERE IDFC.PUNTO_ID            = IP.ID_PUNTO
      AND IDFC.TIPO_DOCUMENTO_ID     = ITDF.ID_TIPO_DOCUMENTO
      AND IP.PERSONA_EMPRESA_ROL_ID  = IPER.ID_PERSONA_ROL
      AND IC.PERSONA_EMPRESA_ROL_ID  = IPER.ID_PERSONA_ROL 
      AND IPER.ID_PERSONA_ROL        = Cn_idPersonaRol
      AND IPER.ESTADO                = 'Activo'
      AND IC.ESTADO                  = 'Activo'
      AND IDFC.ID_DOCUMENTO          = (SELECT MAX(CAB.ID_DOCUMENTO)
                                        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
                                          DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TD
                                        WHERE CAB.PUNTO_ID        = IP.ID_PUNTO
                                        AND CAB.TIPO_DOCUMENTO_ID = TD.ID_TIPO_DOCUMENTO
                                        AND TD.CODIGO_TIPO_DOCUMENTO IN (Lv_TipoFac,Lv_TipoFacp)
                                        AND CAB.ESTADO_IMPRESION_FACT NOT IN (Lv_EstadoAnulado,Lv_EstadoPendiente))
      AND IDFC.ESTADO_IMPRESION_FACT NOT IN (Lv_EstadoAnulado,Lv_EstadoPendiente)
      AND ITDF.CODIGO_TIPO_DOCUMENTO IN (Lv_TipoFac,Lv_TipoFacp)
      ORDER BY IDFC.FE_EMISION DESC;
        
    CURSOR C_ObtenerDiaLaborable(Cn_Fecha VARCHAR2, Cn_DiasPago NUMBER)
    IS
      SELECT
        CASE trim(' ' FROM to_char((to_date(Cn_fecha,'dd/mm/yyyy')),'DAY'))
          WHEN 'SUNDAY' THEN next_day(to_date(Cn_fecha,'dd/mm/yyyy'),'MONDAY')+Cn_DiasPago
          WHEN 'SATURDAY' THEN next_day(to_date(Cn_fecha,'dd/mm/yyyy'),'MONDAY')+Cn_DiasPago
        ELSE to_date(Cn_fecha,'dd/mm/yyyy')+Cn_DiasPago
        END
      FROM dual;
            
    CURSOR C_getParametros(Cn_EmpresaCod      DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE,
                           Cv_NombreParametro VARCHAR2,
                           Cv_Descripcion     VARCHAR2,
                           Cv_Modulo          VARCHAR2,
                           Cv_Estado          VARCHAR2) 
    IS
      SELECT to_number(DET.VALOR1)
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB,
        DB_GENERAL.ADMI_PARAMETRO_DET DET
      WHERE CAB.ID_PARAMETRO   = DET.PARAMETRO_ID
      AND CAB.MODULO           = Cv_Modulo
      AND DET.EMPRESA_COD      = Cn_EmpresaCod
      AND CAB.NOMBRE_PARAMETRO = Cv_NombreParametro
      AND CAB.ESTADO           = Cv_Estado
      AND DET.ESTADO           = Cv_Estado
      AND DET.DESCRIPCION      = Cv_descripcion;
    --
  BEGIN
    -- 
    IF C_ObtenerInfFacturacionCliente%ISOPEN THEN
      CLOSE C_ObtenerInfFacturacionCliente;
    END IF;
     --
    OPEN C_ObtenerInfFacturacionCliente(Fn_idPersonaRol);
    -- 
    FETCH C_ObtenerInfFacturacionCliente INTO Ld_Fecha,Lv_FormaPago,Lv_Ciclo;
    CLOSE C_ObtenerInfFacturacionCliente;   
          
      IF (Lv_Ciclo = 'Ciclo (I) - 1 al 30' or Lv_Ciclo = 'Ciclo (II) - 15 al 14') and 
         (Lv_FormaPago = 'DEBITO BANCARIO' or Lv_FormaPago = 'TARJETA DE CREDITO') THEN
           
        IF C_getParametros%ISOPEN THEN
          CLOSE C_getParametros;
        END IF;
         
        OPEN C_getParametros(Fn_EmpresaCod,
                             Lv_NombreParametro,
                             Lv_DescTarjetaDebBanc,
                             Lv_Modulo,
                             Lv_EstadoActivo);
                                  
        FETCH C_getParametros INTO Ln_DiasPago;
        CLOSE C_getParametros;
      
        IF C_ObtenerDiaLaborable%ISOPEN THEN
          CLOSE C_ObtenerDiaLaborable;
        END IF;

        Lv_FechaRetorna := to_char(Ld_Fecha,'dd/mm/yyyy');
      
        OPEN C_ObtenerDiaLaborable(Lv_FechaRetorna,Ln_DiasPago);       
        FETCH C_ObtenerDiaLaborable INTO Ld_FechaPago;
        CLOSE C_ObtenerDiaLaborable;
              
      ELSIF (Lv_Ciclo = 'Ciclo (I) - 1 al 30' or Lv_Ciclo = 'Ciclo (II) - 15 al 14') and 
                                                            Lv_FormaPago = 'EFECTIVO' THEN
        IF C_getParametros%ISOPEN THEN
          CLOSE C_getParametros;
        END IF;
         
        OPEN C_getParametros(Fn_EmpresaCod,
                             Lv_NombreParametro,
                             Lv_DescEfectivo,
                             Lv_Modulo,
                             Lv_EstadoActivo);
                                  
        FETCH C_getParametros INTO Ln_DiasPago;
        CLOSE C_getParametros;

        Ld_FechaPago  := Ld_Fecha+Ln_DiasPago;    
      END IF;
      --
      RETURN Ld_FechaPago;
      EXCEPTION
      WHEN OTHERS THEN
         
        DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('CONTABILIDAD', 'FNCK_FACTURACION.F_WS_FECHA_PAGO_FACTURA',SQLERRM);
        RETURN NULL; 
         
  END F_WS_FECHA_PAGO_FACTURA;

  PROCEDURE P_WS_ULTIMAS_FACTURAS_X_PUNTO(Pn_IdPunto      IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                          Pn_CantidadDocs IN  NUMBER,
                                          Prf_Result      OUT Lrf_Result,
                                          Pv_Status       OUT VARCHAR2,
                                          Pv_Mensaje      OUT VARCHAR2 )
  IS
    Lv_TipoFac         VARCHAR2(15)   := 'FAC';
    Lv_TipoFacp        VARCHAR2(15)   := 'FACP';
    Lv_EstadoPendiente VARCHAR2(15)   := 'Pendiente';
    Lv_EstadoAnulado   VARCHAR2(15)   := 'Anulado' ;
    Lv_Query           VARCHAR2(3500) := '';
    --
  BEGIN
     --
    Lv_Query := 'SELECT *
                 FROM (SELECT idfc.id_documento,
                         idfc.numero_factura_sri,
                         idfc.valor_total,
                         idfc.estado_impresion_fact,
                         idfc.fe_emision,
                         (CASE 
                          WHEN IDFD.PRODUCTO_ID IS NOT NULL AND IDFD.PRODUCTO_ID !=0  THEN 
                          (SELECT AP.DESCRIPCION_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.ID_PRODUCTO = IDFD.PRODUCTO_ID)  
                          WHEN IDFD.PLAN_ID IS NOT NULL AND IDFD.PLAN_ID !=0 THEN
                          (SELECT IPC.NOMBRE_PLAN FROM DB_COMERCIAL.INFO_PLAN_CAB IPC WHERE IPC.ID_PLAN = IDFD.PLAN_ID)
                          END)DESCRIPCION_PRODUCTO
                       FROM db_financiero.info_documento_financiero_cab idfc,
                         db_financiero.admi_tipo_documento_financiero   atdf,
                         DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET IDFD
                       WHERE idfc.punto_id        = :paramPunto
                       AND idfc.tipo_documento_id = atdf.id_tipo_documento
                       AND IDFD.DOCUMENTO_ID               = idfc.ID_DOCUMENTO
                       AND atdf.codigo_tipo_documento IN (:paramFac,:paramFacp)
                       AND idfc.estado_impresion_fact NOT IN (:paramAnulado,:paramPendiente)
                       ORDER BY idfc.fe_creacion DESC)
                 WHERE 
                 ROWNUM <= :paramCantidad';
                     
    IF Pn_IdPunto IS NOT NULL THEN
      OPEN Prf_Result FOR Lv_Query USING Pn_IdPunto, Lv_TipoFac, Lv_TipoFacp, Lv_EstadoPendiente,Lv_EstadoAnulado,Pn_CantidadDocs;
    END IF;

    Pv_Status  := 'OK';
    Pv_Mensaje := 'OK';
         
    EXCEPTION
    WHEN OTHERS THEN
      IF Prf_Result%ISOPEN THEN
        CLOSE Prf_Result;
      END IF;  
      
      Pv_Mensaje := DBMS_UTILITY.FORMAT_ERROR_STACK||'-'||DBMS_UTILITY.format_call_stack||chr(13);
      Pv_Status  := 'ERROR';
      DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('CONTABILIDAD', 'FNCK_FACTURACION.P_ULTIMAS_FACTURAS_X_PUNTO', Pv_Mensaje);
         
  END P_WS_ULTIMAS_FACTURAS_X_PUNTO;

  PROCEDURE P_WS_ULTIMOS_PAGOS_X_PUNTO(Pn_IdPunto      IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                       Pn_CantidadDocs IN  NUMBER,
                                       Prf_Result      OUT Lrf_Result,
                                       Pv_Status       OUT VARCHAR2,
                                       Pv_Mensaje      OUT VARCHAR2 )
  IS
    Lv_TipoPag        VARCHAR2(15)   := 'PAG';
    Lv_TipoAnt        VARCHAR2(15)   := 'ANT';
    Lv_TipoAnts       VARCHAR2(15)   := 'ANTS';
    Lv_TipoPagc       VARCHAR2(15)   := 'PAGC';
    Lv_TipoAntc       VARCHAR2(15)   := 'ANTC';
    Lv_EstadoCerrado  VARCHAR2(15)   := 'Cerrado';
    Lv_Query          VARCHAR2(3500) := '';
    --
  BEGIN
     --
    Lv_Query := 'SELECT *
                 FROM (SELECT ipc.numero_pago,
                         ipc.valor_total,
                         ipc.estado_pago,
                         ipc.fe_creacion
                       FROM db_financiero.info_pago_cab ipc,
                         db_financiero.admi_tipo_documento_financiero atdf
                       WHERE ipc.punto_id = :paramPunto
                       AND ipc.valor_total > 0
                       AND ipc.estado_pago       = :paramCerrado
                       AND ipc.tipo_documento_id = atdf.id_tipo_documento
                       AND atdf.codigo_tipo_documento IN (:paramPac,:paramAnt,:paramAnts,:paramPagc,:paramAntc)
                       ORDER BY ipc.fe_creacion DESC)
                 WHERE 
                 ROWNUM <= :paramCantidad';
                            
    IF Pn_IdPunto IS NOT NULL THEN
      OPEN Prf_Result FOR Lv_Query USING Pn_IdPunto, Lv_EstadoCerrado, Lv_TipoPag, Lv_TipoAnt, Lv_TipoAnts, Lv_TipoPagc, Lv_TipoAntc, Pn_CantidadDocs;
    END IF;

    Pv_Status  := 'OK';
    Pv_Mensaje := 'OK';
      
  EXCEPTION
  WHEN OTHERS THEN
    IF Prf_Result%ISOPEN THEN
      CLOSE Prf_Result;
    END IF;    

    Pv_Mensaje := DBMS_UTILITY.FORMAT_ERROR_STACK||'-'||DBMS_UTILITY.format_call_stack||chr(13);
    Pv_Status  := 'ERROR';
    DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('CONTABILIDAD', 'FNCK_FACTURACION.P_WS_ULTIMOS_PAGOS_X_PUNTO', Pv_Mensaje);
         
  END P_WS_ULTIMOS_PAGOS_X_PUNTO;

  PROCEDURE P_WS_TIPO_NEGOCIO_X_PUNTO(Pn_IdPunto IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                      Prf_Result OUT Lrf_Result,
                                      Pv_Status  OUT VARCHAR2,
                                      Pv_Mensaje OUT VARCHAR2 )
  IS
    Lv_Query VARCHAR2(3500) := '';
    --
  BEGIN
     --
    Lv_Query := 'SELECT atn.nombre_tipo_negocio 
                 FROM db_comercial.info_punto ip,
                   db_comercial.admi_tipo_negocio atn
                 WHERE ip.tipo_negocio_id = atn.id_tipo_negocio 
                 AND ip.id_punto          = :paramPunto';
                        
    IF Pn_IdPunto IS NOT NULL THEN
      OPEN Prf_Result FOR Lv_Query USING Pn_IdPunto;
    END IF;

    Pv_Status  := 'OK';
    Pv_Mensaje := 'OK';
      
  EXCEPTION
  WHEN OTHERS THEN
         
    Pv_Mensaje := DBMS_UTILITY.FORMAT_ERROR_STACK||'-'||DBMS_UTILITY.format_call_stack||chr(13);
    Pv_Status  := 'ERROR';
    DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('CONTABILIDAD', 'FNCK_FACTURACION.P_WS_TIPO_NEGOCIO_X_PUNTO', Pv_Mensaje);
         
  END P_WS_TIPO_NEGOCIO_X_PUNTO;

  PROCEDURE P_WS_FECHA_FIRMA_CONTRATO(Pn_IdPunto IN  DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
                                      Prf_Result OUT Lrf_Result,
                                      Pv_Status  OUT VARCHAR2,
                                      Pv_Mensaje OUT VARCHAR2)
  IS
    Lv_Producto     VARCHAR2(15)   := 'INTD';
    Lv_EstadoActivo VARCHAR2(15)   := 'Activo';
    Lv_Query        VARCHAR2(3500) := '';
    --
  BEGIN
     --
    Lv_Query := 'SELECT TO_CHAR(MIN(ish.fe_creacion),''YYYY-MM-DD'') FECHA_ACTIVACION
                 FROM db_comercial.info_servicio iser
                   left join db_comercial.info_servicio_historial ish on ish.servicio_id = iser.id_servicio
                   left join db_comercial.info_plan_det ipd on ipd.plan_id               = iser.plan_id
                   left join db_comercial.admi_producto ap on ap.id_producto             = ipd.producto_id
                 WHERE iser.punto_id    = :paramPunto
                 AND ap.codigo_producto = :paramProducto
                 AND ish.estado         = :paramActivo';
                        
    IF Pn_IdPunto IS NOT NULL THEN
      OPEN Prf_Result FOR Lv_Query USING Pn_IdPunto, Lv_Producto, Lv_EstadoActivo;
    END IF;
    
    Pv_Status  := 'OK';
    Pv_Mensaje := 'OK';
      
  EXCEPTION
  WHEN OTHERS THEN
         
    Pv_Mensaje := DBMS_UTILITY.FORMAT_ERROR_STACK||'-'||DBMS_UTILITY.format_call_stack||chr(13);
    Pv_Status  := 'ERROR';
    DB_FINANCIERO.FNCK_COM_ELECTRONICO_TRAN.INSERT_ERROR('CONTABILIDAD', 'FNCK_FACTURACION.P_WS_FECHA_FIRMA_CONTRATO', Pv_Mensaje);
         
  END P_WS_FECHA_FIRMA_CONTRATO;

  --
  PROCEDURE P_GET_VALOR_TOTAL_NC_BY_FACT(
      Pn_IdDocumento IN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE,
      Pn_Saldo  OUT NUMBER )
  IS
    --
    --CURSOR QUE RETORNA EL VALOR TOTAL DE NC APLICADAS A LA FACTURA Y VALOR TOTAL DE LA FACTURA.
    --COSTO QUERY: 9
    CURSOR C_GetValorTotalNcByFactura(Cn_IdDocumento DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE)
    IS
      --
      SELECT NVL(IDFC.VALOR_TOTAL, 0) VALOR_TOTAL_FAC, 
	           SUM(NVL(IDFC_NC.VALOR_TOTAL, 0)) VALOR_TOTAL_NC
	    FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC
	    LEFT JOIN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC_NC ON
	              IDFC_NC.REFERENCIA_DOCUMENTO_ID = IDFC.ID_DOCUMENTO AND IDFC_NC.ESTADO_IMPRESION_FACT IN ('Activo')
	    LEFT JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF ON
	      ATDF.ID_TIPO_DOCUMENTO = IDFC_NC.TIPO_DOCUMENTO_ID 
	      AND ATDF.ESTADO IN ('Activo') 
	      AND ATDF.CODIGO_TIPO_DOCUMENTO IN ('NC')
        WHERE IDFC.ID_DOCUMENTO = Cn_IdDocumento
        GROUP BY 
        IDFC.VALOR_TOTAL, 
        IDFC_NC.VALOR_TOTAL ;
      --

    Lrf_GetValorTotalNcByFactura C_GetValorTotalNcByFactura%ROWTYPE;
    Ln_ValorTotalNc DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE      := 0;
    Ln_ValorTotalFactura DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE := 0;
    Ln_ValorTotal DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.VALOR_TOTAL%TYPE        := 0;
    --
  BEGIN
    --
    IF C_GetValorTotalNcByFactura%ISOPEN THEN
      CLOSE C_GetValorTotalNcByFactura;
    END IF;
    --
    --
    OPEN C_GetValorTotalNcByFactura(Pn_IdDocumento);
    --
    FETCH C_GetValorTotalNcByFactura INTO Lrf_GetValorTotalNcByFactura;
    --
    CLOSE C_GetValorTotalNcByFactura;
    --
    --
    Ln_ValorTotalFactura := NVL(Lrf_GetValorTotalNcByFactura.VALOR_TOTAL_FAC, 0);
    Ln_ValorTotalNc	 := NVL(Lrf_GetValorTotalNcByFactura.VALOR_TOTAL_NC, 0);
    --
    -- Calcula el saldo disponible FAC - SUM(NC)
    Ln_ValorTotal := ROUND(NVL(Ln_ValorTotalFactura, 0), 2) - ROUND(NVL(Ln_ValorTotalNc, 0), 2);
    --
    Pn_Saldo := ROUND(NVL(Ln_ValorTotal, 0), 2);
    --
  EXCEPTION
  WHEN OTHERS THEN
    --
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'P_GET_VALOR_TOTAL_NC_BY_FACT',
                                          'FNCK_FACTURACION.P_GET_VALOR_TOTAL_NC_BY_FACT',
                                          'Error en FNCK_FACTURACION.P_GET_VALOR_TOTAL_NC_BY_FACT. Parametros (IdDocumento: '||Pn_IdDocumento||', ValorTotalFactura: '
                                          ||Ln_ValorTotalFactura||', ValorTotalNc: '||Ln_ValorTotalNc||', Saldo: '||Pn_Saldo||')' || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );

    --
  END P_GET_VALOR_TOTAL_NC_BY_FACT;

END FNCK_FACTURACION;
/
