CREATE OR REPLACE VIEW DB_COMERCIAL.VISTA_PM_CORTE_FAC_ABI
AS SELECT DISTINCT ROW_NUMBER () OVER (ORDER BY PUNTO.ID_PUNTO) AS ID_VISTA,
    PERSONA.ID_PERSONA,
    CASE
      WHEN PERSONA.RAZON_SOCIAL IS NOT NULL
      THEN PERSONA.RAZON_SOCIAL
      ELSE NVL (PERSONA.NOMBRES, '')
        || ' '
        || NVL (PERSONA.APELLIDOS, '')
    END AS NOMBRE_CLIENTE,
    EMPRESA_ROL.EMPRESA_COD,
    OFICINA_GRUPO.ID_OFICINA,
    OFICINA_GRUPO.NOMBRE_OFICINA,
    PUNTO.ID_PUNTO,
    PUNTO.LOGIN,
    TIPO_NEGOCIO.ID_TIPO_NEGOCIO,
    TIPO_NEGOCIO.NOMBRE_TIPO_NEGOCIO,
    FORMA_PAGO.ID_FORMA_PAGO,
    FORMA_PAGO.DESCRIPCION_FORMA_PAGO,
    VISTA_SALDOS.SALDO,
    VISTA_FACTURAS_ABIERTAS.FACTURAS_ABIERTAS,
    (SELECT NOMBRE_TIPO_MEDIO
    FROM DB_INFRAESTRUCTURA.ADMI_TIPO_MEDIO
    WHERE CODIGO_TIPO_MEDIO = DB_COMERCIAL.TECNK_SERVICIOS.FNC_GET_MEDIO_POR_PUNTO(PUNTO.ID_PUNTO, 'INTERNET')
    AND ESTADO              ='Activo'
    ) AS ULTIMA_MILLA,
    CUENTAS_TARJETAS.BANCO_ID,
    CUENTAS_TARJETAS.DESCRIPCION_BANCO,
    CUENTAS_TARJETAS.TIPO_CUENTA_ID,
    CUENTAS_TARJETAS.DESCRIPCION_CUENTA,
    CUENTAS_TARJETAS.ES_TARJETA,
    ROL.DESCRIPCION_ROL AS ROL,
    (SELECT MAX(PER_CARACT.VALOR)
    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PER_CARACT
    INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
    ON CARACT.ID_CARACTERISTICA             = PER_CARACT.CARACTERISTICA_ID
    WHERE PER_CARACT.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
    AND PER_CARACT.ESTADO                   = 'Activo'
    AND CARACT.DESCRIPCION_CARACTERISTICA   = 'CICLO_FACTURACION'
    ) AS CICLO_ID
  FROM DB_COMERCIAL.INFO_PERSONA PERSONA
  INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PERSONA_EMPRESA_ROL
  ON PERSONA_EMPRESA_ROL.PERSONA_ID= PERSONA.ID_PERSONA
  INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EMPRESA_ROL
  ON EMPRESA_ROL.ID_EMPRESA_ROL = PERSONA_EMPRESA_ROL.EMPRESA_ROL_ID
  INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO OFICINA_GRUPO
  ON OFICINA_GRUPO.ID_OFICINA = PERSONA_EMPRESA_ROL.OFICINA_ID
  INNER JOIN DB_GENERAL.ADMI_ROL ROL
  ON ROL.ID_ROL = EMPRESA_ROL.ROL_ID
  INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
  ON PUNTO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
  INNER JOIN DB_COMERCIAL.ADMI_TIPO_NEGOCIO TIPO_NEGOCIO
  ON TIPO_NEGOCIO.ID_TIPO_NEGOCIO = PUNTO.TIPO_NEGOCIO_ID
  INNER JOIN DB_COMERCIAL.INFO_CONTRATO CONTRATO
  ON CONTRATO.PERSONA_EMPRESA_ROL_ID = PERSONA_EMPRESA_ROL.ID_PERSONA_ROL
  INNER JOIN DB_GENERAL.ADMI_FORMA_PAGO FORMA_PAGO
  ON FORMA_PAGO.ID_FORMA_PAGO = CONTRATO.FORMA_PAGO_ID
  INNER JOIN DB_COMERCIAL.INFO_PUNTO_SALDO VISTA_SALDOS
  ON VISTA_SALDOS.PUNTO_ID = PUNTO.ID_PUNTO
  INNER JOIN DB_FINANCIERO.VISTA_FACTURAS_ABIERTAS VISTA_FACTURAS_ABIERTAS
  ON VISTA_FACTURAS_ABIERTAS.PUNTO_ID = PUNTO.ID_PUNTO
  LEFT JOIN
    (SELECT CONTRATO_FORMA_PAGO.CONTRATO_ID,
      BANCO_TIPO_CUENTA.BANCO_ID,
      BANCO.DESCRIPCION_BANCO,
      BANCO_TIPO_CUENTA.TIPO_CUENTA_ID,
      TIPO_CUENTA.DESCRIPCION_CUENTA,
      TIPO_CUENTA.ES_TARJETA
    FROM DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO CONTRATO_FORMA_PAGO
    INNER JOIN DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BANCO_TIPO_CUENTA
    ON BANCO_TIPO_CUENTA.ID_BANCO_TIPO_CUENTA = CONTRATO_FORMA_PAGO.BANCO_TIPO_CUENTA_ID
    LEFT JOIN DB_GENERAL.ADMI_BANCO BANCO
    ON BANCO.ID_BANCO = BANCO_TIPO_CUENTA.BANCO_ID
    LEFT JOIN DB_GENERAL.ADMI_TIPO_CUENTA TIPO_CUENTA
    ON TIPO_CUENTA.ID_TIPO_CUENTA                      = CONTRATO_FORMA_PAGO.TIPO_CUENTA_ID
    WHERE CONTRATO_FORMA_PAGO.ESTADO                   = 'Activo'
    ) CUENTAS_TARJETAS ON CUENTAS_TARJETAS.CONTRATO_ID = CONTRATO.ID_CONTRATO
  WHERE CONTRATO.ESTADO = 'Activo'
  AND EXISTS
    (SELECT NULL
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_PUNTO
    WHERE SERVICIO_PUNTO.PUNTO_ID = PUNTO.ID_PUNTO
    AND SERVICIO_PUNTO.ESTADO     = 'Activo'
    AND SERVICIO_PUNTO.ES_VENTA   = 'S'
    )
  AND NOT EXISTS
    (SELECT NULL
    FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET DET
    WHERE DET.PUNTO_ID = PUNTO.ID_PUNTO
    AND DET.ESTADO    IN ('Pendiente','Fallo')
    AND ROWNUM         = 1
    )
  ORDER BY NOMBRE_CLIENTE ASC;
  
/