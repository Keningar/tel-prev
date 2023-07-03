-- Parametros para los estados validos de un servicio adicional
INSERT INTO db_general.admi_parametro_det 
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT id_parametro
        FROM db_general.admi_parametro_cab
        WHERE nombre_parametro = 'VALIDA_PROD_ADICIONAL'
    ),
    'Estados permitidos para producto cableado ethernet',
    'Factible',
    'PrePlanificada',
    'Detenido',
    'Replanificada',
    'Activo',
    'djreyes',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null,
    'Activo',
    18,
    null,
    null,
    null
);

-- Parametro para agregar el nombre de solicitud de cableado ethernet
INSERT INTO db_general.admi_parametro_det 
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT id_parametro
        FROM db_general.admi_parametro_cab
        WHERE nombre_parametro = 'VALIDA_PROD_ADICIONAL'
    ),
    'Solicitud cableado ethernet',
    '1332',
    'SOLICITUD DE INSTALACION CABLEADO ETHERNET',
    null,
    null,
    'Activo',
    'djreyes',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    18,
    null,
    null,
    null
);

-- Parametros para los estados validos de un traslado de servicio
INSERT INTO db_general.admi_parametro_det 
(
  ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
  USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5,
  EMPRESA_COD, VALOR6, VALOR7, OBSERVACION
)
VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT id_parametro
        FROM db_general.admi_parametro_cab
        WHERE nombre_parametro = 'VALIDA_PROD_ADICIONAL'
    ),
    'Estados permitidos para CE en traslado',
    'PrePlanificada',
    'Asignada',
    'AsignadoTarea',
    'Detenido',
    'Activo',
    'djreyes',
    sysdate,
    '127.0.0.1',
    null,
    null,
    null,
    'Replanificada',
    18,
    null,
    null,
    null
);

COMMIT;
/