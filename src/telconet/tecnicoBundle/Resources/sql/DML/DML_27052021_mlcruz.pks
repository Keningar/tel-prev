SET SERVEROUTPUT ON
--Script que da de baja las solicitudes asociadas a servicios Dual Band con Extender que ya no deben poder gestionarse desde la pantalla de PYL
DECLARE
  CURSOR Lc_SolsAReguDbExtender
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
      PUNTO.LOGIN,
      SERVICIO_ERROR_DB.ID_SERVICIO AS ID_SERVICIO_DB_ERROR,
      SERVICIO_ERROR_DB.ESTADO      AS ESTADO_SERVICIO_DB_ERROR,
      SOL.ID_DETALLE_SOLICITUD,
      SOL.ESTADO AS ESTADO_SOLICITUD
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_ERROR_DB
    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
    ON PUNTO.ID_PUNTO = SERVICIO_ERROR_DB.PUNTO_ID
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
    ON PRODUCTO.ID_PRODUCTO = SERVICIO_ERROR_DB.PRODUCTO_ID
    INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
    ON ST.SERVICIO_ID = SERVICIO_ERROR_DB.ID_SERVICIO
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_CLIENTE_ERROR_DB
    ON ELEMENTO_CLIENTE_ERROR_DB.ID_ELEMENTO = ST.ELEMENTO_CLIENTE_ID
    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL
    ON SOL.SERVICIO_ID = SERVICIO_ERROR_DB.ID_SERVICIO
    INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
    ON TIPO_SOL.ID_TIPO_SOLICITUD  = SOL.TIPO_SOLICITUD_ID
    WHERE PRODUCTO.NOMBRE_TECNICO IN ('WDB_Y_EDB', 'EXTENDER_DUAL_BAND')
    AND ELEMENTO_CLIENTE_ERROR_DB.NOMBRE_ELEMENTO NOT LIKE '%Extender%'
    AND SERVICIO_ERROR_DB.ESTADO NOT   IN ('Activo', 'In-Corte', 'Pendiente', 'PendienteAp', 'PendienteExtender')
    AND TIPO_SOL.DESCRIPCION_SOLICITUD IN ('SOLICITUD CAMBIO EQUIPO POR SOPORTE', 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
                                           'SOLICITUD CAMBIO DE MODEM INMEDIATO', 'SOLICITUD AGREGAR EQUIPO', 'SOLICITUD AGREGAR EQUIPO MASIVO')
    AND SOL.ESTADO                     IN ('Preplanificada', 'Planificada', 'Replanificada', 'Detenido', 'AsignadoTarea', 'Asignada' );

TYPE Lt_FetchArray
IS
  TABLE OF Lc_SolsAReguDbExtender%ROWTYPE;
  Lt_SolsAReguDbExtender Lt_FetchArray;
  Le_BulkErrors             EXCEPTION;
  PRAGMA                    EXCEPTION_INIT(Le_BulkErrors, -24381);
  Lv_Mensaje                VARCHAR2(4000);
  Lv_Proceso                VARCHAR2(20) := 'Regularizar';
  Ln_IndexCierraTareas      NUMBER;
BEGIN
  IF Lc_SolsAReguDbExtender%ISOPEN THEN
    CLOSE Lc_SolsAReguDbExtender;
  END IF;
  OPEN Lc_SolsAReguDbExtender;
  LOOP
    FETCH Lc_SolsAReguDbExtender BULK COLLECT
    INTO Lt_SolsAReguDbExtender LIMIT 1000;
    FORALL Ln_Index IN 1..Lt_SolsAReguDbExtender.COUNT SAVE EXCEPTIONS
    UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
    SET ESTADO                 = 'Eliminada'
    WHERE ID_DETALLE_SOLICITUD = Lt_SolsAReguDbExtender(Ln_Index).ID_DETALLE_SOLICITUD;
    FORALL Ln_Index IN 1..Lt_SolsAReguDbExtender.COUNT SAVE EXCEPTIONS
    INSERT
    INTO DB_COMERCIAL.INFO_DETALLE_SOL_HIST
      (
        ID_SOLICITUD_HISTORIAL,
        DETALLE_SOLICITUD_ID,
        ESTADO,
        OBSERVACION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_DETALLE_SOL_HIST.NEXTVAL,
        Lt_SolsAReguDbExtender(Ln_Index).ID_DETALLE_SOLICITUD,
        'Eliminada',
        'La solicitud pasa de estado '
        || Lt_SolsAReguDbExtender(Ln_Index).ESTADO_SOLICITUD
        || ' a Eliminada debido a que el servicio al que está asociado se encuentra en estado ' 
        || Lt_SolsAReguDbExtender(Ln_Index).ESTADO_SERVICIO_DB_ERROR,
        'regulaSolDbExt',
        SYSDATE,
        '127.0.0.1'
      );
    Ln_IndexCierraTareas := Lt_SolsAReguDbExtender.FIRST;
    WHILE (Ln_IndexCierraTareas IS NOT NULL)
    LOOP
      Lv_Mensaje    := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.INFRP_CIERRA_TAREAS_SOLICITUD( Lt_SolsAReguDbExtender(Ln_IndexCierraTareas).ID_DETALLE_SOLICITUD, 
                                                                            Lv_Proceso, 
                                                                            Lv_Mensaje);
      Ln_IndexCierraTareas := Lt_SolsAReguDbExtender.NEXT(Ln_IndexCierraTareas);
    END LOOP;
    EXIT
  WHEN Lc_SolsAReguDbExtender%NOTFOUND;
  END LOOP;
  CLOSE Lc_SolsAReguDbExtender;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se han regularizado las solicitudes asociadas a servicios Dual Band con Extender que ya no deben gestionarse');
EXCEPTION
WHEN Le_BulkErrors THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
/*
 * Script de regularización para aquellos servicios Dual Band con Extender que tengan errónea la data técnica 
 * y cuyo servicio de Internet se encuentre en Activo o en In-Corte.
 */
DECLARE
  CURSOR Lc_SolsAReguDbExtender(Cn_IdServicioDbError DB_COMERCIAL.INFO_DETALLE_SOLICITUD.ID_DETALLE_SOLICITUD%TYPE)
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
      PUNTO.LOGIN,
      SERVICIO_ERROR_DB.ID_SERVICIO AS ID_SERVICIO_DB_ERROR,
      SERVICIO_ERROR_DB.ESTADO      AS ESTADO_SERVICIO_DB_ERROR,
      SOL.ID_DETALLE_SOLICITUD,
      SOL.ESTADO AS ESTADO_SOLICITUD
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_ERROR_DB
    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
    ON PUNTO.ID_PUNTO = SERVICIO_ERROR_DB.PUNTO_ID
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
    ON PRODUCTO.ID_PRODUCTO = SERVICIO_ERROR_DB.PRODUCTO_ID
    INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
    ON ST.SERVICIO_ID = SERVICIO_ERROR_DB.ID_SERVICIO
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_CLIENTE_ERROR_DB
    ON ELEMENTO_CLIENTE_ERROR_DB.ID_ELEMENTO = ST.ELEMENTO_CLIENTE_ID
    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL
    ON SOL.SERVICIO_ID = SERVICIO_ERROR_DB.ID_SERVICIO
    INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
    ON TIPO_SOL.ID_TIPO_SOLICITUD  = SOL.TIPO_SOLICITUD_ID
    WHERE PRODUCTO.NOMBRE_TECNICO IN ('WDB_Y_EDB', 'EXTENDER_DUAL_BAND')
    AND ELEMENTO_CLIENTE_ERROR_DB.NOMBRE_ELEMENTO NOT LIKE '%Extender%'
    AND SERVICIO_ERROR_DB.ESTADO NOT   IN ('Activo', 'In-Corte', 'Pendiente', 'PendienteAp', 'PendienteExtender')
    AND TIPO_SOL.DESCRIPCION_SOLICITUD IN ('SOLICITUD CAMBIO EQUIPO POR SOPORTE', 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
                                           'SOLICITUD CAMBIO DE MODEM INMEDIATO', 'SOLICITUD AGREGAR EQUIPO', 'SOLICITUD AGREGAR EQUIPO MASIVO')
    AND SOL.ESTADO                     IN ('Preplanificada', 'Planificada', 'Replanificada', 'Detenido', 'AsignadoTarea', 'Asignada' )
    AND SERVICIO_ERROR_DB.ID_SERVICIO = Cn_IdServicioDbError;

  TYPE Lt_FetchArraySolRegu
  IS
    TABLE OF Lc_SolsAReguDbExtender%ROWTYPE;
  Lt_SolsAReguDbExtender    Lt_FetchArraySolRegu;
  Lv_Mensaje                VARCHAR2(4000);
  Lv_Proceso                VARCHAR2(20) := 'Regularizar';
  Ln_IndexSolReguDbExtender NUMBER;

  CURSOR Lc_ServiciosARegularizar
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
    PUNTO.LOGIN,
    NVL((SELECT 'SI'
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_ADIC_CON_EXTENDER
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD_ADICIONAL_CON_EXTENDER
    ON PROD_ADICIONAL_CON_EXTENDER.ID_PRODUCTO = SERVICIO_ADIC_CON_EXTENDER.PRODUCTO_ID
    WHERE SERVICIO_ADIC_CON_EXTENDER.PUNTO_ID = PUNTO.ID_PUNTO 
    AND SERVICIO_ADIC_CON_EXTENDER.ID_SERVICIO <> SERVICIO_ERROR_DB.ID_SERVICIO 
    AND SERVICIO_ADIC_CON_EXTENDER.ESTADO IN ('Activo', 'In-Corte', 'Pendiente', 'PendienteAp', 'PendienteExtender')
    AND PROD_ADICIONAL_CON_EXTENDER.NOMBRE_TECNICO IN ('WDB_Y_EDB', 'EXTENDER_DUAL_BAND')
    AND ROWNUM = 1), 'NO') AS TIENE_EXTENDERS_ADICIONALES,
    SERVICIO_ERROR_DB.ID_SERVICIO AS ID_SERVICIO_DB_ERROR,
    SERVICIO_ERROR_DB.TIPO_ORDEN  AS TIPO_ORDEN_SERVICIO_DB_ERROR,
    PRODUCTO.ID_PRODUCTO          AS ID_PRODUCTO_DB_ERROR,
    PRODUCTO.DESCRIPCION_PRODUCTO AS PRODUCTO_DB_ERROR,
    SERVICIO_ERROR_DB.ESTADO      AS ESTADO_SERVICIO_DB_ERROR,
    (SELECT APC.ID_PRODUCTO_CARACTERISITICA
    FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO AP
    ON AP.ID_PRODUCTO = APC.PRODUCTO_ID
    INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC
    ON AC.ID_CARACTERISTICA           = APC.CARACTERISTICA_ID
    WHERE AC.ESTADO                   = 'Activo'
    AND AP.ID_PRODUCTO                = PRODUCTO.ID_PRODUCTO
    AND AC.DESCRIPCION_CARACTERISTICA = 'MAC'
    AND ROWNUM                        = 1
    )                                                      AS ID_APC_MAC,
    ST.ID_SERVICIO_TECNICO                                 AS ID_SERVICIO_TECNICO_DB_ERROR,
    ELEMENTO_CLIENTE_ERROR_DB.ID_ELEMENTO                  AS ID_ELEM_CLIENTE_DB_ERROR,
    ELEMENTO_CLIENTE_ERROR_DB.NOMBRE_ELEMENTO              AS NOMBRE_ELEM_CLIENTE_DB_ERROR,
    ELEMENTO_CLIENTE_ERROR_DB.ESTADO                       AS ESTADO_ELEM_CLIENTE_DB_ERROR,
    INTERF_ELEM_CLIENTE_ERROR_DB.NOMBRE_INTERFACE_ELEMENTO AS NOMBRE_I_ELEM_CLIENTE_DB_ERROR,
    SERVICIO_INTERNET_ERROR_DB.ID_SERVICIO                 AS ID_SERVICIO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.PLAN_ID,
    NVL((SELECT 'SI'
    FROM DB_COMERCIAL.INFO_PLAN_DET PLAN_DET
    WHERE PLAN_DET.PLAN_ID = SERVICIO_INTERNET_ERROR_DB.PLAN_ID
    AND PLAN_DET.PRODUCTO_ID = PRODUCTO.ID_PRODUCTO
    AND PLAN_DET.ESTADO <> 'Eliminado'
    AND ROWNUM = 1), 'NO') AS TIENE_PLAN_DET_EXTENDER,
    SERVICIO_INTERNET_ERROR_DB.ESTADO_SERVICIO             AS ESTADO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.ID_ONT                      AS ID_ONT_SERVICIO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.NOMBRE_ONT                  AS NOMBRE_ONT_SERVICIO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.MODELO_ONT                  AS NOMBRE_MODELO_ONT_S_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.SERIE_FISICA                AS SERIE_ONT_SERVICIO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.ESTADO_ONT                  AS ESTADO_ONT_SERVICIO_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.ID_INTERFACE_ONT            AS ID_INTERFACE_ONT_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.NOMBRE_INTERFACE_ONT        AS NOMBRE_INTERFACE_ONT_INTERNET,
    SERVICIO_INTERNET_ERROR_DB.ESTADO_INTERFACE_ONT        AS ESTADO_INTERFACE_ONT_INTERNET,
    T_INFO_ELEMENTO_SGT.ID_ELEMENTO_SGT_ONT,
    T_INFO_ELEMENTO_SGT.NOMBRE_ELEMENTO_SGT_ONT,
    (SELECT DET_ELEM.DETALLE_VALOR
    FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DET_ELEM
    WHERE DET_ELEM.ELEMENTO_ID  = T_INFO_ELEMENTO_SGT.ID_ELEMENTO_SGT_ONT
    AND DET_ELEM.ESTADO         = 'Activo'
    AND DET_ELEM.DETALLE_NOMBRE = 'MAC'
    AND ROWNUM                  = 1
    ) AS MAC_ELEMENTO_SGT_ONT,
    T_INFO_ELEMENTO_SGT.ESTADO_ELEMENTO_SGT_ONT,
    T_INFO_ELEMENTO_SGT.ID_INTERFACE_ELEMENTO_SGT_ONT,
    T_INFO_ELEMENTO_SGT.NOMBRE_INTERFACE_ELEM_SGT_ONT,
    T_INFO_ELEMENTO_SGT.ESTADO_INTERFACE_ELEM_SGT_ONT
  FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_ERROR_DB
  INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
  ON PUNTO.ID_PUNTO = SERVICIO_ERROR_DB.PUNTO_ID
  INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
  ON PRODUCTO.ID_PRODUCTO = SERVICIO_ERROR_DB.PRODUCTO_ID
  INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
  ON ST.SERVICIO_ID = SERVICIO_ERROR_DB.ID_SERVICIO
  INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_CLIENTE_ERROR_DB
  ON ELEMENTO_CLIENTE_ERROR_DB.ID_ELEMENTO = ST.ELEMENTO_CLIENTE_ID
  INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERF_ELEM_CLIENTE_ERROR_DB
  ON INTERF_ELEM_CLIENTE_ERROR_DB.ID_INTERFACE_ELEMENTO = ST.INTERFACE_ELEMENTO_CLIENTE_ID
  INNER JOIN
    ( SELECT DISTINCT SERVICIO_INTERNET.ID_SERVICIO,
      SERVICIO_INTERNET.PLAN_ID,
      SERVICIO_INTERNET.ESTADO AS ESTADO_SERVICIO,
      PUNTO.ID_PUNTO,
      ONT.ID_ELEMENTO                   AS ID_ONT,
      ONT.NOMBRE_ELEMENTO               AS NOMBRE_ONT,
      MODELO_ONT.NOMBRE_MODELO_ELEMENTO AS MODELO_ONT,
      ONT.SERIE_FISICA,
      ONT.ESTADO                              AS ESTADO_ONT,
      INTERFACE_ONT.ID_INTERFACE_ELEMENTO     AS ID_INTERFACE_ONT,
      INTERFACE_ONT.NOMBRE_INTERFACE_ELEMENTO AS NOMBRE_INTERFACE_ONT,
      INTERFACE_ONT.ESTADO                    AS ESTADO_INTERFACE_ONT
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_INTERNET
    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
    ON PUNTO.ID_PUNTO = SERVICIO_INTERNET.PUNTO_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_CAB PLAN
    ON PLAN.ID_PLAN = SERVICIO_INTERNET.PLAN_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_DET PLAN_DET
    ON PLAN_DET.PLAN_ID = PLAN.ID_PLAN
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD_INTERNET_EN_PLAN
    ON PROD_INTERNET_EN_PLAN.ID_PRODUCTO = PLAN_DET.PRODUCTO_ID
    INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO SERVICIO_TECNICO_INTERNET
    ON SERVICIO_TECNICO_INTERNET.SERVICIO_ID = SERVICIO_INTERNET.ID_SERVICIO
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO OLT
    ON OLT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_OLT
    ON MODELO_OLT.ID_MODELO_ELEMENTO = OLT.MODELO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIPO_OLT
    ON TIPO_OLT.ID_TIPO_ELEMENTO = MODELO_OLT.TIPO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_OLT
    ON MARCA_OLT.ID_MARCA_ELEMENTO = MODELO_OLT.MARCA_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERFACE_CONECTOR
    ON INTERFACE_CONECTOR.ID_INTERFACE_ELEMENTO = SERVICIO_TECNICO_INTERNET.INTERFACE_ELEMENTO_CONECTOR_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_CONECTOR
    ON ELEMENTO_CONECTOR.ID_ELEMENTO = INTERFACE_CONECTOR.ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ONT
    ON ONT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_CLIENTE_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_ONT
    ON MODELO_ONT.ID_MODELO_ELEMENTO = ONT.MODELO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERFACE_ONT
    ON INTERFACE_ONT.ID_INTERFACE_ELEMENTO                              = SERVICIO_TECNICO_INTERNET.INTERFACE_ELEMENTO_CLIENTE_ID
    WHERE PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO                          = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD                               = '18'
    AND PLAN_DET.ESTADO                                                <> 'Eliminado'
    AND SERVICIO_INTERNET.ESTADO                                       IN ('Activo', 'In-Corte')
    ) SERVICIO_INTERNET_ERROR_DB ON SERVICIO_INTERNET_ERROR_DB.ID_PUNTO = PUNTO.ID_PUNTO
  LEFT JOIN
    (SELECT ENLACE.ID_ENLACE,
      ENLACE.INTERFACE_ELEMENTO_INI_ID            AS ID_INTERFACE_ONT,
      INTERFACE_SGT_ONT.ID_INTERFACE_ELEMENTO     AS ID_INTERFACE_ELEMENTO_SGT_ONT,
      INTERFACE_SGT_ONT.NOMBRE_INTERFACE_ELEMENTO AS NOMBRE_INTERFACE_ELEM_SGT_ONT,
      INTERFACE_SGT_ONT.ESTADO                    AS ESTADO_INTERFACE_ELEM_SGT_ONT,
      ELEMENTO_SGT_ONT.ID_ELEMENTO                AS ID_ELEMENTO_SGT_ONT,
      ELEMENTO_SGT_ONT.NOMBRE_ELEMENTO            AS NOMBRE_ELEMENTO_SGT_ONT,
      ELEMENTO_SGT_ONT.ESTADO                     AS ESTADO_ELEMENTO_SGT_ONT
    FROM DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE
    INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERFACE_SGT_ONT
    ON INTERFACE_SGT_ONT.ID_INTERFACE_ELEMENTO = ENLACE.INTERFACE_ELEMENTO_FIN_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_SGT_ONT
    ON ELEMENTO_SGT_ONT.ID_ELEMENTO                               = INTERFACE_SGT_ONT.ELEMENTO_ID
    WHERE ENLACE.ESTADO                                           = 'Activo'
    ) T_INFO_ELEMENTO_SGT ON T_INFO_ELEMENTO_SGT.ID_INTERFACE_ONT = SERVICIO_INTERNET_ERROR_DB.ID_INTERFACE_ONT
  WHERE PRODUCTO.NOMBRE_TECNICO                                  IN ('WDB_Y_EDB', 'EXTENDER_DUAL_BAND')
  AND ELEMENTO_CLIENTE_ERROR_DB.NOMBRE_ELEMENTO NOT LIKE '%Extender%';

  Ln_IdServicioInternet         DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE;
  Ln_IdPlanServicioInternet     DB_COMERCIAL.INFO_SERVICIO.PLAN_ID%TYPE;
  Lv_TienePlanDetExtender       VARCHAR2(2);
  Lv_EstadoServicioInternet     DB_COMERCIAL.INFO_SERVICIO.ESTADO%TYPE;
  Lv_Login                      DB_COMERCIAL.INFO_PUNTO.LOGIN%TYPE;
  Lv_TieneExtendersAdicionales  VARCHAR2(2);
  Ln_IdOntServicioInternet      DB_INFRAESTRUCTURA.INFO_ELEMENTO.ID_ELEMENTO%TYPE;
  Lv_NombreOntServicioInternet  DB_INFRAESTRUCTURA.INFO_ELEMENTO.NOMBRE_ELEMENTO%TYPE;
  Lv_SerieOntServicioInternet   DB_INFRAESTRUCTURA.INFO_ELEMENTO.SERIE_FISICA%TYPE;
  Lv_EstadoOntServicioInternet  DB_INFRAESTRUCTURA.INFO_ELEMENTO.ESTADO%TYPE;
  Ln_IdInterfaceOntInternet     DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.ID_INTERFACE_ELEMENTO%TYPE;
  Lv_NombreInterfaceOntInternet DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.NOMBRE_INTERFACE_ELEMENTO%TYPE;
  Lv_EstadoInterfaceOntInternet DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.ESTADO%TYPE;

  Ln_IdServicioTecnicoDbError   DB_COMERCIAL.INFO_SERVICIO_TECNICO.ID_SERVICIO_TECNICO%TYPE;

  Ln_IdServicioDbError          DB_COMERCIAL.INFO_SERVICIO.ID_SERVICIO%TYPE;
  Lv_ProductoDbError            DB_COMERCIAL.ADMI_PRODUCTO.DESCRIPCION_PRODUCTO%TYPE;
  Lv_EstadoServicioDbError      DB_COMERCIAL.INFO_SERVICIO.ESTADO%TYPE;
  Ln_IdApcMac                   DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA%TYPE;
  Ln_IdElemClienteDbError       DB_INFRAESTRUCTURA.INFO_ELEMENTO.ID_ELEMENTO%TYPE;
  Lv_NombreElemClienteDbError   DB_INFRAESTRUCTURA.INFO_ELEMENTO.NOMBRE_ELEMENTO%TYPE;
  Lv_NombreIElemClienteDbError  DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.NOMBRE_INTERFACE_ELEMENTO%TYPE;

  Ln_IdElementoSgtOnt           DB_INFRAESTRUCTURA.INFO_ELEMENTO.ID_ELEMENTO%TYPE;
  Lv_NombreElementoSgtOnt       DB_INFRAESTRUCTURA.INFO_ELEMENTO.NOMBRE_ELEMENTO%TYPE;
  Lv_MacElementoSgtOnt          VARCHAR2(4000);
  Lv_EstadoElementoSgtOnt       DB_INFRAESTRUCTURA.INFO_ELEMENTO.ESTADO%TYPE;
  Ln_IdInterfaceElementoSgtOnt  DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.ID_INTERFACE_ELEMENTO%TYPE;
  Lv_NombreInterfaceElemSgtOnt  DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.NOMBRE_INTERFACE_ELEMENTO%TYPE;
  Lv_EstadoInterfaceElemSgtOnt  DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO.ESTADO%TYPE;

  Lv_CreaHistoServicioInternet  VARCHAR2(2);
  Lcl_ObservServicioInternet    CLOB;
  Lcl_ObservServicioDbError     CLOB;
  Lcl_InfoReguServicioPrint     CLOB;
  Lv_Separador                  VARCHAR2(2) := '|';
  Lv_RegularizaSols             VARCHAR2(2);
  TYPE Lt_FetchArray
  IS
    TABLE OF Lc_ServiciosARegularizar%ROWTYPE;
  Lt_ServiciosARegularizar    Lt_FetchArray;
  Ln_IndexServiciosARegularizar NUMBER;
BEGIN
  SYS.DBMS_OUTPUT.PUT_LINE('LOGIN' || Lv_Separador 
                            || 'ID_SERVICIO_INTERNET' || Lv_Separador 
                            || 'ID_PLAN_INTERNET' || Lv_Separador 
                            || 'ESTADO_SERVICIO_INTERNET' || Lv_Separador 
                            || 'PLAN_INCLUYE_EXTENDER' || Lv_Separador 
                            || 'PUNTO_TIENE_OTROS_EXTENDERS' || Lv_Separador 
                            || 'ID_SERVICIO_DB_ERROR' || Lv_Separador 
                            || 'PRODUCTO_DB_ERROR' || Lv_Separador 
                            || 'ESTADO_SERVICIO_DB_ERROR' || Lv_Separador 
                            || 'REGULARIZA_ESTADO_ONT' || Lv_Separador 
                            || 'OBS_REGULARIZA_ESTADO_ONT' || Lv_Separador 
                            || 'REGULARIZA_ESTADO_INTERFACE_ONT' || Lv_Separador 
                            || 'OBS_REGULARIZA_ESTADO_INTERFACE_ONT' || Lv_Separador 
                            || 'VALIDACION_ESTADO_SERVICIO_DB_ERROR_PERMITIDA' || Lv_Separador 
                            || 'OBS_VALIDACION_ESTADO_SERVICIO_DB_ERROR_PERMITIDA' || Lv_Separador 
                            || 'VALIDACION_DATA_TECNICA_PERMITIDA' || Lv_Separador 
                            || 'OBS_VALIDACION_DATA_TECNICA_PERMITIDA' || Lv_Separador 
                            || 'VALIDA_ELIMINACION_SPC' || Lv_Separador 
                            || 'OBS_VALIDA_ELIMINACION_SPC' || Lv_Separador 
                            || 'ACTUALIZA_DATA_TECNICA' || Lv_Separador 
                            || 'OBS_ACTUALIZA_DATA_TECNICA' || Lv_Separador 
                            || 'ACTUALIZA_SOLS_Y_TAREAS' || Lv_Separador 
                            || 'OBS_ACTUALIZA_SOLS_Y_TAREAS');
  IF Lc_ServiciosARegularizar%ISOPEN THEN
    CLOSE Lc_ServiciosARegularizar;
  END IF;
  OPEN Lc_ServiciosARegularizar;
  LOOP
    FETCH Lc_ServiciosARegularizar BULK COLLECT
    INTO Lt_ServiciosARegularizar LIMIT 1000;
    /*
     * Verifico si el id del ont del servicio de Internet es igual al id del elemento cliente del servicio dual band y en caso de ser así,
     * se verifica si el estado del ont del servicio de Internet es Activo y si no se procede a actualizarlo a dicho estado.
     * Además se verifica el estado de la interface del ont asociado al servicio de Internet que debería estar en connected y el resto
     * en not connect y si no es así se procede a actualizarlo
     */
    Ln_IndexServiciosARegularizar := Lt_ServiciosARegularizar.FIRST;
    WHILE (Ln_IndexServiciosARegularizar IS NOT NULL)
    LOOP
      Ln_IdServicioInternet         := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_SERVICIO_INTERNET;
      Ln_IdPlanServicioInternet     := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).PLAN_ID;
      Lv_TienePlanDetExtender       := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).TIENE_PLAN_DET_EXTENDER;
      Lv_EstadoServicioInternet     := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_INTERNET;
      Lv_Login                      := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).LOGIN;
      Lv_TieneExtendersAdicionales  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).TIENE_EXTENDERS_ADICIONALES;
      Ln_IdOntServicioInternet      := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_ONT_SERVICIO_INTERNET;
      Lv_NombreOntServicioInternet  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_ONT_SERVICIO_INTERNET;
      Lv_SerieOntServicioInternet   := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).SERIE_ONT_SERVICIO_INTERNET;
      Lv_EstadoOntServicioInternet  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_ONT_SERVICIO_INTERNET;
      Ln_IdInterfaceOntInternet     := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_INTERFACE_ONT_INTERNET;
      Lv_NombreInterfaceOntInternet := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_INTERFACE_ONT_INTERNET;
      Lv_EstadoInterfaceOntInternet := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_INTERFACE_ONT_INTERNET;

      Ln_IdServicioTecnicoDbError   := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_SERVICIO_TECNICO_DB_ERROR;

      Ln_IdServicioDbError          := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_SERVICIO_DB_ERROR;
      Lv_ProductoDbError            := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).PRODUCTO_DB_ERROR;
      Ln_IdApcMac                   := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_APC_MAC;
      Lv_EstadoServicioDbError      := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_SERVICIO_DB_ERROR;
      Ln_IdElemClienteDbError       := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_ELEM_CLIENTE_DB_ERROR;
      Lv_NombreElemClienteDbError   := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_ELEM_CLIENTE_DB_ERROR;
      Lv_NombreIElemClienteDbError  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_I_ELEM_CLIENTE_DB_ERROR;

      Ln_IdElementoSgtOnt           := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_ELEMENTO_SGT_ONT;
      Lv_NombreElementoSgtOnt       := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_ELEMENTO_SGT_ONT;
      Lv_MacElementoSgtOnt          := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).MAC_ELEMENTO_SGT_ONT;
      Lv_EstadoElementoSgtOnt       := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_ELEMENTO_SGT_ONT;
      Ln_IdInterfaceElementoSgtOnt  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ID_INTERFACE_ELEMENTO_SGT_ONT;
      Lv_NombreInterfaceElemSgtOnt  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).NOMBRE_INTERFACE_ELEM_SGT_ONT;
      Lv_EstadoInterfaceElemSgtOnt  := Lt_ServiciosARegularizar(Ln_IndexServiciosARegularizar).ESTADO_INTERFACE_ELEM_SGT_ONT;

      Lv_CreaHistoServicioInternet  := 'NO';
      Lcl_ObservServicioInternet    := '';
      Lcl_ObservServicioDbError     := '';
      Lv_RegularizaSols             := 'NO';

      Lcl_InfoReguServicioPrint := Lv_Login || Lv_Separador || Ln_IdServicioInternet || Lv_Separador || Ln_IdPlanServicioInternet || Lv_Separador
                                    || Lv_EstadoServicioInternet  || Lv_Separador || Lv_TienePlanDetExtender || Lv_Separador 
                                    || Lv_TieneExtendersAdicionales || Lv_Separador 
                                    || Ln_IdServicioDbError  || Lv_Separador || Lv_ProductoDbError || Lv_Separador
                                    || Lv_EstadoServicioDbError;

      IF Ln_IdOntServicioInternet =  Ln_IdElemClienteDbError THEN
        IF Lv_EstadoOntServicioInternet <> 'Activo' THEN
          UPDATE DB_INFRAESTRUCTURA.INFO_ELEMENTO
          SET ESTADO = 'Activo'
          WHERE ID_ELEMENTO = Ln_IdOntServicioInternet
          AND ESTADO <> 'Activo';
          IF SQL%ROWCOUNT = 1 THEN
            INSERT 
            INTO DB_INFRAESTRUCTURA.INFO_ELEMENTO_TRAZABILIDAD
            ( 
              ID_TRAZABILIDAD,
              NUMERO_SERIE,
              COD_EMPRESA,
              ESTADO_TELCOS,
              ESTADO_NAF,
              ESTADO_ACTIVO,
              UBICACION,
              LOGIN,
              RESPONSABLE,
              OFICINA_ID,
              OBSERVACION,
              USR_CREACION,
              FE_CREACION,
              FE_CREACION_NAF,
              TRANSACCION,
              IP_CREACION
            )
            VALUES
            ( 
              DB_INFRAESTRUCTURA.SEQ_INFO_ELEMENTO_TRAZABILIDAD.NEXTVAL,
              Lv_SerieOntServicioInternet,
              '18',
              'Activo',
              'Instalado',
              'Activo',
              'Cliente',
              Lv_Login,
              NULL,
              0,
              NULL,
              'reguStDualBand',
              SYSDATE,
              SYSDATE,
              'Regularización del estado del elemento por error de equipos Extender Dual Band',
              '127.0.0.1'
            );

            INSERT
            INTO DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO
            (
              ID_HISTORIAL,
              ELEMENTO_ID,
              ESTADO_ELEMENTO,
              OBSERVACION,
              USR_CREACION,
              FE_CREACION,
              IP_CREACION
            )
            VALUES
            (
              DB_INFRAESTRUCTURA.SEQ_INFO_HISTORIAL_ELEMENTO.NEXTVAL,
              Ln_IdOntServicioInternet,
              'Activo',
              'Se actualiza el estado del elemento de ' || Lv_EstadoOntServicioInternet || ' a Activo por servicio de Internet con ID ' 
              || Ln_IdServicioInternet || ' del punto ' || Lv_Login,
              'reguStDualBand',
              SYSDATE,
              '127.0.0.1'
            );
            Lv_CreaHistoServicioInternet  := 'SI';
          END IF;
          Lcl_ObservServicioInternet    := Lcl_ObservServicioInternet 
                                           || 'Se actualiza el estado del ont ' || Lv_NombreOntServicioInternet 
                                           || ' de ' || Lv_EstadoOntServicioInternet || ' a Activo<br/>';
          Lcl_InfoReguServicioPrint     := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' || Lv_Separador 
                                            || 'Se actualiza el estado del ont con ID ' || Ln_IdOntServicioInternet
                                            || ' de ' || Lv_EstadoOntServicioInternet || ' a Activo';
        ELSE
          Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' || Lv_Separador || '';
        END IF;
        
        IF Lv_EstadoInterfaceOntInternet <> 'connected' THEN
          UPDATE DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO
          SET ESTADO = 'connected',
          USR_ULT_MOD = 'reguStDualBand',
          FE_ULT_MOD = SYSDATE
          WHERE ID_INTERFACE_ELEMENTO = Ln_IdInterfaceOntInternet
          AND ESTADO <> 'connected';
          IF SQL%ROWCOUNT = 1 THEN
            UPDATE DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO
            SET ESTADO = 'not connect',
            USR_ULT_MOD = 'reguStDualBand',
            FE_ULT_MOD = SYSDATE
            WHERE ELEMENTO_ID = Ln_IdOntServicioInternet
            AND ID_INTERFACE_ELEMENTO <> Ln_IdInterfaceOntInternet;
            Lv_CreaHistoServicioInternet  := 'SI';
          END IF;
          Lcl_ObservServicioInternet    := Lcl_ObservServicioInternet 
                                           || 'Se actualiza el estado de la interface ' || Lv_NombreInterfaceOntInternet 
                                           || ' del ont ' || Lv_NombreOntServicioInternet 
                                           || ' de ' || Lv_EstadoInterfaceOntInternet || ' a connected<br/>';
          Lcl_InfoReguServicioPrint     := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' || Lv_Separador 
                                            || 'Se actualiza el estado de la interface del ont con ID ' || Ln_IdInterfaceOntInternet
                                            || ' de ' || Lv_EstadoInterfaceOntInternet || ' a connected';
        ELSE
          Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' || Lv_Separador || '';
        END IF;

        IF Lv_CreaHistoServicioInternet = 'SI' THEN
          INSERT 
          INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
          (
            ID_SERVICIO_HISTORIAL,
            SERVICIO_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION,
            ESTADO,
            OBSERVACION
          )
          VALUES
          (
            DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL,
            Ln_IdServicioInternet,
            'reguStDualBand',
            SYSDATE,
            '127.0.0.1',
            Lv_EstadoServicioInternet,
            Lcl_ObservServicioInternet
          );
        END IF;
      ELSE
        Lcl_InfoReguServicioPrint   := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' || Lv_Separador || ''
                                        || Lv_Separador || 'NO' || Lv_Separador || '';
      END IF;
      
      /*
       * 2. Se verifica si el servicio técnico de W+AP o Extender debe ser regularizado ya que actualmente tiene atado otro elemento 
       * que no es un Extender y cuyo punto tiene un plan de Internet sin incluir un Extender y no existen más servicios adicionales 
       * asociados a un Extender
       *
       */
      IF (Lv_EstadoServicioDbError = 'Pendiente' OR Lv_EstadoServicioDbError = 'Activo' OR Lv_EstadoServicioDbError = 'In-Corte'
          OR Lv_EstadoServicioDbError = 'PendienteAp' OR  Lv_EstadoServicioDbError = 'PendienteExtender' ) THEN
        --Si tengo la información técnica registrada por medio de enlaces, procedo a regularizar
        Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' || Lv_Separador || 
                                     'Servicio con error que incluye Extender en estado ' || Lv_EstadoServicioDbError || ' si es permitido';
        IF (Lv_TienePlanDetExtender = 'NO' AND Lv_TieneExtendersAdicionales = 'NO'
            AND Ln_IdElementoSgtOnt IS NOT NULL AND Ln_IdInterfaceElementoSgtOnt IS NOT NULL AND Lv_NombreElementoSgtOnt LIKE '%Extender%') THEN
          Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' 
                                       || Lv_Separador || 'Servicio tiene información técnica válida';
          UPDATE DB_COMERCIAL.INFO_SERVICIO_TECNICO
          SET ELEMENTO_CLIENTE_ID = Ln_IdElementoSgtOnt,
          INTERFACE_ELEMENTO_CLIENTE_ID = Ln_IdInterfaceElementoSgtOnt,
          ELEMENTO_ID = NULL,
          INTERFACE_ELEMENTO_ID = NULL,
          ELEMENTO_CONTENEDOR_ID = NULL,
          ELEMENTO_CONECTOR_ID = NULL,
          INTERFACE_ELEMENTO_CONECTOR_ID = NULL
          WHERE ID_SERVICIO_TECNICO = Ln_IdServicioTecnicoDbError;

          IF (Lv_EstadoServicioDbError <> 'Pendiente' OR Lv_EstadoServicioDbError = 'PendienteAp' OR  Lv_EstadoServicioDbError = 'PendienteExtender')
          THEN
            --Están aún por trasladarse, por lo que no debe borrar las características
            UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
            SET ESTADO = 'Eliminado',
            FE_ULT_MOD = SYSDATE,
            USR_ULT_MOD = 'regulaSolDbExt'
            WHERE SERVICIO_ID = Ln_IdServicioDbError
            AND ESTADO = 'Activo';
            Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' 
                                         || Lv_Separador || 'Se eliminan las características del servicio';
          ELSE
            Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO'
                                         || Lv_Separador || 'No se eliminan las características del servicio ya que se encuentra en estado Pendiente';
          END IF;

          INSERT
          INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
          (
            ID_SERVICIO_PROD_CARACT,
            SERVICIO_ID,
            PRODUCTO_CARACTERISITICA_ID,
            VALOR,
            FE_CREACION,
            USR_CREACION,
            ESTADO
          )
          VALUES
          (
            DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
            Ln_IdServicioDbError,
            Ln_IdApcMac,--MAC
            Lv_MacElementoSgtOnt,
            CURRENT_TIMESTAMP,
            'regulaSolDbExt',
            'Activo'
          );

          Lcl_InfoReguServicioPrint     := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' 
                                           || Lv_Separador || 'Se actualiza la data técnica. Elemento Cliente: ' || Lv_NombreElementoSgtOnt 
                                           || ', Interface Elemento Cliente: ' || Lv_NombreInterfaceElemSgtOnt
                                           || ', MAC: ' || Lv_MacElementoSgtOnt ;

          Lcl_ObservServicioDbError     := Lcl_ObservServicioDbError 
                                           || 'Se actualiza la información técnica del servicio<br/>'
                                           || 'Elemento Cliente Anterior: ' || Lv_NombreElemClienteDbError|| '<br/>'
                                           || 'Interface Elemento Cliente Anterior: ' || Lv_NombreIElemClienteDbError || '<br/>'
                                           || 'Elemento Cliente Nuevo: ' || Lv_NombreElementoSgtOnt || '<br/>'
                                           || 'Interface Elemento Cliente Nuevo: ' || Lv_NombreInterfaceElemSgtOnt || '<br/>'
                                           || 'MAC: ' || Lv_MacElementoSgtOnt || '<br/>';
          INSERT 
          INTO DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
          (
            ID_SERVICIO_HISTORIAL,
            SERVICIO_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION,
            ESTADO,
            OBSERVACION
          )
          VALUES
          (
            DB_COMERCIAL.SEQ_INFO_SERVICIO_HISTORIAL.NEXTVAL,
            Ln_IdServicioDbError,
            'reguStDualBand',
            SYSDATE,
            '127.0.0.1',
            Lv_EstadoServicioDbError,
            Lcl_ObservServicioDbError
          );

          IF Lc_SolsAReguDbExtender%ISOPEN THEN
            CLOSE Lc_SolsAReguDbExtender;
          END IF;
          OPEN Lc_SolsAReguDbExtender(Ln_IdServicioDbError);
          LOOP
            FETCH Lc_SolsAReguDbExtender BULK COLLECT
            INTO Lt_SolsAReguDbExtender LIMIT 100;
            Ln_IndexSolReguDbExtender := Lt_SolsAReguDbExtender.FIRST;
            WHILE (Ln_IndexSolReguDbExtender IS NOT NULL)
            LOOP
              Lv_Mensaje        := '';
              Lv_RegularizaSols := 'SI';
              UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
              SET ESTADO                 = 'Eliminada'
              WHERE ID_DETALLE_SOLICITUD = Lt_SolsAReguDbExtender(Ln_IndexSolReguDbExtender).ID_DETALLE_SOLICITUD;
              INSERT
              INTO DB_COMERCIAL.INFO_DETALLE_SOL_HIST
              (
                ID_SOLICITUD_HISTORIAL,
                DETALLE_SOLICITUD_ID,
                ESTADO,
                OBSERVACION,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION
              )
              VALUES
              (
                DB_COMERCIAL.SEQ_INFO_DETALLE_SOL_HIST.NEXTVAL,
                Lt_SolsAReguDbExtender(Ln_IndexSolReguDbExtender).ID_DETALLE_SOLICITUD,
                'Eliminada',
                'La solicitud pasa de estado '
                || Lt_SolsAReguDbExtender(Ln_IndexSolReguDbExtender).ESTADO_SOLICITUD
                || ' a Eliminada por regularización de la información técnica del servicio',
                'regulaSolDbExt',
                SYSDATE,
                '127.0.0.1'
              );
              DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.INFRP_CIERRA_TAREAS_SOLICITUD( 
                                                                            Lt_SolsAReguDbExtender(Ln_IndexSolReguDbExtender).ID_DETALLE_SOLICITUD,
                                                                            Lv_Proceso, 
                                                                            Lv_Mensaje);
              Ln_IndexSolReguDbExtender := Lt_SolsAReguDbExtender.NEXT(Ln_IndexSolReguDbExtender);
            END LOOP;
            EXIT
          WHEN Lc_SolsAReguDbExtender%NOTFOUND;
          END LOOP;
          CLOSE Lc_SolsAReguDbExtender;

          IF Lv_RegularizaSols = 'SI' THEN
            Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'SI' 
                                         || Lv_Separador || 'Se eliminan las solicitudes asociadas al servicio Dual Band con error';
          ELSE
            Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' 
                                         || Lv_Separador || 'No existen solicitudes asociadas al servicio Dual Band con error';
          END IF;
        ELSE
          --No tiene información del extender o puede que existan adicionales o forme parte de un plan
          Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' 
                                       || Lv_Separador || 'Servicio no tiene información técnica válida'
                                       || Lv_Separador || 'NO' || Lv_Separador || ''
                                       || Lv_Separador || 'NO' || Lv_Separador || ''
                                       || Lv_Separador || 'NO' || Lv_Separador || '';
        END IF;
      ELSE
        Lcl_InfoReguServicioPrint := Lcl_InfoReguServicioPrint || Lv_Separador || 'NO' || Lv_Separador
                                     || 'Servicio con error que incluye Extender en estado ' || Lv_EstadoServicioDbError || ' no es permitido'
                                     || Lv_Separador || 'NO' || Lv_Separador || ''
                                     || Lv_Separador || 'NO' || Lv_Separador || ''
                                     || Lv_Separador || 'NO' || Lv_Separador || ''
                                     || Lv_Separador || 'NO' || Lv_Separador || '';
      END IF;
      Ln_IndexServiciosARegularizar := Lt_ServiciosARegularizar.NEXT(Ln_IndexServiciosARegularizar);
      SYS.DBMS_OUTPUT.PUT_LINE(Lcl_InfoReguServicioPrint);
    END LOOP;
    EXIT
  WHEN Lc_ServiciosARegularizar%NOTFOUND;
  END LOOP;
  CLOSE Lc_ServiciosARegularizar;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha regularizado la data técnica de servicios dual band con Extender atados a otros elementos');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
