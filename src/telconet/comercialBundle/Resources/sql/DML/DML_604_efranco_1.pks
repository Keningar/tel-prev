UPDATE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
SET ACCION = 'Nueva'
WHERE TIPO_ORDEN = 'N';
UPDATE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
SET ACCION = 'Traslado'
WHERE TIPO_ORDEN = 'T';
UPDATE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
SET ACCION = 'Reubicado'
WHERE TIPO_ORDEN = 'R';
INSERT INTO DB_GENERAL.ADMI_MOTIVO (
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD
) VALUES (
  DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
  '7017',
  'Cancelacion por Regularizacion',
  'Inactivo',
  'telcos',
  SYSDATE,
  'telcos',
  SYSDATE
);

INSERT INTO DB_GENERAL.ADMI_MOTIVO (
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD
) VALUES (
  DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
  '7017',
  'Cancelacion',
  'Inactivo',
  'telcos',
  SYSDATE,
  'telcos',
  SYSDATE
);

UPDATE DB_GENERAL.ADMI_MOTIVO
  SET
    REF_MOTIVO_ID = (
      SELECT
        ID_MOTIVO
      FROM
        DB_GENERAL.ADMI_MOTIVO
      WHERE
        NOMBRE_MOTIVO = 'Cancelacion por Regularizacion'
        AND   RELACION_SISTEMA_ID = 7017
        AND   ESTADO = 'Inactivo'
    )
WHERE
  ID_MOTIVO IN (
    SELECT
      ID_MOTIVO
    FROM
      DB_GENERAL.ADMI_MOTIVO
    WHERE
      NOMBRE_MOTIVO IN (
        'Cambio de domicilio',
        'Cambio de puerto - TECNICO',
        'Culminación de cortesía',
        'Requiere  un plan corporativo',
        'Solicitado por 3ra Persona'
      )
      OR   ID_MOTIVO IN (
        1508,
        1507,
        1506,
        1491,
        1502,
        1509
      )
  );

UPDATE DB_GENERAL.ADMI_MOTIVO
  SET
    REF_MOTIVO_ID = (
      SELECT
        ID_MOTIVO
      FROM
        DB_GENERAL.ADMI_MOTIVO
      WHERE
        NOMBRE_MOTIVO = 'Cancelacion'
        AND   RELACION_SISTEMA_ID = 7017
        AND   ESTADO = 'Inactivo'
    )
WHERE
  ID_MOTIVO IN (
    SELECT
      ID_MOTIVO
    FROM
      DB_GENERAL.ADMI_MOTIVO
    WHERE
      NOMBRE_MOTIVO IN (
        'Cierre del negocio',
        'Cliente bloqueado mas de 45 días',
        'Cyber/ISP/Sala de juegos en linea',
        'Inconvenientes en la instalación',
        'Indisponibilidad servicio de internet',
        'Indisponibilidad servicios adicionales',
        'Mal asesoramiento comercial',
        'Mejor oferta de la competencia',
        'Migración de Tecnología',
        'Migracion Huawei / Baja de OLT´s',
        'Problemas de facturación',
        'Problemas económicos',
        'Solicitado por revendedor',
        'Traslado fuera de cobertura'
      )
      OR   ID_MOTIVO IN (
        1495,
        1500,
        1503,
        1504,
        1493,
        1496,
        1494,
        1492,
        1499,
        1505,
        1501,
        1498,
        1497
      )
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
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  VALOR5,
  EMPRESA_COD
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
  '658',
  'ORDENES_CANCELADAS',
  'Clientes Cancelados<br/>por Regularizacion',
  'CLIENTES_CANCELADOS',
  'ACUMULAR',
  'CANCEL_POR_REGULARIZACION',
  'Activo',
  'efranco',
  SYSDATE,
  '127.0.0.1',
  '4',
  '10'
);
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR5 = '5', FE_ULT_MOD = SYSDATE, USR_ULT_MOD = 'efranco'
WHERE ID_PARAMETRO_DET = 3468;
COMMIT;
