SET SERVEROUTPUT ON
--Servicios Extenders Dual Band adicionales eliminados debido a que la tecnología no es permitida
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaEdbError';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosExtenderTecnologiaError
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO
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
    ON MARCA_OLT.ID_MARCA_ELEMENTO           = MODELO_OLT.MARCA_ELEMENTO_ID
    WHERE SERVICIO_INTERNET.ESTADO           IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
    AND PLAN_DET.ESTADO                      = PLAN.ESTADO
    AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
    AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
    AND EXISTS
      (SELECT *
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_EDB
      WHERE SERVICIO_EDB.PRODUCTO_ID = 1232
      AND SERVICIO_EDB.ESTADO NOT   IN ('Anulado', 'Eliminado', 'Trasladado', 'Cancel', 'Rechazada')
      AND SERVICIO_EDB.PUNTO_ID      = PUNTO.ID_PUNTO
      )
    AND (MARCA_OLT.NOMBRE_MARCA_ELEMENTO = 'TELLION'
    OR MARCA_OLT.NOMBRE_MARCA_ELEMENTO   = 'ZTE');
  BEGIN
    FOR I_PtosExtenderTecnologiaError IN Lc_PtosExtenderTecnologiaError
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosExtenderTecnologiaError.ID_PUNTO,
                                                                            'EXTENDER_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'SI',
                                                                            ' por tecnología no permitida para este producto',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Extenders adicionales eliminados debido a que la tecnología no es permitida');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Extenders con tecnología no permitida: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Servicios Wifi Dual Band adicionales eliminados debido a que la tecnología no es permitida
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaWdbError';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosWdbTecnologiaError
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO
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
    ON MARCA_OLT.ID_MARCA_ELEMENTO           = MODELO_OLT.MARCA_ELEMENTO_ID
    WHERE SERVICIO_INTERNET.ESTADO           IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
    AND PLAN_DET.ESTADO                      = PLAN.ESTADO
    AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
    AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
    AND EXISTS
      (SELECT *
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_WDB
      WHERE SERVICIO_WDB.PRODUCTO_ID = 1231
      AND SERVICIO_WDB.ESTADO NOT   IN ('Anulado', 'Eliminado', 'Trasladado', 'Cancel', 'Rechazada')
      AND SERVICIO_WDB.PUNTO_ID      = PUNTO.ID_PUNTO
      )
    AND (MARCA_OLT.NOMBRE_MARCA_ELEMENTO = 'TELLION'
    OR MARCA_OLT.NOMBRE_MARCA_ELEMENTO   = 'ZTE');
  BEGIN
    FOR I_PtosWdbTecnologiaError IN Lc_PtosWdbTecnologiaError
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosWdbTecnologiaError.ID_PUNTO,
                                                                            'WIFI_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'SI',
                                                                            ' por tecnología no permitida para este producto',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Wifi Dual Band adicionales eliminados debido a que la tecnología no es permitida');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Wifi Dual Band con tecnología no permitida: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Servicios Extender Dual Band adicionales en estado Activo o In-Corte que serán cancelados porque no tienen Wifi Dual Band 
-- y no se encuentran registrados en la data técnica(no tienen enlace asociado al ont)
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaEdbSinWdb';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosExtenderActivosSinWdb
  IS
    SELECT ID_PUNTO
    FROM
      ( SELECT DISTINCT PUNTO.ID_PUNTO,
        PUNTO.LOGIN,
        NVL(
        (SELECT ELEMENTO_FIN.ID_ELEMENTO AS ID_ELEMENTO_FIN
        FROM DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE
        INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERFACE_ELEMENTO
        ON INTERFACE_ELEMENTO.ID_INTERFACE_ELEMENTO = ENLACE.INTERFACE_ELEMENTO_FIN_ID
        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_FIN
        ON ELEMENTO_FIN.ID_ELEMENTO          = INTERFACE_ELEMENTO.ELEMENTO_ID
        WHERE ENLACE.ESTADO                  = 'Activo'
        AND ENLACE.INTERFACE_ELEMENTO_INI_ID = SERVICIO_TECNICO_INTERNET.INTERFACE_ELEMENTO_CLIENTE_ID
        AND ROWNUM                           = 1
        ), '') AS ELEMENTO_SGT
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
      INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ONT
      ON ONT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_CLIENTE_ID
      INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_ONT
      ON MODELO_ONT.ID_MODELO_ELEMENTO = ONT.MODELO_ELEMENTO_ID
      INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_ONT
      ON MARCA_ONT.ID_MARCA_ELEMENTO           = MODELO_ONT.MARCA_ELEMENTO_ID
      WHERE SERVICIO_INTERNET.ESTADO           IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
      AND PLAN_DET.ESTADO                      = PLAN.ESTADO
      AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
      AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
      AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
      AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
      AND EXISTS
        (SELECT *
        FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_EDB
        WHERE SERVICIO_EDB.PRODUCTO_ID = 1232
        AND SERVICIO_EDB.ESTADO        IN ('Activo', 'In-Corte')
        AND SERVICIO_EDB.PUNTO_ID      = PUNTO.ID_PUNTO
        )
      AND MARCA_OLT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
      AND MARCA_ONT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
      AND (MODELO_ONT.NOMBRE_MODELO_ELEMENTO <> 'HS8M8245WG06'
      AND MODELO_ONT.NOMBRE_MODELO_ELEMENTO  <> 'HS8M8245WG04')
      )
    WHERE ELEMENTO_SGT IS NULL;
  BEGIN
    FOR I_PtosExtenderActivosSinWdb IN Lc_PtosExtenderActivosSinWdb
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosExtenderActivosSinWdb.ID_PUNTO,
                                                                            'EXTENDER_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'NO',
                                                                            ' debido a que no se encuentra registrado el equipo Extender Dual Band '
                                                                            || 'en la data técnica y el servicio de Internet no tiene un Wifi Dual '
                                                                            || 'Band',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Extenders adicionales cancelados debido a que no se encuentran registrados ni poseen un Wifi Dual Band');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Extenders adicionales que no se encuentran registrados ni poseen un Wifi Dual Band: '|| SQLCODE 
                           || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Servicios Extender Dual Band adicionales en estado Activo o In-Corte que serán cancelados porque tienen Wifi Dual Band 
-- pero no se encuentran registrados en la data técnica(no tienen enlace asociado al ont)
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaEdbConWdb';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosExtenderActivosConWdb
  IS
    SELECT *
    FROM
      ( SELECT DISTINCT PUNTO.ID_PUNTO,
        PUNTO.LOGIN,
        SERVICIO_INTERNET.ESTADO,
        NVL(
        (SELECT ELEMENTO_FIN.ID_ELEMENTO AS ID_ELEMENTO_FIN
        FROM DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE
        INNER JOIN DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INTERFACE_ELEMENTO
        ON INTERFACE_ELEMENTO.ID_INTERFACE_ELEMENTO = ENLACE.INTERFACE_ELEMENTO_FIN_ID
        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEMENTO_FIN
        ON ELEMENTO_FIN.ID_ELEMENTO          = INTERFACE_ELEMENTO.ELEMENTO_ID
        WHERE ENLACE.ESTADO                  = 'Activo'
        AND ENLACE.INTERFACE_ELEMENTO_INI_ID = SERVICIO_TECNICO_INTERNET.INTERFACE_ELEMENTO_CLIENTE_ID
        AND ROWNUM                           = 1
        ), '') AS ELEMENTO_SGT
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
      INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ONT
      ON ONT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_CLIENTE_ID
      INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_ONT
      ON MODELO_ONT.ID_MODELO_ELEMENTO = ONT.MODELO_ELEMENTO_ID
      INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_ONT
      ON MARCA_ONT.ID_MARCA_ELEMENTO           = MODELO_ONT.MARCA_ELEMENTO_ID
      WHERE SERVICIO_INTERNET.ESTADO           IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
      AND PLAN_DET.ESTADO                      = PLAN.ESTADO
      AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
      AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
      AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
      AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
      AND EXISTS
        (SELECT *
        FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_EDB
        WHERE SERVICIO_EDB.PRODUCTO_ID = 1232
        AND SERVICIO_EDB.ESTADO        IN ('Activo', 'In-Corte')
        AND SERVICIO_EDB.PUNTO_ID      = PUNTO.ID_PUNTO
        )
      AND MARCA_OLT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
      AND MARCA_ONT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
      AND (MODELO_ONT.NOMBRE_MODELO_ELEMENTO = 'HS8M8245WG06'
      OR MODELO_ONT.NOMBRE_MODELO_ELEMENTO = 'HS8M8245WG04')
      )
    WHERE ELEMENTO_SGT IS NULL;
  BEGIN
    FOR I_PtosExtenderActivosConWdb IN Lc_PtosExtenderActivosConWdb
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosExtenderActivosConWdb.ID_PUNTO,
                                                                            'EXTENDER_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'NO',
                                                                            ' debido a que no se encuentra registrado el equipo Extender Dual Band '
                                                                            || 'en la data técnica pero el servicio de Internet si tiene un Wifi Dual'
                                                                            || ' Band',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Extenders adicionales cancelados debido a que no se encuentran registrados pero si poseen un Wifi Dual Band');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Extenders adicionales que no se encuentran registrados pero si poseen un Wifi Dual Band: '|| SQLCODE 
                           || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Servicios Wifi Dual Band adicionales en estado Activo o In-Corte que serán cancelados porque no se encuentran registrados en la data técnica
--modelo diferente a HS8M8245WG06 y HS8M8245WG04
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaWdbAdic';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosSinEquipoWdb
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
      PUNTO.LOGIN,
      SERVICIO_INTERNET.ESTADO
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
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ONT
    ON ONT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_CLIENTE_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_ONT
    ON MODELO_ONT.ID_MODELO_ELEMENTO = ONT.MODELO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_ONT
    ON MARCA_ONT.ID_MARCA_ELEMENTO           = MODELO_ONT.MARCA_ELEMENTO_ID
    WHERE SERVICIO_INTERNET.ESTADO          IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
    AND PLAN_DET.ESTADO                      = PLAN.ESTADO
    AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
    AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
    AND EXISTS
      (SELECT *
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_WDB
      WHERE SERVICIO_WDB.PRODUCTO_ID = 1231
      AND SERVICIO_WDB.ESTADO       IN ('Activo', 'In-Corte')
      AND SERVICIO_WDB.PUNTO_ID      = PUNTO.ID_PUNTO
      )
    AND MARCA_OLT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
    AND MARCA_ONT.NOMBRE_MARCA_ELEMENTO     = 'HUAWEI'
    AND (MODELO_ONT.NOMBRE_MODELO_ELEMENTO <> 'HS8M8245WG06'
    AND MODELO_ONT.NOMBRE_MODELO_ELEMENTO  <> 'HS8M8245WG04');
  BEGIN
    FOR I_PtosSinEquipoWdb IN Lc_PtosSinEquipoWdb
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosSinEquipoWdb.ID_PUNTO,
                                                                            'WIFI_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'NO',
                                                                            ' debido a que no se encuentra registrado el equipo Wifi Dual Band',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Wifi Dual Band adicionales cancelados debido a que no se encuentran registrados en la data técnica');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Wifi Dual Band adicionales que no se encuentran registrados en la data técnica: '|| SQLCODE 
                           || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Servicios Wifi Dual Band adicionales que serán eliminados/cancelados porque el plan de Internet ya incluye el Wifi Dual Band
DECLARE
  Lv_UsrCreacion    VARCHAR2(15) := 'regulaWdbUnico';
  Lv_IpCreacion     VARCHAR2(15) := '127.0.0.1';
  Lv_Status         VARCHAR2(5);
  Lv_MsjError       VARCHAR2(4000);
  Cursor Lc_PtosConMas1Wdb
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
      PUNTO.LOGIN,
      SERVICIO_INTERNET.ESTADO
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_INTERNET
    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
    ON PUNTO.ID_PUNTO = SERVICIO_INTERNET.PUNTO_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_CAB PLAN
    ON PLAN.ID_PLAN = SERVICIO_INTERNET.PLAN_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_DET PLAN_DET
    ON PLAN_DET.PLAN_ID = PLAN.ID_PLAN
    INNER JOIN DB_COMERCIAL.INFO_PLAN_DET PLAN_DET_WDB
    ON PLAN_DET_WDB.PLAN_ID = PLAN.ID_PLAN
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
    WHERE SERVICIO_INTERNET.ESTADO NOT IN ('Anulado', 'Eliminado', 'Trasladado', 'Cancel', 'Rechazada')
    AND PLAN_DET.ESTADO                      = PLAN.ESTADO
    AND PLAN_DET_WDB.ESTADO                  = PLAN.ESTADO
    AND PLAN_DET_WDB.PRODUCTO_ID             = 1231
    AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18'
    AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO        = 'OLT'
    AND EXISTS
      (SELECT *
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_WDB
      WHERE SERVICIO_WDB.PRODUCTO_ID = 1231
      AND SERVICIO_WDB.ESTADO NOT   IN ('Anulado', 'Eliminado', 'Trasladado', 'Cancel', 'Rechazada')
      AND SERVICIO_WDB.PUNTO_ID      = PUNTO.ID_PUNTO
      );
  BEGIN
    FOR I_PtosConMas1Wdb IN Lc_PtosConMas1Wdb
    LOOP
      Lv_Status     := '';
      Lv_MsjError   := '';
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_CANCEL_ELIM_SERVS_DUAL_BAND(
                                                                            I_PtosConMas1Wdb.ID_PUNTO,
                                                                            'WIFI_DUAL_BAND',
                                                                            NULL,
                                                                            NULL,
                                                                            'NO',
                                                                            ' debido a que el plan de Internet ya incluye el Wifi Dual Band',
                                                                            Lv_UsrCreacion,
                                                                            Lv_IpCreacion,
                                                                            Lv_Status,
                                                                            Lv_MsjError
                                                                          );
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Wifi Dual Band adicionales eliminados/cancelados debido a que el plan de Internet ya los incluye');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Wifi Dual Band adicionales eliminados/cancelados debido a que el plan de Internet ya los incluye: '|| SQLCODE 
                           || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se actualiza la información de punto de facturación de servicios adicionales Wifi Dual Band y Extender Dual Band que erróneamente 
--no tenían punto de facturación
DECLARE
  Lv_UsrCreacion        VARCHAR2(15) := 'regulaPtoFactDb';
  Lv_IpCreacion         VARCHAR2(15) := '127.0.0.1';
  Lr_ServicioHistorial  DB_COMERCIAL.INFO_SERVICIO_HISTORIAL%ROWTYPE;
  Lv_MsjError           VARCHAR2(4000);
  Cursor Lc_DbSinPtoFact
  IS
    SELECT DISTINCT PUNTO.ID_PUNTO,
      PUNTO.LOGIN,
      SERVICIO_INTERNET.PUNTO_FACTURACION_ID,
      SERVICIOS_DB.ID_SERVICIO AS ID_SERVICIO_DB,
      SERVICIOS_DB.ESTADO AS ESTADO_SERVICIO_DB
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
    INNER JOIN
    (
      SELECT SERVICIO_DB.*
      FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_DB
      WHERE (SERVICIO_DB.PRODUCTO_ID = 1231 OR SERVICIO_DB.PRODUCTO_ID = 1232)
      AND SERVICIO_DB.ESTADO NOT   IN ('Anulado', 'Eliminado', 'Trasladado', 'Cancel', 'Rechazada')
      AND SERVICIO_DB.PUNTO_FACTURACION_ID IS NULL
    ) SERVICIOS_DB
    ON SERVICIOS_DB.PUNTO_ID      = PUNTO.ID_PUNTO
    WHERE SERVICIO_INTERNET.ESTADO IN ('Activo', 'In-Corte', 'EnVerificacion', 'EnPruebas')
    AND PLAN_DET.ESTADO                      = PLAN.ESTADO
    AND PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PROD_INTERNET_EN_PLAN.ESTADO         = 'Activo'
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD    = '18';
  BEGIN
    FOR I_DbSinPtoFact IN Lc_DbSinPtoFact
    LOOP
      Lv_MsjError   := '';
      UPDATE DB_COMERCIAL.INFO_SERVICIO
      SET PUNTO_FACTURACION_ID = I_DbSinPtoFact.PUNTO_FACTURACION_ID,
      MESES_RESTANTES = 1
      WHERE ID_SERVICIO = I_DbSinPtoFact.ID_SERVICIO_DB;
      Lr_ServicioHistorial              := NULL;
      Lr_ServicioHistorial.SERVICIO_ID  := I_DbSinPtoFact.ID_SERVICIO_DB;
      Lr_ServicioHistorial.USR_CREACION := Lv_UsrCreacion;
      Lr_ServicioHistorial.IP_CREACION  := Lv_IpCreacion;
      Lr_ServicioHistorial.ESTADO       := I_DbSinPtoFact.ESTADO_SERVICIO_DB;
      Lr_ServicioHistorial.OBSERVACION  := 'Se regulariza el punto de facturación del servicio';
      Lr_ServicioHistorial.ACCION       := NULL;
      DB_COMERCIAL.COMEK_MODELO.COMEP_INSERT_SERVICIO_HISTORIA(Lr_ServicioHistorial, Lv_MsjError);
    END LOOP;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Servicios Wifi Dual Band y Extender Dual Band sin punto de facturación');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error Servicios Wifi Dual Band y Extender Dual Band sin punto de facturación: '|| SQLCODE 
                           || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se actualiza la observación errónea de servicios adicionales dual band creados automáticamente
UPDATE DB_COMERCIAL.INFO_SERVICIO_HISTORIAL
SET OBSERVACION = 'Se creo el servicio por cambio de plan'
WHERE ID_SERVICIO_HISTORIAL IN
(
  SELECT SERVICIO_HISTORIAL.ID_SERVICIO_HISTORIAL
  FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL SERVICIO_HISTORIAL
  INNER JOIN DB_COMERCIAL.INFO_SERVICIO SERVICIO_DB
  ON SERVICIO_DB.ID_SERVICIO = SERVICIO_HISTORIAL.SERVICIO_ID
  WHERE 	(SERVICIO_DB.PRODUCTO_ID = 1231 OR SERVICIO_DB.PRODUCTO_ID = 1232)
  AND DBMS_LOB.COMPARE(SERVICIO_HISTORIAL.OBSERVACION,'Se creo el servicio por cambio de plan por cambio de plan') = 0
);
COMMIT;
/
--Creación de parámetros para estados válidos para solicitudes
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
  Ln_IdParamModelos         NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'PrePlanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'Planificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'Detenido',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'Replanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'AsignadoTarea',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD AGREGAR EQUIPO');


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'PrePlanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'Planificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'Detenido',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'Replanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'AsignadoTarea',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD AGREGAR EQUIPO MASIVO',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD AGREGAR EQUIPO MASIVO');

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Pendiente',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Aprobada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'PrePlanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Planificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Detenido',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Replanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'AsignadoTarea',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD CAMBIO EQUIPO POR SOPORTE');


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Pendiente',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Aprobada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'PrePlanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Planificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Detenido',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Replanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'AsignadoTarea',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO');


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'PrePlanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'Planificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'Detenido',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'Replanificada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'AsignadoTarea',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD CAMBIO DE MODEM INMEDIATO');

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD MIGRACION',
    'PendienteExtender',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD MIGRACION');


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'TIPOS_SOLICITUDES_GENERALES_GESTIONAN_ONT',
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'TIPOS_SOLICITUDES_GENERALES_GESTIONAN_ONT',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'PERMITE_CLONAR',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'TIPOS_SOLICITUDES_GENERALES_GESTIONAN_ONT',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'PERMITE_CLONAR',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los tipos de solicitudes para TIPOS_SOLICITUDES_GENERALES_GESTIONAN_ONT');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

