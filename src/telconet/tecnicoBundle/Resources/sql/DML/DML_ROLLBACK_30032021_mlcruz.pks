DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'ESTADOS_SERVICIOS_IN'
    AND DET.VALOR2 = 'EXTENDER_DUAL_BAND'
    AND DET.VALOR3 IN ('Planificada','Replanificada')
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'NOMBRES_TECNICOS_ELIMINACION_SIMULTANEA_X_INTERNET'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'MODELOS_EQUIPOS'
    AND DET.VALOR4 = 'ONT V5'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'MODELOS_EQUIPOS'
    AND DET.VALOR4 = 'EXTENDER DUAL BAND'
    AND DET.VALOR5 = 'K562E'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'MODELOS_EXTENDERS_POR_ONT'
    AND DET.ESTADO = 'Activo'
  );


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET VALOR4 = NULL,
  USR_ULT_MOD = NULL,
  FE_ULT_MOD = NULL,
  IP_ULT_MOD = NULL
  WHERE ID_PARAMETRO_DET IN 
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'ESTADOS_SOLICITUDES_ABIERTAS'
    AND DET.VALOR2 = 'SOLICITUD AGREGAR EQUIPO'
    AND DET.VALOR3 = 'PrePlanificada'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'ESTADOS_SOLICITUDES_ABIERTAS'
    AND DET.VALOR2 = 'SOLICITUD PLANIFICACION'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'CAMBIO_RAZON_SOCIAL'
    AND DET.VALOR2 = 'NOMBRES_TECNICOS_PRODS_PERMITIDOS_SIN_ACTIVAR'
    AND DET.VALOR3 = 'EXTENDER_DUAL_BAND'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'CAMBIO_RAZON_SOCIAL'
    AND DET.VALOR2 = 'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO'
    AND DET.VALOR3 = 'EXTENDER_DUAL_BAND'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND CAB.ESTADO = 'Activo'
    AND DET.VALOR1 = 'GESTION_PYL_SIMULTANEA'
    AND DET.ESTADO = 'Activo'
  );

DELETE
FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA IN ('MOTIVO_CREACION_SOLICITUD', 'TIPO_ONT_NUEVO');

COMMIT;
/