DECLARE
  --
  --CURSOR QUE RETORNA SI EXISTE EL ESTADO QUE SE VA A MIGRAR AL DASHBOARD
  CURSOR C_GetValidarEstadoServicio(Cv_EstadoServicio DB_GENERAL.ADMI_PARAMETRO_DET.VALOR2%TYPE,
                                    Cv_EstadoActivo DB_GENERAL.ADMI_PARAMETRO_DET.ESTADO%TYPE,
                                    Cv_LabelDashboardComercial DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                                    Cv_Descripcion DB_GENERAL.ADMI_PARAMETRO_DET.DESCRIPCION%TYPE,
                                    Cv_PrefijoEmpresa DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE,
                                    Cv_LabelAgrupadas DB_GENERAL.ADMI_PARAMETRO_DET.VALOR3%TYPE,
                                    Cv_Valor1 DB_GENERAL.ADMI_PARAMETRO_DET.VALOR1%TYPE)
  IS
    --
    SELECT MAX(APD.ID_PARAMETRO_DET) AS ID_PARAMETRO_DET, APD.VALOR1
    FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
    JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC
    ON APD.PARAMETRO_ID      = APC.ID_PARAMETRO
    WHERE APD.VALOR2         = Cv_EstadoServicio
    AND APD.VALOR3           = Cv_LabelAgrupadas
    AND APD.ESTADO           = Cv_EstadoActivo
    AND APC.ESTADO           = Cv_EstadoActivo
    AND APC.NOMBRE_PARAMETRO = Cv_LabelDashboardComercial
    AND APD.DESCRIPCION      = Cv_Descripcion
    AND APD.VALOR1           = NVL(Cv_Valor1, APD.VALOR1)
    AND APD.EMPRESA_COD      =
      (SELECT COD_EMPRESA
      FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
      WHERE PREFIJO = Cv_PrefijoEmpresa
      AND ESTADO    = Cv_EstadoActivo
      )
    GROUP BY APD.VALOR1;
    --
  --
  --
  --SERVICIOS QUE SE VAN A REGULARIZAR
  CURSOR C_GetServiciosDashboard
  IS
    --
    SELECT IDAS.ID_DASHBOARD_SERVICIO,
      IDAS.CATEGORIA,
      IDAS.FRECUENCIA_PRODUCTO,
      IDAS.PRECIO_VENTA,
      IDAS.CANTIDAD,
      IDAS.PRECIO_INSTALACION,
      IDAS.DESCUENTO_TOTALIZADO,
      IDAS.ESTADO,
      IDAS.MES_TRANSACCION,
      IDAS.TIPO_ORDEN
    FROM DB_COMERCIAL.INFO_DASHBOARD_SERVICIO IDAS;
    --
  --
  Ln_MRC NUMBER;
  Ln_NRC NUMBER;
  Ln_NumeroMesesRestantes NUMBER;
  Ln_Commit NUMBER := 0;
  Lc_GetServiciosDashboard C_GetServiciosDashboard%ROWTYPE;
  Lr_ParamDetEstadoServicio C_GetValidarEstadoServicio%ROWTYPE;
  --
BEGIN
  --
  IF C_GetServiciosDashboard%ISOPEN THEN
    CLOSE C_GetServiciosDashboard;
  END IF;
  --
  OPEN C_GetServiciosDashboard;
  LOOP
    --
    FETCH C_GetServiciosDashboard INTO Lc_GetServiciosDashboard;
    --
    EXIT
    WHEN C_GetServiciosDashboard%NOTFOUND;
    --
    --
    Lr_ParamDetEstadoServicio := NULL;
    Ln_MRC                    := 0;
    Ln_NRC                    := 0;
    Ln_NumeroMesesRestantes   := 0;
    --
    IF C_GetValidarEstadoServicio%ISOPEN THEN
      CLOSE C_GetValidarEstadoServicio;
    END IF;
    --
    OPEN C_GetValidarEstadoServicio('ESTADO_SERVICIO',
                                    'Activo',
                                    'DASHBOARD_COMERCIAL',
                                    TRIM(Lc_GetServiciosDashboard.ESTADO),
                                    'TN',
                                    'AGRUPADAS',
                                    'VENTAS_CANCELADAS');
    --
    FETCH C_GetValidarEstadoServicio INTO Lr_ParamDetEstadoServicio;
    --
    CLOSE C_GetValidarEstadoServicio;
    --
    --
    IF Lr_ParamDetEstadoServicio.ID_PARAMETRO_DET > 0 THEN
      --
      Ln_MRC := 0;
      Ln_NRC := 0;
      --
    ELSE
      --
      Ln_NumeroMesesRestantes := 13 - TO_NUMBER(Lc_GetServiciosDashboard.MES_TRANSACCION, '99');
      --
      --
      IF Lc_GetServiciosDashboard.FRECUENCIA_PRODUCTO >= 1 AND ( Lc_GetServiciosDashboard.CATEGORIA = 'CATEGORIA 2' OR 
                                                                 Lc_GetServiciosDashboard.CATEGORIA = 'CATEGORIA 3' )
         AND Lc_GetServiciosDashboard.TIPO_ORDEN = 'N' THEN
        --
        Ln_MRC := ROUND( ( ( ( ( NVL(Lc_GetServiciosDashboard.PRECIO_VENTA, 0) * NVL(Lc_GetServiciosDashboard.CANTIDAD, 0) ) 
                                - NVL(Lc_GetServiciosDashboard.DESCUENTO_TOTALIZADO, 0) ) / Lc_GetServiciosDashboard.FRECUENCIA_PRODUCTO ) 
                            * Ln_NumeroMesesRestantes ), 2 );
        --
      ELSIF Lc_GetServiciosDashboard.TIPO_ORDEN = 'N' THEN
        --
        Ln_MRC := ( ( NVL(Lc_GetServiciosDashboard.PRECIO_VENTA, 0) * NVL(Lc_GetServiciosDashboard.CANTIDAD, 0) )
                     - NVL(Lc_GetServiciosDashboard.DESCUENTO_TOTALIZADO, 0) );
        --
      END IF;
      --
      Ln_NRC := ROUND( (NVL(Lc_GetServiciosDashboard.PRECIO_INSTALACION, 0) / 12), 2 );
      --
    END IF;
    --
    --
    Ln_Commit := Ln_Commit + 1;
    --
    --
    UPDATE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
    SET NRC = Ln_NRC, MRC = Ln_MRC
    WHERE ID_DASHBOARD_SERVICIO = Lc_GetServiciosDashboard.ID_DASHBOARD_SERVICIO;
    --
    --
    IF Ln_Commit = 5000 THEN
      --
      COMMIT;
      --
      Ln_Commit := 0;
      --
    END IF;
    --
    --
  END LOOP;
  --
  --
  IF Ln_Commit < 5000 THEN
    --
    COMMIT;
    --
  END IF; 
  --
END;
